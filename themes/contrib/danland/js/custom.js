// Responsive menu toggle
jQuery(document).ready(function($) {
  $( ".responsive-menu-icon" ).click(function() {
    $( ".main-navigation-menu" ).toggleClass("active");
    $(this).toggleClass("close");
  });
});
