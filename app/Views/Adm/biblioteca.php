<?php
// Espera receber todos os dados necessários já preparados pelo controller

        $autorId = $autorNome ? $autorService->consultarOuCriarAutor($autorNome) : null;
        $editoraId = $editoraNome ? $editoraService->consultarOuCriarEditora($editoraNome) : null;
        
        $generoNomes = !empty($generoNomesStr) ? array_map('trim', explode(',', $generoNomesStr)) : [];
        $generoIds = [];
        foreach ($generoNomes as $generoNome)
        {
            if (!empty($generoNome))
            {
                $generoIds[] = $generoService->consultarOuCriarGenero($generoNome);
            }
        }

        // --- Salvar o livro ---
        $feedbackMessage = $bibliotecaService->salvarLivro($livroDTO, $foto, $autorId, $editoraId, $generoIds)
            ? "Livro salvo com sucesso!"
            : "Erro ao salvar livro."; 
    

    if ($action === 'emprestimo_manual')
    {
        $livroId = (int)($_POST['loan-book-id'] ?? 0);
        $alunoId = (int)($_POST['loan-student-id'] ?? 0);
        $dataDevolucao = $_POST['loan-return-date'] ?? '';
        $success = false;
        $json_data = [];
        
        if ($livroId && $alunoId && !empty($dataDevolucao))
        {
            $db = new Database();
            $db->beginTransaction();
            try
            {
                $livro = $db->selectOne("SELECT titulo, qtde_disponivel FROM livros WHERE id_livro = :id", [':id' => $livroId]);

                if ($livro && $livro->qtde_disponivel > 0)
                {
                    $db->execute("UPDATE livros SET qtde_disponivel = qtde_disponivel - 1 WHERE id_livro = :id", [':id' => $livroId]);
                    $db->execute(
                        "INSERT INTO emprestimos (id_usuario, id_livro, data_emprestimo, data_devolucao_prevista, status) VALUES (:id_usuario, :id_livro, CURDATE(), :data_devolucao, 'Emprestado')",
                        [':id_usuario' => $alunoId, ':id_livro' => $livroId, ':data_devolucao' => $dataDevolucao]
                    );
                    $newLoanId = $db->lastInsertId();
                    $db->commit();
                    $success = true;
                    $feedbackMessage = "Empréstimo manual realizado com sucesso!";

                    // Buscar dados do novo empréstimo para retornar ao JS
                    $newLoanData = $db->selectOne(
                        "SELECT e.id_emprestimo as id, l.titulo as livroTitulo, u.nome as usuarioNome, e.data_devolucao_prevista as dataDevolucaoPrevista 
                         FROM emprestimos e 
                         JOIN livros l ON e.id_livro = l.id_livro 
                         JOIN usuarios u ON e.id_usuario = u.id_usuario 
                         WHERE e.id_emprestimo = :id",
                        [':id' => $newLoanId]
                    );
                    if ($newLoanData)
                    {
                        $json_data['newLoan'] = $newLoanData;
                        $json_data['livroId'] = $livroId;
                    }
                }
                else
                {
                    $db->rollBack();
                    $feedbackMessage = "Erro: Livro não disponível para empréstimo.";
                }
            }
            catch (Exception $e)
            {
                $db->rollBack();
                $feedbackMessage = "Erro ao realizar empréstimo manual.";
                error_log("Erro no empréstimo manual: " . $e->getMessage());
            }
        }
        header('Content-Type: application/json');
        echo json_encode(array_merge(['success' => $success, 'message' => $feedbackMessage], $json_data));
        exit();
    }

    if ($action === 'confirmar_acao')
    {
        $sub_action = $_POST['sub_action'];
        $id = (int)$_POST['id'];
        $errorMessage = '';
        $success = false;
        $json_data = [];

        switch ($sub_action)
        {
            case 'excluir_livro':
                $success = $bibliotecaService->excluirLivro($id, $errorMessage);
                $feedbackMessage = $success ? "Livro excluído com sucesso!" : ($errorMessage ?: "Erro ao excluir livro.");
                break;
            case 'devolver_livro':
                $success = $bibliotecaService->devolverLivro($id);
                $feedbackMessage = $success ? "Devolução confirmada!" : "Erro ao confirmar devolução.";
                break;
            case 'estender_emprestimo':
                // Por padrão, estende por 15 dias.
                $newDate = $bibliotecaService->estenderEmprestimo($id, 15);
                $success = $newDate !== null;
                $feedbackMessage = $success ? "Prazo de empréstimo estendido com sucesso!" : "Erro ao estender o prazo.";
                if ($success)
                {
                    $json_data['new_date'] = (new DateTime($newDate))->format('d/m/Y');
                }
                break;
            case 'aprovar_reserva':
                $success = $reservaService->confirmarRetirada($id);
                $feedbackMessage = $success ? "Retirada do livro confirmada e empréstimo iniciado!" : "Erro ao confirmar retirada.";
                break;
            case 'recusar_reserva':
                $success = $reservaService->cancelarReservaAdmin($id);
                $feedbackMessage = $success ? "Reserva cancelada." : "Erro ao cancelar reserva.";
                break;
        }

        // Responder com JSON para a requisição AJAX e interromper o script
        header('Content-Type: application/json');
        echo json_encode(array_merge(['success' => $success, 'message' => $feedbackMessage], $json_data));
        exit();
    }

    $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=biblioteca&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
    header("Location: " . $redirectUrl);
    exit();
}



$livros = $bibliotecaService->buscarTodosLivros();
$emprestimos = $bibliotecaService->buscarEmprestimosAtivos();
$reservas = $reservaService->buscarPorStatus('Aguardando Retirada'); // A UI atual é para confirmar retirada
$todosAlunos = $usuarioService->buscarTodosAlunos();
$alunosAtivos = array_filter($todosAlunos, fn($aluno) => $aluno->status === 'Ativo');
$feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
?>

<div class="space-y-6">
    <div class="bg-white p-2 rounded-xl shadow-sm border border-slate-200">
        <nav class="flex space-x-2" id="library-tabs">
            <button data-tab="acervo"
                class="library-tab flex-1 text-center px-4 py-2 rounded-lg font-semibold text-sm transition bg-purple-600 text-white">Acervo</button>
            <button data-tab="emprestimos"
                class="library-tab flex-1 text-center px-4 py-2 rounded-lg font-semibold text-sm transition text-slate-600 hover:bg-slate-100">Empréstimos
                Ativos</button>
            <button data-tab="reservas" id="reservas-tab-btn"
                class="library-tab flex-1 text-center px-4 py-2 rounded-lg font-semibold text-sm transition text-slate-600 hover:bg-slate-100">Pedidos
                de Reserva</button>
        </nav>
    </div>


    <div id="acervo-content" class="library-tab-content space-y-4">
        <?php if ($feedbackMessage): ?>
            <div class="<?= $feedbackType === 'success' ? 'bg-green-100 border-green-200 text-green-800' : 'bg-red-100 border-red-200 text-red-800' ?> border px-4 py-3 rounded-lg relative"
                role="alert">
                <span class="block sm:inline"><?= $feedbackMessage ?></span>
            </div>
        <?php endif; ?>
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="text-xl font-semibold">Acervo da Biblioteca</h2>
            <div class="flex flex-wrap items-center justify-center sm:justify-end gap-2 w-full sm:w-auto">
                <div class="relative flex-grow w-full sm:w-auto">
                    <input id="book-search-input" type="text" placeholder="Pesquisar por título ou autor..."
                        class="pl-10 pr-4 py-2 border border-slate-300 rounded-full focus:ring-2 focus:ring-purple-400 w-full text-sm">
                    <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
                <div class="relative">
                    <select id="book-status-filter"
                        class="pl-4 pr-8 py-2 border border-slate-300 rounded-full focus:ring-2 focus:ring-purple-400 text-sm text-slate-600 appearance-none">
                        <option value="todos">Todos os status</option>
                        <option value="disponivel">Disponível</option>
                        <?php
                        // Esta view espera receber do controller:
                        // $livros: array de livros
                        // $emprestimos: array de empréstimos ativos
                        // $reservas: array de reservas aguardando retirada
                        // $alunosAtivos: array de alunos ativos
                        // $feedbackMessage: mensagem de feedback (string)
                        // $feedbackType: tipo de feedback ('success' ou 'error')
                        ?>
            </div>
        </div>
        <div class="space-y-4" id="reservations-list">
            <?php foreach ($reservas as $reserva): ?>
                <div class="reservation-item bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex items-center justify-between"
                    data-reservation-id="<?= $reserva->id ?>"
                    data-student-name="<?= htmlspecialchars($reserva->usuarioNome) ?>"
                    data-book-title="<?= htmlspecialchars($reserva->livroTitulo) ?>">
                    <div>
                        <p class="font-bold book-title"><?= htmlspecialchars($reserva->livroTitulo) ?></p>
                        <p class="text-sm text-slate-500 student-name-display">Pedido por: <span
                                class="font-medium student-name-text"><?= htmlspecialchars($reserva->usuarioNome) ?></span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="request-btn refuse bg-red-100 text-red-700 p-2 rounded-lg hover:bg-red-200"
                            title="Cancelar Reserva">
                            <i class="fa-solid fa-xmark"></i></button>
                        <button class="request-btn approve bg-green-100 text-green-700 p-2 rounded-lg hover:bg-green-200"
                            title="Confirmar Retirada e Emprestar">
                            <i class="fa-solid fa-check"></i></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="text-center text-slate-500 py-4 <?= !empty($reservas) ? 'hidden' : '' ?>" id="no-reservations-msg">
            Nenhuma reserva aguardando retirada.</p>
    </div>
</div>

<?php Modal::begin('add-edit-book-modal', 'Adicionar Novo Livro', 'book-modal-title', 'max-w-6xl', 'z-30'); ?>
<form class="space-y-6" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="salvar_livro">
    <input type="hidden" id="book-id" name="book-id">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Coluna da foto -->
        <div class="flex flex-col items-center">
            <label for="book-photo" class="block text-sm font-medium text-slate-700 mb-2">Foto do Livro</label>
            <img id="book-photo-preview" src="https://placehold.co/150x200/e2e8f0/94a3b8?text=Capa"
                class="mb-4 w-40 h-56 object-cover rounded-md border border-slate-200">
            <input type="file" id="book-photo" name="book-photo" accept="image/*"
                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
        </div>

        <!-- Colunas dos campos -->
        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="book-title" class="block text-sm font-medium text-slate-700 mb-1">Título</label>
                <input type="text" id="book-title" name="book-title" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label for="book-subtitle" class="block text-sm font-medium text-slate-700 mb-1">Subtítulo</label>
                <input type="text" id="book-subtitle" name="book-subtitle"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm">
            </div>
            <div class="relative">
                <label for="book-author" class="block text-sm font-medium text-slate-700 mb-1">Autor</label>
                <input type="text" id="book-author" name="book-author" autocomplete="off"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm autocomplete-input"
                    data-search-action="search_autores">
                <div
                    class="autocomplete-results hidden absolute z-10 w-full bg-white border border-slate-300 rounded-md mt-1 shadow-lg max-h-48 overflow-y-auto">
                </div>
            </div>
            <div class="relative">
                <label for="book-publisher" class="block text-sm font-medium text-slate-700 mb-1">Editora</label>
                <input type="text" id="book-publisher" name="book-publisher" autocomplete="off"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm autocomplete-input"
                    data-search-action="search_editoras">
                <div
                    class="autocomplete-results hidden absolute z-10 w-full bg-white border border-slate-300 rounded-md mt-1 shadow-lg max-h-48 overflow-y-auto">
                </div>
            </div>
            <div>
                <label for="book-genre-input" class="block text-sm font-medium text-slate-700 mb-1">Gêneros
                    (digite e tecle Enter)</label>
                <div id="genre-tags-container"
                    class="flex flex-wrap gap-2 p-2 border border-slate-300 rounded-md shadow-sm items-center">
                    <!-- Tags de gênero serão inseridas aqui pelo JS -->
                    <input type="text" id="book-genre-input" autocomplete="off"
                        class="flex-grow p-1 border-none focus:ring-0 text-sm" placeholder="Adicionar gênero..."
                        data-search-action="search_generos">
                </div>
                <input type="hidden" id="book-genre" name="book-genre">
                <div id="genre-autocomplete-results"
                    class="autocomplete-results hidden relative z-10 w-full bg-white border border-slate-300 rounded-md mt-1 shadow-lg max-h-48 overflow-y-auto">
                </div>
            </div>
            <div>
                <label for="book-qty" class="block text-sm font-medium text-slate-700 mb-1">Quantidade</label>
                <input type="number" id="book-qty" name="book-qty" min="1" value="1" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label for="book-pages" class="block text-sm font-medium text-slate-700 mb-1">Nº de
                    Páginas</label>
                <input type="number" id="book-pages" name="book-pages" min="1"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label for="book-pubdate" class="block text-sm font-medium text-slate-700 mb-1">Data de
                    Publicação</label>
                <input type="date" id="book-pubdate" name="book-pubdate"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm">
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3 pt-6">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn') ?>
        <?php Botoes::getBotao('', 'Salvar', BotoesCores::VERDE, 'save-book', altura: 40, icone: 'fa-solid fa-floppy-disk', type: 'submit') ?>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal de Confirmação Genérico -->
<?php Modal::begin('confirmation-modal', 'Confirmar Ação', 'confirmation-title', 'max-w-md', 'z-30'); ?>
<form id="confirmation-form" method="POST">
    <input type="hidden" name="action" value="confirmar_acao">
    <input type="hidden" id="confirmation-sub-action" name="sub_action">
    <input type="hidden" id="confirmation-id" name="id">
    <h3 id="confirmation-title" class="text-xl font-semibold mb-4">Confirmar Ação</h3>
    <p id="confirmation-message" class="text-slate-600 mb-6">Tem certeza que deseja prosseguir?</p>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Confirmar', BotoesCores::VERDE, 'confirm-action-btn', altura: 40, icone: 'fa-solid fa-check', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal Empréstimo Manual -->
<?php Modal::begin('manual-loan-modal', 'Realizar Empréstimo Manual', 'manual-loan-modal-title', 'max-w-2xl', 'z-30'); ?>
<form id="manual-loan-form" method="POST">
    <input type="hidden" name="action" value="emprestimo_manual">
    <div class="space-y-4">
        <div>
            <label for="loan-book-select" class="block text-sm font-medium text-slate-700 mb-1">Selecione o Livro
                (apenas disponíveis)</label>
            <select id="loan-book-select" name="loan-book-id" required
                class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm">
                <option value="">Selecione um livro</option>
                <?php foreach ($livros as $livro): ?>
                    <?php if ($livro->qtde_disponivel > 0): ?>
                        <option value="<?= $livro->id ?>"><?= htmlspecialchars($livro->titulo) ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="loan-student-select" class="block text-sm font-medium text-slate-700 mb-1">Selecione o
                Aluno</label>
            <select id="loan-student-select" name="loan-student-id" required
                class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm">
                <option value="">Selecione um aluno</option>
                <?php foreach ($alunosAtivos as $aluno): ?>
                    <option value="<?= $aluno->id ?>"><?= htmlspecialchars($aluno->nome) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="loan-return-date" class="block text-sm font-medium text-slate-700 mb-1">Data de
                Devolução</label>
            <input type="date" id="loan-return-date" name="loan-return-date" required
                class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm">
        </div>
    </div>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-6">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Confirmar Empréstimo', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-check', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Elementos ---
        const libraryTabs = document.querySelectorAll('.library-tab');
        const tabContents = document.querySelectorAll('.library-tab-content');
        const addEditBookModal = document.getElementById('add-edit-book-modal');
        const confirmationModal = document.getElementById('confirmation-modal');
        const bookModalTitle = document.getElementById('book-modal-title');
        const bookForm = addEditBookModal.querySelector('form');
        const bookIdInput = document.getElementById('book-id');
        const bookTitleInput = document.getElementById('book-title');
        const bookAuthorInput = document.getElementById('book-author');
        const bookPhotoInput = document.getElementById('book-photo');
        const bookPhotoPreview = document.getElementById('book-photo-preview');
        const bookPubdateInput = document.getElementById('book-pubdate');
        const bookPagesInput = document.getElementById('book-pages');
        const bookQtyInput = document.getElementById('book-qty');
        const acervoTbody = document.querySelector('#acervo-content tbody');
        const studentSearchInput = document.getElementById('student-search-input');
        const bookSearchInput = document.getElementById('book-search-input');
        const loanDateFilter = document.getElementById('loan-date-filter');
        const bookStatusFilter = document.getElementById('book-status-filter');
        const loansTable = document.getElementById('loans-table');
        const confirmationForm = document.getElementById('confirmation-form');
        const manualLoanModal = document.getElementById('manual-loan-modal');
        const manualLoanForm = document.getElementById('manual-loan-form');
        const reservationSearchInput = document.getElementById('reservation-search-input');
        const reservationsList = document.getElementById('reservations-list');
        const noReservationsMsg = document.getElementById('no-reservations-msg');

        // --- Tags de generos ---
        const genreInput = document.getElementById('book-genre-input');
        const genreTagsContainer = document.getElementById('genre-tags-container');
        const hiddenGenreInput = document.getElementById('book-genre');
        const genreAutocompleteResults = document.getElementById('genre-autocomplete-results');

        // --- Troca de Abas ---
        libraryTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                if (tab.id === 'reservas-tab-btn') {
                    document.querySelector('#reservas-content h2').textContent = 'Reservas Aguardando Retirada';
                }
                const targetTab = tab.dataset.tab;

                libraryTabs.forEach(t => {
                    t.classList.remove('bg-purple-600', 'text-white');
                    t.classList.add('text-slate-600', 'hover:bg-slate-100');
                });

                tab.classList.add('bg-purple-600', 'text-white');
                tab.classList.remove('text-slate-600', 'hover:bg-slate-100');

                tabContents.forEach(content => {
                    if (content.id === `${targetTab}-content`) {
                        content.classList.remove('hidden');
                    } else {
                        content.classList.add('hidden');
                    }
                });
            });
        });

        // --- Acervo ---

        function filterBooks() {
            const searchTerm = bookSearchInput.value.toLowerCase();
            const statusFilter = bookStatusFilter.value;
            const rows = acervoTbody.querySelectorAll('tr');

            rows.forEach(row => {
                const title = row.dataset.title.toLowerCase();
                const author = row.dataset.author.toLowerCase();
                const status = row.dataset.status;

                const searchMatch = title.includes(searchTerm) || author.includes(searchTerm);
                const statusMatch = statusFilter === 'todos' || status === statusFilter;

                if (searchMatch && statusMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        bookSearchInput.addEventListener('input', filterBooks);
        bookStatusFilter.addEventListener('change', filterBooks);

        document.querySelectorAll('.add-book-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                bookModalTitle.textContent = 'Adicionar Novo Livro';
                bookForm.reset();
                bookIdInput.value = '';
                populateGenreTags(''); // Limpa os gêneros
                bookPhotoPreview.src = 'https://placehold.co/150x200/e2e8f0/94a3b8?text=Capa';
                window.ModalManager.open(addEditBookModal);
            });
        });

        acervoTbody.addEventListener('click', (e) => {
            const editBtn = e.target.closest('.edit-book-btn');
            const deleteBtn = e.target.closest('.delete-book-btn');

            if (!editBtn && !deleteBtn) return;

            const row = e.target.closest('tr');
            const title = row.dataset.title;
            const confirmationTitle = document.getElementById('confirmation-title');
            const confirmationMessage = document.getElementById('confirmation-message');
            const confirmActionBtn = document.getElementById('confirm-action-btn');

            if (editBtn) {
                bookModalTitle.textContent = 'Editar Livro';
                bookIdInput.value = row.dataset.bookId;
                bookTitleInput.value = row.dataset.title;
                addEditBookModal.querySelector('#book-subtitle').value = row.dataset.subtitle;
                bookAuthorInput.value = row.dataset.author;
                addEditBookModal.querySelector('#book-publisher').value = row.dataset.publisher;
                populateGenreTags(row.dataset.genre);
                bookQtyInput.value = row.dataset.qty;
                bookPagesInput.value = row.dataset.pages;
                bookPubdateInput.value = row.dataset.pubdate; // Populate publication date
                bookPhotoPreview.src = row.dataset.photoUrl ||
                    'https://placehold.co/150x200/e2e8f0/94a3b8?text=Capa';
                window.ModalManager.open(addEditBookModal);
            }

            if (deleteBtn) {
                const form = document.getElementById('confirmation-form');
                confirmationTitle.textContent = 'Excluir Livro';
                confirmationMessage.innerHTML = `Tem certeza que deseja excluir o livro <strong>${title}</strong>? Esta ação não pode ser desfeita.`;

                confirmActionBtn.querySelector('span').textContent = 'Excluir';
                confirmActionBtn.classList.remove('btn-verde', 'btn-roxo', 'btn-cinza');
                confirmActionBtn.classList.add('btn-vermelho');

                form.querySelector('#confirmation-sub-action').value = 'excluir_livro';
                form.querySelector('#confirmation-id').value = row.dataset.bookId;

                window.ModalManager.open(confirmationModal);
            }
        });

        // --- Empréstimos ---
        function filterLoans() {
            const studentSearchTerm = studentSearchInput.value.toLowerCase();
            const dateFilterValue = loanDateFilter.value;

            const rows = loansTable.querySelectorAll('tbody tr.loan-row');
            rows.forEach(row => {
                const studentName = row.querySelector('.student-name').textContent.toLowerCase();
                const returnDateText = row.querySelector('td:nth-child(3)').textContent;


                const [day, month, year] = returnDateText.split('/');
                const formattedReturnDate = `${year}-${month}-${day}`;

                const studentMatch = studentName.includes(studentSearchTerm);
                const dateMatch = !dateFilterValue || formattedReturnDate === dateFilterValue;

                if (studentMatch && dateMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        studentSearchInput.addEventListener('input', filterLoans);
        loanDateFilter.addEventListener('input', filterLoans);

        loansTable.addEventListener('click', (e) => {
            const row = e.target.closest('tr');
            if (!row) return;

            const returnBtn = e.target.closest('.return-book-btn');
            const extendBtn = e.target.closest('.extend-loan-btn');

            if (!returnBtn && !extendBtn) return;

            const bookTitle = row.querySelector('td:first-child').textContent;
            const studentName = row.querySelector('.student-name').textContent;

            const confirmationTitle = document.getElementById('confirmation-title');
            const confirmationMessage = document.getElementById('confirmation-message');
            const confirmActionBtn = document.getElementById('confirm-action-btn');
            const form = document.getElementById('confirmation-form');

            if (returnBtn) {
                confirmationTitle.textContent = 'Confirmar Devolução';
                confirmationMessage.innerHTML = `Confirmar a devolução do livro <strong>${bookTitle}</strong> por <strong>${studentName}</strong>?`;

                confirmActionBtn.querySelector('span').textContent = 'Confirmar Devolução';
                confirmActionBtn.classList.remove('btn-verde', 'btn-vermelho', 'btn-cinza', 'btn-azul');
                confirmActionBtn.classList.add('btn-roxo');

                form.querySelector('#confirmation-sub-action').value = 'devolver_livro';
            } else if (extendBtn) {
                const bookTitle = row.querySelector('td:first-child').textContent;
                const studentName = row.querySelector('.student-name').textContent;

                const confirmationTitle = document.getElementById('confirmation-title');
                const confirmationMessage = document.getElementById('confirmation-message');
                const confirmActionBtn = document.getElementById('confirm-action-btn');

                confirmationTitle.textContent = 'Estender Prazo';
                confirmationMessage.innerHTML = `Deseja estender o prazo de devolução do livro <strong>${bookTitle}</strong> por mais 15 dias?`;

                confirmActionBtn.querySelector('span').textContent = 'Confirmar Extensão';
                confirmActionBtn.classList.remove('btn-verde', 'btn-vermelho', 'btn-roxo', 'btn-cinza');
                confirmActionBtn.classList.add('btn-azul');

                form.querySelector('#confirmation-sub-action').value = 'estender_emprestimo';
            }

            form.querySelector('#confirmation-id').value = row.dataset.loanId;
            window.ModalManager.open(confirmationModal);
        });

        // --- Reservas ---
        function filterReservations() {
            if (!reservationSearchInput || !reservationsList || !noReservationsMsg) return;

            const searchTerm = reservationSearchInput.value.toLowerCase();
            const items = reservationsList.querySelectorAll('.reservation-item');
            let visibleCount = 0;

            items.forEach(item => {
                const studentName = item.dataset.studentName.toLowerCase();
                const bookTitle = item.dataset.bookTitle.toLowerCase();

                if (studentName.includes(searchTerm) || bookTitle.includes(searchTerm)) {
                    item.style.display = 'flex';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (items.length > 0) {
                noReservationsMsg.classList.toggle('hidden', visibleCount > 0);
                noReservationsMsg.textContent = 'Nenhuma reserva encontrada para a sua busca.';
            } else {
                noReservationsMsg.classList.remove('hidden');
                noReservationsMsg.textContent = 'Nenhuma reserva aguardando retirada.';
            }
        }
        if (reservationSearchInput) {
            reservationSearchInput.addEventListener('input', filterReservations);
        }

        reservationsList.addEventListener('click', (e) => {
            const requestBtn = e.target.closest('.request-btn');
            if (!requestBtn) return;

            const reservationCard = e.target.closest('.reservation-item');
            const bookTitle = reservationCard.querySelector('p.font-bold').textContent;
            const studentName = reservationCard.querySelector('p.text-sm').textContent.replace('Pedido por: ', '');

            const confirmationTitle = document.getElementById('confirmation-title');
            const confirmationMessage = document.getElementById('confirmation-message');
            const confirmActionBtn = document.getElementById('confirm-action-btn');

            const form = document.getElementById('confirmation-form');
            const isApproval = requestBtn.classList.contains('approve');

            confirmationTitle.textContent = isApproval ? 'Confirmar Retirada' : 'Cancelar Reserva';
            confirmationMessage.innerHTML = `Deseja ${isApproval ? 'confirmar a retirada e iniciar o empréstimo' : 'cancelar a reserva'} do livro <strong>${bookTitle}</strong> para <strong>${studentName}</strong>?`;

            if (isApproval) {
                confirmActionBtn.querySelector('span').textContent = 'Confirmar';
                confirmActionBtn.classList.remove('btn-vermelho', 'btn-roxo', 'btn-cinza');
                confirmActionBtn.classList.add('btn-verde');
                form.querySelector('#confirmation-sub-action').value = 'aprovar_reserva';
            } else {
                confirmActionBtn.querySelector('span').textContent = 'Cancelar Reserva';
                confirmActionBtn.classList.remove('btn-verde', 'btn-roxo', 'btn-cinza');
                confirmActionBtn.classList.add('btn-vermelho');
                form.querySelector('#confirmation-sub-action').value = 'recusar_reserva';
            }

            form.querySelector('#confirmation-id').value = reservationCard.dataset.reservationId;
            window.ModalManager.open(confirmationModal);
        });


        bookPhotoInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    bookPhotoPreview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });


        const updateHiddenGenreInput = () => {
            const tags = genreTagsContainer.querySelectorAll('.genre-tag .tag-text');
            const genreNames = Array.from(tags).map(tag => tag.textContent);
            hiddenGenreInput.value = genreNames.join(',');
        };

        const createGenreTag = (genreName) => {
            genreName = genreName.trim();
            if (!genreName) return;

            const existingTags = Array.from(genreTagsContainer.querySelectorAll('.genre-tag .tag-text')).map(t => t.textContent.toLowerCase());
            if (existingTags.includes(genreName.toLowerCase())) {
                genreInput.value = '';
                return;
            }

            const tag = document.createElement('div');
            tag.className = 'genre-tag flex items-center bg-purple-100 text-purple-800 text-sm font-semibold px-2 py-1 rounded-full';
            tag.innerHTML = `
                <span class="tag-text">${genreName}</span>
                <button type="button" class="remove-tag-btn ml-2 text-purple-600 hover:text-purple-900 font-bold">&times;</button>
            `;

            genreTagsContainer.insertBefore(tag, genreInput);
            updateHiddenGenreInput();
            genreInput.value = '';
        };

        genreInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                createGenreTag(genreInput.value);
                genreAutocompleteResults.classList.add('hidden');
            }
            if (e.key === 'Backspace' && genreInput.value === '') {
                const lastTag = genreTagsContainer.querySelector('.genre-tag:last-of-type');
                if (lastTag) {
                    lastTag.remove();
                    updateHiddenGenreInput();
                }
            }
        });

        genreTagsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-tag-btn')) {
                e.target.parentElement.remove();
                updateHiddenGenreInput();
            }
        });

        const populateGenreTags = (genresString) => {
            genreTagsContainer.querySelectorAll('.genre-tag').forEach(tag => tag.remove());
            if (genresString) {
                const genres = genresString.split(',').map(g => g.trim());
                genres.forEach(genre => {
                    if (genre) createGenreTag(genre);
                });
            }
            updateHiddenGenreInput();
        };

        const genreSearchAction = genreInput.dataset.searchAction;
        let genreDebounceTimer;

        genreInput.addEventListener('input', () => {
            clearTimeout(genreDebounceTimer);
            const term = genreInput.value.trim();

            if (term.length < 2) {
                genreAutocompleteResults.innerHTML = '';
                genreAutocompleteResults.classList.add('hidden');
                return;
            }

            genreDebounceTimer = setTimeout(() => {
                fetch(`api.php?action=${genreSearchAction}&term=${encodeURIComponent(term)}`)
                    .then(response => response.json())
                    .then(data => {
                        let html = data.map(item => `<div class="autocomplete-item p-2 hover:bg-slate-100 cursor-pointer text-sm">${item.nome}</div>`).join('');
                        genreAutocompleteResults.innerHTML = html;
                        genreAutocompleteResults.classList.remove('hidden');
                    }).catch(error => console.error('Erro no autocomplete de gênero:', error));
            }, 300);
        });

        genreAutocompleteResults.addEventListener('click', (e) => {
            const itemEl = e.target.closest('.autocomplete-item');
            if (itemEl) {
                createGenreTag(itemEl.textContent);
                genreAutocompleteResults.classList.add('hidden');
                genreInput.focus();
            }
        });

        // --- Lógica AJAX para Ações (Devolver, Estender, Excluir, etc.) ---

        const showAjaxFeedback = (message, type = 'success') => {
            // Remove qualquer mensagem de feedback existente da página (a que vem via URL)
            const urlFeedback = document.querySelector('.bg-green-100, .bg-red-100');
            if (urlFeedback) urlFeedback.remove();

            const feedbackDiv = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-100 border-green-200 text-green-800' : 'bg-red-100 border-red-200 text-red-800';
            feedbackDiv.className = `${bgColor} border px-4 py-3 rounded-lg relative mb-4`;
            feedbackDiv.setAttribute('role', 'alert');
            feedbackDiv.innerHTML = `<span class="block sm:inline">${message}</span>`;

            // Insere a mensagem no topo do conteúdo da aba ativa
            const activeTabContent = document.querySelector('.library-tab-content:not(.hidden)');
            activeTabContent.insertBefore(feedbackDiv, activeTabContent.firstChild);

            // Remove a mensagem após alguns segundos
            setTimeout(() => {
                feedbackDiv.style.transition = 'opacity 0.5s ease';
                feedbackDiv.style.opacity = '0';
                setTimeout(() => feedbackDiv.remove(), 500);
            }, 5000);
        };

        confirmationForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const confirmBtn = this.querySelector('#confirm-action-btn');
            const originalBtnHTML = confirmBtn.innerHTML;
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-2"></i> Processando...`;

            const formData = new FormData(this);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    showAjaxFeedback(data.message, data.success ? 'success' : 'error');

                    if (data.success) {
                        const subAction = formData.get('sub_action');
                        const id = formData.get('id');

                        let elementToRemove;
                        if (subAction === 'devolver_livro') {
                            elementToRemove = loansTable.querySelector(`tr[data-loan-id="${id}"]`);
                        } else if (subAction === 'estender_emprestimo') {
                            const row = loansTable.querySelector(`tr[data-loan-id="${id}"]`);
                            if (row && data.new_date) {
                                const dateCell = row.querySelector('td:nth-child(3)');
                                dateCell.textContent = data.new_date;
                                row.style.transition = 'background-color 0.2s ease-in-out';
                                row.style.backgroundColor = '#f0fdf4';
                                setTimeout(() => {
                                    row.style.backgroundColor = '';
                                }, 1000);
                            }
                        } else if (subAction === 'aprovar_reserva' || subAction === 'recusar_reserva') {
                            elementToRemove = reservationsList.querySelector(`div[data-reservation-id="${id}"]`);
                        } else if (subAction === 'excluir_livro') {
                            elementToRemove = acervoTbody.querySelector(`tr[data-book-id="${id}"]`);
                        }

                        if (elementToRemove) {
                            elementToRemove.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            elementToRemove.style.opacity = '0';
                            elementToRemove.style.transform = 'translateX(-20px)';
                            setTimeout(() => elementToRemove.remove(), 300);
                        }
                    }
                })
                .catch(error => console.error('Erro na ação:', error))
                .finally(() => {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = originalBtnHTML;
                    window.ModalManager.close(confirmationModal);
                });
        });

        if (manualLoanForm) {
            manualLoanForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnHTML = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-2"></i> Processando...`;

                const formData = new FormData(this);

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        showAjaxFeedback(data.message, data.success ? 'success' : 'error');

                        if (data.success && data.newLoan) {
                            window.ModalManager.close(manualLoanModal);

                            // 1. Adicionar nova linha na tabela de empréstimos
                            const newLoan = data.newLoan;
                            const formattedDate = new Date(newLoan.dataDevolucaoPrevista + 'T00:00:00').toLocaleDateString('pt-BR');

                            const newRow = document.createElement('tr');
                            newRow.className = 'loan-row';
                            newRow.dataset.loanId = newLoan.id;
                            newRow.innerHTML = `
                            <td class="p-4 font-medium">${newLoan.livroTitulo}</td>
                            <td class="p-4 text-slate-600 student-name">${newLoan.usuarioNome}</td>
                            <td class="p-4 text-slate-600">${formattedDate}</td>
                            <td class="p-4 flex items-center justify-end gap-2">
                                <button type="button" class="btn btn-roxo return-book-btn !text-sm" style="height: 36px;"><span>Devolver</span></button>
                                <button type="button" class="btn btn-azul extend-loan-btn !text-sm" style="height: 36px;"><span>Estender</span></button>
                            </td>
                        `;
                            loansTable.querySelector('tbody').prepend(newRow);
                            newRow.style.backgroundColor = '#f0fdf4';
                            setTimeout(() => { newRow.style.backgroundColor = ''; }, 2000);

                            // 2. Atualizar a contagem de livros no acervo
                            const bookRowAcervo = acervoTbody.querySelector(`tr[data-book-id="${data.livroId}"]`);
                            if (bookRowAcervo) {
                                const availableCell = bookRowAcervo.querySelector('td:nth-child(6)');
                                const currentQty = parseInt(availableCell.textContent, 10);
                                const newQty = currentQty - 1;
                                availableCell.textContent = newQty;

                                if (newQty <= 0) {
                                    const statusCell = bookRowAcervo.querySelector('td:nth-child(8)');
                                    statusCell.innerHTML = `<span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Indisponível</span>`;
                                    bookRowAcervo.dataset.status = 'indisponivel';
                                }
                            }

                            // 3. Remover a opção do livro do modal de empréstimo manual
                            const bookOption = manualLoanModal.querySelector(`#loan-book-select option[value="${data.livroId}"]`);
                            if (bookOption) {
                                bookOption.remove();
                            }
                        }
                    })
                    .catch(error => console.error('Erro no empréstimo manual:', error))
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHTML;
                    });
            });
        }

        const setupAutocomplete = (inputElement) => {
            const resultsContainer = inputElement.nextElementSibling;
            const searchAction = inputElement.dataset.searchAction;
            let debounceTimer;

            inputElement.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                const term = inputElement.value.trim();

                if (term.length < 2) {
                    resultsContainer.innerHTML = '';
                    resultsContainer.classList.add('hidden');
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`api.php?action=${searchAction}&term=${encodeURIComponent(term)}`)
                        .then(response => response.json())
                        .then(data => {
                            let html = '';
                            data.forEach(item => {
                                html += `<div class="autocomplete-item p-2 hover:bg-slate-100 cursor-pointer text-sm">${item.nome}</div>`;
                            });
                            resultsContainer.innerHTML = html;
                            resultsContainer.classList.toggle('hidden', data.length === 0);
                        })
                        .catch(error => console.error('Erro no autocomplete:', error));
                }, 300);
            });

            resultsContainer.addEventListener('click', (e) => {
                const itemEl = e.target.closest('.autocomplete-item');

                if (itemEl) {
                    inputElement.value = itemEl.textContent;
                    resultsContainer.classList.add('hidden');
                }
            });
        };

        document.querySelectorAll('#book-author, #book-publisher').forEach(setupAutocomplete);

        document.addEventListener('click', (e) => {
            const isAutocompleteClick = e.target.closest('.relative') && e.target.closest('.relative').querySelector('.autocomplete-input');
            if (!isAutocompleteClick) {
                document.querySelectorAll('.autocomplete-results').forEach(container => {
                    container.classList.add('hidden');
                });
            }
        });

        if (manualLoanModal) {
            document.querySelectorAll('.manual-loan-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const form = manualLoanModal.querySelector('form');
                    form.reset();

                    const returnDateInput = document.getElementById('loan-return-date');
                    const today = new Date();
                    today.setDate(today.getDate() + 15);
                    returnDateInput.valueAsDate = today;
                    window.ModalManager.open(manualLoanModal);
                });
            });
        }
    });
</script>