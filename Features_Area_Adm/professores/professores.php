<?php
require_once BASE_PATH . 'core/services/UsuarioService.php';
require_once BASE_PATH . 'widgets/modal/Modal.php';
require_once BASE_PATH . 'core/services/TurmaService.php';

FuncoesUtils::adicionarJs('Widgets/modal/modal.js');

$usuarioService = new UsuarioService();
$turmaService = new TurmaService();
$feedbackMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $action = $_POST['action'] ?? '';

    if ($action === 'salvar_professor')
    {
        $professorDTO = new UsuarioDTO(
            empty($_POST['teacher-id']) ? null : (int)$_POST['teacher-id'],
            $_POST['teacher-name'] ?? '',
            $_POST['teacher-email'] ?? ''
        );
        $turmasIds = $_POST['teacher_classes'] ?? [];
        $feedbackMessage = $usuarioService->salvarProfessor($professorDTO, $turmasIds) ? "Professor salvo com sucesso!" : "Erro ao salvar professor.";
    }

    if ($action === 'desativar_professor')
    {
        $id = (int)$_POST['teacher-id-deactivate'];
        $feedbackMessage = $usuarioService->desativar($id) ? "Professor desativado com sucesso!" : "Erro ao desativar professor.";
    }

    $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=professores&msg=' . urlencode($feedbackMessage);
    header("Location: " . $redirectUrl);
    exit();
}

$professores = $usuarioService->buscarTodosProfessores();
$turmas = $turmaService->buscarTodas();
$feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
?>

<div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
    <h2 class="text-xl font-semibold text-center sm:text-left">Gerenciamento de Professores</h2>
    <?php // Botão para Desktop ?>
    <?php Botoes::getBotao('', 'Cadastrar Professor', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-plus-circle', classes: 'invite-teacher-btn hidden sm:inline-flex') ?>
</div>

<?php // Botão Flutuante para Mobile
Botoes::getBotoesFlutuantes([
    [
        'cor' => BotoesCores::VERDE,
        'icone' => 'fa-solid fa-plus text-xl',
        'type' => 'button',
        'classesAdicionais' => 'invite-teacher-btn'
    ]
]);
?>

<?php if ($feedbackMessage): ?>
    <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg relative mb-4" role="alert">
        <span class="block sm:inline"><?= $feedbackMessage ?></span>
    </div>
<?php endif; ?>
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
    <table class="w-full text-left">
        <thead class="bg-slate-50">
            <tr>
                <th class="p-4 font-semibold w-16"></th>
                <th class="p-4 font-semibold">Nome do Professor</th>
                <th class="p-4 font-semibold">Email</th>
                <th class="p-4 font-semibold">Turmas Associadas</th>
                <th class="p-4 font-semibold">Status</th>
                <th class="p-4 font-semibold text-right">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            <?php foreach ($professores as $professor):
                $turmasNomes = array_column($professor->turmas, 'nome_turma');
                $turmasIds = array_column($professor->turmas, 'id_turma');
                ?>
                <tr data-id="<?= $professor->id ?>" data-name="<?= htmlspecialchars($professor->nome) ?>"
                    data-email="<?= htmlspecialchars($professor->email) ?>" data-class-ids='<?= json_encode($turmasIds) ?>'>
                    <td class="p-2 text-center">
                        <img src="<?= $professor->urlImgPerfil ?? 'https://placehold.co/40x40/818cf8/ffffff?text=' . strtoupper(substr($professor->nome, 0, 1)) ?>"
                            class="w-10 h-10 rounded-full object-cover block mx-auto" alt="Avatar">
                    </td>
                    <td class="p-4 font-medium"><?= htmlspecialchars($professor->nome) ?></td>
                    <td class="p-4 text-slate-600"><?= htmlspecialchars($professor->email) ?></td>
                    <td class="p-4 text-slate-600">
                        <?= empty($turmasNomes) ? 'Nenhuma' : htmlspecialchars(implode(', ', $turmasNomes)) ?>
                    </td>
                    <td class="p-4">
                        <?php if ($professor->status === 'Ativo'): ?>
                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Ativo</span>
                        <?php else: ?>
                            <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Inativo</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-4 text-right">
                        <button
                            class="edit-teacher-btn text-slate-500 hover:text-blue-600 p-2 rounded-lg hover:bg-slate-100"
                            title="Editar Professor"><i class="fa-solid fa-pencil"></i></button>
                        <button
                            class="deactivate-teacher-btn text-slate-500 hover:text-red-600 p-2 rounded-lg hover:bg-slate-100"
                            title="Desativar Professor"><i class="fa-solid fa-user-xmark"></i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal para Convidar/Editar Professor -->
<?php Modal::begin('teacher-modal', 'Convidar Professor', 'teacher-modal-title', 'max-w-2xl'); ?>
<form class="space-y-4" method="POST">
    <input type="hidden" name="action" value="salvar_professor">
    <input type="hidden" id="teacher-id" name="teacher-id">
    <div>
        <label for="teacher-name" class="block text-sm font-medium text-slate-700 mb-1">Nome Completo</label>
        <input type="text" id="teacher-name" name="teacher-name"
            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md shadow-sm text-sm" required>
    </div>
    <div>
        <label for="teacher-email" class="block text-sm font-medium text-slate-700 mb-1">Email Institucional
            (para login)</label>
        <input type="email" id="teacher-email" name="teacher-email"
            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md shadow-sm text-sm" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Turmas Associadas</label>
        <div id="teacher-classes-container" class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <?php foreach ($turmas as $turma): ?>
                <label class="flex items-center space-x-2 text-slate-600"><input type="checkbox" name="teacher_classes[]"
                        value="<?= $turma->id ?>" class="rounded text-purple-600 focus:ring-purple-500">
                    <span><?= htmlspecialchars($turma->nome) ?></span></label>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, 'cancel-teacher-btn', altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'close-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Salvar', BotoesCores::VERDE, 'save-teacher-btn', altura: 40, icone: 'fa-solid fa-floppy-disk', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal de Confirmação para Desativar -->
<?php Modal::begin('deactivate-teacher-modal', null, '', 'max-w-md'); ?>
<form id="deactivate-teacher-form" method="POST">
    <input type="hidden" name="action" value="desativar_professor">
    <input type="hidden" id="teacher-id-deactivate" name="teacher-id-deactivate">
    <h3 id="deactivate-teacher-title" class="text-xl font-semibold mb-4">Desativar Professor</h3>
    <p class="text-slate-600 mb-6">O professor não terá mais acesso ao portal. Tem certeza?</p>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'close-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Desativar', BotoesCores::VERMELHO, 'confirm-deactivate-teacher-btn', altura: 40, icone: 'fa-solid fa-user-xmark', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const teacherModal = document.getElementById('teacher-modal');
        const deactivateModal = document.getElementById('deactivate-teacher-modal');
        const classCheckboxes = document.querySelectorAll('input[name="teacher_classes[]"]');

        // Lógica para abrir modal de convidar
        document.querySelectorAll('.invite-teacher-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                teacherModal.querySelector('#teacher-modal-title').textContent = 'Cadastrar Professor';
                teacherModal.querySelector('form').reset();
                teacherModal.querySelector('#teacher-id').value = '';
                teacherModal.querySelector('#teacher-email').disabled = false;
                window.ModalManager.open(teacherModal);
            });
        });

        // Lógica para abrir modal de editar
        document.querySelectorAll('.edit-teacher-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.currentTarget.closest('tr');
                teacherModal.querySelector('#teacher-modal-title').textContent = 'Editar Professor';
                teacherModal.querySelector('#teacher-id').value = row.dataset.id;
                teacherModal.querySelector('#teacher-name').value = row.dataset.name;
                teacherModal.querySelector('#teacher-email').value = row.dataset.email;
                teacherModal.querySelector('#teacher-email').disabled = true;

                // Lida com as checkboxes de turmas
                const assignedClassIds = JSON.parse(row.dataset.classIds);
                document.querySelectorAll('input[name="teacher_classes[]"]').forEach(checkbox => {
                    checkbox.checked = assignedClassIds.includes(parseInt(checkbox.value));
                });

                window.ModalManager.open(teacherModal);
            });
        });

        // Lógica para abrir modal de desativar
        document.querySelectorAll('.deactivate-teacher-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.currentTarget.closest('tr');
                const teacherName = row.dataset.name;
                const teacherId = row.dataset.id;
                deactivateModal.querySelector('#deactivate-teacher-title').textContent = `Desativar ${teacherName}`;
                deactivateModal.querySelector('#teacher-id-deactivate').value = teacherId;
                window.ModalManager.open(deactivateModal);
            });
        });
    });
</script>