/* *
 * @file
 * Entity Browser.
 *
 */
(function ($, Drupal, once) {
  'use strict';

  Drupal.behaviors.entityBrowser = {
    attach: function (context, settings) {

      // Function to update the UI theme
      function updateTheme(current_theme) {
        // Check DARK Mode at the local storage and if not set check at operating system level.
        let system_dark_scheme = window.matchMedia('(prefers-color-scheme: dark)');
        let body = document.querySelector('body');
        if (current_theme == 'dark') {
          body.classList.add('dark-mode');
        } else if (current_theme == 'light') {
          body.classList.remove('dark-mode');
        } else {
          if (system_dark_scheme.matches) {
            body.classList.add('dark-mode');
          } else {
            body.classList.remove('dark-mode');
          }
        }

        // Check CONTRAST Mode at the local storage and if not set check at operating system level.
        const current_contrast = localStorage.getItem('contrast');
        let system_contrast_scheme = window.matchMedia('(-ms-high-contrast: active)');
        if (current_contrast == 'enabled') {
          body.classList.add('high-ctr');
        } else if (current_contrast == 'disabled') {
          body.classList.remove('high-ctr');
        } else {
          if (system_contrast_scheme.matches) {
            body.classList.add('high-ctr');
          } else {
            body.classList.remove('high-ctr');
          }
        }
      }


      function handleStorageEvent(event) {
        // Check the changed key and react accordingly
        if (event.key === 'theme') {
          // Update the UI theme based on the new value
          updateTheme(event.newValue);
        }
      }

      // Update the UI theme based on the current theme
      let current_theme = localStorage.getItem('theme');
      updateTheme(current_theme);
      // Add the event listener to the window object
      window.addEventListener('storage', handleStorageEvent);
    },
  };
})(jQuery, Drupal, once);
