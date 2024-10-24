/**
 * @file
 * Initialize IU JS
 */
(function ($, window, document) {
  const initIU = function initializeIUFrameworkJavascript() {
    window.Foundation.OffCanvas.defaults.transitionTime = 500;
    window.Foundation.OffCanvas.defaults.forceTop = false;
    window.Foundation.OffCanvas.defaults.position = "right";

    window.Foundation.Accordion.defaults.multiExpand = true;
    window.Foundation.Accordion.defaults.allowAllClosed = true;

    // Initialize Foundation
    $(document).foundation();

    const IU = window.IU || {};

    // Delete modules if necessary
    // delete IU.uiModules['accordion'];

    if (IU.init) {
      IU.init();
    }

    // Account for absolutely positioned elements when animating a scroll effect.
    IU.uiDisplace = function () {
      // Account for IU Framework's sticky nav or branding bar, and bigNav setting.
      let displace = IU.settings.bigNav ? 68 : 55;

      // Account for Drupal Toolbar.
      if ($("body").hasClass("toolbar-fixed")) {
        displace += 39;
      }
      // Account for Drupal Toolbar tray.
      if (
        $("body").hasClass("toolbar-tray-open") &&
        $("body").hasClass("toolbar-horizontal")
      ) {
        displace += 40;
      }
      return displace;
    };

    // Override default smoothScroll helper to integrate with Drupal Toolbar.
    IU.addHelper("smoothScroll", function () {
      $('a[href*="#"]:not([href="#"], [href^="#panel"])')
        .not("#skipnav a")
        .click(function () {
          if (
            window.location.pathname.replace(/^\//, "") ===
              this.pathname.replace(/^\//, "") &&
            window.location.hostname === this.hostname
          ) {
            let target = $(this.hash);
            target = target.length ? target : $(`[name=${this.hash.slice(1)}]`);

            if (target.length) {
              $("html, body").animate(
                {
                  scrollTop: target.offset().top - IU.uiDisplace(),
                },
                1000
              );

              return false;
            }
          }
        });
    });

    // Override default skipNav helper to integrate with Drupal Toolbar.
    IU.addHelper("skipNav", function () {
      $("#skipnav a").on("click", function () {
        const target = $(this.hash);

        if (target.length) {
          $("html, body").animate(
            {
              scrollTop: target.offset().top - IU.uiDisplace(),
            },
            500
          );

          target
            .attr("tabindex", 0)
            .on("blur focusout", function () {
              $(this).removeAttr("tabindex");
            })
            .focus();

          if (target.attr("id") === "search") {
            $("#toggles a.search-toggle")[0].click();
          } else if (
            target.attr("id") === "nav-main" &&
            IU.$window.width() < 1025
          ) {
            $("#toggles [data-toggle]").click();
          }
        }
      });
    });
  };
  $(document).ready(initIU);
})(jQuery, window, window.document);
