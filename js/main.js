document.addEventListener("DOMContentLoaded", function () {
  const navbar = document.querySelector(".custom-navbar-bg");
  let lastScrollTop = 0; // Armazena a última posição de rolagem
  const scrollThreshold = 100; // Distância mínima para rolar para baixo antes de esconder

  // Adiciona a classe 'navbar-show' ao carregar a página para que o header esteja visível inicialmente
  if (navbar) {
    navbar.classList.add("navbar-show");
  }

  window.addEventListener("scroll", function () {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop; // Posição atual da rolagem

    // Esconde a navbar se o usuário rolar para baixo mais de 'scrollThreshold' pixels
    if (scrollTop > lastScrollTop && scrollTop > scrollThreshold) {
      if (navbar) {
        navbar.classList.remove("navbar-show");
        navbar.classList.add("navbar-hide");
      }
    }
    // Mostra a navbar se o usuário rolar para cima (e já tiver rolado para baixo um pouco)
    else if (scrollTop < lastScrollTop && lastScrollTop > scrollThreshold) {
      if (navbar) {
        navbar.classList.remove("navbar-hide");
        navbar.classList.add("navbar-show");
      }
    }

    lastScrollTop = scrollTop; // Atualiza a última posição de rolagem
  });
});
