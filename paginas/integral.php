<?php

require_once __DIR__ . '/../objetos/faq_integral.php'; 
$contato = '/paginas/contato.php'; 

$link_desempenho_integral_pr = 'https://www.aen.pr.gov.br/Noticia/Escolas-estaduais-em-tempo-integral-do-Parana-cresceram-acima-da-media-no-Ideb';

?>


<section class="integral-detalhado-section my-5">
    <div class="container">
        <h2 class="section-title text-center mb-4">Ensino Integral: Um Futuro Completo para Seu Filho</h2>
        <p class="text-center mb-5 lead-text">
            No Colégio Maffei, acreditamos que o tempo estendido na escola transforma o aprendizado.
            Conheça a proposta do Ensino Integral e como ele prepara nossos alunos para a vida com excelência.
        </p>

        <div class="main-card-integral p-4 mb-5 shadow-lg text-center">
            <h3 class="text-center mb-5 custom-heading"><strong>O Dia a Dia no Integral</strong></h3>

            <div id="integralCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#integralCarousel" data-bs-slide-to="0" class="active"
                        aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#integralCarousel" data-bs-slide-to="1"
                        aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#integralCarousel" data-bs-slide-to="2"
                        aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="imagens/integral/integral-alunos-praticas.jpg"
                            class="d-block w-100 rounded-lg carousel-image"
                            alt="Alunos em atividades práticas do integral">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>Oficinas e Projetos Interativos</h5>
                            <p>Engajamento em atividades que estimulam a criatividade e o pensamento crítico.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="imagens/integral/integral-refeicao.jpg"
                            class="d-block w-100 rounded-lg carousel-image" alt="Alunos durante a refeição no integral">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>Refeições Nutritivas e Bem-estar</h5>
                            <p>Momentos de alimentação balanceada e convívio social, preparados com carinho.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="imagens/integral/integral-estudo-orientado.jpg"
                            class="d-block w-100 rounded-lg carousel-image" alt="Alunos estudando com apoio pedagógico">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>Apoio Pedagógico e Estudo Orientado</h5>
                            <p>Suporte individualizado para fortalecer o aprendizado e sanar dúvidas.</p>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#integralCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#integralCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Próximo</span>
                </button>
            </div>
            <p class="mt-5 mb-0 text-center lead-text">
                O Ensino Integral no Colégio Maffei proporciona um ambiente dinâmico e enriquecedor,
                onde cada momento é uma oportunidade para explorar, aprender e crescer.
                Nossos alunos desfrutam de uma jornada educacional completa, que vai muito além da sala de aula.
            </p>
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