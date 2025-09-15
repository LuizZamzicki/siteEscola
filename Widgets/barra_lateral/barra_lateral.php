<?php
class BarraLateral
{
    /**
     * Renderiza a estrutura base da barra lateral com base em uma configuração.
     *
     * @param array $config As configurações para a barra lateral.
     */
    private static function _render_sidebar(array $config): void
    {
        FuncoesUtils::adicionarJs(caminho: 'Widgets/barra_lateral/barra_lateral.js');
        $current_param = $config['current_param'];
        ?>
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-10 lg:hidden hidden transition-opacity duration-300"></div>
        <aside id="sidebar"
            class="w-64 bg-[#401F52] text-white flex flex-col flex-shrink-0 transition-transform duration-300 ease-in-out fixed lg:relative h-full -translate-x-full lg:translate-x-0 z-20">
            
            <!-- Cabeçalho -->
            <div class="flex items-center justify-center h-20 border-b border-purple-900/20">
                <div class="flex items-center gap-3">
                    <?= $config['title_icon_html'] ?>
                    <span class="text-xl font-bold text-white"><?= htmlspecialchars($config['title']) ?></span>
                </div>
            </div>

            <!-- Links de Navegação -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <?php foreach ($config['nav_links'] as $param => $info):
                    if (isset($info['children'])):
                        $is_parent_active = false;
                        foreach ($info['children'] as $child_param => $child_info) {
                            if ($child_param === $current_param) {
                                $is_parent_active = true;
                                break;
                            }
                        }
                        $parent_active_classes = $is_parent_active ? $config['active_classes'] : $config['inactive_classes'];
                        $submenu_hidden_class = $is_parent_active ? '' : 'hidden';
                        ?>
                        <div class="relative">
                            <a href="#" data-submenu-toggle="<?= $param ?>"
                                class="nav-link flex items-center justify-between px-4 py-2.5 rounded-lg transition duration-200 <?= $parent_active_classes ?>">
                                <div><i class="fa-solid <?= $info['icon'] ?> mr-3 w-5 text-center"></i><span><?= htmlspecialchars($info['label']) ?></span></div>
                                <i class="fa-solid fa-chevron-down transition-transform duration-200 <?= $is_parent_active ? 'rotate-180' : '' ?>"></i>
                            </a>
                            <div id="submenu-<?= $param ?>" class="pl-8 mt-1 space-y-1 <?= $submenu_hidden_class ?>">
                                <?php foreach ($info['children'] as $child_param => $child_info): ?>
                                    <a href="?param=<?= $child_param ?>" <?= $config['include_data_page_attr'] ? 'data-page="' . $child_param . '"' : '' ?>
                                        class="nav-link flex items-center px-4 py-2 rounded-lg transition duration-200 <?= self::_get_active_classes($child_param, $current_param, $config['active_classes'], $config['inactive_classes']) ?>">
                                        <i class="fa-solid <?= $child_info['icon'] ?> mr-3 w-5 text-center"></i>
                                        <span><?= htmlspecialchars($child_info['label']) ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="?param=<?= $param ?>"
                            <?php if ($config['include_data_page_attr']): ?> data-page="<?= $param ?>" <?php endif; ?>
                            class="nav-link flex items-center px-4 py-2.5 rounded-lg transition duration-200 <?= self::_get_active_classes($param, $current_param, $config['active_classes'], $config['inactive_classes']) ?>">
                            <i class="fa-solid <?= $info['icon'] ?> mr-3 w-5 text-center"></i>
                            <span><?= htmlspecialchars($info['label']) ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>

            <!-- Rodapé -->
            <div class="px-4 py-4 border-t border-purple-900/20 <?= $config['footer_wrapper_classes'] ?>">
                <?php foreach ($config['footer_links'] as $param => $info): ?>
                    <?php
                    // Permite que links do rodapé (como 'Sair') tenham suas próprias classes de estilo
                    $active_classes = $info['active_classes'] ?? $config['active_classes'];
                    $inactive_classes = $info['inactive_classes'] ?? $config['inactive_classes'];
                    ?>
                    <a href="?param=<?= $param ?>"
                        class="nav-link flex items-center px-4 py-2.5 rounded-lg transition duration-200 <?= self::_get_active_classes($param, $current_param, $active_classes, $inactive_classes) ?>">
                        <i class="fa-solid <?= $info['icon'] ?> mr-3 w-5 text-center"></i>
                        <span><?= htmlspecialchars($info['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </aside>
        <?php
    }

    /**
     * Retorna as classes CSS para um link com base se ele está ativo ou não.
     *
     * @param string $link_param O parâmetro do link.
     * @param string $current_param O parâmetro da página atual.
     * @param string $active_classes Classes para o estado ativo.
     * @param string $inactive_classes Classes para o estado inativo.
     * @return string
     */
    private static function _get_active_classes(string $link_param, string $current_param, string $active_classes, string $inactive_classes): string
    {
        return ($link_param === $current_param) ? $active_classes : $inactive_classes;
    }

    /**
     * Renderiza a barra lateral para a área administrativa.
     */
    public static function getBarraLateral($pagina_atual)
    {
        $current_param = $_GET['param'] ?? 'dashboard';
        $user_role = $_SESSION['user_role'] ?? 'none';

        $all_links = [
            'dashboard' => ['icon' => 'fa-house', 'label' => 'Dashboard'],
            'biblioteca' => [
                'label' => 'Biblioteca',
                'icon' => 'fa-book-open',
                'children' => [
                    'biblioteca' => ['icon' => 'fa-book-bookmark', 'label' => 'Gerenciar Biblioteca'],
                    'autores_biblioteca' => ['icon' => 'fa-user-edit', 'label' => 'Autores'],
                    'editoras_biblioteca' => ['icon' => 'fa-building', 'label' => 'Editoras'],
                    'generos_biblioteca' => ['icon' => 'fa-tags', 'label' => 'Gêneros'],
                ]
            ],
            'avisos' => ['icon' => 'fa-bullhorn', 'label' => 'Avisos e Anúncios'],
            'cadastros' => [
                'label' => 'Cadastros',
                'icon' => 'fa-edit',
                'children' => [
                    'alunos' => ['icon' => 'fa-users', 'label' => 'Alunos'],
                    'turmas' => ['icon' => 'fa-layer-group', 'label' => 'Turmas'],
                    'periodos' => ['icon' => 'fa-sun', 'label' => 'Períodos'],
                    'professores' => ['icon' => 'fa-user-check', 'label' => 'Professores'],
                    'horarios_alunos' => ['icon' => 'fa-clock', 'label' => 'Horários Alunos'],
                    'materias' => ['icon' => 'fa-book-journal-whills', 'label' => 'Matérias'],
                    'cardapios' => ['icon' => 'fa-utensils', 'label' => 'Cardápios'],
                ]
            ],
            'conteudo_site' => [
                'label' => 'Conteúdo do Site',
                'icon' => 'fa-globe',
                'children' => [
                    'gerenciar_historia' => ['icon' => 'fa-scroll', 'label' => 'História'],
                    'gerenciar_estrutura' => ['icon' => 'fa-building-columns', 'label' => 'Estrutura'],
                    'gerenciar_faq' => ['icon' => 'fa-question-circle', 'label' => 'FAQ Integral'],
                    'gerenciar_comentarios' => ['icon' => 'fa-comments', 'label' => 'Comentários'],
                ]
            ],
            'calendario' => ['icon' => 'fa-calendar-days', 'label' => 'Calendário'],
            'usuarios' => ['icon' => 'fa-user', 'label' => 'Usuários'],
        ];

        $navItemsVisibility = [
            'Super Admin' => [
                'dashboard', 'biblioteca', 'avisos', 'cadastros', 'conteudo_site', 'calendario', 'usuarios',
                'autores_biblioteca', 'editoras_biblioteca', 'generos_biblioteca',
                'alunos', 'turmas', 'professores', 'horarios_alunos', 'materias', 'cardapios', 'periodos',
                'gerenciar_historia', 'gerenciar_estrutura', 'gerenciar_faq', 'gerenciar_comentarios'
            ],
            'Secretaria' => [
                'dashboard', 'avisos', 'cadastros', 'calendario',
                'alunos', 'turmas', 'professores', 'horarios_alunos', 'materias', 'cardapios', 'periodos',
            ],
            'Professor' => ['dashboard', 'avisos', 'turmas', 'calendario'],
            'Bibliotecario' => [
                'dashboard', 'biblioteca', 'avisos',
                'autores_biblioteca', 'editoras_biblioteca', 'generos_biblioteca',
            ],
        ];

        $allowed_keys = $navItemsVisibility[$user_role] ?? [];
        $nav_links = array_filter($all_links, fn($key) => in_array($key, $allowed_keys), ARRAY_FILTER_USE_KEY);

        // Filtra os submenus para garantir que apenas links permitidos sejam exibidos
        // e remove menus pais que ficarem vazios.
        foreach ($nav_links as $key => &$link) {
            if (isset($link['children'])) {
                $link['children'] = array_filter(
                    $link['children'],
                    fn($child_key) => in_array($child_key, $allowed_keys),
                    ARRAY_FILTER_USE_KEY
                );
                if (empty($link['children'])) {
                    unset($nav_links[$key]);
                }
            }
        }
        unset($link); // Desfaz a referência da última iteração

        $config = [
            'current_param' => $current_param,
            'title' => 'Admin J.M.R.',
            'title_icon_html' => '<svg class="h-8 w-8 text-[#F2C94C]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L1 9l4 2.18v6.32L12 22l7-4.5V11.18L23 9l-3-1.68V5h-2v2.32L12 2zM12 4.15L19.88 9 12 13.85 4.12 9 12 4.15zM7 12.18v3.64l5 2.73 5-2.73v-3.64L12 15.82 7 12.18z" /></svg>',
            'nav_links' => $nav_links,
            'footer_links' => [
                'logout' => [
                    'icon' => 'fa-right-from-bracket',
                    'label' => 'Sair',
                    'inactive_classes' => 'text-slate-300 hover:bg-red-500/80 hover:text-white',
                    'active_classes' => 'text-slate-300 hover:bg-red-500/80 hover:text-white' // Nunca fica ativo
                ]
            ],
            'active_classes' => 'bg-purple-600 text-white font-semibold',
            'inactive_classes' => 'text-slate-300 hover:bg-purple-700/50 hover:text-white',
            'include_data_page_attr' => true,
            'footer_wrapper_classes' => ''
        ];

        self::_render_sidebar($config);
    }

    /**
     * Renderiza a barra lateral para a área do aluno.
     */
    public static function getBarraLateralAluno()
    {
        $current_param = $_GET['param'] ?? 'area_aluno';

        $config = [
            'current_param' => $current_param,
            'title' => 'Portal do Aluno',
            'title_icon_html' => '<div class="w-8 h-8 text-[#F2C94C] text-2xl flex items-center justify-center"><i class="fa-solid fa-graduation-cap"></i></div>',
            'nav_links' => [
                'area_aluno' => ['icon' => 'fa-house', 'label' => 'Início'],
                'biblioteca_aluno' => ['icon' => 'fa-book-open', 'label' => 'Biblioteca'],
                'notas_aluno' => ['icon' => 'fa-award', 'label' => 'Notas e Faltas'],
                'horario_aluno' => ['icon' => 'fa-clock', 'label' => 'Horário'],
                'calendario_aluno' => ['icon' => 'fa-calendar-days', 'label' => 'Calendário'],
            ],
            'footer_links' => [
                'perfil_aluno' => [
                    'icon' => 'fa-user-circle',
                    'label' => 'Meu Perfil'
                ],
                'logout' => [
                    'icon' => 'fa-solid fa-right-from-bracket',
                    'label' => 'Sair',
                    'inactive_classes' => 'text-slate-300 hover:bg-red-500/80 hover:text-white',
                    'active_classes' => 'text-slate-300 hover:bg-red-500/80 hover:text-white' // Nunca fica ativo
                ]
            ],
            'active_classes' => 'bg-[#F2C94C] text-slate-900 font-semibold',
            'inactive_classes' => 'text-slate-300 hover:bg-purple-900/30',
            'include_data_page_attr' => false,
            'footer_wrapper_classes' => 'space-y-2'
        ];

        self::_render_sidebar($config);
    }
}
