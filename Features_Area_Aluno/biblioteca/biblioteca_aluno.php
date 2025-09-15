<?php
// 1. INCLUDES E CONFIGURAÇÕES
// Inclui as classes e funções necessárias do projeto.
require_once BASE_PATH . 'core/services/BibliotecaService.php';
require_once BASE_PATH . 'core/services/AvaliacaoService.php';
require_once BASE_PATH . 'Utils/funcoesUtils.php';

// Adiciona o arquivo JavaScript específico da biblioteca que já existe no seu projeto.
FuncoesUtils::adicionarJs('Features_Area_Aluno/biblioteca/biblioteca.js');

// Helper function to format book data for JavaScript, matching what biblioteca.js expects
function format_book_for_js($book)
{
    // The service now returns stdClass, so we ensure the object structure is correct for JS.
    if (is_object($book))
    {
        $jsBook = new stdClass();
        $jsBook->id = $book->id ?? null;
        $jsBook->title = $book->titulo ?? 'Título não encontrado';
        $jsBook->img = $book->url_foto ?? null;
        $jsBook->author = implode(', ', $book->autores ?? []);
        $jsBook->genre = implode(', ', $book->generos ?? []);
        $jsBook->publisher = $book->nome_editora ?? 'Não informado';
        $jsBook->pubdate = $book->data_publicacao ? (new DateTime($book->data_publicacao))->format('Y') : 'Não informado';
        $jsBook->synopsis = $book->subtitulo ?? 'Sem sinopse.';
        $jsBook->available = $book->qtde_disponivel ?? 0;
        $jsBook->rating = $book->nota_media ?? 0;
        $jsBook->total_ratings = $book->total_avaliacoes ?? 0;
        $jsBook->my_rating = $book->minha_nota ?? null;
        return $jsBook;
    }
    return null; // Retorna nulo se não for um objeto válido
}

// 2. BUSCA DE DADOS
// Pega as informações do aluno logado a partir da sessão.
$aluno_id = $_SESSION['user_id'] ?? null;
$turma_id = $_SESSION['user_turma_id'] ?? null;

// Instancia os serviços que interagem com o banco de dados.
$bibliotecaService = new BibliotecaService();
$reservaService = new ReservaService();
$avaliacaoService = new AvaliacaoService();

// Busca e formata os dados dos livros para as seções.
$reservasEEmprestimos = $reservaService->buscarReservasEEmprestimosPorAluno($aluno_id);
$reservationsForJs = array_map(function ($item)
{
    $book = new stdClass();
    $book->id = $item->id;
    $book->title = $item->titulo;
    $book->img = $item->url_foto ?? null;
    $book->author = implode(', ', $item->autores);

    if ($item->status_reserva === 'Emprestado')
    {
        $book->status = 'devolver';
        $book->date = $item->data_devolucao_prevista ? (new DateTime($item->data_devolucao_prevista))->format('d/m/Y') : null;
    }
    elseif ($item->status_reserva === 'Aguardando Retirada')
    {
        $book->status = 'retirar';
        $book->date = $item->data_validade_reserva ? (new DateTime($item->data_validade_reserva))->format('d/m/Y') : null;
    }
    return $book;
}, array_filter($reservasEEmprestimos));

$libraryData = [
    'reservations' => $reservationsForJs,
    'required' => array_map('format_book_for_js', $bibliotecaService->buscarLeituraObrigatoriaPorTurma($turma_id)),
    'recommended' => array_map('format_book_for_js', $bibliotecaService->buscarRecomendados($aluno_id)),
    'all' => array_map('format_book_for_js', $bibliotecaService->buscarTodosLivros()),
    'read' => array_map('format_book_for_js', $bibliotecaService->buscarHistoricoDeLeituraPorAluno($aluno_id)),
    'pendingRatings' => $avaliacaoService->buscarAvaliacoesPendentesPorAluno($aluno_id)
];
?>

<!-- 3. INJEÇÃO DE DADOS PARA O JAVASCRIPT -->
<!-- O PHP prepara os dados e o json_encode os transforma em um objeto JavaScript que o 'biblioteca.js' irá usar. -->
<script>
    // Garante que o objeto studentData global exista antes de adicionar propriedades a ele.
    var studentData = studentData || {};
    // Adiciona os dados da biblioteca para serem acessados pelo script 'biblioteca.js'.
    studentData.library = <?= json_encode($libraryData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;
</script>

<!-- Estilos customizados para o carrossel -->
<style>
    /* Esconde a barra de rolagem do carrossel */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }
</style>

<!-- 4. ESTRUTURA HTML (com TailwindCSS) -->
<div id="biblioteca-content" class="space-y-6">
    <!-- Cabeçalho e Abas de Categoria -->
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-slate-200">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-4">Biblioteca</h1>
        <div id="library-category-tabs"
            class="border-b border-slate-200 flex items-center space-x-2 sm:space-x-4 overflow-x-auto pb-2">
            <button data-category="all"
                class="library-category-tab whitespace-nowrap px-3 py-2 text-sm font-medium rounded-md text-slate-600 hover:bg-slate-100 transition-colors">
                Acervo Completo
            </button>
            <button data-category="reservations"
                class="library-category-tab whitespace-nowrap px-3 py-2 text-sm font-medium rounded-md text-slate-600 hover:bg-slate-100 transition-colors">
                Minhas Reservas
            </button>
            <button data-category="required"
                class="library-category-tab whitespace-nowrap px-3 py-2 text-sm font-medium rounded-md text-slate-600 hover:bg-slate-100 transition-colors">
                Leitura Obrigatória
            </button>
            <button data-category="read"
                class="library-category-tab whitespace-nowrap px-3 py-2 text-sm font-medium rounded-md text-slate-600 hover:bg-slate-100 transition-colors">
                Livros Lidos
            </button>
        </div>
    </div>

    <!-- Conteúdo das Abas (Carrosséis) -->
    <div id="library-carousel-content">
        <!-- Seção: Minhas Reservas e Empréstimos -->
        <section id="my-reservations-section" data-category="reservations" class="library-carousel-section hidden">
            <h2 class="text-2xl font-bold text-slate-800 mb-2">Minhas Reservas e Empréstimos</h2>
            <div class="relative group">
                <div
                    class="carousel-container flex items-stretch gap-4 overflow-x-auto scroll-smooth py-4 scrollbar-hide">
                </div>
                <div class="empty-carousel-msg hidden text-center text-slate-500 py-8">Nenhuma reserva ou empréstimo
                    ativo.</div>
                <button
                    class="carousel-btn-prev absolute top-1/2 left-0 transform -translate-y-1/2 bg-white bg-opacity-75 hover:bg-opacity-100 rounded-full p-2 shadow-md hidden md:block opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><i
                        class="fa-solid fa-chevron-left text-slate-700 h-6 w-6"></i></button>
                <button
                    class="carousel-btn-next absolute top-1/2 right-0 transform -translate-y-1/2 bg-white bg-opacity-75 hover:bg-opacity-100 rounded-full p-2 shadow-md hidden md:block opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><i
                        class="fa-solid fa-chevron-right text-slate-700 h-6 w-6"></i></button>
            </div>
        </section>

        <!-- Seção: Leitura Obrigatória -->
        <section id="required-reading-section" data-category="required" class="library-carousel-section hidden">
            <h2 class="text-2xl font-bold text-slate-800 mb-2">Leitura Obrigatória</h2>
            <div class="relative group">
                <div
                    class="carousel-container flex items-stretch gap-4 overflow-x-auto scroll-smooth py-4 scrollbar-hide">
                </div>
                <div class="empty-carousel-msg hidden text-center text-slate-500 py-8">Nenhum livro de leitura
                    obrigatória para sua turma.</div>
                <button
                    class="carousel-btn-prev absolute top-1/2 left-0 transform -translate-y-1/2 bg-white bg-opacity-75 hover:bg-opacity-100 rounded-full p-2 shadow-md hidden md:block opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><i
                        class="fa-solid fa-chevron-left text-slate-700 h-6 w-6"></i></button>
                <button
                    class="carousel-btn-next absolute top-1/2 right-0 transform -translate-y-1/2 bg-white bg-opacity-75 hover:bg-opacity-100 rounded-full p-2 shadow-md hidden md:block opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><i
                        class="fa-solid fa-chevron-right text-slate-700 h-6 w-6"></i></button>
            </div>
        </section>

        <!-- Seção: Livros Lidos -->
        <section id="read-books-section" data-category="read" class="library-carousel-section hidden">
            <h2 class="text-2xl font-bold text-slate-800 mb-2">Seu Histórico de Leitura</h2>
            <div class="relative group">
                <div
                    class="carousel-container flex items-stretch gap-4 overflow-x-auto scroll-smooth py-4 scrollbar-hide">
                </div>
                <div class="empty-carousel-msg hidden text-center text-slate-500 py-8">Você ainda não concluiu a
                    leitura de nenhum livro.</div>
                <button
                    class="carousel-btn-prev absolute top-1/2 left-0 transform -translate-y-1/2 bg-white bg-opacity-75 hover:bg-opacity-100 rounded-full p-2 shadow-md hidden md:block opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><i
                        class="fa-solid fa-chevron-left text-slate-700 h-6 w-6"></i></button>
                <button
                    class="carousel-btn-next absolute top-1/2 right-0 transform -translate-y-1/2 bg-white bg-opacity-75 hover:bg-opacity-100 rounded-full p-2 shadow-md hidden md:block opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><i
                        class="fa-solid fa-chevron-right text-slate-700 h-6 w-6"></i></button>
            </div>
        </section>

        <!-- Seção: Todo o Acervo -->
        <section id="all-books-section" data-category="all" class="library-carousel-section hidden">
            <div class="grid grid-cols-1 lg:grid-cols-4 lg:gap-8">

                <!-- Coluna de Filtros (Esquerda) -->
                <aside class="lg:col-span-1">
                    <h3 class="text-xl font-bold text-slate-800 mb-4 hidden lg:block">Filtros</h3>
                    <div id="book-filters" class="bg-slate-50 p-4 rounded-lg border border-slate-200 space-y-4">
                        <div>
                            <label for="search-book-input"
                                class="block text-sm font-medium text-slate-700 mb-1">Pesquisar
                                por Título ou Autor</label>
                            <input type="text" id="search-book-input" placeholder="Ex: O Pequeno Príncipe"
                                class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="genre-filter-select"
                                class="block text-sm font-medium text-slate-700 mb-1">Filtrar
                                por Gênero</label>
                            <select id="genre-filter-select"
                                class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Todos os Gêneros</option>
                                <!-- Opções de gênero serão populadas via JavaScript -->
                            </select>
                        </div>
                    </div>
                </aside>

                <!-- Coluna de Conteúdo (Direita) -->
                <div class="lg:col-span-3 mt-8 lg:mt-0 space-y-12">
                    <!-- Seção: Recomendados para Você -->
                    <section id="recommended-section">
                        <h2 class="text-2xl font-bold text-slate-800 mb-2">Recomendados para Você</h2>
                        <div class="relative group">
                            <div
                                class="carousel-container flex items-stretch gap-4 overflow-x-auto scroll-smooth py-4 scrollbar-hide">
                            </div>
                            <div class="empty-carousel-msg hidden text-center text-slate-500 py-8">Nenhuma
                                recomendação para você no momento.</div>
                            <button
                                class="carousel-btn-prev absolute top-1/2 left-0 transform -translate-y-1/2 bg-white bg-opacity-75 hover:bg-opacity-100 rounded-full p-2 shadow-md hidden md:block opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><i
                                    class="fa-solid fa-chevron-left text-slate-700 h-6 w-6"></i></button>
                            <button
                                class="carousel-btn-next absolute top-1/2 right-0 transform -translate-y-1/2 bg-white bg-opacity-75 hover:bg-opacity-100 rounded-full p-2 shadow-md hidden md:block opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><i
                                    class="fa-solid fa-chevron-right text-slate-700 h-6 w-6"></i></button>
                        </div>
                    </section>

                    <!-- Container para a lista de "Todo o Acervo" -->
                    <div id="all-books-list-wrapper">
                        <h2 class="text-2xl font-bold text-slate-800 mb-2">Todo o Acervo</h2>
                        <div class="relative group">
                            <div
                                class="carousel-container flex items-stretch gap-4 overflow-x-auto scroll-smooth py-4 scrollbar-hide">
                            </div>
                            <div class="empty-carousel-msg hidden text-center text-slate-500 py-8">Nenhum livro
                                encontrado no acervo.</div>
                            <button
                                class="carousel-btn-prev absolute top-1/2 left-0 transform -translate-y-1/2 bg-white bg-opacity-75 hover:bg-opacity-100 rounded-full p-2 shadow-md hidden md:block opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><i
                                    class="fa-solid fa-chevron-left text-slate-700 h-6 w-6"></i></button>
                            <button
                                class="carousel-btn-next absolute top-1/2 right-0 transform -translate-y-1/2 bg-white bg-opacity-75 hover:bg-opacity-100 rounded-full p-2 shadow-md hidden md:block opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><i
                                    class="fa-solid fa-chevron-right text-slate-700 h-6 w-6"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Modal de Detalhes do Livro (controlado via JS) -->
<div id="book-modal"
    class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden items-center justify-center p-4 transition-opacity duration-300 opacity-0"
    data-current-book-id="">
    <div id="modal-content"
        class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col lg:flex-row overflow-hidden transform scale-95 transition-transform duration-300">
        <!-- Imagem do Livro -->
        <div class="w-full lg:w-1/3 flex-shrink-0">
            <img id="modal-img" src="" alt="Capa do livro" class="w-full h-64 lg:h-full object-cover">
        </div>

        <!-- Informações do Livro -->
        <div class="w-full lg:w-2/3 flex flex-col">
            <div class="p-6 overflow-y-auto flex-grow">
                <!-- Cabeçalho do Modal -->
                <div class="flex justify-between items-start mb-4">
                    <h2 id="modal-title" class="text-2xl font-bold text-slate-900"></h2>
                    <button id="close-modal-btn" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Detalhes -->
                <div class="space-y-4 text-slate-600">
                    <p><strong>Autor:</strong> <span id="modal-author"></span></p>
                    <p><strong>Gênero:</strong> <span id="modal-genre"></span></p>
                    <p><strong>Editora:</strong> <span id="modal-publisher"></span></p>
                    <p><strong>Ano de Publicação:</strong> <span id="modal-pubdate"></span></p>
                    <div>
                        <h4 class="font-semibold text-slate-800 mb-1">Sinopse</h4>
                        <p id="modal-synopsis" class="text-sm leading-relaxed"></p>
                    </div>
                </div>

                <!-- Seção de Avaliação -->
                <div id="modal-rating-section" class="p-6 pt-0">
                    <hr class="mb-4">
                    <h4 class="font-semibold text-slate-800 mb-3">Avaliações</h4>
                    <div class="flex items-center mb-4">
                        <div id="modal-avg-rating-stars" class="flex text-amber-400">
                            <!-- Estrelas da média serão inseridas aqui pelo JS -->
                        </div>
                        <span id="modal-avg-rating-text" class="ml-2 text-sm text-slate-500"></span>
                    </div>

                    <!-- Formulário para o usuário avaliar -->
                    <div id="modal-user-rating-form" class="hidden">
                        <h5 class="font-semibold text-slate-700 mb-2">Sua Avaliação</h5>
                        <div class="flex items-center space-x-1 text-2xl text-slate-300 mb-3" id="user-rating-stars">
                            <i class="fa-solid fa-star cursor-pointer" data-value="1"></i>
                            <i class="fa-solid fa-star cursor-pointer" data-value="2"></i>
                            <i class="fa-solid fa-star cursor-pointer" data-value="3"></i>
                            <i class="fa-solid fa-star cursor-pointer" data-value="4"></i>
                            <i class="fa-solid fa-star cursor-pointer" data-value="5"></i>
                        </div>
                        <textarea id="user-rating-comment"
                            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 text-sm"
                            rows="3" placeholder="Escreva um comentário (opcional)..."></textarea>
                        <button id="submit-rating-btn"
                            class="mt-3 w-full sm:w-auto bg-green-600 text-white font-bold py-2 px-4 rounded-md hover:bg-green-700 transition-colors duration-200 text-sm">
                            Enviar Avaliação
                        </button>
                    </div>
                    <div id="user-rating-prompt" class="text-sm text-slate-500 bg-slate-100 p-3 rounded-md">
                    </div>
                </div>
            </div>

            <!-- Rodapé do Modal -->
            <div
                class="p-6 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div id="modal-availability" class="font-semibold text-sm"></div>
                <button id="modal-reserve-btn"
                    class="w-full sm:w-auto px-6 py-2.5 text-sm font-bold rounded-lg shadow-md transition-transform transform hover:scale-105">
                    Reservar Livro
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Pop-up para Avaliação -->
<div id="rating-prompt-modal"
    class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden items-center justify-center p-4 transition-opacity duration-300 opacity-0">
    <div
        class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 text-center transform scale-95 transition-transform duration-300">
        <h3 class="text-xl font-bold text-slate-800 mb-2">Avalie sua leitura!</h3>
        <p id="rating-prompt-message" class="text-slate-600 mb-4"></p>

        <!-- Formulário de avaliação -->
        <div class="flex items-center justify-center space-x-1 text-3xl text-slate-300 mb-4" id="prompt-rating-stars">
            <i class="fa-solid fa-star cursor-pointer" data-value="1"></i>
            <i class="fa-solid fa-star cursor-pointer" data-value="2"></i>
            <i class="fa-solid fa-star cursor-pointer" data-value="3"></i>
            <i class="fa-solid fa-star cursor-pointer" data-value="4"></i>
            <i class="fa-solid fa-star cursor-pointer" data-value="5"></i>
        </div>
        <textarea id="prompt-rating-comment"
            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 text-sm mb-4"
            rows="3" placeholder="Deixe um comentário (opcional)..."></textarea>

        <div class="flex flex-col sm:flex-row gap-3">
            <button id="rating-prompt-dismiss-btn"
                class="w-full bg-slate-200 text-slate-700 font-bold py-2 px-6 rounded-md hover:bg-slate-300 transition-colors duration-200">Avaliar
                Depois</button>
            <button id="rating-prompt-submit-btn"
                class="w-full bg-purple-600 text-white font-bold py-2 px-6 rounded-md hover:bg-purple-700 transition-colors duration-200">Enviar
                Avaliação</button>
        </div>
    </div>
</div>

<!-- Modal de Informação Genérico -->
<div id="info-modal"
    class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden items-center justify-center p-4 transition-opacity duration-300 opacity-0">
    <div
        class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 text-center transform scale-95 transition-transform duration-300">
        <h3 id="info-modal-title" class="text-xl font-bold text-slate-800 mb-4"></h3>
        <p id="info-modal-message" class="text-slate-600 mb-6"></p>
        <button id="info-modal-close-btn"
            class="bg-purple-600 text-white font-bold py-2 px-6 rounded-md hover:bg-purple-700 transition-colors duration-200">OK</button>
    </div>
</div>