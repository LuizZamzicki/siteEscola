<?php
namespace App\Models;
use Core\Database;
use \Exception;
use \stdClass;
use App\Models\DTO\ReservaDTO;

require_once BASE_PATH . 'core/models/ReservaDTO.php';
require_once BASE_PATH . 'core/services/database.php';
require_once BASE_PATH . 'core/services/NotificacaoService.php';

class ReservaService
{
    private Database $db;
    private NotificacaoService $notificacaoService;


    /**
     * Busca todas as reservas do sistema.
     * @return ReservaDTO[]
     */
    public function buscarTodas(): array
    {
        $sql = "SELECT r.*, u.nome as nome_usuario, l.titulo as titulo_livro
                FROM reservas r
                JOIN usuarios u ON r.id_usuario = u.id_usuario
                JOIN livros l ON r.id_livro = l.id_livro
                ORDER BY r.data_reserva DESC";
        $results = $this->db->query($sql);
        return array_map([ReservaDTO::class, 'fromArray'], $results);
    }

    public function __construct()
    {
        $this->db = new Database();
        $this->notificacaoService = new NotificacaoService();
    }

    /**
     * Busca uma reserva pelo seu ID.
     * @return ReservaDTO|null
     */
    public function buscarPorId(int $id): ?ReservaDTO
    {
        $sql = "SELECT r.*, u.nome as nome_usuario, l.titulo as titulo_livro
                FROM reservas r
                JOIN usuarios u ON r.id_usuario = u.id_usuario
                JOIN livros l ON r.id_livro = l.id_livro
                WHERE r.id_reserva = :id";
        $result = $this->db->query($sql, [':id' => $id]);
        return empty($result) ? null : ReservaDTO::fromArray($result[0]);
    }

    /**
     * Busca todas as reservas de um determinado status.
     * @return ReservaDTO[]
     */
    public function buscarPorStatus(string $status): array
    {
        $sql = "SELECT r.*, u.nome as nome_usuario, l.titulo as titulo_livro
                FROM reservas r
                JOIN usuarios u ON r.id_usuario = u.id_usuario
                JOIN livros l ON r.id_livro = l.id_livro
                WHERE r.status = :status ORDER BY r.data_reserva ASC";
        $results = $this->db->query($sql, [':status' => $status]);
        return array_map([ReservaDTO::class, 'fromArray'], $results);
    }

    /**
     * Busca os empréstimos e reservas ativas de um aluno para a visualização dele.
     * @return array
     */
    public function buscarReservasEEmprestimosPorAluno(int $alunoId): array
    {
        $sql = "
            -- Empréstimos Ativos
            SELECT
                l.id_livro as id, l.titulo, l.url_foto,
                'Emprestado' as status_reserva,
                emp.data_devolucao_prevista,
                NULL as data_validade_reserva,
                GROUP_CONCAT(DISTINCT a.nome SEPARATOR ', ') as autores_nomes
            FROM emprestimos emp
            JOIN livros l ON emp.id_livro = l.id_livro
            LEFT JOIN autores a ON l.id_autor = a.id_autor
            WHERE emp.id_usuario = :alunoId AND emp.status = 'Emprestado'
            GROUP BY l.id_livro, emp.data_devolucao_prevista

            UNION ALL

            -- Reservas Aguardando Retirada
            SELECT
                l.id_livro as id, l.titulo, l.url_foto,
                'Aguardando Retirada' as status_reserva,
                NULL as data_devolucao_prevista,
                r.data_validade_reserva,
                GROUP_CONCAT(DISTINCT a.nome SEPARATOR ', ') as autores_nomes
            FROM reservas r
            JOIN livros l ON r.id_livro = l.id_livro
            LEFT JOIN autores a ON l.id_autor = a.id_autor
            WHERE r.id_usuario = :alunoId AND r.status = 'Aguardando Retirada'
            GROUP BY l.id_livro, r.data_validade_reserva

            ORDER BY status_reserva, titulo
        ";

        $results = $this->db->query($sql, [':alunoId' => $alunoId]);

        return array_map(function ($row)
        {
            $livro = new stdClass();
            $livro->id = $row['id'];
            $livro->titulo = $row['titulo'];
            $livro->url_foto = $row['url_foto'];
            $livro->autores = $row['autores_nomes'] ? explode(', ', $row['autores_nomes']) : [];
            $livro->status_reserva = $row['status_reserva'];
            $livro->data_devolucao_prevista = $row['data_devolucao_prevista'];
            $livro->data_validade_reserva = $row['data_validade_reserva'];
            return $livro;
        }, $results);
    }

    /**
     * Cria uma nova reserva para um aluno.
     * Se o livro estiver disponível, a reserva já é criada como 'Aguardando Retirada'.
     * Se não, fica como 'Pendente'.
     * @return array
     */
    public function criarReserva(int $alunoId, int $livroId): array
    {
        // Validações
        $livro = $this->db->selectOne("SELECT titulo, qtde_disponivel FROM livros WHERE id_livro = :id", [':id' => $livroId]);
        if (!$livro)
        {
            return ['success' => false, 'message' => 'Livro não encontrado.'];
        }

        $reservaAtiva = $this->db->selectOne(
            "SELECT id_reserva FROM reservas WHERE id_usuario = :alunoId AND id_livro = :livroId AND status IN ('Pendente', 'Aguardando Retirada')",
            [':alunoId' => $alunoId, ':livroId' => $livroId]
        );
        if ($reservaAtiva)
        {
            return ['success' => false, 'message' => 'Você já possui uma reserva ativa para este livro.'];
        }

        $emprestimoAtivo = $this->db->selectOne(
            "SELECT id_emprestimo FROM emprestimos WHERE id_usuario = :alunoId AND id_livro = :livroId AND status = 'Emprestado'",
            [':alunoId' => $alunoId, ':livroId' => $livroId]
        );
        if ($emprestimoAtivo)
        {
            return ['success' => false, 'message' => 'Você já está com este livro emprestado.'];
        }

        $this->db->beginTransaction();
        try
        {
            $statusReserva = 'Pendente';
            $dataValidade = null;
            $mensagemSucesso = 'Reserva solicitada com sucesso! Você será notificado quando o livro estiver disponível para retirada.';

            if ($livro->qtde_disponivel > 0)
            {
                $statusReserva = 'Aguardando Retirada';
                $dataValidade = date('Y-m-d', strtotime('+3 weekdays'));
                $mensagemSucesso = 'Livro reservado com sucesso! Você tem até ' . date('d/m/Y', strtotime($dataValidade)) . ' para retirá-lo na biblioteca.';

                $this->db->execute(
                    "UPDATE livros SET qtde_disponivel = qtde_disponivel - 1, qtde_reservada = qtde_reservada + 1 WHERE id_livro = :id",
                    [':id' => $livroId]
                );
            }

            $sql = "INSERT INTO reservas (id_usuario, id_livro, data_validade_reserva, status) VALUES (:id_usuario, :id_livro, :data_validade, :status)";
            $this->db->execute($sql, [':id_usuario' => $alunoId, ':id_livro' => $livroId, ':data_validade' => $dataValidade, ':status' => $statusReserva]);
            $reservaId = $this->db->lastInsertId();

            $this->db->commit();

            $novaReserva = $this->db->selectOne("
                SELECT r.id_reserva, r.id_livro, r.data_validade_reserva, l.titulo, l.url_foto, GROUP_CONCAT(a.nome SEPARATOR ', ') as autores
                FROM reservas r
                JOIN livros l ON r.id_livro = l.id_livro
                LEFT JOIN autores a ON l.id_autor = a.id_autor
                WHERE r.id_reserva = :id
                GROUP BY r.id_reserva
            ", [':id' => $reservaId]);

            return ['success' => true, 'message' => $mensagemSucesso, 'newReservation' => $novaReserva];
        }
        catch (Exception $e)
        {
            $this->db->rollBack();
            error_log("Erro ao criar reserva: " . $e->getMessage());
            return ['success' => false, 'message' => 'Ocorreu um erro interno ao processar sua reserva. Tente novamente.'];
        }
    }

    /**
     * Cancela uma reserva ativa de um aluno (Pendente ou Aguardando Retirada).
     * @return array
     */
    public function cancelarReservaAluno(int $alunoId, int $livroId): array
    {
        $this->db->beginTransaction();
        try
        {
            // Encontra a reserva ativa para o livro e aluno
            $reserva = $this->db->selectOne(
                "SELECT id_reserva, status FROM reservas WHERE id_usuario = :alunoId AND id_livro = :livroId AND status IN ('Pendente', 'Aguardando Retirada')",
                [':alunoId' => $alunoId, ':livroId' => $livroId]
            );

            if (!$reserva)
            {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Nenhuma reserva ativa encontrada para este livro.'];
            }

            // Atualiza o status da reserva para 'Cancelada'
            $this->db->execute("UPDATE reservas SET status = 'Cancelada' WHERE id_reserva = :id", [':id' => $reserva->id_reserva]);

            // Se a reserva estava 'Aguardando Retirada', o livro deve voltar a ficar disponível
            if ($reserva->status === 'Aguardando Retirada')
            {
                $this->db->execute("UPDATE livros SET qtde_disponivel = qtde_disponivel + 1, qtde_reservada = qtde_reservada - 1 WHERE id_livro = :id_livro", [':id_livro' => $livroId]);
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Sua reserva foi cancelada com sucesso.'];
        }
        catch (Exception $e)
        {
            $this->db->rollBack();
            error_log("Erro ao cancelar reserva (aluno): " . $e->getMessage());
            return ['success' => false, 'message' => 'Ocorreu um erro interno ao cancelar sua reserva.'];
        }
    }

    /**
     * Processa todas as reservas que expiraram.
     * Altera o status para 'Expirada' e devolve o livro ao acervo disponível.
     * Ideal para ser executado por uma tarefa agendada (cron job).
     * @return array Um array com o resultado da operação.
     */
    public function processarReservasExpiradas(): array
    {
        $this->db->beginTransaction();
        try
        {
            // 1. Encontrar todas as reservas com status 'Aguardando Retirada' que já venceram.
            $sql = "SELECT r.id_reserva, r.id_livro, r.id_usuario, l.titulo as livroTitulo
                FROM reservas r
                JOIN livros l ON r.id_livro = l.id_livro
                WHERE r.status = 'Aguardando Retirada' AND r.data_validade_reserva < CURDATE()";

            $reservasExpiradas = $this->db->query($sql);
            $count = count($reservasExpiradas);

            if ($count === 0)
            {
                $this->db->commit();
                return ['success' => true, 'message' => 'Nenhuma reserva expirada para processar.', 'processed_count' => 0];
            }

            $idsReservasParaExpirar = array_column($reservasExpiradas, 'id_reserva');
            $placeholders = implode(',', array_fill(0, $count, '?'));

            // 2. Atualizar o status de todas as reservas expiradas para 'Expirada' de uma vez.
            $this->db->execute("UPDATE reservas SET status = 'Expirada' WHERE id_reserva IN ($placeholders)", $idsReservasParaExpirar);

            // 3. Atualizar a quantidade de livros e notificar os usuários.
            foreach ($reservasExpiradas as $reserva)
            {
                // Devolve o livro ao estoque disponível.
                $this->db->execute("UPDATE livros SET qtde_disponivel = qtde_disponivel + 1, qtde_reservada = qtde_reservada - 1 WHERE id_livro = :id_livro", [':id_livro' => $reserva['id_livro']]);

                // Notifica o usuário que a reserva expirou.
                $this->notificacaoService->criarNotificacao($reserva['id_usuario'], 'reserva_expirada', "Reserva Expirada", "Sua reserva para o livro <strong>{$reserva['livroTitulo']}</strong> expirou e foi cancelada.", '?param=biblioteca_aluno');
            }

            $this->db->commit();
            return ['success' => true, 'message' => "$count reserva(s) expirada(s) foram processadas com sucesso.", 'processed_count' => $count];
        }
        catch (Exception $e)
        {
            $this->db->rollBack();
            error_log("Erro ao processar reservas expiradas: " . $e->getMessage());
            return ['success' => false, 'message' => 'Ocorreu um erro interno ao processar as reservas expiradas.'];
        }
    }

    /**
     * Confirma a retirada de um livro, criando um empréstimo e finalizando a reserva.
     * @return bool
     */
    public function confirmarRetirada(int $reservaId): bool
    {
        $this->db->beginTransaction();
        try
        {
            $reserva = $this->buscarPorId($reservaId);
            if (!$reserva || $reserva->status !== 'Aguardando Retirada')
            {
                throw new Exception("Reserva não encontrada ou não está aguardando retirada.");
            }

            $this->db->execute(
                "INSERT INTO emprestimos (id_usuario, id_livro, data_emprestimo, data_devolucao_prevista, status) VALUES (:id_usuario, :id_livro, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 15 DAY), 'Emprestado')",
                [':id_usuario' => $reserva->id_usuario, ':id_livro' => $reserva->id_livro]
            );

            $this->db->execute("UPDATE reservas SET status = 'Concluída' WHERE id_reserva = :id", [':id' => $reservaId]);
            $this->db->execute("UPDATE livros SET qtde_reservada = qtde_reservada - 1 WHERE id_livro = :id_livro", [':id_livro' => $reserva->id_livro]);

            $this->db->commit();
            return true;
        }
        catch (Exception $e)
        {
            $this->db->rollBack();
            error_log("Erro ao confirmar retirada: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancela uma reserva que está 'Aguardando Retirada'. Usado pelo admin.
     * @return bool
     */
    public function cancelarReservaAdmin(int $reservaId): bool
    {
        $this->db->beginTransaction();
        try
        {
            $reserva = $this->buscarPorId($reservaId);
            if (!$reserva || $reserva->status !== 'Aguardando Retirada')
            {
                throw new Exception("Reserva não encontrada ou não está aguardando retirada.");
            }

            $this->db->execute("UPDATE reservas SET status = 'Cancelada' WHERE id_reserva = :id", [':id' => $reservaId]);
            $this->db->execute("UPDATE livros SET qtde_disponivel = qtde_disponivel + 1, qtde_reservada = qtde_reservada - 1 WHERE id_livro = :id_livro", [':id_livro' => $reserva->id_livro]);
            $this->db->commit();

            $this->notificacaoService->criarNotificacao($reserva->id_usuario, 'reserva_recusada', "Reserva Cancelada", "Sua reserva para o livro <strong>{$reserva->livroTitulo}</strong> foi cancelada pela administração.", '?param=biblioteca_aluno');
            return true;
        }
        catch (Exception $e)
        {
            $this->db->rollBack();
            error_log("Erro ao cancelar reserva (admin): " . $e->getMessage());
            return false;
        }
    }
}
