<?php
// Espera receber $eventosDoBanco, $turmas, $caminhoJS e $feedbackMessage já preparados pelo controller
?>
<?php if ($feedbackMessage): ?>
    <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg relative mb-4" role="alert">
        <span class="block sm:inline"><?= $feedbackMessage ?></span>
    </div>
<?php endif; ?>
<?php
CalendarioWidget::render([
    'id_container' => 'calendar-container',
    'id_title' => 'calendar-title',
    'id_prev_btn' => 'prev-month-btn',
    'id_next_btn' => 'next-month-btn',
    'id_grid' => 'calendar-grid',
    'id_list' => 'calendar-list',
    // O JS específico para o admin será carregado separadamente
    'is_admin' => true,
    'add_event_btn_id' => 'add-event-btn',
]);
?>

<!-- Modal para Adicionar/Editar Evento -->
<div id="add-event-modal"
    class="modal-backdrop fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center overflow-y-auto p-4 z-30 hidden opacity-0 pointer-events-none transition-opacity duration-300">
    <div
        class="modal-panel bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 transform -translate-y-10 transition-all duration-300">
        <div class="flex justify-between items-center mb-6">
            <h3 id="event-modal-title" class="text-2xl font-semibold">Adicionar Evento</h3>
            <button class="close-modal-btn p-2 rounded-full hover:bg-slate-100">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form id="event-form" class="space-y-4" method="POST">
            <input type="hidden" name="action" value="salvar_evento">
            <input type="hidden" id="event-id" name="event-id">
            <div>
                <label for="event-title" class="block text-sm font-medium text-slate-700 mb-1">Título do Evento</label>
                <input type="text" id="event-title" name="event-title" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 text-sm">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="event-type" class="block text-sm font-medium text-slate-700 mb-1">Tipo de
                        Evento</label>
                    <select id="event-type" name="event-type"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 text-sm">
                        <option value="evento">Evento Escolar</option>
                        <option value="feriado">Feriado</option>
                        <option value="ferias">Férias</option>
                        <option value="prova">Prova/Avaliação</option>
                        <option value="reuniao">Reunião</option>
                    </select>
                </div>
                <div>
                    <label for="event-target" class="block text-sm font-medium text-slate-700 mb-1">Público-Alvo</label>
                    <select id="event-target" name="event-target"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 text-sm">
                        <option value="todos">Toda a Escola</option>
                        <option value="professores">Apenas Professores</option>
                        <option value="alunos">Apenas Alunos</option>
                        <?php foreach ($turmas as $turma): ?>
                            <option value="<?= htmlspecialchars($turma->nome) ?>"><?= htmlspecialchars($turma->nome) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="event-date" id="event-date-label"
                        class="block text-sm font-medium text-slate-700 mb-1">Data</label>
                    <input type="date" id="event-date" name="event-date" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 text-sm">
                </div>
                <div id="event-end-date-wrapper" class="hidden">
                    <label for="event-end-date" class="block text-sm font-medium text-slate-700 mb-1">Data de
                        Fim</label>
                    <input type="date" id="event-end-date" name="event-end-date"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 text-sm">
                </div>
            </div>
            <div id="event-recurring-wrapper" class="flex items-center pt-2">
                <input id="event-recurring" name="event-recurring" type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                <label for="event-recurring" class="ml-2 block text-sm text-slate-700">Repetir anualmente</label>
            </div>
        </form>
        <div
            class="flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-3 pt-4 mt-4 border-t border-slate-200">
            <div>
                <?php Botoes::getBotao('', 'Excluir', BotoesCores::VERMELHO, 'delete-event-btn', altura: 40, icone: 'fa-solid fa-trash-can', type: 'button', classes: 'hidden w-full sm:w-auto') ?>
            </div>
            <div class="w-full sm:w-auto">
                <button type="submit" form="event-form" id="save-event-btn"
                    class="btn btn-verde w-full sm:w-auto flex items-center justify-center" style="height: 40px;">
                    <i class="fa-solid fa-floppy-disk me-2"></i><span>Salvar Evento</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div id="confirmation-modal-calendario"
    class="modal-backdrop fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center overflow-y-auto p-4 z-40 hidden opacity-0 pointer-events-none transition-opacity duration-300">
    <div
        class="modal-panel bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform -translate-y-10 transition-all duration-300">
        <form id="delete-event-form" method="POST">
            <input type="hidden" name="action" value="excluir_evento">
            <input type="hidden" id="event-id-delete" name="event-id-delete">
            <h3 id="confirmation-title-calendario" class="text-xl font-semibold mb-4">Confirmar Ação</h3>
            <p id="confirmation-message-calendario" class="text-slate-600 mb-6">Tem certeza que deseja prosseguir?</p>
            <div class="flex justify-end gap-3">
                <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn') ?>
                <?php Botoes::getBotao('', 'Excluir', BotoesCores::VERMELHO, 'confirm-action-btn-calendario', altura: 40, icone: 'fa-solid fa-trash-can', type: 'submit') ?>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Ver Eventos do Dia -->
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
            <!-- Eventos serão inseridos aqui -->
        </div>
    </div>
</div>

<?php
FuncoesUtils::adicionarJs($caminhoJS);
?>
<script>
    // Injeta os dados dos eventos para o JavaScript da página
    window.adminCalendarData = {
        dbEvents: <?= json_encode($eventosDoBanco) ?>
    };
</script>