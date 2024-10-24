/**
 * @file
 * Force reloads on orientation change.
 */
(function () {
  Drupal.behaviors.atOrientationChangeReload = {
    attach: function () {
      window.addEventListener('orientationchange', function () {
        // Wipe out the dom.
        document.body.style.display = 'none';
        // Reload from cache if available.
        window.location.reload(false);
      });
    }
  };
}());
