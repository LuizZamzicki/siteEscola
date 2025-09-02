<?php

require_once __DIR__ . '/../objetos/estrutura.php'; 

$contato = 'contato';

?>

<section class="estrutura-escola-section mb-5">

    <h2 class="section-title">Estrutura de Ponta: Onde o Conhecimento Acontece</h2>
    <p class="subtitulo-secao text-center mb-5">
        No Colégio Maffei, cada espaço é pensado para inspirar, acolher e potencializar o aprendizado.
        Conheça as instalações que fazem da nossa escola um ambiente completo para o desenvolvimento do seu
        filho.
    </p>

    <div class="row justify-content-center g-4 pb-5">

        <?php foreach ($estrutura_escola as $item): ?>
        <div class="col-lg-6 col-md-8 col-sm-10 d-flex">
            <div class="card-padrao card-roxo flex-fill">
                <img src="<?= $item['imagem_url'] ?>" class="img-estrutura card-roxo card-img-top img-fluid mb-3"
                    alt="Imagem de <?= $item['titulo'] ?>">
                <div class="card-body-custom p-4 text-center">
                    <div class="icone-circulo mb-3 mx-auto">
                        <i class="<?= $item['icone'] ?>"></i>
                    </div>
                    <h4 class="mb-3"><?= $item['titulo'] ?></h4>
                    <p>
                        <?= $item['descricao'] ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
    <section class="chamada-para-contato pt-5">
        <div class="card-padrao card-roxo text-center">
            <h2 class="mb-4">Venha Conhecer o Maffei de Perto!</h2>
            <p class="text-center mb-5">Agende uma visita e explore cada detalhe do nosso ambiente de
                aprendizado.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="contato" class="btn btn-roxo btn-lg">Agende Sua Visita</a>
                <a href="home" class="btn btn-azul btn-lg">Voltar à Página Inicial</a>
            </div>
        </div>
    </section>

</section>