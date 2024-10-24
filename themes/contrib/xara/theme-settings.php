<?php
/**
 * @file
 * Custom setting for XARA theme.
 */
use Drupal\Core\Form\FormStateInterface;

function xara_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {
  $image_folder = $GLOBALS['base_url'] . '/' . \Drupal::service('extension.list.theme')->getPath('xara') . '/images/theme-settings/';
  $button = "display: inline-block; background: #0984e3; color: white; margin-bottom: 10px; padding: 5px 10px";
  $xarapro = '<img src="' . $image_folder . 'xarapro.jpg" />';
	$form['#attached']['library'][] = 'xara/theme-settings';
  $form['xara'] = [
    '#type'       => 'vertical_tabs',
    '#title'      => '<h3 class="settings-form-title">' . t('') . '</h3>',
    '#default_tab' => 'general',
  ];
  /**
   * Main Tabs.
   */
  $form['general'] = [
    '#type'  => 'details',
    '#title' => t('General'),
    '#description' => t('<h3>Thank you for using XARA Theme</h3>XARA is a free Drupal 8, 9, 10, 11 theme designed and developed by <a href="https://drupar.com" target="_blank">Drupar.com</a>'),
    '#group' => 'xara',
  ];
  $form['layout'] = [
    '#type'  => 'details',
    '#title' => t('Layout'),
    '#group' => 'xara',
  ];
  $form['slider'] = [
    '#type'  => 'details',
    '#title' => t('Homepage Slider'),
    '#description' => t('<h3>Manage Homepage Slider</h3>'),
    '#group' => 'xara',
  ];
  $form['header'] = [
    '#type'  => 'details',
    '#title' => t('Header'),
    '#group' => 'xara',
  ];
  $form['sidebar'] = [
    '#type'  => 'details',
    '#title' => t('Sidebar'),
    '#group' => 'xara',
  ];
  $form['content'] = [
    '#type'  => 'details',
    '#title' => t('Content'),
    '#group' => 'xara',
  ];
  $form['footer'] = [
    '#type'  => 'details',
    '#title' => t('Footer'),
    '#group' => 'xara',
  ];
  $form['comment'] = [
    '#type'  => 'details',
    '#title' => t('Comment'),
    '#group' => 'xara',
  ];
  $form['typography'] = [
    '#type'  => 'details',
    '#title' => t('Typography'),
    '#group' => 'xara',
  ];
  $form['elements'] = [
    '#type'  => 'details',
    '#title' => t('Elements'),
    '#group' => 'xara',
  ];
  $form['components'] = [
    '#type'  => 'details',
    '#title' => t('Components'),
    '#group' => 'xara',
  ];
  $form['color'] = [
    '#type'  => 'details',
    '#title' => t('Theme Color'),
    '#group' => 'xara',
  ];
  $form['insert_codes'] = [
    '#type'  => 'details',
    '#title' => t('Insert Codes'),
    '#group' => 'xara',
  ];
  $form['support'] = [
    '#type'  => 'details',
    '#title' => t('Support'),
    '#group' => 'xara',
  ];
  $form['upgrade'] = [
    '#type'  => 'details',
    '#title' => t('Upgrade To XaraPro'),
    '#description'  => t('<h3>Upgrade To XaraPro For $29 Only</h3><ul><li>One time payment.</li><li>No re-curring payment</li><li>No renewal fee.</li><li>One year updates.</li><li>One year support</li></ul>'),
    '#group' => 'xara',
  ];

  /*
   * General
   */
  $form['general']['general_info'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Theme Info'),
    '#description' => t('<a href="https://drupar.com/theme/xara" target="_blank">Theme Homepage</a> || <a href="https://demo2.drupar.com/xara/" target="_blank">Theme Demo</a> || <a href="https://drupar.com/doc/xara" target="_blank">Theme Documentation</a> || <a href="https://drupar.com/doc/xara/support" target="_blank">Theme Support</a>'),
  ];
  $form['general']['general_info_upgrade'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Upgrade To XaraPro for $29 only'),
    '#description' => t('<p><a href="https://drupar.com/theme/xarapro" target="_blank">Purchase XaraPro</a> || <a href="https://demo2.drupar.com/xarapro/" target="_blank">XaraPro Demo</a></p>') . $xarapro,
  ];
  /*
   * Layout
   */
  // Layout -> Container width
  $form['layout']['layout_container'] = [
    '#type'        => 'fieldset',
    '#title'         => t('Container width (px)'),
  ];
  $form['layout']['layout_container']['container_width'] = [
    '#type'          => 'number',
    '#default_value' => theme_get_setting('container_width', 'xara'),
    '#description'   => t('Set width of the container in px. Default width is 1170px.'),
  ];
  // Layout -> Header Layout
  $form['layout']['layout_header'] = [
    '#type'        => 'fieldset',
    '#title'         => t('Header Layout'),
  ];
  $form['layout']['layout_header']['header_width'] = [
    '#type'          => 'select',
    '#options' => array(
    	'header_width_contained' => t('contained'),
    	'header_width_full' => t('Full Width'),),
    '#default_value' => theme_get_setting('header_width', 'xara'),
  ];
  // Layout -> Main Layout
  $form['layout']['layout_main'] = [
    '#type'        => 'fieldset',
    '#title'         => t('Main Layout'),
  ];
  $form['layout']['layout_main']['main_width'] = [
    '#type'          => 'select',
    '#options' => array(
    	'main_width_contained' => t('contained'),
    	'main_width_full' => t('Full Width'),),
    '#default_value' => theme_get_setting('main_width', 'xara'),
  ];
  // Layout -> Footer Layout
  $form['layout']['layout_footer'] = [
    '#type'        => 'fieldset',
    '#title'         => t('Footer Layout'),
  ];
  $form['layout']['layout_footer']['footer_width'] = [
    '#type'          => 'select',
    '#options' => array(
    	'footer_width_contained' => t('contained'),
    	'footer_width_full' => t('Full Width'),),
    '#default_value' => theme_get_setting('footer_width', 'xara'),
  ];
  /*
   * Homepage slider
   */
  $form['slider']['slider_enable_option'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Enable Slider'),
  ];

  $form['slider']['slider_enable_option']['slider_show'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show Slider on Homepage'),
    '#default_value' => theme_get_setting('slider_show', 'xara'),
    '#description'   => t("Check this option to show slider on homepage. Uncheck to disable slider."),
  ];
  // Slider -> Slider speed
  $form['slider']['slider_speed_option'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Slider Speed'),
  ];

  $form['slider']['slider_speed_option']['slider_speed'] = [
    '#type'          => 'number',
    '#title'         => t('Interval time between two slides'),
    '#default_value' => theme_get_setting('slider_speed', 'xara'),
    '#description'   => t("Time interval between two slides. Default value is 5000, this means 5 seconds."),
  ];
  /* Slider Image upload */
  $form['slider']['slider_image_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Slider Image'),
  ];
  $form['slider']['slider_image_section']['slider_image'] = [
    '#type'          => 'managed_file',
    '#upload_location' => 'public://',
    '#upload_validators' => array(
      'file_validate_extensions' => array('gif png jpg jpeg svg'),
    ),
    '#title'  => t('<p>Upload Slider Image</p>'),
    '#default_value'  => theme_get_setting('slider_image', 'xara'),
    '#description'   => t('Xara theme has limitation of single image for slider. Separate image for each slide is available in XaraPro. <a href="https://drupar.com/theme/xarapro" target="_blank">Buy XaraPro for $29 only.</a>'),
  ];
  $form['slider']['slider_code'] = [
    '#type'          => 'textarea',
    '#title'         => t('Slider Code'),
    '#default_value' => theme_get_setting('slider_code', 'xara'),
    '#description'   => t('Please refer to this <a href="https://drupar.com/doc/xara/homepage-slider" target="_blank">documentation page</a> for slider code tutorial.'),
  ];
  /*
   * Header
   */
  $form['header']['header_tab'] = [
    '#type'  => 'vertical_tabs',
  ];
  // Header -> Quick Links
  $form['header']['header_links'] = [
    '#type'        => 'details',
    '#title'       => t('Header Links'),
    '#description'   => t('<a href="https://drupar.com/doc/xara/how-manage-website-logo" target="_blank">Change Logo</a> || <a href="https://drupar.com/doc/xara/how-change-favicon-icon" target="_blank">Change Favicon Icon</a> || <a href="https://drupar.com/doc/xara/header-main-menu" target="_blank">Manage Main Menu</a> || <a href="https://drupar.com/doc/xara/sliding-search-form" target="_blank">Sliding Search Form</a>'),
    '#group' => 'header_tab',
  ];
  // Header -> Sticky header.
  $form['header']['sticky_header'] = [
    '#type'        => 'details',
    '#title'       => t('Sticky Header'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/xarapro" target="_blank">Buy XaraPro for $29 only.</a>'),
    '#group' => 'header_tab',
  ];
  // header-> page header
  $form['header']['header_page'] = [
    '#type'  => 'details',
    '#title' => t('Page Header'),
    '#group' => 'header_tab',
  ];
  $form['header']['header_page']['header_page_position_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Content Position'),
    '#description'   => t('<hr /><br />Position of content in <strong>Header Main</strong> region.')
  ];
  $form['header']['header_page']['header_page_position_section']['header_page_content_position'] = [
    '#type'          => 'select',
    '#options' => array(
      'flex-start' => t('Left'),
      'flex-end' => t('Right'),
      'center' => t('center'),
    ),
    '#default_value' => theme_get_setting('header_page_content_position', 'xara'),
    '#description'   => t("Default position is <strong>Center</strong>."),
  ];
  /*
   * Sidebar
   */
  $form['sidebar']['sidebar_tab'] = [
    '#type'  => 'vertical_tabs',
  ];
  // Sidebar -> Frontpage sidebar
  $form['sidebar']['front_sidebars'] = [
    '#type'          => 'details',
    '#title'         => t('Homepage Sidebar'),
    '#group' => 'sidebar_tab',
  ];
  $form['sidebar']['front_sidebars']['front_sidebar_section'] = [
    '#type'        => 'fieldset',
  ];
  $form['sidebar']['front_sidebars']['front_sidebar_section']['front_sidebar'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show Sidebars On Homepage'),
    '#default_value' => theme_get_setting('front_sidebar', 'xara'),
    '#description'   => t('<p>Check this option to enable left and right sidebar on homepage.</p><hr /><br /><strong>Homepage Content Top</strong> and <strong>Homepage Content Bottom</strong> block regions will always be full width.'),
  ];
  // Sidebar -> sidebar width
  $form['sidebar']['sidebar_width'] = [
    '#type'          => 'details',
    '#title'         => t('Sidebar Width'),
    '#group' => 'sidebar_tab',
  ];
  $form['sidebar']['sidebar_width']['sidebar_width_default_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Use Default Sidebars Width'),
    '#attributes' => array('class' => array('set-default-fieldset')),
  ];
  $form['sidebar']['sidebar_width']['sidebar_width_default_section']['sidebar_width_default'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Use theme default sidebar width'),
    '#default_value' => theme_get_setting('sidebar_width_default', 'xara'),
    '#description'   => t('Check this option to use theme default sidebar width. Uncheck this to set custom width below.'),
  ];
  $form['sidebar']['sidebar_width']['sidebar_width_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Sidebars Width'),
  ];
  $form['sidebar']['sidebar_width']['sidebar_width_section']['sidebar_width_left'] = [
    '#type'          => 'number',
    '#title'         => t('Left Sidebar Width (in percentage)'),
    '#default_value' => theme_get_setting('sidebar_width_left', 'xara'),
    '#description'   => t('Default width of left sidebar is 30%<br /><br /><p><hr /></p>'),
  ];
  $form['sidebar']['sidebar_width']['sidebar_width_section']['sidebar_width_right'] = [
    '#type'          => 'number',
    '#title'         => t('Right Sidebar Width (in percentage)'),
    '#default_value' => theme_get_setting('sidebar_width_right', 'xara'),
    '#description'   => t('Default width of right sidebar is 30%'),
  ];
  // Sidebar -> Animated Sidebar
  $form['sidebar']['animated_sidebar'] = [
    '#type'        => 'details',
    '#title'       => t('Animated Sliding Sidebar'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/xarapro" target="_blank">Buy XaraPro for $29 only.</a>'),
    '#group' => 'sidebar_tab',
  ];
  /*
   * Content
   */
  $form['content']['content_tab'] = [
    '#type'  => 'vertical_tabs',
  ];
  // content -> Shortcodes
  $form['content']['shortcodes'] = [
    '#type'          => 'details',
    '#title'         => t('Shortcodes'),
    '#description'   => t('<p>xara theme has many custom shortcodes which you can use for creating contents.</p><p>Please visit this page for list of all available shortcodes and how to use these shortcodes.</p><ul><li><a href="https://drupar.com/node/2017/" target="_blank">XARA Shortcodes</a></li><li><a href="https://drupar.com/node/1211/" target="_blank">The-X Shortcodes</a></li></ul><p><hr /></p><h4>More Shortcodes</h4><p><a href="https://drupar.com/theme/xarapro">XaraPro</a> has more custom shortcodes like Tab, Accordion, icon box, card, Model etc.'),
    '#group' => 'content_tab',
  ];
  // content -> RTL
  $form['content']['content_direction'] = [
    '#type'          => 'details',
    '#title'         => t('Content Direction - RTL'),
    '#group' => 'content_tab',
  ];
  $form['content']['content_direction']['rtl'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable RTL (Experimental)'),
    '#default_value' => theme_get_setting('rtl', 'xara'),
    '#description'   => t('Currently not available.'),
    '#disabled'   => TRUE,
  ];

  // Content -> Animated Content.
  $form['content']['animated_content'] = [
    '#type'        => 'details',
    '#title'       => t('Animated Page Content'),
    '#group' => 'content_tab',
  ];
  $form['content']['animated_content']['animated_content_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Animated Page Content - xaraPro Feature'),
    '#description'   => t('<p>With animated page content shortcodes, you can create contents with animation effects. These contents will appear with some animation effect when it will come in browser view.</p><p>This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/xarapro" target="_blank">Buy XaraPro for $29 only.</a></p>'),
  ];

  // Content-> Submitted Details
  $form['content']['submitted_details'] = [
    '#type'  => 'details',
    '#title' => t('Submitted Details'),
    '#group' => 'content_tab',
  ];
  $form['content']['submitted_details']['node_author_pic_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Author Picture'),
  ];
  $form['content']['submitted_details']['node_author_pic_section']['node_author_pic'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show Node Author Picture in Submitted Details.'),
    '#default_value' => theme_get_setting('node_author_pic', 'xara'),
    '#description'   => t("Check this option to show node author picture in submitted details. Uncheck to hide."),
  ];
  // Show tags in node submitted.
  $form['content']['submitted_details']['node_tags_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Show Node Tags'),
  ];
  $form['content']['submitted_details']['node_tags_section']['node_tags'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show Node Tags in Submitted Details.'),
    '#default_value' => theme_get_setting('node_tags', 'xara'),
    '#description'   => t("Check this option to show node tags (if any) in submitted details. Uncheck to hide."),
  ];
  // Node author picture.

  // Show tags in node submitted.

  /*
   * Footer
   */
  // Footer -> Copyright.
  $form['footer']['copyright'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Copyright Text'),
  ];

  $form['footer']['copyright']['copyright_text'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show website copyright text in footer.'),
    '#default_value' => theme_get_setting('copyright_text', 'xara'),
    '#description'   => t("Check this option to show website copyright text in footer. Uncheck to hide.<br />Read more: <a href='https://drupar.com/doc/xara/copyright-text-footer' target='_blank'>Copyright Text in Footer</a>"),
  ];

  // Footer -> Copyright -> custom copyright text
  $form['footer']['copyright']['copyright_text_custom'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Custom copyright text'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/xarapro" target="_blank">Buy XaraPro for $29 only.</a>'),
  ];
  /**
   * Settings under comment tab.
   */
  // Show user picture in comment.
  $form['comment']['comment_photo'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Comment User Picture'),
  ];

  $form['comment']['comment_photo']['comment_user_pic'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show User Picture in comments'),
    '#default_value' => theme_get_setting('comment_user_pic', 'xara'),
    '#description'   => t("Check this option to show user picture in comment. Uncheck to hide."),
  ];
  // Hightlight Node author comment.
  $form['comment']['comment_author'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Author Comment'),
  ];

  $form['comment']['comment_author']['highlight_author_comment'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Highlight Author Comments'),
    '#default_value' => theme_get_setting('highlight_author_comment', 'xara'),
    '#description'   => t("Check this option to highlight node author comments."),
  ];
  $form['comment']['comment_author']['highlight_author_color'] = [
    '#type'          => 'details',
    '#title'         => t('Highlight Color'),
    '#description'   => t('Color option is available in the premium version of this theme. <a href="https://drupar.com/theme/xarapro" target="_blank">Buy XaraPro for $29 only.</a>'),
    '#open' => TRUE,
  ];

  /*
   * Typography
   */
  $form['typography']['body'] = [
    '#type'  => 'fieldset',
    '#title' => t('Body'),
  ];
  $form['typography']['body']['body_font_size_section'] = [
    '#type'        => 'details',
    '#title'       => t('Font Size'),
    '#open' => TRUE,
    '#description'   => t("Value is in <strong>rem</strong> unit. 1 rem = 16px"),
  ];
  $form['typography']['body']['body_font_size_section']['body_font_size'] = [
    '#type'   => 'number',
    '#min'  => 0.5,
    '#max'  => 3,
    '#step' => 0.1,
    '#title'  => t('Font Size (rem)'),
    '#default_value' => theme_get_setting('body_font_size', 'xara'),
    '#description'   => t("Default size is 1rem"),
  ];

  /*
   * Elements
   */
  $form['elements']['elements_tab'] = [
    '#type'  => 'vertical_tabs',
  ];
  // Elements -> Main menu
  $form['elements']['main_menu'] = [
    '#type'  => 'details',
    '#title' => t('Main Menu'),
    '#group' => 'elements_tab',
  ];
  $form['elements']['main_menu']['main_menu_default_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Use Default'),
    '#attributes' => array('class' => array('set-default-fieldset')),
  ];
  $form['elements']['main_menu']['main_menu_default_section']['main_menu_default'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Use Default Main Menu Settings'),
    '#default_value' => theme_get_setting('main_menu_default', 'xara'),
    '#description'   => t('Check this option to use default main menu settings. Uncheck this to set custom values below.'),
  ];
  $form['elements']['main_menu']['main_menu_top'] = [
    '#type'  => 'details',
    '#title' => t('Main Menu'),
    '#open' => TRUE,
  ];
  $form['elements']['main_menu']['main_menu_top']['main_menu_top_size'] = [
    '#type'   => 'number',
    '#min'  => 0.5,
    '#max'  => 3,
    '#step' => 0.1,
    '#title'  => t('Font Size (rem)'),
    '#default_value' => theme_get_setting('main_menu_top_size', 'xara'),
    '#description'   => t("Default value is <strong>1rem</strong>.<br />1 rem = 16px<br /><p><hr /></p>"),
  ];
  $form['elements']['main_menu']['main_menu_top']['main_menu_top_weight'] = [
    '#type'   => 'select',
    '#title'  => t('Font Weight'),
    '#options' => array(
      '400' => t('400'),
      '700' => t('700'),
    ),
    '#default_value' => theme_get_setting('main_menu_top_weight', 'xara'),
    '#description'   => t("Default value is <strong>700</strong>.<br /><p><hr /></p>"),
  ];
  $form['elements']['main_menu']['main_menu_top']['main_menu_top_transform'] = [
    '#type'          => 'select',
    '#title'  => t('Transform'),
    '#options' => array(
    	'none' => t('None'),
      'capitalize' => t('Capitalize'),
      'uppercase' => t('Uppercase'),
      'lowercase' => t('Lowercase'),
    ),
    '#default_value' => theme_get_setting('main_menu_top_transform', 'xara'),
    '#description'   => t("Default value is <strong>None</strong>."),
  ];
  $form['elements']['main_menu']['main_menu_sub'] = [
    '#type'  => 'details',
    '#title' => t('Main Menu: Dropdowns'),
    '#open' => TRUE,
  ];
  $form['elements']['main_menu']['main_menu_sub']['main_menu_sub_size'] = [
    '#type'   => 'number',
    '#min'  => 0.5,
    '#max'  => 3,
    '#step' => 0.1,
    '#title'  => t('Font Size (rem)'),
    '#default_value' => theme_get_setting('main_menu_sub_size', 'xara'),
    '#description'   => t("Default value is <strong>1rem</strong>.<br />1 rem = 16px<br /><p><hr /></p>"),
  ];
  $form['elements']['main_menu']['main_menu_sub']['main_menu_sub_weight'] = [
    '#type'   => 'select',
    '#title'  => t('Font Weight'),
    '#options' => array(
      '400' => t('400'),
      '700' => t('700'),
    ),
    '#default_value' => theme_get_setting('main_menu_sub_weight', 'xara'),
    '#description'   => t("Default value is <strong>700</strong>.<br /><p><hr /></p>"),
  ];
  $form['elements']['main_menu']['main_menu_sub']['main_menu_sub_transform'] = [
    '#type'          => 'select',
    '#title'  => t('Transform'),
    '#options' => array(
    	'none' => t('None'),
      'capitalize' => t('Capitalize'),
      'uppercase' => t('Uppercase'),
      'lowercase' => t('Lowercase'),
    ),
    '#default_value' => theme_get_setting('main_menu_sub_transform', 'xara'),
    '#description'   => t("Default value is <strong>None</strong>."),
  ];
  $form['elements']['main_menu']['main_menu_color'] = [
    '#type'          => 'details',
    '#title'         => t('Main Menu Color'),
    '#description'   => t('Color option is available in the premium version of this theme. <a href="https://drupar.com/theme/xarapro" target="_blank">Buy XaraPro for $29 only.</a>'),
    '#open' => TRUE,
  ];
  // Elements -> Page Title
  $form['elements']['page_title'] = [
    '#type'  => 'details',
    '#title' => t('Page Title'),
    '#group' => 'elements_tab',
  ];
  $form['elements']['page_title']['page_title_default_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Use Default'),
    '#attributes' => array('class' => array('set-default-fieldset')),
  ];
  $form['elements']['page_title']['page_title_default_section']['page_title_default'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Use Default Page Title Settings'),
    '#default_value' => theme_get_setting('page_title_default', 'xara'),
    '#description'   => t('Check this option to use default values for page title. Uncheck this to set custom values below.'),
  ];
  $form['elements']['page_title']['page_title_size_section'] = [
    '#type'        => 'details',
    '#open' => TRUE,
  ];
  $form['elements']['page_title']['page_title_size_section']['page_title_size'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 5,
    '#step' => 0.1,
    '#title'  => t('Desktop and Laptop (rem)'),
    '#default_value' => theme_get_setting('page_title_size', 'xara'),
    '#description'   => t("Default value is <strong>2.6rem</strong>"),
  ];
  $form['elements']['page_title']['page_title_transform_section'] = [
    '#type'        => 'details',
    '#title'       => t('Text Transform'),
    '#open' => TRUE,
  ];
  $form['elements']['page_title']['page_title_transform_section']['page_title_transform'] = [
    '#type'          => 'select',
    '#options' => array(
    	'none' => t('None'),
      'capitalize' => t('Capitalize'),
      'uppercase' => t('Uppercase'),
      'lowercase' => t('Lowercase'),
    ),
    '#default_value' => theme_get_setting('page_title_transform', 'xara'),
    '#description'   => t("Default value is <strong>None</strong>."),
  ];
  /*
   * Components
   */
  $form['components']['components_tab'] = [
    '#type'  => 'vertical_tabs',
  ];
  // Components -> Google Fonts.
  $form['components']['fonts'] = [
    '#type'          => 'details',
    '#title'         => t('Fonts'),
    '#group' => 'components_tab',
  ];
  $form['components']['fonts']['fonts_section'] = [
    '#type'        => 'fieldset',
  ];
  $form['components']['fonts']['fonts_section']['font_src'] = [
    '#type'          => 'select',
    '#title'         => t('Select Google Fonts Location'),
    '#options' => array(
    	'local' => t('Local Self Hosted'),
      'googlecdn' => t('Google CDN Server')
    ),
    '#default_value' => theme_get_setting('font_src', 'xara'),
    '#description'   => t('xara theme uses following Google fonts: Noto Sans.<br />You can serve these fonts locally or from Google server.'),
  ];
  // Components -> Social
  $form['components']['social'] = [
    '#type'  => 'details',
    '#title' => t('Social'),
    '#group' => 'components_tab',
  ];
  $form['components']['social']['all_icons'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Show Social Icons'),
  ];
  $form['components']['social']['all_icons']['social_icons_show'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show social icons in footer'),
    '#default_value' => theme_get_setting('social_icons_show', 'xara'),
    '#description'   => t("Check this option to show social icons in footer. Uncheck to hide."),
  ];
  $form['components']['social']['social_profile'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Social Profile'),
  ];
  // Facebook.
    $form['components']['social']['social_profile']['facebook'] = [
    '#type'        => 'details',
    '#title'       => t("Facebook"),
  ];

  $form['components']['social']['social_profile']['facebook']['facebook_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Facebook Url'),
    '#description'   => t("Enter yours facebook profile or page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('facebook_url', 'xara'),
  ];

  // Twitter.
  $form['components']['social']['social_profile']['twitter'] = [
    '#type'        => 'details',
    '#title'       => t("Twitter"),
  ];

  $form['components']['social']['social_profile']['twitter']['twitter_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Twitter Url'),
    '#description'   => t("Enter yours twitter page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('twitter_url', 'xara'),
  ];

  // Instagram.
  $form['components']['social']['social_profile']['instagram'] = [
    '#type'        => 'details',
    '#title'       => t("Instagram"),
  ];

  $form['components']['social']['social_profile']['instagram']['instagram_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Instagram Url'),
    '#description'   => t("Enter yours instagram page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('instagram_url', 'xara'),
  ];

  // Linkedin.
  $form['components']['social']['social_profile']['linkedin'] = [
    '#type'        => 'details',
    '#title'       => t("Linkedin"),
  ];

  $form['components']['social']['social_profile']['linkedin']['linkedin_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Linkedin Url'),
    '#description'   => t("Enter yours linkedin page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('linkedin_url', 'xara'),
  ];

  // YouTube.
  $form['components']['social']['social_profile']['youtube'] = [
    '#type'        => 'details',
    '#title'       => t("YouTube"),
  ];

  $form['components']['social']['social_profile']['youtube']['youtube_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('YouTube Url'),
    '#description'   => t("Enter yours youtube.com page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('youtube_url', 'xara'),
  ];

  // YouTube.
  $form['components']['social']['social_profile']['vimeo'] = [
    '#type'        => 'details',
    '#title'       => t("vimeo"),
  ];

  $form['components']['social']['social_profile']['vimeo']['vimeo_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('vimeo Url'),
    '#description'   => t("Enter yours vimeo.com page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('vimeo_url', 'xara'),
  ];

  // telegram.
    $form['components']['social']['social_profile']['telegram'] = [
    '#type'        => 'details',
    '#title'       => t("Telegram"),
  ];

  $form['components']['social']['social_profile']['telegram']['telegram_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Telegram Url'),
    '#description'   => t("Enter yours Telegram profile or page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('telegram_url', 'xara'),
  ];

  // WhatsApp.
    $form['components']['social']['social_profile']['whatsapp'] = [
    '#type'        => 'details',
    '#title'       => t("WhatsApp"),
  ];

  $form['components']['social']['social_profile']['whatsapp']['whatsapp_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('WhatsApp Url'),
    '#description'   => t("Enter yours whatsapp message url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('whatsapp_url', 'xara'),
  ];

  // Github.
    $form['components']['social']['social_profile']['github'] = [
    '#type'        => 'details',
    '#title'       => t("GitHub"),
  ];

  $form['components']['social']['social_profile']['github']['github_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('GitHub Url'),
    '#description'   => t("Enter yours github page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('github_url', 'xara'),
  ];

  // Social -> vk.com url.
  $form['components']['social']['social_profile']['vk'] = [
    '#type'        => 'details',
    '#title'       => t("vk.com"),
  ];
  $form['components']['social']['social_profile']['vk']['vk_url'] = [
      '#type'          => 'textfield',
      '#title'         => t('vk.com'),
      '#description'   => t("Enter yours vk.com page url. Leave the url field blank to hide this icon."),
      '#default_value' => theme_get_setting('vk_url', 'xara'),
  ];
  $form['components']['node_share'] = [
    '#type'        => 'details',
    '#title'       => t('Share Page'),
    '#description'   => t('<h4>Share Page On Social networking websites</h4><p>This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/xarapro" target="_blank">Buy XaraPro for $29 only.</a></p>'),
    '#group' => 'components_tab',
  ];
  // Components -> Font icons
  $form['components']['font_icons'] = [
    '#type'  => 'details',
    '#title' => t('Font Icons'),
    '#group' => 'components_tab',
  ];
  $form['components']['font_icons']['fontawesome4'] = [
    '#type'          => 'fieldset',
    '#title'         => t('FontAwesome 4'),
  ];
  $form['components']['font_icons']['fontawesome4']['fontawesome_four'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable FontAwesome 4 Font Icons'),
    '#default_value' => theme_get_setting('fontawesome_four', 'xara'),
    '#description'   => t('Check this option to enable fontawesome version 4 font icons.'),
  ];
  $form['components']['font_icons']['fontawesome5'] = [
    '#type'          => 'fieldset',
    '#title'         => t('FontAwesome 5'),
  ];
  $form['components']['font_icons']['fontawesome5']['fontawesome_five'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable FontAwesome 5 Font Icons'),
    '#default_value' => theme_get_setting('fontawesome_five', 'xara'),
    '#description'   => t('Check this option to enable fontawesome version 5 font icons.'),
  ];
  $form['components']['font_icons']['fontawesome6'] = [
    '#type'          => 'fieldset',
    '#title'         => t('FontAwesome 6'),
  ];
  $form['components']['font_icons']['fontawesome6']['fontawesome_six'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable FontAwesome 6 Font Icons'),
    '#default_value' => theme_get_setting('fontawesome_six', 'xara'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/xarapro" target="_blank">Buy XaraPro for $29 only.</a>'),
    '#disabled'   => TRUE,
  ];
	$form['components']['font_icons']['bootstrap_icons'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Bootstrap Font Icons'),
  ];
  $form['components']['font_icons']['bootstrap_icons']['bootstrapicons'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable Bootstrap Icons'),
    '#default_value' => theme_get_setting('bootstrapicons', 'xara'),
    '#description'   => t('Check this option to enable Bootstrap Font Icons. Read more about <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a>'),
  ];
  $form['components']['font_icons']['materialicons'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Google Material Font Icons'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/xarapro" target="_blank">Purchase XaraPro for $29 only.</a>'),
  ];
  // Components -> Page loader.
  $form['components']['preloader'] = [
    '#type'        => 'details',
    '#title'       => t('Pre Page Loader'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/xarapro" target="_blank">Buy XaraPro for $29 only.</a>'),
    '#group' => 'components_tab',
  ];
  // Components -> Cookie message.
  $form['components']['cookie'] = [
    '#type'        => 'details',
    '#title'       => t('Cookie Consent message'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/xarapro" target="_blank">Buy XaraPro for $29 only.</a>'),
    '#group' => 'components_tab',
  ];

  $form['components']['cookie']['cookie_message'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Show Cookie Consent Message'),
    '#description'   => t('Make your website EU Cookie Law Compliant. According to EU cookies law, websites need to get consent from visitors to store or retrieve cookies.'),
  ];
  // Components -> Scroll to top.
  $form['components']['scrolltotop'] = [
    '#type'  => 'details',
    '#title' => t('Scroll To Top'),
    '#group' => 'components_tab',
  ];
  $form['components']['scrolltotop']['scrolltotop_enable'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Enable Scroll To Top'),
  ];

  $form['components']['scrolltotop']['scrolltotop_enable']['scrolltotop_on'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable scroll to top feature.'),
    '#default_value' => theme_get_setting('scrolltotop_on', 'xara'),
    '#description'   => t("Check this option to enable scroll to top feature. Uncheck to disable this fearure and hide scroll to top icon."),
  ];

  /**
   * Color Options
   */
  $form['color']['theme_color'] = [
    '#type'        => 'details',
    '#title'       => t('Theme Color'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/xarapro" target="_blank">Buy XaraPro for $29 only.</a>'),
    '#open' => TRUE,
  ];
  /**
   * Insert Codes
   */
  $form['insert_codes']['insert_codes_tab'] = [
    '#type'  => 'vertical_tabs',
  ];
  // Insert Codes -> Head
  $form['insert_codes']['head'] = [
    '#type'        => 'details',
    '#title'       => t('Head'),
    '#description' => t('<h3>Insert Codes Before &lt;/HEAD&gt;</h3><p><hr /></p>This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/xarapro" target="_blank">Buy XaraPro for $29 only.</a>'),
    '#group' => 'insert_codes_tab',
  ];
  // Insert Codes -> Body
  $form['insert_codes']['body'] = [
    '#type'        => 'details',
    '#title'       => t('Body'),
    '#group' => 'insert_codes_tab',
  ];
  // Insert Codes -> CSS
  $form['insert_codes']['css'] = [
    '#type'        => 'details',
    '#title'       => t('CSS Codes'),
    '#group'       => 'insert_codes_tab',
  ];
  // Insert Codes -> Body -> Body start codes
  $form['insert_codes']['body']['insert_body_start_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Insert code after &lt;BODY&gt; tag'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/xarapro" target="_blank">Buy XaraPro for $29 only.</a>'),
  ];
  // Insert Codes -> Body -> Body end codes
  $form['insert_codes']['body']['insert_body_end_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Insert code before &lt;/BODY&gt; tag'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/xarapro" target="_blank">Buy XaraPro for $29 only.</a>'),
  ];
  // Insert Codes -> css
  $form['insert_codes']['css']['css_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Custom Styling'),
  ];

  $form['insert_codes']['css']['css_section']['styling'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable Additional CSS'),
    '#default_value' => theme_get_setting('styling', 'xara'),
    '#description'   => t("Check this option to enable custom styling. Uncheck to disable this feature.<br />Please refer to this tutorial page. <a href='https://drupar.com/doc/xara/custom-css' target='_blank'>How To Use Custom Styling</a>"),
  ];

  $form['insert_codes']['css']['css_section']['styling_code'] = [
    '#type'          => 'textarea',
    '#rows'          => 20,
    '#title'         => t('Custom CSS Codes'),
    '#default_value' => theme_get_setting('styling_code', 'xara'),
    '#description'   => t('Please enter your custom css codes in this text box. You can use it to customize the appearance of your site.<br />Please refer to this tutorial for detail: <a href="https://drupar.com/doc/xara/custom-css" target="_blank">Custom CSS</a>'),
  ];
  // Settings under support tab.
  $form['support']['info'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Documentation'),
    '#description' => t('<h4>Xara Documentation</h4>
    <p>Please check our documentation for detailed information on how to use xara theme.<br /><a href="https://drupar.com/doc/xara" target="_blank">Xara Documentation</a>.</p>
    <h4>The-X Documentation</h4>
    <p>xara theme uses The-X theme as the base theme. So, many things are covered in <a href="https://drupar.com/doc/thex" target="_blank">The X Documentation</a>.</p>
    <hr />
    <h4>Create Issue</h4>
    <p>If you need support that is beyond our theme documentation, please <a href="https://www.drupal.org/project/issues/xara?status=All&categories=All" target="_blank">Create an issue</a> at project page.</p>
    <hr />
    <h4>Contact Us</h4>
    <p>If you need some specific customizations in this theme or need custom Drupal theme development, please contact us<br><a href="https://drupar.com/contact" target="_blank">Drupar.com/contact</a></p>'),
  ];

  // Settings under upgrade tab.
  $form['upgrade']['info'] = [
    '#type'        => 'fieldset',
    '#title'       => t('<p><a href="https://demo2.drupar.com/xarapro/" target="_blank">XaraPro Demo</a> | <a href="https://drupar.com/theme/xarapro" target="_blank">Purchase XaraPro for $29 only</a></p>'),
    '#description' => t("$xarapro"),
  ];
// End form.
}
