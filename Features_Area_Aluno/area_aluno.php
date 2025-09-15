<?php
require_once BASE_PATH . 'core/services/NotificacaoService.php';
require_once BASE_PATH . 'core/services/CardapioService.php';
require_once BASE_PATH . 'core/services/HorarioService.php';
require_once BASE_PATH . 'core/services/database.php';

$notificacaoService = new NotificacaoService();
$cardapioService = new CardapioService();

$avisos = $notificacaoService->getAvisosRecentes(2); // Pega os 2 avisos mais recentes
$proximosEventos = $notificacaoService->getProximosEventos(7); // Pega eventos dos próximos 7 dias
$cardapioDeHoje = $cardapioService->buscarDeHoje();

// --- Preparação de Dados do Horário de Hoje ---
$todaySchedule = [];
if (isset($_SESSION['user_turma_id']) && $_SESSION['user_turma_id'] > 0)
{
    $horarioService = new HorarioService();
    $db = new Database();

    // Busca o ID do período da turma do aluno
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

    // Busca a configuração de horários (blocos de aula e intervalos)
    $timeSlotsConfig = $horarioService->buscarTodosHorariosConfig($periodoId);

    // Busca a grade horária da turma
    $rawSchedule = $horarioService->buscarHorarioCompletoPorTurma($_SESSION['user_turma_id']);

    // Monta a estrutura para o horário de hoje
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
            {
                $todaySchedule[] = ['time' => $horarioStr, 'subject' => $subject];
            }
        }
    }
}
?>

<script>
    // Garante que o objeto global studentData exista e injeta os dados do horário de hoje.
    var studentData = window.studentData || {};
    studentData.schedule = studentData.schedule || {};
    studentData.schedule.today = <?= json_encode($todaySchedule, JSON_UNESCAPED_UNICODE) ?>;
</script>

<div class="p-6 bg-purple-600 rounded-xl shadow-lg text-white">
    <h2 class="text-3xl font-bold">Olá, <?= htmlspecialchars(explode(' ', $_SESSION['user_name'] ?? 'Aluno')[0]) ?>!
    </h2>
    <p class="mt-1 text-white/90">Bem-vinda de volta ao seu portal. Aqui estão as últimas
        atualizações para você.</p>
</div>

<div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-xl font-semibold mb-4">Mural de Avisos</h3>
            <ul class="space-y-4">
                <?php if (empty($avisos)): ?>
                    <li class="text-center text-slate-500 py-4">Nenhum aviso recente.</li>
                <?php else: ?>
                    <?php foreach ($avisos as $aviso): ?>
                        <li class="p-4 bg-slate-50 rounded-lg flex items-start gap-4">
                            <div class="bg-purple-100 text-purple-700 p-2 rounded-lg">
                                <i class="fa-solid fa-bullhorn w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="font-semibold"><?= htmlspecialchars($aviso->titulo) ?></p>
                                <p class="text-sm text-slate-600 mt-1">
                                    <?= htmlspecialchars(mb_strimwidth($aviso->conteudo, 0, 120, "...")) ?>
                                </p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-xl font-semibold mb-4">Seu Horário de Hoje</h3>
            <ul class="space-y-3" id="today-schedule">
                <!-- O horário de hoje será inserido aqui pelo JavaScript -->
            </ul>
            <div class="mt-4 pt-4 border-t border-slate-200 text-right">
                <a href="?param=horario_aluno"
                    class="text-sm font-semibold text-purple-600 hover:text-purple-800 transition">
                    Ver horário completo <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="space-y-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-xl font-semibold mb-4">Próximos Eventos</h3>
            <ul class="space-y-4">
                <?php if (empty($proximosEventos)): ?>
                    <li class="text-center text-slate-500 py-4">Nenhum evento agendado.</li>
                <?php else: ?>
                    <?php foreach (array_slice($proximosEventos, 0, 2) as $evento): // Limita a 2 eventos ?>
                        <li class="flex items-start gap-3">
                            <div
                                class="bg-purple-100 text-purple-700 p-2 rounded-lg flex flex-col items-center w-12 text-center">
                                <span
                                    class="text-xs font-bold tracking-wide"><?= strtoupper((new DateTime($evento->data_inicio))->format('M')) ?></span>
                                <span class="text-lg font-bold"><?= (new DateTime($evento->data_inicio))->format('d') ?></span>
                            </div>
                            <div>
                                <p class="font-semibold"><?= htmlspecialchars($evento->titulo) ?></p>
                                <p class="text-sm text-slate-500"><?= htmlspecialchars(ucfirst($evento->tipo)) ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-xl font-semibold mb-4">Cardápio do Dia</h3>
            <?php if ($cardapioDeHoje): ?>
                <ul class="space-y-3 text-sm text-slate-600">
                    <?php if ($cardapioDeHoje->lanche_manha): ?>
                        <li>
                            <p class="font-semibold text-slate-800">Lanche da Manhã</p>
                            <p><?= htmlspecialchars($cardapioDeHoje->lanche_manha) ?></p>
                        </li>
                    <?php endif; ?>
                    <li>
                        <p class="font-semibold text-slate-800">Almoço</p>
                        <p><?= htmlspecialchars($cardapioDeHoje->almoco) ?></p>
                    </li>
                    <?php if ($cardapioDeHoje->lanche_tarde): ?>
                        <li>
                            <p class="font-semibold text-slate-800">Lanche da Tarde</p>
                            <p><?= htmlspecialchars($cardapioDeHoje->lanche_tarde) ?></p>
                        </li>
                    <?php endif; ?>
                </ul>
            <?php else: ?>
                <p class="text-center text-slate-500 py-4">Nenhum cardápio cadastrado para hoje.</p>
            <?php endif; ?>
        </div>
    </div>
</div>