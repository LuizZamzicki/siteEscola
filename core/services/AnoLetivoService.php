<?php
require_once BASE_PATH . 'core/services/database.php';
require_once BASE_PATH . 'core/services/TurmaService.php';

class AnoLetivoService
{
    private Database $db;
    private TurmaService $turmaService;

    public function __construct()
    {
        $this->db = new Database();
        $this->turmaService = new TurmaService();
    }

    /**
     * Gera um plano de progressão para todas as turmas.
     * @return array
     */
    public function getProgressoTurmas(): array
    {
        $turmas = $this->turmaService->buscarTodas();
        $nomesDeTurmasExistentes = array_map(fn($t) => $t->nome, $turmas);
        $plano = [];

        foreach ($turmas as $turma)
        {
            $itemPlano = [
                'turma_atual' => $turma,
                'proxima_turma_nome' => null,
                'status' => '',
                'status_cor' => ''
            ];

            if (str_contains($turma->nome, '3º Ano'))
            {
                $itemPlano['status'] = 'Formar Alunos';
                $itemPlano['status_cor'] = 'bg-blue-100 text-blue-800';
            }
            else
            {
                $proximaTurmaNome = $this->getProximaTurmaNome($turma->nome);
                $itemPlano['proxima_turma_nome'] = $proximaTurmaNome;

                if ($proximaTurmaNome && in_array($proximaTurmaNome, $nomesDeTurmasExistentes, true))
                {
                    $itemPlano['status'] = 'OK para avançar';
                    $itemPlano['status_cor'] = 'bg-green-100 text-green-800';
                }
                else
                {
                    $itemPlano['status'] = 'Próxima turma não encontrada';
                    $itemPlano['status_cor'] = 'bg-amber-100 text-amber-800';
                }
            }
            $plano[] = $itemPlano;
        }
        return $plano;
    }

    /**
     * Executa o avanço de ano letivo para todos os alunos.
     * @return bool
     */
    public function avancarAnoLetivo(): bool
    {
        $plano = $this->getProgressoTurmas();
        $this->db->beginTransaction();

        try
        {
            foreach ($plano as $item)
            {
                $turmaAtual = $item['turma_atual']->nome;

                if ($item['status'] === 'Formar Alunos')
                {
                    $sql = "UPDATE usuarios SET status = 'Formado', turma = NULL WHERE turma = :turma_atual AND tipo = 'Aluno'";
                    $this->db->execute($sql, [':turma_atual' => $turmaAtual]);
                }
                elseif ($item['status'] === 'OK para avançar')
                {
                    $proximaTurma = $item['proxima_turma_nome'];
                    $sql = "UPDATE usuarios SET turma = :proxima_turma WHERE turma = :turma_atual AND tipo = 'Aluno'";
                    $this->db->execute($sql, [':proxima_turma' => $proximaTurma, ':turma_atual' => $turmaAtual]);
                }
            }
            $this->db->commit();
            return true;
        }
        catch (Exception $e)
        {
            $this->db->rollBack();
            return false;
        }
    }

    private function getProximaTurmaNome(string $nomeAtual): ?string
    {
        $nomeLimpo = trim($nomeAtual);

        // Padrão 1: Ensino Médio, formato "1º Ano A (Ensino Médio)"
        if (preg_match('/^(\d+)º Ano\s+([A-Z]{1,2})\s+\(Ensino Médio\)$/u', $nomeLimpo, $matches))
        {
            $proximoNumero = (int)$matches[1] + 1;
            $letraTurma = $matches[2];
            return "{$proximoNumero}º Ano (Ensino Médio) {$letraTurma}";
        }

        // Padrão 2: Ensino Médio, formato "2º Ano (Ensino Médio) B"
        if (preg_match('/^(\d+)º Ano\s+\(Ensino Médio\)\s+([A-Z]{1,2})$/u', $nomeLimpo, $matches))
        {
            $proximoNumero = (int)$matches[1] + 1;
            $letraTurma = $matches[2];
            return "{$proximoNumero}º Ano (Ensino Médio) {$letraTurma}";
        }

        // Padrão 3: Ensino Médio, sem letra. Ex: "1º Ano (Ensino Médio)"
        if (preg_match('/^(\d+)º Ano\s+\(Ensino Médio\)$/u', $nomeLimpo, $matches))
        {
            $proximoNumero = (int)$matches[1] + 1;
            return "{$proximoNumero}º Ano (Ensino Médio)";
        }

        // Padrão 4: Transição do 9º ano. Ex: "9º Ano C"
        if (preg_match('/^9º Ano\s+([A-Z]{1,2})$/u', $nomeLimpo, $matches))
        {
            $letraTurma = $matches[1];
            return "1º Ano (Ensino Médio) {$letraTurma}";
        }

        // Padrão 5: Transição do 9º ano, sem letra. Ex: "9º Ano"
        if (preg_match('/^9º Ano$/u', $nomeLimpo))
        {
            return "1º Ano (Ensino Médio)";
        }

        // Padrão 6: Ensino Fundamental. Ex: "8º Ano D"
        if (preg_match('/^(\d+)º Ano\s+([A-Z]{1,2})$/u', $nomeLimpo, $matches))
        {
            $proximoNumero = (int)$matches[1] + 1;
            $letraTurma = $matches[2];
            return "{$proximoNumero}º Ano {$letraTurma}";
        }

        // Padrão 7: Ensino Fundamental, sem letra. Ex: "7º Ano"
        if (preg_match('/^(\d+)º Ano$/u', $nomeLimpo, $matches))
        {
            $proximoNumero = (int)$matches[1] + 1;
            return "{$proximoNumero}º Ano";
        }
        return null; // Se nenhum padrão corresponder
    }
}
