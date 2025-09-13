<?php

class CalendarioWidget
{
    /**
     * Renderiza a estrutura HTML do calendário.
     *
     * @param array $config Configurações para o calendário.
     *                      'id_container' => ID do container principal.
     *                      'id_title' => ID do elemento do título (h2).
     *                      'id_prev_btn' => ID do botão de mês anterior.
     *                      'id_next_btn' => ID do botão de próximo mês.
     *                      'id_grid' => ID do grid do calendário.
     *                      'id_list' => ID da lista para mobile (opcional).
     *                      'is_admin' => bool, se é o calendário do admin.
     *                      'add_event_btn_id' => ID do botão de adicionar evento (admin).
     */
    public static function render(array $config): void
    {
        FuncoesUtils::adicionarCss(caminho: 'Widgets/calendario/calendario.css');
        FuncoesUtils::adicionarJs(caminho: 'Widgets/calendario/calendario.js');

        $config = array_merge([
            'id_container' => 'calendar-container',
            'id_title' => 'calendar-title',
            'id_prev_btn' => 'prev-month-btn',
            'id_next_btn' => 'next-month-btn',
            'id_grid' => 'calendar-grid',
            'id_list' => 'calendar-list',
            'is_admin' => false,
            'add_event_btn_id' => 'add-event-btn',
        ], $config);
        ?>
        <div id="<?= htmlspecialchars($config['id_container']) ?>"
            class="bg-white rounded-xl shadow-sm border border-slate-200 p-1 sm:p-4 lg:p-6">
            <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="flex items-center">
                        <button id="<?= htmlspecialchars($config['id_prev_btn']) ?>"
                            class="p-2 rounded-full hover:bg-slate-100 text-slate-500 hover:text-slate-800"><i
                                class="fa-solid fa-chevron-left"></i></button>
                        <button id="<?= htmlspecialchars($config['id_next_btn']) ?>"
                            class="p-2 rounded-full hover:bg-slate-100 text-slate-500 hover:text-slate-800"><i
                                class="fa-solid fa-chevron-right"></i></button>
                    </div>
                    <h2 id="<?= htmlspecialchars($config['id_title']) ?>"
                        class="text-2xl font-bold text-left whitespace-nowrap"></h2>
                </div>
                <?php if ($config['is_admin']): ?>
                    <?php Botoes::getBotao('', 'Adicionar Evento', BotoesCores::VERDE, null, altura: 40, icone: 'fa-solid fa-plus-circle', type: 'button', classes: $config['add_event_btn_id'] . ' hidden sm:inline-flex') ?>
                <?php endif; ?>
            </div>

            <!-- Visualização em Grade -->
            <div class="calendar-grid-view">
                <div class="grid grid-cols-7 text-center font-semibold text-slate-600">
                    <div class="py-2 bg-slate-50 rounded-tl-lg">Dom</div>
                    <div class="py-2 bg-slate-50">Seg</div>
                    <div class="py-2 bg-slate-50">Ter</div>
                    <div class="py-2 bg-slate-50">Qua</div>
                    <div class="py-2 bg-slate-50">Qui</div>
                    <div class="py-2 bg-slate-50">Sex</div>
                    <div class="py-2 bg-slate-50 rounded-tr-lg">Sáb</div>
                </div>
                <div id="<?= htmlspecialchars($config['id_grid']) ?>"
                    class="grid grid-cols-7 grid-rows-5 gap-px bg-slate-200 border border-slate-200 rounded-b-lg">
                    <!-- Dias do calendário (grid) serão carregados dinamicamente aqui -->
                </div>
            </div>
        </div>
        <?php
        if ($config['is_admin'])
        {
            Botoes::getBotoesFlutuantes([
                [
                    'cor' => BotoesCores::VERDE,
                    'icone' => 'fa-solid fa-plus text-xl',
                    'type' => 'button',
                    'classesAdicionais' => $config['add_event_btn_id']
                ]
            ]);
        }
    }
}
