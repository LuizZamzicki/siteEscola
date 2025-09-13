<?php
require_once BASE_PATH . 'core/services/database.php';
require_once BASE_PATH . 'core/models/GeneroLivroDTO.php';

class GeneroService
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function consultarOuCriarGenero(string $descricao): int
    {
        $sql = "SELECT id_genero_livro FROM generos_livros WHERE descricao = :descricao";
        $result = $this->db->query($sql, [':descricao' => $descricao]);

        if (!empty($result))
        {
            return $result[0]['id_genero_livro'];
        }

        $sql = "INSERT INTO generos_livros (descricao) VALUES (:descricao)";
        $this->db->execute($sql, [':descricao' => $descricao]);

        return $this->db->lastInsertId();
    }

    public function buscarTodos(): array
    {
        $sql = "SELECT id_genero_livro, descricao FROM generos_livros ORDER BY descricao";
        $results = $this->db->query($sql);
        return array_map([GeneroLivroDTO::class, 'fromArray'], $results);
    }

    public function salvarGenero(GeneroLivroDTO $genero): bool
    {
        if ($genero->id)
        {
            $sql = "UPDATE generos_livros SET descricao = :descricao WHERE id_genero_livro = :id";
            $params = [':descricao' => $genero->descricao, ':id' => $genero->id];
        }
        else
        {
            $sql = "INSERT INTO generos_livros (descricao) VALUES (:descricao)";
            $params = [':descricao' => $genero->descricao];
        }
        return $this->db->execute($sql, $params);
    }

    public function excluirGenero(int $id, &$errorMessage): bool
    {
        $sqlCheck = "SELECT COUNT(*) as count FROM rel_livros_generos WHERE id_genero = :id";
        $count = $this->db->query($sqlCheck, [':id' => $id])[0]['count'];

        if ($count > 0)
        {
            $errorMessage = "Não é possível excluir: gênero está associado a {$count} livro(s).";
            return false;
        }
        return $this->db->execute("DELETE FROM generos_livros WHERE id_genero_livro = :id", [':id' => $id]);
    }
}
