(function ($, Drupal) {
  $.extend(Drupal.theme, {
    tableDragChangedWarning: function tableDragChangedWarning() {
      const marker = Drupal.theme("tableDragChangedMarker");
      const message = Drupal.t("You have unsaved changes.");
      return `<div class="tabledrag-changed-warning alert message" role="alert">${marker} ${message}</div>`;
    },
  });
})(jQuery, Drupal);
