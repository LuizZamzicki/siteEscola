<?php
// paginas/integral.php - Conteúdo detalhado sobre o Ensino Integral

// Opcional: Se você quiser links para outras páginas dentro do texto, defina-os aqui
// Por exemplo, se quiser linkar para os itinerários formativos em uma seção futura
// $itinerarios_url = '/paginas/itinerarios.php'; // Ajuste conforme a sua estrutura real

require_once __DIR__ . '/../objetos/faq_integral.php'; 
$contato = '/paginas/contato.php'; // Caminho correto para o contato

$link_desempenho_integral_pr = 'https://www.aen.pr.gov.br/Noticia/Escolas-estaduais-em-tempo-integral-do-Parana-cresceram-acima-da-media-no-Ideb';

?>


<section class="integral-detalhado-section my-5">
    <div class="container">
        <h2 class="section-title text-center mb-4">Ensino Integral: Um Futuro Completo para Seu Filho</h2>
        <p class="text-center mb-5 lead-text">
            No Colégio Maffei, acreditamos que o tempo estendido na escola transforma o aprendizado.
            Conheça a proposta do Ensino Integral e como ele prepara nossos alunos para a vida com excelência.
        </p>

        <div class="main-card-integral p-4 mb-5 shadow-lg">
            <h3 class="text-center mb-5 custom-heading"><strong>O Que é o Ensino Integral no Colégio Maffei?</strong>
            </h3>
            <div class="row justify-content-center g-4">
                <div class="col-md-4 col-sm-6 d-flex">
                    <div class="feature-card flex-fill p-4 text-center rounded-3">
                        <div class="icon-circle mb-3 mx-auto">
                            <i class="bi bi-clock"></i>
                        </div>
                        <h4 class="feature-title">Horário Estendido</h4>
                        <p class="feature-text">Das **07:45h às 16:00h**, um dia de aprendizado contínuo e
                            produtivo.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 d-flex">
                    <div class="feature-card flex-fill p-4 text-center rounded-3">
                        <div class="icon-circle mb-3 mx-auto">
                            <i class="bi bi-book"></i>
                        </div>
                        <h4 class="feature-title">Currículo Ampliado</h4>
                        <p class="feature-text">Mais tempo para aprofundar conhecimentos e explorar novos temas com
                            qualidade.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 d-flex">
                    <div class="feature-card flex-fill p-4 text-center rounded-3">
                        <div class="icon-circle mb-3 mx-auto">
                            <i class="bi bi-cup-straw"></i>
                        </div>
                        <h4 class="feature-title">Refeições Inclusas</h4>
                        <p class="feature-text">Almoço e lanches nutritivos para manter a energia e o bem-estar do seu
                            filho.</p>
                    </div>
                </div>
            </div>
            <p class="mt-5 mb-0 text-center lead-text">
                O Ensino Integral vai além da sala de aula, focando no **desenvolvimento completo** dos
                alunos com atividades
                que estimulam a criatividade, o pensamento crítico e habilidades essenciais para a vida.
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
                        <p class="feature-text">Escolas integrais no PR cresceram **18% no IDEB** entre
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
                contribui para a **redução da evasão escolar** e forma alunos mais engajados.
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