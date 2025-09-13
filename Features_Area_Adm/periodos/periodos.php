<?php
require_once BASE_PATH . 'core/services/PeriodoService.php';
require_once BASE_PATH . 'widgets/modal/Modal.php';
require_once BASE_PATH . 'core/models/PeriodoDTO.php';

FuncoesUtils::adicionarJs('Widgets/modal/modal.js');

$periodoService = new PeriodoService();
$feedbackMessage = '';
$feedbackType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $action = $_POST['action'] ?? '';

    if ($action === 'salvar_periodo')
    {
        $periodoDTO = new PeriodoDTO(
            empty($_POST['periodo-id']) ? null : (int)$_POST['periodo-id'],
            $_POST['periodo-name'] ?? ''
        );

        if ($periodoService->salvar($periodoDTO))
        {
            $feedbackMessage = "Período salvo com sucesso!";
        }
        else
        {
            $feedbackMessage = "Erro ao salvar período.";
            $feedbackType = 'error';
        }
    }

    if ($action === 'excluir_periodo')
    {
        $id = (int)$_POST['id'];
        $errorMessage = '';
        if ($periodoService->excluir($id, $errorMessage))
        {
            $feedbackMessage = "Período excluído com sucesso!";
        }
        else
        {
            $feedbackMessage = $errorMessage ?: "Erro ao excluir período.";
            $feedbackType = 'error';
        }
    }

    $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=periodos&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
    header("Location: " . $redirectUrl);
    exit();
}

$periodos = $periodoService->buscarTodos();

$feedbackMessage = isset($_GET['msg']) ? htmlspecialchars(urldecode($_GET['msg'])) : '';
$feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
?>

<div class="space-y-6">
    <?php if ($feedbackMessage): ?>
        <div class="<?= $feedbackType === 'success' ? 'bg-green-100 border-green-200 text-green-800' : 'bg-red-100 border-red-200 text-red-800' ?> border px-4 py-3 rounded-lg relative"
            role="alert">
            <span class="block sm:inline"><?= $feedbackMessage ?></span>
        </div>
    <?php endif; ?>

    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <h2 class="text-xl font-semibold">Gerenciar Períodos</h2>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-grow">
                <input id="periodo-search-input" type="text" placeholder="Pesquisar por nome..."
                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-full focus:ring-2 focus:ring-purple-400 w-full text-sm">
                <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            </div>
            <?php Botoes::getBotao('', 'Adicionar Período', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-plus-circle', type: 'button', classes: 'add-periodo-btn hidden sm:inline-flex') ?>
        </div>
    </div>

    <?php
    Botoes::getBotoesFlutuantes([
        [
            'cor' => BotoesCores::VERDE,
            'icone' => 'fa-solid fa-plus text-xl',
            'type' => 'button',
            'classesAdicionais' => 'add-periodo-btn'
        ]
    ]);
    ?>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-4 font-semibold text-sm">Nome</th>
                    <th class="p-4 font-semibold text-sm text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200" id="periodos-table-body">
                <?php foreach ($periodos as $periodo): ?>
                    <tr data-periodo-id="<?= $periodo->id ?>" data-periodo-name="<?= htmlspecialchars($periodo->nome) ?>">
                        <td class="p-4 font-medium"><?= htmlspecialchars($periodo->nome) ?></td>
                        <td class="p-4 flex items-center justify-end gap-2">
                            <button
                                class="edit-periodo-btn text-slate-500 hover:text-blue-600 p-2 rounded-lg hover:bg-slate-100"
                                title="Editar"><i class="fa-solid fa-pencil"></i></button>
                            <button
                                class="delete-periodo-btn text-slate-500 hover:text-red-600 p-2 rounded-lg hover:bg-slate-100"
                                title="Excluir"><i class="fa-solid fa-trash-can"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Adicionar/Editar Período -->
<?php Modal::begin('add-edit-periodo-modal', 'Adicionar Novo Período', 'periodo-modal-title', 'max-w-md', 'z-30'); ?>
<form id="periodo-form" method="POST">
    <input type="hidden" name="action" value="salvar_periodo">
    <input type="hidden" id="periodo-id" name="periodo-id">
    <div class="space-y-4">
        <div>
            <label for="periodo-name" class="block text-sm font-medium text-slate-700 mb-1">Nome do Período</label>
            <input type="text" id="periodo-name" name="periodo-name" required
                class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm">
        </div>
    </div>
    <div class="flex justify-end gap-3 pt-6">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn') ?>
        <?php Botoes::getBotao('', 'Salvar', BotoesCores::VERDE, 'save-periodo-btn', altura: 40, icone: 'fa-solid fa-floppy-disk', type: 'submit') ?>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal de Confirmação para Exclusão -->
<?php Modal::begin('confirmation-modal', null, '', 'max-w-md', 'z-30'); ?>
<form id="confirmation-form" method="POST">
    <input type="hidden" name="action" value="excluir_periodo">
    <input type="hidden" id="confirmation-id" name="id">
    <h3 id="confirmation-title" class="text-xl font-semibold mb-4">Confirmar Exclusão</h3>
    <p id="confirmation-message" class="text-slate-600 mb-6">Tem certeza que deseja excluir este período?</p>
    <div class="flex justify-end gap-3">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn') ?>
        <?php Botoes::getBotao('', 'Confirmar', BotoesCores::VERMELHO, 'confirm-delete-btn', altura: 40, icone: 'fa-solid fa-check', type: 'submit') ?>
    </div>
</form>
<?php Modal::end(); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addEditModal = document.getElementById('add-edit-periodo-modal');
        const periodoModalTitle = document.getElementById('periodo-modal-title');
        const periodoForm = document.getElementById('periodo-form');
        const periodoIdInput = document.getElementById('periodo-id');
        const periodoNameInput = document.getElementById('periodo-name');

        document.querySelectorAll('.add-periodo-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                periodoModalTitle.textContent = 'Adicionar Novo Período';
                periodoForm.reset();
                periodoIdInput.value = '';
                window.ModalManager.open(addEditModal);
            });
        });

        const confirmationModal = document.getElementById('confirmation-modal');
        const confirmationMessage = document.getElementById('confirmation-message');
        const confirmationIdInput = document.getElementById('confirmation-id');

        document.getElementById('periodos-table-body').addEventListener('click', e => {
            const row = e.target.closest('tr');
            if (!row) return;

            if (e.target.closest('.edit-periodo-btn')) {
                periodoModalTitle.textContent = 'Editar Período';
                periodoIdInput.value = row.dataset.periodoId;
                periodoNameInput.value = row.dataset.periodoName;
                window.ModalManager.open(addEditModal);
            }

            if (e.target.closest('.delete-periodo-btn')) {
                confirmationMessage.innerHTML =
                    `Tem certeza que deseja excluir o período <strong>${row.dataset.periodoName}</strong>? Esta ação não pode ser desfeita.`;
                confirmationIdInput.value = row.dataset.periodoId;
                window.ModalManager.open(confirmationModal);
            }
        });

        document.getElementById('periodo-search-input').addEventListener('input', e => {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('#periodos-table-body tr').forEach(row => {
                const name = row.dataset.periodoName.toLowerCase();
                row.style.display = name.includes(searchTerm) ? '' : 'none';
            });
        });
    });
</script>