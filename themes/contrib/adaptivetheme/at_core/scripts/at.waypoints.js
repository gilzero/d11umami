// Get attributes for waypoints and init.
(function ($) {

  "use strict";

  Drupal.behaviors.atWayPoints = {
    attach: function () {

      function wpScrollInit(parameters) {

        var items = parameters.items;

        items.each(function() {

          var wpElement = $(this),
              wpAnimationClass = wpElement.attr('data-wp-animation'),
              wpAnimationDelay = wpElement.attr('data-wp-animation-delay'),
              wpOffset         = wpElement.attr('data-wp-animation-offset'),
              wpSticky         = wpElement.attr('data-wp-sticky');

          if (wpAnimationClass) {
            wpElement.css({
              '-webkit-animation-delay':  wpAnimationDelay,
              '-moz-animation-delay':     wpAnimationDelay,
              'animation-delay':          wpAnimationDelay
            });

            wpElement.waypoint(function() {
              wpElement.addClass('animated').addClass(wpAnimationClass);
              }, {
                offset: wpOffset ? wpOffset: 'bottom-in-view'
            });
          }

          if (wpSticky) {
            var sticky = new Waypoint.Sticky({
              element: $(wpElement)[0]
            })
          }

        });
      }

      wpScrollInit({items: $('.wp-waypoint')});
    }
  };
}(jQuery));
