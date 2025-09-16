<?php

namespace App\Controllers;

use Core\View;
use App\Models\AnoLetivoService;

class AvancarAnoController
{
    private AnoLetivoService $anoLetivoService;

    public function __construct()
    {
        $this->anoLetivoService = new AnoLetivoService();
    }

    public function index()
    {
        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
        $feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
        $planoDeProgresso = $this->anoLetivoService->getProgressoTurmas();
        View::render('adm/avancar_ano', [
            'planoDeProgresso' => $planoDeProgresso,
            'feedbackMessage' => $feedbackMessage,
            'feedbackType' => $feedbackType
        ], 'adm');
    }

    public function avancar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'avancar_ano')
        {
            $feedbackMessage = '';
            $feedbackType = 'success';
            if ($this->anoLetivoService->avancarAnoLetivo())
            {
                $feedbackMessage = "Ano letivo avançado com sucesso! Alunos foram movidos para as novas turmas e formandos foram atualizados.";
            }
            else
            {
                $feedbackMessage = "Ocorreu um erro ao avançar o ano letivo. Nenhuma alteração foi feita.";
                $feedbackType = 'error';
            }
            $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=avancar_ano&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
            header("Location: " . $redirectUrl);
            exit();
        }
    }
}
