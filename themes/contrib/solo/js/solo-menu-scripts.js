/**
 * @file
 * Solo
 *
 * Filename:     solo-menu-scripts.js
 * Website:      https://www.flashwebcenter.com
 * Developer:    Alaa Haddad https://www.alaahaddad.com.
 */
((Drupal, drupalSettings, once) => {
  'use strict';

  Drupal.behaviors.globalMenu = {
    attach: function (context, settings) {
      // This ensures our code only runs once and within the right context.
      if (context === document) {
        // Check if the main navigation exists and contains the specific navigation structure.
        const mainNavigation = document.querySelector('#primary-menu');
        let origOffsetY;

        if (mainNavigation && mainNavigation.querySelector('.solo-inner .navigation__menubar')) {
          origOffsetY = mainNavigation.offsetTop; // Set the original Y offset of the navigation.

          const scrollWindow = () => {
            // Update origOffsetY only once at the start of scrolling.
            let scrollStarted = false;

            return () => {
              if (!scrollStarted) {
                origOffsetY = mainNavigation.offsetTop;
                scrollStarted = true;
              }

              // Toggle 'solo-sticky' class based on the current scroll position.
              mainNavigation.classList.toggle('solo-sticky', window.scrollY > origOffsetY);
            };
          };

          // Attach the scroll event listener with the scrollWindow function.
          window.addEventListener('scroll', scrollWindow());

          // Reset scrollStarted and update origOffsetY on resize to handle layout changes.
          window.addEventListener('resize', () => {
            origOffsetY = mainNavigation.offsetTop; // Update origOffsetY to the new offset after resize.
          });
        }

        // Apply background color to all site submenus based on their parent region's background color.
        const siteSubMenus = document.querySelectorAll('.solo-inner nav .navigation__menubar ul', context);
        siteSubMenus.forEach(el => {
          const getParentBg = (el) => {
            const closestParent = el.closest('.page-wrapper>div') ?? el.closest('.page-wrapper>header');
            return closestParent ? window.getComputedStyle(closestParent).backgroundColor : '';
          };
          el.style.backgroundColor = getParentBg(el);
        });
      }
    }
  };

})(Drupal, drupalSettings, once);

