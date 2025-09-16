<?php
namespace App\Models;

use Core\Database;
use App\Models\DTO\EditoraDTO;

class EditoraService
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function consultarOuCriarEditora(string $nome): int
    {
        $sql = "SELECT id_editora FROM editoras WHERE nome = :nome";
        $result = $this->db->query($sql, [':nome' => $nome]);

        if (!empty($result))
        {
            return $result[0]['id_editora'];
        }

        $sql = "INSERT INTO editoras (nome) VALUES (:nome)";
        $this->db->execute($sql, [':nome' => $nome]);

        return $this->db->lastInsertId();
    }

    public function buscarTodos(): array
    {
        $sql = "SELECT id_editora, nome FROM editoras ORDER BY nome";
        $results = $this->db->query($sql);
        return array_map([EditoraDTO::class, 'fromArray'], $results);
    }

    public function salvarEditora(EditoraDTO $editora): bool
    {
        if ($editora->id)
        {
            $sql = "UPDATE editoras SET nome = :nome WHERE id_editora = :id";
            $params = [':nome' => $editora->nome, ':id' => $editora->id];
        }
        else
        {
            $sql = "INSERT INTO editoras (nome) VALUES (:nome)";
            $params = [':nome' => $editora->nome];
        }
        return $this->db->execute($sql, $params);
    }

    public function excluirEditora(int $id, &$errorMessage): bool
    {
        $sqlCheck = "SELECT COUNT(*) as count FROM livros WHERE id_editora = :id";
        $count = $this->db->query($sqlCheck, [':id' => $id])[0]['count'];

        if ($count > 0)
        {
            $errorMessage = "Não é possível excluir: editora está associada a {$count} livro(s).";
            return false;
        }
        return $this->db->execute("DELETE FROM editoras WHERE id_editora = :id", [':id' => $id]);
    }
}