<!-- A view espera receber $avisos, $proximosEventos, $cardapioDeHoje, $todaySchedule já preparados pelo controller. -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Coluna Esquerda (Maior) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Avisos Recentes -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-xl font-semibold mb-4">Avisos Recentes</h3>
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

        <!-- Seu Horário de Hoje -->
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

    <!-- Coluna Direita (Menor) -->
    <div class="space-y-6">
        <!-- Próximos Eventos -->
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

        <!-- Cardápio do Dia -->
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