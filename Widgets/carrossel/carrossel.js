function inicializarSwiper(selector) {
  const elemento = document.querySelector(selector);

  if (elemento) {
    const numeroDeSlides = elemento.querySelectorAll(".swiper-slide").length;

    const slidesMinimosParaLoop = 2;

    let swiperOptions = {
      effect: "slide",
      grabCursor: true,
      centeredSlides: true,
      slidesPerView: 1,
      loop: numeroDeSlides >= slidesMinimosParaLoop,
      autoplay: {
        delay: 5000,
        disableOnInteraction: true,
      },
      navigation: {
        nextEl: selector + " .swiper-button-next",
        prevEl: selector + " .swiper-button-prev",
      },
      pagination: {
        el: selector + " .swiper-pagination",
        clickable: true,
      },
      breakpoints: {
        768: { slidesPerView: 1 },
        576: { slidesPerView: 1 },
      },
    };

    const swiper = new Swiper(selector, swiperOptions);
  } else {
    console.warn("Elemento Swiper não encontrado para o seletor:", selector);
  }
}
