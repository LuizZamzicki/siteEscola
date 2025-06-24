//Carrossel Itinerários

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
