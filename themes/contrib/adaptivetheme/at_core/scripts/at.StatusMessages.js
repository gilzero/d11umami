/**
 * @file
 * Remove empty markup when status messages are empty.
 */
(function ($) {

  "use strict";

  Drupal.behaviors.atStatusMessages = {
    attach: function () {
      var sm = $('*[data-drupal-messages]');
      // Remove if there is only one active region & status messages is empty.
      if (sm.parents('.arc--1').length && !$.trim($(sm).html())) {
        sm.parents('.l-pr').remove();
      }
    }
  };
}(jQuery));
