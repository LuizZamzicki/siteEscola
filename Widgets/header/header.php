<?php
class Header
{

    private static function is_active($page_name, $current_page)
    {
        return ($page_name === basename($current_page, '.php')) ? 'active' : '';
    }

    private static function is_dropdown_active($dropdown_pages, $current_page)
    {
        foreach ($dropdown_pages as $page)
        {
            if ($page === $current_page)
                return 'active';
        }
        return '';
    }

    public static function getHeaderSiteEscola($pagina_atual): void
    {

        ?>

        <nav class="navbar navbar-site-escola navbar-expand-lg custom-navbar-bg">
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
                            <a class="nav-link <?= Header::is_active("home", $pagina_atual) ?>" aria-current="page"
                                href="home">Início</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= Header::is_dropdown_active(['integral', 'detalhe_itinerario', 'listar_itinerarios', 'estrutura', 'nossa_equipe', 'historia'], $pagina_atual) ?>"
                                href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                A escola
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item <?= Header::is_active("integral", $pagina_atual) ?>"
                                        href="integral">Integral</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item <?= Header::is_dropdown_active(['detalhe_itinerario', 'listar_itinerarios'], $pagina_atual) ?>"
                                        href="listar_itinerarios">Itinerários</a></li>
                                <li><a class="dropdown-item <?= Header::is_active("estrutura", $pagina_atual) ?>"
                                        href="estrutura">Estrutura</a>
                                </li>
                                <li><a class="dropdown-item <?= Header::is_active("nossa_equipe", $pagina_atual) ?>"
                                        href="nossa_equipe">Nossa
                                        Equipe</a></li>
                                <li><a class="dropdown-item <?= Header::is_active("historia", $pagina_atual) ?>"
                                        href="historia">Nossa
                                        História</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= Header::is_active("contato", $pagina_atual) ?>" href="contato">Contato</a>
                        </li>
                        <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                            <a href="dashboard" class="btn btn-amarelo">
                                Área do Aluno
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <?php

    }

    public static function getHeaderAreaAluno(string $pagina_atual)
    {
        $param = $_GET['param'] ?? 'area_aluno';
        // Adicionado para notificações
        if (!isset($_SESSION['user_id']))
        {
            // Early return or redirect if user is not logged in
            return;
        }
        require_once BASE_PATH . 'core/services/NotificacaoService.php';
        require_once BASE_PATH . 'Widgets/header/notificacoes.php';
        $notificacaoService = new NotificacaoService();

        // Busca o número total de notificações não lidas para o contador.
        $unreadCount = $notificacaoService->countNotificacoesNaoLidas($_SESSION['user_id']);
        // Busca as notificações mais recentes para exibir na lista (com um limite maior).
        $notificacoes = $notificacaoService->getNotificacoesNaoLidas($_SESSION['user_id'], 20);
        $titles = [
            'area_aluno' => 'Início',
            'biblioteca_aluno' => 'Biblioteca',
            'notas_aluno' => 'Notas e Faltas',
            'horario_aluno' => 'Horário',
            'calendario_aluno' => 'Calendário',
            'perfil_aluno' => 'Meu Perfil',
        ];
        $pageTitle = $titles[$param] ?? 'Início';
        ?>

        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0">
            <div class="flex items-center">
                <button id="menu-toggle" class="lg:hidden mr-4 text-slate-600 hover:text-purple-600">
                    <i class="fa-solid fa-bars w-6 h-6"></i>
                </button>
                <h1 class="text-2xl font-semibold text-slate-800" id="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
            </div>
            <div class="flex items-center gap-6">
                <?php NotificacoesWidget::render($notificacoes, $unreadCount, ['showModalLink' => false]); ?>
                <!-- Profile Dropdown -->
                <div class="relative">
                    <button id="user-menu-button"
                        class="block rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <img src="<?= $_SESSION['user_avatar'] ?? 'https://placehold.co/40x40/c4b5fd/4f46e5?text=' . strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?>"
                            alt="Avatar do Aluno" class="w-10 h-10 rounded-full object-cover">
                    </button>

                    <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-2xl border border-slate-200 z-30
                               transform opacity-0 -translate-y-2 transition-all duration-200 ease-in-out">
                        <div class="p-4 border-b border-slate-200">
                            <div class="flex items-center gap-3">
                                <img src="<?= $_SESSION['user_avatar'] ?? 'https://placehold.co/48x48/c4b5fd/4f46e5?text=' . strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?>"
                                    alt="Avatar do Aluno" class="w-12 h-12 rounded-full object-cover">
                                <div>
                                    <p class="font-semibold text-sm text-slate-800">
                                        <?= htmlspecialchars($_SESSION['user_name'] ?? 'Aluno') ?>
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        <?= htmlspecialchars($_SESSION['user_turma'] ?? 'Não enturmado') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-2">
                            <a href="?param=perfil_aluno"
                                class="block w-full text-left px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 rounded-md transition-colors">
                                <i class="fa-solid fa-user-circle w-5 mr-2 text-slate-500"></i>
                                Meu Perfil
                            </a>
                            <a href="?param=logout"
                                class="block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md transition-colors">
                                <i class="fa-solid fa-right-from-bracket w-5 mr-2"></i>
                                Sair
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <?php
        FuncoesUtils::adicionarJs('Widgets/header/header.js');
    }


    public static function getHeaderAreaAdm(string $pagina_atual)
    { ?>
        <?php
        if (!isset($_SESSION['user_id']))
            return; // Prevenção: Não renderiza se não estiver logado
        require_once BASE_PATH . 'core/services/NotificacaoService.php';
        require_once BASE_PATH . 'Widgets/header/notificacoes.php';
        $notificacaoService = new NotificacaoService();

        // Busca o número total de notificações não lidas para o contador.
        $unreadCount = $notificacaoService->countNotificacoesNaoLidas($_SESSION['user_id']);
        // Busca as notificações mais recentes para exibir na lista (com um limite maior).
        $notificacoes = $notificacaoService->getNotificacoesNaoLidas($_SESSION['user_id'], 20);

        $param = $_GET['param'] ?? 'dashboard';
        $titles = [
            'dashboard' => 'Dashboard',
            // Biblioteca
            'biblioteca' => 'Gerenciar Biblioteca',
            'autores_biblioteca' => 'Autores',
            'editoras_biblioteca' => 'Editoras',
            'generos_biblioteca' => 'Gêneros',
            // Avisos
            'avisos' => 'Avisos e Anúncios',
            // Cadastros
            'alunos' => 'Alunos',
            'turmas' => 'Turmas',
            'periodos' => 'Períodos',
            'professores' => 'Professores',
            'horarios_alunos' => 'Gerenciamento de Horários',
            'horarios_config' => 'Configuração de Horários',
            'materias' => 'Matérias',
            'cardapios' => 'Cardápios',
            // Conteúdo do Site
            'gerenciar_historia' => 'Gerenciar História',
            'gerenciar_estrutura' => 'Gerenciar Estrutura',
            'gerenciar_faq' => 'Gerenciar FAQ',
            'gerenciar_comentarios' => 'Gerenciar Comentários',
            // Outros
            'calendario' => 'Calendário',
            'usuarios' => 'Usuários',
        ];
        $pageTitle = $titles[$param] ?? 'Dashboard';
        ?>
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-6">
            <div class="flex items-center">
                <button id="menu-toggle" class="lg:hidden mr-4 text-slate-600 hover:text-purple-600">
                    <i class="fa-solid fa-bars w-6 h-6"></i>
                </button>
                <h1 class="text-2xl font-semibold text-slate-800" id="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
            </div>
            <div class="flex items-center gap-6">
                <?php NotificacoesWidget::render($notificacoes, $unreadCount, ['showModalLink' => true]); ?>
                <!-- Profile Dropdown -->
                <div class="relative">
                    <button id="user-menu-button"
                        class="block rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <img id="user-avatar"
                            src="<?= $_SESSION['user_avatar'] ?? 'https://placehold.co/40x40/d8b4fe/ffffff?text=' . strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?>"
                            alt="Avatar do Usuário" class="w-10 h-10 rounded-full object-cover">
                    </button>

                    <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-2xl border border-slate-200 z-30
                               transform opacity-0 -translate-y-2 transition-all duration-200 ease-in-out">
                        <div class="p-4 border-b border-slate-200">
                            <div class="flex items-center gap-3">
                                <img src="<?= $_SESSION['user_avatar'] ?? 'https://placehold.co/48x48/d8b4fe/ffffff?text=' . strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?>"
                                    alt="Avatar do Usuário" class="w-12 h-12 rounded-full object-cover">
                                <div>
                                    <p id="user-name" class="font-semibold text-sm text-slate-800">
                                        <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuário') ?>
                                    </p>
                                    <p id="user-email" class="text-xs text-slate-500">
                                        <?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-2">
                            <a href="?param=logout"
                                class="block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md transition-colors">
                                <i class="fa-solid fa-right-from-bracket w-5 mr-2"></i>
                                Sair
                            </a>
                        </div>
                    </div>
                </div>
        </header>
        <?php
        FuncoesUtils::adicionarJs('Widgets/header/header.js');
    }
}