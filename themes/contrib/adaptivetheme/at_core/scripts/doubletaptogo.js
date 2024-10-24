/**
 * Double Tap To Go
 *
 * Fork: by Jeff Burnz https://github.com/jmburnz/DoubleTapToGo
 *
 * Originally by Osvaldas Valutis, www.osvaldas.info
 * unbind & other improvements by https://github.com/zenopopovici/DoubleTapToGo
 * License MIT & GPL 2.0
 *
 * TODO: upstream request to include the zenopopovici version in cdnjs.com:
 * https://github.com/cdnjs/cdnjs/issues/8439
 */
(function ($, window, document) {

  "use strict";

  Drupal.behaviors.atdoubleTap = {
    attach: function () {

      $.fn.doubleTapToGo = function(action) {

        if (!('ontouchstart' in window) &&
          !navigator.msMaxTouchPoints &&
          !navigator.userAgent.toLowerCase().match( /windows phone os 7/i )) return false;

        if (action === 'unbind') {
          this.each(function() {
            $(this).off();
            $(document).off('click touchstart MSPointerDown', handleTouch);
          });

        } else {
          this.each(function() {
            var curItem = false;

            $(this).on('click', function(e) {
              var item = $(this);
              if (item[0] != curItem[0]) {
                e.preventDefault();
                curItem = item;
              }
            });

            $(document).on('click touchstart MSPointerDown', handleTouch);

            function handleTouch(e) {
              var resetItem = true,
                parents = $(e.target).parents();

              for (var i = 0; i < parents.length; i++)
                if (parents[i] == curItem[0])
                  resetItem = false;

              if(resetItem)
                curItem = false;
            }
          });
        }

        return this;
      };
    }
  };
})(jQuery, window, document);
