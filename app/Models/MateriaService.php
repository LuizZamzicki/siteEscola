<?php

require_once BASE_PATH . 'core/services/database.php';
require_once BASE_PATH . 'core/models/MateriaDTO.php';
require_once BASE_PATH . 'Utils/funcoesUtils.php';

namespace App\Models;
use Core\Database;
use App\Models\DTO\MateriaDTO;


class MateriaService
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * @return MateriaDTO[]
     */
    public function buscarTodas(): array
    {
        $sql = "SELECT * FROM materias ORDER BY nome ASC";
        $results = $this->db->query($sql);

        return array_map([MateriaDTO::class, 'fromArray'], $results);
    }

    public function buscarPorNome(string $nome): ?MateriaDTO
    {
        $sql = "SELECT * FROM materias WHERE nome = :nome";
        $result = $this->db->query($sql, [':nome' => $nome]);
        return empty($result) ? null : MateriaDTO::fromArray($result[0]);
    }

    public function salvar(MateriaDTO $materia): array
    {
        try
        {
            if ($materia->id)
            {
                $sql = "UPDATE materias SET nome = :nome WHERE id_materia = :id";
                $params = [':nome' => $materia->nome, ':id' => $materia->id];
            }
            else
            {
                $sql = "INSERT INTO materias (nome) VALUES (:nome)";
                $params = [':nome' => $materia->nome];
            }

            if ($this->db->execute($sql, $params))
            {
                $id = $materia->id ?: $this->db->lastInsertId();
                return ['success' => true, 'message' => 'Matéria salva com sucesso!', 'id' => (int)$id];
            }
            return ['success' => false, 'message' => 'Erro ao salvar a matéria.'];
        }
        catch (PDOException $e)
        {
            if ($e->getCode() == '23000')
            { // Integrity constraint violation
                return ['success' => false, 'message' => 'Erro: Já existe uma matéria com este nome.'];
            }
            error_log("MateriaService::salvar: " . $e->getMessage());
            return ['success' => false, 'message' => 'Ocorreu um erro no banco de dados.'];
        }
    }

    public function excluir(int $id, &$errorMessage): bool
    {
        // Verifica se a matéria está sendo usada em alguma grade de horário
        $sqlCheck = "SELECT COUNT(*) as count FROM horario_aulas WHERE id_materia = :id";
        $result = $this->db->query($sqlCheck, [':id' => $id]);
        $count = $result[0]['count'] ?? 0;

        if ($count > 0)
        {
            $errorMessage = "Não é possível excluir: a matéria está associada a {$count} aula(s) em grades de horário.";
            return false;
        }

        $sql = "DELETE FROM materias WHERE id_materia = :id";
        if (!$this->db->execute($sql, [':id' => $id]))
        {
            $errorMessage = "Erro ao excluir a matéria.";
            return false;
        }
        return true;
    }
}
