document.addEventListener("DOMContentLoaded", function () {
  var integralSwiperElement = document.querySelector(".integralSwiper");

  if (integralSwiperElement) {
    var swiper = new Swiper(".integralSwiper", {
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
    console.warn("Elemento .integralSwiper não encontrado na página.");
  }
});
