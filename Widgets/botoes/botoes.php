<?php
FuncoesUtils::adicionarCss(caminho: 'Widgets/botoes/botoes.css');

enum BotoesCores
{
    case AMARELO;
    case AZUL;
    case ROXO;
    case VERDE;
    case VERMELHO;
    case CINZA;
    case CINZA_OUTLINE;
    case VERMELHO_OUTLINE;
}

class Botoes
{
    /**
     * Gera um botão <button> estilizado.
     *
     * @param string $link URL de destino (opcional)
     * @param string $texto Texto do botão
     * @param BotoesCores $cor Cor do botão
     * @param string|null $id ID do botão
     * @param int|null $altura Altura em px (opcional)
     * @param string|null $icone Ícone Font Awesome (ex: 'fa-solid fa-plus')
     * @param string|null $classes Classes adicionais (opcional)
     * @param string $posicao_icone 'antes' | 'depois' (posição do ícone)
     * @param string $type Tipo do botão (ex: 'button', 'submit')
     * @param bool $disabled Se o botão deve estar desabilitado
     */
    public static function getBotao(
        string $link,
        string $texto,
        BotoesCores $cor,
        ?string $id = null,
        ?int $altura = null,
        ?string $icone = null,
        ?string $classes = null,
        string $posicao_icone = 'antes',
        string $type = 'button',
        bool $disabled = false
    ): void {
        $classeCor = match ($cor)
        {
                                BotoesCores::AMARELO => 'btn-amarelo',
                                BotoesCores::AZUL => 'btn-azul',
                                BotoesCores::ROXO => 'btn-roxo',
                                BotoesCores::VERDE => 'btn-verde',
                                BotoesCores::VERMELHO => 'btn-vermelho',
                                BotoesCores::CINZA => 'btn-cinza',
                                BotoesCores::CINZA_OUTLINE => 'btn-cinza-outline',
                                BotoesCores::VERMELHO_OUTLINE => 'btn-vermelho-outline',
        };

        $classes = $classes ?? '';

        self::gerarBotao($link, $texto, $classeCor, $id, $altura, $icone, $classes, $posicao_icone, $type, $disabled);
    }

    /**
     * Gera um ou mais Botões de Ação Flutuantes (FABs) para mobile.
     * Encapsula a lógica de posicionamento e estilo, seja para um botão único ou um grupo.
     *
     * @param array $botoesConfig Array de configurações para cada botão.
     * Cada item do array deve ser um array associativo com as chaves:
     * 'cor' (BotoesCores), 'icone' (string), 'link' (string, opcional),
     * 'id' (string|null, opcional), 'type' (string, opcional), 
     * 'classesAdicionais' (string|null, opcional), 'disabled' (bool, opcional).
     */
    public static function getBotoesFlutuantes(array $botoesConfig): void
    {
        if (empty($botoesConfig))
        {
            return;
        }

        $estiloBotao = 'w-14 h-14 rounded-full shadow-lg shadow-black/30 hover:shadow-2xl hover:-translate-y-1 transition';
        ?>
        <div class="sm:hidden fixed bottom-24 right-6 flex flex-col-reverse items-center gap-3 z-30">
            <?php foreach ($botoesConfig as $config):
                $classes = trim($estiloBotao . ' ' . ($config['classesAdicionais'] ?? ''));
                self::getBotao(
                    link: $config['link'] ?? '',
                    texto: '',
                    cor: $config['cor'],
                    id: $config['id'] ?? null,
                    icone: $config['icone'],
                    classes: $classes,
                    type: $config['type'] ?? 'button',
                    disabled: $config['disabled'] ?? false
                );
            endforeach; ?>
        </div>
        <?php
    }

    private static function gerarBotao(
        string $link,
        string $texto,
        string $cor,
        ?string $id = null,
        ?int $altura = null,
        ?string $icone = null,
        string $classes = '',
        string $posicao_icone = 'antes',
        string $type = 'button',
        bool $disabled = false
    ): void {
        $idAttr = $id ? 'id="' . htmlspecialchars($id) . '"' : '';
        $style = $altura ? 'style="height:' . intval($altura) . 'px"' : '';
        $onclick = ($link && !$disabled) ? 'onclick="window.location.href=\'' . htmlspecialchars($link) . '\'"' : '';
        $typeAttr = 'type="' . htmlspecialchars($type) . '"';
        $disabledAttr = $disabled ? 'disabled' : '';

        ?>
        <button <?= $idAttr ?>         <?= $typeAttr ?> class="btn <?= $cor ?> <?= $classes ?>" <?= $style ?>         <?= $onclick ?>
            <?= $disabledAttr ?>>
            <?php if ($icone && $posicao_icone === 'antes'): ?>
                <i class="<?= htmlspecialchars($icone) ?><?= !empty($texto) ? ' me-2' : '' ?>"></i>
            <?php endif; ?>
            <?php if (!empty($texto)): ?>
                <span><?= htmlspecialchars($texto) ?></span>
            <?php endif; ?>
            <?php if ($icone && $posicao_icone === 'depois'): ?>
                <i class="<?= htmlspecialchars($icone) ?><?= !empty($texto) ? ' ms-2' : '' ?>"></i>
            <?php endif; ?>
        </button>
        <?php
    }
}
