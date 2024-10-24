/**
 * @file
 * Solo
 *
 * Filename:     solo-form-theme-settings.js
 * Website:      https://www.flashwebcenter.com
 * Developer:    Alaa Haddad https://www.alaahaddad.com.
 */
((Drupal) => {

  'use strict';

  // Close details on load
  const formDetails = document.querySelectorAll('.system-theme-settings>details');
  formDetails.forEach((formDetail) => {
    formDetail.removeAttribute('open');
  });

  Drupal.behaviors.soloFormThemeSettings = {
    attach: function(context, settings) {
      const categorySelect = context.querySelector('#edit-theme-category');
      if (!categorySelect) {
        return;
      }

      const themeSelect = context.querySelector('#edit-predefined-current-theme');
      if (!themeSelect) {
        return;
      }

      // Initialize once
      if (categorySelect.getAttribute('data-solo-theme-settings-processed') !== 'true') {
        categorySelect.setAttribute('data-solo-theme-settings-processed', 'true');

        // Clone original options
        const initialOptions = Array.from(themeSelect.options);

        // Function to filter theme options based on selected category
        const filterThemeOptions = (selectedCategory) => {
          themeSelect.innerHTML = '';
          if (selectedCategory !== 'none') {
            initialOptions.forEach((option) => {
              if (option.value.startsWith(selectedCategory + '|')) {
                themeSelect.appendChild(option.cloneNode(true));
              }
            });
          }
        };

        // Filter options on category change
        categorySelect.addEventListener('change', function() {
          filterThemeOptions(this.value);
        });

        // Also filter options based on the default value or current value
        filterThemeOptions(categorySelect.value);
      }
    }
  };
})(Drupal);

