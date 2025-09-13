<?php
require_once BASE_PATH . 'core/services/EventoCalendarioService.php';
require_once BASE_PATH . 'Widgets/calendario/calendario.php';

$eventoService = new EventoCalendarioService();
$turmaAluno = $_SESSION['user_turma'] ?? '';
$eventosDoBanco = $eventoService->buscarParaAluno($turmaAluno);
?>

<script>
    // Injeta os dados dos eventos do calendário para o JavaScript
    if (typeof studentData === 'undefined') {
        var studentData = {};
    }
    studentData.dbEvents = <?= json_encode($eventosDoBanco) ?>;
</script>
<?php
CalendarioWidget::render([
    'id_container' => 'calendar-container-aluno',
    'id_title' => 'calendar-title',
    'id_prev_btn' => 'prev-month',
    'id_next_btn' => 'next-month',
    'id_grid' => 'calendar-grid',
    'is_admin' => false,
]);
?>

<!-- Modal para Ver Eventos do Dia (Aluno) -->
<div id="day-events-modal-aluno"
    class="modal-backdrop fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 z-50 hidden opacity-0 pointer-events-none transition-opacity duration-300">
    <div
        class="modal-panel bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 transform -translate-y-10 transition-all duration-300">
        <div class="flex justify-between items-center mb-4">
            <h3 id="day-events-modal-title-aluno" class="text-xl font-semibold">Eventos do Dia</h3>
            <button class="close-modal-btn-aluno p-2 rounded-full hover:bg-slate-100">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div id="day-events-modal-content-aluno" class="space-y-3 max-h-96 overflow-y-auto">
            <!-- Eventos do dia serão inseridos aqui pelo JS -->
        </div>
    </div>
</div>