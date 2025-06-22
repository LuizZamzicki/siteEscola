<?php
require_once __DIR__ . '/../objetos/funcionarios.php';

// Definir departamentos únicos para o filtro
$departamentos_disponiveis = [];
foreach ($funcionarios as $funcionario) {
    if (!empty($funcionario['departamento'])) {
        $departamentos_disponiveis[$funcionario['departamento']] = $funcionario['departamento'];
    }
}
ksort($departamentos_disponiveis); // Ordena os departamentos alfabeticamente

// Filtrar funcionários com base no departamento selecionado 
$funcionarios_filtrados = $funcionarios;
$departamento_selecionado = $_GET['departamento'] ?? 'todos'; // Pega o departamento da URL, padrão 'todos'

if ($departamento_selecionado !== 'todos') {
    $funcionarios_filtrados = array_filter($funcionarios, function($funcionario) use ($departamento_selecionado) {
        return ($funcionario['departamento'] ?? '') === $departamento_selecionado;
    });
}

// URL base para os links de departamento (sem o departamento atual)
$base_url_departamento = strtok($_SERVER["REQUEST_URI"], '?');

?>

<section class="equipe-listagem-section container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            <h2 class="section-title">Nossa Equipe</h2>

            <p class="text-center mb-5">
                Conheça os profissionais dedicados que fazem da nossa escola um ambiente de excelência.
                Nossa equipe é composta por educadores e colaboradores apaixonados por transformar vidas.
            </p>


            <div class="card-padrao">
                <?php if (!empty($departamentos_disponiveis)): ?>
                <div class="filter-controls text-center mb-2">

                    <div id="departamentos" class="categorias-wrapper d-none d-md-flex">
                        <a href="<?= $base_url_departamento ?>?departamento=todos"
                            class="btn btn-verde btn-categoria <?= ($departamento_selecionado === 'todos') ? 'active' : '' ?>">Todos</a>
                        <?php foreach ($departamentos_disponiveis as $categoria_nome): ?>
                        <a href="<?= $base_url_departamento ?>?departamento=<?= urlencode($categoria_nome) ?>"
                            class="btn btn-verde btn-categoria <?= ($departamento_selecionado === $categoria_nome) ? 'active' : '' ?>">
                            <?= htmlspecialchars($categoria_nome) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php
            // O botão de toggle só aparece se houver categorias o suficiente no desktop
            $max_categorias_visiveis_inicialmente = 3; // Ajuste este número conforme a sua necessidade no desktop
            if (count($departamentos_disponiveis) > $max_categorias_visiveis_inicialmente):
        ?>
                    <div class="ver-mais-categorias d-none d-md-flex" id="toggleCategorias"
                        onclick="toggleCategorias()">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <?php endif; ?>

                    <div class="form-group d-md-none w-100">
                        <label for="mobileCategoriaSelect" class="sr-only">Selecione uma categoria</label>
                        <select class="form-select btn-verde" id="mobileCategoriaSelect"
                            onchange="window.location.href = this.value;">
                            <option value="<?= $base_url_departamento ?>?departamento=todos"
                                <?= ($departamento_selecionado === 'todos') ? 'selected' : '' ?>>Todos</option>
                            <?php foreach ($departamentos_disponiveis as $categoria_nome): ?>
                            <option value="<?= $base_url_departamento ?>?departamento=<?= urlencode($categoria_nome) ?>"
                                <?= ($departamento_selecionado === $categoria_nome) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($categoria_nome) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
                <?php endif; ?>
            </div>

            <div class="equipe-grid-wrapper">
                <div class="equipe-grid">
                    <?php if (empty($funcionarios_filtrados)): ?>
                    <p class="text-center w-100 no-results-message">Nenhum funcionário encontrado para o
                        departamento
                        selecionado.</p>
                    <?php else: ?>
                    <?php foreach ($funcionarios_filtrados as $funcionario): ?>
                    <div class="funcionario-card">
                        <img src="<?= $funcionario['foto'] ?>" alt="Foto de <?= $funcionario['nome'] ?>"
                            class="funcionario-img">
                        <div class="funcionario-info">
                            <h4 class="funcionario-nome"><?= $funcionario['nome'] ?></h4>
                            <p class="funcionario-cargo"><?= $funcionario['cargo'] ?></p>
                            <?php if (!empty($funcionario['descricao'])): ?>
                            <p class="funcionario-descricao"><?= $funcionario['descricao'] ?></p>
                            <?php endif; ?>
                            <?php if (!empty($funcionario['formacao'])): ?>
                            <p class="funcionario-formacao"><i class="bi bi-mortarboard-fill"></i>
                                <?= $funcionario['formacao'] ?></p>
                            <?php endif; ?>
                            <?php if (!empty($funcionario['redes_sociais'])): ?>
                            <div class="funcionario-redes">
                                <?php foreach ($funcionario['redes_sociais'] as $rede => $url): ?>
                                <a href="<?= $url ?>" target="_blank"
                                    aria-label="<?= ucfirst($rede) ?> de <?= $funcionario['nome'] ?>">
                                    <?php if ($rede === 'linkedin'): ?>
                                    <i class="bi bi-linkedin"></i>
                                    <?php elseif ($rede === 'instagram'): ?>
                                    <i class="bi bi-instagram"></i>
                                    <?php  ?>
                                    <?php endif; ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>