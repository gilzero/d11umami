/* Use Drupal library file 'weight' key (negative numbers only) to ensure this is loaded after all other Rivet JS libraries. */
(function (Drupal, Rivet) {
  Drupal.behaviors.rivet_init = {
    attach(context) {
      Rivet.init();
    },
  };
})(Drupal, window.Rivet);
