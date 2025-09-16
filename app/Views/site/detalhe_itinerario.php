<?php
// A view espera receber $itinerario_selecionado já preparado pelo controller.
?>

<section class="itinerario-detalhe-section mb-5">

    <h2 class="section-title"><?= $itinerario_selecionado['titulo'] ?></h2>

    <div class="card-padrao card-verde mb-5">
        <p class="card-text-itinerario text-center mb-3">
            <?= $itinerario_selecionado['descricao'] ?>
        </p>
        <br>
        <p class="card-text-itinerario">
            <?= $itinerario_selecionado['descricao_completa'] ?>
        </p>
    </div>

    <?php if (!empty($itinerario_selecionado['imagem_fundo'])): ?>
        <div class="imagem-destaque-card">
            <img src="<?= $itinerario_selecionado['imagem_fundo'] ?>" class="img-fluid"
                alt="Imagem de destaque do itinerário">
        </div>
    <?php endif; ?>

    <?php if (!empty($itinerario_selecionado['projetos_imagens'])): ?>
        <h3 class="section-title">Projetos e Atividades</h3>
        <div class="projetos-grid-wrapper">
            <div class="projetos-grid">
                <?php foreach ($itinerario_selecionado['projetos_imagens'] as $projeto_img): ?>
                    <div class="project-card">

                        <img src="<?= $projeto_img['url'] ?>" class="img-fluid" alt="<?= $projeto_img['legenda'] ?>">

                        <div class="card-body-custom">
                            <p class="project-caption"><?= $projeto_img['legenda'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>


    <section class="chamada-para-contato pt-5">
        <div class="card-padrao card-verde text-center">
            <h2 class="mb-4">Pronto para a Próxima Aventura?</h2>
            <p class="text-center mb-5">
                Cada itinerário é uma chance de explorar novos conhecimentos e paixões. <br>Não pare por aqui, mergulhe
                em
                todas as opções que disponibilizamos para nossos alunos irem além.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="listar_itinerarios" class="btn btn-verde btn-lg">Veja mais Itinerários</a>
                <a href="home" class="btn btn-azul btn-lg">Voltar à Página Inicial</a>
            </div>
        </div>
    </section>

</section>