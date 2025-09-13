function inicializarSwiperMultiplo(selector, qtdeSlides) {
  var elemento = document.querySelector(selector);

  if (elemento) {
    let swiperOptions = {
      slidesPerView: qtdeSlides,
      spaceBetween: 30,
      centeredSlides: true,
      loop: true,
      grabCursor: true,
      slideToClickedSlide: true,

      on: {
        slideChangeTransitionEnd: function () {
          elemento
            .querySelectorAll(".carrosselMultiplo-card")
            .forEach((card) => {
              card.classList.remove("is-active");
            });
          this.slides[this.activeIndex]
            .querySelector(".carrosselMultiplo-card")
            .classList.add("is-active");
        },
        init: function () {
          setTimeout(() => {
            elemento
              .querySelectorAll(".carrosselMultiplo-card")
              .forEach((card) => {
                card.classList.remove("is-active");
              });
            this.slides[this.activeIndex]
              .querySelector(".carrosselMultiplo-card")
              .classList.add("is-active");
          }, 100);
        },
      },

      navigation: {
        nextEl: selector + " .swiper-button-next",
        prevEl: selector + " .swiper-button-prev",
      },

      pagination: {
        el: selector + " .swiper-pagination",
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
    };

    new Swiper(selector, swiperOptions);
  } else {
    console.warn("Elemento .SwiperMultiplo não encontrado na página.");
  }
}
