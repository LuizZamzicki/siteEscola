<?php

function renderizarCarrossel(array $itens, string $idCarrossel, string $baseLink, string $id,
                             BotoesCores $corBotao, string $titulo = 'titulo', string $subtitulo = 'subtitulo',
                             string $imagem = 'imagem', string $texto_botao = 'Saiba Mais'): void
{
    ?>
    <div class="swiper carrosselSwiper <?= htmlspecialchars($idCarrossel) . "Swiper" ?>">
        <div class="swiper-wrapper">
            <?php foreach ($itens as $item): ?>

                <div class="swiper-slide carrossel-slide">
                    <img src="<?= htmlspecialchars($item[$imagem]) ?>" alt="<?= htmlspecialchars($item[$titulo]) ?>"
                        class="carrossel-slide-img">

                    <div class="carrossel-slide-content">
                        <div class="carrossel-overlay-swiper"></div>
                        <div class="carrossel-content-box-swiper">
                            <h1><?= htmlspecialchars($item[$titulo]) ?></h1>
                            <p><?= htmlspecialchars($item[$subtitulo]) ?></p>
                            <?php Botoes::getBotao($baseLink . $item[$id], $texto_botao, $corBotao) ?>
                        </div>
                    </div>
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
        document.addEventListener("DOMContentLoaded", function () {
            inicializarSwiper('.<?= htmlspecialchars($idCarrossel) . "Swiper" ?>');
        });
    </script>
    <?php
}

adicionarCss('Widgets/carrossel/carrossel.css');
adicionarJs('Widgets/carrossel/carrossel.js');
?>