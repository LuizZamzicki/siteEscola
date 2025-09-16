<?php
namespace App\Controllers;

use App\Models\MateriaService;
use App\Models\DTO\MateriaDTO;
use Core\View;

class MateriasController
{
    public function index()
    {
        $materiaService = new MateriaService();
        $materias = $materiaService->buscarTodas();
        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
        $feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
        View::render('Adm/materias', [
            'materias' => $materias,
            'feedbackMessage' => $feedbackMessage,
            'feedbackType' => $feedbackType
        ]);
    }

    public function salvar()
    {
        $materiaService = new MateriaService();
        $materiaDTO = new MateriaDTO(
            empty($_POST['subject-id']) ? null : (int)$_POST['subject-id'],
            $_POST['subject-name'] ?? ''
        );
        $result = $materiaService->salvar($materiaDTO);
        $feedbackMessage = $result['message'];
        $feedbackType = $result['success'] ? 'success' : 'error';
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=materias&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
        header('Location: ' . $redirectUrl);
        exit();
    }

    public function excluir()
    {
        $materiaService = new MateriaService();
        $id = (int)$_POST['id'];
        $errorMessage = '';
        $success = $materiaService->excluir($id, $errorMessage);
        $feedbackMessage = $success ? 'Matéria excluída com sucesso!' : ($errorMessage ?: 'Erro ao excluir matéria.');
        $feedbackType = $success ? 'success' : 'error';
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=materias&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
        header('Location: ' . $redirectUrl);
        exit();
    }
}
