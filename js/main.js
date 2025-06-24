document.addEventListener("DOMContentLoaded", function () {
  const navbar = document.querySelector(".custom-navbar-bg");
  let lastScrollTop = 0; 
  const scrollThreshold = 100; 

  if (navbar) {
    navbar.classList.add("navbar-show");
  }

  window.addEventListener("scroll", function () {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop; 

    if (scrollTop > lastScrollTop && scrollTop > scrollThreshold) {
      if (navbar) {
        navbar.classList.remove("navbar-show");
        navbar.classList.add("navbar-hide");
      }
    }
    
    else if (scrollTop < lastScrollTop && lastScrollTop > scrollThreshold) {
      if (navbar) {
        navbar.classList.remove("navbar-hide");
        navbar.classList.add("navbar-show");
      }
    }

    lastScrollTop = scrollTop;
  });
});
