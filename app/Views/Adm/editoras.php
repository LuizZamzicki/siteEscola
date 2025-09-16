<?php
require_once BASE_PATH . 'widgets/modal/Modal.php';
use App\Models\DTO\EditoraDTO;
use App\Models\EditoraService;


FuncoesUtils::adicionarJs('Widgets/modal/modal.js');

$editoraService = new EditoraService();
$feedbackMessage = '';
$feedbackType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $action = $_POST['action'] ?? '';

    if ($action === 'salvar_editora')
    {
        $editoraDTO = new EditoraDTO(
            empty($_POST['publisher-id']) ? null : (int)$_POST['publisher-id'],
            $_POST['publisher-name'] ?? ''
        );

        if ($editoraService->salvarEditora($editoraDTO))
        {
            $feedbackMessage = "Editora salva com sucesso!";
        }
        else
        {
            $feedbackMessage = "Erro ao salvar editora.";
            $feedbackType = 'error';
        }
    }

    if ($action === 'excluir_editora')
    {
        $id = (int)$_POST['id'];
        $errorMessage = '';
        if ($editoraService->excluirEditora($id, $errorMessage))
        {
            $feedbackMessage = "Editora excluída com sucesso!";
        }
        else
        {
            $feedbackMessage = $errorMessage ?: "Erro ao excluir editora.";
            $feedbackType = 'error';
        }
    }

    $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=editoras_biblioteca&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
    header("Location: " . $redirectUrl);
    exit();
}

$editoras = $editoraService->buscarTodos();

$feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
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
        <h2 class="text-xl font-semibold">Gerenciar Editoras</h2>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-grow">
                <input id="publisher-search-input" type="text" placeholder="Pesquisar por nome..."
                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-full focus:ring-2 focus:ring-purple-400 w-full text-sm">
                <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            </div>
            <?php // Botão para Desktop ?>
            <?php Botoes::getBotao('', 'Adicionar Editora', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-plus-circle', type: 'button', classes: 'add-publisher-btn hidden sm:inline-flex') ?>
        </div>
    </div>

    <?php // Botão Flutuante para Mobile
    Botoes::getBotoesFlutuantes([
        [
            'cor' => BotoesCores::VERDE,
            'icone' => 'fa-solid fa-plus text-xl',
            'type' => 'button',
            'classesAdicionais' => 'add-publisher-btn'
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
            <tbody class="divide-y divide-slate-200" id="publishers-table-body">
                <?php foreach ($editoras as $editora): ?>
                    <tr data-publisher-id="<?= $editora->id ?>"
                        data-publisher-name="<?= htmlspecialchars($editora->nome) ?>">
                        <td class="p-4 font-medium"><?= htmlspecialchars($editora->nome) ?></td>
                        <td class="p-4 flex items-center justify-end gap-2">
                            <button
                                class="edit-publisher-btn text-slate-500 hover:text-blue-600 p-2 rounded-lg hover:bg-slate-100"
                                title="Editar"><i class="fa-solid fa-pencil"></i></button>
                            <button
                                class="delete-publisher-btn text-slate-500 hover:text-red-600 p-2 rounded-lg hover:bg-slate-100"
                                title="Excluir"><i class="fa-solid fa-trash-can"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Adicionar/Editar Editora -->
<?php Modal::begin('add-edit-publisher-modal', 'Adicionar Nova Editora', 'publisher-modal-title', 'max-w-md', 'z-40'); ?>
<form id="publisher-form" method="POST">
    <input type="hidden" name="action" value="salvar_editora">
    <input type="hidden" id="publisher-id" name="publisher-id">
    <div class="space-y-4">
        <div>
            <label for="publisher-name" class="block text-sm font-medium text-slate-700 mb-1">Nome da
                Editora</label>
            <input type="text" id="publisher-name" name="publisher-name" required
                class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm">
        </div>
    </div>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-6">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Salvar', BotoesCores::VERDE, 'save-publisher-btn', altura: 40, icone: 'fa-solid fa-floppy-disk', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal de Confirmação para Exclusão -->
<?php Modal::begin('confirmation-modal', null, '', 'max-w-md', 'z-40'); ?>
<form id="confirmation-form" method="POST">
    <input type="hidden" name="action" value="excluir_editora">
    <input type="hidden" id="confirmation-id" name="id">
    <h3 id="confirmation-title" class="text-xl font-semibold mb-4">Confirmar Exclusão</h3>
    <p id="confirmation-message" class="text-slate-600 mb-6">Tem certeza que deseja excluir esta editora?</p>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Confirmar', BotoesCores::VERMELHO, 'confirm-delete-btn', altura: 40, icone: 'fa-solid fa-check', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addEditModal = document.getElementById('add-edit-publisher-modal');
        const publisherModalTitle = document.getElementById('publisher-modal-title');
        const publisherForm = document.getElementById('publisher-form');
        const publisherIdInput = document.getElementById('publisher-id');
        const publisherNameInput = document.getElementById('publisher-name');

        document.querySelectorAll('.add-publisher-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                publisherModalTitle.textContent = 'Adicionar Nova Editora';
                publisherForm.reset();
                publisherIdInput.value = '';
                window.ModalManager.open(addEditModal);
            });
        });

        const confirmationModal = document.getElementById('confirmation-modal');
        const confirmationMessage = document.getElementById('confirmation-message');
        const confirmationIdInput = document.getElementById('confirmation-id');

        document.getElementById('publishers-table-body').addEventListener('click', e => {
            const row = e.target.closest('tr');
            if (!row) return;

            if (e.target.closest('.edit-publisher-btn')) {
                publisherModalTitle.textContent = 'Editar Editora';
                publisherIdInput.value = row.dataset.publisherId;
                publisherNameInput.value = row.dataset.publisherName;
                window.ModalManager.open(addEditModal);
            }

            if (e.target.closest('.delete-publisher-btn')) {
                confirmationMessage.innerHTML =
                    `Tem certeza que deseja excluir a editora <strong>${row.dataset.publisherName}</strong>? Esta ação não pode ser desfeita.`;
                confirmationIdInput.value = row.dataset.publisherId;
                window.ModalManager.open(confirmationModal);
            }
        });

        document.getElementById('publisher-search-input').addEventListener('input', e => {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('#publishers-table-body tr').forEach(row => {
                const name = row.dataset.publisherName.toLowerCase();
                row.style.display = name.includes(searchTerm) ? '' : 'none';
            });
        });
    });
</script>