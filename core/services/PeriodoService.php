<?php
require_once BASE_PATH . 'core/models/PeriodoDTO.php';
require_once BASE_PATH . 'core/services/database.php';

class PeriodoService
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Busca todos os períodos cadastrados.
     * @return PeriodoDTO[]
     */
    public function buscarTodos(): array
    {
        $sql = "SELECT id, nome FROM periodos ORDER BY nome ASC";
        $results = $this->db->query($sql);

        $periodos = [];
        foreach ($results as $row)
        {
            $periodos[] = PeriodoDTO::fromArray($row);
        }
        return $periodos;
    }

    /**
     * Salva um novo período ou atualiza um existente.
     */
    public function salvar(PeriodoDTO $periodo): bool
    {
        try
        {
            if ($periodo->id)
            {
                $sql = "UPDATE periodos SET nome = :nome WHERE id = :id";
                $params = [':nome' => $periodo->nome, ':id' => $periodo->id];
            }
            else
            {
                $sql = "INSERT INTO periodos (nome) VALUES (:nome)";
                $params = [':nome' => $periodo->nome];
            }
            return $this->db->execute($sql, $params);
        }
        catch (PDOException $e)
        {
            // Retorna falso se houver um erro, como uma violação de nome único.
            error_log("PeriodoService::salvar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui um período, verificando antes se ele não está em uso.
     */
    public function excluir(int $id, &$errorMessage): bool
    {
        // Primeiro, obtemos o nome do período que será excluído.
        $periodo = $this->db->query("SELECT nome FROM periodos WHERE id = :id", [':id' => $id])[0] ?? null;
        if (!$periodo)
        {
            $errorMessage = "Período não encontrado.";
            return false;
        }
        $periodoNome = $periodo['nome'];

        // Verificamos se o período está sendo usado em alguma turma.
        $sqlCheck = "SELECT COUNT(*) as count FROM turmas WHERE periodo = :nome";
        $count = $this->db->query($sqlCheck, [':nome' => $periodoNome])[0]['count'];

        if ($count > 0)
        {
            $errorMessage = "Não é possível excluir: o período está associado a {$count} turma(s).";
            return false;
        }

        $sql = "DELETE FROM periodos WHERE id = :id";
        return $this->db->execute($sql, [':id' => $id]);
    }
}
