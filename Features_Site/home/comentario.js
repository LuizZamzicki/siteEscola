// Carrossel de Comentários
document.addEventListener("DOMContentLoaded", function () {
  var comentariosSwiperElement = document.querySelector(".comentariosSwiper");

  if (comentariosSwiperElement) {
    var comentariosSwiper = new Swiper(".comentariosSwiper", {
      slidesPerView: 5,
      spaceBetween: 30,
      centeredSlides: true,
      loop: true,
      grabCursor: true,
      slideToClickedSlide: true,

      // Destaque visual para o slide ativo
      on: {
        slideChangeTransitionEnd: function () {
          comentariosSwiperElement
            .querySelectorAll(".comentario-card")
            .forEach((card) => {
              card.classList.remove("is-active-comment");
            });
          this.slides[this.activeIndex]
            .querySelector(".comentario-card")
            .classList.add("is-active-comment");
        },
        init: function () {
          setTimeout(() => {
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

      navigation: {
        nextEl: ".comentariosSwiper .swiper-button-next",
        prevEl: ".comentariosSwiper .swiper-button-prev",
      },

      pagination: {
        el: ".comentariosSwiper .swiper-pagination",
        clickable: true,
      },

      // Responsividade
      breakpoints: {
        1200: {
          slidesPerView: 3,
          spaceBetween: 30,
        },
        992: {
          slidesPerView: 3,
          spaceBetween: 20,
        },
        768: {
          slidesPerView: 3,
          spaceBetween: 15,
        },
        576: {
          slidesPerView: 1,
          spaceBetween: 10,
        },
        0: {
          slidesPerView: 1,
          spaceBetween: 10,
        },
      },
    });
  } else {
    console.warn("Elemento .comentariosSwiper não encontrado na página.");
  }
});
