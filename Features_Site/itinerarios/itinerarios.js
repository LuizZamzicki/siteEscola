//Filtros categoria dos itinerários
document.addEventListener("DOMContentLoaded", function () {
  const categoriasWrapper = document.getElementById("categorias");
  const toggleBtn = document.getElementById("toggleCategorias");

  // Reposiciona a categoria ativa após o botão "Todos"
  if (categoriasWrapper) {
    const todosBtn = categoriasWrapper.querySelector(
      'a.btn-categoria[href*="categoria=todos"]'
    );
    const activeCategoryBtn = categoriasWrapper.querySelector(
      'a.btn-categoria.active:not([href*="categoria=todos"])'
    );

    if (activeCategoryBtn && todosBtn) {
      activeCategoryBtn.remove();
      todosBtn.after(activeCategoryBtn);
    }
  }

  const initialMaxHeight = "96px"; // Deve coincidir com o valor definido no CSS

  if (categoriasWrapper) {
    categoriasWrapper.style.maxHeight = initialMaxHeight;
  }

  const checkIfOverflows = () => {
    if (!categoriasWrapper || !toggleBtn) return;

    categoriasWrapper.style.maxHeight = "none";
    const hasOverflow =
      categoriasWrapper.scrollHeight > parseInt(initialMaxHeight);
    categoriasWrapper.style.maxHeight = initialMaxHeight;

    toggleBtn.style.display = hasOverflow ? "flex" : "none";
  };

  checkIfOverflows();
  window.addEventListener("resize", checkIfOverflows);

  window.toggleCategorias = function () {
    if (!categoriasWrapper || !toggleBtn) return;

    const icon = toggleBtn.querySelector("i");
    if (!icon) return;

    if (categoriasWrapper.classList.contains("expandido")) {
      categoriasWrapper.style.maxHeight = categoriasWrapper.scrollHeight + "px";
      requestAnimationFrame(() => {
        categoriasWrapper.style.maxHeight = initialMaxHeight;
        categoriasWrapper.classList.remove("expandido");
        icon.classList.remove("fa-chevron-up");
        icon.classList.add("fa-chevron-down");
      });
    } else {
      categoriasWrapper.style.maxHeight = categoriasWrapper.scrollHeight + "px";
      categoriasWrapper.classList.add("expandido");
      icon.classList.remove("fa-chevron-down");
      icon.classList.add("fa-chevron-up");
    }
  };

  if (categoriasWrapper) {
    categoriasWrapper.addEventListener("transitionend", function () {
      if (categoriasWrapper.classList.contains("expandido")) {
        categoriasWrapper.style.maxHeight = "none";
      }
    });
  }
});
