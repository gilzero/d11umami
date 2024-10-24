/**
 * @file
 * Center element vertically.
 *
 * ## Usage
 * Call the function on the element to be vertically centered in it's parent.
 * The param pS should be the next outer container that we need to get the
 * height for.
 *
 * <div class="outer-container">
 *   <div class="parent-container">
 *     <nav class="element">...</nav>
 *   </div>
 * </div>
 *
 * $(document).ready(function() {
 *   $('.element').atFlexCenter();
 *   $('.element').atFlexCenter({ verticalPosition: 'center' });
 *   $('.element').atFlexCenter({ verticalPosition: 'center', horizontalPosition: 'left', parentSelector: '.outer-container' });
 * });
 */
(function($){
  $.fn.atFlexCenter = function(options) {

    var settings = $.extend({
      verticalPosition: null,
      horizontalPosition: null,
      parentSelector: null
    }, options || {});

    return this.each(function(){
      if(settings.parentSelector) {
        $(this).parent().addClass('is-flexposition-parent');
      }
      if(settings.verticalPosition || settings.horizontalPosition) {
        $(this).addClass('is-flexposition-container');
      }
      if(settings.verticalPosition) {
        $(this).addClass('is-vertical-' + settings.verticalPosition);
      }
      if(settings.horizontalPosition) {
        $(this).addClass('is-horizontal-' + settings.horizontalPosition);
      }
    });
  };
})(jQuery);
