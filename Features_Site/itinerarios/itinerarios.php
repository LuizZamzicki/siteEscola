<?php
// paginas/listar_itinerarios.php - Página de listagem de todos os Itinerários Formativos

require_once __DIR__ . '/../objetos/itinerarios.php'; // Inclui o arquivo com os dados dos itinerários

// Definir categorias únicas para o filtro
$categorias_disponiveis = [];
foreach ($itinerarios as $itinerario)
{
    if (!empty($itinerario['categorias']))
    {
        foreach ($itinerario['categorias'] as $categoria)
        {
            $categorias_disponiveis[$categoria] = $categoria; // Usa a categoria como chave para evitar duplicatas
        }
    }
}
ksort($categorias_disponiveis); // Ordena as categorias alfabeticamente

// Filtrar itinerários com base na categoria selecionada (se houver)
$itinerarios_filtrados = $itinerarios; // Começa com todos os itinerários
$categoria_selecionada = $_GET['categoria'] ?? 'todos'; // Pega a categoria da URL, padrão 'todos'

if ($categoria_selecionada !== 'todos')
{
    $itinerarios_filtrados = array_filter($itinerarios, function ($itinerario) use ($categoria_selecionada)
    {
        return in_array($categoria_selecionada, $itinerario['categorias'] ?? []);
    });
}

// URL base para os links de categoria (sem a categoria atual)
$base_url_categoria = strtok($_SERVER["REQUEST_URI"], '?');

?>

<section class="itinerarios-listagem-section mb-5">

    <h2 class="section-title">Explore Nossos Itinerários Formativos</h2>

    <p class="subtitulo-secao text-center mb-5">
        Descubra as diversas trilhas de conhecimento que oferecemos para o desenvolvimento do seu filho.
        Nossos itinerários formativos são pensados para aprofundar interesses e preparar para o futuro.
    </p>

    <div class="card-padrao card-verde">
        <?php if (!empty($categorias_disponiveis)): ?>
            <div class="filter-controls text-center mb-2">

                <div id="categorias" class="categorias-wrapper d-none d-md-flex">
                    <a href="<?= $base_url_categoria ?>?categoria=todos"
                        class="btn btn-verde btn-categoria <?= ($categoria_selecionada === 'todos') ? 'active' : '' ?>">Todos</a>
                    <?php foreach ($categorias_disponiveis as $categoria_nome): ?>
                        <a href="<?= $base_url_categoria ?>?categoria=<?= urlencode($categoria_nome) ?>"
                            class="btn btn-verde btn-categoria <?= ($categoria_selecionada === $categoria_nome) ? 'active' : '' ?>">
                            <?= htmlspecialchars($categoria_nome) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php
                // O botão de toggle só aparece se houver categorias o suficiente no desktop
                $max_categorias_visiveis_inicialmente = 6; // Ajuste este número conforme a sua necessidade no desktop
                if (count($categorias_disponiveis) > $max_categorias_visiveis_inicialmente):
                    ?>
                    <div class="ver-mais-categorias d-none d-md-flex" id="toggleCategorias" onclick="toggleCategorias()">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                <?php endif; ?>

                <div class="form-group d-md-none w-100">
                    <label for="mobileCategoriaSelect" class="sr-only">Selecione uma categoria</label>
                    <select class="form-select btn-verde" id="mobileCategoriaSelect"
                        onchange="window.location.href = this.value;">
                        <option value="<?= $base_url_categoria ?>?categoria=todos" <?= ($categoria_selecionada === 'todos') ? 'selected' : '' ?>>Todos</option>
                        <?php foreach ($categorias_disponiveis as $categoria_nome): ?>
                            <option value="<?= $base_url_categoria ?>?categoria=<?= urlencode($categoria_nome) ?>"
                                <?= ($categoria_selecionada === $categoria_nome) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($categoria_nome) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

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
                        <a href="/siteEscola/detalhe_itinerario?id=<?= $itinerario['titulo'] ?>" class="card-link">
                            <img src="<?= $itinerario['imagem_fundo'] ?>" class="img-fluid card-img-top"
                                alt="<?= $itinerario['titulo'] ?>">
                            <div class="card-body-custom">
                                <?php if (!empty($itinerario['icone'])): ?>
                                    <div class="icone-circulo mb-3 mx-auto">
                                        <i class="<?= $itinerario['icone'] ?>"></i>
                                    </div>
                                <?php endif; ?>
                                <h4><strong><?= $itinerario['titulo'] ?></strong></h4>
                                <p class="itinerario-card-description"><?= $itinerario['descricao'] ?></p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <section class="chamada-para-contato pt-5">
        <div class="card-padrao card-roxo text-center">
            <h2 class="mb-4">Conheça os Espaços que Tornam o Aprendizado no Maffei Ainda Mais Completo</h2>
            <p class="mb-5">
                Salas bem equipadas, laboratórios modernos, biblioteca, áreas de convivência e muito mais: nossa
                estrutura
                foi pensada para apoiar cada etapa do desenvolvimento dos alunos.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="estrutura" class="btn btn-roxo btn-lg">Explore Nossa Estrutura</a>
                <a href="home" class="btn btn-azul btn-lg">Voltar à Página Inicial</a>
            </div>
        </div>
    </section>

</section>