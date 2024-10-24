<?php

/**
 * @file
 * Preset lists of font options.
 */

/**
 * Websafe fonts list.
 */
function websafe_fonts() {
  $websafe_fonts = [
    'Verdana, Geneva, sans-serif',
    '"Segoe UI", Segoe, Tahoma, Geneva, sans-serif',
    '"Helvetica Neue", Helvetica, Arial, sans-serif',
    'Georgia, Utopia, Palatino, "Palatino Linotype", serif',
    '"Times New Roman", Times, serif',
    'Impact, "Helvetica Inserat", "Arial Black", sans-serif',
    '"Courier New", Courier, monospace, sans-serif',
    'sans-serif',
    'serif',
  ];

  return $websafe_fonts;
}

/**
 * Font Elements Config Array.
 *
 * The fonts array is used by the fonts sub-system to generate forms,
 * build stylesheets and load font styles.
 */
function font_elements() {
  $elements_array = &drupal_static(__FUNCTION__, []);
  if (empty($elements_array)) {
    $elements_array = [
      'base' => [
        'label' => 'Base',
        'selector' => 'html, body, textarea, p',
      ],
      'sitename' => [
        'label' => 'Site name',
        'selector' => '.site-branding__name-link',
      ],
      'siteslogan' => [
        'label' => 'Site slogan',
        'selector' => '.site-branding__slogan',
      ],
      'pagetitle' => [
        'label' => 'Page title',
        'selector' => '.node__title, .page__title, .user__title',
      ],
      'nodetitle' => [
        'label' => 'Node title (full)',
        'selector' => '.node--view-mode-full .node__title, .node--view-mode-full .field-node--node-title *',
      ],
      'nodetitle_teaser' => [
        'label' => 'Node title (teaser)',
        'selector' => '.node--view-mode-teaser .node__title, .node--view-mode-teaser .field-node--node-title *',
      ],
      'commenttitle' => [
        'label' => 'Comment title',
        'selector' => '.comment__title, .comment__title a, .field-comment--comment-title *',
      ],
      'blocktitle' => [
        'label' => 'Block title',
        'selector' => '.block__title, .block-menu__title, .pane__title',
      ],
      'links' => [
        'label' => 'Links',
        'selector' => 'a, .un-button',
      ],
      'menus' => [
        'label' => 'Menus',
        'selector' => '.menu a',
      ],
      'responsivemenu' => [
        'label' => 'Responsive menu',
        'selector' => '.rm-block, .rm-block a',
      ],
      'responsivemenu_toggle' => [
        'label' => 'Responsive menu toggle',
        'selector' => '.rm-toggle, .rm-toggle__link',
      ],
      'image_captions' => [
        'label' => 'Image captions',
        'selector' => 'figcaption, .field-type-image__figcaption',
      ],
      'slideshow_captions' => [
        'label' => 'Slideshow captions',
        'selector' => 'p.flex-caption',
      ],
      'tabs' => [
        'label' => 'Tabs',
        'selector' => '.tabs a',
      ],
      'breadcrumbs' => [
        'label' => 'Breadcrumbs',
        'selector' => '.breadcrumb__list-item, .breadcrumb__link, .breadcrumb__title',
      ],
      'h1h4' => [
        'label' => 'h1 to h4 headings',
        'selector' => 'h1, h2, h3, h4, .h1, .h2, .h3, .h4',
      ],
      'h5h6' => [
        'label' => 'h5, h6 headings',
        'selector' => 'h5, h6, .h5, .h6',
      ],
      'h1' => [
        'label' => 'h1',
        'selector' => 'h1, .h1',
      ],
      'h2' => [
        'label' => 'h2',
        'selector' => 'h2, .h2',
      ],
      'h3' => [
        'label' => 'h3',
        'selector' => 'h3, .h3',
      ],
      'h4' => [
        'label' => 'h4',
        'selector' => 'h4, .h4',
      ],
      'h5' => [
        'label' => 'h5',
        'selector' => 'h5, .h5',
      ],
      'h6' => [
        'label' => 'h6',
        'selector' => 'h6, .h6',
      ],
      'formitems' => [
        'label' => 'Form elements',
        'selector' => 'form, input, select, textarea',
      ],
      'buttons' => [
        'label' => 'Buttons',
        'selector' => 'button, .button',
      ],
      'tables' => [
        'label' => 'Tables',
        'selector' => 'table',
      ],
      'blockquotes' => [
        'label' => 'Blockquotes & citations',
        'selector' => 'blockquote, cite, q',
      ],
      'code' => [
        'label' => 'Code',
        'selector' => 'code, pre, var, kbd, samp',
      ],
      'custom_selectors' => [
        'label' => 'Custom selectors',
        'selector' => 'custom_selectors',
      ],
    ];
  }

  return $elements_array;
}

/**
 * Reusable style options for titles.
 *
 * @param $style
 *
 * @return array
 */
function title_style_options($style) {
  $options = &drupal_static(__FUNCTION__, []);
  switch ($style) {
    case ('case'):
      $options = [
        'none'       => t('Normal'),
        'uppercase'  => t('Upper case'),
        'lowercase'  => t('Lower case'),
        'capitalize' => t('Capitalize'),
        'small-caps' => t('Small caps'),
        'inherit'    => t('Inherit'),
      ];
      break;

    case ('weight'):
      $options = [
        '400' => t('Normal'),
        '700' => t('Bold'),
        '600' => t('Semi-bold'),
        '300' => t('Light'),
      ];
      break;

    case ('alignment'):
      $options = [
        'left'   => t('Left'),
        'right'  => t('Right'),
        'center' => t('Centered'),
      ];
      break;
  }

  return $options;
}

/**
 * Reusable valid type options for titles.
 */
function title_valid_type_options() {
  $options = [
    'sitename',
    'siteslogan',
    'pagetitle',
    'nodetitle',
    'nodetitle_teaser',
    'blocktitle',
    'commenttitle',
    'h1',
    'h2',
    'h3',
    'h4',
    'h5',
    'h6',
  ];

  return $options;
}
