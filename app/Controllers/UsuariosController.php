<?php
namespace App\Controllers;

use App\Models\UsuarioService;
use App\Models\DTO\UsuarioDTO;
use Core\View;

class UsuariosController
{
    public function index()
    {
        $usuarioService = new UsuarioService();
        $usuarios = $usuarioService->buscarTodosAdmins();
        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
        View::render('Adm/usuarios', [
            'usuarios' => $usuarios,
            'feedbackMessage' => $feedbackMessage
        ]);
    }

    public function salvar()
    {
        $usuarioService = new UsuarioService();
        $usuario = new UsuarioDTO(
            empty($_POST['user-id']) ? null : (int)$_POST['user-id'],
            $_POST['user-name'] ?? '',
            $_POST['user-email'] ?? '',
            status: 'Ativo',
            tipo: $_POST['user-role'] ?? ''
        );
        $feedbackMessage = $usuarioService->salvar($usuario) ? 'Usuário salvo com sucesso!' : 'Erro ao salvar usuário.';
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=configuracoes&msg=' . urlencode($feedbackMessage);
        header('Location: ' . $redirectUrl);
        exit();
    }

    public function excluir()
    {
        $usuarioService = new UsuarioService();
        $id = (int)$_POST['user-id-delete'];
        $feedbackMessage = $usuarioService->excluir($id) ? 'Usuário excluído com sucesso!' : 'Erro ao excluir usuário.';
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=configuracoes&msg=' . urlencode($feedbackMessage);
        header('Location: ' . $redirectUrl);
        exit();
    }
}
