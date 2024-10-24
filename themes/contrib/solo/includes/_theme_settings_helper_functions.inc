<?php

/**
 * @file
 * Solo Theme.
 *
 * Filename:     solo.theme
 * Website:      http://www.flashwebcenter.com
 * Description:  template
 * Author:       Alaa Haddad http://www.alaahaddad.com.
 */

/**
 * Helper function.
 */
function _get_site_width_options() {
  return [
    'sw-800' => t('800px / 50rem'),
    'sw-1024' => t('1024px / 64rem'),
    'sw-1280' => t('1280px / 80rem'),
    'sw-1440' => t('1440px / 90rem'),
    'sw-1600' => t('1600px / 100rem'),
    'sw-1920' => t('1920px / 120rem'),
    'sw-2560' => t('2560px / 160rem'),
    'sw-100' => t('100%'),
  ];
}

/**
 * Helper function.
 */
function _get_site_breakpoints_options() {
  return [
    'sb-576' => t('576px / 36rem'),
    'sb-768' => t('768px / 48rem'),
    'sb-992' => t('992px / 62rem'),
    'sb-1200' => t('1200px / 75rem'),
    'sb-1400' => t('1400px / 87.5rem'),
  ];
}

/**
 * Helper function.
 */
function _get_menu_breakpoints_options() {
  return [
    'mn-576' => t('576px / 36rem'),
    'mn-768' => t('768px / 48rem'),
    'mn-992' => t('992px / 62rem'),
    'mn-1200' => t('1200px / 75rem'),
    'mn-1400' => t('1400px / 87.5rem'),
  ];
}

/**
 * Helper function.
 */
function _get_site_global_regions_gap() {
  return [
    '16' => t('Default 16px / 1rem'),
    '24' => t('24px / 1.5rem'),
    '36' => t('36px / 2.25rem'),
    '48' => t('48px / 3rem'),
    '64' => t('64px / 4rem'),
    'no' => t('None'),
  ];
}

/**
 * Helper function.
 */
function _get_site_global_font_size() {
  return [
    '16' => t('Default 16px / 1rem'),
    '18' => t('18px / 1.125rem'),
    '20' => t('20px / 1.25rem'),
  ];
}

/**
 * Helper function.
 */
function _get_site_name_font_size() {
  return [
    '' => t('The Default Settings'),
    '18' => t('Large 18px / 1.125rem'),
    '24' => t('X Large 24px / 1.5rem'),
    '36' => t('XX Large 36px / 2.25rem'),
    '48' => t('XXX Large 48px / 3rem'),
    '64' => t('Jumbo 64px / 4rem'),
  ];
}

/**
 * Helper function.
 */
function _get_page_title_font_size() {
  return [
    '' => t('The Default Settings'),
    '18' => t('Large 18px / 1.125rem'),
    '24' => t('X Large 24px / 1.5rem'),
    '36' => t('XX Large 36px / 2.25rem'),
    '48' => t('XXX Large 48px / 3rem'),
    '64' => t('Jumbo 64px / 4rem'),
  ];
}

/**
 * Helper function.
 */
function _get_site_node_date_format($d_s, $d_m, $d_l) {
  return [
    'solo_date_s' => t("Short: (@d_s)", ['@d_s' => $d_s]),
    'solo_date_m' => t("Medium: (@d_m)", ['@d_m' => $d_m]),
    'solo_date_l' => t("Long: (@d_l)", ['@d_l' => $d_l]),
  ];
}

/**
 * Helper function.
 */
function _get_animate_site_name() {
  return [
    '' => t('Animation Effects - None'),
    'animate-site-aurora-wave' => t('Aurora Wave'),
    'animate-site-cosmic-drift' => t('Cosmic Drift'),
    'animate-site-dreamy-bloom' => t('Dreamy Bloom'),
    'animate-site-echo-pulse' => t('Echo Pulse'),
    'animate-site-electric-surge' => t('Electric Surge'),
    'animate-site-gliding-letters' => t('Gliding Letters'),
    'animate-site-gravity-defy' => t('Gravity Defy'),
    'animate-site-holographic-spin' => t('Holographic Spin'),
    'animate-site-moonlight-dance' => t('Moonlight Dance'),
    'animate-site-mystic-fade' => t('Mystic Fade'),
    'animate-site-phoenix-rise' => t('Phoenix Rise'),
    'animate-site-quantum-leap' => t('Quantum Leap'),
    'animate-site-rhythmic-slide' => t('Rhythmic Slide'),
    'animate-site-sapphire-zoom' => t('Sapphire Zoom'),
    'animate-site-serene-float' => t('Serene Float'),
    'animate-site-shimmering-wave' => t('Shimmering Wave'),
    'animate-site-solar-flare' => t('Solar Flare'),
    'animate-site-starry-glide' => t('Starry Glide'),
    'animate-site-swirling-entrance' => t('Swirling Entrance'),
    'animate-site-timeless-flip' => t('Timeless Flip'),
    'animate-site-velvet-slide' => t('Velvet Slide'),
    'animate-site-whisper-fade' => t('Whisper Fade'),
    'animate-site-whispering-shadows' => t('Whispering Shadows'),
    'animate-site-zen-rotation' => t('Zen Rotation'),
  ];
}

/**
 * Helper function.
 */
function _get_animate_page_title() {
  return [
    '' => t('Animation Effects - None'),
    'animate-title-aurora-wave' => t('Aurora Wave'),
    'animate-title-cosmic-drift' => t('Cosmic Drift'),
    'animate-title-dreamy-bloom' => t('Dreamy Bloom'),
    'animate-title-echo-pulse' => t('Echo Pulse'),
    'animate-title-electric-surge' => t('Electric Surge'),
    'animate-title-gliding-letters' => t('Gliding Letters'),
    'animate-title-gravity-defy' => t('Gravity Defy'),
    'animate-title-holographic-spin' => t('Holographic Spin'),
    'animate-title-moonlight-dance' => t('Moonlight Dance'),
    'animate-title-mystic-fade' => t('Mystic Fade'),
    'animate-title-phoenix-rise' => t('Phoenix Rise'),
    'animate-title-quantum-leap' => t('Quantum Leap'),
    'animate-title-rhythmic-slide' => t('Rhythmic Slide'),
    'animate-title-sapphire-zoom' => t('Sapphire Zoom'),
    'animate-title-serene-float' => t('Serene Float'),
    'animate-title-shimmering-wave' => t('Shimmering Wave'),
    'animate-title-solar-flare' => t('Solar Flare'),
    'animate-title-starry-glide' => t('Starry Glide'),
    'animate-title-swirling-entrance' => t('Swirling Entrance'),
    'animate-title-timeless-flip' => t('Timeless Flip'),
    'animate-title-velvet-slide' => t('Velvet Slide'),
    'animate-title-whisper-fade' => t('Whisper Fade'),
    'animate-title-whispering-shadows' => t('Whispering Shadows'),
    'animate-title-zen-rotation' => t('Zen Rotation'),
  ];
}

/**
 * Helper function.
 */
function _get_theme_category() {
  return [
    'none' => t('None'),
    'elegant_whites_and_neutrals' => t('Elegant Whites and Neutrals'),
    'bold_and_dramatic_colors' => t('Bold and Dramatic Colors'),
    'denim_influences' => t('Denim Influences'),
    'earthy_and_smokey_tones' => t('Earthy and Smokey Tones'),
    'midnight_and_twilight' => t('Midnight and Twilight Themes'),
    'berry_and_fruit_tones' => t('Berry and Fruit Tones'),
    'soft_and_subtle_hues' => t('Soft and Subtle Hues'),
    'galactic_and_celestial' => t('Galactic and Celestial Themes'),
    'urban_and_modern_hues' => t('Urban and Modern Hues'),
    'ocean_and_deep_sea' => t('Ocean and Deep Sea Themes'),
    'forest_and_nature_tones' => t('Forest and Nature Tones'),
    'miscellaneous' => t('Miscellaneous'),
  ];
}

/**
 * Helper function.
 */
function _get_predefined_theme() {
  return [
    'midnight_and_twilight|solo-midnight-whispers' => t('Midnight Whispers'),
    'midnight_and_twilight|solo-midnight-garden-trio' => t('Midnight Garden Trio'),
    'midnight_and_twilight|solo-midnight-denim-swirl' => t('Midnight Denim Swirl'),
    'midnight_and_twilight|solo-mystic-ash-twilight' => t('Mystic Ash Twilight'),
    'midnight_and_twilight|solo-twilight-ash-mystique' => t('Twilight Ash Mystique'),

    'ocean_and_deep_sea|solo-ocean-depths-mystery' => t('Ocean Depths Mystery'),
    'ocean_and_deep_sea|solo-deep-sea-echo' => t('Deep Sea Echo'),
    'ocean_and_deep_sea|solo-deep-ocean-shadows' => t('Deep Ocean Shadows'),

    'berry_and_fruit_tones|solo-berry-blue-dreams' => t('Berry Blue Dreams'),
    'berry_and_fruit_tones|solo-raspberry-nightfall' => t('Raspberry Nightfall'),
    'berry_and_fruit_tones|solo-lemon-lime-forest' => t('Lemon Lime Forest'),
    'berry_and_fruit_tones|solo-blueberry-skies-quartet' => t('Blueberry Skies Quartet'),

    'urban_and_modern_hues|solo-urban-stone-shadows' => t('Urban Stone Shadows'),
    'urban_and_modern_hues|solo-parisian-skyline' => t('Parisian Skyline'),
    'urban_and_modern_hues|solo-nightfall-paris' => t('Nightfall in Paris'),

    'forest_and_nature_tones|solo-enchanted-forest-zest' => t('Enchanted Forest Zest'),
    'forest_and_nature_tones|solo-jungle-denim-fusion' => t('Jungle Denim Fusion'),

    'galactic_and_celestial|solo-galactic-stone-hues' => t('Galactic Stone Hues'),
    'galactic_and_celestial|solo-metallic-nebula' => t('Metallic Nebula'),
    'galactic_and_celestial|solo-celestial-blue-depths' => t('Celestial Blue Depths'),

    'elegant_whites_and_neutrals|solo-pristine-porcelain-glow' => t('Pristine Porcelain Glow'),
    'elegant_whites_and_neutrals|solo-porcelain-brilliance' => t('Porcelain Brilliance'),
    'elegant_whites_and_neutrals|solo-luminous-alabaster' => t('Luminous Alabaster'),
    'elegant_whites_and_neutrals|solo-ivory-whisper' => t('Ivory Whisper'),
    'elegant_whites_and_neutrals|solo-soft-silk-elegance' => t('Soft Silk Elegance'),
    'elegant_whites_and_neutrals|solo-gentle-ivory-breeze' => t('Gentle Ivory Breeze'),
    'elegant_whites_and_neutrals|solo-ethereal-white-harmony' => t('Ethereal White Harmony'),
    'elegant_whites_and_neutrals|solo-classic-ivory-lace' => t('Classic Ivory Lace'),
    'elegant_whites_and_neutrals|solo-silken-shadow-whisper' => t('Silken Shadow Whisper'),

    'bold_and_dramatic_colors|solo-eclipse-noir' => t('Eclipse Noir'),
    'bold_and_dramatic_colors|solo-nocturnal-velvet' => t('Nocturnal Velvet'),
    'bold_and_dramatic_colors|solo-royal-azure-flame' => t('Royal Azure Flame'),
    'bold_and_dramatic_colors|solo-flaming-sapphire' => t('Flaming Sapphire'),
    'bold_and_dramatic_colors|solo-rustic-redwood-charm' => t('Rustic Redwood Charm'),
    'bold_and_dramatic_colors|solo-crimson-denim-night' => t('Crimson Denim Night'),
    'bold_and_dramatic_colors|solo-razzle-onyx-elegance' => t('Razzle Onyx Elegance'),
    'bold_and_dramatic_colors|solo-midnight-navy-duo' => t('Midnight Navy Duo'),

    'soft_and_subtle_hues|solo-blushing-magnolia' => t('Blushing Magnolia'),
    'soft_and_subtle_hues|solo-magnolia-afternoon' => t('Magnolia Afternoon'),
    'soft_and_subtle_hues|solo-cotton-candy-whispers' => t('Cotton Candy Whispers'),

    'denim_influences|solo-denim-twilight' => t('Denim Twilight'),
    'denim_influences|solo-indigo-pulse' => t('Indigo Pulse'),
    'denim_influences|solo-crimson-denim-night' => t('Crimson Denim Night'),
    'denim_influences|solo-jungle-denim-fusion' => t('Jungle Denim Fusion'),
    'denim_influences|solo-midnight-denim-swirl' => t('Midnight Denim Swirl'),
    'denim_influences|solo-salmon-denim-fade' => t('Salmon Denim Fade'),

    'earthy_and_smokey_tones|solo-smokey-quartz-symphony' => t('Smokey Quartz Symphony'),
    'earthy_and_smokey_tones|solo-gunmetal-echo' => t('Gunmetal Echo'),
    'earthy_and_smokey_tones|solo-dusk-shadow-fusion' => t('Dusk Shadow Fusion'),
    'earthy_and_smokey_tones|solo-porpoise-dream' => t('Porpoise Dream'),
    'earthy_and_smokey_tones|solo-obsidian-cascade' => t('Obsidian Cascade'),
    'earthy_and_smokey_tones|solo-charcoal-gray' => t('Charcoal Gray'),

    'miscellaneous|solo-monochrome-splash' => t('Monochrome Splash'),
  ];
}

/**
 * Helper function.
 */
function _get_global_font_options() {
  return [
    '' => t('None'),
    'solo-abel' => t('Abel - sans-serif'),
    'solo-alegreya-sans' => t('Alegreya Sans - sans-serif'),
    'solo-alegreya-sans-sc' => t('Alegreya Sans SC - sans-serif'),
    'solo-amita' => t('Amita - cursive'),
    'solo-archivo-narrow' => t('Archivo Narrow - sans-serif'),
    'solo-arimo' => t('Arimo - sans-serif'),
    'solo-arvo' => t('Arvo - serif'),
    'solo-barlow' => t('Barlow - sans-serif'),
    'solo-barlow-semi-condensed' => t('Barlow Semi Condensed - sans-serif'),
    'solo-bitter' => t('Bitter - serif'),
    'solo-cairo' => t('Cairo - sans-serif'),
    'solo-comfortaa' => t('Comfortaa - cursive'),
    'solo-courgette' => t('Courgette - cursive'),
    'solo-domine' => t('Domine - serif'),
    'solo-dosis' => t('Dosis - sans-serif'),
    'solo-exo-2' => t('EXO 2 - sans-serif'),
    'solo-fira-sans' => t('Fira Sans - sans-serif'),
    'solo-glegoo' => t('Glegoo - serif'),
    'solo-hind-siliguri' => t('Hind Siliguri - sans-serif'),
    'solo-ibm-plex-sans' => t('IBM Plex Sans - sans-serif'),
    'solo-inconsolata' => t('Inconsolata - monospace'),
    'solo-josefin-sans' => t('Josefin Sans - sans-serif'),
    'solo-kanit' => t('Kanit - sans-serif'),
    'solo-kavivanar' => t('Kavivanar - cursive'),
    'solo-lato' => t('Lato - sans-serif'),
    'solo-libre-baskerville' => t('Libre Baskerville - serif'),
    'solo-lora' => t('Lora - serif'),
    'solo-merienda' => t('Merienda - cursive'),
    'solo-merriweather' => t('Merriweather - serif'),
    'solo-montserrat' => t('Montserrat - sans-serif'),
    'solo-mukta' => t('Mukta - sans-serif'),
    'solo-nunito' => t('Nunito - sans-serif'),
    'solo-open-sans' => t('Open Sans - sans-serif'),
    'solo-oranienbaum' => t('Oranienbaum - serif'),
    'solo-oswald' => t('Oswald - sans-serif'),
    'solo-oxygen' => t('Oxygen - sans-serif'),
    'solo-playfair-display' => t('Playfair Display - serif'),
    'solo-poppins' => t('Poppins - sans-serif'),
    'solo-prompt' => t('Prompt - sans-serif'),
    'solo-pt-serif' => t('PT Serif - serif'),
    'solo-quicksand' => t('Quicksand - sans-serif'),
    'solo-raleway' => t('Raleway - sans-serif'),
    'solo-roboto' => t('Roboto - sans-serif'),
    'solo-roboto-condensed' => t('Roboto Condensed - sans-serif'),
    'solo-roboto-mono' => t('Roboto Mono - monospace'),
    'solo-source-sans-3' => t('Source Sans 3 - sans-serif'),
    'solo-spline-sans' => t('Spline Sans - sans-serif'),
    'solo-titillium-web' => t('Titillium Web - sans-serif'),
    'solo-ubuntu' => t('Ubuntu - sans-serif'),
    'solo-work-sans' => t('Work Sans - sans-serif'),
  ];
}

/**
 * Helper function.
 */
function _get_heading_font_options() {
  return [
    '' => t('None'),
    'solo-abril-fatface' => t('Abril Fatface - cursive'),
    'solo-aladin' => t('Aladin - cursive'),
    'solo-anton' => t('Anton - sans-serif'),
    'solo-archivo-black' => t('Archivo Black - sans-serif'),
    'solo-bebas-neue' => t('Bebas Neue - cursive'),
    'solo-big-shoulders-stencil-display' => t('Big Shoulders Stencil Display - cursive'),
    'solo-big-shoulders-stencil-text' => t('Big Shoulders Stencil Text - cursive'),
    'solo-black-ops-one' => t('Black Ops One - cursive'),
    'solo-bree-serif' => t('Bree Serif - serif'),
    'solo-cinzel' => t('Cinzel - serif'),
    'solo-dancing-script' => t('Dancing Script - cursive'),
    'solo-dm-serif-display' => t('DM Serif Display - serif'),
    'solo-doppio-one' => t('Doppio - sans-serif'),
    'solo-fjalla-one' => t('Fjalla One - sans-serif'),
    'solo-francois-one' => t('Francois One - sans-serif'),
    'solo-fredericka-the-great' => t('Fredericka The Great - cursive'),
    'solo-fredoka' => t('Fredoka - cursive'),
    'solo-hammersmith-one' => t('Hammersmith One - sans-serif'),
    'solo-indie-flower' => t('Indie Flower - cursive'),
    'solo-kalam' => t('Kalam - cursive'),
    'solo-lobster-two' => t('Lobster Two - cursive'),
    'solo-patrick-hand' => t('Patrick Hand - cursive'),
    'solo-prosto-one' => t('Prosto One - cursive'),
    'solo-righteous' => t('Righteous - cursive'),
    'solo-staatliches' => t('Staatliches - cursive'),
    'solo-yanone-kaffeesatz' => t('Yanone Kaffeesatz - sans-serif'),
  ];
}

/**
 * Helper function.
 */
function _get_special_font_options() {
  return [
    '' => t('None'),
    'solo-akronim' => t('akronim - cursive'),
    'solo-barriecito' => t('barriecito - cursive'),
    'solo-bungee-shade' => t('bungee-shade - cursive'),
    'solo-ceviche-one' => t('ceviche-one - cursive'),
    'solo-creepster' => t('creepster - cursive'),
    'solo-faster-one' => t('faster-one - cursive'),
    'solo-finger-paint' => t('finger-paint - cursive'),
    'solo-frijole' => t('frijole - cursive'),
    'solo-irish-grover' => t('irish-grover - cursive'),
    'solo-kumar-one' => t('kumar-one - cursive'),
    'solo-londrina-outline' => t('londrina-outline - cursive'),
    'solo-megrim' => t('megrim - cursive'),
    'solo-metal-mania' => t('metal-mania - cursive'),
    'solo-monoton' => t('monoton - cursive'),
    'solo-moo-lah-lah' => t('moo-lah-lah - cursive'),
    'solo-new-rocker' => t('new-rocker - cursive'),
    'solo-nosifer' => t('nosifer - cursive'),
    'solo-pirata-one' => t('pirata-one - system-ui'),
    'solo-ruslan-display' => t('ruslan-display - cursive'),
    'solo-rye' => t('rye - cursive'),
    'solo-sancreek' => t('sancreek - cursive'),
    'solo-slackey' => t('slackey - cursive'),
    'solo-trade-winds' => t('trade-winds - cursive'),
    'solo-train-one' => t('train-one - cursive'),
    'solo-unifrakturmaguntia' => t('unifrakturmaguntia - cursive'),
  ];
}

/**
 * Helper function.
 */
function _get_name_title_google_font() {
  return [
    '' => t('None'),
    'solo-acme' => t('Acme - sans-serif'),
    'solo-audiowide' => t('Audiowide - sans-serif'),
    'solo-berkshire-swash' => t('Berkshire Swash - serif'),
    'solo-contrail-one' => t('Contrail One - sans-serif'),
    'solo-cookie' => t('Cookie - cursive'),
    'solo-croissant-one' => t('Croissant One - serif'),
    'solo-crushed' => t('Crushed - sans-serif'),
    'solo-eagle-lake' => t('Eagle Lake - serif'),
    'solo-fugaz-one' => t('Fugaz One - sans-serif'),
    'solo-gilda-display' => t('Gilda Display - serif'),
    'solo-girassol' => t('Girassol - serif'),
    'solo-italiana' => t('Italiana - sans-serif'),
    'solo-joan' => t('Joan - sans-serif'),
    'solo-julius-sans-one' => t('Julius Sans One - sans-serif'),
    'solo-katibeh' => t('Katibeh - serif'),
    'solo-kaushan-script' => t('Kaushan Script - cursive'),
    'solo-overlock-sc' => t('Overlock SC - sans-serif'),
    'solo-poiret-one' => t('Poiret One - sans-serif'),
    'solo-prata' => t('Prata - serif'),
    'solo-redressed' => t('Redressed - cursive'),
    'solo-tapestry' => t('Tapestry - serif'),
    'solo-uncial-antiqua' => t('Uncial Antiqua - system-ui'),
    'solo-unica-one' => t('Unica One - sans-serif'),
    'solo-yatra-one' => t('Yatra One - system-ui'),
    'solo-yeseva-one' => t('Yeseva One - serif'),
  ];
}

/**
 * Helper function.
 */
function _generate_form_details($label) {
  return [
    '#type' => 'details',
    '#title' => t("<strong class='info'>@label</strong> Settings - Solo Theme", ['@label' => $label]),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#description'   => t('This section is related to general configurations.'),
  ];
}

/**
 * Helper function.
 */
function _generate_form_element($region, $label, $attribute_key, $attribute_label) {
  return [
    '#type' => 'textfield',
    '#maxlength' => 7,
    '#size' => 10,
    '#title' => t("(@attribute_label)", ['@attribute_label' => $attribute_label]),
    '#default_value' => theme_get_setting("settings_{$region}_{$attribute_key}"),
    '#attributes' => [
      'pattern' => '^#[a-fA-F0-9]{6}',
    ],
    '#wrapper_attributes' => [
      'data-drupal-selector' => 'solo-color-picker',
    ],
  ];
}

/**
 * Helper function.
 */
function _generate_css_classes($region, $label) {
  return [
    '#type' => 'textfield',
    '#title' => t('CSS Classes Only'),
    '#description' => t('Insert any CSS classes here. The CSS should be input by a trusted user (an admin or developer).'),
    '#default_value' => theme_get_setting("classes_{$region}"),
  ];

}

/**
 * Helper function.
 */
function _animate_region_border($region) {
  return [
    '#type' => 'checkbox',
    '#title' => t("Tick this box to initiate animation of the region's border."),
    '#default_value' => theme_get_setting("site_regions_{$region}_animate_border"),
  ];
};

/**
 * Helper function.
 */
function _animate_region_hover($region) {
  return [
    '#type' => 'checkbox',
    '#title' => t("Tick this box to initiate on hover animation for the region."),
    '#default_value' => theme_get_setting("site_regions_{$region}_animate_hover"),
  ];
};

/**
 * Helper function.
 */
function _border_region($region) {
  return [
    '#type' => 'checkbox',
    '#title' => t('Tick this box if you prefer to apply a border to the inside regions'),
    '#default_value' => theme_get_setting("site_regions_{$region}_border"),
  ];
};

/**
 * Helper function.
 */
function _rounded_region($region) {
  return [
    '#type' => 'select',
    '#title' => t("Select from this list to enable rounding of the inside regions' corners."),
    '#default_value' => theme_get_setting("site_regions_{$region}_rounded") ?? '',
    '#options' => [
      '' => t('None'),
      'radius-4' => t('4px / 0.25rem'),
      'radius-8' => t('8px / 0.5rem'),
      'radius-16' => t('16px / 1rem'),
      'radius-24' => t('24px / 1.5rem'),
    ],
  ];
};

/**
 * Helper function.
 */
function _disable_region($region) {
  return [
    '#type' => 'select',
    '#title' => t("Change the visibility for @region region", ['@region' => $region]),
    '#default_value' => theme_get_setting("site_regions_{$region}_disable") ?? 0,
    '#options' => [
      0 => t('Visible on all pages'),
      1 => t('Visible on Home Page Only.'),
      2 => t('Visible on All Pages Except Home Page.'),
    ],
  ];
};

/**
 * Helper function.
 */
function _get_active_regions() {

  $active_theme = \Drupal::theme()->getActiveTheme()->getName();
  $theme_regions = system_region_list($active_theme);
  $block_storage = \Drupal::entityTypeManager()->getStorage('block');

  foreach ($theme_regions as $machine_name => $label_name) {
    $blocks = $block_storage->loadByProperties([
      'theme' => $active_theme,
      'region' => $machine_name,
    ]);

    if (!empty($blocks)) {
      $active_regions[] = $machine_name;
    }
  }

  return $active_regions;
}

/**
 * Helper function.
 */
function _get_updated_regions() {
  $active_regions = _get_active_regions();
  $updated_regions_all = [];
  $updated_regions_mix = [];
  $updated_regions = [
    'top' => [],
    'main' => [],
    'bottom' => [],
    'footer' => [],
  ];

  $region_config = [
    'top' => [
      'regions' => ['top_first', 'top_second', 'top_third'],
      'container' => 'Top Container Wrapper',
      'labels' => ['Top First Container', 'Top Second Container',
        'Top Third Container',
      ],
    ],
    'main' => [
      'regions' => ['sidebar_first', 'content', 'sidebar_second'],
      'container' => 'Main Container Wrapper',
      'labels' => ['Sidebar First', 'Content', 'Sidebar Second'],
    ],
    'bottom' => [
      'regions' => ['bottom_first', 'bottom_second', 'bottom_third',
        'bottom_fourth',
      ],
      'container' => 'Bottom Container Wrapper',
      'labels' => ['Bottom First Container', 'Bottom Second Container',
        'Bottom Third Container', 'Bottom Fourth Container',
      ],
    ],
    'footer' => [
      'regions' => ['footer_first', 'footer_second', 'footer_third'],
      'container' => 'Footer Container Wrapper',
      'labels' => ['Footer First Container', 'Footer Second Container',
        'Footer Third Container',
      ],
    ],
  ];

  foreach ($active_regions as $key => $region) {

    $label = ucwords(str_replace('_', ' ', $region));

    foreach ($region_config as $section => $config) {
      if (in_array($region, $config['regions'])) {
        $updated_regions[$section]['container'] = $config['container'];
        $updated_regions_all['container'] = $config['container'];

        $index = array_search($region, $config['regions']);
        $updated_regions[$section][$region] = $config['labels'][$index];
      }
    }

    if (!in_array($region, array_merge(...array_column($region_config, 'regions')))) {
      $updated_regions_mix[$region] = $label;
    }

    $updated_regions_all[$region] = $label;

  }

  return array_merge([
    'all' => $updated_regions_all,
    'mix' => $updated_regions_mix,
  ],
    $updated_regions);
}

/**
 * Helper function.
 */
function _count_regions() {

  $active_theme = \Drupal::theme()->getActiveTheme()->getName();
  $theme_regions = system_region_list($active_theme);
  $block_storage = \Drupal::entityTypeManager()->getStorage('block');
  $config = \Drupal::service('config.factory')->getEditable('solo.settings');

  $regions = [
    'top' => ['top_first', 'top_second', 'top_third'],
    'main' => ['sidebar_first', 'content', 'sidebar_second'],
    'bottom' => ['bottom_first', 'bottom_second', 'bottom_third',
      'bottom_fourth',
    ],
    'footer' => ['footer_first', 'footer_second', 'footer_third'],
  ];

  $counts = [
    'top' => 0,
    'main' => 0,
    'bottom' => 0,
    'footer' => 0,
  ];

  foreach ($theme_regions as $region => $description) {
    $blocks = $block_storage->loadByProperties([
      'theme' => $active_theme,
      'region' => $region,
    ]);

    if (!empty($blocks) && array_filter($blocks, function ($block) {
      return $block->status();
    })) {
      foreach ($regions as $key => $region_group) {
        if (in_array($region, $region_group)) {
          $counts[$key]++;
        }
      }
    }
  }

  $config->set('count_top', $counts['top'])->set('count_main', $counts['main'])->set('count_bottom', $counts['bottom'])->set('count_footer', $counts['footer'])->save();

  \Drupal::messenger()->addMessage(t("<p class='settings-message'>
    <ul class='settings-message'>
        <li>In the <a href='/admin/structure/block'>Regions</a> section, regions are classified into three types: <span class='status'>Enabled</span>, <span class='info'>Disabled</span>, and <span class='error'>Empty</span>. An 'Enabled' region contains at least one active block. A 'Disabled' region has blocks, but all are inactive. An 'Empty' region does not contain any blocks.</li>
        <li>Every region possesses distinct settings. Regions marked as <span class='error'>empty</span> won't be displayed on this page.</li>
        <li>Region status overview: Top Container - <span class='status'>@top</span> enabled region(s), Main Container - <span class='status'>@main</span> enabled, Bottom Container - <span class='status'>@bottom</span> enabled, Footer Container - <span class='status'>@footer</span> enabled.</li>
    </ul>
</p>",
    [
      '@top' => $counts['top'],
      '@main' => $counts['main'],
      '@bottom' => $counts['bottom'],
      '@footer' => $counts['footer'],
    ]
  ));

  return $counts;

}

/**
 * Helper function.
 */
function _create_region_layout_radio_four($region) {

  return [
    '#type' => 'select',
    '#title' => t("The four columns' settings for @region region", ['@region' => $region]),
    '#default_value' => theme_get_setting("{$region}_4col") ?? 'solo-col-1-1-1-1',
    '#options' => [
      'solo-col-1-1-1-1' => t('25%, 25%, 25%, 25%'),
      'solo-col-1-2-2-1' => t('16.6%, 33.3%, 33.3%, 16.6%'),
      'solo-col-2-1-1-2' => t('33.3%, 16.6%, 16.6%, 33.3%'),
      'solo-col-1-2-1-2' => t('16.6%, 33.3%, 16.6%, 33.3%'),
    ],
  ];
}

/**
 * Helper function.
 */
function _create_region_layout_radio_three($region) {
  if ($region == 'main') {
    $def = 'solo-col-1-2-1';
  }
  else {
    $def = 'solo-col-1-1-1';
  }
  return [
    '#type' => 'select',
    '#title' => t("The three columns' settings for @region region", ['@region' => $region]),
    '#default_value' => theme_get_setting("{$region}_3col") ?? $def,
    '#options' => [
      'solo-col-1-1-1' => t('33.3%, 33.3%, 33.3%'),
      'solo-col-1-2-1' => t('25%, 50%, 25%'),
      'solo-col-1-3-1' => t('20%, 60%, 20%'),
    ],
  ];
}

/**
 * Helper function.
 */
function _get_which_main_col($variables) {
  if (empty($variables['page']['sidebar_first'])) {
    return 'solo-col-2-1';
  }
  elseif (empty($variables['page']['sidebar_second'])) {
    return 'solo-col-1-2';
  }
  else {
    return 'solo-col-1-1';
  }

}

/**
 * Helper function.
 */
function _create_region_layout_radio_two($region, &$variables) {
  if ($region == 'main') {
    $def = _get_which_main_col($variables);
  }
  else {
    $def = 'solo-col-1-1';
  }
  return [
    '#type' => 'select',
    '#title' => t("The two columns' settings for @region region", ['@region' => $region]),
    '#default_value' => theme_get_setting("{$region}_2col") ?? $def,
    '#options' => [
      'solo-col-1-1' => t('50%, 50%'),
      'solo-col-2-1' => t('67%, 33%'),
      'solo-col-3-1' => t('75%, 25%'),
      'solo-col-4-1' => t('80%, 20%'),
      'solo-col-1-2' => t('33%, 67%'),
      'solo-col-1-3' => t('25%, 75%'),
      'solo-col-1-4' => t('20%, 80%'),
    ],
  ];
}

/**
 * Helper function.
 */
function _create_megamenu_layout() {
  return [
    '#type' => 'select',
    '#title' => t('Megamenu Layout (1, 2, 3 or 4 Columns).'),
    '#default_value' => theme_get_setting('primary_menu_megamenu_layout') ?? 'm-1-1',
    '#options' => [
      'm-1' => t('1 Column - 100%'),
      'm-1-1' => t('2 Columns - 50%, 50%'),
      'm-2-1' => t('2 Columns - 67%, 33%'),
      'm-3-1' => t('2 Columns - 75%, 25%'),
      'm-4-1' => t('2 Columns - 80%, 20%'),
      'm-1-2' => t('2 Columns - 33%, 67%'),
      'm-1-3' => t('2 Columns - 25%, 75%'),
      'm-1-4' => t('2 Columns - 20%, 80%'),
      'm-1-1-1' => t('3 Columns - 33.3%, 33.3%, 33.3%'),
      'm-1-2-1' => t('3 Columns - 25%, 50%, 25%'),
      'm-1-3-1' => t('3 Columns - 20%, 60%, 20%'),
      'm-1-1-1-1' => t('4 Columns - 25%, 25%, 25%, 25%'),
      'm-1-2-2-1' => t('4 Columns - 16.6%, 33.3%, 33.3%, 16.6%'),
      'm-2-1-1-2' => t('4 Columns - 33.3%, 16.6%, 16.6%, 33.3%'),
      'm-1-2-1-2' => t('4 Columns - 16.6%, 33.3%, 16.6%, 33.3%'),
    ],
    '#states' => [
      'visible' => [
        ':input[name="primary_menu_megamenu"]' => ['checked' => TRUE],
      ],
    ],
  ];
}

/**
 * Helper function to get a list of content types.
 */
function _solo_get_content_types() {
  $content_types = [];
  $entity_type_manager = \Drupal::entityTypeManager();
  $bundle_info = $entity_type_manager->getStorage('node_type')->loadMultiple();

  foreach ($bundle_info as $type) {
    $content_types[$type->id()] = $type->label();
  }

  return $content_types;
}

/**
 * Helper function to check if a theme is in the Solo hierarchy.
 *
 * @param string $theme_name
 *   The name of the theme to check.
 *
 * @return bool
 *   TRUE if the theme or any of its base themes is 'solo', FALSE otherwise.
 */
function solo_utilities__check_solo_theme_hierarchy($theme_name) {
  $theme_handler = \Drupal::service('theme_handler');
  $theme_info = $theme_handler->getTheme($theme_name);

  // If the theme is 'solo', return TRUE.
  if ($theme_name === 'solo') {
    return TRUE;
  }

  // If there are base themes, check them recursively.
  if (!empty($theme_info->info['base theme'])) {
    foreach ((array) $theme_info->info['base theme'] as $base_theme_name) {
      if (solo_utilities__check_solo_theme_hierarchy($base_theme_name)) {
        return TRUE;
      }
    }
  }

  // If 'solo' was not found in the hierarchy, return FALSE.
  return FALSE;
}

/**
 * Helper function to check if the Solo theme or its sub-themes are active.
 *
 * @return bool
 *   TRUE if 'solo' or its sub-themes are in hierarchy, FALSE otherwise.
 */
function solo_utilities__check_solo_theme_hierarchy_active() {
  // Get the front-end (default) theme's name from configuration.
  $default_theme = \Drupal::config('system.theme')->get('default');

  // Check if 'solo' is the default theme or in the base theme hierarchy.
  return solo_utilities__check_solo_theme_hierarchy($default_theme);
}
