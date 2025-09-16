<?php
namespace App\Controllers;

use App\Models\PeriodoService;
use App\Models\DTO\PeriodoDTO;
use Core\View;
use App\Models\Per;

class PeriodosController
{
    public function index()
    {
        $periodoService = new PeriodoService();
        $periodos = $periodoService->buscarTodos();
        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars(urldecode($_GET['msg'])) : '';
        $feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
        View::render('Adm/periodos', [
            'periodos' => $periodos,
            'feedbackMessage' => $feedbackMessage,
            'feedbackType' => $feedbackType
        ]);
    }

    public function salvar()
    {
        $periodoService = new PeriodoService();
        $periodoDTO = new PeriodoDTO(
            empty($_POST['periodo-id']) ? null : (int)$_POST['periodo-id'],
            $_POST['periodo-name'] ?? ''
        );
        $success = $periodoService->salvar($periodoDTO);
        $feedbackMessage = $success ? 'Período salvo com sucesso!' : 'Erro ao salvar período.';
        $feedbackType = $success ? 'success' : 'error';
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=periodos&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
        header('Location: ' . $redirectUrl);
        exit();
    }

    public function excluir()
    {
        $periodoService = new PeriodoService();
        $id = (int)$_POST['id'];
        $errorMessage = '';
        $success = $periodoService->excluir($id, $errorMessage);
        $feedbackMessage = $success ? 'Período excluído com sucesso!' : ($errorMessage ?: 'Erro ao excluir período.');
        $feedbackType = $success ? 'success' : 'error';
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=periodos&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
        header('Location: ' . $redirectUrl);
        exit();
    }
}
