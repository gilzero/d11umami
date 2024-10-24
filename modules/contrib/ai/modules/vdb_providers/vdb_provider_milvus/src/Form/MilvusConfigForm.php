<?php

namespace Drupal\vdb_provider_milvus\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ai\AiVdbProviderPluginManager;
use Drupal\key\KeyRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Milvus DB config form.
 */
class MilvusConfigForm extends ConfigFormBase {

  /**
   * The VDB PRovider service.
   *
   * @var \Drupal\ai\AiVdbProviderPluginManager
   */
  protected AiVdbProviderPluginManager $vdbProviderPluginManager;

  /**
   * The key repository.
   *
   * @var \Drupal\key\KeyRepositoryInterface
   */
  protected KeyRepositoryInterface $keyRepository;

  /**
   * Constructor of the Milvus DB config form.
   *
   * @param \Drupal\ai\AiVdbProviderPluginManager $vdbProviderPluginManager
   *   The VDB Provider plugin manager.
   * @param \Drupal\key\KeyRepositoryInterface $keyRepository
   *   The key repository.
   */
  public function __construct(AiVdbProviderPluginManager $vdbProviderPluginManager, KeyRepositoryInterface $keyRepository) {
    $this->vdbProviderPluginManager = $vdbProviderPluginManager;
    $this->keyRepository = $keyRepository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('ai.vdb_provider'),
      $container->get('key.repository'),
    );
  }

  /**
   * Config settings.
   */
  const CONFIG_NAME = 'vdb_provider_milvus.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'milvus_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      static::CONFIG_NAME,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config(static::CONFIG_NAME);

    $form['server'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Server'),
      '#required' => TRUE,
      '#description' => $this->t('The server to connect to. If you use Zilliz Cloud, this can be found under Public Endpoint.'),
      '#default_value' => $config->get('server'),
    ];

    $form['port'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Port'),
      '#description' => $this->t('The server port to connect to. If you use Zilliz Cloud, this is 443.'),
      '#default_value' => $config->get('port'),
    ];

    $form['api_key'] = [
      '#type' => 'key_select',
      '#title' => $this->t('API Key'),
      '#description' => $this->t('The API key to use for authentication. This is optional if your Milvus Vector Database is installed within your hosting environment (e.g. when using via DDEV).'),
      '#default_value' => $config->get('api_key'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    $server = $form_state->getValue('server');
    if (!filter_var($server, FILTER_VALIDATE_URL)) {
      $form_state->setErrorByName('server', $this->t('The server must be a valid URL.'));
    }

    $port = $form_state->getValue('port');
    if (!empty($port) && !is_numeric($port)) {
      $form_state->setErrorByName('port', $this->t('The port must be a number.'));
    }

    // Test the connection.
    $milvusConnector = $this->vdbProviderPluginManager->createInstance('milvus');
    $key = $form_state->getValue('api_key');
    if (!empty($key)) {
      $key = $this->keyRepository->getKey($key)->getKeyValue();
    }
    $milvusConnector->setCustomConfig([
      'server' => $server,
      'port' => $port,
      'api_key' => $key,
    ]);

    if (!$milvusConnector->ping()) {
      $form_state->setErrorByName('server', $this->t('Could not connect to the server.'));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config(static::CONFIG_NAME)
      ->set('server', rtrim($form_state->getValue('server'), '/'))
      ->set('port', $form_state->getValue('port'))
      ->set('api_key', $form_state->getValue('api_key'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
