<?php

require_once __DIR__ . '/../objetos/faq_integral.php'; 
require_once __DIR__ . '/../objetos/itinerarios.php';


$contato = '/siteEscola/contato'; 
$link_desempenho_integral_pr = 'https://www.aen.pr.gov.br/Noticia/Escolas-estaduais-em-tempo-integral-do-Parana-cresceram-acima-da-media-no-Ideb';

?>


<section class="integral-detalhado-section mb-5">

    <h2 class="section-title">Por Que o Ensino Integral? </h2>

    <div class="card-padrao card-amarelo p-5 mb-5 shadow-lg">
        <div class="row justify-content-center g-4">
            <div class="col-md-4 col-sm-6 d-flex">
                <div class="card-perguntas card-padrao card-amarelo flex-fill p-4 ">
                    <div class="icone-circulo mb-3 mx-auto">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h4>Melhor Desempenho</h4>
                    <p>Escolas integrais no PR cresceram <strong>18% no IDEB</strong> entre
                        2021 e 2023.</p>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 d-flex">
                <div class="card-perguntas card-padrao card-amarelo flex-fill p-4">
                    <div class="icone-circulo mb-3 mx-auto">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h4>Formação Completa</h4>
                    <p>Apoio pedagógico e oficinas que preparam para o futuro e a vida.</p>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 d-flex">
                <div class="card-perguntas card-padrao card-amarelo flex-fill p-4">
                    <div class="icone-circulo mb-3 mx-auto">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <h4>Iniciativa do Governo</h4>
                    <p>Uma aposta do Paraná na qualidade da educação e no futuro dos jovens.
                    </p>
                </div>
            </div>
        </div>
        <p class="mt-5 mb-0 text-center">
            Essa modalidade de ensino, apoiada pelo Governo do Paraná, amplia as oportunidades de aprendizado,
            contribui para a <strong>redução da evasão escolar</strong> e forma alunos mais engajados.
            <a href="<?= $link_desempenho_integral_pr ?>" target="_blank" rel="noopener noreferrer"
                class="link-estudo">Saiba mais sobre esses resultados em reportagem da Agência Estadual de
                Notícias.</a>
        </p>
    </div>

</section>

<section class="chamada-para-itinerarios py-5 mb-5">
    <h2 class="section-title"><strong>Conheça Nossos Itinerários Formativos</strong></h2>

    <v class="integral-content">
        <div class="swiper itinerariosSwiper">
            <div class="swiper-pagination"></div>
            <div class="swiper-wrapper">
                <?php foreach ($itinerarios as $itinerario) : ?>
                <div class="swiper-slide itinerario-slide">
                    <img src="<?= $itinerario['imagem_fundo'] ?>" alt="<?= $itinerario['titulo'] ?>"
                        class="itinerario-slide-img">

                    <div class="itinerario-slide-content">
                        <div class="itinerario-overlay-swiper"> </div>
                        <div class="itinerario-content-box-swiper">
                            <h1><?= $itinerario['titulo'] ?></h1>
                            <p><?= $itinerario['descricao'] ?></p>
                            <a href="detalhe_itinerario?id=<?= $itinerario['id'] ?>" class="btn btn-verde mt-3">Saiba
                                Mais</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="btn-swipper-itinerario">
                <div class="swiper-button-next"><i class="fa-solid fa-angle-right"></i></div>
                <div class="swiper-button-prev"><i class="fa-solid fa-angle-left"></i></div>
            </div>
        </div>

</section>

<section class="faq-section mb-5">

    <h2 class="section-title">Dúvidas Frequentes sobre o Ensino Integral</h2>
    <p class="text-center mb-5">
        Confira abaixo as perguntas mais comuns de pais e alunos.<br>
        Se sua dúvida não for respondida aqui, sinta-se à vontade para entrar em <a href="<?= $contato ?>">contato
            conosco!</a>
    </p>

    <div class=" row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card-padrao p-4">
                <div class="accordion" id="faqAccordion">
                    <?php foreach ($faq_integral as $index => $item): ?> <div class=" accordion-item mb-3 rounded">
                        <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                            <button class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?>"
                                type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>"
                                aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                aria-controls="collapse<?php echo $index; ?>">
                                <?php echo $item['pergunta']; ?>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $index; ?>"
                            class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>"
                            aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <?php echo $item['resposta']; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</section>