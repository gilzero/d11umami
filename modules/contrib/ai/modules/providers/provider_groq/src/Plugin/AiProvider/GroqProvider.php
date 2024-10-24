<?php

namespace Drupal\provider_groq\Plugin\AiProvider;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ai\Attribute\AiProvider;
use Drupal\ai\Base\AiProviderClientBase;
use Drupal\ai\Enum\AiModelCapability;
use Drupal\ai\Exception\AiRateLimitException;
use Drupal\ai\OperationType\Chat\ChatInput;
use Drupal\ai\OperationType\Chat\ChatInterface;
use Drupal\ai\OperationType\Chat\ChatMessage;
use Drupal\ai\OperationType\Chat\ChatOutput;
use Drupal\ai\Traits\OperationType\ChatTrait;
use OpenAI\Client;
use Symfony\Component\Yaml\Yaml;

/**
 * Plugin implementation of the 'groq' provider.
 */
#[AiProvider(
  id: 'groq',
  label: new TranslatableMarkup('Groq'),
)]
class GroqProvider extends AiProviderClientBase implements
  ContainerFactoryPluginInterface,
  ChatInterface {

  use ChatTrait;

  /**
   * The OpenAI Client for API calls.
   *
   * @var \OpenAI\Client|null
   */
  protected $client;

  /**
   * API Key.
   *
   * @var string
   */
  protected string $apiKey = '';

  /**
   * {@inheritdoc}
   */
  public function getConfiguredModels(?string $operation_type = NULL, array $capabilities = []): array {
    // No vision support.
    if (in_array(AiModelCapability::ChatWithImageVision, $capabilities)) {
      return [];
    }

    $response = $this->getClient()->models()->list()->toArray();
    $models = [];
    if (isset($response['data'])) {
      foreach ($response['data'] as $model) {
        $models[$model['id']] = $model['id'];
      }
    }
    // Just one complex JSON support.
    if (in_array(AiModelCapability::ChatJsonOutput, $capabilities)) {
      foreach ($models as $model_id => $model_name) {
        if (strpos($model_id, '3.2-90b') === FALSE) {
          unset($models[$model_id]);
        }
      }
    }
    return $models;
  }

  /**
   * {@inheritdoc}
   */
  public function isUsable(?string $operation_type = NULL, array $capabilities = []): bool {
    // No vision support.
    if (in_array(AiModelCapability::ChatWithImageVision, $capabilities)) {
      return FALSE;
    }
    // If its not configured, it is not usable.
    if (!$this->getConfig()->get('api_key')) {
      return FALSE;
    }
    // If its one of the bundles that Groq supports its usable.
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
    return $this->configFactory->get('provider_groq.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getApiDefinition(): array {
    // Load the configuration.
    $definition = Yaml::parseFile($this->moduleHandler->getModule('provider_groq')->getPath() . '/definitions/api_defaults.yml');
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
    // Set the new API key and reset the client.
    $this->apiKey = $authentication;
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
   * Loads the Groq Client with authentication if not initialized.
   */
  protected function loadClient(): void {
    if (!$this->client) {
      if (!$this->apiKey) {
        $this->setAuthentication($this->loadApiKey());
      }
      $this->client = \OpenAI::factory()
        ->withApiKey($this->apiKey)
        ->withBaseUri('https://api.groq.com/openai/v1')
        ->withHttpClient($this->httpClient)
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
    try {
      $response = $this->client->chat()->create($payload);
    }
    catch (\Exception $e) {
      // Try to figure out rate limit issues.
      if (strpos($e->getMessage(), 'Rate limit reached for model') !== FALSE) {
        throw new AiRateLimitException($e->getMessage());
      }
      else {
        throw $e;
      }
    }
    $message = new ChatMessage($response['choices'][0]['message']['role'], $response['choices'][0]['message']['content']);
    return new ChatOutput($message, $response, []);
  }

}
