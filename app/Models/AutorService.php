<?php
namespace App\Models;

require_once BASE_PATH . 'core/models/AutorDTO.php';
require_once BASE_PATH . 'core/services/database.php';

use Core\Database;
use App\Models\DTO\AutorDTO;

class AutorService
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function consultarOuCriarAutor(string $nome, ?int $idPais = null): int
    {
        $sql = "SELECT id_autor FROM autores WHERE nome = :nome";
        $result = $this->db->query($sql, [':nome' => $nome]);

        if (!empty($result))
        {
            return $result[0]['id_autor'];
        }

        $sql = "INSERT INTO autores (nome, id_pais) VALUES (:nome, :id_pais)";
        $this->db->execute($sql, [':nome' => $nome, ':id_pais' => $idPais]);

        return $this->db->lastInsertId();
    }

    public function buscarTodos(): array
    {
        $sql = "SELECT a.id_autor, a.nome, a.id_pais, p.nome as nome_pais 
                FROM autores a
                LEFT JOIN paises p ON a.id_pais = p.id_pais
                ORDER BY a.nome";
        $results = $this->db->query($sql);
        return array_map([AutorDTO::class, 'fromArray'], $results);
    }

    public function buscarAutorPorId(int $id): ?AutorDTO
    {
        $sql = "SELECT a.id_autor, a.nome, a.id_pais, p.nome as nome_pais 
            FROM autores a 
            LEFT JOIN paises p ON a.id_pais = p.id_pais 
            WHERE a.id_autor = :id";
        $result = $this->db->query($sql, [':id' => $id]);
        if (empty($result))
        {
            return null;
        }
        return AutorDTO::fromArray($result[0]);
    }

    public function salvarAutor(AutorDTO $autor): bool
    {
        if ($autor->id)
        {
            $sql = "UPDATE autores SET nome = :nome, id_pais = :id_pais WHERE id_autor = :id";
            $params = [':nome' => $autor->nome, ':id_pais' => $autor->idPais, ':id' => $autor->id];
        }
        else
        {
            $sql = "INSERT INTO autores (nome, id_pais) VALUES (:nome, :id_pais)";
            $params = [':nome' => $autor->nome, ':id_pais' => $autor->idPais];
        }
        return $this->db->execute($sql, $params);
    }

    public function excluirAutor(int $id, &$errorMessage): bool
    {
        $sqlCheck = "SELECT COUNT(*) as count FROM livros WHERE id_autor = :id";
        $count = $this->db->query($sqlCheck, [':id' => $id])[0]['count'];

        if ($count > 0)
        {
            $errorMessage = "Não é possível excluir: autor está associado a {$count} livro(s).";
            return false;
        }
        return $this->db->execute("DELETE FROM autores WHERE id_autor = :id", [':id' => $id]);
    }
}
