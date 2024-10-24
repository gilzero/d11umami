/**
 * @file
 * Solo
 *
 * Filename:     solo-menu-keyboard.js
 * Website:      https://www.flashwebcenter.com
 * Developer:    Alaa Haddad https://www.alaahaddad.com.
 */
((Drupal, drupalSettings, once) => {
  'use strict';

  class MenubarNavigation {
    constructor(menu) {
      this.isMegamenu = false;
      this.menu = menu;
      this.menuItems = menu.querySelectorAll('li > a, li > button');
      this.pageClass = document.querySelector('.page-wrapper');
      this.brNum = Drupal.solo.getMyBreakpoints(this.pageClass, 'mn');
      this.init();
    }

    getCurrentWidth() {
      return window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    }

    isSmallScreen() {
      return this.getCurrentWidth() <= this.brNum;
    }

    init() {
      if (this.menu.querySelector('ul[role="menubar"]').classList.contains('navigation__megamenu')) {
        this.isMegamenu = true;
      }

      this.bindEventListeners();
      // this.setDefaultFocus(); // Sets the initial focus based on screen size
      this.bindMobileNavClickListener();
      // this.handleResize(); // Ensure correct focus on initial load
    }

    bindEventListeners() {
      this.menuItems.forEach(item => {
        item.addEventListener('keydown', this.handleMenuItemKeydown.bind(this));
      });

      // Add resize event listener
      window.addEventListener('resize', () => {
        if ((document.activeElement && document.activeElement.tagName !== 'INPUT')) {
          this.handleResize();
        }
      });

    }

    handleResize() {
      this.setDefaultFocus();

    }

    setDefaultFocus() {
      // Update the screen size check every time setDefaultFocus is called
      const isSmallScreen = this.getCurrentWidth() <= this.brNum;

      if (isSmallScreen) {
        // On small screens, set focus to div.mobile-nav
        const mobileNav = this.menu.querySelector('div.mobile-nav>button');
        mobileNav?.focus();
      }
      else {
        // On large screens, set focus to the first item of the menubar
        const firstMenubarItem = this.menu.querySelector('ul[role="menubar"] > li:first-child a, ul[role="menubar"] > li:first-child button');
        firstMenubarItem?.focus();
      }
    }

    bindMobileNavClickListener() {
      const mobileNavButton = this.menu.querySelector('div.mobile-nav>button');
      if (mobileNavButton) {
        mobileNavButton.addEventListener('click', () => {
          // Assuming a class 'expanded' is added to the menubar when it is expanded
          const menubar = this.menu.querySelector('ul[role="menubar"]');

          // Wait for the DOM updates to be completed
          setTimeout(() => {
            if (menubar && menubar.classList.contains('toggled')) {
              const firstMenubarItem = this.menu.querySelector('ul[role="menubar"] > li:first-child a, ul[role="menubar"] > li:first-child button');
              firstMenubarItem?.focus();
            }
          }, 0);
        });
      }
    }

    handleArrowRight(currentItem, isMenubar, isSmallScreen) {
      if (isMenubar && !isSmallScreen) {
        this.focusNextItem(currentItem);
        return true;
      }
      else if (!isMenubar && !isSmallScreen && currentItem.classList.contains('dropdown-toggler')) {
        let submenu = currentItem.nextElementSibling;
        if (submenu && submenu.classList.contains('toggled')) {
          let firstSubItem = submenu.querySelector('li:first-child a, li:first-child button');
          if (firstSubItem) {
            firstSubItem.focus();
            return true;
          }
        }
      }
      else {
        let nextSibling = currentItem.nextElementSibling;
        if (nextSibling && (nextSibling.tagName === 'A' || nextSibling.tagName === 'BUTTON')) {
          nextSibling.focus();
          return true;
        }
      }
      return false;
    }

    handleArrowLeft(currentItem, isMenubar, isSmallScreen) {
      if (isMenubar && !isSmallScreen) {
        this.focusPreviousItem(currentItem);
        return true;
      }
      else if (!isMenubar && !isSmallScreen) {
        // Check if the current item is the first child in a 'ul' with role 'menu'
        let parentUl = currentItem.closest('ul');
        let isFirstChild = currentItem.parentElement === parentUl.firstElementChild;
        if (parentUl.getAttribute('role') === 'menu' && isFirstChild) {
          let parentLi = parentUl.closest('li');
          if (parentLi) {
            let focusableElement = parentLi.querySelector('a, button');
            if (focusableElement) {
              focusableElement.focus();
              return true;
            }
          }
        }
      }
      else {
        // Fallback to focus on the next sibling if it's a 'a' or 'button'
        let prevSibling = currentItem.previousElementSibling;
        if (prevSibling && (prevSibling.tagName === 'A' || prevSibling.tagName === 'BUTTON')) {
          prevSibling.focus();
          return true;
        }
      }
      return false;
    }

    handleArrowDown(currentItem, isSmallScreen) {
      // Check if we are in a megamenu
      if (this.isMegamenu && !isSmallScreen) {
        let nextItem;
        // Determine if the current li is the last child of its parent ul
        let currentLi = currentItem.closest('li');
        let isLastLi = currentLi.nextElementSibling === null;

        if (isLastLi && !currentLi.classList.contains('has-sub__menu')) {
          // Navigate up to find the correct level of ul > li to jump to the next focusable item
          let currentLevelLi = currentItem.closest('ul').closest('li');
          let nextLevelLi = currentLevelLi.nextElementSibling;

          if (nextLevelLi) {
            // If there is a next sibling at the current nested level, focus the first item
            nextItem = nextLevelLi.querySelector('a, button');
          } else {
            // If at the last item of the current nested level, move up to find the next focusable item at the parent level
            let parentLevelLi = currentLevelLi.closest('ul').closest('ul').closest('li').nextElementSibling;
            if (parentLevelLi) {
              nextItem = parentLevelLi.querySelector('a, button');
            }
          }
        } else {
          // For non-last li elements, simply move to the next item within the same list
          nextItem = currentItem.nextElementSibling ? currentItem.nextElementSibling.querySelector('a, button') : null;
        }

        if (nextItem) {
          nextItem.focus();
          return true;
        }
      }

      // Check if currentItem is a dropdown-toggler and the submenu is toggled
      if (currentItem.classList.contains('dropdown-toggler')) {
        let submenu = currentItem.nextElementSibling;
        if (submenu && submenu.classList.contains('toggled')) {
          // If submenu is open, move focus to the first item in the submenu
          let firstSubItem = submenu.querySelector('li:first-child a, li:first-child button');
          if (firstSubItem) {
            firstSubItem.focus();
            return true;
          }
        }
      }

      // For other cases, move to the next item
      this.focusNextItem(currentItem);
      return true;
    }

    handleArrowUp(currentItem, isMenubar, isSmallScreen) {
      // Check for small screen and if the current item is in the menubar
      if (isSmallScreen && isMenubar) {
        // Check if the currentItem's parent LI is the first child of a UL with role 'menubar'
        const isFirstItemInMenubar = currentItem.parentElement.matches('ul[role="menubar"] > li:first-child');
        if (isFirstItemInMenubar) {
          const mobileNavButton = this.menu.querySelector('div.mobile-nav>button');
          if (mobileNavButton) {
            mobileNavButton.focus();
            return true;
          }
        }
        else {
          // If not the first item in the menubar, find the previous item <a> or <button>
          let previousItem = this.findPreviousItem(currentItem, isSmallScreen);
          if (previousItem) {
            previousItem.focus();
            return true;
          }
        }
      }
      // For large screens and menubar items, focus on the last item of the last menubar ul
      if (!isSmallScreen && isMenubar) {
        let lastMenubar = document.querySelector('ul[role="menubar"]:last-of-type');
        let lastItem = lastMenubar.querySelector('li:last-child a, li:last-child button');
        if (lastItem) {
          lastItem.focus();
          return true;
        }
      }
      else {
        // For small screens or non-menubar items, use findPreviousItem
        let previousItem = this.findPreviousItem(currentItem, isSmallScreen);
        if (previousItem) {
          previousItem.focus();
          return true;
        }
      }
      return false;
    }

    findPreviousItem(currentItem, isSmallScreen) {
      let currentLi = currentItem.closest('li');
      let parentUl = currentLi.closest('ul');
      let isFirstChild = currentLi === parentUl.firstElementChild;
      let siblingElement = parentUl ? parentUl.previousElementSibling : null;

      if (isFirstChild) {
        // Focus on the button if it's the sibling of the parent UL
        if (siblingElement && siblingElement.tagName === 'BUTTON') {
          return siblingElement;
        }
        // On small screens, focus on mobile-nav if it's the sibling
        else if (isSmallScreen && siblingElement && siblingElement.classList.contains('mobile-nav')) {
          return siblingElement;
        }
      }

      // Default case: Move to the previous <li>
      return this.findFocusableElementInPreviousLi(currentLi);
    }

    findFocusableElementInPreviousLi(currentLi) {
      let previousLi = currentLi.previousElementSibling;
      while (previousLi) {
        let focusableElement = previousLi.querySelector('a, button');
        if (focusableElement) {
          return focusableElement;
        }
        previousLi = previousLi.previousElementSibling;
      }
      return null;
    }

    focusNextItem(currentItem) {
      // Check for a sibling of currentItem that is either 'a' or 'button'
      let sibling = currentItem.nextElementSibling;
      if (sibling && (sibling.tagName === 'A' || sibling.tagName === 'BUTTON')) {
        sibling.focus();
      }
      else {
        // If no sibling, move to the next parent sibling and find its first focusable child
        let nextParentSibling = currentItem.parentElement.nextElementSibling;
        if (nextParentSibling) {
          let target = nextParentSibling.querySelector('a, button');
          target?.focus();
        }
      }
    }

    focusPreviousItem(currentItem) {
      // Check for a direct sibling of currentItem that is either 'a' or 'button'
      let sibling = currentItem.previousElementSibling;
      if (sibling && (sibling.tagName === 'A' || sibling.tagName === 'BUTTON')) {
        sibling.focus();
      }
      else {
        // If no direct sibling, move to the previous parent sibling
        let prevParentSibling = currentItem.parentElement.previousElementSibling;
        if (prevParentSibling) {
          // Prefer focusing on a button if available, otherwise focus on a link
          let target = prevParentSibling.querySelector('button') || prevParentSibling.querySelector('a');
          target?.focus();
        }
      }
    }

    openSubMenu(currentItem) {
      // ...rest of the method remains the same
      const subMenu = currentItem.nextElementSibling;
      if (subMenu && !subMenu.classList.contains('toggled')) {
        Drupal.solo.slideDown(subMenu);
        let firstSubItem = subMenu.querySelector('li:first-child a, li:first-child button');
        firstSubItem?.focus();
        // Add logic to set tabindex to "-1" for all items except the first one
        const subMenuItems = subMenu.querySelectorAll('li');
        subMenuItems.forEach((item, index) => {
          if (index > 0) { // Skip the first item
            let focusableElement = item.querySelector('a, button');
            if (focusableElement) {
              focusableElement.tabIndex = -1;
            }
          }
        });
      }
    }

    closeSubMenu(currentItem) {
      // Traverse up the DOM to find the closest `ul` with the `toggled` class
      let subMenu = currentItem;
      let megaMenu = document.querySelector('.solo-inner .solo-menu ul.navigation__megamenu');

      if (megaMenu) {
        // If inside a mega menu, close all submenus within it
        let allSubMenus = megaMenu.querySelectorAll('ul');
        allSubMenus.forEach(subMenu => {
          if (subMenu.classList.contains('toggled')) {
            Drupal.solo.slideUp(subMenu);
            subMenu.classList.remove('toggled');
          }
        });
      } else {
        // Regular menu logic
        while (subMenu && subMenu.tagName !== 'UL') {
          subMenu = subMenu.parentElement;
        }
        subMenu = subMenu && subMenu.classList.contains('toggled') ? subMenu : null;

        if (subMenu) {
          // Close the submenu
          Drupal.solo.slideUp(subMenu);
          subMenu.classList.remove('toggled');
          // Focus on the corresponding button within the parent `li`
          const parentLi = subMenu.closest('li');
          const siblingButton = parentLi ? parentLi.querySelector('button.dropdown-toggler') : null;
          siblingButton?.focus();
        } else {
          currentItem.focus();
        }
      }
    }

    handleMenuItemKeydown(event) {
      const key = event.key;
      const currentItem = event.target; // Use target to ensure we get the element that was actually clicked
      const isMenubar = currentItem.closest('ul').getAttribute('role') === 'menubar';
      let handled = false;
      const isSmallScreen = this.isSmallScreen();

      // Ensure default behavior is prevented when a navigation key is pressed
      if (['ArrowRight', 'ArrowLeft', 'ArrowDown', 'ArrowUp'].includes(key)) {
        event.preventDefault();
      }

      switch (key) {
        case 'ArrowRight':
          handled = this.handleArrowRight(currentItem, isMenubar, isSmallScreen);
          break;
        case 'ArrowLeft':
          handled = this.handleArrowLeft(currentItem, isMenubar, isSmallScreen);
          break;
        case 'ArrowDown':
          handled = this.handleArrowDown(currentItem, isSmallScreen);
          break;
        case 'ArrowUp':
          handled = this.handleArrowUp(currentItem, isMenubar, isSmallScreen);
          break;
        case 'Escape':
          this.closeSubMenu(currentItem);
          handled = true;
          break;
      }

      if (handled) {
        event.stopPropagation();
      }
    }

  } // End of class

  Drupal.behaviors.menubarNavigationBehavior = {
    attach: function(context, settings) {
      const menus = context.querySelectorAll('.solo-menu.navigation-primary-responsive');
      menus.forEach(menu => {
        new MenubarNavigation(menu);
      });
    }
  };

})(Drupal, drupalSettings, once);
