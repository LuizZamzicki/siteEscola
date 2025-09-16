<?php
// A view espera receber $eventosDoBanco já preparado pelo controller.
require_once BASE_PATH . 'Widgets/calendario/calendario.php';
?>

<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-6">
    <h2 class="text-2xl font-semibold">Calendário Acadêmico</h2>
    <p class="text-slate-600 mt-1">Confira os próximos eventos, feriados e avaliações.</p>
</div>

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
    'id_title' => 'calendar-title', // ID para o título do mês (ex: "Setembro 2024")
    'id_prev_btn' => 'prev-month-btn', // ID para o botão de mês anterior
    'id_next_btn' => 'next-month-btn', // ID para o botão de mês seguinte
    'id_grid' => 'calendar-grid', // ID para a grade do calendário
    'id_list' => 'calendar-list', // ID para a lista de eventos do mês
    'is_admin' => false,
]);
?>

<!-- Modal para Ver Eventos do Dia (Aluno) -->
<div id="day-events-modal"
    class="modal-backdrop fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center overflow-y-auto p-4 z-30 hidden opacity-0 pointer-events-none transition-opacity duration-300">
    <div
        class="modal-panel bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 transform -translate-y-10 transition-all duration-300">
        <div class="flex justify-between items-center mb-4">
            <h3 id="day-events-modal-title" class="text-xl font-semibold">Eventos do Dia</h3>
            <button class="close-modal-btn p-2 rounded-full hover:bg-slate-100">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div id="day-events-modal-content" class="space-y-3 max-h-96 overflow-y-auto">
            <!-- Eventos do dia serão inseridos aqui pelo JS -->
        </div>
    </div>
</div>