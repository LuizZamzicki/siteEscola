<?php

namespace App\Controllers;

use App\Models\EditoraService;
use App\Models\DTO\EditoraDTO;
use Core\View;

class EditorasController
{
    public function index()
    {
        $editoraService = new EditoraService();
        $editoras = $editoraService->buscarTodos();
        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
        $feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
        View::render('Adm/editoras', [
            'editoras' => $editoras,
            'feedbackMessage' => $feedbackMessage,
            'feedbackType' => $feedbackType
        ]);
    }

    public function salvar()
    {
        $editoraService = new EditoraService();
        $editoraDTO = new EditoraDTO(
            empty($_POST['publisher-id']) ? null : (int)$_POST['publisher-id'],
            $_POST['publisher-name'] ?? ''
        );
        $success = $editoraService->salvarEditora($editoraDTO);
        $feedbackMessage = $success ? 'Editora salva com sucesso!' : 'Erro ao salvar editora.';
        $feedbackType = $success ? 'success' : 'error';
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=editoras_biblioteca&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
        header('Location: ' . $redirectUrl);
        exit();
    }

    public function excluir()
    {
        $editoraService = new EditoraService();
        $id = (int)$_POST['id'];
        $errorMessage = '';
        $success = $editoraService->excluirEditora($id, $errorMessage);
        $feedbackMessage = $success ? 'Editora excluída com sucesso!' : ($errorMessage ?: 'Erro ao excluir editora.');
        $feedbackType = $success ? 'success' : 'error';
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=editoras_biblioteca&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
        header('Location: ' . $redirectUrl);
        exit();
    }
}
