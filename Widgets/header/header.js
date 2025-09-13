document.addEventListener("DOMContentLoaded", () => {
  const userMenuButton = document.getElementById("user-menu-button");
  const userMenuDropdown = document.getElementById("user-menu-dropdown");

  if (!userMenuButton || !userMenuDropdown) {
    return;
  }

  const openDropdown = () => {
    userMenuDropdown.classList.remove("hidden");
    setTimeout(() => {
      userMenuDropdown.classList.remove("opacity-0", "-translate-y-2");
    }, 10);
  };

  const closeDropdown = () => {
    userMenuDropdown.classList.add("opacity-0", "-translate-y-2");
    setTimeout(() => {
      userMenuDropdown.classList.add("hidden");
    }, 200); // Corresponde à duração da transição
  };

  userMenuButton.addEventListener("click", (e) => {
    e.stopPropagation();
    if (userMenuDropdown.classList.contains("hidden")) {
      openDropdown();
    } else {
      closeDropdown();
    }
  });

  // Fecha o dropdown ao clicar fora dele
  document.addEventListener("click", (e) => {
    if (
      !userMenuDropdown.classList.contains("hidden") &&
      !userMenuDropdown.contains(e.target)
    ) {
      closeDropdown();
    }
  });
});
