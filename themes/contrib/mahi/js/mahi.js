/* Load jQuery.
------------------------------------------------*/
jQuery(document).ready(function ($) {
  // Mobile menu.
  $('.mobile-menu-icon').click(function () {
    $(this).toggleClass('menu-icon-active');
    $(this).next('.primary-menu-wrapper').toggleClass('active-menu');
  });
  $('.close-mobile-menu').click(function () {
    $(this).closest('.primary-menu-wrapper').toggleClass('active-menu');
    $('.mobile-menu-icon').removeClass('menu-icon-active');
  });

  // Full page search.
  $('.search-icon, .search-box-close').click(function () {
    $('.search-box').toggleClass('active-search');
    return false;
  });

  // Scroll To Top.
  $(window).scroll(function () {
    if ($(this).scrollTop() > 80) {
      $('.scrolltop').css('display', 'flex');
    } else {
      $('.scrolltop').fadeOut('slow');
    }
  });
  $('.scrolltop').click(function () {
    $('html, body').scrollTop(0);
  });
  // services
  $('.service').hover(function(){
    $(this).toggleClass('dark');
  });
  $('.service:nth-child(even)').addClass('dark');
// End document ready.
});


