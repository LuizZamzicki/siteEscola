<?php

include 'Widgets/secoes/secoes.php';
include 'Widgets/carrossel/carrossel.php';
include 'Widgets/carrossel_multiplo/carrossel_multiplo.php';

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

    renderizarCarrossel($itinerarios, 'itinerarios', 'detalhe_itinerario?titulo=', 'titulo', BotoesCores::VERDE, corBase: CoresSistema::VERDE);
    ?>

</div>

<?php

ob_start();

renderizarCarrosselMultiplo(
    $comentarios,
    'comentarios',
    function ($item)
    {
        ?>
    <p class="comentarios-texto">"<?= $item['comentario'] ?>"</p>
    <div class="comentarios-divider"></div>
    <div class="comentarios-info">
        <img src="<?= $item['foto'] ?>" alt="<?= $item['nome'] ?>" class="comentarios-avatar">
        <div class="comentarios-autor-info">
            <p class="nome-aluno"><?= $item['nome'] ?></p>
            <p class="serie-aluno"><?= $item['serie'] ?></p>
        </div>
    </div>
    <?php
    },
    corBase: CoresSistema::ROXO,
    qtdeCarrossel: 3,
    usaBotoesNavegacao: false
);

$conteudo_carrossel = ob_get_clean();

Secoes::renderizarSecao(
    "comentarios-section",
    "O Que Nossos Alunos Dizem Sobre Nós",
    CoresSistema::AMARELO,
    $conteudo_carrossel,
);

?>

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