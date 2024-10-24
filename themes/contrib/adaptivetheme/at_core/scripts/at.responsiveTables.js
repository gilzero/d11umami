/**
 * @file
 * Responsive tables.
 */
(function ($, Drupal) {

  "use strict";

  $('table.forum').addClass('responsive-enabled');
  $('table.responsive-enabled').wrap('<div class="responsive-table"><div class="responsive-table__scroll"></div></div>');

  if ($('table.responsive-enabled').prop('scrollWidth') > $(".responsive-table").width() ) {
    var overflowmessage = Drupal.t('Scroll to view');
    $('.responsive-table__scroll').append('<div class="responsive-table__message"><em>' + overflowmessage + '</em></div>');
    $('table.responsive-enabled').addClass('has-overflow');
  }

})(jQuery, Drupal);
