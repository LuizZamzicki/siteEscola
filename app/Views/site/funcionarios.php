V<section class="equipe-listagem-section mb-5">
    <h2 class="section-title">Nossa Equipe</h2>

    <p class="subtitulo-secao text-center mb-5">
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
                    <div class="ver-mais-categorias d-none d-md-flex" id="toggleCategorias" onclick="toggleCategorias()">
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
                                <p class="funcionario-formacao"><i class="fa-solid fa-graduation-cap"></i>
                                    <?= $funcionario['formacao'] ?></p>
                            <?php endif; ?>
                            <?php if (!empty($funcionario['redes_sociais'])): ?>
                                <div class="funcionario-redes">
                                    <?php foreach ($funcionario['redes_sociais'] as $rede => $url): ?>
                                        <a href="<?= $url ?>" target="_blank"
                                            aria-label="<?= ucfirst($rede) ?> de <?= $funcionario['nome'] ?>">
                                            <?php if ($rede === 'linkedin'): ?>
                                                <i class="fa-brands fa-linkedin"></i>
                                            <?php elseif ($rede === 'instagram'): ?>
                                                <i class="fa-brands fa-instagram"></i>
                                                <?php ?>
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
</section>

<section class="chamada-para-contato py-5">
    <div class="card-padrao card-roxo text-center">
        <h3 class="mb-4">Conheça a Jornada que Nos Trouxe Até Aqui</h3>
        <p class="mb-5">
            Por trás de cada profissional, há uma história rica de dedicação e um legado construído com paixão. Descubra
            a trajetória do Colégio João Maffei Rosa.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="historia" class="btn btn-amarelo btn-lg">Saiba Mais Sobre Nossa história</a>
            <a href="home" class="btn btn-azul btn-lg">Voltar à Página Inicial</a>
        </div>
    </div>
</section>