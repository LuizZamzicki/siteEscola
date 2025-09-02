<?php
adicionarCss(caminho: 'Widgets/botoes/botoes.css');
enum BotoesCores
{
    case AMARELO;
    case AZUL;
    case ROXO;

    case VERDE;
}


class Botoes
{

    public static function getBotao(string $pagina, string $texto, BotoesCores $cor): void
    {

        switch ($cor)
        {
            case BotoesCores::AMARELO:
                self::getBotaoAmarelo($pagina, $texto);
                break;
            case BotoesCores::AZUL:
                self::getBotaoAzul($pagina, $texto);
                break;
            case BotoesCores::ROXO:
                self::getBotaoRoxo($pagina, $texto);
                break;
            case BotoesCores::VERDE:
                self::getBotaoVerde($pagina, $texto);
                break;
            default:
                self::getBotaoAmarelo($pagina, $texto);
                break;
        }

    }

    private static function getBotaoAmarelo(string $pagina, string $texto): void
    {

        ?>
<a href="<?= $pagina ?>" class="btn btn-amarelo mt-4"><?= $texto ?></a>
<?php
    }

    private static function getBotaoAzul(string $pagina, string $texto): void
    {

        ?>
<a href="<?= $pagina ?>" class="btn btn-azul mt-4"><?= $texto ?></a>
<?php
    }

    private static function getBotaoRoxo(string $pagina, string $texto): void
    {

        ?>
<a href="<?= $pagina ?>" class="btn btn-roxo mt-4"><?= $texto ?></a>
<?php
    }

    private static function getBotaoVerde(string $pagina, string $texto): void
    {

        ?>
<a href="<?= $pagina ?>" class="btn btn-verde mt-4"><?= $texto ?></a>
<?php
    }
}