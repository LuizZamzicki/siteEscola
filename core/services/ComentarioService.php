<?php
require_once BASE_PATH . 'core/services/database.php';

class ComentarioService
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function buscarPorAluno(int $alunoId): ?array
    {
        return $this->db->selectOne(
            "SELECT comentario, status FROM comentarios_escola WHERE id_usuario = :alunoId ORDER BY data_submissao DESC LIMIT 1",
            [':alunoId' => $alunoId]
        );
    }

    public function salvar(int $alunoId, string $comentario): bool
    {
        // UPSERT: Insere um novo comentário ou atualiza o existente.
        // A regra de negócio é que um aluno pode ter apenas um comentário.
        // Se ele enviar um novo, o antigo é sobrescrito e o status volta para 'pendente' para nova avaliação.
        $sql = "
            INSERT INTO comentarios_escola (id_usuario, comentario, status, data_submissao)
            VALUES (:alunoId, :comentario, 'pendente', NOW())
            ON DUPLICATE KEY UPDATE comentario = :comentario, status = 'pendente', data_submissao = NOW()
        ";

        // Nota: Para 'ON DUPLICATE KEY UPDATE' funcionar, a coluna `id_usuario` deve ser uma chave única (UNIQUE KEY).
        return $this->db->execute($sql, [':alunoId' => $alunoId, ':comentario' => $comentario]);
    }
}
