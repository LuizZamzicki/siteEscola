<?php

require_once BASE_PATH . 'Utils/FuncoesUtils.php';

adicionarCss('Features_Site/home/home.css');

include 'Widgets/botoes/botoes.php';
include 'Widgets/carrossel/carrossel.php';
include 'Features_Site\itinerarios\itinerariosObj.php';
include 'Features_Site\funcionarios\funcionariosObj.php';

// Dados para os posts do Instagram (Inserir API Dps)
$instagram_posts = [

['imagem' => 'imagens/postInsta/post1.webp', 'link' => 'https://www.instagram.com/p/DLKk-PkuQnj/'],
['imagem' => 'imagens/postInsta/post2.jpg', 'link' => 'https://www.instagram.com/p/DLKiYigOI_0/'],
['imagem' => 'imagens/postInsta/post3.webp', 'link' => 'https://www.instagram.com/p/DLKgmKtuOjh/?img_index=1'],
['imagem' => 'imagens/postInsta/post4.jpg', 'link' => 'https://www.instagram.com/p/DK5sg6mMwXk/'],
['imagem' => 'imagens/postInsta/post5.jpg', 'link' => 'https://www.instagram.com/p/DKw6jNgJYDV/'],
['imagem' => 'imagens/postInsta/post8.webp', 'link' => 'https://www.instagram.com/p/DKdWsDPJx6e/?img_index=1']
];

?>

<section class="principal-section pb-5">

    <h2 class="section-title">O Colégio João Maffei Rosa</h2>
    <p class="subtitulo-secao text-center mb-5">
        No Colégio Maffei, valorizamos um ambiente de aprendizado inovador e acolhedor.<br>
        Com foco no desenvolvimento integral, preparamos nossos alunos para os desafios do futuro. </p>

    <div class="card-padrao card-amarelo p-4 my-5 d-flex flex-column flex-md-row align-items-center">

        <div class="texto-escola text-start">
            <h3><strong>Colégio com ensino integral</strong></h3>
            <p>Nossas instalações foram projetadas para estimular a criatividade e o bem-estar em um
                ambiente
                seguro
                e acolhedor. Oferecemos salas de aula modernas, laboratórios equipados, áreas de lazer e um
                refeitório confortável, tudo pensando no desenvolvimento completo do seu filho.</p>
            <p>
                O <strong>Ensino Integral</strong> é a base da nossa metodologia, proporcionando um currículo
                ampliado e
                atividades que vão além da grade tradicional, preparando os alunos para a vida.</p>
            <?php Botoes::getBotao("integral.php", "Conheça o Integral", BotoesCores::AMARELO) ?>

        </div>

        <div class="integral-image col-12 col-md-6 mt-4 mt-md-0 ps-md-4">
            <img src="<?= $caminho_pasta ?>/imagens/escola/img-entrada-escola.jpg"
                alt="Entrada da Escola - Ensino Integral" class="img-fluid rounded">
        </div>
    </div>

    <div class="card-padrao card-amarelo p-4 pb-5">
        <h3 class="mb-5"><strong>As 3 Perguntas Mais Frequentes sobre o Ensino
                Integral</strong></h3>
        <div class="row justify-content-center g-4">
            <div class="col-md-4 col-sm-6 d-flex">
                <div class="card-perguntas card-padrao card-amarelo flex-fill p-4">
                    <div class="icone-circulo mb-3 mx-auto">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <h4>Horário do Integral</h4>
                    <p>Nossas aulas vão das <strong>07:40 às 16:45,</strong>
                        oferecendo um dia completo de atividades e aprendizado.</p>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 d-flex">
                <div class="card-perguntas card-padrao card-amarelo flex-fill p-4">
                    <div class="icone-circulo mb-3 mx-auto">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <h4>Refeições</h4>
                    <p><strong>São 2 intervalos de 20 minutos (manhã e tarde) e 1 hora de almoço,</strong> incluindo
                        almoço e
                        lanches balanceados.</p>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 d-flex">
                <div class="card-perguntas card-padrao card-amarelo flex-fill p-4">
                    <div class="icone-circulo mb-3 mx-auto">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <h4>Grade Ampliada</h4>
                    <p>Inclui oficinas práticas e dinâmicas, projetos de vida, apoio pedagógico e trilhas de
                        aprendizagem para desenvolvimento integral.</p>
                </div>
            </div>
        </div>
        <div class="pt-5">
            <a href="integral" class="btn btn-amarelo">Explore nosso Ensino Integral</a>
        </div>
    </div>
</section>


<div class="integral-content pb-5">
    <h2 class="section-title">Nossos Itinerários Formativos</h2>
    <p class="subtitulo-secao text-center mb-5 ">No Colégio João Maffei Rosa, o ensino integral oferece caminhos
        inovadores
        para o seu desenvolvimento. Conheça nossos itinerários:</p>

    <?php

    renderizarCarrossel($itinerarios, 'itinerarios',
     'detalhe_itinerario?titulo=', 'titulo', BotoesCores::VERDE, corBase: CarrosselCores::VERDE);
    ?>

</div>
</div>

<!-- <section class="comentarios-section pb-5">
    <h2 class="section-title mb-5">O Que Nossos Alunos Dizem Sobre Nós</h2>
    <div class="swiper comentariosSwiper">
        <div class="swiper-pagination"></div>
        <div class="swiper-wrapper">
            <?php foreach ($comentarios_alunos as $comentario): ?>
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

</section> -->
<!-- 
<section class="instagram-section pb-5">
    <h2 class="section-title">Fique Por Dentro das Novidades no
        <a href="https://www.instagram.com/colegio_maffei/" target="_blank" data-text="Instagram">Instagram</a>
    </h2>
    <p class="subtitulo-secao text-center mb-5">Acompanhe nosso dia a dia, eventos e projetos através das nossas
        últimas
        postagens:</p>

    <div class="instagram-grid">
        <?php foreach ($instagram_posts as $post): ?>
            <div class="instagram-post">
                <a href="<?= $post['link'] ?>" target="_blank">
                    <img src="<?= $post['imagem'] ?>" alt="Post Instagram">
                    <div class="instagram-overlay">
                        <i class="fa-brands fa-instagram"></i>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div> -->

</section>