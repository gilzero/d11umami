<?php
/**
 * @file
 * Custom setting for edux theme.
 */
use Drupal\Core\Form\FormStateInterface;

function edux_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {
  $image_folder = $GLOBALS['base_url'] . '/' . \Drupal::service('extension.list.theme')->getPath('edux') . '/images/theme-settings/';
  $button = "display: inline-block; background: #0984e3; color: white; margin-bottom: 10px; padding: 5px 10px";
  $eduxpro = '<img src="' . $image_folder . 'eduxpro.png" />';
	$form['#attached']['library'][] = 'edux/theme-settings';
  $form['edux'] = [
    '#type'       => 'vertical_tabs',
    '#title'      => '<h3 class="settings-form-title"></h3>',
    '#default_tab' => 'general',
  ];
  /**
   * Main Tabs.
   */
  $form['general'] = [
    '#type'  => 'details',
    '#title' => t('General'),
    '#description' => t('<h3>Thank you for using EduX Theme</h3><strong>Edu X</strong> is a free Drupal 8, 9, 10, 11 theme designed and developed by <a href="https://drupar.com" target="_blank">Drupar.com</a>'),
    '#group' => 'edux',
  ];
  $form['layout'] = [
    '#type'  => 'details',
    '#title' => t('Layout'),
    '#group' => 'edux',
  ];
  $form['slider'] = [
    '#type'  => 'details',
    '#title' => t('Homepage Slider'),
    '#description' => t('<h3>Manage Homepage Slider</h3>'),
    '#group' => 'edux',
  ];
  $form['header'] = [
    '#type'  => 'details',
    '#title' => t('Header'),
    '#group' => 'edux',
  ];
  $form['sidebar'] = [
    '#type'  => 'details',
    '#title' => t('Sidebar'),
    '#group' => 'edux',
  ];
  $form['content'] = [
    '#type'  => 'details',
    '#title' => t('Content'),
    '#group' => 'edux',
  ];
  $form['footer'] = [
    '#type'  => 'details',
    '#title' => t('Footer'),
    '#group' => 'edux',
  ];
  $form['comment'] = [
    '#type'  => 'details',
    '#title' => t('Comment'),
    '#group' => 'edux',
  ];
  $form['typography'] = [
    '#type'  => 'details',
    '#title' => t('Typography'),
    '#group' => 'edux',
  ];
  $form['elements'] = [
    '#type'  => 'details',
    '#title' => t('Elements'),
    '#group' => 'edux',
  ];
  $form['components'] = [
    '#type'  => 'details',
    '#title' => t('Components'),
    '#group' => 'edux',
  ];
  $form['color'] = [
    '#type'  => 'details',
    '#title' => t('Theme Color'),
    '#group' => 'edux',
  ];
  $form['insert_codes'] = [
    '#type'  => 'details',
    '#title' => t('Insert Codes'),
    '#group' => 'edux',
  ];
  $form['support'] = [
    '#type'  => 'details',
    '#title' => t('Support'),
    '#group' => 'edux',
  ];
  $form['upgrade'] = [
    '#type'  => 'details',
    '#title' => t('Upgrade To Edu-X-Pro'),
    '#description'  => t('<h3>Upgrade To Edu X Pro For $29 Only</h3><ul><li>One time payment.</li><li>No re-curring payment</li><li>No renewal fee.</li><li>Free life time updates.</li><li>One year support</li></ul>'),
    '#group' => 'edux',
  ];

  /*
   * General
   */
  $form['general']['general_info'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Theme Info'),
    '#description' => t('<a href="https://drupar.com/theme/edux" target="_blank">Theme Homepage</a> || <a href="https://demo2.drupar.com/edux/" target="_blank">Theme Demo</a> || <a href="https://drupar.com/doc/edux" target="_blank">Theme Documentation</a> || <a href="https://drupar.com/doc/edux/support" target="_blank">Theme Support</a>'),
  ];
  $form['general']['general_info_upgrade'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Upgrade To Edu-X-Pro for $29 only'),
    '#description' => t('<p><a href="https://drupar.com/theme/eduxpro" target="_blank">Purchase Edu-X-Pro</a> || <a href="https://demo2.drupar.com/eduxpro/" target="_blank">Edu-X-Pro Demo</a></p>') . $eduxpro,
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
    '#default_value' => theme_get_setting('container_width', 'edux'),
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
    '#default_value' => theme_get_setting('header_width', 'edux'),
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
    '#default_value' => theme_get_setting('main_width', 'edux'),
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
    '#default_value' => theme_get_setting('footer_width', 'edux'),
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
    '#default_value' => theme_get_setting('slider_show', 'edux'),
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
    '#default_value' => theme_get_setting('slider_speed', 'edux'),
    '#description'   => t("Time interval between two slides. Default value is 5000, this means 5 seconds."),
  ];
  /* Slider Image upload */
  $form['slider']['slider_image_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Slider Image'),
  ];
  $form['slider']['slider_image_section']['slider_image_bg'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Slider Image Blob Background'),
    '#default_value' => theme_get_setting('slider_image_bg', 'edux'),
    '#description'   => t("Check this option to show the animated blob backgroung behind the slider image. Uncheck to hide."),
  ];
  $form['slider']['slider_image_section']['slider_image'] = [
    '#type'          => 'managed_file',
    '#upload_location' => 'public://',
    '#upload_validators' => array(
      'file_validate_extensions' => array('gif png jpg jpeg svg'),
    ),
    '#title'  => t('<p>Upload Slider Image</p>'),
    '#default_value'  => theme_get_setting('slider_image', 'edux'),
    '#description'   => t('Edux theme has limitation of single image for slider. Separate image for each slide is available in EduxPro. <a href="https://drupar.com/theme/eduxpro" target="_blank">Buy Edu-X-Pro for $29 only.</a>'),
  ];
  $form['slider']['slider_code'] = [
    '#type'          => 'textarea',
    '#title'         => t('Slider Code'),
    '#default_value' => theme_get_setting('slider_code', 'edux'),
    '#description'   => t('Please refer to this <a href="https://drupar.com/doc/edux/homepage-slider" target="_blank">documentation page</a> for slider code tutorial.'),
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
    '#group' => 'header_tab',
  ];
  $form['header']['header_links']['header_links_section'] = [
    '#type'        => 'fieldset',
    '#description'   => t('<a href="https://drupar.com/doc/edux/how-manage-website-logo" target="_blank">Change Logo</a> || <a href="https://drupar.com/doc/edux/how-change-favicon-icon" target="_blank">Change Favicon Icon</a> || <a href="https://drupar.com/doc/edux/header-main-menu" target="_blank">Manage Main Menu</a> || <a href="https://drupar.com/doc/edux/sliding-search-form" target="_blank">Sliding Search Form</a>'),
  ];
  // Header -> Login Links
  $form['header']['header_login'] = [
    '#type'        => 'details',
    '#title'       => t('Header Login Links'),
    '#group' => 'header_tab',
  ];
  $form['header']['header_login']['header_login_links'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show login / logout links in header top region.'),
    '#default_value' => theme_get_setting('header_login_links', 'edux'),
    '#description'   => t('Check this option to show login links in header top region.<br />Guest will get links to <strong>login</strong> and <strong>register</strong> while authentic users will get link for <strong>my account</strong> and <strong>logout</strong>.'),
  ];
  // Header -> Sticky header.
  $form['header']['sticky_header'] = [
    '#type'        => 'details',
    '#title'       => t('Sticky Header'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/eduxpro" target="_blank">Buy Edu-X-Pro for $29 only.</a>'),
    '#group' => 'header_tab',
  ];
  // Header -> Header Presets
  $form['header']['header_presets'] = [
    '#type'        => 'details',
    '#title'       => t('Header Presets / Styles'),
    '#description'   => t('coming soon..'),
    '#group' => 'header_tab',
  ];
  $form['header']['header_presets']['header_style'] = [
    '#type'        => 'radios',
    '#title'       => t('Select A Style'),
    '#options' => array(
    	'header_style_one' => t('Classic'),
      'header_style_two' => t('Inverted'),
      'header_style_three' => t('Centerted'),
      'header_style_four' => t('Spaced'),
    ),
    '#disabled'   => TRUE,
  ];
  // header -> Header main
  $form['header']['header_main'] = [
    '#type'  => 'details',
    '#title' => t('Header Main'),
    '#group' => 'header_tab',
  ];
  $form['header']['header_main']['header_main_default_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Use Default Header Padding'),
    '#attributes' => array('class' => array('set-default-fieldset')),
  ];
  $form['header']['header_main']['header_main_default_section']['header_main_default'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Use theme default header padding'),
    '#default_value' => theme_get_setting('header_main_default', 'edux'),
    '#description'   => t('Check this option to use theme default value of header padding. Uncheck this to set custom value below.'),
  ];
  $form['header']['header_main']['header_main_info_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Header Main Region'),
    '#description'   => t('This region contains logo and main menu.'),
    '#attributes' => array('class' => array('info-fieldset')),
  ];
  $form['header']['header_main']['header_main_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Header Padding'),
    '#description'   => t('<hr /><br />This header region contains <strong>logo</strong> and <strong>main menu</strong>.')
  ];
  $form['header']['header_main']['header_main_section']['header_main_padding_top'] = [
    '#type'   => 'number',
    '#min'  => 0,
    '#max'  => 10,
    '#step' => 0.1,
    '#title'  => t('Padding Top (rem)'),
    '#default_value' => theme_get_setting('header_main_padding_top', 'edux'),
    '#description'   => t("Default padding top is <strong>1rem</strong> which is equivalent to 16px.<br /><br /><p><hr /></p><br />"),
  ];
  $form['header']['header_main']['header_main_section']['header_main_padding_bottom'] = [
    '#type'   => 'number',
    '#min'  => 0,
    '#max'  => 10,
    '#step' => 0.1,
    '#title'  => t('Padding Bottom (rem)'),
    '#default_value' => theme_get_setting('header_main_padding_bottom', 'edux'),
    '#description'   => t("Default padding bottom is <strong>1rem</strong> which is equivalent to 16px."),
  ];
  // header-> page header
  $form['header']['header_page'] = [
    '#type'  => 'details',
    '#title' => t('Page Header'),
    '#group' => 'header_tab',
  ];
  $form['header']['header_page']['header_page_default_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Use Default Settings'),
    '#attributes' => array('class' => array('set-default-fieldset')),
  ];
  $form['header']['header_page']['header_page_default_section']['header_page_default'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Use theme default page header settings'),
    '#default_value' => theme_get_setting('header_page_default', 'edux'),
    '#description'   => t('Check this option to use theme default value of sidebar width. Uncheck this to set custom value below.'),
  ];
  $form['header']['header_page']['header_page_info_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Page Header Region'),
    '#description'   => t('This region contains page tile and breadcrumb navigation.'),
    '#attributes' => array('class' => array('info-fieldset')),
  ];
  $form['header']['header_page']['header_page_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Page Header Padding'),
  ];
  $form['header']['header_page']['header_page_section']['header_page_padding_top'] = [
    '#type'   => 'number',
    '#min'  => 0.1,
    '#max'  => 10,
    '#step' => 0.1,
    '#title'  => t('Padding Top (rem)'),
    '#default_value' => theme_get_setting('header_page_padding_top', 'edux'),
    '#description'   => t("Default padding top is <strong>5rem</strong><br /><br /><p><hr /></p>"),
  ];
  $form['header']['header_page']['header_page_section']['header_page_padding_bottom'] = [
    '#type'   => 'number',
    '#min'  => 0.1,
    '#max'  => 10,
    '#step' => 0.1,
    '#title'  => t('Padding Bottom (rem)'),
    '#default_value' => theme_get_setting('header_page_padding_bottom', 'edux'),
    '#description'   => t("Default padding bottom is <strong>5rem</strong>."),
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
    '#default_value' => theme_get_setting('header_page_content_position', 'edux'),
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
    '#default_value' => theme_get_setting('front_sidebar', 'edux'),
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
    '#default_value' => theme_get_setting('sidebar_width_default', 'edux'),
    '#description'   => t('Check this option to use theme default sidebar width. Uncheck this to set custom width below.'),
  ];
  $form['sidebar']['sidebar_width']['sidebar_width_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Sidebars Width'),
  ];
  $form['sidebar']['sidebar_width']['sidebar_width_section']['sidebar_width_left'] = [
    '#type'          => 'number',
    '#title'         => t('Left Sidebar Width (in percentage)'),
    '#default_value' => theme_get_setting('sidebar_width_left', 'edux'),
    '#description'   => t('Default width of left sidebar is 30%<br /><br /><p><hr /></p>'),
  ];
  $form['sidebar']['sidebar_width']['sidebar_width_section']['sidebar_width_right'] = [
    '#type'          => 'number',
    '#title'         => t('Right Sidebar Width (in percentage)'),
    '#default_value' => theme_get_setting('sidebar_width_right', 'edux'),
    '#description'   => t('Default width of right sidebar is 30%'),
  ];
  // Sidebar -> Animated Sidebar
  $form['sidebar']['animated_sidebar'] = [
    '#type'        => 'details',
    '#title'       => t('Animated Sliding Sidebar'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/eduxpro" target="_blank">Buy Edu-X-Pro for $29 only.</a>'),
    '#group' => 'sidebar_tab',
  ];
  // Sidebar -> Sidebar Block
  $form['sidebar']['sidebar_block'] = [
    '#type'          => 'details',
    '#title'         => t('Sidebar Blocks'),
    '#group' => 'sidebar_tab',
  ];
  $form['sidebar']['sidebar_block']['sidebar_block_default_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Use Default Sidebars Block Settings'),
    '#attributes' => array('class' => array('set-default-fieldset')),
  ];
  $form['sidebar']['sidebar_block']['sidebar_block_default_section']['sidebar_block_default'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Use theme default sidebar block settings.'),
    '#default_value' => theme_get_setting('sidebar_block_default', 'edux'),
    '#description'   => t('Check this option to use theme default value of sidebar block. Uncheck this to set custom value below.'),
  ];
  $form['sidebar']['sidebar_block']['sidebar_block_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Sidebar Block'),
  ];
  $form['sidebar']['sidebar_block']['sidebar_block_section']['sidebar_padding'] = [
    '#type'   => 'number',
    '#min'  => 0,
    '#max'  => 50,
    '#step' => 1,
    '#title'  => t('Sidebar Block Padding (px)'),
    '#default_value' => theme_get_setting('sidebar_padding', 'edux'),
    '#description'   => t("Default is 20px.<br /><br /><p><hr /></p>"),
  ];
  $form['sidebar']['sidebar_block']['sidebar_block_section']['sidebar_radius'] = [
    '#type'   => 'number',
    '#min'  => 0,
    '#max'  => 50,
    '#step' => 1,
    '#title'  => t('Sidebar Block Border Radius (px)'),
    '#default_value' => theme_get_setting('sidebar_radius', 'edux'),
    '#description'   => t("Default is 6px.<br /><br /><p><hr /></p>"),
  ];
  $form['sidebar']['sidebar_block']['sidebar_block_section']['sidebar_margin'] = [
    '#type'   => 'number',
    '#min'  => 0,
    '#max'  => 50,
    '#step' => 1,
    '#title'  => t('Sidebar Block Margin Bottom (rem)'),
    '#default_value' => theme_get_setting('sidebar_margin', 'edux'),
    '#description'   => t("Default value is 2rem which is equivalent to 32px.<br />1rem = 16px"),
  ];
  $form['sidebar']['sidebar_block']['sidebar_title_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Sidebar Block Title'),
  ];
  $form['sidebar']['sidebar_block']['sidebar_title_section']['sidebar_title_font_size'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 50,
    '#step' => 0.1,
    '#title'  => t('Font Size (rem)'),
    '#default_value' => theme_get_setting('sidebar_title_font_size', 'edux'),
    '#description'   => t("Default value is 2.2rem<br />1rem = 16px<br /><br /><p><hr /></p>"),
  ];
  $form['sidebar']['sidebar_block']['sidebar_title_section']['sidebar_title_transform'] = [
    '#type'          => 'select',
    '#title'  => t('Transform'),
    '#options' => array(
    	'none' => t('None'),
      'capitalize' => t('Capitalize'),
      'uppercase' => t('Uppercase'),
      'lowercase' => t('Lowercase'),
    ),
    '#default_value' => theme_get_setting('sidebar_title_transform', 'edux'),
    '#description'   => t("Default value is <strong>None</strong>."),
  ];
  $form['sidebar']['sidebar_block']['sidebar_block_color'] = [
    '#type'          => 'details',
    '#title'         => t('Sidebar Block Background Color'),
    '#description'   => t('Color option is available in the premium version of this theme. <a href="https://drupar.com/theme/eduxpro" target="_blank">Buy Edu-X-Pro for $29 only.</a>'),
    '#open' => TRUE,
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
    '#description'   => t('<p>EduX theme has many custom shortcodes which you can use for creating contents.</p><p>Please visit this page for list of all available shortcodes and how to use these shortcodes.</p><ul><li><a href="https://drupar.com/node/1283/" target="_blank">Edu-X Shortcodes</a></li><li><a href="https://drupar.com/node/1211/" target="_blank">The-X Shortcodes</a></li></ul><p><hr /></p><h4>More Shortcodes</h4><p><a href="https://drupar.com/theme/eduxpro">Edu-X-Pro</a> has more custom shortcodes like Tab, Accordion, icon box, card, Model etc.'),
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
    '#default_value' => theme_get_setting('rtl', 'edux'),
    '#description'   => t('Currently not available.'),
    '#disabled'   => TRUE,
    //'#description'   => t('edux theme is Right-to-left (RTL) languages compatible. Check this option to enable RTL. This feature is currently under testing phase. So, this may not work perfectly.'),
  ];

  // Content -> Animated Content.
  $form['content']['animated_content'] = [
    '#type'        => 'details',
    '#title'       => t('Animated Page Content'),
    '#group' => 'content_tab',
  ];
  $form['content']['animated_content']['animated_content_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Animated Page Content - Edu-X-Pro Feature'),
    '#description'   => t('<p>With animated page content shortcodes, you can create contents with animation effects. These contents will appear with some animation effect when it will come in browser view.</p><p>This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/eduxpro" target="_blank">Buy Edu-X-Pro for $29 only.</a></p>'),
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
    '#default_value' => theme_get_setting('node_author_pic', 'edux'),
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
    '#default_value' => theme_get_setting('node_tags', 'edux'),
    '#description'   => t("Check this option to show node tags (if any) in submitted details. Uncheck to hide."),
  ];
  // Node author picture.

  // Show tags in node submitted.

  /*
   * Footer
   */
  $form['footer']['footer_tab'] = [
    '#type'  => 'vertical_tabs',
  ];
  // Footer -> Footer Presets
  $form['footer']['footer_presets'] = [
    '#type'        => 'details',
    '#title'       => t('Footer Presets / Styles'),
    '#description'   => t('coming soon..'),
    '#group' => 'footer_tab',
  ];
  $form['footer']['footer_presets']['footer_style'] = [
    '#type'        => 'radios',
    '#title'       => t('Select A Style'),
    '#options' => array(
    	'footer_style_one' => t('Classic'),
      'footer_style_two' => t('Inverted'),
      'footer_style_three' => t('Centerted'),
      'footer_style_four' => t('Spaced'),
    ),
    '#disabled'   => TRUE
  ];
  // Footer -> Copyright.
  $form['footer']['copyright'] = [
    '#type'        => 'details',
    '#title'       => t('Copyright Text'),
    '#group' => 'footer_tab',
  ];

  $form['footer']['copyright']['copyright_text'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show website copyright text in footer.'),
    '#default_value' => theme_get_setting('copyright_text', 'edux'),
    '#description'   => t("Check this option to show website copyright text in footer. Uncheck to hide.<br />Read more: <a href='https://drupar.com/doc/edux/copyright-text-footer' target='_blank'>Copyright Text in Footer</a>"),
  ];

  // Footer -> Copyright -> custom copyright text
  $form['footer']['copyright']['copyright_text_custom'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Custom copyright text'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/eduxpro" target="_blank">Buy Edu-X-Pro for $29 only.</a>'),
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
    '#default_value' => theme_get_setting('comment_user_pic', 'edux'),
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
    '#default_value' => theme_get_setting('highlight_author_comment', 'edux'),
    '#description'   => t("Check this option to highlight node author comments."),
  ];
  $form['comment']['comment_author']['highlight_author_color'] = [
    '#type'          => 'details',
    '#title'         => t('Highlight Color'),
    '#description'   => t('Color option is available in the premium version of this theme. <a href="https://drupar.com/theme/eduxpro" target="_blank">Buy Edu-X-Pro for $29 only.</a>'),
    '#open' => TRUE,
  ];

  /*
   * Typography
   */
  $form['typography']['typography_tab'] = [
    '#type'  => 'vertical_tabs',
  ];
  // Typography -> Body
  $form['typography']['body'] = [
    '#type'  => 'details',
    '#title' => t('Body'),
    '#group' => 'typography_tab',
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
    '#default_value' => theme_get_setting('body_font_size', 'edux'),
    '#description'   => t("Default size is 1rem which is equivalent to 16px."),
  ];
  $form['typography']['body']['body_line_height_section'] = [
    '#type'        => 'details',
    '#title'       => t('Line Height'),
    '#open' => TRUE,
  ];
  $form['typography']['body']['body_line_height_section']['body_line_height'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 3,
    '#step' => 0.1,
    '#default_value' => theme_get_setting('body_line_height', 'edux'),
    '#description'   => t("Default value is 1.7"),
  ];
  // Typography -> Paragraph
  $form['typography']['paragraph'] = [
    '#type'  => 'details',
    '#title' => t('Paragraph'),
    '#group' => 'typography_tab',
  ];
  $form['typography']['paragraph']['paragraph_section'] = [
    '#type'        => 'details',
    '#title'       => t('Paragraph Margin Bottom'),
    '#open' => TRUE,
    '#description'   => t("Value is in <strong>rem</strong> unit. 1 rem = 16px"),
  ];
  $form['typography']['paragraph']['paragraph_section']['paragraph_bottom'] = [
    '#type'   => 'number',
    '#min'  => 0,
    '#max'  => 3,
    '#step' => 0.1,
    '#default_value' => theme_get_setting('paragraph_bottom', 'edux'),
    '#description'   => t("Default size is <strong>1.2rem</strong>."),
  ];
  // Typography -> Headings
  $form['typography']['headings'] = [
    '#type'  => 'details',
    '#title' => t('Headings'),
    '#group' => 'typography_tab',
  ];
  $form['typography']['headings']['headings_default_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Use Default Heading Values'),
    '#attributes' => array('class' => array('set-default-fieldset')),
  ];
  $form['typography']['headings']['headings_default_section']['headings_default'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Use theme default heading values.'),
    '#default_value' => theme_get_setting('headings_default', 'edux'),
    '#description'   => t('Check this option to use theme default value for headings. Uncheck this to set custom value below.'),
  ];
  $form['typography']['headings']['headings_section_info'] = [
    '#type'  => 'fieldset',
    '#title' => t('Please Note'),
    '#description'   => t('<p>Below settings for heading will only apply to large screens (laptop and desktop).</p><p>If you want to modify headings for small devices (mobile and tablet), please use <strong>Custom Styling</strong> section.</p>'),
    '#attributes' => array('class' => array('info-fieldset')),
  ];
  $form['typography']['headings']['h1'] = [
    '#type'        => 'details',
    '#title'       => t('Heading 1 (H1)'),
  ];
  $form['typography']['headings']['h1']['h1_size'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 5,
    '#step' => 0.1,
    '#title'  => t('Font Size (rem)'),
    '#default_value' => theme_get_setting('h1_size', 'edux'),
    '#description'   => t("Value is in <strong>rem</strong> unit. 1 rem = 16px<br />Default size is 2.2rem.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h1']['h1_weight'] = [
    '#type'   => 'select',
    '#title'  => t('Font Weight'),
    '#options' => array(
      '400' => t('400'),
      '700' => t('700'),
    ),
    '#default_value' => theme_get_setting('h1_weight', 'edux'),
    '#description'   => t("Default value is <strong>700</strong>.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h1']['h1_transform'] = [
    '#type'          => 'select',
    '#title'  => t('Transform'),
    '#options' => array(
    	'none' => t('None'),
      'capitalize' => t('Capitalize'),
      'uppercase' => t('Uppercase'),
      'lowercase' => t('Lowercase'),
    ),
    '#default_value' => theme_get_setting('h1_transform', 'edux'),
    '#description'   => t("Default value is <strong>None</strong>.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h1']['h1_height'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 3,
    '#step' => 0.1,
    '#title'  => t('Line Height'),
    '#default_value' => theme_get_setting('h1_height', 'edux'),
    '#description'   => t("Default size is 1.7"),
  ];
  $form['typography']['headings']['h2'] = [
    '#type'        => 'details',
    '#title'       => t('Heading 2 (H2)'),
  ];
  $form['typography']['headings']['h2']['h2_size'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 5,
    '#step' => 0.1,
    '#title'  => t('Font Size (rem)'),
    '#default_value' => theme_get_setting('h2_size', 'edux'),
    '#description'   => t("Value is in <strong>rem</strong> unit. 1 rem = 16px<br />Default size is 1.9rem.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h2']['h2_weight'] = [
    '#type'   => 'select',
    '#title'  => t('Font Weight'),
    '#options' => array(
      '400' => t('400'),
      '700' => t('700'),
    ),
    '#default_value' => theme_get_setting('h2_weight', 'edux'),
    '#description'   => t("Default value is <strong>700</strong>.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h2']['h2_transform'] = [
    '#type'          => 'select',
    '#title'  => t('Transform'),
    '#options' => array(
    	'none' => t('None'),
      'capitalize' => t('Capitalize'),
      'uppercase' => t('Uppercase'),
      'lowercase' => t('Lowercase'),
    ),
    '#default_value' => theme_get_setting('h2_transform', 'edux'),
    '#description'   => t("Default value is <strong>None</strong>.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h2']['h2_height'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 3,
    '#step' => 0.1,
    '#title'  => t('Line Height'),
    '#default_value' => theme_get_setting('h2_height', 'edux'),
    '#description'   => t("Default size is 1.7"),
  ];
  $form['typography']['headings']['h3'] = [
    '#type'        => 'details',
    '#title'       => t('Heading 3 (H3)'),
  ];
  $form['typography']['headings']['h3']['h3_size'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 5,
    '#step' => 0.1,
    '#title'  => t('Font Size (rem)'),
    '#default_value' => theme_get_setting('h3_size', 'edux'),
    '#description'   => t("Value is in <strong>rem</strong> unit. 1 rem = 16px<br />Default size is 1.6rem.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h3']['h3_weight'] = [
    '#type'   => 'select',
    '#title'  => t('Font Weight'),
    '#options' => array(
      '400' => t('400'),
      '700' => t('700'),
    ),
    '#default_value' => theme_get_setting('h3_weight', 'edux'),
    '#description'   => t("Default value is <strong>700</strong>.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h3']['h3_transform'] = [
    '#type'          => 'select',
    '#title'  => t('Transform'),
    '#options' => array(
    	'none' => t('None'),
      'capitalize' => t('Capitalize'),
      'uppercase' => t('Uppercase'),
      'lowercase' => t('Lowercase'),
    ),
    '#default_value' => theme_get_setting('h3_transform', 'edux'),
    '#description'   => t("Default value is <strong>None</strong>.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h3']['h3_height'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 3,
    '#step' => 0.1,
    '#title'  => t('Line Height'),
    '#default_value' => theme_get_setting('h3_height', 'edux'),
    '#description'   => t("Default size is 1.7"),
  ];
  $form['typography']['headings']['h4'] = [
    '#type'        => 'details',
    '#title'       => t('Heading 4 (H4)'),
  ];
  $form['typography']['headings']['h4']['h4_size'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 5,
    '#step' => 0.1,
    '#title'  => t('Font Size (rem)'),
    '#default_value' => theme_get_setting('h4_size', 'edux'),
    '#description'   => t("Value is in <strong>rem</strong> unit. 1 rem = 16px<br />Default size is 1.3rem.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h4']['h4_weight'] = [
    '#type'   => 'select',
    '#title'  => t('Font Weight'),
    '#options' => array(
      '400' => t('400'),
      '700' => t('700'),
    ),
    '#default_value' => theme_get_setting('h4_weight', 'edux'),
    '#description'   => t("Default value is <strong>700</strong>.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h4']['h4_transform'] = [
    '#type'          => 'select',
    '#title'  => t('Transform'),
    '#options' => array(
    	'none' => t('None'),
      'capitalize' => t('Capitalize'),
      'uppercase' => t('Uppercase'),
      'lowercase' => t('Lowercase'),
    ),
    '#default_value' => theme_get_setting('h4_transform', 'edux'),
    '#description'   => t("Default value is <strong>None</strong>.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h4']['h4_height'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 3,
    '#step' => 0.1,
    '#title'  => t('Line Height'),
    '#default_value' => theme_get_setting('h4_height', 'edux'),
    '#description'   => t("Default size is 1.7"),
  ];
  $form['typography']['headings']['h5'] = [
    '#type'        => 'details',
    '#title'       => t('Heading 5 (H5)'),
  ];
  $form['typography']['headings']['h5']['h5_size'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 5,
    '#step' => 0.1,
    '#title'  => t('Font Size (rem)'),
    '#default_value' => theme_get_setting('h5_size', 'edux'),
    '#description'   => t("Value is in <strong>rem</strong> unit. 1 rem = 16px<br />Default size is 1.1rem.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h5']['h5_weight'] = [
    '#type'   => 'select',
    '#title'  => t('Font Weight'),
    '#options' => array(
      '400' => t('400'),
      '700' => t('700'),
    ),
    '#default_value' => theme_get_setting('h5_weight', 'edux'),
    '#description'   => t("Default value is <strong>700</strong>.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h5']['h5_transform'] = [
    '#type'          => 'select',
    '#title'  => t('Transform'),
    '#options' => array(
    	'none' => t('None'),
      'capitalize' => t('Capitalize'),
      'uppercase' => t('Uppercase'),
      'lowercase' => t('Lowercase'),
    ),
    '#default_value' => theme_get_setting('h5_transform', 'edux'),
    '#description'   => t("Default value is <strong>None</strong>.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h5']['h5_height'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 3,
    '#step' => 0.1,
    '#title'  => t('Line Height'),
    '#default_value' => theme_get_setting('h5_height', 'edux'),
    '#description'   => t("Default size is 1.7"),
  ];
  $form['typography']['headings']['h6'] = [
    '#type'        => 'details',
    '#title'       => t('Heading 6 (H6)'),
  ];
  $form['typography']['headings']['h6']['h6_size'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 5,
    '#step' => 0.1,
    '#title'  => t('Font Size (rem)'),
    '#default_value' => theme_get_setting('h6_size', 'edux'),
    '#description'   => t("Value is in <strong>rem</strong> unit. 1 rem = 16px<br />Default size is 1.1rem.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h6']['h6_weight'] = [
    '#type'   => 'select',
    '#title'  => t('Font Weight'),
    '#options' => array(
      '400' => t('400'),
      '700' => t('700'),
    ),
    '#default_value' => theme_get_setting('h6_weight', 'edux'),
    '#description'   => t("Default value is <strong>700</strong>.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h6']['h6_transform'] = [
    '#type'          => 'select',
    '#title'  => t('Transform'),
    '#options' => array(
    	'none' => t('None'),
      'capitalize' => t('Capitalize'),
      'uppercase' => t('Uppercase'),
      'lowercase' => t('Lowercase'),
    ),
    '#default_value' => theme_get_setting('h6_transform', 'edux'),
    '#description'   => t("Default value is <strong>None</strong>.<br /><br /><p><hr /></p>"),
  ];
  $form['typography']['headings']['h6']['h6_height'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 3,
    '#step' => 0.1,
    '#title'  => t('Line Height'),
    '#default_value' => theme_get_setting('h6_height', 'edux'),
    '#description'   => t("Default size is 1.7"),
  ];
  /*
   * Elements
   */
  $form['elements']['elements_tab'] = [
    '#type'  => 'vertical_tabs',
  ];
  // Elements -> Logo
  $form['elements']['logo'] = [
    '#type'  => 'details',
    '#title' => t('Logo'),
    '#group' => 'elements_tab',
  ];
  $form['elements']['logo']['logo_default_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Use Default'),
    '#attributes' => array('class' => array('set-default-fieldset')),
  ];
  $form['elements']['logo']['logo_default_section']['logo_default'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Use Default Logo Settings'),
    '#default_value' => theme_get_setting('logo_default', 'edux'),
    '#description'   => t('Check this option to use default values for sitename and site slogan. Uncheck this to set custom values below.'),
  ];
  $form['elements']['logo']['site_name'] = [
    '#type'        => 'details',
    '#title'       => t('Site Name'),
    '#open' => TRUE,
  ];
  $form['elements']['logo']['site_name']['site_name_size'] = [
    '#type'   => 'number',
    '#min'  => 0.5,
    '#max'  => 3,
    '#step' => 0.1,
    '#title'  => t('Font Size (rem)'),
    '#default_value' => theme_get_setting('site_name_size', 'edux'),
    '#description'   => t("Default value is <strong>1rem</strong>.<br />1 rem = 16px<br /><br /><br /><p><hr /></p>"),
  ];
  $form['elements']['logo']['site_name']['site_name_weight'] = [
    '#type'   => 'select',
    '#title'  => t('Font Weight'),
    '#options' => array(
      '400' => t('400'),
      '700' => t('700'),
    ),
    '#default_value' => theme_get_setting('site_name_weight', 'edux'),
    '#description'   => t("Default value is <strong>700</strong>.<br /><br /><p><hr /></p>"),
  ];
  $form['elements']['logo']['site_name']['site_name_transform'] = [
    '#type'          => 'select',
    '#title'  => t('Transform'),
    '#options' => array(
    	'none' => t('None'),
      'capitalize' => t('Capitalize'),
      'uppercase' => t('Uppercase'),
      'lowercase' => t('Lowercase'),
    ),
    '#default_value' => theme_get_setting('site_name_transform', 'edux'),
    '#description'   => t("Default value is <strong>None</strong>.<br /><br /><p><hr /></p>"),
  ];
  $form['elements']['logo']['site_name']['site_name_height'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 3,
    '#step' => 0.1,
    '#title'  => t('Line Height'),
    '#default_value' => theme_get_setting('site_name_height', 'edux'),
    '#description'   => t("Default size is 1.1"),
  ];
  $form['elements']['logo']['slogan'] = [
    '#type'        => 'details',
    '#title'       => t('Slogan'),
    '#open' => TRUE,
  ];
  $form['elements']['logo']['slogan']['slogan_size'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 5,
    '#step' => 0.1,
    '#title'  => t('Font Size (rem)'),
    '#default_value' => theme_get_setting('slogan_size', 'edux'),
    '#description'   => t("Value is in <strong>rem</strong> unit. 1 rem = 16px<br />Default size is 1rem.<br /><br /><p><hr /></p>"),
  ];
  $form['elements']['logo']['slogan']['slogan_transform'] = [
    '#type'          => 'select',
    '#title'  => t('Transform'),
    '#options' => array(
    	'none' => t('None'),
      'capitalize' => t('Capitalize'),
      'uppercase' => t('Uppercase'),
      'lowercase' => t('Lowercase'),
    ),
    '#default_value' => theme_get_setting('slogan_transform', 'edux'),
    '#description'   => t("Default value is <strong>None</strong>.<br /><br /><p><hr /></p>"),
  ];
  $form['elements']['logo']['slogan']['slogan_height'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 3,
    '#step' => 0.1,
    '#title'  => t('Line Height'),
    '#default_value' => theme_get_setting('slogan_height', 'edux'),
    '#description'   => t("Default size is 1.7"),
  ];
  $form['elements']['logo']['slogan']['slogan_style'] = [
    '#type'          => 'select',
    '#title'  => t('Style'),
    '#options' => array(
    	'normal' => t('Normal'),
      'italic' => t('Italic'),
    ),
    '#default_value' => theme_get_setting('slogan_style', 'edux'),
    '#description'   => t("Default value is <strong>Normal</strong>."),
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
    '#default_value' => theme_get_setting('main_menu_default', 'edux'),
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
    '#default_value' => theme_get_setting('main_menu_top_size', 'edux'),
    '#description'   => t("Default value is <strong>1rem</strong>.<br />1 rem = 16px<br /><p><hr /></p>"),
  ];
  $form['elements']['main_menu']['main_menu_top']['main_menu_top_weight'] = [
    '#type'   => 'select',
    '#title'  => t('Font Weight'),
    '#options' => array(
      '400' => t('400'),
      '700' => t('700'),
    ),
    '#default_value' => theme_get_setting('main_menu_top_weight', 'edux'),
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
    '#default_value' => theme_get_setting('main_menu_top_transform', 'edux'),
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
    '#default_value' => theme_get_setting('main_menu_sub_size', 'edux'),
    '#description'   => t("Default value is <strong>1rem</strong>.<br />1 rem = 16px<br /><p><hr /></p>"),
  ];
  $form['elements']['main_menu']['main_menu_sub']['main_menu_sub_weight'] = [
    '#type'   => 'select',
    '#title'  => t('Font Weight'),
    '#options' => array(
      '400' => t('400'),
      '700' => t('700'),
    ),
    '#default_value' => theme_get_setting('main_menu_sub_weight', 'edux'),
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
    '#default_value' => theme_get_setting('main_menu_sub_transform', 'edux'),
    '#description'   => t("Default value is <strong>None</strong>."),
  ];
  $form['elements']['main_menu']['main_menu_color'] = [
    '#type'          => 'details',
    '#title'         => t('Main Menu Color'),
    '#description'   => t('Color option is available in the premium version of this theme. <a href="https://drupar.com/theme/eduxpro" target="_blank">Buy Edu-X-Pro for $29 only.</a>'),
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
    '#default_value' => theme_get_setting('page_title_default', 'edux'),
    '#description'   => t('Check this option to use default values for page title. Uncheck this to set custom values below.'),
  ];
  $form['elements']['page_title']['page_title_size_section'] = [
    '#type'        => 'details',
    '#title'       => t('Font Size (rem)'),
    '#open' => TRUE,
    '#description'   => t("Value is in rem unit. 1 rem = 16px"),
  ];
  $form['elements']['page_title']['page_title_size_section']['page_title_size_desktop'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 5,
    '#step' => 0.1,
    '#title'  => t('Desktop and Laptop (rem)'),
    '#default_value' => theme_get_setting('page_title_size_desktop', 'edux'),
    '#description'   => t("Default value is <strong>2.6rem</strong>.<br /><p><hr /></p>"),
  ];
  $form['elements']['page_title']['page_title_size_section']['page_title_size_mobile'] = [
    '#type'   => 'number',
    '#min'  => 1,
    '#max'  => 5,
    '#step' => 0.1,
    '#title'  => t('Mobile and Tablet (rem)'),
    '#default_value' => theme_get_setting('page_title_size_mobile', 'edux'),
    '#description'   => t("Default value is <strong>2.2rem</strong>."),
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
    '#default_value' => theme_get_setting('page_title_transform', 'edux'),
    '#description'   => t("Default value is <strong>None</strong>."),
  ];
  // Elements -> Button
  $form['elements']['button'] = [
    '#type'  => 'details',
    '#title' => t('Button'),
    '#group' => 'elements_tab',
  ];
  $form['elements']['button']['button_default_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Use Default'),
    '#attributes' => array('class' => array('set-default-fieldset')),
  ];
  $form['elements']['button']['button_default_section']['button_default'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Use Default Main Menu Settings'),
    '#default_value' => theme_get_setting('button_default', 'edux'),
    '#description'   => t('Check this option to use default main menu settings. Uncheck this to set custom values below.'),
  ];
  $form['elements']['button']['button_section'] = [
    '#type'        => 'details',
    '#title'       => t('Button Padding'),
    '#open' => TRUE,
  ];
  $form['elements']['button']['button_section']['button_padding'] = [
    '#type'   => 'textfield',
    '#title'  => t('Top Right Bottom Left'),
    '#default_value' => theme_get_setting('button_padding', 'edux'),
    '#description'   => t("Padding of button. Example: <strong>5px 10px 5px 10px</strong><br />
    Default value is: 8px 10px 8px 10px<br /><p><hr /></p>"),
  ];
  $form['elements']['button']['button_section']['button_radius'] = [
    '#type'   => 'number',
    '#min'  => 0,
    '#step' => 1,
    '#title'  => t('Border Radius (px)'),
    '#default_value' => theme_get_setting('button_radius', 'edux'),
    '#description'   => t("Border radius of buttons. Default value is 8px."),
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
    '#default_value' => theme_get_setting('font_src', 'edux'),
    '#description'   => t('EduX theme uses following Google fonts: Noto Sans.<br />You can serve these fonts locally or from Google server.'),
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
  $form['components']['social']['all_icons']['all_icons_show'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show social icons in footer'),
    '#default_value' => theme_get_setting('all_icons_show', 'edux'),
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
    '#default_value' => theme_get_setting('facebook_url', 'edux'),
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
    '#default_value' => theme_get_setting('twitter_url', 'edux'),
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
    '#default_value' => theme_get_setting('instagram_url', 'edux'),
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
    '#default_value' => theme_get_setting('linkedin_url', 'edux'),
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
    '#default_value' => theme_get_setting('youtube_url', 'edux'),
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
    '#default_value' => theme_get_setting('vimeo_url', 'edux'),
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
    '#default_value' => theme_get_setting('telegram_url', 'edux'),
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
    '#default_value' => theme_get_setting('whatsapp_url', 'edux'),
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
    '#default_value' => theme_get_setting('github_url', 'edux'),
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
      '#default_value' => theme_get_setting('vk_url', 'edux'),
  ];
  $form['components']['social']['node_share'] = [
    '#type'        => 'details',
    '#title'       => t('Share Page'),
    '#description'   => t('<h3>Share Page On Social networking websites</h3><p>This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/eduxpro" target="_blank">Buy Edu-X-Pro for $29 only.</a></p>'),
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
    '#default_value' => theme_get_setting('fontawesome_four', 'edux'),
    '#description'   => t('Check this option to enable fontawesome version 4 font icons.'),
  ];
  $form['components']['font_icons']['fontawesome5'] = [
    '#type'          => 'fieldset',
    '#title'         => t('FontAwesome 5'),
  ];
  $form['components']['font_icons']['fontawesome5']['fontawesome_five'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable FontAwesome 5 Font Icons'),
    '#default_value' => theme_get_setting('fontawesome_five', 'edux'),
    '#description'   => t('Check this option to enable fontawesome version 5 font icons.'),
  ];
	$form['components']['font_icons']['bootstrap_icons'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Bootstrap Font Icons'),
  ];
  $form['components']['font_icons']['bootstrap_icons']['bootstrapicons'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable Bootstrap Icons'),
    '#default_value' => theme_get_setting('bootstrapicons', 'edux'),
    '#description'   => t('Check this option to enable Bootstrap Font Icons. Read more about <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a>'),
  ];
  $form['components']['font_icons']['materialicons'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Google Material Font Icons'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/eduxpro" target="_blank">Purchase Edu-X-Pro for $29 only.</a>'),
  ];
  // Components -> Page loader.
  $form['components']['preloader'] = [
    '#type'        => 'details',
    '#title'       => t('Pre Page Loader'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/eduxpro" target="_blank">Buy Edu-X-Pro for $29 only.</a>'),
    '#group' => 'components_tab',
  ];
  // Components -> Cookie message.
  $form['components']['cookie'] = [
    '#type'        => 'details',
    '#title'       => t('Cookie Consent message'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/eduxpro" target="_blank">Buy Edu-X-Pro for $29 only.</a>'),
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
  $form['components']['scrolltotop']['scrolltotop_on'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable Scroll To Top'),
    '#default_value' => theme_get_setting('scrolltotop_on', 'edux'),
    '#description'   => t("Check this option to enable scroll to top feature. Uncheck to disable this fearure and hide scroll to top icon."),
  ];


  /**
   * Color Options
   */
  $form['color']['theme_color'] = [
    '#type'        => 'details',
    '#title'       => t('Theme Color'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/eduxpro" target="_blank">Buy Edu-X-Pro for $29 only.</a>'),
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
    '#description' => t('<h3>Insert Codes Before &lt;/HEAD&gt;</h3><p><hr /></p>This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/eduxpro" target="_blank">Buy Edu-X-Pro for $29 only.</a>'),
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
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/eduxpro" target="_blank">Buy Edu-X-Pro for $29 only.</a>'),
  ];
  // Insert Codes -> Body -> Body end codes
  $form['insert_codes']['body']['insert_body_end_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Insert code before &lt;/BODY&gt; tag'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/eduxpro" target="_blank">Buy Edu-X-Pro for $29 only.</a>'),
  ];
  // Insert Codes -> css
  $form['insert_codes']['css']['css_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Custom Styling'),
  ];

  $form['insert_codes']['css']['css_section']['styling'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable Additional CSS'),
    '#default_value' => theme_get_setting('styling', 'edux'),
    '#description'   => t("Check this option to enable custom styling. Uncheck to disable this feature.<br />Please refer to this tutorial page. <a href='https://drupar.com/doc/edux/custom-css' target='_blank'>How To Use Custom Styling</a>"),
  ];

  $form['insert_codes']['css']['css_section']['styling_code'] = [
    '#type'          => 'textarea',
    '#rows'          => 20,
    '#title'         => t('Custom CSS Codes'),
    '#default_value' => theme_get_setting('styling_code', 'edux'),
    '#description'   => t('Please enter your custom css codes in this text box. You can use it to customize the appearance of your site.<br />Please refer to this tutorial for detail: <a href="https://drupar.com/doc/edux/custom-css" target="_blank">Custom CSS</a>'),
  ];
  // Settings under support tab.
  $form['support']['info'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Documentation'),
    '#description' => t('<h4>Edu-X Documentation</h4>
    <p>Please check our documentation for detailed information on how to use Edu-X theme.<br /><a href="https://drupar.com/doc/edux" target="_blank">Edu X Documentation</a>.</p>
    <h4>The-X Documentation</h4>
    <p>Edu-X theme uses The-X theme as the base theme. So, many things are covered in <a href="https://drupar.com/doc/thex" target="_blank">The X Documentation</a>.</p>
    <hr />
    <h4>Create Issue</h4>
    <p>If you need support that is beyond our theme documentation, please <a href="https://www.drupal.org/project/issues/edux?status=All&categories=All" target="_blank">Create an issue</a> at project page.</p>
    <hr />
    <h4>Contact Us</h4>
    <p>If you need some specific customizations in this theme or need custom Drupal theme development, please contact us<br><a href="https://drupar.com/contact" target="_blank">Drupar.com/contact</a></p>'),
  ];

  // Settings under upgrade tab.
  $form['upgrade']['info'] = [
    '#type'        => 'fieldset',
    '#title'       => t('<p><a href="https://demo2.drupar.com/eduxpro/" target="_blank">eduxPro Demo</a> | <a href="https://drupar.com/theme/eduxpro" target="_blank">Purchase eduxPro for $29 only</a></p>'),
    '#description' => t("$eduxpro"),
  ];
// End form.
}
