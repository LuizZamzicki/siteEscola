<?php

require_once __DIR__ . '/../objetos/faq_integral.php'; 
require_once __DIR__ . '/../objetos/dia_a_dia_integral.php';

$contato = '/paginas/contato.php'; 

$link_desempenho_integral_pr = 'https://www.aen.pr.gov.br/Noticia/Escolas-estaduais-em-tempo-integral-do-Parana-cresceram-acima-da-media-no-Ideb';

?>


<section class="integral-detalhado-section my-5">
    <div class="container">
        <h2 class="section-title text-center mb-4">O dia a dia no Integral</h2>


        <div class="swiper itinerariosSwiper">
            <div class="swiper-pagination"></div>
            <div class="swiper-wrapper">
                <?php foreach ($dia_a_dia_integral as $dia_dia) : ?>
                <div class="swiper-slide itinerario-slide">
                    <img src="<?= $dia_dia['imagem_url'] ?>" alt="<?= $dia_dia['titulo'] ?>"
                        class="itinerario-slide-img">

                    <div class="itinerario-slide-content">
                        <div class="itinerario-overlay-swiper"> </div>
                        <div class="itinerario-content-box-swiper">
                            <h5><?= $dia_dia['titulo'] ?></h5>
                            <a href="detalhe_itinerario?id=<?= $itinerario['id'] ?>" class="btn btn-verde mt-3">Conheça
                                nossos itinerarios</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="swiper-button-next"><i class="fa-solid fa-chevron-right"></i></div>
            <div class="swiper-button-prev"><i class="fa-solid fa-chevron-left"></i></div>
        </div>

        <div class="main-card-integral p-4 mb-5 shadow-lg">
            <h3 class="text-center mb-5 custom-heading"><strong>Por Que o Ensino Integral?</strong></h3>
            <div class="row justify-content-center g-4">
                <div class="col-md-4 col-sm-6 d-flex">
                    <div class="feature-card flex-fill p-4 text-center rounded-3">
                        <div class="icon-circle mb-3 mx-auto">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h4 class="feature-title">Melhor Desempenho</h4>
                        <p class="feature-text">Escolas integrais no PR cresceram <strong>18% no IDEB</strong> entre
                            2021 e 2023.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 d-flex">
                    <div class="feature-card flex-fill p-4 text-center rounded-3">
                        <div class="icon-circle mb-3 mx-auto">
                            <i class="bi bi-people"></i>
                        </div>
                        <h4 class="feature-title">Formação Completa</h4>
                        <p class="feature-text">Apoio pedagógico e oficinas que preparam para o futuro e a vida.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 d-flex">
                    <div class="feature-card flex-fill p-4 text-center rounded-3">
                        <div class="icon-circle mb-3 mx-auto">
                            <i class="bi bi-patch-check"></i>
                        </div>
                        <h4 class="feature-title">Iniciativa do Governo</h4>
                        <p class="feature-text">Uma aposta do Paraná na qualidade da educação e no futuro dos jovens.
                        </p>
                    </div>
                </div>
            </div>
            <p class="mt-5 mb-0 text-center lead-text">
                Essa modalidade de ensino, apoiada pelo Governo do Paraná, amplia as oportunidades de aprendizado,
                contribui para a <strong>redução da evasão escolar</strong> e forma alunos mais engajados.
                <a href="<?= $link_desempenho_integral_pr ?>" target="_blank" rel="noopener noreferrer"
                    class="text-link-inline">Saiba mais sobre esses resultados em reportagem da Agência Estadual de
                    Notícias.</a>
            </p>
        </div>
</section>



<section class="faq-section my-5">
    <div class="container">
        <h2 class="section-title text-center mb-4">Dúvidas Frequentes sobre o Ensino Integral</h2>
        <p class="text-center mb-5">
            Confira abaixo as perguntas mais comuns de pais e alunos.
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
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?php echo $index; ?>"
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
    </div>
</section>