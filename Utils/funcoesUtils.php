<?php

class FuncoesUtils
{
    /**
     * @var array<string>
     */
    private static array $css_files = [];
    /**
     * @var array<string>
     */
    private static array $js_files = [];

    /**
     * Retorna o nome do dia da semana em português.
     * @param int $diaSemanaNum O dia da semana (0 para Domingo, 1 para Segunda, ..., 6 para Sábado).
     * @return string O nome do dia da semana.
     */
    public static function getDiaDaSemana(int $diaSemanaNum): string
    {
        $dias = [
            'Domingo',
            'Segunda-feira',
            'Terça-feira',
            'Quarta-feira',
            'Quinta-feira',
            'Sexta-feira',
            'Sábado'
        ];

        return $dias[$diaSemanaNum] ?? 'Dia inválido';
    }

    public static function getCorBase(CoresSistema $corBase): array
    {

        // Mapa de cores para ser a fonte de verdade
        $coresCarrossel = [
            CoresSistema::VERDE->name => [
                'primaria_escuro' => 'var(--corPrimariaVerdeEscuro)',
                'primaria' => 'var(--corPrimariaVerde)',
                'primaria_claro' => 'var(--corPrimariaVerdeClaro)',
            ],
            CoresSistema::ROXO->name => [
                'primaria_escuro' => 'var(--corPrimariaRoxoEscuro)',
                'primaria' => 'var(--corPrimariaRoxo)',
                'primaria_claro' => 'var(--corPrimariaRoxoClaro)',
            ],
            CoresSistema::AMARELO->name => [
                'primaria_escuro' => 'var(--corPrimariaAmareloEscuro)',
                'primaria' => 'var(--corPrimariaAmarelo)',
                'primaria_claro' => 'var(--corPrimariaAmareloClaro)',
            ],
            CoresSistema::AZUL->name => [
                'primaria_escuro' => 'var(--corSecundariaAzulCamisaEscuro)',
                'primaria' => 'var(--corSecundariaAzulCamisa)',
                'primaria_claro' => 'var(--corSecundariaAzulCamisaClaro)',
            ],
        ];
        return $coresCarrossel[$corBase->name] ?? $coresCarrossel[CoresSistema::VERDE->name];
    }

    public static function resolvePagina(string $rota): string
    {
        // Quebra a rota em partes
        $partes = explode('/', trim($rota, '/'));

        // Se não vier nada, manda pra home
        if (empty($partes[0]))
        {
            return 'Features_Site/home/home.php';
        }

        // Mapeamento de rotas "especiais"
        $rotasPersonalizadas = [
            'home' => 'Features_Site/home/home.php',
            'integral' => 'Features_Site/integral/integral.php',
            'itinerarios' => 'Features_Site/itinerarios/itinerario.php',
            'itinerarios/detalhes' => 'Features_Site/itinerarios/detalhes_itinerarios.php',
            'historia' => 'Features_Site/historia/historia.php',
            'estrutura' => 'Features_Site/estrutura/estrutura.php',
            'nossa_equipe' => 'Features_Site/nossa_equipe/nossa_equipe.php',
            'contato' => 'Features_Site/contato/contato.php',
            // Área do Aluno
            'area_aluno' => 'Features_Area_Aluno/area_aluno.php',
            'biblioteca_aluno' => 'Features_Area_Aluno/biblioteca/biblioteca_aluno.php',
            'notas_aluno' => 'Features_Area_Aluno/notas/notas_aluno.php',
            'horario_aluno' => 'Features_Area_Aluno/horario_aluno.php',
            'calendario_aluno' => 'Features_Area_Aluno/calendario_aluno.php',
            'perfil_aluno' => 'Features_Area_Aluno/perfil_aluno.php',

            // Autenticação
            'login' => 'Features_Auth/login.php',
            'google-auth' => 'Features_Auth/google-auth.php',
            'google-callback' => 'Features_Auth/google-callback.php',
            'logout' => 'Features_Auth/logout.php',

            //Area adm
            'dashboard' => 'Features_Area_Adm/home/area_adm.php',
            'alunos' => 'Features_Area_Adm/alunos/cad_alunos.php',
            'avisos' => 'Features_Area_Adm/avisos/avisos.php',
            'biblioteca' => 'Features_Area_Adm/biblioteca/biblioteca.php',
            'horarios_alunos' => 'Features_Area_Adm/horarios/horarios_alunos.php',
            'horarios_config' => 'Features_Area_Adm/horarios/horarios_config.php',
            'autores_biblioteca' => 'Features_Area_Adm/biblioteca/autores_biblioteca.php',
            'editoras_biblioteca' => 'Features_Area_Adm/biblioteca/editoras_biblioteca.php',
            'generos_biblioteca' => 'Features_Area_Adm/biblioteca/generos_biblioteca.php',
            'periodos' => 'Features_Area_Adm/periodos/periodos.php',
            'materias' => 'Features_Area_Adm/materias/materias.php',
            'cardapios' => 'Features_Area_Adm/cardapios/cardapios.php',
            'calendario' => 'Features_Area_Adm/calendario/calendario_adm.php',
            'usuarios' => 'Features_Area_Adm/usuarios/usuarios.php',
            'professores' => 'Features_Area_Adm/professores/professores.php',
            'avancar_ano' => 'Features_Area_Adm/ano_letivo/avancar_ano.php',
            'turmas' => 'Features_Area_Adm/turmas/turmas.php',
        ];

        $rotaChave = implode('/', $partes);

        // Se a rota estiver no array personalizado, retorna ela
        if (isset($rotasPersonalizadas[$rotaChave]))
        {
            return $rotasPersonalizadas[$rotaChave];
        }

        // Se não estiver, tenta procurar o arquivo automaticamente
        $arquivo = $rotaChave . '.php';
        if (file_exists($arquivo))
        {
            return $arquivo;
        }

        // Se não achou nada, página de erro
        return 'Features_Site/erro/erro.php';
    }

    public static function adicionarCss(string $caminho)
    {
        if (!in_array($caminho, self::$css_files))
        {
            self::$css_files[] = $caminho;
        }
    }

    public static function renderizarCss()
    {
        foreach (self::$css_files as $css)
        {
            echo "<link rel='stylesheet' href='$css'>\n";
        }
    }

    public static function adicionarJs(string $caminho)
    {
        if (!in_array($caminho, self::$js_files))
            self::$js_files[] = $caminho;
    }

    public static function renderizarJs()
    {
        foreach (self::$js_files as $js)
        {
            echo "<script src='$js'></script>\n";
        }
    }

    public static function debug_to_console($label, $data)
    {
        $output = json_encode($data);
        echo "<script>console.log('Debug PHP - " . addslashes($label) . ": ', " . $output . ");</script>";
    }
}

enum CoresSistema
{
    case AMARELO;
    case AZUL;
    case ROXO;
    case VERDE;
}