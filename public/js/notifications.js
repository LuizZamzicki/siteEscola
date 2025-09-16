document.addEventListener("DOMContentLoaded", () => {
  /**
   * Calcula o tempo relativo a partir de uma data em string (ex: "2024-05-20 15:00:00").
   * @param {string} dateString - A data/hora do evento.
   * @returns {string} O texto formatado do tempo relativo (ex: "há 5 min").
   */
  function getRelativeTime(dateString) {
    // Converte a string de data (assumida como UTC) para um objeto Date.
    // O 'T' e 'Z' garantem que seja interpretada como UTC, evitando problemas de fuso horário.
    const date = new Date(dateString.replace(" ", "T") + "Z");
    const now = new Date();
    const seconds = Math.round((now - date) / 1000);
    const minutes = Math.round(seconds / 60);
    const hours = Math.round(minutes / 60);
    const days = Math.round(hours / 24);
    const weeks = Math.round(days / 7);
    const months = Math.round(days / 30);
    const years = Math.round(days / 365);

    if (seconds < 10) return "agora mesmo";
    if (seconds < 60) return `há ${seconds} seg`;
    if (minutes < 60) return `há ${minutes} min`;
    if (hours < 24) return `há ${hours} h`;
    if (days === 1) return "ontem";
    if (days < 7) return `há ${days} dias`;
    if (weeks === 1) return "há 1 semana";
    if (weeks < 5) return `há ${weeks} semanas`;
    if (months === 1) return "há 1 mês";
    if (months < 12) return `há ${months} meses`;
    if (years === 1) return "há 1 ano";
    return `há ${years} anos`;
  }

  // --- Elements ---
  const notificationToggle = document.getElementById("notification-toggle");
  const notificationPanel = document.getElementById("notification-panel");
  const detailModal = document.getElementById("notification-detail-modal");

  if (!notificationPanel || !detailModal) {
    // Se os elementos essenciais não existirem, não faz nada.
    // Isso previne erros em páginas que não têm o sistema de notificação.
    return;
  }

  const modalPanel = detailModal.querySelector(".modal-panel");
  const modalTitle = detailModal.querySelector("#notification-modal-title");
  const modalContent = detailModal.querySelector("#notification-modal-content");
  const modalTime = detailModal.querySelector("#notification-modal-time");
  const modalLink = detailModal.querySelector("#notification-modal-link");
  const closeNotificationPanelMobile = notificationPanel.querySelector(
    "#close-notification-panel-mobile"
  );

  // --- Funções do Painel de Notificações ---
  const openNotificationPanel = () => {
    notificationPanel.classList.remove("hidden", "pointer-events-none");
    setTimeout(() => {
      // Remove a opacidade e a translação para animar o painel para a visão (de baixo para cima)
      notificationPanel.classList.remove("opacity-0", "translate-y-full");
    }, 10);
  };

  const closeNotificationPanel = () => {
    // Adiciona a opacidade e a translação para animar o painel para fora da visão (de cima para baixo)
    notificationPanel.classList.add("opacity-0", "translate-y-full");
    setTimeout(() => {
      notificationPanel.classList.add("hidden", "pointer-events-none");
    }, 300); // Tempo da transição
  };

  // --- Notification Click Logic ---
  notificationPanel.addEventListener("click", (e) => {
    const notificationItem = e.target.closest(".notification-item");
    if (!notificationItem) return;

    e.preventDefault();

    const notificationId = notificationItem.dataset.notificationId;
    if (!notificationId) {
      console.error("ID da notificação não encontrado no elemento clicado.");
      return;
    }

    // 1. Pega os dados do item e popula o modal
    const title = notificationItem.dataset.title;
    const content = notificationItem.dataset.content;
    const timestamp = notificationItem.dataset.time;
    const link = notificationItem.dataset.link;

    modalTitle.textContent = title;
    modalContent.innerHTML = content; // innerHTML para permitir tags como <strong>
    modalTime.textContent = getRelativeTime(timestamp);

    if (modalLink) {
      if (link) {
        let finalLink = link;
        // Se o link não for uma URL completa e não começar com '?' ou '/',
        // assume-se que é um parâmetro para a página atual (ex: ?param=biblioteca_aluno).
        if (
          !link.startsWith("http") &&
          !link.startsWith("?") &&
          !link.startsWith("/")
        ) {
          finalLink = `?param=${link}`;
        }
        modalLink.href = finalLink;
        modalLink.classList.remove("hidden");
      } else {
        modalLink.classList.add("hidden");
      }
    }

    // 2. Abre o modal e esconde o painel de notificações
    window.ModalManager.open(detailModal);
    // No desktop, fecha o painel. No mobile, mantém o painel aberto atrás do modal.
    const isDesktop = window.innerWidth >= 768;
    if (isDesktop) {
      closeNotificationPanel();
    }

    // 3. Foca no elemento principal do modal para acessibilidade
    setTimeout(() => {
      const linkButton = detailModal.querySelector(
        "#notification-modal-link:not(.hidden)"
      );
      const closeButton = detailModal.querySelector(".close-modal-btn");

      if (linkButton) {
        linkButton.focus();
      } else if (closeButton) {
        closeButton.focus();
      }
    }, 100); // Delay para garantir que o modal esteja visível

    // 4. Anima a remoção do item da lista (Atualização Otimista da UI)
    // A notificação é removida da tela imediatamente para uma melhor experiência.
    notificationItem.style.transition =
      "opacity 0.3s ease, transform 0.3s ease";
    notificationItem.style.opacity = "0";
    notificationItem.style.transform = "translateX(-20px)";

    setTimeout(() => {
      notificationItem.remove();

      // Atualiza o contador no ícone
      const badge = document.getElementById("notification-count-badge");
      if (badge) {
        let count = parseInt(badge.dataset.count, 10) || 0;
        count = Math.max(0, count - 1);
        badge.dataset.count = count;

        badge.textContent = count > 9 ? "+" : count;

        if (count <= 0) {
          badge.classList.add("hidden");
        }
      }

      // Verifica se a lista está vazia e mostra a mensagem
      const list = document.getElementById("notification-list");
      const noNotificationsMsg = document.getElementById(
        "no-notifications-msg"
      );
      if (list && list.querySelector(".notification-item") === null) {
        if (noNotificationsMsg) noNotificationsMsg.classList.remove("hidden");
      }
    }, 300); // Tempo da animação

    // 5. Marca como lida no backend (em segundo plano)
    const formData = new FormData();
    formData.append("action", "mark_notification_read");
    formData.append("notification_id", notificationId);

    fetch("api.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        console.log("Resposta do backend ao marcar como lida:", data);
        if (!data.success) {
          console.error(
            "Falha ao marcar notificação como lida no backend:",
            data.message
          );
        }
      })
      .catch((error) => {
        console.error("Erro na requisição para marcar como lida:", error);
      });
  });

  // --- Lógica para abrir/fechar o painel de notificações ---
  if (notificationToggle) {
    notificationToggle.addEventListener("click", (e) => {
      e.stopPropagation(); // Impede que o clique feche o painel imediatamente
      if (notificationPanel.classList.contains("hidden")) {
        openNotificationPanel();
      } else {
        closeNotificationPanel();
      }
    });
  }

  // Adiciona listener para o botão de fechar no modo mobile
  if (closeNotificationPanelMobile) {
    closeNotificationPanelMobile.addEventListener("click", () => {
      closeNotificationPanel();
    });
  }

  // Fecha o painel se clicar fora dele
  document.addEventListener("click", (e) => {
    // Se o painel já estiver escondido ou em processo de fechar, não faz nada.
    if (
      notificationPanel.classList.contains("hidden") ||
      notificationPanel.classList.contains("opacity-0")
    ) {
      return;
    }

    // No mobile, o painel é um modal de tela cheia, fechado pelo seu próprio botão.
    // Esta lógica de "clicar fora" deve ser aplicada apenas no desktop.
    const isDesktop = window.innerWidth >= 768; // Breakpoint 'md' do Tailwind

    if (
      isDesktop &&
      !notificationPanel.contains(e.target) &&
      !notificationToggle.contains(e.target)
    ) {
      closeNotificationPanel();
    }
  });
});
