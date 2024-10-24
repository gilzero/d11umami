/**
 * @file
 * Responsive menus.
 */
(function ($, document, window) {

  "use strict";

  Drupal.behaviors.atrM = {
    attach: function (context, settings) {

      $('.rm-block').removeClass('js-hide');

      // Verify that the user agent understands media queries.
      if (!window.matchMedia('only screen').matches) {
        return;
      }

      var at = settings['ajaxPageState']['theme'],
          rm = settings[at]['at_responsivemenus'],
          def = rm['default'],
          resp = rm['responsive'],
          tl = '.rm-block .rm-toggle__link',
          acd_def = rm['acd']['acd_default'],
          acd_resp = rm['acd']['acd_responsive'],
          acd_both = rm['acd']['acd_both'],
          acd_load = rm['acd']['acd_load'];

      // Hamburger toggles.
      function toggleClick(e) {
        e.preventDefault();
        e.stopPropagation();
        // The toggle class is on <body> because we must account for menu types
        // that style block parent elements, e.g. off-canvas will transform the
        // .page element. Toggle aria attributes for accessibility on block
        // elements.
        $(document.body).toggleClass('rm-is-open');
        if ($(this).attr('aria-expanded') == 'true') {
          $(this).attr('aria-expanded', 'false');
          $('#rm-toggle__icon--use').attr("xlink:href", "#rm-toggle__icon--open");
        } else if ($(this).attr('aria-expanded') == 'false') {
          $(this).attr('aria-expanded', 'true');
          $('#rm-toggle__icon--use').attr("xlink:href", "#rm-toggle__icon--close");
        }
        $(document).one('click', function(e) {
          if($('.rm-block').has(e.target).length === 0){
            $(document.body).removeClass('rm-is-open');
            $(tl).attr("aria-expanded", "false");
            $('#rm-toggle__icon--use').attr("xlink:href", "#rm-toggle__icon--open");
          }
        });
      }
      $(tl, context).on('click', toggleClick);

      // Accordion toggles.
      function accordionClick(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).toggleClass('is-open--parent');
        if ($(this).attr('aria-expanded') == 'true') {
          $(this).attr('aria-expanded', 'false');
        } else if ($(this).attr('aria-expanded') == 'false') {
          $(this).attr('aria-expanded', 'true');
        }
        $(this).parent().next('.is-child').toggleClass('is-open--child');
      }

      // Copy and prepend buttons to parent item links.
      function copyButtons(p) {
        var button = $('#rm-accordion-trigger').html();
        $(p).each(function() {
          // Avoid adding buttons twice if enquire fires twice.
          if($(this).next('.rm-accordion-trigger').length == 0) {
            $(this).after(button);
          }
          var mlid = $(this).parent().parent().attr('id');
          $(this).next().attr('aria-controls', mlid + '__child-menu');
          $(this).parent().next('.is-child').attr('id', mlid + '__child-menu');
        });
      }

      // Enquire is a fancy wrapper for matchMedia.
      enquire
      .register(rm['bp'], {
        // Setup fires strait away unless deferred.
        setup: function() {
          $(document.body).addClass(def);
          $('.rm-block').parent('.l-r').addClass('rm-region').parent('.l-rw').addClass('rm-row');

          if (def == 'ms-dropmenu') {
            $('.rm-block__content li:has(ul)').doubleTapToGo();
          }

          if (acd_def == true && acd_load == true) {
            $('.rm-block .menu-level-1').addClass('ms-accordion');
            $.ready(copyButtons('.ms-accordion .is-parent__wrapper .menu__link'));
            $('.ms-accordion .rm-accordion-trigger', context).on('click', accordionClick);
          }
        },
        // The resp menu system only uses one breakpoint, if it matches this
        // fires strait after setup. By default resp is a "desktop view".
        match: function() {
          if (resp !== 'ms-none') {
            if (resp !== def) {
              $(document.body).removeClass(def).addClass(resp);

              if (acd_load == true) {
                if (acd_resp == true) {
                  if (acd_both == false) {
                    $('.rm-block .menu-level-1').addClass('ms-accordion');
                    $.ready(copyButtons('.ms-accordion .is-parent__wrapper .menu__link'));
                    $('.ms-accordion .rm-accordion-trigger', context).on('click', accordionClick);
                  }
                } else {
                  $('.ms-accordion .rm-accordion-trigger').remove();
                  $('.rm-block .menu-level-1').removeClass('ms-accordion');
                  $('.rm-block .menu').removeClass('is-open--child');
                }
              }

              if (resp == 'ms-dropmenu') {
                $('.rm-block__content li:has(ul)').doubleTapToGo();
              } else {
                $('.rm-block__content li:has(ul)').doubleTapToGo('unbind');
              }
            }
          }
        },
        // unmatch fires the first time the media query is unmatched.
        unmatch : function() {
          $(document.body).addClass(def);

          if (acd_load == true) {
            if (acd_def == true) {
              if (acd_both == false) {
                $('.rm-block .menu-level-1').addClass('ms-accordion');
                $.ready(copyButtons('.ms-accordion .is-parent__wrapper .menu__link'));
                $('.ms-accordion .rm-accordion-trigger', context).on('click', accordionClick);
              }
            } else {
              $('.ms-accordion .rm-accordion-trigger').remove();
              $('.rm-block .menu-level-1').removeClass('ms-accordion');
              $('.rm-block .menu').removeClass('is-open--child');
            }
          }

          if (def == 'ms-dropmenu') {
            $('.rm-block__content li:has(ul)').doubleTapToGo();
          } else {
            $('.rm-block__content li:has(ul)').doubleTapToGo('unbind');
          }

          if (resp !== def) {
            $(document.body).removeClass(resp);
          }
        }
      });
    }
  };
}(jQuery, document, window));
