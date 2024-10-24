<?php

namespace Drupal\styled_google_map\Form;

use Drupal\Core\Asset\LibraryDiscoveryInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\styled_google_map\StyledGoogleMapInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure styled google map settings.
 */
class StyledGoogleMapSettingsForm extends ConfigFormBase {

  /**
   * The library discovery.
   *
   * @var \Drupal\Core\Asset\LibraryDiscoveryInterface
   */
  protected $libraryDiscovery;

  /**
   * StyledGoogleMapSettingsForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Asset\LibraryDiscoveryInterface $library_discovery
   *   The library discovery.
   */
  public function __construct(ConfigFactoryInterface $config_factory, LibraryDiscoveryInterface $library_discovery) {
    parent::__construct($config_factory);
    $this->libraryDiscovery = $library_discovery;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('library.discovery')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'styled_google_map_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'styled_google_map.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('styled_google_map.settings');

    $form['styled_google_map_google_auth_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Google API Authentication Method'),
      '#options' => [
        StyledGoogleMapInterface::STYLED_GOOGLE_MAP_GOOGLE_AUTH_KEY => $this->t('API Key'),
        StyledGoogleMapInterface::STYLED_GOOGLE_MAP_GOOGLE_AUTH_WORK => $this->t('Google Maps API for Work'),
      ],
      '#default_value' => $config->get('styled_google_map_google_auth_method'),
    ];

    $form['styled_google_map_google_apikey'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google Maps API Key'),
      '#description' => $this->t('Obtain a Google Maps Javascript API key at <a href="@link">@link</a>', [
        '@link' => 'https://developers.google.com/maps/documentation/javascript/get-api-key',
      ]),
      '#default_value' => $config->get('styled_google_map_google_apikey'),
      '#required' => FALSE,
      '#states' => [
        'visible' => [
          ':input[name="styled_google_map_google_auth_method"]' => ['value' => StyledGoogleMapInterface::STYLED_GOOGLE_MAP_GOOGLE_AUTH_KEY],
        ],
      ],
    ];
    $form['styled_google_map_google_client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google Maps API for Work: Client ID'),
      '#description' => $this->t('For more information, visit: <a href="@link">@link</a>', [
        '@link' => 'https://developers.google.com/maps/documentation/javascript/get-api-key#client-id',
      ]),
      '#default_value' => $config->get('styled_google_map_google_client_id'),
      '#required' => FALSE,
      '#states' => [
        'visible' => [
          ':input[name="styled_google_map_google_auth_method"]' => ['value' => StyledGoogleMapInterface::STYLED_GOOGLE_MAP_GOOGLE_AUTH_WORK],
        ],
      ],
    ];
    $form['styled_google_map_libraries'] = [
      '#type' => 'checkboxes',
      '#options' => [
        'drawing' => $this->t('Drawing'),
        'geometry' => $this->t('Geometry'),
        'localContext' => $this->t('Local Context'),
        'places' => $this->t('Places'),
      ],
      '#default_value' => $config->get('styled_google_map_libraries') ?? [],
      '#title' => $this->t('Additional libraries to load with Google Map'),
      '#description' => $this->t('Read more <a href="@url" target="_blank">here</a>. "visualization" library is already included', ['@url' => 'https://developers.google.com/maps/documentation/javascript/libraries']),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('styled_google_map.settings');
    $config->set('styled_google_map_google_auth_method', $form_state->getValue('styled_google_map_google_auth_method'))
      ->set('styled_google_map_google_apikey', $form_state->getValue('styled_google_map_google_apikey'))
      ->set('styled_google_map_google_client_id', $form_state->getValue('styled_google_map_google_client_id'))
      ->set('styled_google_map_libraries', $form_state->getValue('styled_google_map_libraries'))
      ->save();
    $this->libraryDiscovery->clearCachedDefinitions();
    parent::submitForm($form, $form_state);
  }

}
