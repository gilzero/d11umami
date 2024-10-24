/* *
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal, once) {
  'use strict';

  Drupal.behaviors.languageDropdown = {
    attach: function (context, settings) {
      $(once('languageDropdown', '.dropdown-language .dropdown-toggle')).click(function (e) {
        const dropdownLinks = $(this).siblings('.dropdown-menu');
        dropdownLinks.toggle("show");

        if (!dropdownLinks.find('.is-active').hasClass('selected')) {
          dropdownLinks.find('.is-active').addClass('selected');
          dropdownLinks.find('.is-active').focus();
        }
      });

    },
  };

  Drupal.behaviors.dropdownFocusout = {
    attach: function (context, settings) {
      $(once('dropdownFocusout', context)).on("keydown", function (event) {
        if (event.key === 'Tab') {
          setTimeout(() => {
            const focusedElement = document.activeElement;
            const dropdownMenu = $('.dropdown-menu');
            if (dropdownMenu.is(':visible') && (!$(focusedElement).parent().hasClass('dropdown-language') && !$(focusedElement).parent().hasClass('dropdown-menu-language'))) {
              dropdownMenu.hide();
              $('.dropdown-language .dropdown-menu a').removeClass('selected');
            }
          }, 0);
        }
      });
    }
  };

  var li = $('.dropdown-language .dropdown-menu a');
  $(window).keydown(function (e) {
    if ((e.which === 38 || e.which === 40) && $('.dropdown-language .dropdown-menu a.selected').length > 0) {
      e.preventDefault();
    }
    setTimeout(() => {
      var focusedElement = $(document.activeElement);
      const dropdownLinks = $('.dropdown-menu');
      const dropdownLinksIsVisible = dropdownLinks.is(":visible");
      if ( dropdownLinksIsVisible && $(focusedElement).parent().hasClass('dropdown-menu-language') ) {
        dropdownLinks.find('a').attr('tabindex', -1)
        if (dropdownLinks.find('.is-active').hasClass('selected')) {
          dropdownLinks.find('.selected').focus();
        }
        var liSelected = $('.dropdown-language .dropdown-menu').find('.selected');
        var next;
        if (e.which === 40) {
          e.preventDefault();
          if (liSelected) {
            next = liSelected.next();
            liSelected.removeClass('selected');
            if (next.length > 0) {
              liSelected = next.addClass('selected');
            } else {
              var firstChild = $('.dropdown-language .dropdown-menu').children().first();
              liSelected = firstChild.addClass('selected');
            }
          } else {
            liSelected = li.eq(0).addClass('selected');
          }
          liSelected.focus();
        }
        else if (e.which === 38) {
          e.preventDefault();
          if (liSelected) {
            liSelected.removeClass('selected');
            next = liSelected.prev();
            if (next.length > 0) {
              liSelected = next.addClass('selected');
            } else {
              var lastChild = $('.dropdown-language .dropdown-menu').children().last();
              liSelected = lastChild.addClass('selected');
            }
          } else {
            liSelected = li.last().addClass('selected');
          }
          liSelected.focus();
        }
      }
    }, 0);
  });
})(jQuery, Drupal, once);
