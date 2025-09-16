<?php
namespace App\Models;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../Models/DTO/NotificacaoDTO.php';
require_once __DIR__ . '/../Models/DTO/AvisoDTO.php';
require_once __DIR__ . '/../Models/DTO/EventoCalendarioDTO.php';
require_once __DIR__ . '/../../core/database.php';

use Core\Database;
use App\Models\DTO\NotificacaoDTO;
use App\Models\DTO\AvisoDTO;
use App\Models\DTO\EventoCalendarioDTO;

class NotificacaoService
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function criarNotificacao(int $id_usuario, string $tipo, string $titulo, string $descricao, ?string $link = null): bool
    {
        $sql = "INSERT INTO notificacoes (id_usuario, tipo, titulo, descricao, link) VALUES (:id_usuario, :tipo, :titulo, :descricao, :link)";
        return $this->db->execute($sql, [
            ':id_usuario' => $id_usuario,
            ':tipo' => $tipo,
            ':titulo' => $titulo,
            ':descricao' => $descricao,
            ':link' => $link
        ]);
    }

    public function criarNotificacaoParaGrupo(string $grupo, string $tipo, string $titulo, string $descricao, ?string $link = null): void
    {
        $sqlUsuarios = "SELECT id_usuario FROM usuarios WHERE ";
        $params = [];

        if ($grupo === 'professores')
        {
            $sqlUsuarios .= "status = 'Ativo' AND tipo = 'Professor'";
        }
        elseif ($grupo === 'alunos')
        {
            $sqlUsuarios .= "status = 'Ativo' AND tipo = 'Aluno'";
        }
        elseif ($grupo === 'todos')
        {
            // Notifica todos os usuários ativos E os Super Admins (mesmo que não estejam 'Ativos', pois eles gerenciam o sistema)
            $sqlUsuarios .= "status = 'Ativo' OR tipo != 'Aluno'";
        }
        else
        {
            // Assume que é um nome de turma específico
            $sqlUsuarios .= "status = 'Ativo' AND tipo = 'Aluno' AND turma = :turma";
            $params[':turma'] = $grupo;
        }

        $usuarios = $this->db->query($sqlUsuarios, $params);

        foreach ($usuarios as $usuario)
        {
            $this->criarNotificacao($usuario['id_usuario'], $tipo, $titulo, $descricao, $link);
        }
    }

    /**
     * @return NotificacaoDTO[]
     */
    public function getNotificacoesNaoLidas(int $id_usuario, int $limit = 5): array
    {
        $sql = "SELECT * FROM notificacoes WHERE id_usuario = :id_usuario AND data_leitura IS NULL ORDER BY data_criacao DESC LIMIT " . (int)$limit;
        $results = $this->db->query($sql, [':id_usuario' => $id_usuario]);

        $notificacoes = [];
        foreach ($results as $row)
        {
            $notificacoes[] = new NotificacaoDTO($row);
        }
        return $notificacoes;
    }

    public function countNotificacoesNaoLidas(int $id_usuario): int
    {
        $sql = "SELECT count(*) as count FROM notificacoes WHERE id_usuario = :id_usuario AND data_leitura IS NULL";
        $results = $this->db->query($sql, [':id_usuario' => $id_usuario]);

        return (int)($results[0]['count'] ?? 0);
    }

    /**
     * @return NotificacaoDTO[]
     */
    public function getAtividadesRecentesDashboard(int $limit = 5): array
    {
        // Para o dashboard, pegamos as atividades mais recentes de todos, independentemente de quem ou se foi lida
        $sql = "
            SELECT n.*, u.nome as autor_nome 
            FROM notificacoes n
            JOIN usuarios u ON n.id_usuario = u.id_usuario
            ORDER BY n.data_criacao DESC LIMIT " . (int)$limit;
        $results = $this->db->query($sql);

        $atividades = [];
        foreach ($results as $row)
        {
            $atividades[] = new NotificacaoDTO($row);
        }
        return $atividades;
    }

    public function marcarComoLida(int $id_notificacao, int $id_usuario): bool
    {

        $sql = "UPDATE notificacoes SET data_leitura = NOW() WHERE id_notificacao = :id_notificacao AND id_usuario = :id_usuario AND data_leitura IS NULL";
        return $this->db->execute($sql, [':id_notificacao' => $id_notificacao, ':id_usuario' => $id_usuario]);
    }

    // --- Métodos para o Dashboard ---

    public function getDashboardStats(): array
    {
        $sql = "
            SELECT
                (SELECT COUNT(*) FROM usuarios WHERE tipo = 'Aluno' AND status = 'Ativo') as total_alunos,
                (SELECT COUNT(*) FROM emprestimos WHERE status = 'Emprestado') as livros_emprestados,
                (SELECT COUNT(*) FROM reservas WHERE status = 'Pendente') as reservas_ativas,
                (SELECT COUNT(*) FROM usuarios WHERE tipo = 'Professor' AND status = 'Ativo') as total_professores
        ";
        $result = $this->db->query($sql);
        return $result[0] ?? [
            'total_alunos' => 0,
            'livros_emprestados' => 0,
            'reservas_ativas' => 0,
            'total_professores' => 0
        ];
    }

    public function getAvisosRecentes(int $limit = 3): array
    {
        require_once BASE_PATH . 'core/models/AvisoDTO.php';
        $sql = "SELECT * FROM avisos ORDER BY data_postagem DESC LIMIT " . (int)$limit;
        $results = $this->db->query($sql);

        $avisos = [];
        foreach ($results as $row)
        {
            $avisos[] = AvisoDTO::fromArray($row);
        }
        return $avisos;
    }

    public function getProximosEventos(int $dias = 7): array
    {
        require_once BASE_PATH . 'core/models/EventoCalendarioDTO.php';
        $sql = "
            SELECT * FROM eventos_calendario 
            WHERE data_inicio >= CURDATE() 
              AND data_inicio <= DATE_ADD(CURDATE(), INTERVAL :dias DAY)
            ORDER BY data_inicio ASC
        ";
        $results = $this->db->query($sql, [':dias' => $dias]);

        $eventos = [];
        foreach ($results as $row)
        {
            $eventos[] = EventoCalendarioDTO::fromArray($row);
        }
        return $eventos;
    }
}
