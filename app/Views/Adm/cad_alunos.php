<?php
// Espera receber $alunos, $turmas e $feedbackMessage já preparados pelo controller
?>
<div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
    <h2 class="text-xl font-semibold text-center sm:text-left">Gerenciamento de Alunos</h2>
    <div class="flex flex-wrap items-center justify-center sm:justify-end gap-4 w-full sm:w-auto">
        <div class="relative flex-grow w-full sm:w-auto">
            <input id="student-search-input" type="text" placeholder="Pesquisar por nome..."
                class="pl-10 pr-4 py-2 border border-slate-300 rounded-full focus:ring-2 focus:ring-purple-400 w-full text-sm">
            <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
            <input type="checkbox" id="show-inactive-toggle" class="rounded text-purple-600 focus:ring-purple-500">
            <span>Mostrar Inativos</span>
        </label>
        <?php // Botão para Desktop ?>
        <?php Botoes::getBotao('', 'Cadastrar Aluno', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-plus-circle', type: 'button', classes: 'invite-student-btn hidden sm:inline-flex') ?>
    </div>
</div>

<?php // Botão Flutuante para Mobile
Botoes::getBotoesFlutuantes([
    [
        'cor' => BotoesCores::VERDE,
        'icone' => 'fa-solid fa-plus text-xl',
        'type' => 'button',
        'classesAdicionais' => 'invite-student-btn'
    ]
]);
?>

<?php if ($feedbackMessage): ?>
    <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg relative mb-4" role="alert">
        <span class="block sm:inline"><?= $feedbackMessage ?></span>
    </div>
<?php endif; ?>
<div class="relative">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-4 font-semibold w-16"></th>
                    <th class="p-4 font-semibold">Nome</th>
                    <th class="p-4 font-semibold">Email Institucional</th>
                    <th class="p-4 font-semibold">Turma</th>
                    <th class="p-4 font-semibold">Status</th>
                    <th class="p-4 font-semibold text-right">Ações</th>
                </tr>
            </thead>
            <tbody id="students-table-body" class="divide-y divide-slate-200">
                <?php foreach ($alunos as $aluno): ?>
                    <tr data-id="<?= $aluno->id ?>" data-name="<?= htmlspecialchars($aluno->nome) ?>"
                        data-email="<?= htmlspecialchars($aluno->email) ?>"
                        data-class="<?= htmlspecialchars($aluno->turma) ?>"
                        data-status="<?= htmlspecialchars($aluno->status) ?>">
                        <td class="p-2 text-center">
                            <img src="<?= $aluno->urlImgPerfil ?? 'https://placehold.co/40x40/c4b5fd/ffffff?text=' . strtoupper(substr($aluno->nome, 0, 1)) ?>"
                                class="w-10 h-10 rounded-full inline-block object-cover" alt="Avatar">
                        </td>
                        <td class="p-4 font-medium"><?= htmlspecialchars($aluno->nome) ?></td>
                        <td class="p-4 text-slate-600"><?= htmlspecialchars($aluno->email) ?></td>
                        <td class="p-4 text-slate-600"><?= htmlspecialchars($aluno->turma ?? 'Não enturmado') ?></td>
                        <td class="p-4">
                            <?php if ($aluno->status === 'Ativo'): ?>
                                <span
                                    class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Ativo</span>
                            <?php elseif ($aluno->status === 'Inativo'): ?>
                                <span
                                    class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Inativo</span>
                            <?php else: ?>
                                <span
                                    class="px-2 py-1 text-xs font-semibold text-slate-700 bg-slate-200 rounded-full"><?= htmlspecialchars($aluno->status) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-right">
                            <button
                                class="edit-student-btn text-slate-500 hover:text-blue-600 p-2 rounded-lg hover:bg-slate-100"
                                title="Editar Aluno"><i class="fa-solid fa-pencil"></i></button>
                            <button
                                class="deactivate-student-btn text-slate-500 hover:text-red-600 p-2 rounded-lg hover:bg-slate-100"
                                title="Desativar Aluno"><i class="fa-solid fa-user-xmark"></i></button>
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

<!-- Modal para Convidar/Editar Aluno -->
<?php Modal::begin('student-modal', 'Convidar Aluno', 'student-modal-title', 'max-w-2xl'); ?>
<form class="space-y-4" method="POST">
    <input type="hidden" name="action" value="salvar_aluno">
    <input type="hidden" id="student-id" name="student-id">
    <div>
        <label for="student-name" class="block text-sm font-medium text-slate-700 mb-1">Nome Completo</label>
        <input type="text" id="student-name" name="student-name"
            class="w-full px-4 py-3 bg-white border border-slate-300 rounded-lg shadow-sm text-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            required>
    </div>
    <div>
        <label for="student-email" class="block text-sm font-medium text-slate-700 mb-1">Email Institucional
            (para login)</label>
        <input type="email" id="student-email" name="student-email"
            class="w-full px-4 py-3 bg-white border border-slate-300 rounded-lg shadow-sm text-base focus:ring-2 focus:ring-purple-500 focus:border-purple-500 disabled:bg-slate-100"
            required>
    </div>
    <div>
        <label for="student-class" class="block text-sm font-medium text-slate-700 mb-1">Turma</label>
        <select id="student-class" name="student-class"
            class="w-full px-4 py-3 bg-white border border-slate-300 rounded-lg shadow-sm text-base focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            required>
            <option value="">Selecione a turma</option>
            <?php foreach ($turmas as $turma): ?>
                <option value="<?= htmlspecialchars($turma->nome) ?>"><?= htmlspecialchars($turma->nome) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, 'cancel-student-btn', icone: 'fa-solid fa-xmark', type: 'button', classes: 'w-full sm:w-auto close-modal-btn') ?>
        <?php Botoes::getBotao('', 'Salvar', BotoesCores::VERDE, 'save-student-btn', icone: 'fa-solid fa-floppy-disk', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal de Confirmação para Desativar -->
<?php Modal::begin('deactivate-modal', null, '', 'max-w-md'); ?>
<form id="deactivate-form" method="POST">
    <input type="hidden" name="action" value="desativar_aluno">
    <input type="hidden" id="deactivate-student-id" name="deactivate-student-id">
    <h3 id="deactivate-title" class="text-xl font-semibold mb-4">Desativar Aluno</h3>
    <p id="deactivate-message" class="text-slate-600 mb-6">O aluno não terá mais acesso ao portal. Tem certeza?
    </p>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, icone: 'fa-solid fa-xmark', type: 'button', classes: 'w-full sm:w-auto close-modal-btn') ?>
        <?php Botoes::getBotao('', 'Desativar', BotoesCores::VERMELHO, 'confirm-deactivate-btn', icone: 'fa-solid fa-user-xmark', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const studentModal = document.getElementById('student-modal');
        const deactivateModal = document.getElementById('deactivate-modal');

        // Lógica para abrir modal de convidar
        document.querySelectorAll('.invite-student-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                studentModal.querySelector('#student-modal-title').textContent = 'Cadastrar Aluno';
                studentModal.querySelector('form').reset();
                studentModal.querySelector('#student-id').value = '';
                studentModal.querySelector('#student-email').disabled = false; // Habilita o campo de email para novos alunos
                window.ModalManager.open(studentModal);
            });
        });

        // Lógica para abrir modal de editar
        document.querySelectorAll('.edit-student-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.currentTarget.closest('tr');
                studentModal.querySelector('#student-modal-title').textContent = 'Editar Aluno';
                studentModal.querySelector('#student-id').value = row.dataset.id;
                studentModal.querySelector('#student-name').value = row.dataset.name;
                studentModal.querySelector('#student-email').value = row.dataset.email;
                studentModal.querySelector('#student-email').disabled = true; // Email não pode ser editado
                studentModal.querySelector('#student-class').value = row.dataset.class;
                window.ModalManager.open(studentModal);
            });
        });

        // Lógica para abrir modal de desativar
        document.querySelectorAll('.deactivate-student-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.currentTarget.closest('tr');
                const studentName = row.dataset.name;
                const studentId = row.dataset.id;
                deactivateModal.querySelector('#deactivate-title').textContent = `Desativar ${studentName}`;
                deactivateModal.querySelector('#deactivate-student-id').value = studentId;
                window.ModalManager.open(deactivateModal);
            });
        });

        // --- Filtering Logic ---
        const searchInput = document.getElementById('student-search-input');
        const showInactiveToggle = document.getElementById('show-inactive-toggle');
        const tableBody = document.getElementById('students-table-body');
        const rows = tableBody.querySelectorAll('tr');

        function filterStudents() {
            const searchTerm = searchInput.value.toLowerCase();
            const showInactive = showInactiveToggle.checked;

            rows.forEach(row => {
                const name = row.dataset.name.toLowerCase();
                const status = row.dataset.status;

                const nameMatch = name.includes(searchTerm);
                const statusMatch = showInactive || status === 'Ativo';

                if (nameMatch && statusMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterStudents);
        showInactiveToggle.addEventListener('change', filterStudents);

        // Initial filter on page load to hide inactive students by default
        filterStudents();
    });
</script>