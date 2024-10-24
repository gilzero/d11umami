/**
 * @file
 * Contains utility functions for Solo module.
 *
 * Filename: solo-utils.js
 * Website: https://www.flashwebcenter.com
 * Developer: Alaa Haddad https://www.alaahaddad.com.
 */
((Drupal, drupalSettings, once) => {

  'use strict';

  /**
   * solo helper functions.
   *
   * @namespace
   */
  Drupal.solo = {};

  const cssStyles = {
    overflow: 'hidden',
    height: '0',
    paddingTop: '0',
    paddingBottom: '0',
    marginTop: '0',
    marginBottom: '0'
  };

  const removeStyles = (target) => {
    const stylesToRemove = ['height', 'paddingTop', 'paddingBottom', 'marginTop', 'marginBottom', 'overflow', 'transitionDuration', 'transitionProperty'];
    stylesToRemove.forEach(st => target.style.removeProperty(st));
  }
  Drupal.solo.removeStyles = removeStyles;

  const updateTabindex = (target, isOpen) => {
    const firstLevelItems = target.querySelectorAll(':scope > li > a, :scope > li > button');
    firstLevelItems.forEach(item => {
      item.setAttribute('tabindex', isOpen ? '0' : '-1');
    });
  };

  const slideUp = (target, duration = 600) => {
    target.style.transitionProperty = 'height, margin, padding';
    target.style.transitionDuration = `${duration}ms`;
    target.style.transitionTimingFunction = 'ease-in-out'; // Added for smoother transition
    target.style.boxSizing = 'border-box';
    target.style.height = `${target.offsetHeight}px`;
    target.offsetHeight; // Trigger reflow
    target.classList.remove('toggled');
    target.setAttribute('aria-hidden', 'true');
    updateTabindex(target, false);

    Object.keys(cssStyles)
      .forEach(style => {
        target.style[style] = cssStyles[style];
      });

    setTimeout(() => {
      target.style.display = 'none';
      removeStyles(target);
    }, duration);
  };
  Drupal.solo.slideUp = slideUp;

  const slideDown = (target, menuDisplay = 'block', duration = 600) => {
    target.style.removeProperty('display');
    let currentDisplay = window.getComputedStyle(target).display;

    if (currentDisplay === 'none') currentDisplay = menuDisplay;

    target.style.display = menuDisplay;
    let height = target.offsetHeight;
    height = Math.round(height);
    Object.keys(cssStyles)
      .forEach(style => {
        target.style[style] = cssStyles[style];
      });

    target.offsetHeight; // Trigger reflow
    target.style.boxSizing = 'border-box';
    target.style.transitionProperty = "height, margin, padding";
    target.style.transitionDuration = `${duration}ms`;
    target.style.transitionTimingFunction = 'ease-in-out'; // Added for smoother transition
    target.style.height = `${height}px`;
    target.classList.add('toggled');
    target.setAttribute('aria-hidden', 'false');
    updateTabindex(target, true);

    ['paddingTop', 'paddingBottom', 'marginTop', 'marginBottom'].forEach(property => {
      target.style.removeProperty(property);
    });

    setTimeout(() => {
      ['height', 'overflow', 'transitionDuration', 'transitionProperty'].forEach(property =>
        target.style.removeProperty(property));
    }, duration);
  };

  Drupal.solo.slideDown = slideDown;

  const slideToggle = (target, duration = 500) => {
    if (!target.classList.contains('toggled')) {
      return slideDown(target, duration);
    } else {
      return slideUp(target, duration);
    }
  }
  Drupal.solo.slideToggle = slideToggle;

  // Get current width
  const getCurrentWidth = () => window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

  const calculateDepth = (element) => {
    let depth = 1;
    let parent = element.parentElement;

    while (parent) {
      if (parent.tagName.toLowerCase() === 'ul') {
        depth++;
      }
      parent = parent.parentElement;
    }
    return depth;
  };

  const addClassAccordingToDepth = (element, depth) => {
    element.classList.add(`ul-${depth}`);
    Array.from(element.children).forEach(child => child.classList.add(`li-${depth}`));
  };
  Drupal.solo.calculateDepth = calculateDepth;

  const getMyBreakpoints = (element, mn) => {
    const classes = [`${mn}-576`, `${mn}-768`, `${mn}-992`, `${mn}-1200`, `${mn}-1400`];
    const classList = Array.from(element.classList);
    const foundClass = classList.find(c => classes.includes(c));
    return foundClass ? parseInt(foundClass.split('-')[1]) : 992;
  }
  Drupal.solo.getMyBreakpoints = getMyBreakpoints;

  /**
   * Drupal behavior to add classes based on depth.
   */
  Drupal.behaviors.soloMenuDepth = {
    attach: (context, settings) => {
      const menus = once('soloMenuDepth', '.solo-inner nav ul:not(.field ul)', context);
      menus.forEach((element) => {
        const depth = calculateDepth(element);
        addClassAccordingToDepth(element, depth);
      });
    }
  };

})(Drupal, drupalSettings, once);
