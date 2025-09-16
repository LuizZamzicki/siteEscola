
    /**
     * Exibe a página do ensino integral.
     */
    public function integral()
    {
        $faq_integral = include BASE_PATH . 'app/Views/Features_Site/integral/faq_integral.php';
        $itinerarios = include BASE_PATH . 'app/Views/Features_Site/itinerarios/itinerariosObj.php';
        $contato = '/siteEscola/contato';
        $link_desempenho_integral_pr = 'https://www.parana.pr.gov.br/aen/Noticia/Escolas-estaduais-em-tempo-integral-do-Parana-cresceram-acima-da-media-no-Ideb';
        View::render('site/integral', [
            'faq_integral' => $faq_integral,
            'itinerarios' => $itinerarios,
            'contato' => $contato,
            'link_desempenho_integral_pr' => $link_desempenho_integral_pr
        ]);
    }
<?php


use App\Models\FuncionarioService;
use Core\View;


class PaginasController {

    /**
     * Exibe a página de listagem de itinerários formativos.
     */
    public function itinerarios()
    {
        $itinerarios = include BASE_PATH . 'app/Views/Features_Site/itinerarios/itinerariosObj.php';
        // Definir categorias únicas para o filtro
        $categorias_disponiveis = [];
        foreach ($itinerarios as $itinerario) {
            if (!empty($itinerario['categorias'])) {
                foreach ($itinerario['categorias'] as $categoria) {
                    $categorias_disponiveis[$categoria] = $categoria;
                }
            }
        }
        ksort($categorias_disponiveis);
        $categoria_selecionada = $_GET['categoria'] ?? 'todos';
        $itinerarios_filtrados = $itinerarios;
        if ($categoria_selecionada !== 'todos') {
            $itinerarios_filtrados = array_filter($itinerarios, function ($itinerario) use ($categoria_selecionada) {
                return in_array($categoria_selecionada, $itinerario['categorias'] ?? []);
            });
        }
        $base_url_categoria = strtok($_SERVER["REQUEST_URI"], '?');
        View::render('site/itinerarios', [
            'itinerarios_filtrados' => $itinerarios_filtrados,
            'categorias_disponiveis' => $categorias_disponiveis,
            'categoria_selecionada' => $categoria_selecionada,
            'base_url_categoria' => $base_url_categoria
        ]);
    }

    /**
     * Exibe a página de estrutura da escola.
     */
    public function estrutura()
    {
        $estrutura_escola = include BASE_PATH . 'app/Views/Features_Site/estrutura/estruturaObj.php';
        View::render('site/estrutura', ['estrutura_escola' => $estrutura_escola]);
    }
    /**
     * Exibe a página de detalhes de um itinerário formativo.
     */
    public function detalheItinerario()
    {
        $todos_itinerarios_url = '/siteEscola/listar_itinerarios';
        $itinerarios = include BASE_PATH . 'app/Views/Features_Site/itinerarios/itinerariosObj.php';
        $itinerario_id = $_GET['titulo'] ?? null;
        $itinerario_selecionado = null;
        if ($itinerario_id)
        {
            foreach ($itinerarios as $item)
            {
                if ($item['titulo'] === $itinerario_id)
                {
                    $itinerario_selecionado = $item;
                    break;
                }
            }
        }
        if (!$itinerario_selecionado)
        {
            header('Location: ' . $todos_itinerarios_url);
            exit;
        }
        View::render('site/detalhe_itinerario', ['itinerario_selecionado' => $itinerario_selecionado]);
    }

    /**
     * Exibe a página inicial (home) do site.
     */
    public function home()
    {
        // Dados para os posts do Instagram (futuramente pode vir de um service)
        $instagram_posts = [
            ['imagem' => 'imagens/postInsta/post1.webp', 'link' => 'https://www.instagram.com/p/DLKk-PkuQnj/'],
            ['imagem' => 'imagens/postInsta/post2.jpg', 'link' => 'https://www.instagram.com/p/DLKiYigOI_0/'],
            ['imagem' => 'imagens/postInsta/post3.webp', 'link' => 'https://www.instagram.com/p/DLKgmKtuOjh/?img_index=1'],
            ['imagem' => 'imagens/postInsta/post4.jpg', 'link' => 'https://www.instagram.com/p/DK5sg6mMwXk/'],
            ['imagem' => 'imagens/postInsta/post5.jpg', 'link' => 'https://www.instagram.com/p/DKw6jNgJYDV/'],
            ['imagem' => 'imagens/postInsta/post8.webp', 'link' => 'https://www.instagram.com/p/DKdWsDPJx6e/?img_index=1']
        ];

        // Comentários e itinerários podem ser carregados de services ou arquivos, se necessário
        $comentarios = include BASE_PATH . 'app/Views/Features_Site/home/comentarios.php';
        $itinerarios = include BASE_PATH . 'app/Views/Features_Site/itinerarios/itinerariosObj.php';

        $viewData = [
            'instagram_posts' => $instagram_posts,
            'comentarios' => $comentarios,
            'itinerarios' => $itinerarios
        ];
        View::render('site/home', $viewData);
    }
    /**
     * Exibe a página da equipe de funcionários, com filtros por departamento.
     */
    public function funcionarios()
    {
        $service = new FuncionarioService();
        $funcionarios = $service->getFuncionarios();
        $departamentos_disponiveis = $service->getDepartamentosUnicos($funcionarios);
        $departamento_selecionado = $_GET['departamento'] ?? 'todos';
        $funcionarios_filtrados = $service->filtrarPorDepartamento($funcionarios, $departamento_selecionado);
        $viewData = [
            'departamentos_disponiveis' => $departamentos_disponiveis,
            'departamento_selecionado' => $departamento_selecionado,
            'funcionarios_filtrados' => $funcionarios_filtrados,
            'base_url_departamento' => strtok($_SERVER["REQUEST_URI"], '?')
        ];
        View::render('site/funcionarios', $viewData);
    }

    /**
     * Exibe a página estática sobre a história da escola.
     */
    public function historia()
    {
        require_once BASE_PATH . 'app/Views/site/historia.php';
    }
}
