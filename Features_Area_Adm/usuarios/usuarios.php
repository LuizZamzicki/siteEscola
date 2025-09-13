<?php

require_once BASE_PATH . 'widgets/modal/Modal.php';
require_once BASE_PATH . 'core/services/UsuarioService.php';

$usuarioService = new UsuarioService();
$feedbackMessage = '';

// Lógica para processar os formulários da página
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $action = $_POST['action'] ?? '';

    if ($action === 'salvar_usuario')
    {
        // Usa o operador null coalescing ?? para garantir que sempre há valor
        $usuario = new UsuarioDTO(
            empty($_POST['user-id']) ? null : (int)$_POST['user-id'],
            $_POST['user-name'] ?? '',   // se não existir, string vazia
            $_POST['user-email'] ?? '',
            status: 'Ativo',                     // status
            tipo: $_POST['user-role'] ?? ''    // se não existir, string vazia
        );

        if ($usuarioService->salvar($usuario))
        {
            $feedbackMessage = "Usuário salvo com sucesso!";
        }
        else
        {
            $feedbackMessage = "Erro ao salvar usuário.";
        }
    }


    if ($action === 'excluir_usuario')
    {
        $id = (int)$_POST['user-id-delete'];
        if ($usuarioService->excluir($id))
        {
            $feedbackMessage = "Usuário excluído com sucesso!";
        }
        else
        {
            $feedbackMessage = "Erro ao excluir usuário.";
        }
    }

    // Redireciona para evitar reenvio do formulário ao atualizar a página
    $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=configuracoes&msg=' . urlencode($feedbackMessage);
    header("Location: " . $redirectUrl);
    exit();
}

$usuarios = $usuarioService->buscarTodosAdmins();
$feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
?>

<div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold">Usuários e Permissões</h2>
        <p class="text-sm text-slate-600 mt-1">Gerencie quem pode acessar as áreas administrativas do sistema.</p>
    </div>
    <?php Botoes::getBotao('', 'Novo Usuário', BotoesCores::ROXO, null, altura: 40, icone: 'fa-solid fa-plus-circle', classes: 'add-user-btn hidden sm:inline-flex') ?>
</div>

<?php
Botoes::getBotoesFlutuantes([
    [
        'cor' => BotoesCores::ROXO,
        'icone' => 'fa-solid fa-plus text-xl',
        'type' => 'button',
        'classesAdicionais' => 'add-user-btn'
    ]
]);
?>

<?php if ($feedbackMessage): ?>
    <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg relative mb-4" role="alert">
        <span class="block sm:inline"><?= $feedbackMessage ?></span>
    </div>
<?php endif; ?>

<div class="bg-white rounded-xl shadow-sm border border-slate-200">
    <div class="p-6">
        <ul class="divide-y divide-slate-200">
            <?php foreach ($usuarios as $usuario): ?>
                <li class="flex items-center gap-4 py-3" data-id="<?= $usuario->id ?>"
                    data-name="<?= htmlspecialchars($usuario->nome) ?>"
                    data-email="<?= htmlspecialchars($usuario->email) ?>"
                    data-role="<?= htmlspecialchars($usuario->tipo) ?>">
                    <div class="flex flex-1 items-center gap-3 min-w-0">
                        <img src="<?= $usuario->urlImgPerfil ?? 'https://placehold.co/40x40/d8b4fe/ffffff?text=' . strtoupper(substr($usuario->nome, 0, 1)) ?>"
                            class="w-10 h-10 rounded-full object-cover flex-shrink-0" alt="Avatar">
                        <div class="min-w-0">
                            <p class="font-medium truncate" title="<?= htmlspecialchars($usuario->nome) ?>">
                                <?= htmlspecialchars($usuario->nome) ?>
                            </p>
                            <p class="text-sm text-slate-500 truncate" title="<?= htmlspecialchars($usuario->email) ?>">
                                <?= htmlspecialchars($usuario->email) ?>
                            </p>
                        </div>
                    </div>
                    <span
                        class="text-sm font-semibold text-purple-700 flex-shrink-0"><?= htmlspecialchars($usuario->tipo) ?></span>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button class="edit-user-btn text-slate-500 hover:text-blue-600 p-1"><i
                                class="fa-solid fa-pencil"></i></button>
                        <button class="delete-user-btn text-slate-500 hover:text-red-600 p-1"><i
                                class="fa-solid fa-trash-can"></i></button>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Modal para Adicionar/Editar Usuário -->
<?php Modal::begin('user-modal', 'Novo Usuário', 'user-modal-title', 'max-w-lg'); ?>
<form class="space-y-4" method="POST">
    <input type="hidden" name="action" value="salvar_usuario">
    <input type="hidden" id="user-id" name="user-id">
    <div>
        <label for="user-name" class="block text-sm font-medium text-slate-700 mb-1">Nome</label>
        <input type="text" id="user-name" name="user-name"
            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md shadow-sm text-sm" required>
    </div>
    <div>
        <label for="user-email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
        <input type="email" id="user-email" name="user-email"
            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md shadow-sm text-sm" required>
    </div>
    <div>
        <label for="user-role" class="block text-sm font-medium text-slate-700 mb-1">Permissão</label>
        <select id="user-role" <select id="user-role" name="user-role"
            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md shadow-sm text-sm" required>
            <option value="Super Admin">Super Admin</option>
            <option value="Secretaria">Secretaria</option>
            <option value="Bibliotecario">Bibliotecário</option>
        </select>
    </div>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, 'cancel-user-btn', altura: 40, icone: 'fa-solid fa-xmark', type: 'button') ?>
        <?php Botoes::getBotao('', 'Salvar', BotoesCores::VERDE, 'save-user-btn', altura: 40, icone: 'fa-solid fa-floppy-disk', type: 'submit') ?>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal de Confirmação para Excluir Usuário -->
<?php Modal::begin('delete-user-modal', null, '', 'max-w-md'); ?>
<form id="delete-user-form" method="POST">
    <input type="hidden" name="action" value="excluir_usuario">
    <input type="hidden" id="user-id-delete" name="user-id-delete">
    <h3 id="delete-user-title" class="text-xl font-semibold mb-4">Excluir Usuário</h3>
    <p class="text-slate-600 mb-6">Esta ação é permanente. Tem certeza que deseja excluir este usuário?</p>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, 'cancel-delete-btn', altura: 40, icone: 'fa-solid fa-xmark', type: 'button') ?>
        <?php Botoes::getBotao('', 'Excluir', BotoesCores::VERMELHO, 'confirm-delete-btn', altura: 40, icone: 'fa-solid fa-trash-can', type: 'submit') ?>
    </div>
</form>
<?php Modal::end(); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const userModal = document.getElementById('user-modal');
        const deleteModal = document.getElementById('delete-user-modal');

        // Cancel buttons in forms should also close the modal
        document.querySelectorAll('#cancel-user-btn, #cancel-delete-btn').forEach(btn => {
            btn.addEventListener('click', () => window.ModalManager.close(btn.closest('.modal-backdrop')));
        });

        // "Novo Usuário" button logic
        document.querySelectorAll('.add-user-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                userModal.querySelector('#user-modal-title').textContent = 'Novo Usuário';
                userModal.querySelector('form').reset();
                userModal.querySelector('#user-id').value = '';
                window.ModalManager.open(userModal);
            });
        });

        // "Editar Usuário" buttons logic
        document.querySelectorAll('.edit-user-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const userRow = e.currentTarget.closest('li');
                userModal.querySelector('#user-modal-title').textContent = 'Editar Usuário';
                userModal.querySelector('#user-id').value = userRow.dataset.id;
                userModal.querySelector('#user-name').value = userRow.dataset.name;
                userModal.querySelector('#user-email').value = userRow.dataset.email;
                userModal.querySelector('#user-role').value = userRow.dataset.role;
                window.ModalManager.open(userModal);
            });
        });

        // "Excluir Usuário" buttons logic
        document.querySelectorAll('.delete-user-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const userRow = e.currentTarget.closest('li');
                const userName = userRow.dataset.name;
                const userId = userRow.dataset.id;
                deleteModal.querySelector('#delete-user-title').textContent = `Excluir ${userName}`;
                deleteModal.querySelector('#user-id-delete').value = userId;
                window.ModalManager.open(deleteModal);
            });
        });
    });
</script>