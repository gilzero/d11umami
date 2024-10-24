/**
 * @file
 * Responsive Columns
 * We need to know if layout columns will fit in the horizontal space.
 */
(function ($, Drupal, window) {

  'use strict';

  function init(i, columns) {

    var rep_cols = $(columns);

    function handleResize(e) {
      rep_cols.addClass('is-horizontal');

      var layout_cols = rep_cols.find('.is-responsive__layout');
      var layout_cols_width = 0;

      layout_cols.find('.is-responsive__column').each(function() {
        layout_cols_width += $(this).outerWidth(true);
      });

      var isHorizontal = layout_cols.outerWidth(true) >= layout_cols_width;

      if (isHorizontal == false) {
        rep_cols.removeClass('is-horizontal').addClass('is-vertical');
      } else {
        rep_cols.removeClass('is-vertical').addClass('is-horizontal');
      }
    }

    $(window).on('resize.layout_cols', Drupal.debounce(handleResize, 150)).trigger('resize.layout_cols');
  }

  // Initialize the Responsive Cols.
  Drupal.behaviors.atRC = {
    attach: function (context) {
      var columns = $(context).find('[data-at-responsive-columns]');
      if (columns.length) {
        columns.once().each(init);
      }
    }
  };
})(jQuery, Drupal, window);
