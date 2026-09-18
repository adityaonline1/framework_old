(function ($) {
  "use strict"; // Start of use strict

  // Preloader
  $(window).on('load', function () {
    $('.preloader').fadeOut(500);
  });

  // Toggle the side navigation
  $(document).ready(function () {
    $("#sidebar").mCustomScrollbar({
      theme: "minimal"
    });

    $('#sidebarCollapse').on('click', function () {
      $('#sidebar, .header-area, .main-wrapper').toggleClass('active');
      if ($(".menu-overlay").css("display") == 'block')
        $(".menu-overlay").fadeOut(500);
      else
        $(".menu-overlay").fadeIn(500);
    });

    $(".menu-overlay").click(function (event) {
      $("#sidebarCollapse").trigger("click");
      $(".menu-overlay").fadeOut(500);
    });

  });

  
    // Choose branches JS
    // $('.branch-items.dropdown-menu a').click(function () {
    //   $('#selected').text($(this).text());
    // });

    //message-center
    $(document).ready(function () {
      $(".message-center, .notification-center, .todo-block, .activity-block, .conversation-body").mCustomScrollbar({
        theme: "minimal"
      });
    });



    // Scroll to top button appear
    $(document).on('scroll', function () {
      var scrollDistance = $(this).scrollTop();
      if (scrollDistance > 100) {
        $('.scroll-to-top').fadeIn();
      } else {
        $('.scroll-to-top').fadeOut();
      }
    });

    // Smooth scrolling using jQuery easing
    $(document).on('click', 'a.scroll-to-top', function (event) {
      var $anchor = $(this);
      $('html, body').stop().animate({
        scrollTop: ($($anchor.attr('href')).offset().top)
      }, 1000, 'easeInOutExpo');
      event.preventDefault();
    });

  })(jQuery); // End of use strict