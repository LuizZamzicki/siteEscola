document.addEventListener("DOMContentLoaded", function () {
  const categoriasWrapper = document.getElementById("departamentos"); // O div que contém os botões
  const toggleBtn = document.getElementById("toggleCategorias"); // O botão de mostrar mais/menos

  // --- Lógica de Reposicionamento da Categoria Selecionada (NOVO) ---
  if (categoriasWrapper) {
    const todosBtn = categoriasWrapper.querySelector(
      'a.btn-categoria[href*="departamento=todos"]'
    );
    const activeCategoryBtn = categoriasWrapper.querySelector(
      'a.btn-categoria.active:not([href*="departamento=todos"])'
    );

    // Se uma categoria diferente de "Todos" está ativa e o botão "Todos" existe
    if (activeCategoryBtn && todosBtn) {
      // Remove o botão ativo de sua posição original
      activeCategoryBtn.remove();
      // Insere o botão ativo logo após o botão "Todos"
      // O método .after() insere o elemento após o elemento de referência
      todosBtn.after(activeCategoryBtn);
    }
  }
  // --- Fim da Lógica de Reposicionamento ---

  // --- Lógica de Mostrar Mais/Menos (Atualizada para Font Awesome) ---
  // Define a altura máxima inicial que deve ser a mesma do CSS
  const initialMaxHeight = "96px"; // **DEVE SER O MESMO VALOR DO CSS PARA max-height NO .categorias-wrapper**

  // Garante que a altura inicial esteja definida ao carregar a página
  if (categoriasWrapper) {
    categoriasWrapper.style.maxHeight = initialMaxHeight;
  }

  // Função para verificar se o conteúdo excede a altura inicial
  const checkIfOverflows = () => {
    if (!categoriasWrapper || !toggleBtn) return; // Se os elementos não existirem, encerra

    // Temporariamente remove max-height para obter a altura real de todo o conteúdo
    categoriasWrapper.style.maxHeight = "none";
    const hasOverflow =
      categoriasWrapper.scrollHeight > parseInt(initialMaxHeight);
    categoriasWrapper.style.maxHeight = initialMaxHeight; // Restaura a altura inicial

    if (hasOverflow) {
      toggleBtn.style.display = "flex"; // Mostra o botão (usando flex para centralizar o ícone)
    } else {
      toggleBtn.style.display = "none"; // Esconde o botão se não houver overflow
    }
  };

  // Chama a função ao carregar a página
  checkIfOverflows();

  // Chama a função sempre que a janela for redimensionada (para responsividade)
  window.addEventListener("resize", checkIfOverflows);

  // Define a função globalmente (se você a chamou via onclick no HTML)
  window.toggleCategorias = function () {
    if (!categoriasWrapper || !toggleBtn) return;

    // Pega o elemento <i> (ícone) dentro do botão de toggle
    const icon = toggleBtn.querySelector("i");
    if (!icon) return; // Se não encontrar o ícone, encerra

    if (categoriasWrapper.classList.contains("expandido")) {
      // Se está expandido, vamos colapsar
      categoriasWrapper.style.maxHeight = categoriasWrapper.scrollHeight + "px";
      requestAnimationFrame(() => {
        categoriasWrapper.style.maxHeight = initialMaxHeight;
        categoriasWrapper.classList.remove("expandido");
        icon.classList.remove("fa-chevron-up"); // Remove ícone de seta para cima
        icon.classList.add("fa-chevron-down"); // Adiciona ícone de seta para baixo
      });
    } else {
      // Se está colapsado, vamos expandir
      categoriasWrapper.style.maxHeight = categoriasWrapper.scrollHeight + "px";
      categoriasWrapper.classList.add("expandido");
      icon.classList.remove("fa-chevron-down"); // Remove ícone de seta para baixo
      icon.classList.add("fa-chevron-up"); // Adiciona ícone de seta para cima
    }
  };

  // Evento para remover o max-height após a transição de expansão
  // para garantir flexibilidade total do conteúdo quando expandido
  if (categoriasWrapper) {
    categoriasWrapper.addEventListener("transitionend", function () {
      if (categoriasWrapper.classList.contains("expandido")) {
        categoriasWrapper.style.maxHeight = "none";
      }
    });
  }
});
