<?php
namespace App\Controllers;

use App\Models\TurmaService;
use App\Models\HorarioService;
use App\Models\MateriaService;
use App\Models\UsuarioService;
use Core\View;

class HorariosController
{
    public function index()
    {
        $turmaService = new TurmaService();
        $horarioService = new HorarioService();
        $materiaService = new MateriaService();
        $usuarioService = new UsuarioService();
        $turmas = $turmaService->buscarTodas();
        $professores = $usuarioService->buscarTodosProfessores();
        $materias = $materiaService->buscarTodas();
        $selectedTurmaId = $_GET['turma_id'] ?? ($turmas[0]->id ?? null);
        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars(urldecode($_GET['msg'])) : '';
        $feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
        View::render('Adm/horarios', [
            'turmas' => $turmas,
            'professores' => $professores,
            'materias' => $materias,
            'selectedTurmaId' => $selectedTurmaId,
            'feedbackMessage' => $feedbackMessage,
            'feedbackType' => $feedbackType
        ]);
    }

    public function salvar()
    {
        $horarioService = new HorarioService();
        $turmaId = (int)$_POST['turma_id'];
        $diaSemana = $_POST['dia_semana'];
        $horario = $_POST['horario'];
        $materiaId = (int)($_POST['materia_id'] ?? 0);
        $professorId = (int)$_POST['professor_id'];
        if (empty($materiaId) || empty($professorId))
        {
            $result = ['success' => false, 'message' => 'Matéria e professor são obrigatórios.'];
        }
        else
        {
            $result = $horarioService->salvarAula($turmaId, $diaSemana, $horario, $materiaId, $professorId);
        }
        $feedbackMessage = $result['message'] ?? 'Ação desconhecida.';
        $feedbackType = ($result['success'] ?? false) ? 'success' : 'error';
        $queryParams = $_GET;
        $queryParams['msg'] = urlencode($feedbackMessage);
        $queryParams['type'] = $feedbackType;
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?' . http_build_query($queryParams);
        header('Location: ' . $redirectUrl);
        exit();
    }

    public function excluir()
    {
        $horarioService = new HorarioService();
        $turmaId = (int)$_POST['turma_id'];
        $diaSemana = $_POST['dia_semana'];
        $horario = $_POST['horario'];
        $result = $horarioService->excluirAula($turmaId, $diaSemana, $horario);
        $feedbackMessage = $result['message'] ?? 'Ação desconhecida.';
        $feedbackType = ($result['success'] ?? false) ? 'success' : 'error';
        $queryParams = $_GET;
        $queryParams['msg'] = urlencode($feedbackMessage);
        $queryParams['type'] = $feedbackType;
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?' . http_build_query($queryParams);
        header('Location: ' . $redirectUrl);
        exit();
    }
}
