<?php
class NotificacoesWidget
{
    /**
     * Renderiza o widget de notificações completo (botão, painel e modal).
     *
     * @param array $notificacoes Array de objetos de notificação para a lista.
     * @param int   $unreadCount  O número total de notificações não lidas para o contador.
     * @param array $options      Opções de configuração, como 'showModalLink' (bool).
     */
    public static function render(array $notificacoes, int $unreadCount, array $options = []): void
    {
        // Opções padrão
        $config = array_merge([
            'showModalLink' => false,
        ], $options);

        ?>
        <div class="relative">
            <button id="notification-toggle" class="text-slate-500 hover:text-purple-600">
                <i class="fa-solid fa-bell w-6 h-6"></i>
                <?php if ($unreadCount > 0): ?>
                    <span id="notification-count-badge" data-count="<?= $unreadCount ?>"
                        class="absolute -top-0.5 -right-0.5 block h-3 w-3 rounded-full bg-red-500 border-2 border-white text-white text-[10px] flex items-center justify-center"><?= $unreadCount > 9 ? '' : $unreadCount ?></span>
                <?php else: ?>
                    <span id="notification-count-badge" data-count="0"
                        class="absolute -top-0.5 -right-1 block h-3 w-3 rounded-full bg-red-500 border-2 border-white text-white text-[10px] flex items-center justify-center hidden"></span>
                <?php endif; ?>
            </button>

            <!-- Painel de Notificações -->
            <div id="notification-panel"
                class="hidden opacity-0 pointer-events-none transition-all duration-300 ease-in-out fixed inset-0 z-50 bg-white p-0 flex flex-col transform translate-y-full md:absolute md:inset-auto md:right-0 md:mt-2 md:w-80 md:max-h-[460px] md:rounded-xl md:shadow-2xl md:border md:border-slate-200 md:translate-y-0">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="font-semibold">Notificações</h3>
                    <button id="close-notification-panel-mobile" class="md:hidden text-slate-500 hover:text-red-500">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <div id="notification-list" class="flex-grow overflow-y-auto">
                    <?php if (empty($notificacoes)): ?>
                        <p id="no-notifications-msg" class="text-center text-slate-500 py-6 text-sm">Nenhuma notificação nova.
                        </p>
                    <?php else: ?>
                        <p id="no-notifications-msg" class="text-center text-slate-500 py-6 text-sm hidden">Nenhuma
                            notificação nova.</p>
                        <?php foreach ($notificacoes as $notificacao): ?>
                            <div class="notification-item group flex items-start gap-3 p-4 hover:bg-slate-50 border-b border-slate-100 last:border-b-0 cursor-pointer"
                                data-notification-id="<?= $notificacao->id ?>"
                                data-title="<?= htmlspecialchars($notificacao->titulo) ?>"
                                data-content="<?= htmlspecialchars($notificacao->descricao) ?>"
                                data-time="<?= htmlspecialchars($notificacao->data_criacao) ?>"
                                data-link="<?= htmlspecialchars($notificacao->link ?? '') ?>">
                                <div
                                    class="w-10 h-10 flex-shrink-0 rounded-full flex items-center justify-center <?= $notificacao->cor_fundo_icone ?>">
                                    <i class="<?= $notificacao->icone ?> <?= $notificacao->cor_icone ?>"></i>
                                </div>
                                <div class="flex-grow">
                                    <p class="text-sm text-slate-700"><?= $notificacao->descricao ?></p>
                                    <p class="text-xs text-slate-400 mt-1"><?= $notificacao->getTempoRelativo() ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Modal de Detalhes da Notificação -->
        <div id="notification-detail-modal"
            class="modal-backdrop fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 z-50 hidden opacity-0 pointer-events-none transition-all duration-300">
            <div
                class="modal-panel bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 transform -translate-y-10 transition-all duration-300">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="notification-modal-title" class="text-xl font-semibold text-slate-800">Detalhes da Notificação
                    </h3>
                    <button type="button" class="close-modal-btn p-2 rounded-full text-slate-500 hover:bg-slate-100">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <p id="notification-modal-content" class="text-slate-700 leading-relaxed"></p>
                    <p id="notification-modal-time" class="text-sm text-slate-500 text-right"></p>
                    <?php if ($config['showModalLink']): ?>
                        <div class="pt-4 border-t border-slate-200 empty:hidden">
                            <a href="#" id="notification-modal-link"
                                class="hidden w-full text-center bg-purple-600 text-white hover:bg-purple-700 font-bold py-2 px-4 rounded-lg transition block">
                                Ver Detalhes
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        FuncoesUtils::adicionarJs('Widgets/modal/modal.js');
        FuncoesUtils::adicionarJs('js/notifications.js');
    }
}
