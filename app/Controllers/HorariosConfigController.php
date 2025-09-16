<?php
namespace App\Controllers;

use App\Models\HorarioService;
use App\Models\PeriodoService;
use App\Models\DTO\HorarioConfigDTO;
use Core\View;

class HorariosConfigController
{
    public function index()
    {
        $horarioService = new HorarioService();
        $periodoService = new PeriodoService();
        $selectedPeriodoId = !empty($_GET['periodo_id']) ? (int)$_GET['periodo_id'] : null;
        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars(urldecode($_GET['msg'])) : '';
        $feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
        View::render('Adm/horarios_config', [
            'selectedPeriodoId' => $selectedPeriodoId,
            'feedbackMessage' => $feedbackMessage,
            'feedbackType' => $feedbackType
        ]);
    }

    public function salvar()
    {
        $horarioService = new HorarioService();
        $horarioConfig = new HorarioConfigDTO(
            id: empty($_POST['horario-id']) ? null : (int)$_POST['horario-id'],
            id_periodo: (int)$_POST['periodo_id'],
            tipo: $_POST['tipo_horario'],
            horario_inicio: $_POST['horario_inicio'],
            horario_fim: $_POST['horario_fim'],
            label: $_POST['tipo_horario'] === 'intervalo' ? ($_POST['label'] ?? null) : null
        );
        $result = $horarioService->salvarHorarioConfig($horarioConfig);
        $feedbackMessage = urlencode($result['message']);
        $feedbackType = $result['success'] ? 'success' : 'error';
        $redirectParams = ['param' => 'horarios_config', 'msg' => $feedbackMessage, 'type' => $feedbackType];
        if (!empty($_POST['periodo_id']))
        {
            $redirectParams['periodo_id'] = $_POST['periodo_id'];
        }
        header('Location: ?' . http_build_query($redirectParams));
        exit();
    }

    public function excluir()
    {
        $horarioService = new HorarioService();
        $id = (int)($_POST['horario-id-delete'] ?? 0);
        $result = $horarioService->excluirHorarioConfig($id);
        $feedbackMessage = urlencode($result['message']);
        $feedbackType = $result['success'] ? 'success' : 'error';
        $redirectParams = ['param' => 'horarios_config', 'msg' => $feedbackMessage, 'type' => $feedbackType];
        if (!empty($_POST['periodo_id']))
        {
            $redirectParams['periodo_id'] = $_POST['periodo_id'];
        }
        header('Location: ?' . http_build_query($redirectParams));
        exit();
    }
}
