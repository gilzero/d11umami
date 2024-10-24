<?php

namespace Drupal\provider_huggingface\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\ai\AiProviderPluginManager;
use Drupal\ai\Service\AiProviderFormHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Huggingface access.
 */
class HuggingfaceConfigForm extends ConfigFormBase {

  /**
   * The form helper.
   *
   * @var \Drupal\ai\Service\AiProviderFormHelper
   */
  protected $formHelper;

  /**
   * The AI Provider manager.
   *
   * @var \Drupal\ai\AiProviderPluginManager
   */
  protected $providerManager;

  /**
   * Constructs a new HuggingfaceConfigForm object.
   */
  final public function __construct(AiProviderFormHelper $form_helper, AiProviderPluginManager $provider_manager) {
    $this->formHelper = $form_helper;
    $this->providerManager = $provider_manager;
  }

  /**
   * {@inheritdoc}
   */
  final public static function create(ContainerInterface $container) {
    return new static(
      $container->get('ai.form_helper'),
      $container->get('ai.provider'),
    );
  }

  /**
   * Config settings.
   */
  const CONFIG_NAME = 'provider_huggingface.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'huggingface_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::CONFIG_NAME,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::CONFIG_NAME);

    $form['api_key'] = [
      '#type' => 'key_select',
      '#title' => $this->t('Huggingface Access Token'),
      '#description' => $this->t('The Access Token. Can be found on @link admin pages. <strong>Make sure that the token has the correct rights.</strong>', [
        '@link' => Link::fromTextAndUrl('Huggingface', Url::fromUri('https://huggingface.co/settings/tokens'))->toString(),
      ]),
      '#default_value' => $config->get('api_key'),
    ];

    $provider = $this->providerManager->createInstance('huggingface');
    $form['models'] = $this->formHelper->getModelsTable($form, $form_state, $provider);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $models = [];
    foreach ($form_state->getValues() as $key => $value) {
      if (substr($key, 0, 7) == 'model__' && $value) {
        $parts = explode('__', $key);
        $models[$parts[1]][] = $value;
      }
    }

    // Retrieve the configuration.
    $this->config(static::CONFIG_NAME)
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('models', $models)
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Add more model.
   */
  public function addMoreModel(array &$form, FormStateInterface $form_state) {
    $models = [];
    foreach ($form_state->getValues() as $key => $value) {
      if (substr($key, 0, 7) == 'model__' && $value) {
        $parts = explode('__', $key);
        $models[$parts[1]][] = $value;
      }
    }
    $form_state->set('models', $models);
    $form_state->setRebuild();
  }

  /**
   * Add more model callback.
   */
  public function addMoreModelCallback(array &$form, FormStateInterface $form_state) {
    $trigger = $form_state->getTriggeringElement();
    $type = $trigger['#attributes']['data-type'];
    return $form[$type];
  }

}
