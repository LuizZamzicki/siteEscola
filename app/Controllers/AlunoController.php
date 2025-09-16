<?php

namespace App\Controllers;


use Core\View;
use App\Models\UsuarioService;
use App\Models\ComentarioService;

if (!function_exists('redirect'))
{
    function redirect($url)
    {
        header('Location: ' . $url);
        exit();
    }
}

class AlunoController
{
    private UsuarioService $usuarioService;
    private ComentarioService $comentarioService;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
        $this->comentarioService = new ComentarioService();
    }

    /**
     * Exibe a página de perfil do aluno.
     */
    public function perfil()
    {
        if (!isset($_SESSION['user_id']))
        {
            redirect('login');
        }

        $alunoId = $_SESSION['user_id'];
        $aluno = $this->usuarioService->buscarUsuarioPorId($alunoId);
        $meuComentario = $this->comentarioService->buscarPorAluno($alunoId);

        if (!$aluno)
        {
            // Lidar com erro, talvez redirecionar ou mostrar uma página de erro
            View::render('error', ['message' => 'Não foi possível carregar os dados do aluno.']);
            return;
        }

        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
        $feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';

        $data = [
            'aluno' => $aluno,
            'meuComentario' => $meuComentario,
            'feedbackMessage' => $feedbackMessage,
            'feedbackType' => $feedbackType,
            'errorMessage' => ''
        ];

        View::render('aluno/perfil', $data, 'aluno');
    }

    /**
     * Processa o formulário de salvar comentário.
     */
    public function salvarComentario()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']))
        {
            redirect('perfil'); // Redireciona se não for POST ou não estiver logado
        }

        $alunoId = $_SESSION['user_id'];
        $comentarioTexto = $_POST['testimonial-text'] ?? '';

        $resultado = $this->comentarioService->salvarComValidacao($alunoId, $comentarioTexto);

        $queryParams = http_build_query([
            'msg' => $resultado['mensagem'],
            'type' => $resultado['tipo']
        ]);
        redirect('perfil?' . $queryParams);
    }
}
