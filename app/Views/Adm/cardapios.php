<?php
require_once BASE_PATH . 'Utils/funcoesUtils.php';
require_once BASE_PATH . 'widgets/modal/Modal.php';
FuncoesUtils::adicionarJs('Widgets/modal/modal.js');
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-xl font-semibold">Gerenciamento de Cardápios</h2>
        <?php Botoes::getBotao('', 'Adicionar Cardápio do Dia', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-plus-circle', type: 'button', classes: 'add-cardapio-btn hidden sm:inline-flex') ?>
    </div>

    <?php
    Botoes::getBotoesFlutuantes([
        [
            'cor' => BotoesCores::VERDE,
            'icone' => 'fa-solid fa-plus text-xl',
            'type' => 'button',
            'classesAdicionais' => 'add-cardapio-btn'
        ]
    ]);
    ?>

    <?php if ($feedbackMessage): ?>
        <div class="<?= $feedbackType === 'success' ? 'bg-green-100 border-green-200 text-green-800' : 'bg-red-100 border-red-200 text-red-800' ?> border px-4 py-3 rounded-lg relative mb-4"
            role="alert">
            <span class="block sm:inline"><?= $feedbackMessage ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-left min-w-[700px]">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-4 font-semibold whitespace-nowrap">Data</th>
                    <th class="p-4 font-semibold whitespace-nowrap">Lanche da Manhã</th>
                    <th class="p-4 font-semibold whitespace-nowrap">Almoço</th>
                    <th class="p-4 font-semibold whitespace-nowrap">Lanche da Tarde</th>
                    <th class="p-4 font-semibold text-right whitespace-nowrap">Ações</th>
                </tr>
            </thead>
            <tbody id="cardapios-table-body" class="divide-y divide-slate-200">
                <?php if (empty($cardapios)): ?>
                    <tr>
                        <td colspan="5" class="p-6 text-center text-slate-500">Nenhum cardápio cadastrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cardapios as $cardapio):
                        $data = new DateTime($cardapio->data);
                        ?>
                        <tr class="cardapio-row" data-id="<?= $cardapio->id ?>" data-date="<?= $cardapio->data ?>"
                            data-lanche1="<?= htmlspecialchars($cardapio->lanche_manha) ?>"
                            data-almoco="<?= htmlspecialchars($cardapio->almoco) ?>"
                            data-lanche2="<?= htmlspecialchars($cardapio->lanche_tarde) ?>">
                            <td class="p-4 font-medium whitespace-nowrap">
                                <?= $data->format('d/m/Y') ?>
                                <span
                                    class="block text-sm text-slate-500"><?= FuncoesUtils::getDiaDaSemana($data->format('w')) ?></span>
                            </td>
                            <td class="p-4 text-slate-600 max-w-xs truncate"
                                title="<?= htmlspecialchars($cardapio->lanche_manha) ?>">
                                <?= htmlspecialchars($cardapio->lanche_manha) ?>
                            </td>
                            <td class="p-4 text-slate-600 max-w-xs truncate" title="<?= htmlspecialchars($cardapio->almoco) ?>">
                                <?= htmlspecialchars($cardapio->almoco) ?>
                            </td>
                            <td class="p-4 text-slate-600 max-w-xs truncate"
                                title="<?= htmlspecialchars($cardapio->lanche_tarde) ?>">
                                <?= htmlspecialchars($cardapio->lanche_tarde) ?>
                            </td>
                            <td class="p-4 text-right whitespace-nowrap">
                                <button
                                    class="edit-cardapio-btn text-slate-500 hover:text-blue-600 p-2 rounded-lg hover:bg-slate-100"
                                    title="Editar Cardápio"><i class="fa-solid fa-pencil"></i></button>
                                <button
                                    class="delete-cardapio-btn text-slate-500 hover:text-red-600 p-2 rounded-lg hover:bg-slate-100"
                                    title="Excluir Cardápio"><i class="fa-solid fa-trash-can"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Adicionar/Editar Cardápio -->
<?php Modal::begin('cardapio-modal', 'Adicionar Cardápio', 'cardapio-modal-title', 'max-w-2xl'); ?>
<form id="cardapio-form" method="POST" class="space-y-4">
    <input type="hidden" name="action" value="salvar_cardapio">
    <input type="hidden" id="cardapio-id" name="cardapio-id">
    <div>
        <label for="cardapio-data" class="block text-sm font-medium text-slate-700 mb-1">Data</label>
        <input type="date" id="cardapio-data" name="data" required
            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
    </div>
    <div>
        <label for="cardapio-lanche-manha" class="block text-sm font-medium text-slate-700 mb-1">Lanche da
            Manhã</label>
        <textarea id="cardapio-lanche-manha" name="lanche_manha" rows="2"
            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"></textarea>
    </div>
    <div>
        <label for="cardapio-almoco" class="block text-sm font-medium text-slate-700 mb-1">Almoço</label>
        <textarea id="cardapio-almoco" name="almoco" rows="3" required
            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"></textarea>
    </div>
    <div>
        <label for="cardapio-lanche-tarde" class="block text-sm font-medium text-slate-700 mb-1">Lanche da
            Tarde</label>
        <textarea id="cardapio-lanche-tarde" name="lanche_tarde" rows="2"
            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"></textarea>
    </div>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Salvar Cardápio', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-floppy-disk', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal de Confirmação para Excluir -->
<?php Modal::begin('delete-cardapio-modal', null, '', 'max-w-md', 'z-50'); ?>
<form id="delete-cardapio-form" method="POST">
    <input type="hidden" name="action" value="excluir_cardapio">
    <input type="hidden" id="delete-cardapio-id" name="cardapio-id">
    <h3 class="text-xl font-semibold mb-4">Excluir Cardápio</h3>
    <p id="delete-cardapio-message" class="text-slate-600 mb-6">Tem certeza que deseja excluir o cardápio deste
        dia?</p>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Excluir', BotoesCores::VERMELHO, null, altura: 40, icone: 'fa-solid fa-trash-can', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cardapioModal = document.getElementById('cardapio-modal');
        const deleteModal = document.getElementById('delete-cardapio-modal');

        document.querySelectorAll('.add-cardapio-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                cardapioModal.querySelector('#cardapio-modal-title').textContent = 'Adicionar Cardápio';
                cardapioModal.querySelector('form').reset();
                cardapioModal.querySelector('#cardapio-id').value = '';
                window.ModalManager.open(cardapioModal);
            });
        });

        document.querySelectorAll('.edit-cardapio-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const row = e.currentTarget.closest('.cardapio-row');
                cardapioModal.querySelector('#cardapio-modal-title').textContent = 'Editar Cardápio';
                cardapioModal.querySelector('#cardapio-id').value = row.dataset.id;
                cardapioModal.querySelector('#cardapio-data').value = row.dataset.date;
                cardapioModal.querySelector('#cardapio-lanche-manha').value = row.dataset.lanche1;
                cardapioModal.querySelector('#cardapio-almoco').value = row.dataset.almoco;
                cardapioModal.querySelector('#cardapio-lanche-tarde').value = row.dataset.lanche2;
                window.ModalManager.open(cardapioModal);
            });
        });

        document.querySelectorAll('.delete-cardapio-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const row = e.currentTarget.closest('.cardapio-row');
                const id = row.dataset.id;
                const date = new Date(row.dataset.date + 'T00:00:00').toLocaleDateString('pt-BR');
                deleteModal.querySelector('#delete-cardapio-id').value = id;
                deleteModal.querySelector('#delete-cardapio-message').innerHTML = `Tem certeza que deseja excluir o cardápio do dia <strong>${date}</strong>?`;
                window.ModalManager.open(deleteModal);
            });
        });
    });
</script>