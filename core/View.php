<?php

namespace Core;

class View
{
    /**
     * Renderiza uma view com um layout.
     *
     * @param string $view O caminho do arquivo da view (ex: 'aluno/perfil').
     * @param array $data Os dados a serem passados para a view.
     * @param string|null $layout O nome do arquivo de layout (ex: 'aluno'). Se nulo, renderiza apenas a view.
     */
    public static function render(string $view, array $data = [], ?string $layout = null)
    {
        // Transforma as chaves do array em variáveis
        extract($data);

        ob_start();
        require_once APP_PATH . "Views/{$view}.php";
        $conteudo_da_pagina = ob_get_clean();

        if ($layout) {
            // Define um título padrão se não for passado nos dados
            $titulo = $data['title'] ?? 'Escola Maffei';
            require_once APP_PATH . "Views/layouts/{$layout}.php";
        } else {
            echo $conteudo_da_pagina;
        }
    }
}
