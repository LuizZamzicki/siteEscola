<?php
require_once BASE_PATH . 'Utils/FuncoesUtils.php';
FuncoesUtils::adicionarCss('Features_Area_Adm\style.css');
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white p-6 rounded-xl shadow-sm border-t-4 border-blue-400 flex items-center gap-4">
        <div class="bg-blue-100 p-3 rounded-full"><i class="fa-solid fa-users text-blue-500"></i></div>
        <div>
            <p class="text-slate-500 text-sm">Total de Alunos</p>
            <p class="text-2xl font-bold"><?= number_format($stats['total_alunos'], 0, ',', '.') ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border-t-4 border-green-400 flex items-center gap-4">
        <div class="bg-green-100 p-3 rounded-full"><i class="fa-solid fa-book-open-reader text-green-500"></i></div>
        <div>
            <p class="text-slate-500 text-sm">Livros Emprestados</p>
            <p class="text-2xl font-bold"><?= number_format($stats['livros_emprestados'], 0, ',', '.') ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border-t-4 border-amber-400 flex items-center gap-4">
        <div class="bg-amber-100 p-3 rounded-full"><i class="fa-solid fa-bookmark text-amber-500"></i></div>
        <div>
            <p class="text-slate-500 text-sm">Reservas Ativas</p>
            <p class="text-2xl font-bold"><?= number_format($stats['reservas_ativas'], 0, ',', '.') ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border-t-4 border-purple-400 flex items-center gap-4">
        <div class="bg-purple-100 p-3 rounded-full"><i class="fa-solid fa-user-check text-purple-500"></i></div>
        <div>
            <p class="text-slate-500 text-sm">Professores</p>
            <p class="text-2xl font-bold"><?= number_format($stats['total_professores'], 0, ',', '.') ?></p>
        </div>
    </div>
</div>

<!-- Grid principal do conteúdo -->
<div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Coluna Esquerda (Maior) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Mural de Avisos -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-xl font-semibold mb-4">Mural de Avisos</h3>
            <?php if (empty($avisos)): ?>
                <p class="text-center text-slate-500 py-4">Nenhum aviso recente.</p>
            <?php else: ?>
                <ul class="space-y-4">
                    <?php foreach ($avisos as $aviso): ?>
                        <li class="p-4 bg-slate-50 rounded-lg flex items-start gap-4">
                            <div class="bg-purple-100 text-purple-700 p-2 rounded-lg"><i
                                    class="fa-solid fa-bullhorn w-5 h-5"></i></div>
                            <div>
                                <p class="font-semibold"><?= htmlspecialchars($aviso->titulo) ?></p>
                                <p class="text-sm text-slate-600 mt-1">
                                    <?= htmlspecialchars(mb_strimwidth($aviso->conteudo, 0, 120, "...")) ?>
                                </p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="mt-4 pt-4 border-t border-slate-200 text-right">
                    <a href="?param=avisos" class="text-sm font-semibold text-purple-600 hover:text-purple-800 transition">
                        Ver todos os avisos <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Atividades Recentes -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h2 class="text-xl font-semibold mb-4">Atividades Recentes</h2>
            <?php if (empty($atividades)): ?>
                <p class="text-center text-slate-500 py-4">Nenhuma atividade recente para mostrar.</p>
            <?php else: ?>
                <ul class="space-y-4">
                    <?php foreach ($atividades as $atividade): ?>
                        <li class="flex items-start gap-4 p-3 rounded-lg hover:bg-slate-50">
                            <div
                                class="w-10 h-10 flex-shrink-0 rounded-full flex items-center justify-center <?= $atividade->cor_fundo_icone ?>">
                                <i class="<?= $atividade->icone ?> <?= $atividade->cor_icone ?>"></i>
                            </div>
                            <p class="flex-1 text-sm">
                                <?= $atividade->descricao ?>
                            </p>
                            <span class="text-sm text-slate-500"><?= $atividade->getTempoRelativo() ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Coluna Direita (Menor) -->
    <div class="space-y-6">
        <!-- Próximos Eventos -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-xl font-semibold mb-4">Próximos 7 Dias</h3>
            <?php if (empty($proximosEventos)): ?>
                <p class="text-center text-slate-500 py-4">Nenhum evento agendado.</p>
            <?php else: ?>
                <ul class="space-y-4">
                    <?php foreach ($proximosEventos as $evento): ?>
                        <li class="flex items-start gap-3">
                            <div
                                class="bg-purple-100 text-purple-700 p-2 rounded-lg flex flex-col items-center w-14 text-center">
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
                </ul>
            <?php endif; ?>
        </div>

        <!-- Acesso Rápido -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-xl font-semibold mb-4">Acesso Rápido</h3>
            <div class="space-y-3">
                <a href="?param=alunos"
                    class="block w-full text-left bg-slate-50 hover:bg-slate-100 p-3 rounded-lg font-medium text-slate-700 transition">
                    <i class="fa-solid fa-users mr-3 text-blue-500"></i> Gerenciar Alunos
                </a>
                <a href="?param=biblioteca"
                    class="block w-full text-left bg-slate-50 hover:bg-slate-100 p-3 rounded-lg font-medium text-slate-700 transition">
                    <i class="fa-solid fa-book-open mr-3 text-green-500"></i> Gerenciar Biblioteca
                </a>
                <a href="?param=turmas"
                    class="block w-full text-left bg-slate-50 hover:bg-slate-100 p-3 rounded-lg font-medium text-slate-700 transition">
                    <i class="fa-solid fa-layer-group mr-3 text-amber-500"></i> Gerenciar Turmas
                </a>
            </div>
        </div>
    </div>
</div>