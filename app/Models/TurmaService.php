<?php
namespace App\Models;

require_once BASE_PATH . 'core/models/TurmaDTO.php';
require_once BASE_PATH . 'core/services/database.php';

use Core\Database;
use App\Models\DTO\TurmaDTO;

class TurmaService
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * @return TurmaDTO[]
     */
    public function buscarTodas(): array
    {
        $sql = "
            SELECT
                t.id_turma,
                t.nome_turma,
                t.ensino,
                t.periodo,
                (SELECT COUNT(*) FROM usuarios u WHERE u.turma = t.nome_turma AND u.tipo = 'Aluno') AS total_alunos,
                (SELECT COUNT(*) FROM rel_professores_turmas rpt WHERE rpt.id_turma = t.id_turma) AS total_professores
            FROM
                turmas t
            ORDER BY
                t.nome_turma;
        ";
        $results = $this->db->query($sql);

        $turmas = [];
        foreach ($results as $row)
        {
            $turmas[] = TurmaDTO::fromArray($row);
        }
        return $turmas;
    }

    public function salvar(TurmaDTO $turma): bool
    {
        if ($turma->id)
        {
            $sql = "UPDATE turmas SET nome_turma = :nome, ensino = :ensino, periodo = :periodo WHERE id_turma = :id";
            $params = [':nome' => $turma->nome, ':ensino' => $turma->ensino, ':periodo' => $turma->periodo, ':id' => $turma->id];
        }
        else
        {
            $sql = "INSERT INTO turmas (nome_turma, ensino, periodo) VALUES (:nome, :ensino, :periodo)";
            $params = [':nome' => $turma->nome, ':ensino' => $turma->ensino, ':periodo' => $turma->periodo];
        }
        return $this->db->execute($sql, $params);
    }

    public function excluir(int $id, &$errorMessage): bool
    {
        $turma = $this->db->query("SELECT nome_turma FROM turmas WHERE id_turma = :id", [':id' => $id])[0] ?? null;
        if (!$turma)
            return false;

        $sqlCheckAlunos = "SELECT COUNT(*) as count FROM usuarios WHERE turma = :nome_turma AND tipo = 'Aluno'";
        $alunosCount = $this->db->query($sqlCheckAlunos, [':nome_turma' => $turma['nome_turma']])[0]['count'];
        if ($alunosCount > 0)
        {
            $errorMessage = "Não é possível excluir: turma possui {$alunosCount} aluno(s).";
            return false;
        }

        $sqlCheckProf = "SELECT COUNT(*) as count FROM rel_professores_turmas WHERE id_turma = :id";
        $profCount = $this->db->query($sqlCheckProf, [':id' => $id])[0]['count'];
        if ($profCount > 0)
        {
            $errorMessage = "Não é possível excluir: turma possui {$profCount} professor(es).";
            return false;
        }

        $sql = "DELETE FROM turmas WHERE id_turma = :id";
        return $this->db->execute($sql, [':id' => $id]);
    }
}
