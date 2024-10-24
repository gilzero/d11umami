(function ($, Drupal, once) {
  Drupal.behaviors.vtBehavior = {
    attach: function (context, settings) {


      // for adjust the aside bar height based on window height
      var fullHeight = function() {
      $('.main-container aside.sidebar').css('height', $(window).height());
        $(window).resize(function(){
          $('.main-container aside.sidebar').css('height', $(window).height());
        });
      };
      fullHeight();

      // fancy checkbox js
      if (window.navigator.userAgent.indexOf("Edge") !== -1 || navigator.appVersion.indexOf("MSIE 10") !== -1 || window.navigator.userAgent.indexOf("Trident/7.0") > 0) { 
        document.documentElement.className += ' crappy-browser';
      } else {
          document.documentElement.className += ' modern-browser';
      }
      // Status massages js
      $('#close').click(function(){
        $('.js_basic-popup').removeClass('popup--visible');
      });
      $('.sidebar .mob_menu #menu').change(function(){
        if ($('.sidebar .mob_menu #menu').is(':checked')) {
          $('.sidebar .menu--main').css({"left": "0px", "transition": "left 0.3s ease"});
        }
        else{
          $('.sidebar .menu--main').css({"left": "-100%", "transition": "left 0.3s ease"});
        }
      });
    }
  };
})(jQuery, Drupal, once);
