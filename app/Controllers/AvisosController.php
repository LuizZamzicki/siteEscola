<?php

namespace App\Controllers;

use Core\View;
use App\Models\AvisoService;
use App\Models\TurmaService;

class AvisosController
{
    private AvisoService $avisoService;
    private TurmaService $turmaService;

    public function __construct()
    {
        $this->avisoService = new AvisoService();
        $this->turmaService = new TurmaService();
    }

    public function index()
    {
        $avisos = $this->avisoService->buscarTodos();
        $turmas = $this->turmaService->buscarTodas();
        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
        $feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
        View::render('adm/avisos', [
            'avisos' => $avisos,
            'turmas' => $turmas,
            'feedbackMessage' => $feedbackMessage,
            'feedbackType' => $feedbackType
        ], 'adm');
    }

    public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $action = $_POST['action'] ?? '';
            $feedbackMessage = '';
            $feedbackType = 'success';
            if ($action === 'salvar_aviso')
            {
                $avisoDTO = new \App\Models\DTO\AvisoDTO(
                    empty($_POST['notice-id']) ? null : (int)$_POST['notice-id'],
                    $_POST['notice-title'] ?? '',
                    $_POST['notice-content'] ?? '',
                    $_POST['notice-target'] ?? 'todos'
                );
                $feedbackMessage = $this->avisoService->salvar($avisoDTO) ? "Aviso salvo com sucesso!" : "Erro ao salvar o aviso.";
                if (strpos($feedbackMessage, 'Erro') !== false)
                    $feedbackType = 'error';
            }
            if ($action === 'excluir_aviso')
            {
                $id = (int)$_POST['notice-id-delete'];
                $feedbackMessage = $this->avisoService->excluir($id) ? "Aviso excluído com sucesso!" : "Erro ao excluir o aviso.";
                if (strpos($feedbackMessage, 'Erro') !== false)
                    $feedbackType = 'error';
            }
            $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=avisos&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
            header("Location: " . $redirectUrl);
            exit();
        }
    }
}
