<?php
// paginas/listar_itinerarios.php - Página de listagem de todos os Itinerários Formativos

require_once __DIR__ . '/../objetos/itinerarios.php'; // Inclui o arquivo com os dados dos itinerários

// Definir categorias únicas para o filtro
$categorias_disponiveis = [];
foreach ($itinerarios as $itinerario) {
    if (!empty($itinerario['categorias'])) {
        foreach ($itinerario['categorias'] as $categoria) {
            $categorias_disponiveis[$categoria] = $categoria; // Usa a categoria como chave para evitar duplicatas
        }
    }
}
ksort($categorias_disponiveis); // Ordena as categorias alfabeticamente

// Filtrar itinerários com base na categoria selecionada (se houver)
$itinerarios_filtrados = $itinerarios; // Começa com todos os itinerários
$categoria_selecionada = $_GET['categoria'] ?? 'todos'; // Pega a categoria da URL, padrão 'todos'

if ($categoria_selecionada !== 'todos') {
    $itinerarios_filtrados = array_filter($itinerarios, function($itinerario) use ($categoria_selecionada) {
        return in_array($categoria_selecionada, $itinerario['categorias'] ?? []);
    });
}

// URL base para os links de categoria (sem a categoria atual)
$base_url_categoria = strtok($_SERVER["REQUEST_URI"], '?');

?>

<section class="itinerarios-listagem-section container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            <h1 class="detalhe-titulo-principal">Explore Nossos Itinerários Formativos</h1>

            <p class="lead-text-listagem text-center mb-5">
                Descubra as diversas trilhas de conhecimento que oferecemos para o desenvolvimento do seu filho.
                Nossos itinerários formativos são pensados para aprofundar interesses e preparar para o futuro.
            </p>

            <div class="card-padrao">
                <?php if (!empty($categorias_disponiveis)): ?>
                <div class="filter-controls text-center mb-5">
                    <a href="<?= $base_url_categoria ?>?categoria=todos"
                        class="btn btn-categoria <?= ($categoria_selecionada === 'todos') ? 'active' : '' ?>">Todos</a>
                    <?php foreach ($categorias_disponiveis as $categoria_nome): ?>
                    <a href="<?= $base_url_categoria ?>?categoria=<?= urlencode($categoria_nome) ?>"
                        class="btn btn-categoria <?= ($categoria_selecionada === $categoria_nome) ? 'active' : '' ?>">
                        <?= htmlspecialchars($categoria_nome) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="projetos-grid-wrapper">
                <div class="projetos-grid"> <?php if (empty($itinerarios_filtrados)): ?>
                    <p class="text-center w-100 no-results-message">Nenhum itinerário encontrado para a categoria
                        selecionada.</p>
                    <?php else: ?>
                    <?php foreach ($itinerarios_filtrados as $itinerario): ?>
                    <div class="project-card itinerario-item-card">
                        <a href="/siteEscola/detalhe_itinerario?id=<?= $itinerario['id'] ?>" class="card-link">
                            <img src="<?= $itinerario['imagem_fundo'] ?>" class="img-fluid card-img-top"
                                alt="<?= $itinerario['titulo'] ?>">
                            <div class="card-body-custom">
                                <?php if (!empty($itinerario['icone'])): ?>
                                <div class="icon-circle mb-3 mx-auto">
                                    <i class="<?= $itinerario['icone'] ?>"></i>
                                </div>
                                <?php endif; ?>
                                <h4 class="project-caption itinerario-card-title"><?= $itinerario['titulo'] ?></h4>
                                <p class="itinerario-card-description"><?= $itinerario['descricao'] ?></p>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>