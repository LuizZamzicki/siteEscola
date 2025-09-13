<?php
require_once BASE_PATH . 'core/services/MateriaService.php';
require_once BASE_PATH . 'widgets/modal/Modal.php';
require_once BASE_PATH . 'core/models/MateriaDTO.php';

FuncoesUtils::adicionarJs('Widgets/modal/modal.js');

$materiaService = new MateriaService();
$feedbackMessage = '';
$feedbackType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $action = $_POST['action'] ?? '';

    if ($action === 'salvar_materia')
    {
        $materiaDTO = new MateriaDTO(
            empty($_POST['subject-id']) ? null : (int)$_POST['subject-id'],
            $_POST['subject-name'] ?? ''
        );
        $result = $materiaService->salvar($materiaDTO);
        $feedbackMessage = $result['message'];
        $feedbackType = $result['success'] ? 'success' : 'error';
    }

    if ($action === 'excluir_materia')
    {
        $id = (int)$_POST['id'];
        $errorMessage = '';
        if ($materiaService->excluir($id, $errorMessage))
        {
            $feedbackMessage = "Matéria excluída com sucesso!";
        }
        else
        {
            $feedbackMessage = $errorMessage ?: "Erro ao excluir matéria.";
            $feedbackType = 'error';
        }
    }

    $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=materias&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
    header("Location: " . $redirectUrl);
    exit();
}

$materias = $materiaService->buscarTodas();

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
        <h2 class="text-xl font-semibold">Gerenciar Matérias</h2>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-grow">
                <input id="subject-search-input" type="text" placeholder="Pesquisar por nome..."
                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-full focus:ring-2 focus:ring-purple-400 w-full text-sm">
                <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            </div>
            <?php Botoes::getBotao('', 'Adicionar Matéria', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-plus-circle', type: 'button', classes: 'add-subject-btn hidden sm:inline-flex') ?>
        </div>
    </div>

    <?php Botoes::getBotoesFlutuantes([
    [
    'cor' => BotoesCores::VERDE,
    'icone' => 'fa-solid fa-plus text-xl',
    'type' => 'button',
    'classesAdicionais' => 'add-subject-btn'
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
            <tbody class="divide-y divide-slate-200" id="subjects-table-body">
                <?php foreach ($materias as $materia): ?>
                    <tr data-subject-id="<?= $materia->id ?>" data-subject-name="<?= htmlspecialchars($materia->nome) ?>">
                        <td class="p-4 font-medium"><?= htmlspecialchars($materia->nome) ?></td>
                        <td class="p-4 flex items-center justify-end gap-2">
                            <button
                                class="edit-subject-btn text-slate-500 hover:text-blue-600 p-2 rounded-lg hover:bg-slate-100"
                                title="Editar"><i class="fa-solid fa-pencil"></i></button>
                            <button
                                class="delete-subject-btn text-slate-500 hover:text-red-600 p-2 rounded-lg hover:bg-slate-100"
                                title="Excluir"><i class="fa-solid fa-trash-can"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Adicionar/Editar Matéria -->
<?php Modal::begin('add-edit-subject-modal', 'Adicionar Nova Matéria', 'subject-modal-title', 'max-w-md', 'z-40'); ?>
<form id="subject-form" method="POST">
    <input type="hidden" name="action" value="salvar_materia">
    <input type="hidden" id="subject-id" name="subject-id">
    <div class="space-y-4">
        <div>
            <label for="subject-name" class="block text-sm font-medium text-slate-700 mb-1">Nome da Matéria</label>
            <input type="text" id="subject-name" name="subject-name" required
                class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm">
        </div>
    </div>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-6">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Salvar', BotoesCores::VERDE, 'save-subject-btn', altura: 40, icone: 'fa-solid fa-floppy-disk', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal de Confirmação para Exclusão -->
<?php Modal::begin('confirmation-modal', null, '', 'max-w-md', 'z-40'); ?>
<form id="confirmation-form" method="POST">
    <input type="hidden" name="action" value="excluir_materia">
    <input type="hidden" id="confirmation-id" name="id">
    <h3 id="confirmation-title" class="text-xl font-semibold mb-4">Confirmar Exclusão</h3>
    <p id="confirmation-message" class="text-slate-600 mb-6">Tem certeza que deseja excluir esta matéria?</p>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Confirmar', BotoesCores::VERMELHO, 'confirm-delete-btn', altura: 40, icone: 'fa-solid fa-check', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addEditModal = document.getElementById('add-edit-subject-modal');
        const subjectModalTitle = document.getElementById('subject-modal-title');
        const subjectForm = document.getElementById('subject-form');
        const subjectIdInput = document.getElementById('subject-id');
        const subjectNameInput = document.getElementById('subject-name');

        document.querySelectorAll('.add-subject-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                subjectModalTitle.textContent = 'Adicionar Nova Matéria';
                subjectForm.reset();
                subjectIdInput.value = '';
                window.ModalManager.open(addEditModal);
            });
        });

        const confirmationModal = document.getElementById('confirmation-modal');
        const confirmationMessage = document.getElementById('confirmation-message');
        const confirmationIdInput = document.getElementById('confirmation-id');

        document.getElementById('subjects-table-body').addEventListener('click', e => {
            const row = e.target.closest('tr');
            if (!row) return;

            if (e.target.closest('.edit-subject-btn')) {
                subjectModalTitle.textContent = 'Editar Matéria';
                subjectIdInput.value = row.dataset.subjectId;
                subjectNameInput.value = row.dataset.subjectName;
                window.ModalManager.open(addEditModal);
            }

            if (e.target.closest('.delete-subject-btn')) {
                confirmationMessage.innerHTML =
                    `Tem certeza que deseja excluir a matéria <strong>${row.dataset.subjectName}</strong>? Esta ação não pode ser desfeita.`;
                confirmationIdInput.value = row.dataset.subjectId;
                window.ModalManager.open(confirmationModal);
            }
        });

        document.getElementById('subject-search-input').addEventListener('input', e => {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('#subjects-table-body tr').forEach(row => {
                const name = row.dataset.subjectName.toLowerCase();
                row.style.display = name.includes(searchTerm) ? '' : 'none';
            });
        });
    });
</script>