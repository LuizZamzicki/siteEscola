<?php
require_once BASE_PATH . 'core/services/HorarioService.php';
require_once BASE_PATH . 'core/services/database.php';

$fullSchedule = [];
$turmaId = $_SESSION['user_turma_id'] ?? null;

if ($turmaId)
{
    $horarioService = new HorarioService();
    $db = new Database();

    // Busca o ID do período da turma do aluno
    $turmaInfo = $db->query("SELECT periodo FROM turmas WHERE id_turma = :id_turma", [':id_turma' => $turmaId]);
    $periodoId = null;
    if (!empty($turmaInfo))
    {
        $periodoNome = $turmaInfo[0]['periodo'];
        $periodoInfo = $db->query("SELECT id FROM periodos WHERE nome = :nome", [':nome' => $periodoNome]);
        if (!empty($periodoInfo))
        {
            $periodoId = $periodoInfo[0]['id'];
        }
    }

    // Busca a configuração de horários e a grade da turma
    $timeSlotsConfig = $horarioService->buscarTodosHorariosConfig($periodoId);
    $rawSchedule = $horarioService->buscarHorarioCompletoPorTurma($turmaId);

    $diasSemanaPhp = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira'];
    $diasSemanaJs = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];

    foreach ($timeSlotsConfig as $slot)
    {
        $horarioStr = date('H:i', strtotime($slot['horario_inicio'])) . ' - ' . date('H:i', strtotime($slot['horario_fim']));
        $row = ['time' => $horarioStr];

        if ($slot['tipo'] === 'intervalo')
        {
            $row['type'] = $slot['label'] ?: 'Intervalo';
        }
        else
        { // tipo 'aula'
            foreach ($diasSemanaPhp as $index => $diaPhp)
            {
                $diaJs = $diasSemanaJs[$index];
                $aula = $rawSchedule[$diaPhp][$horarioStr] ?? null;
                $row[$diaJs] = $aula ? $aula['materia'] : null;
            }
        }
        $fullSchedule[] = $row;
    }
}

$turmaNome = $_SESSION['user_turma'] ?? 'Sua Turma';
?>

<script>
    // Garante que o objeto global studentData exista e injeta os dados do horário.
    var studentData = window.studentData || {};
    studentData.schedule = studentData.schedule || {};
    studentData.schedule.full = <?= json_encode($fullSchedule, JSON_UNESCAPED_UNICODE) ?>;
</script>

<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
    <h2 class="text-2xl font-semibold">Horário de Aulas -
        <?= htmlspecialchars($turmaNome) ?>
    </h2>
    <p class="text-slate-600 mt-1">Confira sua grade horária completa da semana.</p>
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-center border-collapse">
            <thead class="border-b-2 border-slate-200">
                <tr>
                    <th class="p-3 text-sm font-semibold tracking-wide w-32">Horário</th>
                    <th class="p-3 text-sm font-semibold tracking-wide">Segunda</th>
                    <th class="p-3 text-sm font-semibold tracking-wide">Terça</th>
                    <th class="p-3 text-sm font-semibold tracking-wide">Quarta</th>
                    <th class="p-3 text-sm font-semibold tracking-wide">Quinta</th>
                    <th class="p-3 text-sm font-semibold tracking-wide">Sexta</th>
                </tr>
            </thead>
            <tbody id="schedule-table-body">
                <!-- O horário será preenchido pelo JavaScript -->
            </tbody>
        </table>
    </div>
</div>