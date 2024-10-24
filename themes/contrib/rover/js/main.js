jQuery(function () {
  jQuery(".user_profile").on("mouseenter", function () {
    jQuery('#account_nav').addClass('show');
    jQuery('#account_nav').on("mouseenter", function () {
      jQuery('#account_nav').addClass('show');
      jQuery(".user_profile").addClass('hover');
    }).on("mouseleave", function () {
      jQuery('#account_nav').removeClass('show');
      jQuery(".user_profile").removeClass('hover');
    });
  }).on("mouseleave", function () {
    jQuery('#account_nav').removeClass('show');
  });
});
(function ($, Drupal, once) {
    Drupal.behaviors.roverBehavior = {
      attach: function (context, settings) {
        once('roverBehavior', 'input.roverBehavior', context).forEach(function (element) {

        });
      }
    };
})(jQuery, Drupal, once);
