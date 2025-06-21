<?php
// paginas/integral.php - Conteúdo da página de Ensino Integral

// Inclua os arquivos que contêm os dados
require_once __DIR__ . '/../objetos/itinerarios.php';
require_once __DIR__ . '/../objetos/comentarios.php';

// Dados para os posts do Instagram (placeholders - podem ser movidos para outro arquivo se crescerem)
$instagram_posts = [
    ['imagem' => 'https://placehold.co/400', 'link' => 'https://www.instagram.com/colegio_maffei/'],
    ['imagem' => 'https://placehold.co/400', 'link' => 'https://www.instagram.com/colegio_maffei/'],
    ['imagem' => 'https://placehold.co/400', 'link' => 'https://www.instagram.com/colegio_maffei/'],
    ['imagem' => 'https://placehold.co/400', 'link' => 'https://www.instagram.com/colegio_maffei/'],
    ['imagem' => 'https://placehold.co/400', 'link' => 'https://www.instagram.com/colegio_maffei/'],
    ['imagem' => 'https://placehold.co/400', 'link' => 'https://www.instagram.com/colegio_maffei/'],
    ['imagem' => 'https://placehold.co/400', 'link' => 'https://www.instagram.com/colegio_maffei/'],
    ['imagem' => 'https://placehold.co/400', 'link' => 'https://www.instagram.com/colegio_maffei/']
];

?>

<section class="principal-section my-5">
    <div class="container">
        <h2 class="section-title">O colégio João Maffei Rosa</h2>
        <p class="text-center mb-5">
            No Colégio Maffei, valorizamos um ambiente de aprendizado inovador e acolhedor.<br>
            Com foco no desenvolvimento integral, preparamos nossos alunos para os desafios do futuro. </p>

        <div class="card-integral p-4 my-5 rounded d-flex flex-column flex-md-row align-items-center shadow">

            <div class="card-integral-text col-12 col-md-8 pe-md-4">

                <h3><strong>Colégio com ensino integral</strong>
                </h3>
                <p>Nossas instalações foram projetadas para estimular a criatividade e o bem-estar em um
                    ambiente
                    seguro
                    e acolhedor. Oferecemos salas de aula modernas, laboratórios equipados, áreas de lazer e um
                    refeitório confortável, tudo pensando no desenvolvimento completo do seu filho.</p><br>
                O <strong>Ensino Integral</strong> é a base da nossa metodologia, proporcionando um currículo
                ampliado e
                atividades que vão além da grade tradicional, preparando os alunos para a vida.</p>
                <a href="integral" class="btn btn-amarelo mt-4">Conheça o Integral</a>
            </div>

            <div class="card-integral-image col-12 col-md-4 mt-4 mt-md-0 ps-md-4">
                <img src="imagens/img-entrada-escola.jpg" alt="Entrada da Escola - Ensino Integral"
                    class="img-fluid rounded">
            </div>
        </div>
        <div class="main-card-integral p-4 mb-5 shadow-lg">
            <h3 class="text-center mb-5"><strong>As 3 Perguntas Mais Frequentes sobre o Ensino
                    Integral</strong></h3>
            <div class="row justify-content-center g-4">
                <div class="col-md-4 col-sm-6 d-flex">
                    <div class="feature-card flex-fill p-4 text-center rounded-3">
                        <div class="icon-circle mb-3 mx-auto">
                            <i class="bi bi-clock"></i>
                        </div>
                        <h4 class="feature-title">Horário do Integral</h4>
                        <p class="feature-text">Nosso programa funciona das <strong>07:45 às 16:00</strong>,
                            oferecendo
                            um dia completo de atividades e aprendizado.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 d-flex">
                    <div class="feature-card flex-fill p-4 text-center rounded-3">
                        <div class="icon-circle mb-3 mx-auto">
                            <i class="bi bi-cup-straw"></i>
                        </div>
                        <h4 class="feature-title">Refeições</h4>
                        <p class="feature-text">Sim, a escola oferece todas as refeições necessárias durante o
                            período
                            integral, incluindo <strong>almoço e lanches</strong> balanceados.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 d-flex">
                    <div class="feature-card flex-fill p-4 text-center rounded-3">
                        <div class="icon-circle mb-3 mx-auto">
                            <i class="bi bi-lightbulb"></i>
                        </div>
                        <h4 class="feature-title">Currículo Ampliado</h4>
                        <p class="feature-text">Inclui <strong>projetos de vida, apoio pedagógico, oficinas práticas
                                e
                                dinâmicas, e trilhas de aprendizagem</strong> para desenvolvimento integral.</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="integral" class="btn btn-amarelo">Explore nosso Ensino Integral</a>
            </div>
        </div>
    </div>
</section>


<div class="integral-content">
    <h2 class="section-title">Nossos Itinerários Formativos</h2>
    <p class="text-center mb-5 ">No Colégio João Maffei Rosa, o ensino integral oferece caminhos inovadores
        para o seu desenvolvimento. Conheça nossos itinerários:</p>

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
                        <h5><?= $itinerario['titulo'] ?></h5>
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

    <hr class="my-5">

    <section class="comentarios-section">
        <h2 class="section-title mb-5">O que nossos alunos dizem sobre nós</h2>
        <div class="swiper comentariosSwiper">
            <div class="swiper-wrapper">
                <?php foreach ($comentarios_alunos as $comentario) : ?>
                <div class="swiper-slide comentario-slide">
                    <div class="comentario-card card card-roxo">
                        <p class="comentario-texto">"<?= $comentario['comentario'] ?>"</p>
                        <div class="comentario-divider"></div>
                        <div class="comentario-info">
                            <img src="<?= $comentario['foto'] ?>" alt="<?= $comentario['nome'] ?>"
                                class="comentario-avatar">
                            <div class="comentario-autor-info">
                                <p class="nome-aluno"><?= $comentario['nome'] ?></p>
                                <p class="serie-aluno"><?= $comentario['serie'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="btn-swipper-comentarios">
                <div class="swiper-button-next"><i class="fa-solid fa-angle-right"></i></div>
                <div class="swiper-button-prev"><i class="fa-solid fa-angle-left"></i></div>
            </div>

    </section>

    <hr class="my-5">

    <section class="instagram-section">
        <h2 class="section-title">Fique por dentro das novidades no Instagram</h2>
        <p class="text-center mb-5">Acompanhe nosso dia a dia, eventos e projetos através das nossas últimas
            postagens:</p>

        <div class="instagram-grid">
            <?php foreach ($instagram_posts as $post) : ?>
            <div class="instagram-post">
                <a href="<?= $post['link'] ?>" target="_blank">
                    <img src="<?= $post['imagem'] ?>" alt="Post Instagram">
                    <div class="instagram-overlay">
                        <i class="bi bi-instagram"></i>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <a href="https://www.instagram.com/colegio_maffei/" target="_blank" class="btn btn-instagram-more">Ver Mais
            no
            Instagram</a>
    </section>
</div>