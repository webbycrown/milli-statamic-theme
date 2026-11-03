$(document).ready(function () {
  $(window).scroll(function () {
    if ($(this).scrollTop() > 1) {
      $("#header").addClass("header-scrolled");
    } else {
      $("#header").removeClass("header-scrolled");
    }
  });

  //===== mobile-toggle-bar =====//

  $(".mobile-drop").click(function () {
    $(".mobile-pages .sub-menu").slideToggle();
  });

  //== menu-bar ==//

  $("#menu span").on("click", function () {
    $(".menu-bar").addClass("show");
  });
  $(".menu-close").on("click", function () {
    $(".menu-bar").removeClass("show");
  });

  //== search-bar ==//

  // $("#search span").on("click", function () {
  //   $("#header").toggleClass("serch-open");
  // });
  $("#search span .search-bugger").on("click", function () {
    $("#header").toggleClass("serch-open");
  });

  //-- news-swiper --//

  var swiper = new Swiper(".news-swiper", {
    direction: "vertical",
    // loop:true,
    slidesPerView: 3,
    spaceBetween: 30,
    navigation: {
      nextEl: ".news-swiper .swiper-button-next",
      prevEl: ".news-swiper .swiper-button-prev",
    },
  });

  //-- edition-swiper --//

  var swiper = new Swiper(".edition-swiper", {
    slidesPerView: 2,
    loop: true,
    navigation: {
      nextEl: ".edition-swiper .swiper-button-next",
      prevEl: ".edition-swiper .swiper-button-prev",
    },
    breakpoints: {
      0: {
        slidesPerView: 1,
        spaceBetween: 0,
      },
      575: {
        slidesPerView: 2,
        spaceBetween: 30,
      },
      768: {
        slidesPerView: 2,
        spaceBetween: 0,
      },
    },
  });

  //-- blog-swiper --//
  var swiper = new Swiper(".blog-section .blog-swiper", {
    slidesPerView: 1,
    navigation: {
      nextEl: ".blog-section .swiper-button-next",
      prevEl: ".blog-section .swiper-button-prev",
    },
    breakpoints: {
      0: {
        slidesPerView: 1,
        spaceBetween: 20,
      },
      575: {
        slidesPerView: 1,
        spaceBetween: 20,
      },
      768: {
        slidesPerView: 1,
        spaceBetween: 20,
      },
    },
  });

  //-- culture-swiper --//

  var swiper = new Swiper(".culture-section .culture-swiper", {
    loop: true,
    spaceBetween: 50,
    breakpoints: {
      0: {
        slidesPerView: 1,
        spaceBetween: 30,
      },
      575: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      991: {
        slidesPerView: 3,
        spaceBetween: 30,
      },
      1440: {
        slidesPerView: 3.3,
        spaceBetween: 50,
      },
    },
  });

  //-- culture-pase-2-swiper --//
  var swiper = new Swiper(".culture-section-pase-2 .culture-swiper", {
    loop: true,
    spaceBetween: 20,
    breakpoints: {
      0: {
        slidesPerView: 1,
        spaceBetween: 20,
      },
      575: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      991: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
      1440: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
    },
  });
  //-- edition-swiper-pase-3 --//
  var swiper = new Swiper(".edition-swiper-3", {
    slidesPerView: 2,
    spaceBetween: 30,
    loop: true,
    navigation: {
      nextEl: ".edition-swiper-3 .swiper-button-next",
      prevEl: ".edition-swiper-3 .swiper-button-prev",
    },
    breakpoints: {
      0: {
        slidesPerView: 1,
        spaceBetween: 20,
      },
      575: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
      1440: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
    },
  });
  //-- edition-swiper-pase-3 --//
  var swiper = new Swiper(".edition-milli .swiper", {
    slidesPerView: 1,
    loop: true,
    centeredSlides: false,
    navigation: {
      nextEl: ".edition-milli .swiper-button-next",
      prevEl: ".edition-milli .swiper-button-prev",
    },
    breakpoints: {
      0: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      575: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
      1440: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
    },
  });

  // --thumbline-swiper-workshopp---//

  var swiper = new Swiper(".workshop-section .mySwiper", {
    loop: true,
    spaceBetween: 10,
    slidesPerView: 1,
    freeMode: true,
    watchSlidesProgress: true,
  });
  var swiper2 = new Swiper(".workshop-section .mySwiper2", {
    loop: true,
    spaceBetween: 10,
    slidesPerView: 1,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    thumbs: {
      swiper: swiper,
    },
  });

  //-- FAQ--//

  // const accordion = document.getElementsByClassName("content-container");

  // for (let i = 0; i < accordion.length; i++) {
  //   accordion[i].addEventListener("click", function () {
  //     this.classList.toggle("active");
  //     console.log(this);
  //   });
  // }

  $("body").on("click", ".accordion .accordion-tabs", function () {
    var $tab = $(this);
    if ($tab.hasClass("acco-active")) {
      $tab.next(".accordion-content").slideUp();
      $tab.removeClass("acco-active").attr("aria-expanded", "false");
    } else {
      $(".accordion .accordion-tabs")
        .removeClass("acco-active")
        .attr("aria-expanded", "false");
      $(".accordion-content").slideUp();
      $tab.addClass("acco-active").attr("aria-expanded", "true");
      $tab.next(".accordion-content").slideDown();
    }
  });

  // FAQ page search field — clear + filter accordion items
  $("#clearBtn").on("click", function () {
    $("#queryFind").val("").trigger("input").focus();
  });
  $("#queryFind").on("input", function () {
    var q = String($(this).val() || "")
      .toLowerCase()
      .trim();
    $("[data-faq-item]").each(function () {
      var text = $(this).text().toLowerCase();
      $(this).toggle(!q || text.indexOf(q) !== -1);
    });
  });

  //-- edition-swiper --//

  var swiper = new Swiper(".inner-pages-swiper", {
    slidesPerView: 2,
    centeredSlides: true,
    loop: true,
    navigation: {
      nextEl: ".inner-pages-swiper .swiper-button-next",
      prevEl: ".inner-pages-swiper .swiper-button-prev",
    },
    breakpoints: {
      0: {
        slidesPerView: 1,
        spaceBetween: 0,
      },
      575: {
        slidesPerView: 2,
        spaceBetween: 30,
      },
      768: {
        slidesPerView: 2,
        spaceBetween: 30,
      },
    },
  });

  //-- cursor --//

  $(".culture-section")
    .mousemove(function (e) {
      $(".cursor").show().css({
        left: e.clientX,
        top: e.clientY,
      });
    })
    .mouseout(function () {
      $(".cursor").hide();
    });

  //-- circle text (must run after circletype.min.js) --//
  var circleEl = document.getElementById("circle-text");
  if (circleEl && typeof CircleType === "function") {
    new CircleType(circleEl).radius(72);
  }

  //-- aos-animation --//
  // Init first so in-view elements get .aos-animate, then drop the FOUC
  // override so below-fold sections can animate on scroll.
  document.documentElement.classList.add("milli-css-ready");
  var fouc = document.getElementById("milli-fouc-fix");
  if (fouc) fouc.remove();
  if (typeof AOS !== "undefined") {
    AOS.init({ once: true });
    requestAnimationFrame(function () {
      var fix = document.getElementById("aos-fouc-fix");
      if (fix) fix.remove();
    });
  }
});

$(document).ready(function () {
  if (typeof $.fn.magnificPopup === "function") {
    $(".popup-with-form").magnificPopup({
      type: "inline",
      preloader: false,
      focus: "#career-name",
      midClick: true,
      removalDelay: 100,
      mainClass: "mfp-fade",
      closeBtnInside: true,
    });
  }
});

