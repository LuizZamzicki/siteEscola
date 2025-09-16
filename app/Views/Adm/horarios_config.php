<?php
require_once BASE_PATH . 'widgets/modal/Modal.php';
FuncoesUtils::adicionarJs('Widgets/modal/modal.js');
{
    $feedbackMessage = htmlspecialchars(urldecode($_GET['msg']));
    $feedbackType = $_GET['type'] ?? 'success';
}

$periodos = $periodoService->buscarTodos();
$horarios = $horarioService->buscarTodosHorariosConfig($selectedPeriodoId);
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-xl font-semibold">Configuração de Horários</h2>
        <div class="flex items-center gap-4">
            <form method="GET" class="flex items-center gap-2">
                <input type="hidden" name="param" value="horarios_config">
                <label for="periodo-select" class="text-sm font-medium sr-only">Selecione o Período:</label>
                <select id="periodo-select" name="periodo_id" onchange="this.form.submit()"
                    class="border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-400">
                    <option value="">Selecione um Período</option>
                    <?php foreach ($periodos as $periodo): ?>
                        <option value="<?= $periodo->id ?>" <?= $selectedPeriodoId == $periodo->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($periodo->nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <?php Botoes::getBotao('', 'Adicionar Horário', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-plus-circle', type: 'button', classes: 'add-horario-btn hidden sm:inline-flex' . (!$selectedPeriodoId ? ' opacity-50 cursor-not-allowed' : '')) ?>
        </div>
    </div>

    <?php
    Botoes::getBotoesFlutuantes([
        [
            'cor' => BotoesCores::VERDE,
            'icone' => 'fa-solid fa-plus text-xl',
            'type' => 'button',
            'classesAdicionais' => 'add-horario-btn' . (!$selectedPeriodoId ? ' opacity-50 cursor-not-allowed' : '')
        ]
    ]);
    ?>

    <?php if ($feedbackMessage): ?>
        <div class="<?= $feedbackType === 'success' ? 'bg-green-100 border-green-200 text-green-800' : 'bg-red-100 border-red-200 text-red-800' ?> border px-4 py-3 rounded-lg relative mb-4"
            role="alert">
            <span class="block sm:inline"><?= $feedbackMessage ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <ul id="horarios-list" class="space-y-2">
            <?php if (!$selectedPeriodoId): ?>
                <li class="text-center text-slate-500 py-8">
                    <i class="fa-solid fa-arrow-pointer fa-2x text-slate-400 mb-3"></i>
                    <p class="font-medium">Selecione um período para começar</p>
                    <p class="text-sm">Use o seletor acima para visualizar ou gerenciar os horários de um período.</p>
                </li>
            <?php elseif (empty($horarios)): ?>
                <li class="text-center text-slate-500 py-8">
                    <i class="fa-solid fa-clock-rotate-left fa-2x text-slate-400 mb-3"></i>
                    <p class="font-medium">Nenhum horário configurado para este período</p>
                    <p class="text-sm">Clique em "Adicionar Horário" para criar o primeiro.</p>
                </li>
            <?php else: ?>
                <?php foreach ($horarios as $horario): ?>
                    <li class="horario-item flex items-center justify-between p-3 border border-slate-200 rounded-lg bg-white"
                        data-id="<?= $horario['id'] ?>">
                        <div class="flex items-center gap-4">
                            <div>
                                <p class="font-semibold">
                                    <?= htmlspecialchars(date('H:i', strtotime($horario['horario_inicio']))) ?> às
                                    <?= htmlspecialchars(date('H:i', strtotime($horario['horario_fim']))) ?>
                                </p>
                                <p class="text-sm text-slate-500">
                                    <?php if ($horario['tipo'] === 'intervalo'): ?>
                                        <i class="fa-solid fa-mug-saucer mr-1 text-purple-500"></i>
                                        <?= htmlspecialchars($horario['label']) ?>
                                    <?php else: ?>
                                        <i class="fa-solid fa-book-open-reader mr-1 text-green-500"></i>
                                        Aula
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                class="edit-horario-btn text-slate-500 hover:text-blue-600 p-2 rounded-lg hover:bg-slate-100"
                                title="Editar" data-id="<?= $horario['id'] ?>"
                                data-label="<?= htmlspecialchars($horario['label'] ?? '') ?>"
                                data-tipo="<?= htmlspecialchars($horario['tipo']) ?>"
                                data-inicio="<?= htmlspecialchars($horario['horario_inicio']) ?>"
                                data-fim="<?= htmlspecialchars($horario['horario_fim']) ?>"
                                data-periodo-id="<?= htmlspecialchars($horario['id_periodo']) ?>">
                                <i class="fa-solid fa-pencil"></i>
                            </button>
                            <button
                                class="delete-horario-btn text-slate-500 hover:text-red-600 p-2 rounded-lg hover:bg-slate-100"
                                title="Excluir" data-id="<?= $horario['id'] ?>"
                                data-label="<?= htmlspecialchars($horario['label'] ? $horario['label'] : 'Aula de ' . date('H:i', strtotime($horario['horario_inicio']))) ?>">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- Modal para Adicionar/Editar Horário -->
<?php Modal::begin('horario-config-modal', 'Adicionar Horário', 'horario-config-modal-title', 'max-w-md'); ?>
<form id="horario-config-form" method="POST" class="space-y-4">
    <input type="hidden" name="action" value="salvar_horario_config">
    <input type="hidden" id="horario-id" name="horario-id">
    <input type="hidden" id="form-periodo-id" name="periodo_id">

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de Horário</label>
        <div class="flex gap-6">
            <div class="flex items-center">
                <input type="radio" id="tipo-aula" name="tipo_horario" value="aula"
                    class="h-4 w-4 text-purple-600 border-slate-300 focus:ring-purple-500" checked>
                <label for="tipo-aula" class="ml-2 block text-sm text-slate-900">
                    <i class="fa-solid fa-book-open-reader mr-1 text-green-500"></i> Aula
                </label>
            </div>
            <div class="flex items-center">
                <input type="radio" id="tipo-intervalo" name="tipo_horario" value="intervalo"
                    class="h-4 w-4 text-purple-600 border-slate-300 focus:ring-purple-500">
                <label for="tipo-intervalo" class="ml-2 block text-sm text-slate-900">
                    <i class="fa-solid fa-mug-saucer mr-1 text-purple-500"></i> Intervalo
                </label>
            </div>
        </div>
    </div>

    <div id="label-container" class="hidden">
        <label for="horario-label" class="block text-sm font-medium text-slate-700 mb-1">Rótulo do
            Intervalo</label>
        <input type="text" id="horario-label" name="label" placeholder="Ex: Intervalo, Almoço"
            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="horario-inicio" class="block text-sm font-medium text-slate-700 mb-1">Início</label>
            <input type="time" id="horario-inicio" name="horario_inicio" required
                class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm">
        </div>
        <div>
            <label for="horario-fim" class="block text-sm font-medium text-slate-700 mb-1">Fim</label>
            <input type="time" id="horario-fim" name="horario_fim" required
                class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm">
        </div>
    </div>

    <div id="horario-config-error"
        class="hidden bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4"></div>

    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'close-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Salvar', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-floppy-disk', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal de Confirmação para Excluir -->
<?php Modal::begin('delete-horario-modal', null, '', 'max-w-md', 'z-50'); ?>
<form id="delete-horario-form" method="POST">
    <input type="hidden" name="action" value="excluir_horario_config">
    <input type="hidden" id="delete-horario-id" name="horario-id-delete">
    <input type="hidden" name="periodo_id" value="<?= $selectedPeriodoId ?>">
    <h3 class="text-xl font-semibold mb-4">Excluir Horário</h3>
    <p id="delete-horario-message" class="text-slate-600 mb-6">Tem certeza?</p>
    <div class="flex justify-end gap-3 pt-4">
        <button type="button" class="close-modal-btn btn btn-cinza">Cancelar</button>
        <button type="submit" class="btn btn-vermelho">Excluir</button>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal de Alerta para Seleção de Período -->
<?php Modal::begin('alert-periodo-modal', null, '', 'max-w-sm', 'z-50'); ?>
<div class="text-center">
    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-100">
        <i class="fa-solid fa-triangle-exclamation text-2xl text-amber-500"></i>
    </div>
    <h3 class="text-lg leading-6 font-medium text-slate-900 mt-5">Atenção</h3>
    <div class="mt-2 py-3">
        <p class="text-sm text-slate-500">
            Por favor, selecione um período antes de adicionar um horário.
        </p>
    </div>
    <div class="mt-4">
        <button type="button" class="close-modal-btn btn btn-roxo w-full">Entendi</button>
    </div>
</div>
<?php Modal::end(); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const configModal = document.getElementById('horario-config-modal');
        const deleteModal = document.getElementById('delete-horario-modal');
        const alertPeriodoModal = document.getElementById('alert-periodo-modal');
        const tipoAulaRadio = document.getElementById('tipo-aula');
        const tipoIntervaloRadio = document.getElementById('tipo-intervalo');
        const labelContainer = document.getElementById('label-container');
        const labelInput = document.getElementById('horario-label');
        const configForm = document.getElementById('horario-config-form');
        const errorContainer = document.getElementById('horario-config-error');
        const periodoSelect = document.getElementById('periodo-select');

        document.querySelectorAll('.add-horario-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (!periodoSelect.value) {
                    window.ModalManager.open(alertPeriodoModal);
                    return;
                }

                configModal.querySelector('#horario-config-modal-title').textContent = 'Adicionar Horário';
                configModal.querySelector('form').reset();
                configModal.querySelector('#horario-id').value = '';
                configModal.querySelector('#form-periodo-id').value = periodoSelect.value; // Seta o período selecionado no form
                tipoAulaRadio.checked = true;
                toggleLabelInput();
                errorContainer.classList.add('hidden');
                errorContainer.textContent = '';
                window.ModalManager.open(configModal);
                setTimeout(() => {
                    const firstFocusable = configModal.querySelector('input:not([type="hidden"]), select, textarea, button');
                    if (firstFocusable) {
                        firstFocusable.focus();
                    }
                }, 100); // Delay para garantir que o modal esteja visível
            });
        });

        const toggleLabelInput = () => {
            if (tipoIntervaloRadio.checked) {
                labelContainer.classList.remove('hidden');
                labelInput.required = true;
            } else {
                labelContainer.classList.add('hidden');
                labelInput.required = false;
                labelInput.value = ''; // Limpa o valor para não ser enviado
            }
        };

        tipoAulaRadio.addEventListener('change', toggleLabelInput);
        tipoIntervaloRadio.addEventListener('change', toggleLabelInput);

        document.querySelectorAll('.edit-horario-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const button = e.currentTarget;
                const label = button.dataset.label;
                const tipo = button.dataset.tipo;

                configModal.querySelector('#horario-config-modal-title').textContent = 'Editar Horário';
                configModal.querySelector('#horario-id').value = button.dataset.id;
                configModal.querySelector('#horario-inicio').value = button.dataset.inicio;
                configModal.querySelector('#horario-fim').value = button.dataset.fim;
                configModal.querySelector('#form-periodo-id').value = button.dataset.periodoId; // Seta o período do item
                labelInput.value = label;

                tipoIntervaloRadio.checked = (tipo === 'intervalo');
                tipoAulaRadio.checked = (tipo !== 'intervalo');
                toggleLabelInput();
                errorContainer.classList.add('hidden');
                errorContainer.textContent = '';

                window.ModalManager.open(configModal);
                setTimeout(() => {
                    const firstFocusable = configModal.querySelector('input:not([type="hidden"]), select, textarea, button');
                    if (firstFocusable) {
                        firstFocusable.focus();
                    }
                }, 100); // Delay para garantir que o modal esteja visível
            });
        });

        document.querySelectorAll('.delete-horario-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const button = e.currentTarget;
                deleteModal.querySelector('#delete-horario-id').value = button.dataset.id;
                deleteModal.querySelector('#delete-horario-message').innerHTML = `Tem certeza que deseja excluir o horário <strong>${button.dataset.label}</strong>?`;
                window.ModalManager.open(deleteModal);
                setTimeout(() => {
                    const cancelButton = deleteModal.querySelector('.close-modal-btn');
                    if (cancelButton) {
                        cancelButton.focus();
                    }
                }, 100); // Foca no botão de cancelar por segurança
            });
        });

    });
</script>