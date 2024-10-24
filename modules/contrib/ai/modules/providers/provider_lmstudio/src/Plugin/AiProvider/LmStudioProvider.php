<?php

namespace Drupal\provider_lmstudio\Plugin\AiProvider;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ai\Attribute\AiProvider;
use Drupal\ai\Base\AiProviderClientBase;
use Drupal\ai\OperationType\Chat\ChatInput;
use Drupal\ai\OperationType\Chat\ChatInterface;
use Drupal\ai\OperationType\Chat\ChatMessage;
use Drupal\ai\OperationType\Chat\ChatOutput;
use Drupal\ai\OperationType\Embeddings\EmbeddingsInput;
use Drupal\ai\OperationType\Embeddings\EmbeddingsInterface;
use Drupal\ai\OperationType\Embeddings\EmbeddingsOutput;
use Drupal\ai\Traits\OperationType\ChatTrait;
use Drupal\provider_lmstudio\LmStudioControlApi;
use OpenAI\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Plugin implementation of the 'lmstudio' provider.
 */
#[AiProvider(
  id: 'lmstudio',
  label: new TranslatableMarkup('LM Studio'),
)]
class LmStudioProvider extends AiProviderClientBase implements
  ContainerFactoryPluginInterface,
  ChatInterface,
  EmbeddingsInterface {

  use StringTranslationTrait;
  use ChatTrait;

  /**
   * The OpenAI Client for API calls.
   *
   * @var \OpenAI\Client|null
   */
  protected $client;

  /**
   * The control API.
   *
   * @var \Drupal\provider_lmstudio\LmStudioControlApi
   */
  protected $controlApi;

  /**
   * Get the current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Dependency Injection for the LM Studio Control API.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->controlApi = $container->get('provider_lmstudio.control_api');
    $instance->controlApi->setConnectData($instance->getBaseHost());
    $instance->currentUser = $container->get('current_user');
    $instance->messenger = $container->get('messenger');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguredModels(?string $operation_type = NULL, array $capabilities = []): array {
    $this->loadClient();
    try {
      $response = $this->controlApi->getModels();
    }
    catch (\Exception $e) {
      if ($this->currentUser->hasPermission('administer ai providers')) {
        $this->messenger->addError($this->t('Failed to get models from LM Studio: @error', ['@error' => $e->getMessage()]));
      }
      $this->loggerFactory->get('provider_lmstudio')->error('Failed to get models from LM Studio: @error', ['@error' => $e->getMessage()]);
      return [];
    }
    $models = [];
    if (isset($response['data'])) {
      foreach ($response['data'] as $model) {
        $models[$model['id']] = $model['id'];
      }
    }
    return $models;
  }

  /**
   * {@inheritdoc}
   */
  public function isUsable(?string $operation_type = NULL, array $capabilities = []): bool {
    if (!$this->getBaseHost()) {
      return FALSE;
    }
    // If its one of the bundles that Ollama supports its usable.
    if ($operation_type) {
      return in_array($operation_type, $this->getSupportedOperationTypes());
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSupportedOperationTypes(): array {
    return [
      'chat',
      'embeddings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(): ImmutableConfig {
    return $this->configFactory->get('provider_lmstudio.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getApiDefinition(): array {
    // Load the configuration.
    $definition = Yaml::parseFile($this->moduleHandler->getModule('provider_lmstudio')->getPath() . '/definitions/api_defaults.yml');
    return $definition;
  }

  /**
   * {@inheritdoc}
   */
  public function getModelSettings(string $model_id, array $generalConfig = []): array {
    return $generalConfig;
  }

  /**
   * {@inheritdoc}
   */
  public function setAuthentication(mixed $authentication): void {
    // Doesn't do anything.
    $this->client = NULL;
  }

  /**
   * Gets the raw client.
   *
   * This is the client for inference.
   *
   * @return \OpenAI\Client
   *   The OpenAI client.
   */
  public function getClient(): Client {
    $this->loadClient();
    return $this->client;
  }

  /**
   * Get control client.
   *
   * This is the client for controlling the LM Studio API.
   *
   * @return \Drupal\provider_lmstudio\LmStudioControlApi
   *   The control client.
   */
  public function getControlClient(): LmStudioControlApi {
    return $this->controlApi;
  }

  /**
   * Loads the Ollama Client with hostname and port.
   */
  protected function loadClient(): void {
    if (!$this->client) {
      $host = $this->getBaseHost();
      $host .= '/v1';

      $this->client = \OpenAI::factory()
        ->withHttpClient($this->httpClient)
        ->withBaseUri($host)
        ->make();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function chat(array|string|ChatInput $input, string $model_id, array $tags = []): ChatOutput {
    $this->loadClient();
    // Normalize the input if needed.
    $chat_input = $input;
    if ($input instanceof ChatInput) {
      $chat_input = [];
      // Add a system role if wanted.
      if ($this->chatSystemRole) {
        $chat_input[] = [
          'role' => 'system',
          'content' => $this->chatSystemRole,
        ];
      }
      foreach ($input->getMessages() as $message) {
        $chat_input[] = [
          'role' => $message->getRole(),
          'content' => $message->getText(),
        ];
      }
    }
    $payload = [
      'model' => $model_id,
      'messages' => $chat_input,
    ] + $this->configuration;
    $response = $this->client->chat()->create($payload);
    $message = new ChatMessage($response['choices'][0]['message']['role'], $response['choices'][0]['message']['content']);
    return new ChatOutput($message, $response, []);
  }

  /**
   * {@inheritdoc}
   */
  public function embeddings(string|EmbeddingsInput $input, string $model_id, array $tags = []): EmbeddingsOutput {
    $this->loadClient();
    // Normalize the input if needed.
    if ($input instanceof EmbeddingsInput) {
      $input = $input->getPrompt();
    }
    // Send the request.
    $payload = [
      'model' => $model_id,
      'input' => $input,
    ] + $this->configuration;
    $response = $this->client->embeddings()->create($payload)->toArray();

    return new EmbeddingsOutput($response['data'][0]['embedding'], $response, []);
  }

  /**
   * Gets the base host.
   *
   * @return string
   *   The base host.
   */
  protected function getBaseHost(): string {
    $host = rtrim($this->getConfig()->get('host_name'), '/');
    if ($this->getConfig()->get('port')) {
      $host .= ':' . $this->getConfig()->get('port');
    }
    return $host;
  }

  /**
   * {@inheritdoc}
   */
  public function maxEmbeddingsInput($model_id = ''): int {
    // @todo this is playing safe. Ideally, we should provide real number per model.
    return 1024;
  }

}
