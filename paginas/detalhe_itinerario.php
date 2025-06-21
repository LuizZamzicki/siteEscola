<?php
// paginas/detalhe_itinerario.php - Página de detalhes de um Itinerário Formativo

require_once __DIR__ . '/../objetos/itinerarios.php';

// Defina a variável para o URL da página com todos os itinerários
$todos_itinerarios_url = '/siteEscola/listar_itinerarios'; 

$itinerario_id = $_GET['id'] ?? null;
$itinerario_selecionado = null;

if ($itinerario_id) {
    foreach ($itinerarios as $item) {
        if ($item['id'] === $itinerario_id) {
            $itinerario_selecionado = $item;
            break;
        }
    }
}

if (!$itinerario_selecionado) {
    header('Location: ' . $todos_itinerarios_url);
    exit;
}
?>

<section class="itinerario-detalhe-section container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            <h2 class="section-title"><?= $itinerario_selecionado['titulo'] ?></h2>

            <div class="main-content-card">
                <p class="card-text-itinerario text-center mb-3">
                    <?= $itinerario_selecionado['descricao'] ?>
                </p>
                <br>
                <p class="card-text-itinerario">
                    <?= $itinerario_selecionado['descricao_completa'] ?>
                </p>
            </div>

            <?php if (!empty($itinerario_selecionado['imagem_fundo'])) : ?>
            <div class="imagem-destaque-card">
                <img src="<?= $itinerario_selecionado['imagem_fundo'] ?>" class="img-fluid"
                    alt="Imagem de destaque do itinerário">
            </div>
            <?php endif; ?>

            <?php if (!empty($itinerario_selecionado['projetos_imagens'])) : ?>
            <h3 class="section-subtitle-detalhe">Projetos e Atividades</h3>
            <div class="projetos-grid-wrapper">
                <div class="projetos-grid">
                    <?php foreach ($itinerario_selecionado['projetos_imagens'] as $projeto_img) : ?>
                    <div class="project-card">
                        <img src="<?= $projeto_img['url'] ?>" class="img-fluid" alt="<?= $projeto_img['legenda'] ?>">
                        <div class="card-body-custom">
                            <p class="project-caption"><?= $projeto_img['legenda'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="text-center mt-5">
                <a href="<?= $todos_itinerarios_url ?>" class="btn btn-verde">
                    Veja mais Itinerários
                </a>
            </div>
        </div>
    </div>
</section>