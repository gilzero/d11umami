/* *
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal, once) {
  'use strict';

  Drupal.behaviors.support_items = {
    attach: function (context, settings) {
      once('support_items', '.support-area', context).forEach(function (element) {
        let opne_btn = document.querySelector('.support-area .open-btn'),
          page_body = document.querySelector('body'),
          nextSibling = opne_btn.previousElementSibling,
          support_btn = document.querySelectorAll('.support-area .support-dropdown .support-button'),
          theme = 'unset',
          contrast = 'unset';

        // Action to open the support menu.
        opne_btn.addEventListener('click', function () {
          opne_btn.classList.toggle('active');
          nextSibling.classList.toggle('active');
        });

        // Action of dark-mode button (moon icon) and contrast button (half circle icon).
        let body = document.body;
        support_btn.forEach(function (e) {
          e.addEventListener('click', function () {
            e.querySelector('.btn-state').classList.toggle('active');

            if (e.classList.contains('contrast-btn')) {
              page_body.classList.toggle('high-ctr');
              if (body.classList.contains('high-ctr')) {
                contrast = 'enabled';
              } else {
                contrast = 'disabled';
              }
              localStorage.setItem('contrast', contrast);
            } else if (e.classList.contains('dark-btn')) {
              page_body.classList.toggle('dark-mode');
              if (body.classList.contains('dark-mode')) {
                theme = 'dark';
              } else {
                theme = 'light';
              }
              localStorage.setItem('theme', theme);
            }
          });
        });

        // Check DARK Mode at the local storage and if not set check at operating system level.
        const current_theme = localStorage.getItem('theme');
        let system_dark_scheme = window.matchMedia('(prefers-color-scheme: dark)');
        const dark_mode_btn_state = document.querySelector('.dark-btn .btn-state');

        if (current_theme == 'dark') {
          body.classList.add('dark-mode');
          dark_mode_btn_state.classList.add('active');
        } else if (current_theme == 'light') {
          body.classList.remove('dark-mode');
          dark_mode_btn_state.classList.remove('active');
        } else {
          if (system_dark_scheme.matches) {
            body.classList.add('dark-mode');
            dark_mode_btn_state.classList.add('active');
          } else {
            body.classList.remove('dark-mode');
            dark_mode_btn_state.classList.remove('active');
          }
        }

        // Check CONTRAST Mode at the local storage and if not set check at operating system level.
        const current_contrast = localStorage.getItem('contrast');
        let system_contrast_scheme = window.matchMedia('(-ms-high-contrast: active)');
        const contrast_mode_btn_state = document.querySelector('.contrast-btn .btn-state');

        if (current_contrast == 'enabled') {
          body.classList.add('high-ctr');
          contrast_mode_btn_state.classList.add('active');
        } else if (current_contrast == 'disabled') {
          body.classList.remove('high-ctr');
          contrast_mode_btn_state.classList.remove('active');
        } else {
          if (system_contrast_scheme.matches) {
            body.classList.add('high-ctr');
            contrast_mode_btn_state.classList.add('active');
          } else {
            body.classList.remove('high-ctr');
            contrast_mode_btn_state.classList.remove('active');
          }
        }
      });
    },
  };

  Drupal.behaviors.stickyActions = {
    attach: function (context, settings) {
      let $bulkActions = $('.view-content .views-form [data-drupal-selector=edit-header]')
        .add('.webform-bulk-form > .container-inline');
      // return if not applicable
      if ($bulkActions.length === 0) return;
      let hasVScroll = $(document).height() > $(window).height();
      // Remove sticky when scroll isn't present
      if (!hasVScroll) {
        $bulkActions.removeClass('is-fixed-to-bottom');
      }
      // Add & remove sticky on scroll
      $(window).on("scroll", function () {
        if ($(window).scrollTop() + window.innerHeight > $(document).height() - 100) {
          $bulkActions.removeClass('is-fixed-to-bottom');
        } else {
          $bulkActions.addClass('is-fixed-to-bottom');
        }
      });
    },
  };

  // Add class to body when dialog is opened and remove it when is closed
  Drupal.behaviors.addClassWhenModalOpen = {
    attach: function (context, settings) {
      $(document).on('dialogopen', function () {
        $('html').addClass('dialog-open');
      });
      $(document).on('dialogclose', function () {
        $('html').removeClass('dialog-open');
      });
    }
  };

  // Close modal when click on background
  Drupal.behaviors.closeModalsWhenClickingBackdrop = {
    attach: function (context, settings) {
      $(once('closeModalsWhenClickingBackdrop', context)).on("click", ".ui-widget-overlay", function () {
        $.each($(".ui-dialog"), function () {
          var $modal = $(this).children(".ui-dialog-content");
          if ($modal.dialog("option", "modal")) {
            $modal.dialog("close");
          }
        });
      });
    },
  };

  Drupal.behaviors.genericThumbnails = {
    attach: function (context, settings) {
      $('.image-style-thumbnail, .image-style-media-library').each(function () {
        var $img_source = $(this).attr('src');
        if ($img_source.includes("/public/media-icons")) {
          $(this).addClass("dk-thumbnail");
        }
      });
    },
  };

  // On the admin toolbar, mark all links with the class 'is-active' to trace the link path
  Drupal.behaviors.adminToolbarActiveItem = {
    attach: function (context, settings) {
      let activeLink = $('.toolbar-menu .menu-item .is-active');

      if (activeLink.length > 0) {
        let parentList = activeLink.closest('.toolbar-menu');
        let linkSibling = parentList.siblings('a');
        // Check all parents links and add 'is-active' class
        while (linkSibling.length > 0 && !linkSibling.hasClass('is-active')) {
          linkSibling.addClass('is-active');
          parentList = linkSibling.closest('.toolbar-menu');
          linkSibling = parentList.siblings('a');
        }
      }
    },
  };

  Drupal.behaviors.responsiveTables = {
    attach: function (context, settings) {
      function transformToCards() {
        let table = $('table.responsive-enabled.draggable-table, table.permissions, table.responsive-enabled.custom-responsive-enabled');
        if ($(window).width() <= 768) {
          let headerCells = table.find('thead th');
          let labels = {};
          headerCells.each(function (index, item) {
            labels[index] = $(item).text();
          });
          table.find('thead').hide();
          let tableRows = table.find('tbody tr');
          let tableRowsModule = tableRows.find('.module').closest('tr').addClass('module');
          tableRows.each(function () {
            let tableCells = $(this).find('td');
            if (tableCells.length > 1) {
              tableCells.each(function (index, item) {
                if (index < Object.keys(labels).length
                  && $(item).find('strong:first').length == 0 // only write label if there is no label already (in a <strong> element)
                  && $(item).find('input[type="hidden"]:first').length == 0 // only write label if the corresponding input is not of type "hidden"
                  && !$(item).hasClass('tabledrag-cell') // only write label if <td> is not a draggable row (in this case, it is the "title" line, so no label is needed)
                  && $(item).find('.permission').length == 0) { // only write label if <td> doesn't correspond to the "permission" title: only in permissions table
                  // let firstDiv = $(item).find('div:first');
                  // se não tiver nada dentro dessa <td>? Não há div. Mas pode ter texto.
                  // firstDiv.before(strongElement);
                  if ($(item).children().length > 0) {
                    let strongElement = $('<strong class="element-label">' + labels[index] + '</strong>');
                    $(item).children().first().before(strongElement);
                  } else {
                    let itemText = $(item).text();
                    let strongElement = $('<strong class="element-label inline-element">' + labels[index] + '</strong>');
                    $(item).html(strongElement);
                    $(item).append(itemText);
                  }
                }
              });
            }
          });
        } else {
          table.find('thead').show();
          let elementLabels = $('.element-label');
          if (elementLabels.length > 0) {
            elementLabels.remove();
          }
        }
      };
      // Call the function initially
      transformToCards();

      // Re-transform when window resizes
      $(window).on('resize', transformToCards);
    },
  };

  Drupal.behaviors.responsiveTablesPermissions = {
    attach: function (context, settings) {
      function showHideRoles() {
        let table = $('table.permissions');
        if ($(window).width() <= 768) {
          let tablePermissions = table.find('tbody td .permission');
          tablePermissions.each(function (index, item) {
            if ($(item).closest('td').next('.roles-toggle-button').length == 0) {
              let $button = $('<button>', {
                class: 'link tableresponsive-toggle',
                text: Drupal.t('Show roles')
              });
              $(item).closest('td').after($button.addClass('roles-toggle-button'));
              $(once('responsiveTablesPermissions', '.roles-toggle-button')).on('click', function (e) {
                e.preventDefault();
                let rolesCheckboxes = $(this).siblings('.checkbox');
                if (rolesCheckboxes.hasClass("visible")) {
                  $(this).text(Drupal.t('Show roles'));
                  rolesCheckboxes.removeClass("visible");
                } else {
                  $(this).text(Drupal.t('Hide roles'));
                  rolesCheckboxes.addClass("visible");
                }
              });
            }
          });
        } else {
          let rolesToggleBtn = $('.roles-toggle-button');
          if (rolesToggleBtn.length > 0) {
            rolesToggleBtn.siblings('.checkbox').removeClass("visible");
            rolesToggleBtn.remove();
          }
        }
      };
      // Call the function initially
      showHideRoles();

      // Re-transform when window resizes
      $(window).on('resize', showHideRoles);
    },
  };
})(jQuery, Drupal, once);
