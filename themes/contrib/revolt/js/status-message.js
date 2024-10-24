(function ($, Drupal) {
  Drupal.behaviors.vtstatusBehavior = {
    attach: function (context, settings) {
      $('.vt-status-message .vt-message .vt-btn-close').click(function () {
        $('.vt-status-message .vt-message').remove();
      });
    }
  };
})(jQuery, Drupal);
