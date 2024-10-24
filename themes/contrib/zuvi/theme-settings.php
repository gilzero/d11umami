<?php
use Drupal\Core\Form\FormStateInterface;
/**
 * @file
 * Custom setting for Zuvi theme.
 */

function zuvi_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {
  $form['#attached']['library'][] = 'zuvi/theme-settings';
  $image_zuvipro = $GLOBALS['base_url'] . '/' . \Drupal::service('extension.list.theme')->getPath('zuvi') . '/images/zuvipro.png';
  $zuvipro = '<img src="'.$image_zuvipro.'" alt="zuviPro" />';
  $form['zuvi'] = [
    '#type'       => 'vertical_tabs',
    '#title'      => '<h3 class="settings-form-title">' . t('') . '</h3>',
    '#default_tab' => 'general',
  ];

  /**
   * Main Tabs.
   */

  // Main Tabs -> General.
  $form['general'] = [
    '#type'  => 'details',
    '#title' => t('General'),
    '#description' => t('<h3>Thanks for using ZUVI Theme</h3>Zuvi is a free Drupal 9, 10, 11 theme designed and developed by <a href="https://drupar.com" target="_blank">Drupar.com</a>'),
    '#group' => 'zuvi',
  ];
  $form['layout'] = [
    '#type'  => 'details',
    '#title' => t('Layout'),
    '#group' => 'zuvi',
  ];
  // Main Tabs -> Color.
  $form['color'] = [
    '#type'  => 'details',
    '#title' => t('Theme Color'),
    '#group' => 'zuvi',
  ];
  // Main Tabs -> Homepage Slider.
  $form['slider'] = [
    '#type'  => 'details',
    '#title' => t('Slider'),
    '#group' => 'zuvi',
  ];

  // Main Tabs -> Header.
  $form['header'] = [
    '#type'  => 'details',
    '#title' => t('Header'),
    '#group' => 'zuvi',
  ];

  // Main Tabs -> Sidebar.
  $form['sidebar'] = [
    '#type'  => 'details',
    '#title' => t('Sidebar'),
    '#group' => 'zuvi',
  ];

  // Main Tabs -> Content.
  $form['content'] = [
    '#type'  => 'details',
    '#title' => t('Content'),
    '#group' => 'zuvi',
  ];

  // Main Tabs -> Footer.
  $form['footer'] = [
    '#type'  => 'details',
    '#title' => t('Footer'),
    '#group' => 'zuvi',
  ];
  $form['comment'] = [
    '#type'  => 'details',
    '#title' => t('Comment'),
    '#group' => 'zuvi',
  ];
  $form['components'] = [
    '#type'  => 'details',
    '#title' => t('Components'),
    '#group' => 'zuvi',
  ];
  // Insert codes
  $form['insert_codes'] = [
    '#type'  => 'details',
    '#title' => t('Insert Codes'),
    '#group' => 'zuvi',
  ];
  // Main Tabs -> Support.
  $form['support'] = [
    '#type'  => 'details',
    '#title' => t('Support'),
    '#group' => 'zuvi',
  ];

  // Main Tabs -> Upgrade to ZuviPro tab.
  $form['upgrade'] = [
    '#type'  => 'details',
    '#title' => t('Upgrade To ZuviPro'),
    '#description'  => t("<h3>Upgrade To ZuviPro For $29 only.</h3><ul><li>One time payment.</li><li>No re-curring payment</li><li>No renewal fee.</li><li>One year updates.</li><li>One year support</li></ul>"),
    '#group' => 'zuvi',
  ];

  // General -> info.
  $form['general']['general_info'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Theme Info'),
    '#description' => t('<a href="https://drupar.com/theme/zuvi" target="_blank">Theme Homepage</a> || <a href="https://demo2.drupar.com/zuvi/" target="_blank">Theme Demo</a> || <a href="https://drupar.com/zuvi-documentation" target="_blank">Theme Documentation</a> || <a href="https://drupar.com/node/889" target="_blank">Theme Support</a>'),
  ];

  $form['general']['general_info_upgrade'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Upgrade To ZuviPro for $29 only'),
    '#description' => t("<p><a href='https://drupar.com/theme/zuvipro' target='_blank'>Purchase ZuviPro</a> || <a href='https://demo2.drupar.com/zuvipro/' target='_blank'>ZuviPro Demo</a></p><p>$zuvipro</p>"),
  ];
  // Layout
  $form['layout']['layout_container'] = [
    '#type'        => 'fieldset',
    '#title'         => t('Container width (px)'),
  ];
  $form['layout']['layout_container']['container_width'] = [
    '#type'          => 'number',
    '#default_value' => theme_get_setting('container_width', 'zuvi'),
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
    '#default_value' => theme_get_setting('header_width', 'zuvi'),
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
    '#default_value' => theme_get_setting('main_width', 'zuvi'),
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
    '#default_value' => theme_get_setting('footer_width', 'zuvi'),
  ];
  // Color -> Settings.
  $form['color']['theme_color'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Theme Color'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
  ];
  // Settings under slider tab.
  // Show or hide slider on homepage.
  $form['slider']['slider_enable_option'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Enable Slider'),
    '#description'   => t("Slider will be enabled on the pages that have content in slider block region."),
  ];

  // Slider -> Bottom Wave
  $form['slider']['slider'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Hide or change Slider Bottom Wave Shape'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
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
    '#default_value'  => theme_get_setting('slider_image', 'zuvi'),
    '#description'   => t('<p>Zuvi theme has limitation of single image for slider. Separate image for each slide is available in ZuviPro. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a></p>'),
  ];
  $form['slider']['slider_style'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Slider Style'),
    '#description'   => t('Only Classic slider style is available in the free version of the theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only</a> for all slider styles.'),
  ];
  $form['slider']['slider_style']['slider_style_options'] = [
    '#type'          => 'radios',
    '#options' => array(
    	'slider_one' => t('Basic Slider (text only)'),
      'slider_two' => t('Basic Slider (text and image)'),
      'slider_three' => t('Classic Slider'),
      'slider_four' => t('Layered Slider'),
    ),
    '#default_value' => 'slider_three',
    '#disabled'   => TRUE,

  ];
  $form['slider']['slider_code'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Slider Code'),
    '#description'   => t('Create a block and place it in the slider block region. Please refer to the <a href="https://drupar.com/node/847/" target="_blank">slider documentation page</a> for more details.'),
  ];
  $form['slider']['slider_doc'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Slider Documentation'),
    '#description'   => t('<p>Please refer to the <a href="https://drupar.com/node/847/" target="_blank">slider documentation page</a> for more details.</p>'),
  ];
  // Settings under header tab.
  $form['header']['doc_links'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Documentation Links'),
    '#description'   => t('<ul>
    <li><a href="https://drupar.com/node/843" target="_blank">Change Logo</a></li>
    <li><a href="https://drupar.com/node/844" target="_blank">Change Favicon Icon</a></li>
    <li><a href="https://drupar.com/node/845/" target="_blank">Manage Main Menu</a></li>
    <li><a href="https://drupar.com/node/846/" target="_blank">Sliding Search Form</a></li>
  </ul>
  '),
  ];
  $form['header']['sticky_header'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Sticky Header'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
  ];
  // Settings under sidebar.
  $form['sidebar']['front_sidebars'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Homepage Sidebar'),
  ];
  $form['sidebar']['front_sidebars']['front_sidebar'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show Sidebars On Homepage'),
    '#default_value' => theme_get_setting('front_sidebar', 'zuvi'),
    '#description'   => t('Check this option to enable left and right sidebar on homepage.'),
  ];
  $form['sidebar']['animated_sidebar'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Animated Sidebar'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
  ];
  // Settings under content tab.
  $form['content']['demo_content'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Demo Site Content'),
    '#description'   => t('You can <a href="https://drupar.com/demo-site/zuvi" target="_blank">purchase demo site content</a> for $10 only. This contains all Drupal files and database file. We can also create demo site on your server.'),
  ];
  $form['content']['shortcodes'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Custom Shortcodes'),
    '#description'   => t('<p>Zuvi theme comes with many useful custom shortcodes. Please check the documenattion page for details.</p><p><a href="https://drupar.com/node/895/" target="_blank">Custom Shortcodes</a></p>'),
  ];
  // Settings under content tab -> Animated Content.
  $form['content']['animated_content'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Animated Page Content'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
  ];

  // Node author picture.
  $form['content']['node'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Node'),
  ];

  $form['content']['node']['node_author_pic'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Node Author Picture'),
    '#default_value' => theme_get_setting('node_author_pic', 'zuvi'),
    '#description'   => t("Check this option to show node author picture in submitted details. Uncheck to hide."),
  ];

  // Show tags in node submitted.
  $form['content']['node']['node_tags'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Node Tags'),
    '#default_value' => theme_get_setting('node_tags', 'zuvi'),
    '#description'   => t("Check this option to show node tags (if any) in submitted details. Uncheck to hide."),
  ];
  // Comments
  $form['comment']['comment_photo'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Comment User Picture'),
  ];

  $form['comment']['comment_photo']['comment_user_pic'] = [
    '#type'          => 'checkbox',
    '#title'         => t('User Picture in comments'),
    '#default_value' => theme_get_setting('comment_user_pic', 'zuvi'),
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
    '#default_value' => theme_get_setting('highlight_author_comment', 'zuvi'),
    '#description'   => t("Check this option to highlight node author comments."),
  ];
  $form['comment']['comment_author']['highlight_author_color'] = [
    '#type'          => 'details',
    '#title'         => t('Highlight Color'),
    '#description'   => t('Color option is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
    '#open' => TRUE,
  ];
  // Settings under footer tab.
  // Footer wave shape
  $form['footer']['footer_wave'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Footer Wave Shape'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
  ];
  // Scroll to top.
  $form['footer']['scrolltotop'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Scroll To Top'),
  ];

  $form['footer']['scrolltotop']['scrolltotop_on'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable scroll to top feature.'),
    '#default_value' => theme_get_setting('scrolltotop_on', 'zuvi'),
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
    '#default_value' => theme_get_setting('copyright_text', 'zuvi'),
    '#description'   => t("Check this option to show website copyright text in footer. Uncheck to hide."),
  ];

  // Footer -> Copyright -> custom copyright text
  $form['footer']['copyright']['copyright_text_custom'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Custom copyright text'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
  ];

  // Footer -> Cookie message.
  $form['footer']['cookie'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Cookie Consent message'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
  ];

  $form['footer']['cookie']['cookie_message'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Show Cookie Consent Message'),
    '#description'   => t('Make your website EU Cookie Law Compliant. According to EU cookies law, websites need to get consent from visitors to store or retrieve cookies.'),
  ];
  /*
   * Components
   */
  $form['components']['components_tab'] = [
    '#type'  => 'vertical_tabs',
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
    '#default_value' => theme_get_setting('all_icons_show', 'zuvi'),
    '#description'   => t("Check this option to show social icons in footer. Uncheck to hide."),
  ];
  // Facebook.
    $form['components']['social']['facebook'] = [
    '#type'        => 'details',
    '#title'       => t("Facebook"),
  ];

  $form['components']['social']['facebook']['facebook_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Facebook Url'),
    '#description'   => t("Enter yours facebook profile or page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('facebook_url', 'zuvi'),
  ];

  // Twitter.
  $form['components']['social']['twitter'] = [
    '#type'        => 'details',
    '#title'       => t("Twitter"),
  ];

  $form['components']['social']['twitter']['twitter_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Twitter Url'),
    '#description'   => t("Enter yours twitter page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('twitter_url', 'zuvi'),
  ];

  // Instagram.
  $form['components']['social']['instagram'] = [
    '#type'        => 'details',
    '#title'       => t("Instagram"),
  ];

  $form['components']['social']['instagram']['instagram_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Instagram Url'),
    '#description'   => t("Enter yours instagram page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('instagram_url', 'zuvi'),
  ];

  // Linkedin.
  $form['components']['social']['linkedin'] = [
    '#type'        => 'details',
    '#title'       => t("Linkedin"),
  ];

  $form['components']['social']['linkedin']['linkedin_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Linkedin Url'),
    '#description'   => t("Enter yours linkedin page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('linkedin_url', 'zuvi'),
  ];

  // YouTube.
  $form['components']['social']['youtube'] = [
    '#type'        => 'details',
    '#title'       => t("YouTube"),
  ];

  $form['components']['social']['youtube']['youtube_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('YouTube Url'),
    '#description'   => t("Enter yours youtube.com page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('youtube_url', 'zuvi'),
  ];

  // YouTube.
  $form['components']['social']['vimeo'] = [
    '#type'        => 'details',
    '#title'       => t("vimeo"),
  ];

  $form['components']['social']['vimeo']['vimeo_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('vimeo Url'),
    '#description'   => t("Enter yours vimeo.com page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('vimeo_url', 'zuvi'),
  ];

  // telegram.
    $form['components']['social']['telegram'] = [
    '#type'        => 'details',
    '#title'       => t("Telegram"),
  ];

  $form['components']['social']['telegram']['telegram_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('Telegram Url'),
    '#description'   => t("Enter yours Telegram profile or page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('telegram_url', 'zuvi'),
  ];

  // WhatsApp.
    $form['components']['social']['whatsapp'] = [
    '#type'        => 'details',
    '#title'       => t("WhatsApp"),
  ];

  $form['components']['social']['whatsapp']['whatsapp_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('WhatsApp Url'),
    '#description'   => t("Enter yours whatsapp message url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('whatsapp_url', 'zuvi'),
  ];

  // Github.
    $form['components']['social']['github'] = [
    '#type'        => 'details',
    '#title'       => t("GitHub"),
  ];

  $form['components']['social']['github']['github_url'] = [
    '#type'          => 'textfield',
    '#title'         => t('GitHub Url'),
    '#description'   => t("Enter yours github page url. Leave the url field blank to hide this icon."),
    '#default_value' => theme_get_setting('github_url', 'zuvi'),
  ];

  // Social -> vk.com url.
  $form['components']['social']['vk'] = [
    '#type'        => 'details',
    '#title'       => t("vk.com"),
  ];
  $form['components']['social']['vk']['vk_url'] = [
      '#type'          => 'textfield',
      '#title'         => t('vk.com'),
      '#description'   => t("Enter yours vk.com page url. Leave the url field blank to hide this icon."),
      '#default_value' => theme_get_setting('vk_url', 'zuvi'),
  ];

  // Social -> New Social Icons
  $form['components']['social']['social_new_icon'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Add More Social Icons'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
  ];
  $form['components']['social']['node_share'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Share Page On Social networking websites'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
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
    '#default_value' => theme_get_setting('fontawesome_four', 'zuvi'),
    '#description'   => t('Check this option to enable fontawesome version 4 font icons.'),
  ];
  $form['components']['font_icons']['fontawesome5'] = [
    '#type'          => 'fieldset',
    '#title'         => t('FontAwesome 5'),
  ];
  $form['components']['font_icons']['fontawesome5']['fontawesome_five'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable FontAwesome 5 Font Icons'),
    '#default_value' => theme_get_setting('fontawesome_five', 'zuvi'),
    '#description'   => t('Check this option to enable fontawesome version 5 font icons.'),
  ];
  $form['components']['font_icons']['fontawesome6'] = [
    '#type'          => 'fieldset',
    '#title'         => t('FontAwesome 6'),
  ];
  $form['components']['font_icons']['fontawesome6']['fontawesome_six'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable FontAwesome 6 Font Icons'),
    '#default_value' => theme_get_setting('fontawesome_six', 'zuvi'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
    '#disabled'   => TRUE,
  ];
	$form['components']['font_icons']['bootstrap_icons'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Bootstrap Font Icons'),
  ];
  $form['components']['font_icons']['bootstrap_icons']['bootstrapicons'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable Bootstrap Icons'),
    '#default_value' => theme_get_setting('bootstrapicons', 'zuvi'),
    '#description'   => t('Check this option to enable Bootstrap Font Icons. Read more about <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a>'),
  ];
  $form['components']['font_icons']['materialicons'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Google Material Font Icons'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Purchase ZuviPro for $29 only.</a>'),
  ];
  $form['components']['cursor'] = [
    '#type'        => 'details',
    '#title'       => t('Fancy Cursor'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
    '#group' => 'components_tab',
  ];
  $form['components']['node_share'] = [
    '#type'        => 'details',
    '#title'       => t('Share Page'),
    '#description'   => t('<strong>Share Page On Social networking websites</strong><p>This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a></p>'),
    '#group' => 'components_tab',
  ];
  // Components -> Page loader.
  $form['components']['preloader'] = [
    '#type'        => 'details',
    '#title'       => t('Pre Page Loader'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
    '#group' => 'components_tab',
  ];
  // Components -> Cookie message.
  $form['components']['cookie'] = [
    '#type'        => 'details',
    '#title'       => t('Cookie Consent message'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
    '#group' => 'components_tab',
  ];

  $form['components']['cookie']['cookie_message'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Show Cookie Consent Message'),
    '#description'   => t('Make your website EU Cookie Law Compliant. According to EU cookies law, websites need to get consent from visitors to store or retrieve cookies.'),
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
    '#description' => t('<h3>Insert Codes Before &lt;/HEAD&gt;</h3><hr />'),
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
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
  ];
  // Insert Codes -> Body -> Body start codes
  $form['insert_codes']['body']['insert_body_start_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Insert code after &lt;BODY&gt; tag'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
  ];
  // Insert Codes -> Body -> Body ENd codes
  $form['insert_codes']['body']['insert_body_end_section'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Insert code before &lt;/BODY&gt; tag'),
    '#description'   => t('This feature is available in the premium version of this theme. <a href="https://drupar.com/theme/zuvipro" target="_blank">Buy ZuviPro for $29 only.</a>'),
  ];
  // Insert Codes -> css
  $form['insert_codes']['css']['custom'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Custom Styling'),
  ];

  $form['insert_codes']['css']['custom']['styling'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable custom css'),
    '#default_value' => theme_get_setting('styling', 'zuvi'),
    '#description'   => t("Check this option to enable custom styling. Uncheck to disable this fearure.<br />Please refer to this tutorial page. <a href='https://drupar.com/zuvi-documentation/custom-css' target='_blank'>How To Use Custom Styling</a>"),
  ];

  $form['insert_codes']['css']['custom']['styling_code'] = [
    '#type'          => 'textarea',
    '#title'         => t('Custom CSS Codes'),
    '#default_value' => theme_get_setting('styling_code', 'zuvi'),
    '#description'   => t('Please enter your custom css codes in this text box. You can use it to customize the appearance of your site.<br />Please refer to this tutorial for detail: <a href="https://drupar.com/zuvi-documentation/custom-css" target="_blank">Custom CSS</a>'),
  ];

  // Settings under support tab.
  $form['support']['info'] = [
    '#type'        => 'fieldset',
    '#title'         => t('Support'),
    '#description' => t('<h4>Documentation</h4>
    <p>We have a detailed documentation about how to use theme. Please read the <a href="https://drupar.com/zuvi-documentation" target="_blank">Zuvi Theme Documentation</a>.</p>
    <hr />
    <h4>Open An Issue</h4>
    <p>If you need support that is beyond our theme documentation, please <a href="https://www.drupal.org/project/issues/zuvi" target="_blank">create an issue</a> at Drupal.org project page.</p>
    <hr />
    <h4>Contact Us</h4>
    <p>If you need some specific customization in theme, please contact us<br><a href="https://drupar.com/contact" target="_blank">drupar.com/contact</a></p>'),
  ];

  // Settings under upgrade tab.
  $form['upgrade']['info'] = [
    '#type'        => 'fieldset',
    '#title'       => t('<a href="https://demo2.drupar.com/zuvipro/" target="_blank">ZuviPro Demo</a> | <a href="https://drupar.com/theme/zuvipro" target="_blank">Purchase ZuviPro for $29 only</a>'),
    '#description' => $zuvipro,
  ];
// End form.
}
