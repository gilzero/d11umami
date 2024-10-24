/**
 * @file
 * Solo
 *
 * Filename:     solo-full-opacity.js
 * Website:      https://www.flashwebcenter.com
 * Developer:    Alaa Haddad https://www.alaahaddad.com.
 */
(function(Drupal, once) {
  'use strict';
  let scrollOn = false;
  const animationHeight = window.innerHeight * 0.15;
  const ratio = Math.round((1 / animationHeight) * 10000) / 10000;

  const fadeBox = () => {
    scrollOn = true;
    const fadeElements = document.querySelectorAll('.fade-inner');
    fadeElements.forEach((element) => {
      const objectTop = element.getBoundingClientRect().top + window.pageYOffset;
      const windowBottom = window.pageYOffset + window.innerHeight;
      const opacity = objectTop < windowBottom - animationHeight ? 1 : (windowBottom - objectTop) * ratio;
      const transition = objectTop < windowBottom - animationHeight ? 'opacity 1s linear' : 'opacity 0.5s linear';

      element.style.transition = transition;
      element.style.opacity = objectTop < windowBottom ? opacity : 0;
    });
  };

  setInterval(() => {
    if (scrollOn) {
      scrollOn = false;
    }
  }, 100);

  Drupal.behaviors.soloFullOpacity = {
    attach: function(context, settings) {
      if (!document.getElementById("layout-builder")) {
        const fadeElements = document.querySelectorAll('.fade-inner');
        fadeElements.forEach((element) => {
          element.style.opacity = 0;
        });
        fadeBox();
        window.addEventListener('scroll', fadeBox);
      }
    }
  };

})(Drupal, once);
