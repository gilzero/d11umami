/**
 * @file
 * Appearance settings.
 */
(function ($, Drupal) {
  Drupal.behaviors.atCoreLayoutPreview = {
    attach: function () {
      $('#edit-layout-select select.row-layout-select').change(function(){
        $('#' + $(this).attr('id')).parent().next().children().removeClass().addClass(this.value);
      });
    }
  };
}(jQuery, Drupal));
