/* Load jQuery.
------------------------------------------------*/
jQuery(document).ready(function ($) {
  // Mobile menu.
  $('.mobile-menu').click(function () {
    $(this).toggleClass('menu-icon-active');
    $(this).next('.primary-menu-wrapper').toggleClass('active-menu');
  });
  $('.close-mobile-menu').click(function () {
    $(this).closest('.primary-menu-wrapper').toggleClass('active-menu');
    $('.mobile-menu').removeClass('menu-icon-active');
  });
  // Full Screen header.
  function fullscreen() {
    var headerheight = $('.header-main').outerHeight();
    $('.slider').css({ height: $(window).height() - headerheight })
  }
  fullscreen();
  $(window).resize(function () {
  	fullscreen();
  });
  // Full page search.
  $('.search-icon').on('click', function() {
    $('.search-box').addClass('open');
    return false;
  });

  $('.search-box-close').on('click', function() {
    $('.search-box').removeClass('open');
    return false;
  });

  // Scroll To Top.
  $(window).scroll(function () {
    if ($(this).scrollTop() > 80) {
      $('.scrolltop').css('display', 'grid');
    } else {
      $('.scrolltop').fadeOut('slow');
    }
  });
  $('.scrolltop').click(function () {
    $('html, body').scrollTop(0);
  });

// End document ready.
});
