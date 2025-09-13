<?php
require_once BASE_PATH . 'core/services/BibliotecaService.php';
require_once BASE_PATH . 'widgets/modal/Modal.php';

$bibliotecaService = new BibliotecaService();
$feedbackMessage = '';
$feedbackType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $action = $_POST['action'] ?? '';

    if ($action === 'salvar_livro')
    {
        // Busca o livro existente para manter a URL da foto se nenhuma nova for enviada
        $urlFotoExistente = null;
        if (!empty($_POST['book-id']))
        {
            $livroExistente = $bibliotecaService->buscarLivroPorId((int)$_POST['book-id']);
            if ($livroExistente)
            {
                $urlFotoExistente = $livroExistente->url_foto;
            }
        }

        $livroDTO = new LivroDTO(
            empty($_POST['book-id']) ? null : (int)$_POST['book-id'], // id
            $_POST['book-title'] ?? '',                               // titulo
            $_POST['book-subtitle'] ?? null,                          // subtitulo
            empty($_POST['book-pages']) ? null : (int)$_POST['book-pages'], // num_paginas
            $urlFotoExistente,                                        // url_foto
            (int)($_POST['book-qty'] ?? 1),                           // qtde_total
            (int)($_POST['book-qty'] ?? 1),                           // qtde_disponivel
            0,                                                        // qtde_reservada
            null                                                      // id_editora (definido abaixo)
        );
        $livroDTO->data_publicacao = $_POST['book-pubdate'] ?? null; // Adiciona a data de publicação

        $foto = isset($_FILES['book-photo']) && $_FILES['book-photo']['error'] === UPLOAD_ERR_OK
            ? $_FILES['book-photo']
            : null;

        // --- Resolver IDs usando os Services ---
        require_once BASE_PATH . 'core/services/AutorService.php';
        require_once BASE_PATH . 'core/services/EditoraService.php';
        require_once BASE_PATH . 'core/services/GeneroService.php';

        $autorNome = $_POST['book-author'] ?? '';
        $editoraNome = $_POST['book-publisher'] ?? '';
        $generoNomesStr = $_POST['book-genre'] ?? ''; // "Aventura, Ficção"

        $autorService = new AutorService();
        $editoraService = new EditoraService();
        $generoService = new GeneroService();

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
    }

    if ($action === 'confirmar_acao')
    {
        $sub_action = $_POST['sub_action'];
        $id = (int)$_POST['id'];
        $errorMessage = '';

        switch ($sub_action)
        {
            case 'excluir_livro':
                if ($bibliotecaService->excluirLivro($id, $errorMessage))
                {
                    $feedbackMessage = "Livro excluído com sucesso!";
                }
                else
                {
                    $feedbackMessage = $errorMessage ?: "Erro ao excluir livro.";
                    $feedbackType = 'error';
                }
                break;
            case 'devolver_livro':
                $feedbackMessage = $bibliotecaService->devolverLivro($id) ? "Devolução confirmada!" : "Erro ao confirmar devolução.";
                break;
            case 'aprovar_reserva':
                $feedbackMessage = $bibliotecaService->aprovarReserva($id) ? "Reserva aprovada!" : "Erro ao aprovar reserva.";
                break;
            case 'recusar_reserva':
                $feedbackMessage = $bibliotecaService->recusarReserva($id) ? "Reserva recusada." : "Erro ao recusar reserva.";
                break;
        }
    }

    $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=biblioteca&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
    header("Location: " . $redirectUrl);
    exit();
}



$livros = $bibliotecaService->buscarTodosLivros();
$emprestimos = $bibliotecaService->buscarEmprestimosAtivos();
$reservas = $bibliotecaService->buscarReservasPendentes();
$feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
?>
<!-- Conteúdo da Biblioteca -->
<div class="space-y-6">
    <!-- Abas de Navegação -->
    <div class="bg-white p-2 rounded-xl shadow-sm border border-slate-200">
        <nav class="flex space-x-2" id="library-tabs">
            <button data-tab="acervo"
                class="library-tab flex-1 text-center px-4 py-2 rounded-lg font-semibold text-sm transition bg-purple-600 text-white">Acervo</button>
            <button data-tab="emprestimos"
                class="library-tab flex-1 text-center px-4 py-2 rounded-lg font-semibold text-sm transition text-slate-600 hover:bg-slate-100">Empréstimos
                Ativos</button>
            <button data-tab="reservas"
                class="library-tab flex-1 text-center px-4 py-2 rounded-lg font-semibold text-sm transition text-slate-600 hover:bg-slate-100">Pedidos
                de Reserva</button>
        </nav>
    </div>

    <!-- Conteúdo das Abas -->
    <div id="acervo-content" class="library-tab-content space-y-4">
        <?php if ($feedbackMessage): ?>
                <div class="<?= $feedbackType === 'success' ? 'bg-green-100 border-green-200 text-green-800' : 'bg-red-100 border-red-200 text-red-800' ?> border px-4 py-3 rounded-lg relative"
                    role="alert">
                    <span class="block sm:inline"><?= $feedbackMessage ?></span>
                </div>
        <?php endif; ?>
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="text-xl font-semibold">Acervo da Biblioteca</h2>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative flex-grow">
                    <input id="book-search-input" type="text" placeholder="Pesquisar por título..."
                        class="pl-10 pr-4 py-2 border border-slate-300 rounded-full focus:ring-2 focus:ring-purple-400 w-full text-sm">
                    <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
                <?php // Botão para Desktop ?>
                <?php Botoes::getBotao('', 'Adicionar Livro', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-plus-circle', type: 'button', classes: 'add-book-btn hidden sm:inline-flex') ?>
            </div>
        </div>

        <?php // Botão Flutuante para Mobile ?>
        <?php Botoes::getBotao('', '', BotoesCores::VERDE, null, icone: 'fa-solid fa-plus text-xl', type: 'button', classes: 'add-book-btn sm:hidden fixed bottom-24 right-6 w-14 h-14 rounded-full shadow-xl z-20') ?>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-4 font-semibold text-sm w-20">Capa</th>
                        <th class="p-4 font-semibold text-sm">Título</th>
                        <th class="p-4 font-semibold text-sm">Autor</th>
                        <th class="p-4 font-semibold text-sm">Páginas</th>
                        <th class="p-4 font-semibold text-sm">Publicação</th>
                        <th class="p-4 font-semibold text-sm">Disponível</th>
                        <th class="p-4 font-semibold text-sm">Reservado</th>
                        <th class="p-4 font-semibold text-sm">Status</th>
                        <th class="p-4 font-semibold text-sm text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($livros as $livro): ?>
                            <tr data-book-id="<?= $livro->id ?>" data-title="<?= htmlspecialchars($livro->titulo) ?>"
                                data-subtitle="<?= htmlspecialchars($livro->subtitulo ?? '') ?>"
                                data-author="<?= htmlspecialchars(implode(', ', $livro->autores)) ?>"
                                data-publisher="<?= htmlspecialchars($livro->nome_editora ?? '') ?>"
                                data-genre="<?= htmlspecialchars(implode(', ', $livro->generos)) ?>"
                                data-pubdate="<?= htmlspecialchars($livro->data_publicacao ?? '') ?>"
                                data-qty="<?= $livro->qtde_total ?>" data-pages="<?= $livro->num_paginas ?? '' ?>"
                                data-photo-url="<?= htmlspecialchars($livro->url_foto ?? '') ?>">
                                <td class="p-2">
                                    <img src="<?= htmlspecialchars($livro->url_foto ?? 'https://placehold.co/50x70/e2e8f0/94a3b8?text=Capa') ?>"
                                        alt="Capa do livro <?= htmlspecialchars($livro->titulo) ?>"
                                        class="w-12 h-16 object-cover rounded-md shadow-sm mx-auto"
                                        onerror="this.onerror=null;this.src='https://placehold.co/50x70/e2e8f0/94a3b8?text=Capa';">
                                </td>
                                <td class="p-4 font-medium"><?= htmlspecialchars($livro->titulo) ?></td>
                                <td class="p-4 text-slate-600"><?= htmlspecialchars(implode(', ', $livro->autores)) ?></td>
                                <td class="p-4 text-slate-600"><?= $livro->num_paginas ?? 'N/A' ?></td>
                                <td class="p-4 text-slate-600">
                                    <?= $livro->data_publicacao ? (new DateTime($livro->data_publicacao))->format('d/m/Y') : 'N/A' ?>
                                </td>
                                <td class="p-4 text-slate-600"><?= $livro->qtde_disponivel ?></td>
                                <td class="p-4 text-slate-600"><?= $livro->qtde_reservada ?></td>
                                <td class="p-4">
                                    <?php if ($livro->qtde_disponivel > 0): ?>
                                            <span
                                                class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Disponível</span>
                                    <?php else: ?>
                                            <span
                                                class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Indisponível</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 flex items-center justify-end gap-2">
                                    <button
                                        class="edit-book-btn text-slate-500 hover:text-blue-600 p-2 rounded-lg hover:bg-slate-100"
                                        title="Editar"><i class="fa-solid fa-pencil"></i></button>
                                    <button
                                        class="delete-book-btn text-slate-500 hover:text-red-600 p-2 rounded-lg hover:bg-slate-100"
                                        title="Excluir"><i class="fa-solid fa-trash-can"></i></button>
                                </td>
                            </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>


    <div id="emprestimos-content" class="library-tab-content space-y-4 hidden">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="text-xl font-semibold">Empréstimos Ativos</h2>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative flex-grow sm:flex-grow-0">
                    <input id="loan-date-filter" type="date" title="Filtrar por data de devolução"
                        class="px-4 py-2 border border-slate-300 rounded-full focus:ring-2 focus:ring-purple-400 text-sm text-slate-600 w-full">
                </div>
                <div class="relative flex-grow">
                    <input id="student-search-input" type="text" placeholder="Pesquisar por aluno..."
                        class="pl-10 pr-4 py-2 border border-slate-300 rounded-full focus:ring-2 focus:ring-purple-400 w-full text-sm">
                    <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
            <table class="w-full text-left" id="loans-table">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-4 font-semibold text-sm">Livro</th>
                        <th class="p-4 font-semibold text-sm">Aluno</th>
                        <th class="p-4 font-semibold text-sm">Data de Devolução</th>
                        <th class="p-4 font-semibold text-sm text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($emprestimos as $emprestimo): ?>
                            <tr class="loan-row" data-loan-id="<?= $emprestimo->id ?>">
                                <td class="p-4 font-medium"><?= htmlspecialchars($emprestimo->livroTitulo) ?></td>
                                <td class="p-4 text-slate-600 student-name"><?= htmlspecialchars($emprestimo->usuarioNome) ?>
                                </td>
                                <td class="p-4 text-slate-600">
                                    <?= (new DateTime($emprestimo->dataDevolucaoPrevista))->format('d/m/Y') ?>
                                </td>
                                <td class="p-4 text-right">
                                    <?php Botoes::getBotao('', 'Devolver', BotoesCores::ROXO, null, altura: 36, classes: 'return-book-btn !text-sm', type: 'button') ?>
                                </td>
                            </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="reservas-content" class="library-tab-content space-y-4 hidden">
        <h2 class="text-xl font-semibold">Pedidos de Reserva Pendentes</h2>
        <div class="space-y-4">
            <?php if (empty($reservas)): ?>
                    <p class="text-center text-slate-500 py-4">Nenhum pedido de reserva pendente.</p>
            <?php else: ?>
                    <?php foreach ($reservas as $reserva): ?>
                            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex items-center justify-between"
                                data-reservation-id="<?= $reserva->id ?>">
                                <div>
                                    <p class="font-bold"><?= htmlspecialchars($reserva->livroTitulo) ?></p>
                                    <p class="text-sm text-slate-500">Pedido por: <?= htmlspecialchars($reserva->usuarioNome) ?></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button class="request-btn refuse bg-red-100 text-red-700 p-2 rounded-lg hover:bg-red-200"
                                        title="Recusar"><i class="fa-solid fa-trash-can"></i></button>
                                    <button class="request-btn approve bg-green-100 text-green-700 p-2 rounded-lg hover:bg-green-200"
                                        title="Aprovar"><i class="fa-solid fa-check"></i></button>
                                </div>
                            </div>
                    <?php endforeach; ?>
            <?php endif; ?>
        </div>
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
    <div class="flex justify-end gap-3">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn') ?>
        <?php Botoes::getBotao('', 'Confirmar', BotoesCores::VERDE, 'confirm-action-btn', altura: 40, icone: 'fa-solid fa-check', type: 'submit') ?>
    </div>
</form>
<?php Modal::end(); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Elements ---
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
        const loansTable = document.getElementById('loans-table');
        const reservationsContainer = document.querySelector('#reservas-content .space-y-4');

        // --- Genre Tags Elements ---
        const genreInput = document.getElementById('book-genre-input');
        const genreTagsContainer = document.getElementById('genre-tags-container');
        const hiddenGenreInput = document.getElementById('book-genre');
        const genreAutocompleteResults = document.getElementById('genre-autocomplete-results');

        // --- Tab Switching ---
        libraryTabs.forEach(tab => {
            tab.addEventListener('click', () => {
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

        // --- Acervo (Collection) Logic ---

        // Book search filter
        bookSearchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const rows = acervoTbody.querySelectorAll('tr');
            rows.forEach(row => {
                const title = row.dataset.title.toLowerCase();
                row.style.display = title.includes(searchTerm) ? '' : 'none';
            });
        });

        // Open "Add Book" modal
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

        // Event delegation for Edit and Delete buttons
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

        // --- Empréstimos (Loans) Logic ---
        function filterLoans() {
            const studentSearchTerm = studentSearchInput.value.toLowerCase();
            const dateFilterValue = loanDateFilter.value; // Format: YYYY-MM-DD

            const rows = loansTable.querySelectorAll('tbody tr.loan-row');
            rows.forEach(row => {
                const studentName = row.querySelector('.student-name').textContent.toLowerCase();
                const returnDateText = row.querySelector('td:nth-child(3)').textContent; // Format: DD/MM/YYYY

                // Convert DD/MM/YYYY to YYYY-MM-DD for comparison
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
            const returnBtn = e.target.closest('.return-book-btn');
            if (returnBtn) {
                const row = e.target.closest('tr');
                const bookTitle = row.querySelector('td:first-child').textContent;
                const studentName = row.querySelector('.student-name').textContent;

                const confirmationTitle = document.getElementById('confirmation-title');
                const confirmationMessage = document.getElementById('confirmation-message');
                const confirmActionBtn = document.getElementById('confirm-action-btn');

                confirmationTitle.textContent = 'Confirmar Devolução';
                confirmationMessage.innerHTML = `Confirmar a devolução do livro <strong>${bookTitle}</strong> por <strong>${studentName}</strong>?`;

                confirmActionBtn.querySelector('span').textContent = 'Confirmar Devolução';
                confirmActionBtn.classList.remove('btn-verde', 'btn-vermelho', 'btn-cinza');
                confirmActionBtn.classList.add('btn-roxo');

                const form = document.getElementById('confirmation-form');
                form.querySelector('#confirmation-sub-action').value = 'devolver_livro';
                form.querySelector('#confirmation-id').value = row.dataset.loanId;
                window.ModalManager.open(confirmationModal);
            }
        });

        // --- Reservas (Reservations) Logic ---
        reservationsContainer.addEventListener('click', (e) => {
            const requestBtn = e.target.closest('.request-btn');
            if (!requestBtn) return;

            const reservationCard = e.target.closest('.flex.items-center.justify-between');
            const bookTitle = reservationCard.querySelector('p.font-bold').textContent;
            const studentName = reservationCard.querySelector('p.text-sm').textContent.replace('Pedido por: ', '');

            const confirmationTitle = document.getElementById('confirmation-title');
            const confirmationMessage = document.getElementById('confirmation-message');
            const confirmActionBtn = document.getElementById('confirm-action-btn');

            const form = document.getElementById('confirmation-form');
            const isApproval = requestBtn.classList.contains('approve');

            confirmationTitle.textContent = isApproval ? 'Aprovar Reserva' : 'Recusar Reserva';
            confirmationMessage.innerHTML = `Deseja ${isApproval ? 'aprovar' : 'recusar'} a reserva do livro <strong>${bookTitle}</strong> para <strong>${studentName}</strong>?`;

            if (isApproval) {
                confirmActionBtn.querySelector('span').textContent = 'Aprovar';
                confirmActionBtn.classList.remove('btn-vermelho', 'btn-roxo', 'btn-cinza');
                confirmActionBtn.classList.add('btn-verde');
                form.querySelector('#confirmation-sub-action').value = 'aprovar_reserva';
            } else {
                confirmActionBtn.querySelector('span').textContent = 'Recusar';
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

        // --- Genre Tags Logic ---
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

        // --- Autocomplete for Genres (specific implementation) ---
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

        // --- Autocomplete Logic ---
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

            // Use event delegation on the container for handling clicks
            resultsContainer.addEventListener('click', (e) => {
                const itemEl = e.target.closest('.autocomplete-item');

                if (itemEl) {
                    inputElement.value = itemEl.textContent;
                    resultsContainer.classList.add('hidden');
                }
            });
        };

        // Setup autocomplete for author and publisher only
        document.querySelectorAll('#book-author, #book-publisher').forEach(setupAutocomplete);

        // Close autocomplete when clicking outside
        document.addEventListener('click', (e) => {
            const isAutocompleteClick = e.target.closest('.relative') && e.target.closest('.relative').querySelector('.autocomplete-input');
            if (!isAutocompleteClick) {
                document.querySelectorAll('.autocomplete-results').forEach(container => {
                    container.classList.add('hidden');
                });
            }
        });
    });
</script>