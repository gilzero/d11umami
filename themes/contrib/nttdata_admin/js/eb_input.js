/* *
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal, once) {
  'use strict';

  Drupal.behaviors.addInputWrapper = {
    attach: function (context, settings) {
      $(once('addInputWrapper', '.entities-list .item-container', context)).each(function () {
        let inputs = $(this).find('input.button');
        inputs.wrapAll("<div class='input-container'></div>");
      });
      $(once('addInputWrapper2', 'table.entities-list tbody tr', context)).each(function () {
        let inputParElem = $(this).find('input.button').parent('td');
        inputParElem.addClass('input-parent-element');
      });
    },
  };
})(jQuery, Drupal, once);
