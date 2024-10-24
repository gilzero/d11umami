/**
 * @file
 * Manage header mobile behaviors.
 */

((window, Drupal, once) => {
  "use strict";

  /**
   * DSFR Large screen breakpoint.
   * Displays menu button under this screen size.
   *
   * @type {number}
   */
  const DSFR_BREAKPOINT_LG = 992;

  /**
   * DSFR Header wrappers.
   *
   * @type {string}
   */
  const MENU_LINKS_WRAPPER = ".fr-header__menu-links";
  const TOOLS_LINKS_WRAPPER = ".fr-header__tools-links";

  Drupal.behaviors.dsfrHeaderMobile = {
    attach: (context) => {
      const menuLinksWrapper = once("header-menu-links-wrapper", MENU_LINKS_WRAPPER, context);
      const toolsLinksWrapper = once("header-tools-links-wrapper", TOOLS_LINKS_WRAPPER, context);

      if (!menuLinksWrapper.length || !toolsLinksWrapper.length) {
        return;
      }

      window.addEventListener("resize",  () => {
        if(
          window.screen.availWidth < DSFR_BREAKPOINT_LG &&
          toolsLinksWrapper[0].innerHTML
        ) {
          // Move tools links into menu links.
          menuLinksWrapper[0].innerHTML = toolsLinksWrapper[0].innerHTML;
          toolsLinksWrapper[0].innerHTML = "";
        }
        else if (
          window.screen.availWidth >= DSFR_BREAKPOINT_LG &&
          !toolsLinksWrapper[0].innerHTML
        ) {
          // Move menu links into tools links.
          toolsLinksWrapper[0].innerHTML = menuLinksWrapper[0].innerHTML;
          menuLinksWrapper[0].innerHTML = "";
        }
      });

      // Trigger resize event on page loading.
      window.dispatchEvent(new Event("resize"));
    }
  };

})(window, Drupal, once);
