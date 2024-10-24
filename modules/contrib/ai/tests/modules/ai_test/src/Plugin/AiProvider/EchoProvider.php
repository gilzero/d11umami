<?php

namespace Drupal\ai_test\Plugin\AiProvider;

use Drupal\Core\Config\ImmutableConfig;
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
use Drupal\ai\OperationType\GenericType\AudioFile;
use Drupal\ai\OperationType\Moderation\ModerationInput;
use Drupal\ai\OperationType\Moderation\ModerationInterface;
use Drupal\ai\OperationType\Moderation\ModerationOutput;
use Drupal\ai\OperationType\Moderation\ModerationResponse;
use Drupal\ai\OperationType\SpeechToText\SpeechToTextInput;
use Drupal\ai\OperationType\SpeechToText\SpeechToTextInterface;
use Drupal\ai\OperationType\SpeechToText\SpeechToTextOutput;
use Drupal\ai\OperationType\TextToSpeech\TextToSpeechInput;
use Drupal\ai\OperationType\TextToSpeech\TextToSpeechInterface;
use Drupal\ai\OperationType\TextToSpeech\TextToSpeechOutput;
use Symfony\Component\Yaml\Yaml;

/**
 * Plugin implementation of the 'mock' provider.
 */
#[AiProvider(
  id: 'echoai',
  label: new TranslatableMarkup('EchoAI'),
)]
class EchoProvider extends AiProviderClientBase implements
  ChatInterface,
  EmbeddingsInterface,
  ModerationInterface,
  SpeechToTextInterface,
  TextToSpeechInterface {

  /**
   * {@inheritdoc}
   */
  public function getConfig(): ImmutableConfig {
    return $this->configFactory->get('system.site');
  }

  /**
   * {@inheritdoc}
   */
  public function getApiDefinition(): array {
    // Load the configuration.
    return Yaml::parseFile($this->moduleHandler->getModule('ai_test')->getPath() . '/definitions/api_defaults.yml');
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
  public function getConfiguredModels(?string $operation_type = NULL, array $capabilities = []): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function isUsable(?string $operation_type = NULL, array $capabilities = []): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSupportedOperationTypes(): array {
    return [
      'chat',
      'embeddings',
      'speech_to_text',
      'text_to_speech',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setAuthentication(mixed $authentication): void {
  }

  /**
   * {@inheritdoc}
   */
  public function chat(array|string|ChatInput $input, string $model_id, array $tags = []): ChatOutput {
    $response = [];
    $message = new ChatMessage('user', sprintf('Hello world! Input: %s. Config: %s.', (string) $input, json_encode($this->configuration)));

    return new ChatOutput($message, $response, []);
  }

  /**
   * {@inheritdoc}
   */
  public function embeddings(EmbeddingsInput|string $input, string $model_id, array $tags = []): EmbeddingsOutput {
    $response = ['input' => sprintf('Hello world! %s', (string) $input)];

    return new EmbeddingsOutput($response, $response, []);
  }

  /**
   * {@inheritdoc}
   */
  public function maxEmbeddingsInput($model_id = ''): int {
    return 1024;
  }

  /**
   * {@inheritdoc}
   */
  public function moderation(ModerationInput|string $input, ?string $model_id = NULL, array $tags = []): ModerationOutput {
    $response = [
      'input' => sprintf('Hello world! %s', (string) $input),
    ];
    $mod = new ModerationResponse(TRUE, $response);

    return new ModerationOutput($mod, $response, []);
  }

  /**
   * {@inheritdoc}
   */
  public function speechToText(SpeechToTextInput|string $input, string $model_id, array $tags = []): SpeechToTextOutput {
    $response = [
      'input' => sprintf('Hello world! Input: %s. Config: %s', (string) $input, json_encode($this->configuration)),
    ];

    return new SpeechToTextOutput($response['input'], $response, []);
  }

  /**
   * {@inheritdoc}
   */
  public function textToSpeech(TextToSpeechInput|string $input, string $model_id, array $tags = []): TextToSpeechOutput {
    $response = [
      'input' => sprintf('Hello world! %s', (string) $input),
    ];
    $audio = new AudioFile($response['input'], 'audio/mpeg', 'echoai.mp3');

    return new TextToSpeechOutput([$audio], $response, []);
  }

}
