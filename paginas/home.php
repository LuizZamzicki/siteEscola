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

<section>
    <h2 class="section-title">A escola</h2>
    <div class="card-integral p-4 my-5 rounded d-flex flex-column flex-md-row align-items-center shadow">
        <div class="card-integral-text flex-grow-1">
            <h1 class="mb-3"><strong>Ensino Integral</strong></h1>
            <h5>Para o desenvolvimento completo dos nossos alunos.
            </h5><br><br>
            <ul>
                <li>Entrada às <strong>07:45</strong> e saída às <strong>16:00</strong></li>
                <li>Refeições fornecidas pela escola (almoço e lanches)</li>
                <li>Projetos de Vida e Apoio Pedagógico</li>
                <li>Oficinas práticas e dinâmicas</li>
                <li>Inúmeras trilhas para escolher</li>
            </ul>
            <a href="integral" class="btn btn-amarelo mt-3">Saiba mais</a>
        </div>
        <div class="card-integral-image mt-4 mt-md-0 ms-md-4">
            <img src="imagens/img-entrada-escola.jpg" alt="Ensino Integral" class="img-fluid rounded">
        </div>
    </div>
</section>


<div class="integral-content">
    <h2 class="section-title">Nossos Itinerários Formativos</h2>
    <p class="text-center mb-4 ">No Colégio João Maffei Rosa, o ensino integral oferece caminhos inovadores
        para o seu desenvolvimento. Conheça nossos itinerários:</p>

    <div class="swiper itinerariosSwiper">
        <div class="swiper-wrapper">
            <?php foreach ($itinerarios as $itinerario) : ?>
            <div class="swiper-slide itinerario-slide"
                style="background-image: url('<?= $itinerario['imagem_fundo'] ?>');">
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

        <div class="btn-itinerarios-swiper swiper-button-next"></div>
        <div class="btn-itinerarios-swiper swiper-button-prev"></div>

        <div class="swiper-pagination"></div>
    </div>

    <hr class="my-5">

    <section class="comentarios-section">
        <h2 class="section-title">O que nossos alunos dizem sobre nós</h2>
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
            <div class="btn-comentarios-swiper swiper-button-next"></div>
            <div class="btn-comentarios-swiper swiper-button-prev"></div>

            <div class="swiper-pagination"></div>
        </div>
    </section>

    <hr class="my-5">

    <section class="instagram-section">
        <h2 class="section-title">Fique por dentro das novidades no Instagram</h2>
        <p class="text-center mb-4 ">Acompanhe nosso dia a dia, eventos e projetos através das nossas últimas
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