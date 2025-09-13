<?php
session_start();

define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR);

require_once BASE_PATH . 'Utils/funcoesUtils.php';
require_once BASE_PATH . 'vendor/autoload.php';
require_once BASE_PATH . 'Widgets/barra_lateral/barra_lateral.php';
require_once BASE_PATH . 'Widgets/header/header.php';
require_once BASE_PATH . 'Widgets/footer/footer.php';
require_once BASE_PATH . 'Widgets/botoes/botoes.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- Controle de Sessão e Roteamento ---
$base = $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
$param = $_GET['param'] ?? 'home'; // Default para a home do site

// Rotas que não precisam de login
$publicRoutes = ['home', 'integral', 'itinerarios', 'itinerarios/detalhes', 'historia', 'estrutura', 'nossa_equipe', 'contato', 'login', 'google-auth', 'google-callback'];

// Se a rota não é pública e o usuário não está logado, redireciona para o login
if (!in_array($param, $publicRoutes) && !isset($_SESSION['user_id']))
{
    // Comportamento padrão: Redireciona para a página de login central.
    header('Location: ?param=login');
    exit();
}

// Se o usuário está logado e tenta acessar a página de login, redireciona para o dashboard
if ($param === 'login' && isset($_SESSION['user_id']))
{
    $redirect = ($_SESSION['user_role'] ?? 'Aluno') === 'Aluno' ? '?param=area_aluno' : '?param=dashboard';
    header('Location: ' . $redirect);
    exit();
}

$pagina_atual = FuncoesUtils::resolvePagina($param);

// --- Verificação de Permissão de Acesso por Papel ---
if (isset($_SESSION['user_id']))
{
    $user_role = $_SESSION['user_role'];

    // Se for um aluno tentando acessar a área administrativa
    if ($user_role === 'Aluno' && str_contains($pagina_atual, "Features_Area_Adm"))
    {
        header('Location: ?param=area_aluno');
        exit();
    }

    // Se for um administrador tentando acessar a área do aluno
    if ($user_role !== 'Aluno' && str_contains($pagina_atual, "Features_Area_Aluno"))
    {
        header('Location: ?param=dashboard');
        exit();
    }
}

$caminho_pasta = dirname($pagina_atual);

ob_start();
include $pagina_atual;

$conteudo_da_pagina = ob_get_clean();
$titulo = "Maffei";

?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $titulo ?></title>
    <link rel="icon" href="imagens/logo.png" type="image/png">
    <link rel="shortcut icon" href="imagens/logo.png" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Gabriela&display=swap">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="Assets/global.css">
    <?php if (str_contains($pagina_atual, "Features_Site"))
        echo "<link rel=\"stylesheet\" href=\"Assets/bootstrap.min.css\">";
    ?>
    <?php if (str_contains($pagina_atual, "Features_Area_Adm") || str_contains($pagina_atual, "Features_Area_Aluno")): ?>
        <script src="https://cdn.tailwindcss.com"></script>
    <?php endif; ?>
    <?php if (str_contains($pagina_atual, "Features_Area_Aluno")): ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <?php endif; ?>
    <?php FuncoesUtils::renderizarCss(); ?>

</head>



<?php if (str_contains($pagina_atual, "Features_Area_Adm"))
{

    $titulo = "Painel do Administrador - Escola";

    ?>

    <body class="bg-slate-50 text-slate-800">
        <div class="flex h-screen overflow-hidden">

            <?= BarraLateral::getBarraLateral($pagina_atual) ?>
            <div class="flex-1 flex flex-col overflow-hidden">
                <?= Header::getHeaderAreaAdm($pagina_atual) ?>
                <div class="flex-1 flex flex-col overflow-y-auto">
                    <main id="main-content" class="flex-grow bg-slate-100 p-1 sm:p-6 lg:p-8">
                        <?= $conteudo_da_pagina; ?>
                    </main>
                    <?= Footer::getFooterAreaInterna() ?>
                </div>
            </div>
        </div>
        <script src="js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <script async src="//www.instagram.com/embed.js"></script>
        <script src="Features_Area_Adm/home/admin.js"></script>
        <?php FuncoesUtils::renderizarJs() ?>
    </body>
    <?php
}
else if (str_contains($pagina_atual, "Features_Area_Aluno"))
{

    $titulo = "Painel do Aluno - Escola";

    // --- Preparação de Dados para o Aluno ---
    $studentScheduleData = [
        'full' => [],
        'today' => [],
    ];

    if (isset($_SESSION['user_turma_id']) && $_SESSION['user_turma_id'] > 0)
    {
        require_once BASE_PATH . 'core/services/HorarioService.php';
        $horarioService = new HorarioService();

        // Adicionado para buscar o ID do período do aluno
        require_once BASE_PATH . 'core/services/database.php';
        $db = new Database();
        $turmaInfo = $db->query("SELECT periodo FROM turmas WHERE id_turma = :id_turma", [':id_turma' => $_SESSION['user_turma_id']]);
        $periodoId = null;
        if (!empty($turmaInfo))
        {
            $periodoNome = $turmaInfo[0]['periodo'];
            $periodoInfo = $db->query("SELECT id FROM periodos WHERE nome = :nome", [':nome' => $periodoNome]);
            if (!empty($periodoInfo))
            {
                $periodoId = $periodoInfo[0]['id'];
            }
        }

        // Busca a grade horária completa da turma do aluno
        $rawSchedule = $horarioService->buscarHorarioCompletoPorTurma($_SESSION['user_turma_id']);

        // Busca a configuração de horários (blocos de aula e intervalos) do banco de dados
        $timeSlotsConfig = $horarioService->buscarTodosHorariosConfig($periodoId);

        $daysOfWeek = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira'];

        // 1. Monta a estrutura para o horário completo
        foreach ($timeSlotsConfig as $slot)
        {
            $horarioStr = date('H:i', strtotime($slot['horario_inicio'])) . ' - ' . date('H:i', strtotime($slot['horario_fim']));
            $row = ['time' => $horarioStr];

            if ($slot['tipo'] === 'intervalo')
            {
                $row['type'] = $slot['label'] ?: 'Intervalo';
            }
            else
            {
                foreach ($daysOfWeek as $day)
                {
                    // A chave em $rawSchedule é 'Segunda-feira', 'Terça-feira', etc.
                    // A chave do horário é '07:30 - 08:20'
                    $row[$day] = $rawSchedule[$day][$horarioStr]['materia'] ?? null;
                }
            }
            $studentScheduleData['full'][] = $row;
        }

        // 2. Monta a estrutura para o horário de hoje
        $diasDaSemanaMap = [1 => 'Segunda-feira', 2 => 'Terça-feira', 3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira'];
        $todayIndex = date('N'); // 'N' retorna 1 para Segunda, 7 para Domingo

        if (isset($diasDaSemanaMap[$todayIndex]))
        {
            $todayName = $diasDaSemanaMap[$todayIndex];

            foreach ($timeSlotsConfig as $slot)
            {
                $horarioStr = date('H:i', strtotime($slot['horario_inicio'])) . ' - ' . date('H:i', strtotime($slot['horario_fim']));
                $subject = null;
                if ($slot['tipo'] === 'intervalo')
                {
                    $subject = $slot['label'] ?: 'Intervalo';
                }
                else if (isset($rawSchedule[$todayName][$horarioStr]))
                {
                    $subject = $rawSchedule[$todayName][$horarioStr]['materia'] ?? null;
                }

                if ($subject)
                    $studentScheduleData['today'][] = ['time' => $horarioStr, 'subject' => $subject];
            }
        }
    }
    ?>

        <body class="bg-slate-50 text-slate-800">
            <div class="flex h-screen overflow-hidden">

            <?= BarraLateral::getBarraLateralAluno() ?>
                <div class="flex-1 flex flex-col overflow-hidden">
                <?= Header::getHeaderAreaAluno($pagina_atual) ?>
                    <div class="flex-1 flex flex-col overflow-y-auto">
                        <main id="main-content" class="flex-grow bg-slate-100 p-1 sm:p-6 lg:p-8">
                        <?= $conteudo_da_pagina; ?>
                        </main>
                    <?= Footer::getFooterAreaInterna() ?>
                    </div>
                </div>
            </div>
            <script src="js/bootstrap.bundle.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
            <script async src="//www.instagram.com/embed.js"></script>
            <script>
                var studentData = studentData || {};
                studentData.schedule = <?= json_encode($studentScheduleData, JSON_UNESCAPED_UNICODE) ?>;
            </script>
            <script src="Features_Area_Aluno/area_aluno.js"></script>
        <?php FuncoesUtils::renderizarJs() ?>
        </body>
    <?php
}
else
{

    $titulo = "Maffei";
    ?>

        <body class="body_site_escola">
            <?php
            Header::getHeaderSiteEscola($pagina_atual)
            ?>
            <main class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-md-12">
                    <?= $conteudo_da_pagina ?>
                    </div>
                </div>
            </main>
            <?php
            Footer::getFooterSiteEscola();
            ?>
            <script src="js/bootstrap.bundle.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
            <script async src="//www.instagram.com/embed.js"></script>
            <script src="js/main.js"></script>

        <?php FuncoesUtils::renderizarJs() ?>
        </body>

    <?php
}

?>


</html>