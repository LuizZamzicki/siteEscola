//Itinerários

document.addEventListener("DOMContentLoaded", function () {
  var itinerariosSwiperElement = document.querySelector(".itinerariosSwiper");

  if (itinerariosSwiperElement) {
    var swiper = new Swiper(".itinerariosSwiper", {
      effect: "slide",
      grabCursor: true,
      centeredSlides: true,
      slidesPerView: 1,
      loop: true,

      autoplay: {
        delay: 5000,
        disableOnInteraction: true,
      },

      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },

      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      breakpoints: {
        768: {
          slidesPerView: 1,
        },
        576: {
          slidesPerView: 1,
        },
      },
    });
  } else {
    console.warn("Elemento .itinerariosSwiper não encontrado na página.");
  }
});

//Comentários
document.addEventListener("DOMContentLoaded", function () {
  // ... (Seu código existente para o carrossel de itinerários) ...

  // Inicialização para o Carrossel de Comentários
  var comentariosSwiperElement = document.querySelector(".comentariosSwiper");

  if (comentariosSwiperElement) {
    var comentariosSwiper = new Swiper(".comentariosSwiper", {
      slidesPerView: 5, // Tenta mostrar 5 slides por vez
      spaceBetween: 30, // Espaçamento entre os cards
      centeredSlides: true, // O slide ativo (central) fica no meio
      loop: true, // Carrossel infinito
      grabCursor: true, // Cursor de "mão" para indicar arrasto
      slideToClickedSlide: true, // clicar nos cards passa pro lado

      // Animação de escala para o slide central
      on: {
        slideChangeTransitionEnd: function () {
          // Remove a classe de destaque de todos os slides
          comentariosSwiperElement
            .querySelectorAll(".comentario-card")
            .forEach((card) => {
              card.classList.remove("is-active-comment");
            });
          // Adiciona a classe de destaque ao slide ativo
          this.slides[this.activeIndex]
            .querySelector(".comentario-card")
            .classList.add("is-active-comment");
        },
        // Garante que o slide central esteja ativo no carregamento inicial
        init: function () {
          setTimeout(() => {
            // Pequeno delay para garantir que os slides sejam renderizados
            comentariosSwiperElement
              .querySelectorAll(".comentario-card")
              .forEach((card) => {
                card.classList.remove("is-active-comment");
              });
            this.slides[this.activeIndex]
              .querySelector(".comentario-card")
              .classList.add("is-active-comment");
          }, 100);
        },
      },

      // Navegação (Setas)
      navigation: {
        nextEl: ".comentariosSwiper .swiper-button-next",
        prevEl: ".comentariosSwiper .swiper-button-prev",
      },

      // Paginação (Bolinhas)
      pagination: {
        el: ".comentariosSwiper .swiper-pagination",
        clickable: true,
      },

      // Responsividade: Ajusta slides por view em diferentes tamanhos de tela
      breakpoints: {
        1200: {
          // 5 slides em telas grandes (desktop)
          slidesPerView: 3,
          spaceBetween: 30,
        },
        992: {
          // 3 slides em telas médias (tablet paisagem)
          slidesPerView: 3,
          spaceBetween: 20,
        },
        768: {
          // 3 slides em telas menores (tablet retrato)
          slidesPerView: 3,
          spaceBetween: 15,
        },
        576: {
          // 1 slide em telas pequenas (celular)
          slidesPerView: 1,
          spaceBetween: 10,
        },
        0: {
          // Para telas muito pequenas (celular)
          slidesPerView: 1,
          spaceBetween: 10,
        },
      },
    });
  } else {
    console.warn("Elemento .comentariosSwiper não encontrado na página.");
  }
});
