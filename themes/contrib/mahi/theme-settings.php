<?php

use Drupal\Core\Form\FormStateInterface;

/**
 * Custom setting for Mahi theme.
 */
function mahi_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {
  $form['#attached']['library'][] = 'mahi/theme-settings';
  $mahipro_img = $GLOBALS['base_url'] . '/' . \Drupal::service('extension.list.theme')->getPath('mahi') . '/images/mahipro.png';
  $mahipro = '<img src="'.$mahipro_img.'" alt="mahipro" />';
  $form['mahi'] = [
    '#type'       => 'vertical_tabs',
    '#title'      => '<h3>' . t('Mahi Theme Settings') . '</h3>',
    '#default_tab' => 'general',
  ];
  // General settings tab.
  $form['general'] = [
    '#type'  => 'details',
    '#title' => t('General'),
    '#description' => t('<h4>Thanks for using Mahi Theme</h4><p>Mahi is a free Drupal 9, 10 & 11 theme designed and developed by <a href="https://drupar.com" target="_blank">Drupar.com</a></p>'),
    '#group' => 'mahi',
  ];
  // layout tab.
  $form['layout'] = [
    '#type'  => 'details',
    '#title' => t('Layout'),
    '#group' => 'mahi',
  ];
  // Theme Color tab.
  $form['color'] = [
    '#type'  => 'details',
    '#title' => t('Theme Color'),
    '#group' => 'mahi',
  ];
  // Social tab.
  $form['social'] = [
    '#type'  => 'details',
    '#title' => t('Social'),
    '#description' => t('These icons appear in footer region.'),
    '#group' => 'mahi',
  ];
  // Slider tab.
  $form['slider'] = [
    '#type'  => 'details',
    '#title' => t('Homepage Slider'),
    '#description' => t('<h4>Manage Homepage Slider</h4>'),
    '#group' => 'mahi',
  ];
  // Header tab.
  $form['header'] = [
    '#type'  => 'details',
    '#title' => t('Header'),
    '#group' => 'mahi',
  ];
  // Sidebar tab.
  $form['sidebar'] = [
    '#type'  => 'details',
    '#title' => t('Sidebar'),
    '#group' => 'mahi',
  ];
  // Content tab.
  $form['content'] = [
    '#type'  => 'details',
    '#title' => t('Content'),
    '#group' => 'mahi',
  ];
  // Footer tab.
  $form['footer'] = [
    '#type'  => 'details',
    '#title' => t('Footer'),
    '#group' => 'mahi',
  ];
  // components tab.
  $form['components'] = [
    '#type'  => 'details',
    '#title' => t('Components'),
    '#group' => 'mahi',
  ];
  // SEO
  $form['seo'] = [
    '#type'  => 'details',
    '#title' => t('SEO'),
    '#group' => 'mahi',
    '#description'  => t('<stong>Under Development</strong>'),
  ];
  // Insert codes
  $form['insert_codes'] = [
    '#type'  => 'details',
    '#title' => t('Insert Codes'),
    '#group' => 'mahi',
  ];
  // Support tab.
  $form['support'] = [
    '#type'  => 'details',
    '#title' => t('Support'),
    '#group' => 'mahi',
  ];
  // Upgrade to mahipro tab.
  $form['upgrade'] = [
    '#type'  => 'details',
    '#title' => t('Upgrade to MahiPro'),
    '#description'  => t("<h4>Upgrade To MahiPro For $29 Only.</h4><p><a href='https://drupar.com/theme/mahipro' target='_blank'>Purchase MahiPro</a> || <a href='https://demo.drupar.com/mahipro/' target='_blank'>MahiPro Demo</a></p><p>$mahipro</p>"),
    '#group' => 'mahi',
  ];
  // General
  $form['general']['general_info'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Theme Info'),
    '#description' => t('<a href="https://drupar.com/theme/mahi" target="_blank">Theme Homepage</a> || <a href="https://demo.drupar.com/mahi/" target="_blank">Theme Demo</a> || <a href="https://drupar.com/doc/mahi" target="_blank">Theme Documentation</a> || <a href="https://drupar.com/doc/mahi/support" target="_blank">Theme Support</a>'),
  ];

  $form['general']['general_info_upgrade'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Upgrade To MahiPro for $29 only'),
    '#description' => t('<a href="https://drupar.com/theme/mahipro" target="_blank">Purchase MahiPro</a> || <a href="https://demo.drupar.com/mahipro/" target="_blank">MahiPro Demo</a>'),
  ];
  // Layout -> Container width
  $form['layout']['layout_container'] = [
    '#type'        => 'fieldset',
    '#title'         => t('Container width (px)'),
  ];
  $form['layout']['layout_container']['container_width'] = [
    '#type'          => 'number',
    '#default_value' => theme_get_setting('container_width', 'mahi'),
    '#description'   => t('Set width of the container in px. Default width is 1200px.'),
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
    '#default_value' => theme_get_setting('header_width', 'mahi'),
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
    '#default_value' => theme_get_setting('main_width', 'mahi'),
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
    '#default_value' => theme_get_setting('footer_width', 'mahi'),
  ];
  // Color
  $form['color']['theme_color'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Theme Color'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/mahipro" target="_blank">Buy MahiPro for $29 only.</a>'),
  ];
  // Social settings
  /* Social -> Show or hide all icons */
  $form['social']['all_icons'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Show Social Icons'),
  ];
  $form['social']['all_icons']['all_icons_show'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show social icons in footer'),
    '#default_value' => theme_get_setting('all_icons_show', 'mahi'),
    '#description'   => t("Check this option to show social icons in footer. Uncheck to hide."),
  ];
  // Facebook.
    $form['social']['facebook'] = [
    '#type'        => 'details',
    '#title'       => t("Facebook"),
  ];
  $form['social']['facebook']['facebook_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Facebook Url'),
    '#description'   => t("Enter yours facebook profile or page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('facebook_url', 'mahi'),
  ];
  // Twitter.
  $form['social']['twitter'] = [
    '#type'        => 'details',
    '#title'       => t("Twitter"),
  ];
  $form['social']['twitter']['twitter_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Twitter Url'),
    '#description'   => t("Enter yours twitter page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('twitter_url', 'mahi'),
  ];
  // Instagram.
  $form['social']['instagram'] = [
    '#type'        => 'details',
    '#title'       => t("Instagram"),
  ];
  $form['social']['instagram']['instagram_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Instagram Url'),
    '#description'   => t("Enter yours instagram page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('instagram_url', 'mahi'),
  ];
  // Linkedin.
  $form['social']['linkedin'] = [
    '#type'        => 'details',
    '#title'       => t("Linkedin"),
  ];
  $form['social']['linkedin']['linkedin_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Linkedin Url'),
    '#description'   => t("Enter yours linkedin page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('linkedin_url', 'mahi'),
  ];
  // YouTube.
  $form['social']['youtube'] = [
    '#type'        => 'details',
    '#title'       => t("YouTube"),
  ];
  $form['social']['youtube']['youtube_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('YouTube Url'),
    '#description'   => t("Enter yours youtube.com page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('youtube_url', 'mahi'),
  ];
  // Vimeo.
  $form['social']['vimeo'] = [
    '#type'        => 'details',
    '#title'       => t("Vimeo"),
  ];
  $form['social']['vimeo']['vimeo_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('YouTube Url'),
    '#description'   => t("Enter yours vimeo.com page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('vimeo_url', 'mahi'),
  ];
  // Social -> vk.com url.
  $form['social']['vk'] = [
    '#type'        => 'details',
    '#title'       => t("vk.com"),
  ];
  $form['social']['vk']['vk_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('vk.com'),
    '#description'   => t("Enter yours vk.com page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('vk_url', 'mahi'),
  ];
  // Social -> whatsapp.
  $form['social']['whatsapp'] = [
    '#type'        => 'details',
    '#title'       => t("whatsapp"),
  ];
  $form['social']['whatsapp']['whatsapp_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('WhatsApp'),
    '#description'   => t("Enter yours whatsapp url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('whatsapp_url', 'mahi'),
  ];
  // Social -> github.
  $form['social']['github'] = [
    '#type'        => 'details',
    '#title'       => t("Github"),
  ];
  $form['social']['github']['github_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Github'),
    '#description'   => t("Enter yours github url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('github_url', 'mahi'),
  ];
  // Social -> telegram.
  $form['social']['telegram'] = [
    '#type'        => 'details',
    '#title'       => t("Telegram"),
  ];
  $form['social']['telegram']['telegram_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Telegram'),
    '#description'   => t("Enter yours telegram url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('telegram_url', 'mahi'),
  ];
  // Homepage slider
  // Show or hide slider on homepage.
  $form['slider']['slider_show_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Enable Slider'),
  ];
  $form['slider']['slider_show_section']['slider_show'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show Slider on Homepage'),
    '#default_value' => theme_get_setting('slider_show', 'mahi'),
    '#description'   => t("Check this option to show slider on homepage. Uncheck to hide."),
  ];
  $form['slider']['slider_image_section'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Upload Slider Images'),
  ];
  $form['slider']['slider_image_section']['slider_images'] = [
    '#type'          => 'managed_file',
    '#multiple' => TRUE,
    '#upload_location' => 'public://',
    '#upload_validators' => array(
      'file_validate_extensions' => array('gif png jpg jpeg svg'),
    ),
    '#default_value'  => theme_get_setting('slider_images', 'mahi'),
    '#description'   => t('<p><hr /></p><p><ul><li>You can upload multiple images.</li><li>Recommended size is 1920px X 1080px</li><li>Right click the image to copy the image path and use it in the slider code below.</li></ul></p>'),
  ];
  // Slider -> Slider code.
  $form['slider']['slider_code'] = [
    '#type'          => 'textarea',
    '#title'         => t('Slider Code'),
    '#default_value' => theme_get_setting('slider_code', 'mahi'),
    // '#default_value' => check_markup(theme_get_setting('slider_code'), 'full_html'),
    '#description'   => t('Please refer to this <a href="https://drupar.com/node/2626" target="_blank">tutorial page</a> for slider code tutorial.<br />Leave slider code box empty to show default slider.'),
  ];
  // Header
  $form['header']['sticky_header'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Sticky Header'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/mahipro" target="_blank">Buy MahiPro for $29 only.</a>'),
  ];
  $form['header']['header_links'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Documentation Links'),
    '#description'   => t('<p><a href="https://drupar.com/node/2622">How to change favicon icon</a></p><p><a href="https://drupar.com/node/2623">How to manage website logo</a></p><p><a href="https://drupar.com/node/2625">Fancy Search Form</a></p><p><a href="https://drupar.com/node/2624">Header main menu</a></p>'),
  ];
  // Sidebar
  $form['sidebar']['animated_sidebar'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Animated Sliding Sidebar'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/mahipro" target="_blank">Buy MahiPro for $29 only.</a>'),
  ];
  // Content
  $form['content']['content_tab'] = [
    '#type'  => 'vertical_tabs',
  ];
  // content -> Homepage  content
  $form['content_tab']['home_content'] = [
    '#type'        => 'details',
    '#title'       => t('Homepage content'),
    '#description' => t('<p>Please follow this tutorial to add content on homepage.</p><p><a href="https://drupar.com/node/2627" target="_blank">How To Create Homepage</a></p><p><a href="https://drupar.com/node/2628" target="_blank">How to add content on homepage</a></p>'),
    '#group' => 'content_tab',
  ];
  // content -> Page loader
  $form['content_tab']['preloader'] = [
    '#type'        => 'details',
    '#title'       => t('Pre Page Loader'),
    '#description' => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/mahipro" target="_blank">Buy mahiPro for $29 only.</a>'),
    '#group' => 'content_tab',
  ];
  // content -> Animated Content
  $form['content_tab']['animated_content'] = [
    '#type'        => 'details',
    '#title'       => t('Animated Content'),
    '#description' => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/mahipro" target="_blank">Buy mahiPro for $29 only.</a>'),
    '#group' => 'content_tab',
  ];
  // content -> theme font
  $form['content_tab']['font_tab'] = [
    '#type'        => 'details',
    '#title'       => t('Fonts'),
    '#group' => 'content_tab',
  ];
  // content -> Font icons
  $form['content_tab']['icon_tab'] = [
    '#type'        => 'details',
    '#title'       => t('Font Icons'),
    '#group' => 'content_tab',
  ];
  // content -> shortcodes
  $form['content_tab']['shortcode'] = [
    '#type'        => 'details',
    '#title'       => t('Shortcodes'),
    '#description' => t('<p>Mahi theme has some custom shortcodes. You can create some styling content using these shortcodes.</p><p>Please visit this tutorial page for details. <a href="https://drupar.com/node/2636" target="_blank">Shortcodes in mahi theme</a>.</p>'),
    '#group' => 'content_tab',
  ];
  // content -> comment
  $form['content_tab']['comment'] = [
    '#type'        => 'details',
    '#title'       => t('Comment'),
    '#description' => t(''),
    '#group' => 'content_tab',
  ];
  // content -> share page
  $form['content_tab']['node_share'] = [
    '#type'        => 'details',
    '#title'       => t('Share Page'),
    '#description' => t('<p><strong>Share Page On Social Media</strong></p><p>This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/mahipro" target="_blank">Buy mahiPro for $29 only.</a></p>'),
    '#group' => 'content_tab',
  ];
  // content -> share page
  $form['content_tab']['demo_content'] = [
    '#type'        => 'details',
    '#title'       => t('Demo Sie Content'),
    '#description' => t('<p>Content of theme Mahi demo site is available on our website.</p><p><a href="https://demo.drupar.com/mahi/" target="_blank">Mahi Theme Demo</a> | <a href="https://drupar.com/node/2638/" target="_blank">Get Demo Site Content</a></p>'),
    '#group' => 'content_tab',
  ];
  // content -> theme font -> Font source
  $form['content_tab']['font_tab']['font_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Theme Font Source'),
  ];
  $form['content_tab']['font_tab']['font_section']['font_src'] = [
    '#type'          => 'select',
    '#title'         => t('Select Google Fonts Location'),
    '#options' => array(
    	'local' => t('Local Self Hosted'),
      'googlecdn' => t('Google CDN Server')
    ),
    '#default_value' => theme_get_setting('font_src', 'mahi'),
    '#description'   => t('Mahi theme uses following Google fonts: Plus Jakarta Sans and Poppins. You can serve these fonts locally or from Google server.'),
  ];
  // content -> font icon -> fontaweome 5
  $form['content_tab']['icon_tab']['fontawesome5_sec'] = [
    '#type'        => 'fieldset',
    '#title'       => t('FontAwesome 5'),
		'#description'   => t("<mark>Please do not enable both FontAwesome 5 and FontAwesome 6</mark>")
  ];
  $form['content_tab']['icon_tab']['fontawesome5_sec']['fontawesome_five'] = [
    '#type'          => 'checkbox',
    '#title'         => t("Enable FontAwesome 5 font icons library."),
    '#default_value' => theme_get_setting('fontawesome_five', 'mahi'),
    '#description'   => t('Check this option to enable FontAwesome5 font icons.<br />Please refer to <a href="https://fontawesome.com/v5/search?o=r&m=free" target="_blank">FontAwesome 5 page</a> for icons code.'),
  ];
  // content -> font icon -> fontaweome 6
  $form['content_tab']['icon_tab']['fontawesome6_sec'] = [
    '#type'        => 'fieldset',
    '#title'       => t('FontAwesome 6'),
		'#description'   => t("<mark>Please do not enable both FontAwesome 5 and FontAwesome 6</mark>")
  ];
  $form['content_tab']['icon_tab']['fontawesome6_sec']['fontawesome_six'] = [
    '#type'          => 'checkbox',
    '#title'         => t("Enable FontAwesome 6 font icons library."),
    '#default_value' => theme_get_setting('fontawesome_six', 'mahi'),
    '#description'   => t('Check this option to enable FontAwesome6 font icons.<br />Please refer to <a href="https://fontawesome.com/v6/search?o=r&m=free" target="_blank">FontAwesome 6 page</a> for icons code.'),
  ];
  // content -> font icon -> bootstrap
  $form['content_tab']['icon_tab']['bootstrap_icons'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Bootstrap Font Icons'),
  ];
  $form['content_tab']['icon_tab']['bootstrap_icons']['bootstrapicons'] = [
    '#type'          => 'checkbox',
    '#title'         => t("Enable Bootstrap Icons"),
    '#default_value' => theme_get_setting('bootstrapicons', 'mahi'),
    '#description'   => t('Check this option to enable Bootstrap Font Icons. Read more about <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a>'),
  ];
  // content -> comment -> Highlight author comment
  $form['content_tab']['comment']['comment_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Highlight Node Author Comment'),
  ];
  $form['content_tab']['comment']['comment_section']['highlight_author_comment'] = [
    '#type'          => 'checkbox',
    '#title'         => t("Highlight Node Author's Comments"),
    '#default_value' => theme_get_setting('highlight_author_comment', 'mahi'),
    '#description'   => t("Check this option to highlight node author's comments."),
  ];
  // Scroll to top.
  $form['footer']['scrolltotop'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Scroll To Top'),
  ];
  $form['footer']['scrolltotop']['scrolltotop_on'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable scroll to top feature.'),
    '#default_value' => theme_get_setting('scrolltotop_on', 'mahi'),
    '#description'   => t("Check this option to enable scroll to top feature. Uncheck to disable this fearure and hide scroll to top icon."),
  ];
  // Footer -> Copyright.
  $form['footer']['copyright'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Website Copyright Text'),
  ];
  $form['footer']['copyright']['copyright_text'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show website copyright text in footer.'),
    '#default_value' => theme_get_setting('copyright_text', 'mahi'),
    '#description'   => t("Check this option to show website copyright text in footer. Uncheck to hide."),
  ];
  // Footer -> Copyright -> custom copyright text
  $form['footer']['copyright']['copyright_text_custom'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Custom copyright text'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/mahipro" target="_blank">Buy mahiPro for $29 only.</a>'),
  ];
  // Footer -> Cookie message.
  $form['footer']['cookie'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Cookie Consent message'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/mahipro" target="_blank">Buy mahiPro for $29 only.</a>'),
  ];
  $form['footer']['cookie']['cookie_message'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Show Cookie Consent Message'),
    '#description'   => t('Make your website EU Cookie Law Compliant. According to EU cookies law, websites need to get consent from visitors to store or retrieve cookies.'),
  ];
  /**
   * Components
   */
  $form['components']['component_button'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Buttons'),
    '#description' => t('<a href="https://drupar.com/node/2667" target="_blank">Buttons</a>'),
  ];
  $form['components']['component_text_box'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Text Box'),
    '#description' => t('<a href="https://drupar.com/node/2649" target="_blank">Text Box</a>'),
  ];
  $form['components']['component_message_box'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Message Box'),
    '#description' => t('<a href="https://drupar.com/node/2665" target="_blank">Message Box</a>'),
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
    '#description' => t('<h4>Insert Codes Before &lt;/HEAD&gt;</h4><hr />'),
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
  // Insert Codes -> Head -> Head codes
  $form['insert_codes']['head']['insert_head'] = [
    '#type'          => 'fieldset',
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/mahipro" target="_blank">Buy MahiPro for $29 only.</a>'),
  ];
  // Insert Codes -> Body -> Body start codes
  $form['insert_codes']['body']['insert_body_start_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Insert code after &lt;BODY&gt; tag'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/mahipro" target="_blank">Buy MahiPro for $29 only.</a>'),
  ];
  // Insert Codes -> Body -> Body ENd codes
  $form['insert_codes']['body']['insert_body_end_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Insert code before &lt;/BODY&gt; tag'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/mahipro" target="_blank">Buy MahiPro for $29 only.</a>'),
  ];
  // Insert Codes -> css
  $form['insert_codes']['css']['css_custom'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Addtional CSS'),
  ];
  $form['insert_codes']['css']['css_custom']['css_extra'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable Addtional CSS'),
    '#default_value' => theme_get_setting('css_extra', 'mahi'),
    '#description'   => t("Check this option to enable additional styling / css. Uncheck to disable this feature."),
  ];
  $form['insert_codes']['css']['css_code'] = [
    '#type'          => 'textarea',
    '#title'         => t('Addtional CSS Codes'),
    '#default_value' => theme_get_setting('css_code', 'mahi'),
    '#description'   => t('Add your own CSS codes here to customize the appearance of your site. Please refer to this tutorial for detail: <a href="https://drupar.com/node/2637" target="_blank">Custom CSS</a>'),
  ];
  // Support
  $form['support']['info'] = [
    '#type'        => 'fieldset',
    '#title'         => t('Theme Support'),
    '#description' => t('<h4>Documentation</h4>
    <p>We have a detailed documentation about how to use theme. Please read the <a href="https://drupar.com/doc/mahi" target="_blank">Mahi Theme Documentation</a>.</p>
    <hr />
    <h4>Open An Issue</h4>
    <p>If you need support that is beyond our theme documentation, please <a href="https://www.drupal.org/project/issues/mahi?categories=All" target="_blank">open an issue</a> at project page.</p>
    <hr />
    <h4>Contact Us</h4>
    <p>If you need some specific customization in theme, please contact us<br><a href="https://drupar.com/contact" target="_blank">drupar.com/contact</a></p>'),
  ];
}
