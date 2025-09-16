<?php
// Espera receber $planoDeProgresso, $feedbackMessage e $feedbackType já preparados pelo controller
?>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <h2 class="text-2xl font-bold mb-2">Avançar Ano Letivo</h2>
        <p class="text-slate-600 mb-6">Esta ferramenta irá mover os alunos para a série seguinte e marcar os alunos do
            último ano como "Formado". Revise o plano de progressão abaixo antes de continuar. <strong
                class="text-red-600">Esta ação não pode ser desfeita.</strong></p>

        <?php if ($feedbackMessage): ?>
            <div class="<?= $feedbackType === 'success' ? 'bg-green-100 border-green-200 text-green-800' : 'bg-red-100 border-red-200 text-red-800' ?> border px-4 py-3 rounded-lg relative mb-4"
                role="alert">
                <span class="block sm:inline"><?= $feedbackMessage ?></span>
            </div>
        <?php endif; ?>

        <div class="relative">
            <div class="border rounded-lg overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="p-4 font-semibold text-sm">Turma Atual</th>
                            <th class="p-4 font-semibold text-sm">Alunos</th>
                            <th class="p-4 font-semibold text-sm">Ação Proposta</th>
                            <th class="p-4 font-semibold text-sm">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php foreach ($planoDeProgresso as $item): ?>
                            <tr>
                                <td class="p-4 font-medium"><?= htmlspecialchars($item['turma_atual']->nome) ?></td>
                                <td class="p-4 text-slate-600"><?= $item['turma_atual']->totalAlunos ?></td>
                                <td class="p-4 text-slate-600">
                                    <?php if ($item['status'] === 'Formar Alunos'): ?>
                                        Mudar status para "Formado"
                                    <?php else: ?>
                                        Mover para
                                        <strong><?= htmlspecialchars($item['proxima_turma_nome'] ?? 'N/A') ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full <?= $item['status_cor'] ?>"><?= $item['status'] ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div aria-hidden="true"
                class="absolute inset-y-0 right-0 w-16 bg-gradient-to-l from-white to-transparent pointer-events-none sm:hidden">
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row sm:justify-end">
            <?php Botoes::getBotao('', 'Avançar Ano Letivo', BotoesCores::VERMELHO, 'confirm-advance-btn', altura: 44, icone: 'fa-solid fa-angles-right', type: 'button', classes: 'w-full sm:w-auto') ?>
        </div>
    </div>
</div>

<?php Modal::begin('confirmation-modal', null, '', 'max-w-md'); ?>
<form id="advance-form" method="POST">
    <input type="hidden" name="action" value="avancar_ano">
    <h3 class="text-xl font-semibold mb-4">Confirmar Ação Irreversível</h3>
    <p class="text-slate-600 mb-6">Você tem certeza absoluta que deseja avançar o ano letivo? Todos os alunos
        serão movidos conforme o plano. <strong>Esta ação não pode ser desfeita.</strong></p>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA_OUTLINE, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'w-full sm:w-auto close-modal-btn') ?>
        <?php Botoes::getBotao('', 'Sim, tenho certeza', BotoesCores::VERMELHO, null, altura: 40, icone: 'fa-solid fa-check', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const confirmationModal = document.getElementById('confirmation-modal');
        const confirmBtn = document.getElementById('confirm-advance-btn');

        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => {
                window.ModalManager.open(confirmationModal);
            });
        }
    });
</script>