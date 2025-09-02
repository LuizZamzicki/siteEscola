<?php

function getHeaderSiteEscola($pagina_atual): void
{

    ?>


<nav class="navbar navbar-expand-lg bg-body-tertiary custom-navbar-bg py-3">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="home">
            <img src="imagens/logo.png" alt="Logo Maffei" class="navbar-logo me-2">
            <span class="navbar-title">João Maffei Rosa</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?= is_active("home", $pagina_atual) ?>" aria-current="page"
                        href="home">Início</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= is_dropdown_active(['integral', 'detalhe_itinerario', 'listar_itinerarios', 'estrutura', 'nossa_equipe', 'historia'], $pagina_atual) ?>"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        A escola
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item <?= is_active("integral", $pagina_atual) ?>"
                                href="integral">Integral</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item <?= is_dropdown_active(['detalhe_itinerario', 'listar_itinerarios'], $pagina_atual) ?>"
                                href="listar_itinerarios">Itinerários</a></li>
                        <li><a class="dropdown-item <?= is_active("estrutura", $pagina_atual) ?>"
                                href="estrutura">Estrutura</a>
                        </li>
                        <li><a class="dropdown-item <?= is_active("nossa_equipe", $pagina_atual) ?>"
                                href="nossa_equipe">Nossa
                                Equipe</a></li>
                        <li><a class="dropdown-item <?= is_active("historia", $pagina_atual) ?>" href="historia">Nossa
                                História</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= is_active("contato", $pagina_atual) ?>" href="contato">Contato</a>
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

<?php

}

function is_active($page_name, $current_page)
{
    return ($page_name === basename($current_page, '.php')) ? 'active' : '';
}

function is_dropdown_active($dropdown_pages, $current_page)
{
    foreach ($dropdown_pages as $page)
    {
        if ($page === $current_page)
            return 'active';
    }
    return '';
}