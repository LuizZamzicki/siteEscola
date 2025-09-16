<?php

namespace App\Controllers;

use Core\View;
use App\Models\UsuarioService;
use App\Models\TurmaService;

class CadAlunosController
{
    private UsuarioService $usuarioService;
    private TurmaService $turmaService;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
        $this->turmaService = new TurmaService();
    }

    public function index()
    {
        $alunos = $this->usuarioService->buscarTodosAlunos();
        $turmas = $this->turmaService->buscarTodas();
        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
        View::render('adm/cad_alunos', [
            'alunos' => $alunos,
            'turmas' => $turmas,
            'feedbackMessage' => $feedbackMessage
        ], 'adm');
    }

    public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $action = $_POST['action'] ?? '';
            $feedbackMessage = '';
            if ($action === 'salvar_aluno')
            {
                $alunoDTO = new \App\Models\DTO\UsuarioDTO(
                    empty($_POST['student-id']) ? null : (int)$_POST['student-id'],
                    $_POST['student-name'] ?? '',
                    $_POST['student-email'] ?? '',
                    null,
                    'Ativo',
                    'Aluno',
                    $_POST['student-class'] ?? null
                );
                $feedbackMessage = $this->usuarioService->salvar($alunoDTO) ? "Aluno salvo com sucesso!" : "Erro ao salvar aluno.";
            }
            if ($action === 'desativar_aluno')
            {
                $id = (int)$_POST['deactivate-student-id'];
                $feedbackMessage = $this->usuarioService->desativar($id) ? "Aluno desativado com sucesso!" : "Erro ao desativar aluno.";
            }
            $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=alunos&msg=' . urlencode($feedbackMessage);
            header("Location: " . $redirectUrl);
            exit();
        }
    }
}
