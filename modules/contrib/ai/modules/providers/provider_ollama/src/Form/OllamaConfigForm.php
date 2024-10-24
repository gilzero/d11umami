<?php

namespace Drupal\provider_ollama\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Ollama API access.
 */
class OllamaConfigForm extends ConfigFormBase {

  /**
   * Config settings.
   */
  const CONFIG_NAME = 'provider_ollama.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ollama_settings';
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

    $form['host_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Host Name'),
      '#description' => $this->t('The host name for the API, including protocol, typically http://127.0.0.1 on a server. Use http://host.docker.internal for DDEV, see <a href="https://www.drupal.org/docs/extending-drupal/contributed-modules/contributed-module-documentation/ai/how-to-set-up-a-provider">AI documentation</a>.'),
      '#required' => TRUE,
      '#default_value' => $config->get('host_name'),
      '#attributes' => [
        'placeholder' => 'http://127.0.0.1 or http://host.docker.internal for DDEV, Docker, etc.',
      ],
    ];

    $form['port'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Port'),
      '#description' => $this->t('The port number for the API. Can be left empty if 80 or 443.'),
      '#default_value' => $config->get('port'),
      '#attributes' => [
        'placeholder' => '11434',
      ],
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
      ->set('host_name', $form_state->getValue('host_name'))
      ->set('port', $form_state->getValue('port'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
