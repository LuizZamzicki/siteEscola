<?php
require_once BASE_PATH . 'core/services/BibliotecaService.php';
require_once BASE_PATH . 'core/services/GeneroService.php';
require_once BASE_PATH . 'core/services/AutorService.php';

FuncoesUtils::adicionarJs('Features_Area_Aluno/biblioteca/biblioteca.js');

$bibliotecaService = new BibliotecaService();
$generoService = new GeneroService();
$autorService = new AutorService();

$alunoId = $_SESSION['user_id'] ?? 0;
$turmaId = $_SESSION['user_turma_id'] ?? 0; // Assumindo que o ID da turma está na sessão
$turmaNome = $_SESSION['user_turma'] ?? 'sua turma';

// Buscar dados dos serviços
$reservas = $bibliotecaService->buscarReservasEEmprestimosPorAluno($alunoId);
$leituraObrigatoria = $bibliotecaService->buscarLeituraObrigatoriaPorTurma($turmaId);
$recomendados = $bibliotecaService->buscarRecomendados($alunoId, 5); // Limita a 5, por exemplo
$acervoCompleto = $bibliotecaService->buscarTodosLivros();
$generos = $generoService->buscarTodos();
$autores = $autorService->buscarTodos();

// Função auxiliar para formatar os dados dos livros para o JavaScript
function formatarLivroParaJs($livro)
{
    $status = '';
    $date = '';

    // Lógica para livros que são reservas ou empréstimos
    if (isset($livro->status_reserva))
    { // Identifica se é um objeto de reserva/empréstimo
        if ($livro->status_reserva === 'Emprestado')
        {
            $status = 'devolver';
            $date = isset($livro->data_devolucao_prevista) ? (new DateTime($livro->data_devolucao_prevista))->format('d/m/Y') : '';
        }
        elseif ($livro->status_reserva === 'Aguardando Retirada')
        {
            $status = 'retirar';
            $date = isset($livro->data_validade_reserva) ? (new DateTime($livro->data_validade_reserva))->format('d/m/Y') : '';
        }
    }

    $autoresNomes = isset($livro->autores) && is_array($livro->autores) ? $livro->autores : [];
    $generosNomes = isset($livro->generos) && is_array($livro->generos) ? $livro->generos : [];

    return [
        'id' => $livro->id ?? $livro->id_livro ?? null,
        'title' => $livro->titulo ?? 'Título não disponível',
        'author' => implode(', ', $autoresNomes),
        'img' => $livro->url_foto ?? 'imagens/book-placeholder.png',
        'available' => $livro->qtde_disponivel ?? 0,
        'genre' => implode(', ', $generosNomes),
        'publisher' => $livro->nome_editora ?? 'Não informado',
        'pubdate' => isset($livro->data_publicacao) ? (new DateTime($livro->data_publicacao))->format('Y') : 'Não informado',
        'synopsis' => $livro->sinopse ?? 'Sinopse não disponível.',
        'status' => $status,
        'date' => $date,
    ];
}

$libraryData = [
    'reservations' => array_map('formatarLivroParaJs', $reservas),
    'required' => array_map('formatarLivroParaJs', $leituraObrigatoria),
    'recommended' => array_map('formatarLivroParaJs', $recomendados),
    'all' => array_map('formatarLivroParaJs', $acervoCompleto),
];
?>
<script>
    // Garante que o objeto global studentData exista antes de adicionar os dados da biblioteca.
    var studentData = window.studentData || {};
    studentData.library = <?= json_encode($libraryData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
</script>

<div id="biblioteca-content">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-8">
        <h2 class="text-2xl font-semibold">Explore o Acervo da Biblioteca</h2>
        <p class="text-slate-600 mt-1">Encontre sua próxima leitura! Veja suas reservas, os livros
            obrigatórios da sua turma e navegue por todo o nosso acervo.</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
        <!-- Coluna de Filtros -->
        <aside class="lg:col-span-1">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 sticky top-6 lg:mt-20">
                <h3 class="text-xl font-semibold mb-6">Filtrar Livros</h3>

                <div class="relative mb-6">
                    <input type="text" id="filter-title-input" name="filter-title" placeholder="Buscar por título..."
                        class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-full focus:ring-2 focus:ring-amber-400 focus:border-amber-400 text-sm"
                        autocomplete="off">
                    <i
                        class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                </div>

                <!-- Filtro por Gênero -->
                <div class="mb-6">
                    <h4 class="font-semibold mb-3">Gênero</h4>
                    <div class="space-y-2 text-sm max-h-48 overflow-y-auto pr-2">
                        <?php if (empty($generos)): ?>
                            <p class="text-slate-500">Nenhum gênero.</p>
                        <?php else: ?>
                            <?php foreach ($generos as $genero): ?>
                                <label class="flex items-center space-x-2 text-slate-600 hover:text-slate-800 cursor-pointer">
                                    <input type="checkbox" name="genre[]" value="<?= htmlspecialchars($genero['descricao']) ?>"
                                        class="rounded text-purple-600 focus:ring-purple-500">
                                    <span><?= htmlspecialchars($genero['descricao']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Filtro por Autor -->
                <div>
                    <h4 class="font-semibold mb-3">Autor</h4>
                    <select id="filter-author-select" name="filter-author"
                        class="w-full text-sm border-slate-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500"
                        autocomplete="off">
                        <option value="">Todos os Autores</option>
                        <?php if (empty($autores)): ?>
                            <option disabled>Nenhum autor encontrado</option>
                        <?php else: ?>
                            <?php foreach ($autores as $autor): ?>
                                <option value="<?= htmlspecialchars($autor['nome']) ?>"><?= htmlspecialchars($autor['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </aside>

        <!-- Conteúdo Principal da Biblioteca -->
        <main class="lg:col-span-3 space-y-8">
            <!-- Carrossel: Minhas Reservas -->
            <section id="my-reservations-section">
                <h3 class="text-2xl font-bold mb-4">Minhas Reservas</h3>
                <div class="carousel-wrapper relative group">
                    <div class="carousel-container overflow-x-auto flex gap-6 pb-4 no-scrollbar">
                        <!-- Livros reservados serão inseridos aqui pelo JS -->
                    </div>
                    <button
                        class="carousel-prev carousel-btn absolute top-1/2 -translate-y-1/2 -left-5 w-10 h-10 bg-white rounded-full shadow-lg items-center justify-center text-slate-600 hover:bg-slate-100 opacity-0 group-hover:opacity-100 transition-opacity z-10 hidden sm:flex"><i
                            class="fa-solid fa-chevron-left w-6 h-6"></i></button>
                    <button
                        class="carousel-next carousel-btn absolute top-1/2 -translate-y-1/2 -right-5 w-10 h-10 bg-white rounded-full shadow-lg items-center justify-center text-slate-600 hover:bg-slate-100 opacity-0 group-hover:opacity-100 transition-opacity z-10 hidden sm:flex"><i
                            class="fa-solid fa-chevron-right w-6 h-6"></i></button>
                </div>
            </section>

            <!-- Carrossel: Leitura da Turma -->
            <section id="required-reading-section">
                <h3 class="text-2xl font-bold mb-4">Leitura Obrigatória - <?= htmlspecialchars($turmaNome) ?></h3>
                <div class="carousel-wrapper relative group">
                    <div class="carousel-container overflow-x-auto flex gap-6 pb-4 no-scrollbar">
                        <!-- Livros obrigatórios serão inseridos aqui pelo JS -->
                    </div>
                    <button
                        class="carousel-prev carousel-btn absolute top-1/2 -translate-y-1/2 -left-5 w-10 h-10 bg-white rounded-full shadow-lg items-center justify-center text-slate-600 hover:bg-slate-100 opacity-0 group-hover:opacity-100 transition-opacity z-10 hidden sm:flex"><i
                            class="fa-solid fa-chevron-left w-6 h-6"></i></button>
                    <button
                        class="carousel-next carousel-btn absolute top-1/2 -translate-y-1/2 -right-5 w-10 h-10 bg-white rounded-full shadow-lg items-center justify-center text-slate-600 hover:bg-slate-100 opacity-0 group-hover:opacity-100 transition-opacity z-10 hidden sm:flex"><i
                            class="fa-solid fa-chevron-right w-6 h-6"></i></button>
                </div>
            </section>

            <!-- Carrossel: Livros Recomendados -->
            <section id="recommended-section">
                <h3 class="text-2xl font-bold mb-4">Recomendados para Você</h3>
                <div class="carousel-wrapper relative group">
                    <div class="carousel-container overflow-x-auto flex gap-6 pb-4 no-scrollbar">
                        <!-- Livros recomendados serão inseridos aqui pelo JS -->
                    </div>
                    <button
                        class="carousel-prev carousel-btn absolute top-1/2 -translate-y-1/2 -left-5 w-10 h-10 bg-white rounded-full shadow-lg items-center justify-center text-slate-600 hover:bg-slate-100 opacity-0 group-hover:opacity-100 transition-opacity z-10 hidden sm:flex"><i
                            class="fa-solid fa-chevron-left w-6 h-6"></i></button>
                    <button
                        class="carousel-next carousel-btn absolute top-1/2 -translate-y-1/2 -right-5 w-10 h-10 bg-white rounded-full shadow-lg items-center justify-center text-slate-600 hover:bg-slate-100 opacity-0 group-hover:opacity-100 transition-opacity z-10 hidden sm:flex"><i
                            class="fa-solid fa-chevron-right w-6 h-6"></i></button>
                </div>
            </section>

            <!-- Carrossel: Todo o Acervo -->
            <section id="all-books-section">
                <h3 class="text-2xl font-bold mb-4">Continue Navegando</h3>
                <div class="carousel-wrapper relative group">
                    <div class="carousel-container overflow-x-auto flex gap-6 pb-4 no-scrollbar">
                        <!-- Todos os livros do acervo serão carregados aqui pelo JavaScript -->
                    </div>
                    <button
                        class="carousel-prev carousel-btn absolute top-1/2 -translate-y-1/2 -left-5 w-10 h-10 bg-white rounded-full shadow-lg items-center justify-center text-slate-600 hover:bg-slate-100 opacity-0 group-hover:opacity-100 transition-opacity z-10 hidden sm:flex">
                        <i class="fa-solid fa-chevron-left w-6 h-6"></i>
                    </button>
                    <button
                        class="carousel-next carousel-btn absolute top-1/2 -translate-y-1/2 -right-5 w-10 h-10 bg-white rounded-full shadow-lg items-center justify-center text-slate-600 hover:bg-slate-100 opacity-0 group-hover:opacity-100 transition-opacity z-10 hidden sm:flex">
                        <i class="fa-solid fa-chevron-right w-6 h-6"></i>
                    </button>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal de Detalhes do Livro -->
    <?php Modal::begin('book-modal', null, '', 'max-w-4xl', 'z-50', 'p-0 flex flex-col md:flex-row max-h-[90vh] overflow-y-hidden rounded-2xl'); ?>
    <!-- Imagem do Livro -->
    <div class="w-full md:w-1/3 flex-shrink-0">
        <img id="modal-img" src="imagens/book-placeholder.png" alt="Capa do livro"
            class="w-full h-64 md:h-full object-cover rounded-t-2xl md:rounded-l-2xl md:rounded-tr-none">
    </div>

    <!-- Detalhes do Livro -->
    <div class="w-full md:w-2/3 p-6 sm:p-8 flex flex-col overflow-y-auto">
        <div class="flex justify-between items-start mb-4">
            <h3 id="modal-title" class="text-2xl sm:text-3xl font-bold text-slate-800 leading-tight">Título do
                Livro
            </h3>
            <button id="close-modal-btn"
                class="close-modal-btn p-2 -mr-2 -mt-2 rounded-full text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition">
                <i class="fa-solid fa-xmark text-2xl"></i>
            </button>
        </div>

        <div class="flex-grow space-y-4 text-slate-600">
            <p class="text-lg">por <span id="modal-author" class="font-semibold text-slate-700">Autor</span></p>
            <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div><strong>Gênero:</strong> <span id="modal-genre"></span></div>
                <div><strong>Editora:</strong> <span id="modal-publisher"></span></div>
                <div><strong>Publicado em:</strong> <span id="modal-pubdate"></span></div>
                <div class="flex items-center gap-2"><strong>Disponibilidade:</strong> <span
                        id="modal-availability"></span></div>
            </div>
            <div>
                <h4 class="font-semibold text-slate-800 mb-1">Sinopse</h4>
                <p id="modal-synopsis" class="text-sm leading-relaxed"></p>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-slate-200">
            <button id="modal-reserve-btn"
                class="w-full bg-[#F2C94C] text-slate-900 hover:bg-amber-400 font-bold py-3 px-4 rounded-lg transition-transform transform hover:scale-105">
                Reservar Livro
            </button>
        </div>
    </div>
    <?php Modal::end(); ?>
</div>