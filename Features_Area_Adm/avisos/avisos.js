document.addEventListener("DOMContentLoaded", () => {
  // --- Elements ---
  const addNoticeModal = document.getElementById("add-notice-modal");
  const confirmationModal = document.getElementById(
    "confirmation-modal-avisos"
  );
  const noticeModalTitle = document.getElementById("notice-modal-title");
  const noticeForm = document.getElementById("notice-form");
  const noticeIdInput = document.getElementById("notice-id");
  const noticeTitleInput = document.getElementById("notice-title");
  const noticeContentInput = document.getElementById("notice-content");
  const noticeTargetInput = document.getElementById("notice-target");
  const noticeList = document.getElementById("notice-list");

  // --- Notice Logic ---

  // Open "Add Notice" modal
  document.querySelectorAll(".add-notice-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      noticeModalTitle.textContent = "Criar Novo Aviso";
      noticeForm.reset();
      noticeIdInput.value = "";
      window.ModalManager.open(addNoticeModal);
      setTimeout(() => {
        const firstFocusable = addNoticeModal.querySelector(
          'input:not([type="hidden"]), select, textarea, button'
        );
        if (firstFocusable) {
          firstFocusable.focus();
        }
      }, 100); // Delay to ensure modal is visible
    });
  });

  // Event delegation for Edit and Delete buttons
  if (noticeList) {
    noticeList.addEventListener("click", (e) => {
      const editBtn = e.target.closest(".edit-notice-btn");
      const deleteBtn = e.target.closest(".delete-notice-btn");

      if (!editBtn && !deleteBtn) return;

      const listItem = e.target.closest("li");
      if (!listItem) return;

      const noticeId = listItem.dataset.noticeId;
      const title = listItem.dataset.title;
      const fullContent = listItem.dataset.content;
      const target = listItem.dataset.target;

      if (editBtn) {
        noticeModalTitle.textContent = "Editar Aviso";
        noticeIdInput.value = noticeId;
        noticeTitleInput.value = title;
        noticeContentInput.value = fullContent;
        noticeTargetInput.value = target;
        window.ModalManager.open(addNoticeModal);
        setTimeout(() => {
          const firstFocusable = addNoticeModal.querySelector(
            'input:not([type="hidden"]), select, textarea, button'
          );
          if (firstFocusable) {
            firstFocusable.focus();
          }
        }, 100); // Delay to ensure modal is visible
      }

      if (deleteBtn) {
        const confirmationMessage = document.getElementById(
          "confirmation-message-avisos"
        );
        const noticeIdDeleteInput = document.getElementById("notice-id-delete");
        confirmationMessage.innerHTML = `Tem certeza que deseja excluir o aviso <strong>"${title}"</strong>? Esta ação não pode ser desfeita.`;
        noticeIdDeleteInput.value = noticeId;
        window.ModalManager.open(confirmationModal);
        setTimeout(() => {
          const cancelButton =
            confirmationModal.querySelector(".cancel-modal-btn");
          if (cancelButton) {
            cancelButton.focus();
          }
        }, 100); // Foca no botão de cancelar por segurança
      }
    });
  }
});
