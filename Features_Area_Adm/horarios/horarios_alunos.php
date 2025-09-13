<?php
require_once BASE_PATH . 'core/services/TurmaService.php';
require_once BASE_PATH . 'core/services/HorarioService.php';
require_once BASE_PATH . 'core/services/MateriaService.php';
require_once BASE_PATH . 'core/services/UsuarioService.php';
require_once BASE_PATH . 'core/services/database.php';
require_once BASE_PATH . 'widgets/modal/Modal.php';

FuncoesUtils::adicionarJs('Widgets/modal/modal.js');

$turmaService = new TurmaService();
$horarioService = new HorarioService();
$materiaService = new MateriaService();
$usuarioService = new UsuarioService();
$turmas = $turmaService->buscarTodas();
$feedbackMessage = '';
$feedbackType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $action = $_POST['action'] ?? '';
    $result = [];

    if ($action === 'salvar_horario')
    {
        $turmaId = (int)$_POST['turma_id'];
        $diaSemana = $_POST['dia_semana'];
        $horario = $_POST['horario'];
        $materiaId = (int)($_POST['materia_id'] ?? 0);
        $materiaNome = trim($_POST['materia']);        
        $professorId = (int)$_POST['professor_id'];

       
                if (empty($materiaId) || empty($professorId)) {
            $result = ['success' => false, 'message' => 'Matéria e professor são obrigatórios.'];
        } else {
            $result = $horarioService->salvarAula($turmaId, $diaSemana, $horario, $materiaId, $professorId);
        }
    } elseif ($action === 'excluir_horario') {
        $turmaId = (int)$_POST['turma_id'];
        $diaSemana = $_POST['dia_semana'];
        $horario = $_POST['horario'];
        $result = $horarioService->excluirAula($turmaId, $diaSemana, $horario);
    }

    $feedbackMessage = $result['message'] ?? 'Ação desconhecida.';
    $feedbackType = ($result['success'] ?? false) ? 'success' : 'error';
    $queryParams = $_GET;
    $queryParams['msg'] = urlencode($feedbackMessage);
    $queryParams['type'] = $feedbackType;
    $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?' . http_build_query($queryParams);
    header("Location: " . $redirectUrl);
    exit();
}

$professores = $usuarioService->buscarTodosProfessores();
$materias = $materiaService->buscarTodas(); 

$selectedTurmaId = $_GET['turma_id'] ?? ($turmas[0]->id ?? null);
$feedbackMessage = isset($_GET['msg']) ? htmlspecialchars(urldecode($_GET['msg'])) : '';
$feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
$horariosDaTurma = [];

if ($selectedTurmaId)
{
    // Busca o ID do período da turma selecionada para filtrar os horários corretamente
    $db = new Database();
    $turmaInfo = $db->query("SELECT periodo FROM turmas WHERE id_turma = :id_turma", [':id_turma' => $selectedTurmaId]);
    $periodoId = null;
    if (!empty($turmaInfo)) {
        $periodoNome = $turmaInfo[0]['periodo'];
        $periodoInfo = $db->query("SELECT id FROM periodos WHERE nome = :nome", [':nome' => $periodoNome]);
        if (!empty($periodoInfo)) {
            $periodoId = $periodoInfo[0]['id'];
        }
    }

    $horariosAulasConfig = $horarioService->buscarTodosHorariosConfig($periodoId);
    $horariosDaTurma = $horarioService->buscarHorarioCompletoPorTurma($selectedTurmaId);
} else {
    $horariosAulasConfig = [];
}

$horariosAulas = [];
foreach ($horariosAulasConfig as $config) {
    $horarioStr = date('H:i', strtotime($config['horario_inicio'])) . ' - ' . date('H:i', strtotime($config['horario_fim']));
    if ($config['tipo'] === 'intervalo') {
        $horariosAulas[$horarioStr] = $config['label'] ?: 'Intervalo';
    } else {
        $horariosAulas[] = $horarioStr;
    }
}

$diasDaSemana = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira'];

?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <h2 class="text-xl font-semibold">Gerenciamento de Horários</h2>
        <div class="flex items-center gap-4">
            <a href="?param=horarios_config" class="btn btn-cinza-outline text-sm hidden sm:inline-flex">
                <i class="fa-solid fa-gear mr-2"></i>Configurar Horários
            </a>
            <form method="GET" class="flex items-center gap-2">
                <input type="hidden" name="param" value="horarios_alunos">
                <label for="turma-select" class="text-sm font-medium sr-only">Selecione a Turma:</label>
                <select id="turma-select" name="turma_id" onchange="this.form.submit()"
                    class="border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-400">
                    <?php foreach ($turmas as $turma): ?>
                        <option value="<?= $turma->id ?>" <?= $selectedTurmaId == $turma->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($turma->nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <?php
    // Botão Flutuante para Mobile
    Botoes::getBotoesFlutuantes([
        [
            'link' => '?param=horarios_config',
            'cor' => BotoesCores::CINZA,
            'icone' => 'fa-solid fa-gear text-xl',
            'type' => 'button',
            'classesAdicionais' => 'config-horarios-btn'
        ]
    ]);
    ?>

    <?php if ($feedbackMessage): ?>
        <div class="px-4 py-3 rounded-lg relative <?= $feedbackType === 'success' ? 'bg-green-100 border-green-200 text-green-800' : 'bg-red-100 border-red-200 text-red-800' ?>" role="alert">
            <span class="block sm:inline"><?= $feedbackMessage ?></span>
        </div>
    <?php endif; ?>

    <!-- Mobile View -->
    <div class="md:hidden bg-white rounded-xl shadow-sm border border-slate-200">
        <!-- Tabs -->
        <div class="border-b border-slate-200">
            <nav id="mobile-day-tabs" class="flex -mb-px space-x-4 overflow-x-auto px-4" aria-label="Tabs">
                <?php foreach ($diasDaSemana as $index => $dia): ?>
                    <button data-target-tab="#tab-<?= $index ?>"
                        class="mobile-tab-btn whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm <?= $index === 0 ? 'border-purple-500 text-purple-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?>">
                        <?= htmlspecialchars($dia) ?>
                    </button>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- Tab Panels -->
        <div>
            <?php foreach ($diasDaSemana as $index => $dia): ?>
                <div id="tab-<?= $index ?>" class="mobile-tab-content <?= $index !== 0 ? 'hidden' : '' ?>">
                    <ul class="divide-y divide-slate-100">
                        <?php foreach ($horariosAulas as $horario => $label):
                            $isIntervalo = !is_numeric($horario);
                            $horarioKey = $isIntervalo ? $horario : $label;
                            $aula = $horariosDaTurma[$dia][$horarioKey] ?? null;
                            ?>
                            <li class="flex items-stretch">
                                <div
                                    class="w-24 sm:w-28 flex-shrink-0 text-xs sm:text-sm font-medium text-slate-600 flex items-center justify-center bg-slate-50/70 border-r border-slate-100">
                                    <?= htmlspecialchars($horarioKey) ?>
                                </div>
                                <div class="flex-1 p-2">
                                    <?php if ($isIntervalo): ?>
                                        <div class="flex justify-center items-center h-full text-slate-500 font-medium rounded-md p-2">
                                            <i
                                                class="fa-solid <?= $label === 'Almoço' ? 'fa-utensils' : 'fa-mug-saucer' ?> mr-2"></i>
                                            <span><?= $label ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="h-full schedule-cell cursor-pointer"
                                            data-turma-id="<?= $selectedTurmaId ?>" data-dia="<?= $dia ?>"
                                            data-horario="<?= $horarioKey ?>" data-materia-id="<?= $aula['id_materia'] ?? '' ?>"
                                            data-professor-id="<?= $aula['id_professor'] ?? '' ?>">
                                            <?php if ($aula): ?>
                                                <div
                                                    class="flex flex-col justify-center h-full bg-purple-100 rounded-md p-2 border border-purple-200">
                                                    <p class="font-semibold text-purple-800 text-sm">
                                                        <?= htmlspecialchars($aula['materia']) ?>
                                                    </p>
                                                    <p class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($aula['professor']) ?></p>
                                                </div>
                                            <?php else: ?>
                                                <div
                                                    class="flex items-center justify-center h-full gap-2 text-slate-400 rounded-md border-2 border-dashed border-slate-200 p-2 hover:border-purple-400 hover:text-purple-400 transition-colors">
                                                    <i class="fa-solid fa-plus"></i>
                                                    <span class="text-sm">Adicionar</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Desktop View -->
    <div class="hidden md:block bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-left text-sm min-w-[1000px]">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-3 font-semibold w-32">Horário</th>
                    <?php foreach ($diasDaSemana as $dia): ?>
                        <th class="p-3 font-semibold text-center"><?= $dia ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php foreach ($horariosAulas as $horario => $label):
                    $isIntervalo = !is_numeric($horario);
                    $horarioKey = $isIntervalo ? $horario : $label;
                    ?>
                    <tr class="<?= $isIntervalo ? 'bg-slate-100 font-medium text-center' : '' ?>">
                        <td class="p-3 font-medium whitespace-nowrap"><?= $horarioKey ?></td>
                        <?php if ($isIntervalo): ?>
                            <td colspan="<?= count($diasDaSemana) ?>" class="p-3 text-slate-500">
                                <div class="flex justify-center items-center">
                                    <i
                                        class="fa-solid <?= $label === 'Almoço' ? 'fa-utensils' : 'fa-mug-saucer' ?> mr-2"></i>
                                    <span><?= $label ?></span>
                                </div>
                            </td>
                        <?php else: ?>
                            <?php foreach ($diasDaSemana as $dia):
                                $aula = $horariosDaTurma[$dia][$horarioKey] ?? null;
                                ?>
                                <td class="p-1.5 text-center align-top h-24 hover:bg-purple-50 transition-colors duration-200 cursor-pointer schedule-cell" data-turma-id="<?= $selectedTurmaId ?>" data-dia="<?= $dia ?>" data-horario="<?= $horarioKey ?>" data-materia-id="<?= $aula['id_materia'] ?? '' ?>"
                                    data-professor-id="<?= $aula['id_professor'] ?? '' ?>">
                                    <?php if ($aula): ?>
                                        <div
                                            class="flex flex-col justify-center items-center h-full w-full bg-purple-100 rounded-md p-1 border border-purple-200">
                                            <p class="font-semibold text-purple-800 text-xs sm:text-sm">
                                                <?= htmlspecialchars($aula['materia']) ?>
                                            </p>
                                            <p class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($aula['professor']) ?></p>
                                        </div>
                                    <?php else: ?>
                                        <div
                                            class="flex justify-center items-center h-full w-full text-slate-300 rounded-md border-2 border-dashed border-slate-200 hover:border-purple-400 hover:text-purple-400">
                                            <i class="fa-solid fa-plus"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Editar Horário -->
<?php Modal::begin('horario-modal', 'Editar Aula', 'horario-modal-title', 'max-w-md', 'z-40'); ?>
<form id="horario-form" method="POST" class="space-y-4">
    <input type="hidden" name="action" value="salvar_horario">
    <input type="hidden" id="horario-id" name="horario-id"> <!-- Para edições futuras -->
    <input type="hidden" id="horario-turma-id" name="turma_id">
    <input type="hidden" id="horario-dia" name="dia_semana">
    <input type="hidden" id="horario-horario" name="horario">

    <div>
        <label for="horario-materia" class="block text-sm font-medium text-slate-700 mb-1">Matéria</label>
        <div class="flex items-center gap-2">
            <select id="horario-materia" name="materia_id"
                class="flex-grow px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                required>
                <option value="">Selecione uma matéria</option>
                <?php foreach ($materias as $materia): ?>
                    <option value="<?= $materia->id ?>"><?= htmlspecialchars($materia->nome) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" id="add-materia-btn" title="Adicionar Nova Matéria"
                class="btn btn-azul flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-md">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
        <?php if (empty($materias)): ?>
            <p class="text-slate-500 text-xs mt-2">
                Nenhuma matéria cadastrada.
                <a href="?param=materias" class="text-purple-600 hover:underline font-medium">
                    Cadastrar Matérias
                </a>
            </p>
        <?php endif; ?>
    </div>
    <div>
        <label for="horario-professor" class="block text-sm font-medium text-slate-700 mb-1">Professor</label>
        <select id="horario-professor" name="professor_id"
            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
            required>
            <option value="">Selecione um professor</option>
            <?php foreach ($professores as $professor): ?>
                <option value="<?= $professor->id ?>"><?= htmlspecialchars($professor->nome) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-4 mt-4 border-t">
        <button type="button" class="close-modal-btn btn btn-cinza w-full sm:w-auto">Cancelar</button>
        <button type="submit" class="btn btn-verde w-full sm:w-auto">Salvar</button>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal para Nova Matéria -->
<?php Modal::begin('nova-materia-modal', 'Cadastrar Nova Matéria', 'nova-materia-modal-title', 'max-w-md', 'z-50'); ?>
<form id="nova-materia-form" class="space-y-4">
    <div>
        <label for="nova-materia-nome" class="block text-sm font-medium text-slate-700 mb-1">Nome da Matéria</label>
        <input type="text" id="nova-materia-nome" name="materia_nome" required
            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
    </div>
    <div id="nova-materia-error" class="hidden text-red-600 text-sm bg-red-100 p-3 rounded-md"></div>
    <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 mt-4">
        <button type="button" class="btn btn-cinza close-modal-btn">Cancelar</button>
        <button type="submit" class="btn btn-verde">Salvar Matéria</button>
    </div>
</form>
<?php Modal::end(); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const horarioModal = document.getElementById('horario-modal');
        const novaMateriaModal = document.getElementById('nova-materia-modal');
        const novaMateriaForm = document.getElementById('nova-materia-form');
        const horarioMateriaSelect = document.getElementById('horario-materia');

        // Tab switching logic for mobile view
        const mobileTabs = document.querySelectorAll('.mobile-tab-btn');
        const mobileTabContents = document.querySelectorAll('.mobile-tab-content');

        mobileTabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                // Deactivate all tabs and content
                mobileTabs.forEach(t => {
                    t.classList.remove('border-purple-500', 'text-purple-600');
                    t.classList.add('border-transparent', 'text-slate-500', 'hover:text-slate-700', 'hover:border-slate-300');
                });
                mobileTabContents.forEach(c => {
                    c.classList.add('hidden');
                });

                // Activate clicked tab and its content
                tab.classList.add('border-purple-500', 'text-purple-600');
                tab.classList.remove('border-transparent', 'text-slate-500', 'hover:text-slate-700', 'hover:border-slate-300');
                const targetContent = document.querySelector(tab.dataset.targetTab);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            });
        });

        document.querySelectorAll('.schedule-cell').forEach(cell => {
            cell.addEventListener('click', () => {
                const turmaId = cell.dataset.turmaId;
                if (!turmaId) return;

                const dia = cell.dataset.dia;
                const horario = cell.dataset.horario;
                const materiaId = cell.dataset.materiaId;
                const professorId = cell.dataset.professorId;

                horarioModal.querySelector('#horario-modal-title').textContent = `Aula - ${dia} - ${horario}`;
                const form = horarioModal.querySelector('#horario-form');
                form.reset();
                form.querySelector('#horario-turma-id').value = turmaId;
                form.querySelector('#horario-dia').value = dia;
                form.querySelector('#horario-horario').value = horario;
                form.querySelector('#horario-materia').value = materiaId;
                form.querySelector('#horario-professor').value = professorId;

                window.ModalManager.open(horarioModal);
            });
        });

        // Botão para abrir modal de nova matéria
        document.getElementById('add-materia-btn').addEventListener('click', () => {
            novaMateriaForm.reset();
            const errorDiv = document.getElementById('nova-materia-error');
            if (errorDiv) {
                errorDiv.classList.add('hidden');
                errorDiv.textContent = '';
            }
            // Abre o modal de nova matéria sobre o modal de horário
            window.ModalManager.open(novaMateriaModal);
        });

        // Submissão do form de nova matéria
        novaMateriaForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(novaMateriaForm);
            formData.append('action', 'salvar_materia');

            fetch('api.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    const errorDiv = document.getElementById('nova-materia-error');
                    if (data.success) {
                        // Adiciona a nova matéria no select de matérias
                        const newOption = new Option(formData.get('materia_nome'), data.id, false, true);
                        horarioMateriaSelect.add(newOption);
                        horarioMateriaSelect.value = data.id; // Seleciona a nova matéria

                        // Remove a mensagem de "nenhuma matéria cadastrada", se existir
                        const noSubjectsMessage = horarioMateriaSelect.nextElementSibling;
                        if (noSubjectsMessage && noSubjectsMessage.tagName === 'P') {
                            noSubjectsMessage.remove();
                        }

                        window.ModalManager.close(novaMateriaModal);
                    } else {
                        if (errorDiv) {
                            errorDiv.textContent = data.message || 'Ocorreu um erro.';
                            errorDiv.classList.remove('hidden');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });
</script>