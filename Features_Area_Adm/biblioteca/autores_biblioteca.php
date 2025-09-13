<?php
require_once BASE_PATH . 'core/services/AutorService.php';
require_once BASE_PATH . 'widgets/modal/Modal.php';
require_once BASE_PATH . 'core/models/AutorDTO.php';
require_once BASE_PATH . 'core/services/database.php';

FuncoesUtils::adicionarJs('Widgets/modal/modal.js');

$autorService = new AutorService();
$feedbackMessage = '';
$feedbackType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $action = $_POST['action'] ?? '';

    if ($action === 'salvar_autor')
    {
        $autorDTO = new AutorDTO(
            empty($_POST['author-id']) ? null : (int)$_POST['author-id'],
            $_POST['author-name'] ?? '',
            (int)($_POST['author-country'] ?? 1)
        );

        if ($autorService->salvarAutor($autorDTO))
        {
            $feedbackMessage = "Autor salvo com sucesso!";
        }
        else
        {
            $feedbackMessage = "Erro ao salvar autor.";
            $feedbackType = 'error';
        }
    }

    if ($action === 'excluir_autor')
    {
        $id = (int)$_POST['id'];
        $errorMessage = '';
        if ($autorService->excluirAutor($id, $errorMessage))
        {
            $feedbackMessage = "Autor excluído com sucesso!";
        }
        else
        {
            $feedbackMessage = $errorMessage ?: "Erro ao excluir autor.";
            $feedbackType = 'error';
        }
    }

    $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=autores_biblioteca&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
    header("Location: " . $redirectUrl);
    exit();
}

$autores = $autorService->buscarTodos();
$db = new Database();
$paises = $db->query("SELECT id_pais, nome FROM paises ORDER BY nome");

$feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
?>

<div class="space-y-6">
    <?php if ($feedbackMessage): ?>
        <div class="<?= $feedbackType === 'success' ? 'bg-green-100 border-green-200 text-green-800' : 'bg-red-100 border-red-200 text-red-800' ?> border px-4 py-3 rounded-lg relative"
            role="alert">
            <span class="block sm:inline"><?= $feedbackMessage ?></span>
        </div>
    <?php endif; ?>

    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <h2 class="text-xl font-semibold">Gerenciar Autores</h2>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-grow">
                <input id="author-search-input" type="text" placeholder="Pesquisar por nome..."
                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-full focus:ring-2 focus:ring-purple-400 w-full text-sm">
                <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            </div>
            <?php // Botão para Desktop ?>
            <?php Botoes::getBotao('', 'Adicionar Autor', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-plus-circle', type: 'button', classes: 'add-author-btn hidden sm:inline-flex') ?>
        </div>
    </div>

    <?php // Botão Flutuante para Mobile
    Botoes::getBotoesFlutuantes([
        [
            'cor' => BotoesCores::VERDE,
            'icone' => 'fa-solid fa-plus text-xl',
            'type' => 'button',
            'classesAdicionais' => 'add-author-btn'
        ]
    ]);
    ?>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-4 font-semibold text-sm">Nome</th>
                    <th class="p-4 font-semibold text-sm">País</th>
                    <th class="p-4 font-semibold text-sm text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200" id="authors-table-body">
                <?php foreach ($autores as $autor): ?>
                    <tr data-author-id="<?= $autor->id ?>" data-author-name="<?= htmlspecialchars($autor->nome) ?>"
                        data-country-id="<?= $autor->idPais ?>">
                        <td class="p-4 font-medium"><?= htmlspecialchars($autor->nome) ?></td>
                        <td class="p-4 text-slate-600"><?= htmlspecialchars($autor->paisNome ?? 'N/A') ?></td>
                        <td class="p-4 flex items-center justify-end gap-2">
                            <button
                                class="edit-author-btn text-slate-500 hover:text-blue-600 p-2 rounded-lg hover:bg-slate-100"
                                title="Editar"><i class="fa-solid fa-pencil"></i></button>
                            <button
                                class="delete-author-btn text-slate-500 hover:text-red-600 p-2 rounded-lg hover:bg-slate-100"
                                title="Excluir"><i class="fa-solid fa-trash-can"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Adicionar/Editar Autor -->
<?php Modal::begin('add-edit-author-modal', 'Adicionar Novo Autor', 'author-modal-title', 'max-w-md', 'z-40'); ?>
<form id="author-form" method="POST">
    <input type="hidden" name="action" value="salvar_autor">
    <input type="hidden" id="author-id" name="author-id">
    <div class="space-y-4">
        <div>
            <label for="author-name" class="block text-sm font-medium text-slate-700 mb-1">Nome do Autor</label>
            <input type="text" id="author-name" name="author-name" required
                class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm">
        </div>
        <div>
            <label for="author-country" class="block text-sm font-medium text-slate-700 mb-1">País</label>
            <div class="flex gap-2">
                <select id="author-country" name="author-country" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm">
                    <?php foreach ($paises as $pais): ?>
                        <option value="<?= $pais['id_pais'] ?>"><?= htmlspecialchars($pais['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" id="add-country-btn"
                    class="p-2 rounded-md bg-purple-100 text-purple-700 hover:bg-purple-200"
                    title="Adicionar Novo País">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-6">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Salvar', BotoesCores::VERDE, 'save-author-btn', altura: 40, icone: 'fa-solid fa-floppy-disk', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal de Confirmação para Exclusão -->
<?php Modal::begin('confirmation-modal', null, '', 'max-w-md', 'z-40'); ?>
<form id="confirmation-form" method="POST">
    <input type="hidden" name="action" value="excluir_autor">
    <input type="hidden" id="confirmation-id" name="id">
    <h3 id="confirmation-title" class="text-xl font-semibold mb-4">Confirmar Exclusão</h3>
    <p id="confirmation-message" class="text-slate-600 mb-6">Tem certeza que deseja excluir este autor?</p>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Confirmar', BotoesCores::VERMELHO, 'confirm-delete-btn', altura: 40, icone: 'fa-solid fa-check', type: 'submit', classes: 'w-full sm:w-auto') ?>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal Adicionar País -->
<?php Modal::begin('add-country-modal', 'Adicionar Novo País', '', 'max-w-sm', 'z-50'); ?>
<div id="country-selection-container">
    <div class="space-y-4">
        <div>
            <label for="country-search-input" class="block text-sm font-medium text-slate-700 mb-1">Pesquisar
                País</label>
            <input type="text" id="country-search-input" placeholder="Digite para buscar..."
                class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm text-sm">
        </div>
        <div id="country-list-container" class="max-h-60 overflow-y-auto border border-slate-200 rounded-md">
            <p class="p-4 text-center text-slate-500">Carregando países...</p>
        </div>
        <input type="hidden" id="selected-country-name">
    </div>
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-6">
        <?php Botoes::getBotao('', 'Cancelar', BotoesCores::CINZA, null, altura: 40, icone: 'fa-solid fa-xmark', type: 'button', classes: 'cancel-modal-btn w-full sm:w-auto') ?>
        <?php Botoes::getBotao('', 'Salvar', BotoesCores::VERDE, 'save-country-btn', altura: 40, icone: 'fa-solid fa-floppy-disk', type: 'button', classes: 'w-full sm:w-auto') ?>
    </div>
</div>
<?php Modal::end(); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addEditModal = document.getElementById('add-edit-author-modal');
        const authorModalTitle = document.getElementById('author-modal-title');
        const authorForm = document.getElementById('author-form');
        const authorIdInput = document.getElementById('author-id');
        const authorNameInput = document.getElementById('author-name');
        const authorCountrySelect = document.getElementById('author-country');
        const addCountryBtn = document.getElementById('add-country-btn');

        const addCountryModal = document.getElementById('add-country-modal');
        const countrySearchInput = document.getElementById('country-search-input');
        const countryListContainer = document.getElementById('country-list-container');
        const selectedCountryNameInput = document.getElementById('selected-country-name');

        // Function to fetch and populate countries
        const fetchAndPopulateCountries = async (selectedId = null) => {
            try {
                const response = await fetch('api.php?action=get_paises');
                const data = await response.json();
                if (data.success) {
                    authorCountrySelect.innerHTML = ''; // Clear existing options
                    data.data.forEach(pais => {
                        const option = document.createElement('option');
                        option.value = pais.id_pais;
                        option.textContent = pais.nome;
                        authorCountrySelect.appendChild(option);
                    });
                    if (selectedId) {
                        authorCountrySelect.value = selectedId;
                    }
                } else {
                    console.error('Erro ao buscar países:', data.message);
                }
            } catch (error) {
                console.error('Erro na requisição para buscar países:', error);
            }
        };

        // Open Add Country Modal
        if (addCountryBtn) {
            addCountryBtn.addEventListener('click', () => {
                window.ModalManager.open(addCountryModal);
                countryListContainer.innerHTML = '<p class="p-4 text-center text-slate-500">Carregando países...</p>';
                selectedCountryNameInput.value = '';
                countrySearchInput.value = '';

                fetch('https://restcountries.com/v3.1/all?fields=name,translations')
                    .then(response => response.json())
                    .then(data => {
                        const countries = data
                            .map(country => country.translations.por.common)
                            .sort((a, b) => a.localeCompare(b));

                        let listHtml = '<ul class="divide-y divide-slate-100">';
                        countries.forEach(name => {
                            listHtml += `<li class="country-item p-3 hover:bg-slate-100 cursor-pointer text-sm" data-name="${name}">${name}</li>`;
                        });
                        listHtml += '</ul>';
                        countryListContainer.innerHTML = listHtml;
                    })
                    .catch(error => {
                        console.error('Erro ao buscar países da API:', error);
                        countryListContainer.innerHTML = '<p class="p-4 text-center text-red-500">Falha ao carregar países.</p>';
                    });
            });
        }

        // Search/filter countries in modal
        if (countrySearchInput) {
            countrySearchInput.addEventListener('input', () => {
                const searchTerm = countrySearchInput.value.toLowerCase();
                countryListContainer.querySelectorAll('.country-item').forEach(item => {
                    const name = item.dataset.name.toLowerCase();
                    item.style.display = name.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Select a country in modal
        if (countryListContainer) {
            countryListContainer.addEventListener('click', e => {
                if (e.target.classList.contains('country-item')) {
                    countryListContainer.querySelectorAll('.country-item').forEach(item => item.classList.remove('bg-purple-100', 'font-semibold'));
                    e.target.classList.add('bg-purple-100', 'font-semibold');
                    selectedCountryNameInput.value = e.target.dataset.name;
                }
            });
        }

        // Handle Country Save Button (AJAX)
        addCountryModal.querySelector('#save-country-btn').addEventListener('click', async () => {
            const countryName = selectedCountryNameInput.value;
            if (!countryName) {
                alert('Por favor, selecione um país da lista.');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('action', 'salvar_pais');
                formData.append('country_name', countryName);

                const response = await fetch('api.php', { method: 'POST', body: formData });
                const data = await response.json();

                if (data.success) {
                    closeModal(addCountryModal);
                    await fetchAndPopulateCountries(data.id);
                } else {
                    alert('Erro: ' + data.message);
                }
            } catch (error) {
                console.error('Erro ao salvar país:', error);
                alert('Erro ao salvar país. Verifique o console para mais detalhes.');
            }
        });

        document.querySelectorAll('.add-author-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                authorModalTitle.textContent = 'Adicionar Novo Autor';
                authorForm.reset();
                authorIdInput.value = '';
                fetchAndPopulateCountries(); // Ensure countries are loaded when adding new author
                window.ModalManager.open(addEditModal);
            });
        });

        const confirmationModal = document.getElementById('confirmation-modal');
        const confirmationMessage = document.getElementById('confirmation-message');
        const confirmationIdInput = document.getElementById('confirmation-id');

        document.getElementById('authors-table-body').addEventListener('click', e => {
            const row = e.target.closest('tr');
            if (!row) return;

            if (e.target.closest('.edit-author-btn')) {
                authorModalTitle.textContent = 'Editar Autor';
                authorIdInput.value = row.dataset.authorId;
                authorNameInput.value = row.dataset.authorName;
                authorCountrySelect.value = row.dataset.countryId;
                fetchAndPopulateCountries(row.dataset.countryId); // Load countries and select current one
                window.ModalManager.open(addEditModal);
            }

            if (e.target.closest('.delete-author-btn')) {
                confirmationMessage.innerHTML =
                    `Tem certeza que deseja excluir o autor <strong>${row.dataset.authorName}</strong>? Esta ação não pode ser desfeita.`;
                confirmationIdInput.value = row.dataset.authorId;
                window.ModalManager.open(confirmationModal);
            }
        });

        document.getElementById('author-search-input').addEventListener('input', e => {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('#authors-table-body tr').forEach(row => {
                const name = row.dataset.authorName.toLowerCase();
                row.style.display = name.includes(searchTerm) ? '' : 'none';
            });
        });
    });
</script>