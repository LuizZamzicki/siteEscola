<?php

function renderizarCarrosselMultiplo(
    array $itens,
    string $idCarrossel,
    callable $renderSlide,
    int $qtdeCarrossel = 1,
    bool $usaBotoesNavegacao = true,
    CoresSistema $corBase = CoresSistema::ROXO,

) {

    $cores = FuncoesUtils::getCorBase($corBase);
    ?>
    <style>
        .<?= $idCarrossel ?>Swiper {
            --carrossel-cor-primaria:
                <?= $cores['primaria'] ?>
            ;
            --carrossel-cor-primaria-escuro:
                <?= $cores['primaria_escuro'] ?>
            ;
            --carrossel-cor-primaria-claro:
                <?= $cores['primaria_claro'] ?>
            ;
        }

        .<?= $idCarrossel ?>Swiper .carrosselMultiplo-card {
            background-color: var(--carrossel-cor-primaria) !important;
            color: white !important;
        }

        .<?= $idCarrossel ?>Swiper .carrosselMultiplo-card:hover {
            background-color: var(--carrossel-cor-primaria-escuro) !important;
        }

        .<?= $idCarrossel ?>Swiper .carrosselMultiplo-card.is-active {
            background-color: var(--carrossel-cor-primaria-escuro) !important;
        }

        .<?= $idCarrossel ?>Swiper .swiper-button-next i,
        .<?= $idCarrossel ?>Swiper .swiper-button-prev i {
            color: var(--carrossel-cor-primaria-escuro) !important;
        }

        .<?= $idCarrossel ?>Swiper .swiper-button-next:hover,
        .<?= $idCarrossel ?>Swiper .swiper-button-prev:hover {
            background-color: var(--carrossel-cor-primaria) !important;
        }

        .<?= $idCarrossel ?>Swiper .swiper-button-next:hover i,
        .<?= $idCarrossel ?>Swiper .swiper-button-prev:hover i {
            color: var(--carrossel-cor-primaria-claro) !important;
        }

        .<?= $idCarrossel ?>Swiper .swiper-pagination-bullet-active {
            background-color: var(--carrossel-cor-primaria-claro) !important;
        }
    </style>


    <div class="swiper CarrosselMultiploSwiper <?= $idCarrossel ?>Swiper">
        <?php if ($usaBotoesNavegacao): ?>
            <div class="swiper-pagination"></div>
        <?php endif; ?>
        <div class="swiper-wrapper">
            <?php foreach ($itens as $item): ?>
                <div class="swiper-slide carrosselMultiplo-slide">
                    <div class="carrosselMultiplo-card card card-roxo">
                        <?= $renderSlide($item) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($usaBotoesNavegacao): ?>
            <div class="btn-swipper">
                <div class="swiper-button-next"><i class="fa-solid fa-angle-right"></i></div>
                <div class="swiper-button-prev"><i class="fa-solid fa-angle-left"></i></div>
            </div>
        <?php endif; ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                inicializarSwiperMultiplo('.<?= htmlspecialchars($idCarrossel) . "Swiper" ?>', <?= $qtdeCarrossel ?>);
            });
        </script>
    </div>
    <?php
}

FuncoesUtils::adicionarCss('Widgets/carrossel_multiplo/carrossel_multiplo.css');
FuncoesUtils::adicionarJs('Widgets/carrossel_multiplo/carrossel_multiplo.js');
?>