<?php
namespace App\Controllers;

use App\Models\TurmaService;
use App\Models\PeriodoService;
use App\Models\DTO\TurmaDTO;
use Core\View;

class TurmasController
{
    public function index()
    {
        $turmaService = new TurmaService();
        $periodoService = new PeriodoService();
        $turmas = $turmaService->buscarTodas();
        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
        $feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
        View::render('Adm/turmas', [
            'turmas' => $turmas,
            'feedbackMessage' => $feedbackMessage,
            'feedbackType' => $feedbackType
        ]);
    }

    public function salvar()
    {
        $turmaService = new TurmaService();
        $serie = $_POST['serie'] ?? '';
        $letra = strtoupper(trim($_POST['letra'] ?? ''));
        $nome_turma = '';
        if (strpos($serie, '(Ensino Médio)') !== false)
        {
            $nome_turma = str_replace('(Ensino Médio)', ' ' . trim($letra) . ' (Ensino Médio)', $serie);
        }
        else
        {
            $nome_turma = trim("$serie $letra");
        }
        $ensino = (strpos($serie, 'Ensino Médio') !== false) ? 'Ensino Médio' : 'Ensino Fundamental';
        $turmaDTO = new TurmaDTO(
            empty($_POST['id_turma']) ? null : (int)$_POST['id_turma'],
            $nome_turma,
            $ensino,
            $_POST['periodo'] ?? ''
        );
        $feedbackMessage = $turmaService->salvar($turmaDTO) ? 'Turma salva com sucesso!' : 'Erro ao salvar a turma.';
        $feedbackType = (strpos($feedbackMessage, 'Erro') !== false) ? 'error' : 'success';
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=turmas&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
        header('Location: ' . $redirectUrl);
        exit();
    }

    public function excluir()
    {
        $turmaService = new TurmaService();
        $id = (int)$_POST['id_turma'];
        $errorMessage = '';
        $feedbackMessage = $turmaService->excluir($id, $errorMessage) ? 'Turma excluída com sucesso!' : ($errorMessage ?: 'Erro ao excluir a turma.');
        $feedbackType = (strpos($feedbackMessage, 'Erro') !== false) ? 'error' : 'success';
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=turmas&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
        header('Location: ' . $redirectUrl);
        exit();
    }
}
