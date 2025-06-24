<?php

    $base = $_SERVER['SERVER_NAME']. $_SERVER['SCRIPT_NAME'];

    $pagina_atual = $_GET['param'] ?? "home";

    

    // Função auxiliar para verificar se um link está ativo    
    function is_active($page_name, $current_page) {
        return ($page_name === $current_page) ? 'active' : '';
    }

    // Função auxiliar para verificar se um link dropdown tem um item ativo    
    function is_dropdown_active($dropdown_pages, $current_page) {
        foreach ($dropdown_pages as $page) {
            if ($page === $current_page) {
                return 'active'; 
            }
        }
        return '';
    }


?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maffei</title>
    <base href="http://<?=$base?>">
    <link rel="icon" href="imagens/logo.png" type="image/png">
    <link rel="shortcut icon" href="imagens/logo.png" type="image/png">
    <link rel=" stylesheet" href="css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Gabriela&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary custom-navbar-bg py-3">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="home">
                <img src="imagens/logo.png" alt="Logo Maffei" class="navbar-logo me-2">
                <span class="navbar-title">João Maffei Rosa</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item">
                        <a class="nav-link <?=is_active("home", $pagina_atual)?>" aria-current="page"
                            href="home">Início</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= is_dropdown_active(['integral', 'detalhe_itinerario', 'listar_itinerarios', 'estrutura', 'nossa_equipe', 'historia'], $pagina_atual) ?>"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            A escola
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?=is_active("integral", $pagina_atual)?>"
                                    href="integral">Integral</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item <?= is_dropdown_active(['detalhe_itinerario', 'listar_itinerarios'], $pagina_atual) ?>"
                                    href="listar_itinerarios">Itinerários</a></li>
                            <li><a class="dropdown-item <?=is_active("estrutura", $pagina_atual)?>"
                                    href="estrutura">Estrutura</a></li>
                            <li><a class="dropdown-item <?=is_active("nossa_equipe", $pagina_atual)?>"
                                    href="nossa_equipe">Nossa Equipe</a></li>
                            <li><a class="dropdown-item <?=is_active("historia", $pagina_atual)?>" href="historia">Nossa
                                    História</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?=is_active("contato", $pagina_atual)?>" href="contato">Contato</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a href="integral" class="btn btn-amarelo">
                            Conheça Nosso Integral
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <?php
            $pagina = $_GET['param'] ?? "home";
            $pagina_path = "paginas/$pagina.php";
            if (file_exists($pagina_path)) {
             
                 include $pagina_path;
                
            }
            else {
                include "paginas/erro.php";
            }

            $caminho_script = "js/{$pagina}.js";
  
            if (file_exists($caminho_script)) {
                echo "<script src='$caminho_script'></script>";
                
            }
        ?>

            </div>
        </div>
    </main>
    <footer class="footer mt-auto py-3">
        <div class="container">
            <div class="row align-items-center">
                <div
                    class="col-lg-3 col-md-3 text-center text-lg-start mb-3 mb-lg-0 d-flex justify-content-center align-items-center">
                    <a href="home"><img src="imagens/logo.png" alt="Maffei" class="logo img-fluid"></a>
                </div>
                <div
                    class="col-lg-3 col-md-3 text-center text-md-start mb-3 mb-md-0 d-flex flex-column align-items-center align-items-md-center">

                    <div class="enderecoInfos">
                        <i class="fa-solid fa-location-dot footer-icon me-2">
                        </i>
                        <address class="endereco mb-0 footer-text">
                            <p
                                class="mb-0 footer-text d-flex align-items-center justify-content-center justify-content-md-center">
                                R. Tamôios, 2454 - Centro</p>
                            <p
                                class="mb-0 footer-text d-flex align-items-center justify-content-center justify-content-md-center">
                                Juranda - PR, 87355-000</p>
                        </address>
                    </div>
                </div>


                <div
                    class="col-lg-3 col-md-3 text-center text-md-start mb-3 mb-md-0 d-flex flex-column align-items-center align-items-md-center">
                    <div class="contatoInfos">
                        <p
                            class="mb-0 footer-text d-flex align-items-center justify-content-center justify-content-md-center">
                            <i class="fa-solid fa-phone-alt footer-icon me-2"></i>
                            (44) 3569-1318
                        </p>
                        <p
                            class="mb-0 footer-text d-flex align-items-center justify-content-center justify-content-md-center">
                            <i class="fa-solid fa-envelope footer-icon me-2"></i>
                            jrnjoaorosa@escola.pr.gov.br
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-2 d-flex justify-content-center justify-content-md-end">
                    <div class="redes-card mb-0">
                        <div class="card-body">
                            <h5 class="card-title">Nossas Redes</h5>
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item">
                                    <a href="https://www.instagram.com/colegio_maffei/" target="_blank"
                                        class="social-icon instagram-icon" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Instagram">
                                        <i class="fa-brands fa-instagram"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://www.facebook.com/profile.php?id=212544995436310" target="_blank"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Facebook"
                                        class="social-icon facebook-icon">
                                        <i class="fa-brands fa-facebook-f"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://x.com/maffeirosa" target="_blank" class="social-icon twitter-icon"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Twitter">
                                        <i class="fa-brands fa-x-twitter"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="credito-container py-2 mt-3">
            <div class="container">
                <div class="row">
                    <div class="col text-center">
                        <p class="credito mb-0">
                            <a href="https://www.instagram.com/luiz_zamzicki/" target="_blank"
                                class="credito-link-wrapper">
                                <span>🛠 Desenvolvido por </span>
                                <span>Luiz H. G. Zamzicki</span>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script async src="//www.instagram.com/embed.js"></script>
    <script src="js/main.js"></script>

</body>

</html>