<?php
require_once BASE_PATH . 'core/services/TurmaService.php';
require_once BASE_PATH . 'core/services/PeriodoService.php';

$turmaService = new TurmaService();
$periodoService = new PeriodoService();
$feedbackMessage = '';
$feedbackType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $action = $_POST['action'] ?? '';

    if ($action === 'salvar_turma')
    {
        $serie = $_POST['serie'] ?? '';
        $letra = strtoupper(trim($_POST['letra'] ?? ''));

        $nome_turma = '';
        if (strpos($serie, '(Ensino Médio)') !== false)
        {
            // Insere a letra da turma ANTES de '(Ensino Médio)' para o formato correto.
            // Ex: '1º Ano (Ensino Médio)' + 'A' -> '1º Ano A (Ensino Médio)'
            $nome_turma = str_replace('(Ensino Médio)', ' ' . trim($letra) . ' (Ensino Médio)', $serie);
        }
        else
        {
            // Para o fundamental, apenas anexa a letra no final. Ex: '9º Ano A'
            $nome_turma = trim("$serie $letra");
        }

        // Infere o nível de ensino a partir da série selecionada
        $ensino = (strpos($serie, 'Ensino Médio') !== false) ? 'Ensino Médio' : 'Ensino Fundamental';

        $turmaDTO = new TurmaDTO(
            empty($_POST['id_turma']) ? null : (int)$_POST['id_turma'],
            $nome_turma,
            $ensino,
            $_POST['periodo'] ?? '',
        );
        $feedbackMessage = $turmaService->salvar($turmaDTO) ? "Turma salva com sucesso!" : "Erro ao salvar a turma.";
        if (strpos($feedbackMessage, 'Erro') !== false)
            $feedbackType = 'error';
    }

    if ($action === 'excluir_turma')
    {
        $id = (int)$_POST['id_turma'];
        $errorMessage = '';
        if ($turmaService->excluir($id, $errorMessage))
        {
            $feedbackMessage = "Turma excluída com sucesso!";
        }
        else
        {
            $feedbackMessage = $errorMessage ?: "Erro ao excluir a turma.";
            $feedbackType = 'error';
        }
    }

    $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=turmas&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
    header("Location: " . $redirectUrl);
    exit();
}

$turmas = $turmaService->buscarTodas();
$periodos = $periodoService->buscarTodos();
$feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
?>
<!-- Turmas Content -->
<div>
    <!-- Visualização em Lista (Padrão) -->
    <div id="turmas-list-view">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h2 class="text-xl font-semibold">Gerenciamento de Turmas</h2>
            <?php // Botões para Desktop ?>
            <div class="hidden sm:flex flex-wrap justify-end gap-2">
                <?php Botoes::getBotao('?param=avancar_ano', 'Avançar Ano Letivo', BotoesCores::AZUL, '', altura: 40, icone: 'fa-solid fa-angles-right') ?>
                <?php Botoes::getBotao('', 'Criar Turma', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-plus-circle', type: 'button', classes: 'add-class-btn') ?>
            </div>
        </div>

        <?php // Botões Flutuantes para Mobile
        Botoes::getBotoesFlutuantes([
            [
                'cor' => BotoesCores::VERDE,
                'icone' => 'fa-solid fa-plus text-xl',
                'type' => 'button',
                'classesAdicionais' => 'add-class-btn'
            ],
            [
                'link' => '?param=avancar_ano',
                'cor' => BotoesCores::AZUL,
                'icone' => 'fa-solid fa-angles-right text-xl'
            ]
        ]);
        ?>

        <?php if ($feedbackMessage): ?>
            <div class="<?= $feedbackType === 'success' ? 'bg-green-100 border-green-200 text-green-800' : 'bg-red-100 border-red-200 text-red-800' ?> border px-4 py-3 rounded-lg relative mb-4"
                role="alert">
                <span class="block sm:inline"><?= $feedbackMessage ?></span>
            </div>
        <?php endif; ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($turmas as $turma): ?>
                <div class="class-card bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col justify-between"
                    data-id="<?= $turma->id ?>" data-nome="<?= htmlspecialchars($turma->nome) ?>"
                    data-ensino="<?= htmlspecialchars($turma->ensino) ?>"
                    data-periodo="<?= htmlspecialchars($turma->periodo) ?>">
                    <div>
                        <h3 class="text-lg font-bold text-purple-700"><?= htmlspecialchars($turma->nome) ?></h3>
                        <p class="text-slate-500 text-sm mt-1"><?= htmlspecialchars($turma->ensino) ?></p>
                        <div class="flex items-center gap-4 mt-4 text-sm">
                            <div class="flex items-center gap-2 text-slate-600">
                                <i class="fa-solid fa-users w-4 h-4"></i>
                                <span><?= $turma->totalAlunos ?> Alunos</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-600">
                                <i class="fa-solid fa-user-check w-4 h-4"></i>
                                <span><?= $turma->totalProfessores ?> Professores</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button class="edit-class-btn text-slate-700 hover:text-slate-900 transition p-2"
                            title="Editar Turma">
                            <i class="fa-solid fa-pencil text-lg"></i>
                        </button>
                        <button class="delete-class-btn text-red-500 hover:text-red-600 transition p-2"
                            title="Excluir Turma">
                            <i class="fa-solid fa-trash text-lg"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>


</div>

<!-- Modal Genérico -->
<div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50 hidden overflow-y-auto">
    <div class="bg-white rounded-xl shadow-lg w-11/12 max-w-md p-6 relative">
        <button id="modal-close"
            class="absolute top-3 right-3 text-slate-500 hover:text-red-500 text-xl font-bold">&times;</button>
        <h3 id="modal-title" class="text-xl font-bold mb-4">Título do Modal</h3>
        <div id="modal-content">
            <!-- Conteúdo dinâmico -->
        </div>
    </div>
</div>


<script>
    const modal = document.getElementById('modal');
    const modalTitle = document.getElementById('modal-title');
    const modalContent = document.getElementById('modal-content');
    const modalClose = document.getElementById('modal-close');

    const series = [
        '6º Ano', '7º Ano', '8º Ano', '9º Ano',
        '1º Ano (Ensino Médio)', '2º Ano (Ensino Médio)', '3º Ano (Ensino Médio)'
    ];
    const seriesOptions = series.map(s => `<option value="${s}">${s}</option>`).join('');

    const periodos = <?= json_encode(array_map(fn($p) => ['id' => $p->id, 'nome' => $p->nome], $periodos)) ?>;
    const periodosOptions = periodos.map(p => `<option value="${p.nome}">${p.nome}</option>`).join('');

    modalClose.addEventListener('click', () => modal.classList.add('hidden'));
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.add('hidden'); });

    // Criar Turma
    document.querySelectorAll('.add-class-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            modalTitle.textContent = "Criar Nova Turma";
            modalContent.innerHTML = `
                <form method="POST" class="flex flex-col gap-4">
                    <input type="hidden" name="action" value="salvar_turma">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label for="serie" class="block text-sm font-medium text-slate-700 mb-1">Série</label>
                            <select id="serie" name="serie" class="border border-slate-300 rounded-lg px-3 py-2 w-full" required>
                                <option value="">Selecione a série</option>
                                ${seriesOptions}
                            </select>
                        </div>
                        <div>
                            <label for="letra" class="block text-sm font-medium text-slate-700 mb-1">Turma</label>
                            <input type="text" id="letra" name="letra" placeholder="Ex: A" maxlength="2" class="border border-slate-300 rounded-lg px-3 py-2 w-full uppercase" required>
                        </div>
                    </div>
                    <div>
                        <label for="periodo" class="block text-sm font-medium text-slate-700 mb-1">Período</label>
                        <select name="periodo" class="border border-slate-300 rounded-lg px-3 py-2 w-full" required>
                            <option value="">Selecione o período</option>
                            ${periodosOptions}
                        </select>
                    </div>
                    <button type="submit" class="btn btn-verde" style="height: 40px;"><i class="fa-solid fa-plus-circle me-2"></i><span>Criar Turma</span></button>
                </form>
            `;
            modal.classList.remove('hidden');
        });
    });

    // Editar Turma
    document.querySelectorAll('.edit-class-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const card = e.target.closest('.class-card');
            const id = card.dataset.id;
            const nome = card.dataset.nome;
            const ensino = card.dataset.ensino;
            const periodo = card.dataset.periodo;

            let serieEncontrada = '';
            let letraEncontrada = '';


            const match = nome.match(/^(\d+º Ano)\s*([A-Z]{1,2})\s*(\(Ensino Médio\))?$/);

            if (match) {
                const baseSerie = match[1]; // "1º Ano" ou "9º Ano"
                letraEncontrada = match[2]; // "A" ou "B"
                const sufixo = match[3] || ''; // "(Ensino Médio)" ou ""
                serieEncontrada = (baseSerie + ' ' + sufixo).trim(); // Reconstrói para "1º Ano (Ensino Médio)" ou "9º Ano"
            } else {
                // Fallback para o formato antigo: "1º Ano (Ensino Médio) A"
                for (const s of series) {
                    if (nome.startsWith(s)) {
                        serieEncontrada = s;
                        letraEncontrada = nome.substring(s.length).trim();
                        break;
                    }
                }
            }

            const seriesOptionsEdit = series.map(s => `<option value="${s}" ${s === serieEncontrada ? 'selected' : ''}>${s}</option>`).join('');
            const periodosOptionsEdit = periodos.map(p => `<option value="${p.nome}" ${p.nome === periodo ? 'selected' : ''}>${p.nome}</option>`).join('');

            modalTitle.textContent = `Editar Turma: ${nome}`;
            modalContent.innerHTML = `
                <form method="POST" class="flex flex-col gap-4">
                    <input type="hidden" name="action" value="salvar_turma">
                    <input type="hidden" name="id_turma" value="${id}">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label for="serie" class="block text-sm font-medium text-slate-700 mb-1">Série</label>
                            <select id="serie" name="serie" class="border border-slate-300 rounded-lg px-3 py-2 w-full" required>
                                ${seriesOptionsEdit}
                            </select>
                        </div>
                        <div>
                            <label for="letra" class="block text-sm font-medium text-slate-700 mb-1">Turma</label>
                            <input type="text" id="letra" name="letra" value="${letraEncontrada}" placeholder="Ex: A" maxlength="2" class="border border-slate-300 rounded-lg px-3 py-2 w-full uppercase" required>
                        </div>
                    </div>
                    <div>
                        <label for="periodo" class="block text-sm font-medium text-slate-700 mb-1">Período</label>
                        <select name="periodo" class="border border-slate-300 rounded-lg px-3 py-2 w-full" required>
                            ${periodosOptionsEdit}
                        </select>
                    </div>
                    <button type="submit" class="btn btn-amarelo" style="height: 40px;"><i class="fa-solid fa-floppy-disk me-2"></i><span>Salvar Alterações</span></button>
                </form>
            `;
            modal.classList.remove('hidden');
        });
    });

    // Excluir Turma
    document.querySelectorAll('.delete-class-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const card = e.target.closest('.class-card');
            const turmaNome = card.dataset.nome;
            const turmaId = card.dataset.id;

            modalTitle.textContent = `Excluir Turma: ${turmaNome}`;
            modalContent.innerHTML = `
                <p class="mb-6 text-slate-600">Tem certeza que deseja excluir a turma <strong>${turmaNome}</strong>? Esta ação não pode ser desfeita e pode afetar alunos e professores associados.</p>
                <form id="delete-form" method="POST">
                    <input type="hidden" name="action" value="excluir_turma">
                    <input type="hidden" name="id_turma" value="${turmaId}">
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" class="btn btn-cinza w-full sm:w-auto modal-cancel-btn"><i class="fa-solid fa-xmark me-2"></i><span>Cancelar</span></button>
                        <button type="submit" class="btn btn-vermelho w-full sm:w-auto"><i class="fa-solid fa-trash-can me-2"></i><span>Excluir</span></button>
                    </div>
                </form>
            `;
            modal.classList.remove('hidden');

            modalContent.querySelector('.modal-cancel-btn').addEventListener('click', () => modal.classList.add('hidden'));
        });
    });
</script>