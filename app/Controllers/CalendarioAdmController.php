<?php

namespace App\Controllers;

use Core\View;
use App\Models\EventoCalendarioService;
use App\Models\TurmaService;

class CalendarioAdmController
{
    private EventoCalendarioService $eventoService;
    private TurmaService $turmaService;

    public function __construct()
    {
        $this->eventoService = new EventoCalendarioService();
        $this->turmaService = new TurmaService();
    }

    public function index()
    {
        $eventosDoBanco = $this->eventoService->buscarTodos();
        $turmas = $this->turmaService->buscarTodas();
        $caminhoJS = 'Features_Area_Adm/calendario/calendario_adm.js';
        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
        View::render('adm/calendario_adm', [
            'eventosDoBanco' => $eventosDoBanco,
            'turmas' => $turmas,
            'caminhoJS' => $caminhoJS,
            'feedbackMessage' => $feedbackMessage
        ], 'adm');
    }

    public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $action = $_POST['action'] ?? '';
            $feedbackMessage = '';
            if ($action === 'salvar_evento')
            {
                $evento = new \App\Models\DTO\EventoCalendarioDTO(
                    empty($_POST['event-id']) ? null : (int)$_POST['event-id'],
                    $_POST['event-title'] ?? '',
                    $_POST['event-date'] ?? '',
                    empty($_POST['event-end-date']) ? null : $_POST['event-end-date'],
                    $_POST['event-type'] ?? 'evento',
                    $_POST['event-target'] ?? 'todos',
                    isset($_POST['event-recurring'])
                );
                $feedbackMessage = $this->eventoService->salvar($evento) ? "Evento salvo com sucesso!" : "Erro ao salvar evento.";
            }
            if ($action === 'excluir_evento')
            {
                $id = (int)$_POST['event-id-delete'];
                $feedbackMessage = $this->eventoService->excluir($id) ? "Evento excluído com sucesso!" : "Erro ao excluir evento.";
            }
            $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=calendario&msg=' . urlencode($feedbackMessage);
            header("Location: " . $redirectUrl);
            exit();
        }
    }
}
