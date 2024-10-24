/**
 * @file
 * Solo
 *
 * Filename:     solo-scripts.js
 * Website:      https://www.flashwebcenter.com
 * Developer:    Alaa Haddad https://www.alaahaddad.com.
 */
((Drupal, drupalSettings, once) => {
  'use strict';

  // Get current widht
  const getCurrentWidth = () => window.innerWidth || document.documentElement
    .clientWidth || document.body.clientWidth;

  const checkRegionsWidth = () => {
    const regions = document.querySelectorAll('.region-inner, .copyright-inner, .footer-menu-inner');
    regions.forEach(region => {
      const regionWidth = region.getBoundingClientRect().width;

      // Remove all previous size classes to prevent class duplication
      region.classList.remove('region-xs', 'region-s', 'region-m', 'region-l', 'region-xl', 'region-xxl');

      // Assign new class based on region width
      if (regionWidth <= 320) {
        region.classList.add('region-xs'); // Extra Small Devices
      } else if (regionWidth > 320 && regionWidth <= 576) {
        region.classList.add('region-s'); // Small Devices
      } else if (regionWidth > 576 && regionWidth <= 768) {
        region.classList.add('region-m'); // Medium Devices
      } else if (regionWidth > 768 && regionWidth <= 992) {
        region.classList.add('region-l'); // Large Devices
      } else if (regionWidth > 992 && regionWidth <= 1200) {
        region.classList.add('region-xl'); // Extra Large Devices
      } else if (regionWidth > 1200) {
        region.classList.add('region-xxl'); // Extra Extra Large Devices
      }
    });
  };

  // Add/remove css classes according to screen changes.
  let mediaSize = function() {
    const currentWidth = getCurrentWidth();
    const bodyTag = document.body;
    // Remove all previous size classes to prevent class duplication
    bodyTag.classList.remove('small-screen', 'medium-screen', 'large-screen');

    if (currentWidth >= 992) {
      bodyTag.classList.add('large-screen');
    } else if (currentWidth >= 576 && currentWidth < 992) {
      bodyTag.classList.add('medium-screen');
    } else if (currentWidth < 576) {
      bodyTag.classList.add('small-screen');
    }

    checkRegionsWidth();

  };

  Drupal.behaviors.soloTheme = {
    attach: function(context, settings) {

      // Select all spans with the class 'file--mime-application-octet-stream'
      const fileSpans = context.querySelectorAll('.field--type-file span.file');

      fileSpans.forEach(function (span) {
        const link = span.querySelector('a');
        if (link && span.contains(link)) {
          const url = link.getAttribute('href');
          const urlParts = url.split('.');
          const fileExtension = urlParts[urlParts.length - 1]; // Get the last part as the file extension
          if (fileExtension) {
            // Add the file extension as a class to the span
            span.classList.add(`file--${fileExtension}`);
          }
        }
      });

      // Ensure code only runs once per element
      const footerMenu = document.querySelector('#footer-menu', context);
      if (footerMenu) {
        const footerFormBg = window.getComputedStyle(footerMenu).backgroundColor;
        const footerFormTxt = window.getComputedStyle(footerMenu).color;
        let footerMenuForm = document.querySelector('#footer-menu form', context);

        if (footerMenuForm) {
          footerMenuForm.style.background = footerFormBg;
          footerMenuForm.style.color = footerFormTxt;
        }
      }

      // Remove attribute 'open'
      const detailsElements = document.querySelectorAll('#system-theme-settings details');
      detailsElements.forEach(element => {
        element.removeAttribute('open');
      });

      // Select all <img> and <picture> elements inside an <a> tag, excluding
      // those with a specific class and also excluding those with classes
      // that contain the word 'icon'
      let clickableElements = document.querySelectorAll('a > img, a > picture');

      const filteredElements = Array.from(clickableElements).filter(el => {
        // Check if any parent up to the root has specific classes to exclude
        let ancestor = el.parentElement;
        while (ancestor && ancestor !== document.body) {
          if (ancestor.matches('.site-logo, .field--name-user-picture, .field--type-text-long, .field--type-text-with-summary')) {
            return false;
          }
          ancestor = ancestor.parentElement;
        }

        // Exclude elements with "icon" in their class or in their parent <a> element's class
        if (el.classList.contains('icon') || (el.parentElement && el.parentElement.classList.contains('icon'))) {
          return false;
        }

        return true;
      });

      // Apply the class to the parent <a> tags
      filteredElements.forEach(function(el) {
        el.parentElement.classList.add('img--is-clickable');
      });

      // Handle broken images
      const handleBrokenImages = (context) => {
        const images = context.querySelectorAll('img');
        images.forEach(img => {
          img.onerror = function() {
            if (!this.classList.contains('broken-image')) {
              this.classList.add('broken-image');
              const placeholder = document.createElement('div');
              placeholder.className = 'img-placeholder';
              placeholder.innerHTML = 'Image not available';
              this.style.display = 'none';
              this.parentNode.insertBefore(placeholder, this.nextSibling);
            }
          };
          // Force recheck the image load status to trigger the error if the image is broken
          img.src = img.src;
        });
      };

      // Call the function to handle broken images
      handleBrokenImages(context);

      mediaSize();
      window.addEventListener('resize', () => {
        mediaSize();
      });


    }
  };

})(Drupal, drupalSettings, once);
