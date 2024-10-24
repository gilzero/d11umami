/**
 * @file
 * Responsive Lists
 * This is based on the the Seven themes responsive tabs JS, except I have
 * generalized it so we can use it for any list. Also we use outerWidth and
 * calculate the total width of all list items.
 *
 * In AT we use this for tabs (local tasks), pagers and breadcrumbs.
 * See the breadcrumbs template and SCSS partial to see how this works.
 */
(function ($, Drupal, once, window) {

  'use strict';

  function init(i, list) {

    var rep_list = $(list);

    function handleResize(e) {
      rep_list.addClass('is-horizontal');

      var lists = rep_list.find('.is-responsive__list');
      var list_items_width = 0;

      lists.find('.is-responsive__item').each(function() {
        list_items_width += $(this).outerWidth(true);
      });

      var isVertical = lists.outerWidth(true) <= list_items_width;

      if (isVertical == true) {
        rep_list.removeClass('is-horizontal').addClass('is-vertical');
      } else {
        rep_list.removeClass('is-vertical').addClass('is-horizontal');
      }
    }

    $(window).on('resize.lists', Drupal.debounce(handleResize, 150)).trigger('resize.lists');
  }

  // Initialize the Responsive lists JS.
  Drupal.behaviors.atRL = {
    attach: function (context) {
      const list = Array.from($(context).find('[data-at-responsive-list]'));
      if (list.length) {
        // Use the new once function, and iterate over the elements using forEach instead of each.
        once('atRL-init', '[data-at-responsive-list]', context).forEach(init);
      }
    }
  };

})(jQuery, Drupal, window);
