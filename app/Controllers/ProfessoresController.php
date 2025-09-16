<?php
namespace App\Controllers;

use App\Models\UsuarioService;
use App\Models\TurmaService;
use App\Models\DTO\UsuarioDTO;
use Core\View;

class ProfessoresController
{
    public function index()
    {
        $usuarioService = new UsuarioService();
        $turmaService = new TurmaService();
        $professores = $usuarioService->buscarTodosProfessores();
        $turmas = $turmaService->buscarTodas();
        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
        View::render('Adm/professores', [
            'professores' => $professores,
            'turmas' => $turmas,
            'feedbackMessage' => $feedbackMessage
        ]);
    }

    public function salvar()
    {
        $usuarioService = new UsuarioService();
        $professorDTO = new UsuarioDTO(
            empty($_POST['teacher-id']) ? null : (int)$_POST['teacher-id'],
            $_POST['teacher-name'] ?? '',
            $_POST['teacher-email'] ?? ''
        );
        $turmasIds = $_POST['teacher_classes'] ?? [];
        $feedbackMessage = $usuarioService->salvarProfessor($professorDTO, $turmasIds) ? 'Professor salvo com sucesso!' : 'Erro ao salvar professor.';
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=professores&msg=' . urlencode($feedbackMessage);
        header('Location: ' . $redirectUrl);
        exit();
    }

    public function desativar()
    {
        $usuarioService = new UsuarioService();
        $id = (int)$_POST['teacher-id-deactivate'];
        $feedbackMessage = $usuarioService->desativar($id) ? 'Professor desativado com sucesso!' : 'Erro ao desativar professor.';
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=professores&msg=' . urlencode($feedbackMessage);
        header('Location: ' . $redirectUrl);
        exit();
    }
}
