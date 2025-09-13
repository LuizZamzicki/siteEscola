<?php

class Modal
{
    /**
     * Inicia a renderização de um modal.
     *
     * @param string $id O ID do elemento do modal.
     * @param string|null $title O título do modal. Se null, o cabeçalho padrão não é renderizado.
     * @param string $titleId O ID do elemento do título.
     * @param string $sizeClass A classe de tamanho (e.g., 'max-w-md', 'max-w-2xl').
     * @param string $extraBackdropClasses Classes extras para o backdrop (ex: z-index).
     * @param string $extraPanelClasses Classes extras para o painel.
     */
    public static function begin(
        string $id,
        ?string $title,
        string $titleId = '',
        string $sizeClass = 'max-w-2xl',
        string $extraBackdropClasses = 'z-40',
        string $extraPanelClasses = ''
    ): void {
        // Garante que o JavaScript do modal seja sempre carregado quando o componente for utilizado.
        FuncoesUtils::adicionarJs('Widgets/modal/modal.js');

        $titleIdAttr = $titleId ? "id='{$titleId}'" : '';

        $headerHtml = '';
        if ($title !== null)
        {
            $headerHtml = <<<HTML
                <div class="flex justify-between items-center mb-6">
                    <h3 {$titleIdAttr} class="text-2xl font-semibold">{$title}</h3>
                    <button type="button" class="close-modal-btn p-2 rounded-full hover:bg-slate-100"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
HTML;
        }

        echo <<<HTML
        <div id="{$id}" class="modal-backdrop fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center overflow-y-auto p-4 sm:pt-12 {$extraBackdropClasses} hidden opacity-0 pointer-events-none transition-opacity duration-300">
            <div class="modal-panel bg-white rounded-xl shadow-2xl w-full {$sizeClass} p-6 transform -translate-y-10 transition-all duration-300 max-h-screen overflow-y-auto {$extraPanelClasses}">
                {$headerHtml}
HTML;
    }

    /**
     * Finaliza a renderização de um modal.
     */
    public static function end(): void
    {
        echo '</div></div>';
    }
}
