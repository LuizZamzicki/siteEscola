<?php

use App\Models\HorarioService;
use Core\View;

class HorarioController
{
    public function index()
    {
        $fullSchedule = [];
        $turmaNome = $_SESSION['user_turma'] ?? 'Sua Turma';
        $turmaId = $_SESSION['user_turma_id'] ?? null;
        if ($turmaId)
        {
            $horarioService = new HorarioService();
            $periodoNome = $horarioService->buscarPeriodoNomePorTurmaId($turmaId);
            $periodoId = null;
            if ($periodoNome)
            {
                $periodoId = $horarioService->buscarPeriodoIdPorNome($periodoNome);
            }
            $timeSlotsConfig = $horarioService->buscarTodosHorariosConfig($periodoId);
            $rawSchedule = $horarioService->buscarHorarioCompletoPorTurma($turmaId);
            $diasSemanaPhp = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira'];
            $diasSemanaJs = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];
            foreach ($timeSlotsConfig as $slot)
            {
                $horarioStr = date('H:i', strtotime($slot['horario_inicio'])) . ' - ' . date('H:i', strtotime($slot['horario_fim']));
                $row = ['time' => $horarioStr];
                if ($slot['tipo'] === 'intervalo')
                {
                    $row['type'] = $slot['label'] ?: 'Intervalo';
                }
                else
                {
                    foreach ($diasSemanaPhp as $index => $diaPhp)
                    {
                        $diaJs = $diasSemanaJs[$index];
                        $aula = $rawSchedule[$diaPhp][$horarioStr] ?? null;
                        $row[$diaJs] = $aula ? $aula['materia'] : null;
                    }
                }
                $fullSchedule[] = $row;
            }
        }
        View::render('Aluno/horario', [
            'fullSchedule' => $fullSchedule,
            'turmaNome' => $turmaNome
        ]);
    }
}
