<?php
namespace App\Controllers;

use App\Models\NotificacaoService;
use Core\View;

class DashBoardAdmController
{
    public function index()
    {
        $notificacaoService = new NotificacaoService();
        $stats = $notificacaoService->getDashboardStats();
        $atividades = $notificacaoService->getAtividadesRecentesDashboard(5);
        $avisos = $notificacaoService->getAvisosRecentes(3);
        $proximosEventos = $notificacaoService->getProximosEventos(7);

        View::render('Adm/dashboard', [
            'stats' => $stats,
            'atividades' => $atividades,
            'avisos' => $avisos,
            'proximosEventos' => $proximosEventos
        ]);
    }
}
