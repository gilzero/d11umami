<?php

/**
 * @file
 * Generate settings for the Slideshows form.
 */

use Drupal\Component\Utility\Html;

$form['slideshows'] = [
  '#type' => 'details',
  '#title' => t('Slideshows'),
  '#group' => 'extension_settings',
];

$form['slideshows']['help'] = [
  '#type' => 'container',
  '#markup' => t('Choose how many slideshows you want to configure, then set options for each slideshow.'),
];

$form['slideshows']['settings_slideshow_count'] = [
  '#type' => 'number',
  '#title' => t('Number of slideshows'),
  '#attributes' => [
    'min' => 1,
    'max' => 10,
    'step' => 1,
  ],
  '#default_value' => theme_get_setting('settings.slideshow_count'),
  '#description' => t('Set the number of slideshows you want to configure then save the Extension Settings to generate options and markup code for each slideshow. A "slideshow" is markup with associated CSS classes and settings that you can use in a custom block, node or template.'),
];

$slideshow_count = theme_get_setting('settings.slideshow_count');

if (isset($slideshow_count) && $slideshow_count >= 1) {
  for ($i = 0; $i < $slideshow_count; $i++) {

    $slideshow_class = Html::cleanCssIdentifier($theme . '-slideshow-' . $i);

    $form['slideshows']['slideshow_' . $i]['slideshow_options'] = [
      '#type' => 'details',
      '#title' => t('Options: @slidername', ['@slidername' => $slideshow_class]),
    ];

    // Enable/disable toggle.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['settings_slideshow_' . $i . '_enable'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable this slideshow'),
      '#default_value' => theme_get_setting('settings.slideshow_' . $i . '_enable'),
      '#description' => t('Only enable slideshows you are using.'),
    ];

    // Fieldset to globally disabled or enable form elements.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper'] = [
      '#type' => 'fieldset',
      '#title' => t('Settings for @slidername', ['@slidername' => $slideshow_class]),
      '#states' => [
        'visible' => ['input[name="settings_slideshow_' . $i . '_enable"]' => ['checked' => TRUE]],
      ],
    ];

    /* BASIC */
    // Animation : String Controls the animation type, "fade" or "slide".
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['settings_slideshow_' . $i . '_animation'] = [
      '#type' => 'select',
      '#title' => t('Animation'),
      '#options' => [
        'slide' => t('Slide'),
        'fade'  => t('Fade'),
      ],
      '#default_value' => theme_get_setting('settings.slideshow_' . $i . '_animation'),
    ];

    // Direction : String Controls the animation direction, "horizontal" or "vertical".
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['settings_slideshow_' . $i . '_direction'] = [
      '#type' => 'select',
      '#title' => t('Direction'),
      '#options' => [
        'horizontal' => t('horizontal'),
        'vertical'   => t('vertical'),
      ],
      '#default_value' => theme_get_setting('settings.slideshow_' . $i . '_direction'),
      '#states' => [
        'visible' => ['select[name="settings_slideshow_' . $i . '_animation"]' => ['value' => 'slide']],
      ],
    ];

    // smoothHeight     : false,          // Boolean Animate the height of the slider smoothly for slides of varying height.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['settings_slideshow_' . $i . '_smoothheight'] = [
      '#type' => 'checkbox',
      '#title' => t('Smooth height'),
      '#default_value' => theme_get_setting('settings.slideshow_' . $i . '_smoothheight'),
      '#description' => t('Animate the height of the slider for slides of varying height. NOTE: this can delay slideshow rendering.'),
      '#states' => [
        'visible' => [
          'select[name="settings_slideshow_' . $i . '_animation"]' => ['value' => 'slide'],
          'select[name="settings_slideshow_' . $i . '_direction"]' => ['value' => 'horizontal'],
        ],
      ],
    ];

    // slideshowSpeed : Number Set the speed of the slideshow cycling, in milliseconds.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['settings_slideshow_' . $i . '_slideshowspeed'] = [
      '#type' => 'number',
      '#title' => t('Slideshow speed'),
      '#attributes' => [
        'min' => 100,
        'max' => 10000,
        'step' => 100,
      ],
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_slideshowspeed') ? theme_get_setting('settings.slideshow_' . $i . '_slideshowspeed') : 4000,
      '#description' => t('Set the speed of the slideshow cycling, in milliseconds.'),
    ];

    // animationSpeed : Number Set the speed of animations, in milliseconds.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['settings_slideshow_' . $i . '_animationspeed'] = [
      '#type' => 'number',
      '#title' => t('Animation speed'),
      '#attributes' => [
        'min' => 0,
        'max' => 5000,
        'step' => 50,
      ],
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_animationspeed') ? theme_get_setting('settings.slideshow_' . $i . '_animationspeed') : 600,
      '#description' => t('Set the speed of animations, in milliseconds.'),
    ];

    // controlNav : Boolean Create navigation for paging control of each slide.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['settings_slideshow_' . $i . '_controlnav'] = [
      '#type' => 'checkbox',
      '#title' => t('Pager <small>(Show the pager)</small>'),
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_controlnav') ? theme_get_setting('settings.slideshow_' . $i . '_controlnav') : 1,
    ];

    // Thumbnail controlNav toggle.
    // TODO - probably remove this, setting the data-thumb attribute is probably too hard for most users to do manually in markup.
    /*
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['settings_slideshow_' . $i . '_controlnav_thumbs'] = array(
    '#type' => 'checkbox',
    '#title' => t('Use thumbnail pager'),
    '#default_value' => theme_get_setting('settings.slideshow_' . $i . '_controlnav_thumbs'),
    '#states' => array(
    'invisible' => array('input[name="settings_slideshow_' . $i . '_controlnav"]' => array('checked' => FALSE)),
    ),
    );
     */

    // directionNav : Boolean Create previous/next arrow navigation.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['settings_slideshow_' . $i . '_directionnav'] = [
      '#type' => 'checkbox',
      '#title' => t('Controls <small>(Show previous/next links)</small>'),
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_directionnav') ? theme_get_setting('settings.slideshow_' . $i . '_directionnav') : 1,
    ];

    /* Carousels */
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['settings_slideshow_' . $i . '_as_carousel'] = [
      '#type' => 'checkbox',
      '#title' => t('Carousel <small>(Requires Animation: slide, Direction: horizontal)</small>'),
      '#default_value' => theme_get_setting('settings.slideshow_' . $i . '_as_carousel'),
      '#states' => [
        'enabled' => [
          'select[name="settings_slideshow_' . $i . '_animation"]' => ['value' => 'slide'],
          'select[name="settings_slideshow_' . $i . '_direction"]' => ['value' => 'horizontal'],
        ],
      ],
    ];

    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['carousels'] = [
      '#type' => 'fieldset',
      '#title' => t('Options for carousels'),
      '#states' => [
        'visible' => [
          'input[name="settings_slideshow_' . $i . '_as_carousel"]' => ['checked' => TRUE],
          'select[name="settings_slideshow_' . $i . '_animation"]' => ['value' => 'slide'],
          'select[name="settings_slideshow_' . $i . '_direction"]' => ['value' => 'horizontal'],
        ],
      ],
    ];

    // itemWidth : Number Box-model width of individual carousel items, including horizontal borders and padding.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['carousels']['settings_slideshow_' . $i . '_itemwidth'] = [
      '#type' => 'number',
      '#title' => t('Item width (px)'),
      '#attributes' => [
        'min' => 40,
        'max' => 1000,
        'step' => 1,
      ],
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_itemwidth') ? theme_get_setting('settings.slideshow_' . $i . '_itemwidth') : 300,
      '#description' => t('Set the width of individual carousel items.'),
    ];

    // itemMargin : Number Margin between carousel items.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['carousels']['settings_slideshow_' . $i . '_itemmargin'] = [
      '#type' => 'number',
      '#title' => t('Item margin (px)'),
      '#attributes' => [
        'min' => 0,
        'max' => 100,
        'step' => 1,
      ],
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_itemmargin') ? theme_get_setting('settings.slideshow_' . $i . '_itemmargin') : 0,
      '#description' => t('Set the margin between carousel items.'),
    ];

    // minItems : Number Minimum number of carousel items that should be visible.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['carousels']['settings_slideshow_' . $i . '_minitems'] = [
      '#type' => 'number',
      '#title' => t('Min items'),
      '#attributes' => [
        'min' => 1,
        'max' => 12,
        'step' => 1,
      ],
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_minitems') ? theme_get_setting('settings.slideshow_' . $i . '_minitems') : 2,
      '#description' => t('Set the minimum number of carousel items that should be visible.'),
    ];

    // maxItems : Number Maximum number of carousel items that should be visible.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['carousels']['settings_slideshow_' . $i . '_maxitems'] = [
      '#type' => 'number',
      '#title' => t('Max items'),
      '#attributes' => [
        'min' => 1,
        'max' => 24,
        'step' => 1,
      ],
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_maxitems') ? theme_get_setting('settings.slideshow_' . $i . '_maxitems') : 4,
      '#description' => t('Set the maximum number of carousel items that should be visible.'),
    ];

    // Move : 0 Number Number of carousel items that should move on animation.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['carousels']['settings_slideshow_' . $i . '_move'] = [
      '#type' => 'number',
      '#title' => t('Move'),
      '#attributes' => [
        'min' => 1,
        'max' => 12,
        'step' => 1,
      ],
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_move') ? theme_get_setting('settings.slideshow_' . $i . '_move') : 1,
      '#description' => t('Set the number of carousel items that should move on animation.'),
    ];

    /* ADVANCED */
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options'] = [
      '#type' => 'details',
      '#title' => t('Advanced Options'),
      '#open' => FALSE,
    ];

    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options']['settings_slideshow_' . $i . '_pauseonaction'] = [
      '#type' => 'checkbox',
      '#title' => t('Pause on action'),
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_pauseonaction') ? theme_get_setting('settings.slideshow_' . $i . '_pauseonaction') : 1,
      '#description' => t('Pause the slideshow when interacting with control elements.'),
    ];

    // pauseOnHover : Boolean Pause the slideshow when hovering over slider, then resume when no longer hovering.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options']['settings_slideshow_' . $i . '_pauseonhover'] = [
      '#type' => 'checkbox',
      '#title' => t('Pause on hover'),
      '#default_value' => theme_get_setting('settings.slideshow_' . $i . '_pauseonhover'),
      '#description' => t('Pause the slideshow when hovering over slider, then resume when no longer hovering.'),
    ];

    // animationLoop: true,  Boolean Gives the slider a seamless infinite loop.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options']['settings_slideshow_' . $i . '_animationloop'] = [
      '#type' => 'checkbox',
      '#title' => t('Animation loop'),
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_animationloop') ? theme_get_setting('settings.slideshow_' . $i . '_animationloop') : 1,
      '#description' => t('Gives the slider a seamless infinite loop.'),
    ];

    // Reverse          : false,             // Boolean Reverse the animation direction.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options']['settings_slideshow_' . $i . '_reverse'] = [
      '#type' => 'checkbox',
      '#title' => t('Reverse'),
      '#default_value' => theme_get_setting('settings.slideshow_' . $i . '_reverse'),
      '#description' => t('Reverse the animation direction.'),
    ];

    // Randomize        : false,          // Boolean Randomize slide order, on load.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options']['settings_slideshow_' . $i . '_randomize'] = [
      '#type' => 'checkbox',
      '#title' => t('Randomize'),
      '#default_value' => theme_get_setting('settings.slideshow_' . $i . '_randomize'),
      '#description' => t('Randomize slide order, on load.'),
    ];

    // This one is really bad variable name, we customize it here rather than use the default Flexslider variable.
    // slideshow: true,  Boolean Setup a slideshow for the slider to animate automatically.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options']['settings_slideshow_' . $i . '_autostart'] = [
      '#type' => 'checkbox',
      '#title' => t('Auto start'),
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_autostart') ? theme_get_setting('settings.slideshow_' . $i . '_autostart') : 1,
      '#description' => t('Start the slideshow automatically.'),
    ];

    // initDelay        : 0,              // Number Set an initialization delay, in milliseconds.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options']['settings_slideshow_' . $i . '_initdelay'] = [
      '#type' => 'number',
      '#title' => t('Initialization delay'),
      '#attributes' => [
        'min' => 0,
        'max' => 10000,
        'step' => 50,
      ],
      '#default_value' => theme_get_setting('settings.slideshow_' . $i . '_initdelay'),
      '#description' => t('Set an initialization delay, in milliseconds, e.g. 100.'),
    ];

    // Easing           : "swing",        // String Determines the easing method used in jQuery transitions.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options']['settings_slideshow_' . $i . '_easing'] = [
      '#type' => 'select',
      '#title' => t('Easing'),
      '#options' => [
        'swing' => t('swing'),
      ],
      '#default_value' => theme_get_setting('settings.slideshow_' . $i . '_easing'),
      '#description' => t('Determines the easing method used in jQuery transitions.'),
    ];

    // useCSS           : false,           // Boolean Slider will use CSS3 transitions, if available.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options']['settings_slideshow_' . $i . '_usecss'] = [
      '#type' => 'checkbox',
      '#title' => t('Use CSS'),
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_usecss') ? theme_get_setting('settings.slideshow_' . $i . '_usecss') : 0,
      '#description' => t('Slider will use CSS3 transitions if the browser supports them. Un-check if you encounter issues with flickering or flashes.'),
    ];

    // Touch            : true,           // Boolean Allow touch swipe navigation of the slider on enabled devices.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options']['settings_slideshow_' . $i . '_touch'] = [
      '#type' => 'checkbox',
      '#title' => t('Touch swipe navigation'),
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_touch') ? theme_get_setting('settings.slideshow_' . $i . '_touch') : 1,
      '#description' => t('Allow touch swipe navigation of the slider on enabled devices.'),
    ];

    // Video            : false,          // Boolean Will prevent use of CSS3 3D Transforms, avoiding graphical glitches.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options']['settings_slideshow_' . $i . '_video'] = [
      '#type' => 'checkbox',
      '#title' => t('Video'),
      '#default_value' => theme_get_setting('settings.slideshow_' . $i . '_video'),
      '#description' => t('Checking this setting will prevent use of CSS3 3D Transforms, avoiding graphical glitches when embedding video.'),
    ];

    // prevText : String Set the text for the "previous" directionNav item.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options']['settings_slideshow_' . $i . '_prevtext'] = [
      '#type' => 'textfield',
      '#title' => t('Previous text'),
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_prevtext') ? theme_get_setting('settings.slideshow_' . $i . '_prevtext') : t('Previous'),
      '#description' => t('Text for the "previous" direction nav item.'),
    ];

    // nextText : String Set the text for the "next" directionNav item.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options']['settings_slideshow_' . $i . '_nexttext'] = [
      '#type' => 'textfield',
      '#title' => t('Next text'),
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_nexttext') ? theme_get_setting('settings.slideshow_' . $i . '_nexttext') : t('Next'),
      '#description' => t('Text for the "next" direction nav item.'),
    ];

    // Slideshow selector         : themename-slideshow-N i.e. $slideshow_class.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options']['settings_slideshow_' . $i . '_slideshow_class'] = [
      '#type' => 'textfield',
      '#title' => t('Slideshow selector'),
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_slideshow_class') ? theme_get_setting('settings.slideshow_' . $i . '_slideshow_class') : '.' . $slideshow_class,
      '#description' => t('Change this if you are using your own markup, e.g. a custom block with image fields.'),
    ];

    // Slide selector         : ".slides > li", // Selector Must match a simple pattern. '{container} > {slide}'.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['advanced_options']['settings_slideshow_' . $i . '_selector'] = [
      '#type' => 'textfield',
      '#title' => t('Slide selector'),
      '#default_value' => NULL !== theme_get_setting('settings.slideshow_' . $i . '_selector') ? theme_get_setting('settings.slideshow_' . $i . '_selector') : '.slides > li',
      '#description' => t('Selector must match the pattern <code>{container} &#62; {slide}</code>. Modify with caution. The generated markup snippet will not reflect changes here, and you will need to account for changes both in markup and CSS. Changing this without editing the markup in your slideshow or CSS will break the slideshow.'),
    ];

    if (theme_get_setting('settings.slideshow_' . $i . '_slideshow_class') !== NULL) {
      $slideshow_class_setting = theme_get_setting('settings.slideshow_' . $i . '_slideshow_class');
      $this_slideshow_class = Html::cleanCssIdentifier($slideshow_class_setting);
    }
    else {
      $this_slideshow_class = $slideshow_class;
    }

    // Class and markup generator TODO: markup generator.
    $form['slideshows']['slideshow_' . $i]['slideshow_options']['wrapper']['slideshow_markup'] = [
      '#type' => 'textarea',
      '#title' => t('Generated markup for this slideshow (with working examples)'),
      '#default_value' =>
      '<div class="flexslider loading ' . ltrim($this_slideshow_class, '.') . '">
  <ul class="slides">
    <li>
      <img src="' . base_path() . $subtheme_path . '/images/slides/test-slide-1.png" alt="Test slide one" />
      <p class="flex-caption">Test slide one</p>
    </li>
    <li>
      <img src="' . base_path() . $subtheme_path . '/images/slides/test-slide-2.png" alt="Test slide two" />
      <p class="flex-caption">Test slide two</p>
    </li>
    <li>
      <img src="' . base_path() . $subtheme_path . '/images/slides/test-slide-3.png" alt="Test slide three" />
      <p class="flex-caption">Test slide three</p>
    </li>
  </ul>
</div>',
      '#disabled' => FALSE,
      '#cols' => 30,
      '#rows' => 18,
      '#description' => t('Markup for this slideshow with initialization class <code>@initialization_class</code>. Use this in blocks, nodes, templates etc (anywhere in the output between the <code>&#60;body&#62;</code> elements). Each image or content must be in an <code>@licode</code>, add or remove as required. Note: this code and initialization class are re-usable, for example you want a slideshow for each section of your site and want to use the same settings - just re-use this snippet for each slideshow.', ['@licode' => '<li></li>', '@initialization_class' => $slideshow_class]),
    ];
  }
}
