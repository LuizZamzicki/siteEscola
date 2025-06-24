//filtro de departamento dos funcionários, mesmo esquema dos itinerários, só troquei o nome

document.addEventListener("DOMContentLoaded", function () {
  const categoriasWrapper = document.getElementById("departamentos");
  const toggleBtn = document.getElementById("toggleCategorias");

  // Reposiciona a categoria ativa após o botão "Todos"
  if (categoriasWrapper) {
    const todosBtn = categoriasWrapper.querySelector(
      'a.btn-categoria[href*="departamento=todos"]'
    );
    const activeCategoryBtn = categoriasWrapper.querySelector(
      'a.btn-categoria.active:not([href*="departamento=todos"])'
    );

    if (activeCategoryBtn && todosBtn) {
      activeCategoryBtn.remove();
      todosBtn.after(activeCategoryBtn);
    }
  }

  const initialMaxHeight = "96px";

  if (categoriasWrapper) {
    categoriasWrapper.style.maxHeight = initialMaxHeight;
  }

  const checkIfOverflows = () => {
    if (!categoriasWrapper || !toggleBtn) return;

    categoriasWrapper.style.maxHeight = "none";
    const hasOverflow =
      categoriasWrapper.scrollHeight > parseInt(initialMaxHeight);
    categoriasWrapper.style.maxHeight = initialMaxHeight;

    if (hasOverflow) {
      toggleBtn.style.display = "flex";
    } else {
      toggleBtn.style.display = "none";
    }
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
