<?php
// paginas/estrutura.php - Conteúdo detalhado sobre a Estrutura da Escola (Dinâmico)

// Inclua o arquivo que contém o array da estrutura da escola
require_once __DIR__ . '/../objetos/estrutura.php'; // Ajuste o caminho se necessário!

$contato = 'contato';

?>

<section class="estrutura-escola-section my-5">
    <div class="container">
        <h2 class="section-title text-center mb-4">Estrutura de Ponta: Onde o Conhecimento Acontece</h2>
        <p class="text-center mb-5 lead-text">
            No Colégio Maffei, cada espaço é pensado para inspirar, acolher e potencializar o aprendizado.
            Conheça as instalações que fazem da nossa escola um ambiente completo para o desenvolvimento do seu filho.
        </p>

        <div class="row justify-content-center g-4">

            <?php foreach ($estrutura_escola as $item): ?>
            <div class="col-lg-6 col-md-8 col-sm-10 d-flex">
                <div class="feature-card flex-fill rounded-3">
                    <img src="https://placehold.co/800x450/B7E4F6/105F7E?text=<?= $item['imagem_placeholder'] ?>"
                        class="img-estrutura card-img-top img-fluid rounded-top mb-3"
                        alt="Imagem de <?= $item['titulo'] ?>">
                    <div class="card-body-custom p-4 text-center">
                        <div class="icon-circle mb-3 mx-auto">
                            <i class="<?= $item['icone'] ?>"></i>
                        </div>
                        <h4 class="feature-title"><?= $item['titulo'] ?></h4>
                        <p class="feature-text">
                            <?= $item['descricao'] ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
        <p class="text-center mt-5 lead-text">
            Quer conhecer de perto nossa estrutura? Entre em <a href="<?= $contato ?>">contato conosco</a> e agende uma
            visita!
        </p>

    </div>
</section>