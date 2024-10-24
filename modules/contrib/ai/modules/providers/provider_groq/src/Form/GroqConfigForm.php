<?php

namespace Drupal\provider_groq\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ai\AiProviderPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Groq API access.
 */
class GroqConfigForm extends ConfigFormBase {

  /**
   * Config settings.
   */
  const CONFIG_NAME = 'provider_groq.settings';

  /**
   * The AI Provider service.
   *
   * @var \Drupal\ai\AiProviderPluginManager
   */
  protected $aiProviderManager;

  /**
   * Constructs a new GroqConfigForm object.
   */
  final public function __construct(AiProviderPluginManager $ai_provider_manager) {
    $this->aiProviderManager = $ai_provider_manager;
  }

  /**
   * {@inheritdoc}
   */
  final public static function create(ContainerInterface $container) {
    return new static(
      $container->get('ai.provider'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'groq_settings';
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
      '#title' => $this->t('Groq API Key'),
      '#description' => $this->t('The API Key. Can be found on <a href="https://console.groq.com/keys">https://console.groq.com/keys</a>.'),
      '#default_value' => $config->get('api_key'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->config(static::CONFIG_NAME)
      ->set('api_key', $form_state->getValue('api_key'))
      ->save();

    $this->aiProviderManager->defaultIfNone('chat', 'anthropic', 'llama-3.1-8b-instant');

    parent::submitForm($form, $form_state);
  }

}
