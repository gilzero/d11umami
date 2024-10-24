/**
 * @file
 * Solo
 *
 * Filename:     solo-menu-side.js
 * Website:      https://www.flashwebcenter.com
 * Developer:    Alaa Haddad https://www.alaahaddad.com.
 */
((Drupal, drupalSettings, once) => {
  'use strict';

  // Get the primary sidebar menu and all the sidebar hamburger icons
  const verticalNav = document.getElementById('primary-sidebar-menu');
  if (verticalNav) {
    const closeBtns = document.querySelectorAll('.sidebar-button-close>button');
    const openBtns = document.querySelectorAll('.sidebar-button-open>button');
    const firstLevelSelector = '#primary-sidebar-menu .nav__menubar-item > a, #primary-sidebar-menu .nav__menubar-item > button';

    // Function to add a click event listener to specified elements
    const sideMenubarCloseOpen = (buttons, callback) => {
      buttons.forEach(button => {
        if (button) {
          button.addEventListener('click', callback);
        }
      });
    };

    // Function to toggle aria-expanded on the hamburger icons
    const setAriaExpanded = (buttons, isOpen) => {
      buttons.forEach(button => {
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      });
    };

    // Function to toggle aria-hidden on the vertical navigation
    const setAriaHidden = (element, isHidden) => {
      element.setAttribute('aria-hidden', isHidden ? 'true' : 'false');
    };

    // Function to focus the first interactive element in the vertical navigation
    const focusFirstInteractiveElement = () => {
      const firstInteractiveElement = verticalNav.querySelector(firstLevelSelector);
      if (firstInteractiveElement) {
        firstInteractiveElement.focus();
      }
    };

    // Function to update tabindex of first level menu items
    const updateTabindex = (isOpen) => {
      const firstLevelItems = verticalNav.querySelectorAll(firstLevelSelector);
      firstLevelItems.forEach(item => {
        item.setAttribute('tabindex', isOpen ? '0' : '-1');
      });

      closeBtns.forEach(button => {
        button.setAttribute('tabindex', isOpen ? '0' : '-1');
      });
    };

    // Function to trap focus within the sidebar
    const trapFocus = (event) => {
      const focusableElements = Array.from(verticalNav.querySelectorAll(firstLevelSelector)).filter(el => el.getAttribute('tabindex') === '0');
      const currentIndex = focusableElements.indexOf(document.activeElement);
      let nextIndex;

      if (event.key === 'Tab') {
        if (event.shiftKey) { // Shift + Tab
          nextIndex = currentIndex > 0 ? currentIndex - 1 : focusableElements.length - 1;
        } else { // Tab
          nextIndex = currentIndex < focusableElements.length - 1 ? currentIndex + 1 : 0;
        }
        event.preventDefault();
        focusableElements[nextIndex].focus();
      }
    };

    const sideMenubarToggleNav = (isOpen) => {
      // Set aria-expanded for buttons
      setAriaExpanded(closeBtns, isOpen);
      setAriaExpanded(openBtns, isOpen);

      // Set aria-hidden for vertical navigation
      setAriaHidden(verticalNav, !isOpen);

      // Update tabindex for first level menu items and close buttons
      updateTabindex(isOpen);

      // Toggle the class for the vertical navigation
      if (isOpen) {
        verticalNav.classList.add('toggled');
        // Focus the first interactive element after opening the sidebar
        focusFirstInteractiveElement();
        document.addEventListener('keydown', trapFocus);
      } else {
        verticalNav.classList.remove('toggled');
        const subMenus = verticalNav.querySelectorAll('.navigation__sidebar li ul.sub__menu');
        subMenus?.forEach(Drupal.solo.hideSubMenus);
        document.removeEventListener('keydown', trapFocus);
      }
    };
    Drupal.solo.sideMenubarToggleNav = sideMenubarToggleNav;

    function addOutsideClickListener(enable) {
      if (enable === true) {
        document.addEventListener('click', outsideClickListener);
      } else {
        document.removeEventListener('click', outsideClickListener);
      }
    }

    function outsideClickListener(event) {
      if (verticalNav && !verticalNav.contains(event.target) && !event.target.closest('.sidebar-button-open')) {
        sideMenubarToggleNav(false);
      }
    }

    // Expose the function to the Drupal namespace
    Drupal.solo.addOutsideClickListener = addOutsideClickListener;

    Drupal.behaviors.soloPrimarySideMenu = {
      attach: function (context, settings) {
        // Close nav button event
        sideMenubarCloseOpen(closeBtns, () => sideMenubarToggleNav(false));

        // Open nav button event
        sideMenubarCloseOpen(openBtns, () => sideMenubarToggleNav(true));

        // Add outside click listener
        Drupal.solo.addOutsideClickListener(true);

        // Ensure the tabindex is always set correctly on load
        const verticalNav = document.querySelector('.vertical-nav');
        if (verticalNav) {
          if (verticalNav.classList.contains('toggled')) {
            updateTabindex(true);
          } else {
            updateTabindex(false);
          }
        }
      }
    };

  }
})(Drupal, drupalSettings, once);
