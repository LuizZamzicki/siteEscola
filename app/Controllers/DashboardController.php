<?php

use App\Models\NotificacaoService;
use App\Models\CardapioService;
use App\Models\HorarioService;
use Core\View;

class DashboardController
{
    public function index()
    {
        $notificacaoService = new NotificacaoService();
        $cardapioService = new CardapioService();
        $avisos = $notificacaoService->getAvisosRecentes(2);
        $proximosEventos = $notificacaoService->getProximosEventos(7);
        $cardapioDeHoje = $cardapioService->buscarDeHoje();
        $todaySchedule = [];
        if (isset($_SESSION['user_turma_id']) && $_SESSION['user_turma_id'] > 0)
        {
            $horarioService = new HorarioService();
            $periodoNome = $horarioService->buscarPeriodoNomePorTurmaId($_SESSION['user_turma_id']);
            $periodoId = null;
            if ($periodoNome)
            {
                $periodoId = $horarioService->buscarPeriodoIdPorNome($periodoNome);
            }
            $timeSlotsConfig = $horarioService->buscarTodosHorariosConfig($periodoId);
            $rawSchedule = $horarioService->buscarHorarioCompletoPorTurma($_SESSION['user_turma_id']);
            $diasDaSemanaMap = [1 => 'Segunda-feira', 2 => 'Terça-feira', 3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira'];
            $todayIndex = date('N');
            if (isset($diasDaSemanaMap[$todayIndex]))
            {
                $todayName = $diasDaSemanaMap[$todayIndex];
                foreach ($timeSlotsConfig as $slot)
                {
                    $horarioStr = date('H:i', strtotime($slot['horario_inicio'])) . ' - ' . date('H:i', strtotime($slot['horario_fim']));
                    $subject = null;
                    if ($slot['tipo'] === 'intervalo')
                    {
                        $subject = $slot['label'] ?: 'Intervalo';
                    }
                    else if (isset($rawSchedule[$todayName][$horarioStr]))
                    {
                        $subject = $rawSchedule[$todayName][$horarioStr]['materia'] ?? null;
                    }
                    if ($subject)
                    {
                        $todaySchedule[] = ['time' => $horarioStr, 'subject' => $subject];
                    }
                }
            }
        }
        View::render('Aluno/dashboard', [
            'avisos' => $avisos,
            'proximosEventos' => $proximosEventos,
            'cardapioDeHoje' => $cardapioDeHoje,
            'todaySchedule' => $todaySchedule
        ]);
    }
}
