<?php

namespace App\Controllers;

use Core\View;
use App\Models\EventoCalendarioService;

class CalendarioController
{
    private EventoCalendarioService $eventoService;

    public function __construct()
    {
        $this->eventoService = new EventoCalendarioService();
    }

    /**
     * Exibe o calendário do aluno.
     */
    public function index()
    {
        if (!isset($_SESSION['user_turma']))
        {
            View::render('error', ['message' => 'Turma do aluno não encontrada.']);
            return;
        }
        $turmaAluno = $_SESSION['user_turma'];
        $eventosDoBanco = $this->eventoService->buscarParaAluno($turmaAluno);
        View::render('aluno/calendario', [
            'eventosDoBanco' => $eventosDoBanco
        ], 'aluno');
    }
}
