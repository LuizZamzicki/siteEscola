<?php

function resolvePagina(string $rota): string
{

    $rota = explode('/', $rota);

    switch ($rota[0])
    {
        case 'home':
            return 'Features_Site/home/home.php';
        case 'integral':
            return 'Features_Site/integral/integral.php';
        case 'itinerarios':
            if (isset($rota[1]) && $rota[1] === 'detalhes')
                return 'Features_Site/itinerarios/detalhes_itinerarios.php';
            else
                return 'Features_Site/itinerarios/itinerario.php';
        default:
            return 'Features_Site/erro/erro.php';
    }
}

$js_files = [];
$css_files = [];

function adicionarCss(string $caminho)
{

    global $css_files;
    if (!in_array($caminho, $css_files))
    {
        $css_files[] = $caminho;
    }
}

function renderizarCss()
{
    global $css_files;
    foreach ($css_files as $css)
    {
        echo "<link rel='stylesheet' href='$css'>\n";
    }
}

function adicionarJs(string $caminho)
{
    global $js_files;
    if (!in_array($caminho, $js_files))
        $js_files[] = $caminho;
}

function renderizarJs()
{
    global $js_files;
    foreach ($js_files as $js)
    {
        echo "<script src='$js'></script>\n";
    }
}