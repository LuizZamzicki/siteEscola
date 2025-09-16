<?php

namespace App\Controllers;

use App\Models\GeneroService;
use App\Models\DTO\GeneroLivroDTO;
use Core\View;

class GenerosController
{
    public function index()
    {
        $generoService = new GeneroService();
        $generos = $generoService->buscarTodos();
        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
        $feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
        View::render('Adm/generos', [
            'generos' => $generos,
            'feedbackMessage' => $feedbackMessage,
            'feedbackType' => $feedbackType
        ]);
    }

    public function salvar()
    {
        $generoService = new GeneroService();
        $generoDTO = new GeneroLivroDTO(
            empty($_POST['genre-id']) ? null : (int)$_POST['genre-id'],
            $_POST['genre-name'] ?? ''
        );
        $success = $generoService->salvarGenero($generoDTO);
        $feedbackMessage = $success ? 'Gênero salvo com sucesso!' : 'Erro ao salvar gênero.';
        $feedbackType = $success ? 'success' : 'error';
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=generos_biblioteca&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
        header('Location: ' . $redirectUrl);
        exit();
    }

    public function excluir()
    {
        $generoService = new GeneroService();
        $id = (int)$_POST['id'];
        $errorMessage = '';
        $success = $generoService->excluirGenero($id, $errorMessage);
        $feedbackMessage = $success ? 'Gênero excluído com sucesso!' : ($errorMessage ?: 'Erro ao excluir gênero.');
        $feedbackType = $success ? 'success' : 'error';
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=generos_biblioteca&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
        header('Location: ' . $redirectUrl);
        exit();
    }
}
