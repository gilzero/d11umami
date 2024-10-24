/**
 * @file
 * Breakpoints.
 */
(function ($, window, document) {

  "use strict";

  Drupal.behaviors.atBP = {
    attach: function (context, settings) {

      // Verify that the user agent understands media queries.
      if (!window.matchMedia('only screen').matches) {
        return;
      }

      // Get breakpoints from drupalSettings, these are added during preprocess
      // and write the breakpoints used in layout settings, which are themselves
      // set in breakpoints module config, i.e. themeName.breakpoints.yml and are
      // the group selected to be used by the themes layout.
      var activeTheme = settings['ajaxPageState']['theme'];
      var bps = settings[activeTheme]['at_breakpoints'];

      function registerEnquire(breakpoint_label, breakpoint_query) {
        enquire.register(breakpoint_query, {
          match: function() {
            document.body.classList.add('bp--' + breakpoint_label);
          },
          unmatch: function() {
            document.body.classList.remove('bp--' + breakpoint_label);
          }
        });
      }

      for (var item in bps) {
        if (bps.hasOwnProperty(item)) {
          registerEnquire(item.split('_').join('-'), bps[item]['mediaquery']);
        }
      }
    }
  };
}(jQuery, window, document));
