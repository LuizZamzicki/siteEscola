<?php
require_once BASE_PATH . 'core/services/GeneroService.php';
require_once BASE_PATH . 'widgets/modal/Modal.php';
require_once BASE_PATH . 'core/models/GeneroLivroDTO.php';

FuncoesUtils::adicionarJs('Widgets/modal/modal.js');

$generoService = new GeneroService();
$feedbackMessage = '';
$feedbackType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $action = $_POST['action'] ?? '';

    if ($action === 'salvar_genero')
    {
        $generoDTO = new GeneroLivroDTO(
            empty($_POST['genre-id']) ? null : (int)$_POST['genre-id'],
            $_POST['genre-name'] ?? ''
        );

        if ($generoService->salvarGenero($generoDTO))
        {
            $feedbackMessage = "Gênero salvo com sucesso!";
        }
        else
        {
            $feedbackMessage = "Erro ao salvar gênero.";
            $feedbackType = 'error';
        }
    }

    if ($action === 'excluir_genero')
    {
        $id = (int)$_POST['id'];
        $errorMessage = '';
        if ($generoService->excluirGenero($id, $errorMessage))
        {
            $feedbackMessage = "Gênero excluído com sucesso!";
        }
        else
        {
            $feedbackMessage = $errorMessage ?: "Erro ao excluir gênero.";
            $feedbackType = 'error';
        }
    }

    $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=generos_biblioteca&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
    header("Location: " . $redirectUrl);
    exit();
}

$generos = $generoService->buscarTodos();

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
        <h2 class="text-xl font-semibold">Gerenciar Gêneros</h2>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-grow">
                <input id="genre-search-input" type="text" placeholder="Pesquisar por nome..."
                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-full focus:ring-2 focus:ring-purple-400 w-full text-sm">
                <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            </div>
            <?php // Botão para Desktop ?>
            <?php Botoes::getBotao('', 'Adicionar Gênero', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-plus-circle', type: 'button', classes: 'add-genre-btn hidden sm:inline-flex') ?>
        </div>
    </div>

    <?php // Botão Flutuante para Mobile
    Botoes::getBotoesFlutuantes([
        [
            'cor' => BotoesCores::VERDE,
            'icone' => 'fa-solid fa-plus text-xl',
            'type' => 'button',
            'classesAdicionais' => 'add-genre-btn'
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
            <tbody class="divide-y divide-slate-200" id="genres-table-body">
                <?php foreach ($generos as $genero): ?>
                        <tr data-genre-id="<?= $genero->id ?>" data-genre-name="<?= htmlspecialchars($genero->descricao) ?>">
                            <td class="p-4 font-medium"><?= htmlspecialchars($genero->descricao) ?></td>
                            <td class="p-4 flex items-center justify-end gap-2">
                                <button
                                    class="edit-genre-btn text-slate-500 hover:text-blue-600 p-2 rounded-lg hover:bg-slate-100"
                                    title="Editar"><i class="fa-solid fa-pencil"></i></button>
                                <button
                                    class="delete-genre-btn text-slate-500 hover:text-red-600 p-2 rounded-lg hover:bg-slate-100"
                                    title="Excluir"><i class="fa-solid fa-trash-can"></i></button>
                            </td>
                        </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Adicionar/Editar Gênero -->
<?php Modal::begin('add-edit-genre-modal', 'Adicionar Novo Gênero', 'genre-modal-title', 'max-w-md', 'z-30'); ?>
<form id="genre-form" method="POST">
    <input type="hidden" name="action" value="salvar_genero">
    <input type="hidden" id="genre-id" name="genre-id">
    <div class="space-y-4">
        <div>
            <label for="genre-name" class="block text-sm font-medium text-slate-700 mb-1">Nome do Gênero</label>
            <input type="text" id="genre-name" name="genre-name" required
                class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm">
        </div>
    </div>
    <div class="flex justify-end gap-3 pt-6">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn') ?>
        <?php Botoes::getBotao('', 'Salvar', BotoesCores::VERDE, 'save-genre-btn', altura: 40, icone: 'fa-solid fa-floppy-disk', type: 'submit') ?>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal de Confirmação para Exclusão -->
<?php Modal::begin('confirmation-modal', null, '', 'max-w-md', 'z-30'); ?>
<form id="confirmation-form" method="POST">
    <input type="hidden" name="action" value="excluir_genero">
    <input type="hidden" id="confirmation-id" name="id">
    <h3 id="confirmation-title" class="text-xl font-semibold mb-4">Confirmar Exclusão</h3>
    <p id="confirmation-message" class="text-slate-600 mb-6">Tem certeza que deseja excluir este gênero?</p>
    <div class="flex justify-end gap-3">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn') ?>
        <?php Botoes::getBotao('', 'Confirmar', BotoesCores::VERMELHO, 'confirm-delete-btn', altura: 40, icone: 'fa-solid fa-check', type: 'submit') ?>
    </div>
</form>
<?php Modal::end(); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addEditModal = document.getElementById('add-edit-genre-modal');
        const genreModalTitle = document.getElementById('genre-modal-title');
        const genreForm = document.getElementById('genre-form');
        const genreIdInput = document.getElementById('genre-id');
        const genreNameInput = document.getElementById('genre-name');

        document.querySelectorAll('.add-genre-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                genreModalTitle.textContent = 'Adicionar Novo Gênero';
                genreForm.reset();
                genreIdInput.value = '';
                window.ModalManager.open(addEditModal);
            });
        });

        const confirmationModal = document.getElementById('confirmation-modal');
        const confirmationMessage = document.getElementById('confirmation-message');
        const confirmationIdInput = document.getElementById('confirmation-id');

        document.getElementById('genres-table-body').addEventListener('click', e => {
            const row = e.target.closest('tr');
            if (!row) return;

            if (e.target.closest('.edit-genre-btn')) {
                genreModalTitle.textContent = 'Editar Gênero';
                genreIdInput.value = row.dataset.genreId;
                genreNameInput.value = row.dataset.genreName;
                window.ModalManager.open(addEditModal);
            }

            if (e.target.closest('.delete-genre-btn')) {
                confirmationMessage.innerHTML =
                    `Tem certeza que deseja excluir o gênero <strong>${row.dataset.genreName}</strong>? Esta ação não pode ser desfeita.`;
                confirmationIdInput.value = row.dataset.genreId;
                window.ModalManager.open(confirmationModal);
            }
        });

        document.getElementById('genre-search-input').addEventListener('input', e => {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('#genres-table-body tr').forEach(row => {
                const name = row.dataset.genreName.toLowerCase();
                row.style.display = name.includes(searchTerm) ? '' : 'none';
            });
        });
    });
</script>