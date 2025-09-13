document.addEventListener("DOMContentLoaded", () => {
  const openModal = (modal) => {
    if (!modal) return;
    modal.classList.remove("hidden", "opacity-0", "pointer-events-none");
    const panel = modal.querySelector(".modal-panel");
    if (panel) {
      setTimeout(() => {
        modal.classList.remove("opacity-0");
        panel.classList.remove("-translate-y-10");
      }, 10);
    }
  };

  const closeModal = (modal) => {
    if (!modal) return;
    modal.classList.add("opacity-0");
    const panel = modal.querySelector(".modal-panel");
    if (panel) {
      panel.classList.add("-translate-y-10");
    }
    setTimeout(() => {
      modal.classList.add("hidden", "pointer-events-none");
    }, 300);
  };

  // Torna as funções globais para serem acessíveis por scripts inline ou outros arquivos
  window.ModalManager = {
    open: openModal,
    close: closeModal,
  };

  // Lógica genérica para fechar modais
  document.addEventListener("click", (e) => {
    const closeButton = e.target.closest(".close-modal-btn, .cancel-modal-btn");
    if (closeButton) {
      closeModal(closeButton.closest(".modal-backdrop"));
      return;
    }

    if (e.target.classList.contains("modal-backdrop")) {
      closeModal(e.target);
    }
  });
});
