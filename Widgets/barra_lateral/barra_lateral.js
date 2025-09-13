document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById("sidebar");
  const menuToggle = document.getElementById("menu-toggle");
  const sidebarOverlay = document.getElementById("sidebar-overlay");

  // --- Mobile Sidebar Toggle ---
  if (sidebar && menuToggle && sidebarOverlay) {
    const closeSidebar = () => {
      sidebar.classList.add("-translate-x-full");
      sidebarOverlay.classList.add("hidden");
    };

    menuToggle.addEventListener("click", () => {
      // Usar 'toggle' é mais simples e robusto, resolvendo o conflito
      // que ocorria ao ter a mesma lógica em múltiplos arquivos.
      sidebar.classList.toggle("-translate-x-full");
      sidebarOverlay.classList.toggle("hidden");
    });

    sidebarOverlay.addEventListener("click", closeSidebar);
  }

  // Submenu toggling
  document.querySelectorAll("[data-submenu-toggle]").forEach((toggleBtn) => {
    toggleBtn.addEventListener("click", (e) => {
      e.preventDefault();
      const submenuId = toggleBtn.dataset.submenuToggle;
      const submenu = document.getElementById(`submenu-${submenuId}`);
      const chevronIcon = toggleBtn.querySelector(".fa-chevron-down");

      if (submenu && chevronIcon) {
        submenu.classList.toggle("hidden");
        chevronIcon.classList.toggle("rotate-180");
      }
    });
  });
});
