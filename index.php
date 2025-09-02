<?php
define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR);

require_once BASE_PATH . 'Utils\funcoesUtils.php';
require_once BASE_PATH . 'Widgets\header\header_site_escola.php';
require_once BASE_PATH . 'Widgets\footer\footer_site_escola.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$base = $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
$param = $_GET['param'] ?? 'Features_Site/home/home.php';
$pagina_atual = resolvePagina($param);
$caminho_pasta = dirname($pagina_atual);

ob_start();
include $pagina_atual;
$conteudo_da_pagina = ob_get_clean();

?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maffei</title>
    <link rel="icon" href="imagens/logo.png" type="image/png">
    <link rel="shortcut icon" href="imagens/logo.png" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Gabriela&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="Assets/bootstrap.min.css">
    <link rel="stylesheet" href="Assets/global.css">

    <?php renderizarCss(); ?>
</head>

<body>
    <?php getHeaderSiteEscola($pagina_atual); ?>

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <?= $conteudo_da_pagina ?>
            </div>
        </div>
    </main>

    <?php getFooterSiteEscola() ?>

    <script src="js/bootstrap.bundle.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script async src="//www.instagram.com/embed.js"></script>
    <script src="js/main.js"></script>

    <?php renderizarJs() ?>
</body>

</html>