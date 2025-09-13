<?php

class Secoes
{

    public static function renderizarSecao($id, $titulo, $corBase, $conteudoHTML, $subtitulo = '')
    {

        if ($corBase === CoresSistema::AMARELO)
        {
            $corClasse = 'secao-amarela';
        }
        elseif ($corBase === CoresSistema::ROXO)
        {
            $corClasse = 'secao-roxa';
        }
        elseif ($corBase === CoresSistema::VERDE)
        {
            $corClasse = 'secao-verde';
        }
        else
        {
            $corClasse = 'secao-azul';
        }

        ?>



        <section class="<?= $id ?> pb-5">
            <h2 class="section-title <?= $corClasse ?>"><?= htmlspecialchars($titulo) ?> </h2>

            <?php if (!empty($subtitulo)): ?>
                <p class="subtitulo-secao text-center mb-5"><?= htmlspecialchars($subtitulo) ?></p>
            <?php endif; ?>

            <div class="conteudo-interno">
                <?= $conteudoHTML ?>
            </div>
        </section>
        <?php
        FuncoesUtils::adicionarCss('Widgets/secoes/secoes.css');
    }
}
