/*
 Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.md or http://ckeditor.com/license
*/

// Set the skin name.
CKEDITOR.skin.name = "mimic";

// Unset browser based skin css files.
CKEDITOR.skin.ua_editor = '';
CKEDITOR.skin.ua_dialog = '';

// Check if font-awesome has loaded & set body classes.
if (document.documentElement.classList.contains('fa-loaded') == true) {
  CKEDITOR.config.bodyClass = "fa-loaded";
} else {
  CKEDITOR.config.bodyClass = "fa-unavailable";
}

// Workaround solution to make CKEditor respect textarea row count as set in the
// form display settings.
// Related CKEditor issue: https://dev.ckeditor.com/ticket/5153
// Drupal issue: https://www.drupal.org/node/2717599
// Thank-you to https://www.drupal.org/u/zserno,
// see https://www.drupal.org/node/2717599#comment-11317995
var rows = document.body.querySelector('[data-editor-active-text-format]').getAttribute("rows");
var height = (rows * 1.5) + 'rem';
CKEDITOR.config.height = height;
