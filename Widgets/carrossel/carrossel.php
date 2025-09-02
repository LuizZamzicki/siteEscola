<?php

function renderizarCarrossel(array $itens, string $idCarrossel, string $baseLink, string $id): void
{
    ?>
<div class="swiper <?= htmlspecialchars($idCarrossel) ?>">
    <div class="swiper-wrapper">
        <?php foreach ($itens as $item): ?>
        <? php// echo $baseLink . $item[$id] ?>
        <?php echo htmlspecialchars($idCarrossel) ?>

        <!-- <div class="swiper-slide <?= htmlspecialchars($idCarrossel) ?>-slide">
                    <img src="<?= htmlspecialchars($item['imagem']) ?>" alt="<?= htmlspecialchars($item['titulo']) ?>"
                        class="<?= htmlspecialchars($idCarrossel) ?>-slide-img"> -->

        <!-- <div class="<?= htmlspecialchars($idCarrossel) ?>-slide-content">
                <div class="<?= htmlspecialchars($idCarrossel) ?>-overlay-swiper"></div>
                <div class="<?= htmlspecialchars($idCarrossel) ?>-content-box-swiper">
                    <h1><?= htmlspecialchars($item['titulo']) ?></h1>
                    <p><?= htmlspecialchars($item['subtitulo']) ?></p>
                    <a href="<?= $baseLink . $item[$id] ?>" class="btn btn-padrao mt-3">Saiba Mais</a>
                </div>
            </div> -->
    </div>
    <?php endforeach; ?>
</div>

<!-- Paginação -->
<div class="swiper-pagination"></div>

<!-- Botões de navegação -->
<div class="swiper-button-next"><i class="fa-solid fa-angle-right"></i></div>
<div class="swiper-button-prev"><i class="fa-solid fa-angle-left"></i></div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    inicializarSwiper('.<?= htmlspecialchars($idCarrossel) ?>');
});
</script>
<?php
}

adicionarCss('Widgets/carrossel/carrossel.css');
adicionarJs('Widgets/carrossel/carrossel.js');
?>