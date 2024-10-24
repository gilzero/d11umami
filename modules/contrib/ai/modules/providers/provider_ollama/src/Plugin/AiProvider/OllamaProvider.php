<?php

namespace Drupal\provider_ollama\Plugin\AiProvider;

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
use Drupal\provider_ollama\OllamaChatMessageIterator;
use Drupal\provider_ollama\OllamaControlApi;
use GuzzleHttp\Client as GuzzleClient;
use OpenAI\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Plugin implementation of the 'ollama' provider.
 */
#[AiProvider(
  id: 'ollama',
  label: new TranslatableMarkup('Ollama'),
)]
class OllamaProvider extends AiProviderClientBase implements
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
   * The Ollama Control API for configuration calls.
   *
   * @var \Drupal\provider_ollama\OllamaControlApi
   */
  protected $controlApi;

  /**
   * The current user.
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
   * Dependency Injection for the Ollama Control API.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->controlApi = $container->get('provider_ollama.control_api');
    $instance->controlApi->setConnectData($instance->getBaseHost());
    $instance->currentUser = $container->get('current_user');
    $instance->messenger = $container->get('messenger');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguredModels(?string $operation_type = NULL, array $capabilities = []): array {
    // Graceful failure.
    try {
      $response = $this->controlApi->getModels();
    }
    catch (\Exception $e) {
      if ($this->currentUser->hasPermission('administer ai providers')) {
        $this->messenger->addError($this->t('Failed to get models from Ollama: @error', ['@error' => $e->getMessage()]));
      }
      $this->loggerFactory->get('provider_ollama')->error('Failed to get models from Ollama: @error', ['@error' => $e->getMessage()]);
      return [];
    }
    $models = [];
    if (isset($response['models'])) {
      foreach ($response['models'] as $model) {
        $models[$model['model']] = $model['name'];
      }
    }
    return $models;
  }

  /**
   * {@inheritdoc}
   */
  public function isUsable(?string $operation_type = NULL, array $capabilities = []): bool {
    // If its one of the bundles that Ollama supports its usable.
    if (!$this->getBaseHost()) {
      return FALSE;
    }
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
    return $this->configFactory->get('provider_ollama.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getApiDefinition(): array {
    // Load the configuration.
    $definition = Yaml::parseFile($this->moduleHandler->getModule('provider_ollama')->getPath() . '/definitions/api_defaults.yml');
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
   * This is the client for controlling the Ollama API.
   *
   * @return \Drupal\provider_ollama\OllamaControlApi
   *   The control client.
   */
  public function getControlClient(): OllamaControlApi {
    return $this->controlApi;
  }

  /**
   * Loads the Ollama Client with hostname and port.
   */
  protected function loadClient(): void {
    if (!$this->client) {
      $host = $this->getBaseHost();
      $host .= '/v1';

      // Set longer timeout.
      $client = new GuzzleClient(['timeout' => 600]);

      $this->client = \OpenAI::factory()
        ->withHttpClient($client)
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
      /** @var \Drupal\ai\OperationType\Chat\ChatMessage $message */
      foreach ($input->getMessages() as $message) {

        $images = [];
        if (count($message->getImages())) {
          foreach ($message->getImages() as $image) {
            $images[] = $image->getAsBase64EncodedString('');
          }
        }
        $new_message = [
          'role' => $message->getRole(),
          'content' => $message->getText(),
          'images' => $images,
        ];
        $chat_input[] = $new_message;
      }
    }
    $payload = [
      'model' => $model_id,
      'messages' => $chat_input,
    ] + $this->configuration;
    $response = $this->client->chat()->create($payload);

    if ($this->streamed) {
      $response = $this->client->chat()->createStreamed($payload);
      $message = new OllamaChatMessageIterator($response);
    }
    else {
      $response = $this->client->chat()->create($payload)->toArray();
      $message = new ChatMessage($response['choices'][0]['message']['role'], $response['choices'][0]['message']['content']);
    }
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
    $response = $this->controlApi->embeddings($input, $model_id);

    return new EmbeddingsOutput($response['embedding'], $response, []);
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
