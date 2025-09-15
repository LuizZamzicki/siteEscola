<?php
require_once BASE_PATH . 'core/services/database.php';

class AvaliacaoService
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Salva ou atualiza a avaliação de um livro por um aluno.
     * Recalcula a nota média do livro após a operação.
     * @return array
     */
    public function salvarAvaliacao(int $alunoId, int $livroId, int $nota, ?string $comentario): array
    {
        // Validação da nota
        if ($nota < 1 || $nota > 5)
        {
            return ['success' => false, 'message' => 'A nota deve ser entre 1 e 5.'];
        }

        // Regra de negócio: Apenas permite avaliar se o aluno já pegou o livro emprestado alguma vez.
        $jaEmprestou = $this->db->selectOne(
            "SELECT 1 FROM emprestimos WHERE id_usuario = :alunoId AND id_livro = :livroId LIMIT 1",
            [':alunoId' => $alunoId, ':livroId' => $livroId]
        );

        if (!$jaEmprestou)
        {
            return ['success' => false, 'message' => 'Você precisa ter emprestado este livro para poder avaliá-lo.'];
        }

        $this->db->beginTransaction();
        try
        {
            // Verifica se já existe uma avaliação para inserir ou atualizar (UPSERT)
            $sql = "
                INSERT INTO avaliacoes_livros (id_usuario, id_livro, nota, comentario, data_avaliacao)
                VALUES (:alunoId, :livroId, :nota, :comentario, NOW())
                ON DUPLICATE KEY UPDATE nota = :nota, comentario = :comentario, data_avaliacao = NOW()
            ";
            $this->db->execute($sql, [
                ':alunoId' => $alunoId,
                ':livroId' => $livroId,
                ':nota' => $nota,
                ':comentario' => $comentario
            ]);

            // Após salvar, recalcula a média e o total de avaliações do livro
            $stats = $this->db->selectOne(
                "SELECT AVG(nota) as media, COUNT(id_avaliacao) as total FROM avaliacoes_livros WHERE id_livro = :livroId",
                [':livroId' => $livroId]
            );

            if ($stats)
            {
                $this->db->execute(
                    "UPDATE livros SET nota_media = :media, total_avaliacoes = :total WHERE id_livro = :livroId",
                    [':media' => $stats->media, ':total' => $stats->total, ':livroId' => $livroId]
                );
            }

            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Sua avaliação foi registrada com sucesso!',
                'new_average_rating' => round($stats->media, 2),
                'new_total_ratings' => (int)$stats->total
            ];
        }
        catch (Exception $e)
        {
            $this->db->rollBack();
            error_log("Erro ao salvar avaliação: " . $e->getMessage());
            return ['success' => false, 'message' => 'Ocorreu um erro interno ao salvar sua avaliação.'];
        }
    }

    /**
     * Busca livros que o aluno já leu mas ainda não avaliou.
     * @return array
     */
    public function buscarAvaliacoesPendentesPorAluno(int $alunoId): array
    {
        try
        {
            // Seleciona os IDs de livros que o aluno já emprestou e devolveu, de forma única.
            $sql = "
                SELECT DISTINCT e.id_livro, l.titulo
                FROM emprestimos e
                JOIN livros l ON e.id_livro = l.id_livro
                WHERE e.id_usuario = :alunoId
                  AND e.status = 'Devolvido'
                  AND e.id_livro NOT IN (
                      SELECT av.id_livro
                      FROM avaliacoes_livros av
                      WHERE av.id_usuario = :alunoId
                  )
                ORDER BY e.data_devolucao_real DESC
                LIMIT 5 -- Limita para não sobrecarregar o usuário com pop-ups
            ";

            return $this->db->query($sql, [':alunoId' => $alunoId]);
        }
        catch (Exception $e)
        {
            error_log("Erro ao buscar avaliações pendentes: " . $e->getMessage());
            return [];
        }
    }
}
