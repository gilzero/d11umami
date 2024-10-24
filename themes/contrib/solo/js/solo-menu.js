/**
 * @file
 * Solo
 *
 * Filename:     solo-menu.js
 * Website:      https://www.flashwebcenter.com
 * Developer:    Alaa Haddad https://www.alaahaddad.com.
 */
((Drupal, drupalSettings, once) => {
  'use strict';
  let isClicked = false;
  let currentWidth;

  Drupal.behaviors.menuAction = {
    attach: function(context, settings) {

      const querySelectorElements = (selector) => context.querySelectorAll(selector) ?? null;
      const siteMenuBars = querySelectorElements('.solo-inner .solo-menu .navigation__menubar');
      const siteSubMenus = querySelectorElements('.solo-inner .solo-menu .navigation__menubar ul');
      const svgIcons = querySelectorElements('.solo-inner .solo-menu .navigation__menubar .toggler-icon>svg');
      const mmClickBig = querySelectorElements('.solo-inner .solo-menu.navigation-responsive-click .navigation__megamenu>li>.dropdown-toggler');
      const mmClickSmall = querySelectorElements('.solo-inner .solo-menu.navigation-responsive-click .navigation__megamenu li .dropdown-toggler');
      const mmHoverSmall = querySelectorElements('.solo-inner .solo-menu.navigation-responsive-hover .navigation__megamenu li .dropdown-toggler');
      const navigationMegamenuHover = querySelectorElements('.solo-inner .solo-menu.navigation-responsive-hover .navigation__megamenu li .dropdown-toggler');
      const navigationResponsiveHover = querySelectorElements('.solo-inner .solo-menu.navigation-responsive-hover .navigation__menubar:not(.navigation__megamenu) .dropdown-toggler');
      const navigationResponsiveClick = querySelectorElements('.solo-inner .solo-menu.navigation-responsive-click .navigation__menubar:not(.navigation__megamenu) .dropdown-toggler');
      const navigationDefault = querySelectorElements('.solo-inner .solo-menu .navigation__default .dropdown-toggler');
      const navigationSidebarHover = querySelectorElements('.solo-inner .solo-menu.navigation-sidebar-hover li .dropdown-toggler');
      const navigationSidebarClick = querySelectorElements('.solo-inner .solo-menu.navigation-sidebar-click li .dropdown-toggler');

      // Get current width
      const getCurrentWidth = () => window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
      currentWidth = getCurrentWidth();

      // Function to handle the click, so don't click fast twice.
      const delay = (duration) => new Promise(resolve => setTimeout(resolve, duration));
      const clickedHandler = async (callback) => {
        if (!isClicked) {
          isClicked = true;
          await callback();
          await delay(500);
          isClicked = false;
        }
      }
      Drupal.solo.clickedHandler = clickedHandler;

      const hideSubMenus = (childElement) => {
        Drupal.solo.slideUp(childElement, 300);
      }
      Drupal.solo.hideSubMenus = hideSubMenus;

      const getNavigationMenubarClass = (menuBar) => {
        return document.querySelector(`.solo-inner #${menuBar} .navigation__menubar`);
      }
      Drupal.solo.getNavigationMenubarClass = getNavigationMenubarClass;

      const getSubMenuClasses = (subMenus) => {
        return document.querySelectorAll(`.solo-inner #${subMenus} .navigation__menubar ul.sub__menu`);
      }
      Drupal.solo.getSubMenuClasses = getSubMenuClasses;

      const hasParentWithClass = (element, className) => !!element.closest(`.${className}`);

      const getNavTagId = (dropdownTogglerButton) => {
        const { id: navId } = dropdownTogglerButton.closest('nav');
        return navId;
      }

      const getRotated = (dropdownTogglerButton) => {
        return dropdownTogglerButton.querySelector('.toggler-icon svg');
      }

      const getArrowDirection = (verticalNav) => {
        const pageClass = document.querySelector('.page-wrapper');
        const expandLeft = document.querySelector('#primary-menu .expand-left');
        const brNum = Drupal.solo.getMyBreakpoints(pageClass, 'mn');
        if (expandLeft) {
          return (currentWidth >= brNum && !verticalNav) ? 'rotate(90deg)' : 'rotate(180deg)';
        }
        return (currentWidth >= brNum && !verticalNav) ? 'rotate(-90deg)' : 'rotate(180deg)';
      }

      // Change the arrow direction on close.
      const revertIcons = (navId) => {
        let svgIcons = document.querySelectorAll(`.solo-inner #${navId} .toggler-icon svg`);
        svgIcons.forEach((svgIcon) => {
          svgIcon.style.removeProperty('transform');
        });
      }
      Drupal.solo.revertIcons = revertIcons

      // click anywhere to close the nav
      const removesiteMenuBarsStyles = (siteMenuBars) => {
        siteMenuBars.forEach((siteMenuBar) => {
          siteMenuBar.removeAttribute('style');
        });
      }

      const getDropdownElements = (dropdownTogglerButton) => {
        const togglerSibling = dropdownTogglerButton.closest('.solo-inner .solo-menu ul');
        const nestedSubMenus = [...togglerSibling.querySelectorAll('.solo-inner .solo-menu ul')];
        const nestedTogglers = [...togglerSibling.querySelectorAll('.solo-inner .solo-menu ul .dropdown-toggler svg')];
        return [nestedSubMenus, nestedTogglers];
      }

      // This function handles the attributes for closing the menu
      // Helper function to set multiple attributes
      const setSubMenuAttributes = (element, attributes) => {
        for (const key in attributes) {
          element.setAttribute(key, attributes[key]);
        }
      }

      // This function handles the attributes for closing the menu
      const closeMenuHelper = (rotated, dropdownTogglerButton, subMenu) => {
        rotated.style.removeProperty('transform');
        setSubMenuAttributes(dropdownTogglerButton, {
          'aria-expanded': 'false'
        });

        Drupal.solo.slideUp(subMenu, 400);
      }

      // This function handles the attributes for opening the menu
      const openMenuHelper = (dropdownTogglerButton, subMenu) => {

        currentWidth = getCurrentWidth();
        const pageClass = document.querySelector('.page-wrapper');
        const brNum = Drupal.solo.getMyBreakpoints(pageClass, 'mn');
        if (subMenu.classList.contains('sub-mega') && currentWidth >= brNum) {
          Drupal.solo.slideDown(subMenu, 'grid', 1000);
        } else {
          Drupal.solo.slideDown(subMenu);
        }

        setSubMenuAttributes(dropdownTogglerButton, {
          'aria-expanded': 'true'
        });
      }

      // This function is used in two times. 1- When clicked any where closer to
      // menubar it will close any submenus. 2- When resizing the screen, it will
      // close any submenus.
      const resetSubMenus = (siteSubMenus, svgIcons) => {
        svgIcons.forEach(el => el.style.removeProperty('transform'));
        siteSubMenus.forEach(el => Drupal.solo.slideUp(el, 500));
        setTimeout(() => {
          siteSubMenus.forEach(el => el.style.removeProperty('transform'));
        }, 550);
      };

      // Function to reset specific sub-menus and their icons
      const resetSpecificSubMenus = (specificSubMenus, specificSvgIcons) => {
        specificSvgIcons.forEach(el => el.style.removeProperty('transform'));
        specificSubMenus.forEach(el => Drupal.solo.slideUp(el, 500));
        setTimeout(() => {
          specificSubMenus.forEach(el => el.style.removeProperty('transform'));
        }, 550);
      };

      // Click anywhere to close any submenu inside the specified IDs only.
      const closeSubMenusOnClick = () => {
        const navMenus = ['.solo-inner .solo-menu.navigation-responsive-click .navigation__menubar', '#primary-sidebar-menu .navigation__menubar'];

        document.addEventListener('click', (event) => {
          clickedHandler(() => {
            let isInsideSpecifiedNavMenu = false;

            navMenus.forEach(selector => {
              if (event.target.closest(selector)) {
                isInsideSpecifiedNavMenu = true;
              }
            });

            if (!isInsideSpecifiedNavMenu) {
              const specificNavMenus = document.querySelectorAll(navMenus.join(', '));
              const specificSubMenus = [];
              const specificSvgIcons = [];
              specificNavMenus.forEach(menu => {
                specificSubMenus.push(...menu.querySelectorAll('ul.navigation__menubar ul'));
                specificSvgIcons.push(...menu.querySelectorAll('.toggler-icon svg'));
              });

              resetSpecificSubMenus(specificSubMenus, specificSvgIcons);
            }
          });
        });
      };

      // Open menubar get called by dropdownTogglerButtonIsClicked();
      const openMenubar = (dropdownTogglerButton, subMenu) => {
        const navTagId = getNavTagId(dropdownTogglerButton);
        const subMenuClasses = getSubMenuClasses(navTagId);
        const rotated = getRotated(dropdownTogglerButton);
        subMenuClasses?.forEach((subMenuClass) => {
          if (subMenuClass !== subMenu) {
            hideSubMenus(subMenuClass);
            revertIcons(navTagId);
          }
        });
        rotated.style.transform = 'rotate(180deg)';
        openMenuHelper(dropdownTogglerButton, subMenu);
      }

      // Close menubar get called by dropdownTogglerButtonIsClicked();
      const closeMenubar = (dropdownTogglerButton, subMenu) => {
        const navTagId = getNavTagId(dropdownTogglerButton);
        const subMenuClasses = getSubMenuClasses(navTagId);
        const rotated = getRotated(dropdownTogglerButton);
        subMenuClasses?.forEach((subMenuClass) => {
          hideSubMenus(subMenuClass);
          revertIcons(navTagId);
        });
        closeMenuHelper(rotated, dropdownTogglerButton, subMenu);
      }

      // Open submenu get called by dropdownTogglerButtonIsClicked();
      const openSubMenu = (dropdownTogglerButton, subMenu) => {
        const [nestedSubMenus, nestedTogglers] = getDropdownElements(dropdownTogglerButton);
        const rotated = getRotated(dropdownTogglerButton);
        const verticalNav = subMenu.closest('.navigation-sidebar');
        // Close all opened sibling menu
        nestedSubMenus.forEach((nestedSubMenu) => {
          if (nestedSubMenu !== subMenu) {
            hideSubMenus(nestedSubMenu);
          }
        });
        // Revert all rotated icons
        nestedTogglers.forEach((nestedToggler) => {
          if (nestedToggler !== dropdownTogglerButton) {
            nestedToggler.style.removeProperty('transform');
          }
        });
        let arrowDirection = getArrowDirection(verticalNav);
        rotated.style.transform = arrowDirection;
        openMenuHelper(dropdownTogglerButton, subMenu);
      }

      // Close submenu get called by dropdownTogglerButtonIsClicked();
      const closeSubMenu = (dropdownTogglerButton, subMenu) => {
        const rotated = getRotated(dropdownTogglerButton);
        closeMenuHelper(rotated, dropdownTogglerButton, subMenu);
      }

      // Toggler handle, according to the menu type functions will be called.
      const dropdownTogglerButtonIsClicked = (dropdownTogglerButton, subMenu) => {
        clickedHandler(() => {
          // Let get the menu type menubar or submenu.
          let isClassPresent = dropdownTogglerButton.parentElement.classList.contains('nav__menubar-item');
          if (isClassPresent) {
            if (!subMenu.classList.contains('toggled')) {
              openMenubar(dropdownTogglerButton, subMenu);
            } else {
              closeMenubar(dropdownTogglerButton, subMenu);
            }
          } else {
            if (!subMenu.classList.contains('toggled')) {
              openSubMenu(dropdownTogglerButton, subMenu);
            } else {
              closeSubMenu(dropdownTogglerButton, subMenu);
            }
          }
        });
      }

      // Function to add hover functionality
      const addHoverFunctionality = () => {
        if (document.querySelector('.navigation-responsive-hover')) {
          const dropdownTogglers = document.querySelectorAll('.dropdown-toggler');

          dropdownTogglers.forEach(toggler => {
            toggler.addEventListener('mouseenter', () => {
              toggler.setAttribute('aria-expanded', 'true');
            });

            toggler.addEventListener('mouseleave', () => {
              toggler.setAttribute('aria-expanded', 'false');
            });
          });
        }
      };

      // Add focus class once you click on menubar.
      siteMenuBars.forEach((siteMenuBar) => {
        siteMenuBar.addEventListener('click', (event) => {
          const sideMenu = ('navigation-sidebar');
          const pageClass = document.querySelector('.page-wrapper');
          const brNum = Drupal.solo.getMyBreakpoints(pageClass, 'mn');
          if (hasParentWithClass(siteMenuBar, sideMenu) || currentWidth <= brNum) {
            siteMenuBar.classList.remove('focus-in');
          } else {
            siteMenuBar.classList.add('focus-in');
          }
        });
      });

      function addRemoveListener(event) {
        const button = event.currentTarget;
        const subMenu = button.nextElementSibling;
        dropdownTogglerButtonIsClicked(button, subMenu);
      }

      function addEventListenerToButtons(buttons) {
        buttons.forEach((button) => {
          button.addEventListener('click', addRemoveListener);
        });
      }

      function removeEventListenerToButtons(buttons) {
        buttons.forEach((button) => {
          button.removeEventListener('click', addRemoveListener);
        });
      }

      function menusHelper(currentWidth) {
        const pageClass = document.querySelector('.page-wrapper');
        const brNum = Drupal.solo.getMyBreakpoints(pageClass, 'mn');
        if (currentWidth >= brNum) {
          // Remove the first level menu styles.
          removesiteMenuBarsStyles(siteMenuBars);
          // Menus.
          removeEventListenerToButtons(mmClickSmall);
          addEventListenerToButtons(mmClickBig);
          removeEventListenerToButtons(mmHoverSmall);
          removeEventListenerToButtons(navigationSidebarHover);
          removeEventListenerToButtons(navigationResponsiveHover);
        } else {
          // Call the hover type only on small screen.
          removeEventListenerToButtons(mmClickBig);
          addEventListenerToButtons(mmClickSmall);
          addEventListenerToButtons(mmHoverSmall);
          addEventListenerToButtons(navigationSidebarHover);
          addEventListenerToButtons(navigationResponsiveHover);
        }
      }

      // Add active class to the menu if the page is views.
      const currentPath = window.location.pathname;
      const links = document.querySelectorAll('.views-page .navigation__menubar li a');
      if (links) {
        links.forEach(link => {
          if (link.getAttribute('href') === currentPath) {
            let currentElement = link;

            // Traverse up to find the 'ul.nav-menu'
            while (currentElement && !currentElement.matches('ul.navigation__menubar')) {
              if (currentElement.tagName === 'LI') {
                // Add 'is-active' class to the li element
                currentElement.classList.add('is-active');
              }
              currentElement = currentElement.parentElement;
            }
          }
        });
      }

      addHoverFunctionality();
      addEventListenerToButtons(navigationDefault);
      addEventListenerToButtons(navigationResponsiveClick);
      addEventListenerToButtons(navigationSidebarClick);
      // We only call main menu click and hover type when hover is disabled.
      currentWidth = getCurrentWidth();
      menusHelper(currentWidth);
      window.addEventListener('resize', () => {
        currentWidth = getCurrentWidth();
        menusHelper(currentWidth);
        // Reset menu styles on resize.
        resetSubMenus(siteSubMenus, svgIcons);
        addHoverFunctionality();
      });

      // Add new functionality
      closeSubMenusOnClick();
    }
  };
})(Drupal, drupalSettings, once);

