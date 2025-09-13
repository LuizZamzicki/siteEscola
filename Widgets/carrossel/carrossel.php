<?php

function renderizarCarrossel(
    array $itens,
    string $idCarrossel,
    string $baseLink,
    string $id,
    BotoesCores $corBotao,
    string $titulo = 'titulo',
    string $subtitulo = 'subtitulo',
    string $imagem = 'imagem',
    string $texto_botao = 'Saiba Mais',
    CoresSistema $corBase = CoresSistema::VERDE
): void {

    $cores = FuncoesUtils::getCorBase($corBase);
    ?>
    <style>
        /* Define as variáveis CSS no escopo do carrossel */
        .<?= htmlspecialchars($idCarrossel) ?>Swiper {
            --carrossel-cor-primaria-escuro:
                <?= $cores['primaria_escuro'] ?>
            ;
            --carrossel-cor-primaria:
                <?= $cores['primaria'] ?>
            ;
            --carrossel-cor-primaria-claro:
                <?= $cores['primaria_claro'] ?>
            ;
        }

        .<?= htmlspecialchars($idCarrossel) ?>Swiper .swiper-button-next i,
        .<?= htmlspecialchars($idCarrossel) ?>Swiper .swiper-button-prev i {
            color: var(--carrossel-cor-primaria-escuro) !important;
        }

        .<?= htmlspecialchars($idCarrossel) ?>Swiper .swiper-button-next:hover,
        .<?= htmlspecialchars($idCarrossel) ?>Swiper .swiper-button-prev:hover {
            background-color: var(--carrossel-cor-primaria) !important;
        }

        .<?= htmlspecialchars($idCarrossel) ?>Swiper .swiper-pagination-bullet-active {
            background-color: var(--carrossel-cor-primaria-claro) !important;
        }

        .<?= htmlspecialchars($idCarrossel) ?>Swiper .carrossel-content-box-swiper h1 {
            color: var(--carrossel-cor-primaria-escuro);
        }
    </style>

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

FuncoesUtils::adicionarCss('Widgets/carrossel/carrossel.css');
FuncoesUtils::adicionarJs('Widgets/carrossel/carrossel.js');
?>