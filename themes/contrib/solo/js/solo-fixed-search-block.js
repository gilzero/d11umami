/**
 * @file
 * Adds toggle functionality to a fixed search block.
 *
 * Filename: solo-fixed-search-block.js
 * Website: https://www.flashwebcenter.com
 * Developer: Alaa Haddad https://www.alaahaddad.com.
 */
((Drupal, drupalSettings, once) => {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    // Function to handle focus events
    const handleFocus = (event) => {
      const element = event.target;
      const tagName = element.tagName.toLowerCase();
      const id = element.id ? `#${element.id}` : '';
      const className = element.className ? `.${element.className.split(' ').join('.')}` : '';
    };

    // Add event listener to all focusable elements
    const focusableElements = document.querySelectorAll('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
    focusableElements.forEach((element) => {
      element.addEventListener('focus', handleFocus);
    });
  });

  const mainSideNav = document.getElementById('primary-sidebar-menu');
  const searchBlock = document.getElementById('fixed-search-block');
  const openSearchButtons = document.querySelectorAll('.search-button-open>button');
  const closeSearchButton = document.querySelector('.search-button-close>button');
  const skipToContent = document.getElementById('skip-to-content');
  const mainContent = document.getElementById('main-content');

  // Function to add a click event listener to a specified element
  const searchBlockCloseOpen = (elements, callback) => {
    elements.forEach(element => {
      if (element) {
        element.addEventListener('click', callback);
      }
    });
  };

  // Function to toggle the aria-expanded attribute for open/close buttons
  // and aria-hidden for search block
  const setAriaAttributes = (isOpen) => {
    openSearchButtons.forEach(btn => btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false'));
    closeSearchButton?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    searchBlock.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
  };

  // Function to set tabindex on buttons and inputs inside the search block
  const setTabindexOnElements = (isOpen) => {
    const elements = searchBlock.querySelectorAll('button, input');
    elements.forEach(element => {
      if (isOpen) {
        element.removeAttribute('tabindex');
      } else {
        element.setAttribute('tabindex', '-1');
      }
    });
  };

  // Function to focus the first input field in the search block
  const focusFirstInput = () => {
    const firstInput = searchBlock.querySelector('input');
    if (firstInput) {
      firstInput.focus();
    }
  };

  // Function to move focus to the close button when tabbing out of the last input
  const focusTrap = (event) => {
    const elements = Array.from(searchBlock.querySelectorAll('button, input'));
    const lastElement = elements[elements.length - 1];
    if (event.target === lastElement && event.key === 'Tab' && !event.shiftKey) {
      event.preventDefault();
      closeSearchButton.focus();
    }
  };

  const searchBlockToggle = (isOpen) => {
    setAriaAttributes(isOpen);
    setTabindexOnElements(isOpen);

    if (isOpen) {
      searchBlock.classList.add('toggled');
      searchBlock.style.height = 'auto';

      const height = searchBlock.clientHeight + 'px';
      searchBlock.style.height = '0px';

      setTimeout(() => {
        searchBlock.style.height = height;
      }, 10); // Added a small delay

      if (mainSideNav) {
        Drupal.solo.sideMenubarToggleNav(false);
      }

      focusFirstInput();
    } else {
      searchBlock.style.height = '0px';
      searchBlock.addEventListener('transitionend', (event) => {
        if (event.propertyName === 'height' && event.target === searchBlock) {
          searchBlock.classList.remove('toggled');
        }
      }, { once: true });
    }
  };

  if (searchBlock) {
    Drupal.solo.searchBlockToggle = searchBlockToggle;

    // Attach these behaviors to the Drupal system
    Drupal.behaviors.soloFixedSearchBlock = {
      attach: function(context, settings) {
        // Close nav button found in page.html.twig in vertical menu region.
        searchBlockCloseOpen([closeSearchButton], () => searchBlockToggle(false));

        // Open nav button found in page.html.twig in header region.
        searchBlockCloseOpen(openSearchButtons, () => searchBlockToggle(true));

        // Add focus trap to the last element to redirect focus to the close button
        searchBlock.addEventListener('keydown', focusTrap);

        // Click anywhere outside the search block to close it.
        document.addEventListener('click', (event) => {
          if (searchBlock && searchBlock.classList.contains('toggled')) {
            // Check if the click is outside the search block
            if (!searchBlock.contains(event.target) && !event.target.closest('.search-button-open')) {
              searchBlockToggle(false);
            }
          }
        });

        // Initialize the search block as hidden and non-focusable
        searchBlockToggle(false);

        // Handle skip to content link
        if (skipToContent) {
          skipToContent.addEventListener('click', (event) => {
            event.preventDefault();
            mainContent.focus();
          });
        }
      }
    };
  }

})(Drupal, drupalSettings, once);
