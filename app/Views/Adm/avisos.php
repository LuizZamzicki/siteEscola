<?php
// Espera receber $avisos, $turmas, $feedbackMessage e $feedbackType já preparados pelo controller
?>
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <h2 class="text-xl font-semibold p-2 text-center">Gerenciamento de Avisos e Anúncios</h2>
    <?php // Botão para Desktop ?>
    <?php Botoes::getBotao('', 'Criar Aviso', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-plus-circle', type: 'button', classes: 'add-notice-btn hidden sm:inline-flex') ?>
</div>

<?php // Botão Flutuante para Mobile
Botoes::getBotoesFlutuantes([
    [
        'cor' => BotoesCores::VERDE,
        'icone' => 'fa-solid fa-plus text-xl',
        'type' => 'button',
        'classesAdicionais' => 'add-notice-btn'
    ]
]);
?>

<?php if ($feedbackMessage): ?>
    <div class="<?= $feedbackType === 'success' ? 'bg-green-100 border-green-200 text-green-800' : 'bg-red-100 border-red-200 text-red-800' ?> border px-4 py-3 rounded-lg relative mb-4"
        role="alert">
        <span class="block sm:inline"><?= $feedbackMessage ?></span>
    </div>
<?php endif; ?>
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    <ul id="notice-list" class="space-y-4">
        <?php if (empty($avisos)): ?>
            <li class="text-center text-slate-500 py-4">Nenhum aviso encontrado.</li>
        <?php else: ?>
            <?php foreach ($avisos as $aviso): ?>
                <li class="p-4 border border-slate-200 rounded-lg flex justify-between items-start"
                    data-notice-id="<?= $aviso->id ?>" data-title="<?= htmlspecialchars($aviso->titulo) ?>"
                    data-content="<?= htmlspecialchars($aviso->conteudo) ?>"
                    data-target="<?= htmlspecialchars($aviso->publico_alvo) ?>">
                    <div>
                        <h3 class="font-semibold text-lg"><?= htmlspecialchars($aviso->titulo) ?></h3>
                        <p class="text-slate-600 mt-1"><?= htmlspecialchars(mb_strimwidth($aviso->conteudo, 0, 100, "...")) ?>
                        </p>
                        <p class="text-xs text-slate-400 mt-2">Postado em: <?= $aviso->getFormattedDate() ?></p>
                    </div>
                    <div class="flex-shrink-0 ml-4">
                        <button class="edit-notice-btn text-slate-500 hover:text-blue-600 p-1" title="Editar"><i
                                class="fa-solid fa-pencil"></i></button>
                        <button class="delete-notice-btn text-slate-500 hover:text-red-600 p-1" title="Excluir"><i
                                class="fa-solid fa-trash-can"></i></button>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

<!-- Modal para Adicionar/Editar Aviso -->
<?php Modal::begin('add-notice-modal', 'Criar Novo Aviso', 'notice-modal-title', 'max-w-2xl', 'z-40'); ?>
<form id="notice-form" class="space-y-4" method="POST">
    <input type="hidden" name="action" value="salvar_aviso">
    <input type="hidden" id="notice-id" name="notice-id">
    <div>
        <label for="notice-title" class="block text-sm font-medium text-slate-700 mb-1">Título do Aviso</label>
        <input type="text" id="notice-title" name="notice-title" required
            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 text-sm">
    </div>
    <div>
        <label for="notice-content" class="block text-sm font-medium text-slate-700 mb-1">Conteúdo</label>
        <textarea id="notice-content" name="notice-content" rows="5" required
            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 text-sm"></textarea>
    </div>
    <div>
        <label for="notice-target" class="block text-sm font-medium text-slate-700 mb-1">Público-Alvo</label>
        <select id="notice-target" name="notice-target"
            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 text-sm">
            <option value="todos">Toda a Escola</option>
            <option value="professores">Apenas Professores</option>
            <option value="alunos">Apenas Alunos</option>
            <?php foreach ($turmas as $turma): ?>
                <option value="<?= htmlspecialchars($turma->nome) ?>"><?= htmlspecialchars($turma->nome) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Salvar Aviso', BotoesCores::VERDE, 'save-notice-btn', altura: 40, icone: 'fa-solid fa-floppy-disk', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal de Confirmação -->
<?php Modal::begin('confirmation-modal-avisos', null, '', 'max-w-md', 'z-50'); ?>
<form id="delete-notice-form" method="POST">
    <input type="hidden" name="action" value="excluir_aviso">
    <input type="hidden" id="notice-id-delete" name="notice-id-delete">
    <h3 id="confirmation-title-avisos" class="text-xl font-semibold mb-4">Confirmar Ação</h3>
    <p id="confirmation-message-avisos" class="text-slate-600 mb-6">Tem certeza que deseja prosseguir?</p>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Excluir', BotoesCores::VERMELHO, 'confirm-action-btn-avisos', altura: 40, icone: 'fa-solid fa-trash-can', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>