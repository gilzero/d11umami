<?php

namespace Drupal\provider_huggingface\Plugin\AiProvider;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ai\Attribute\AiProvider;
use Drupal\ai\Base\AiProviderClientBase;
use Drupal\ai\Exception\AiMissingFeatureException;
use Drupal\ai\Exception\AiRateLimitException;
use Drupal\ai\Exception\AiResponseErrorException;
use Drupal\ai\OperationType\Chat\ChatInput;
use Drupal\ai\OperationType\Chat\ChatInterface;
use Drupal\ai\OperationType\Chat\ChatMessage;
use Drupal\ai\OperationType\Chat\ChatOutput;
use Drupal\ai\OperationType\Embeddings\EmbeddingsInput;
use Drupal\ai\OperationType\Embeddings\EmbeddingsInterface;
use Drupal\ai\OperationType\Embeddings\EmbeddingsOutput;
use Drupal\ai\OperationType\ImageClassification\ImageClassificationInput;
use Drupal\ai\OperationType\ImageClassification\ImageClassificationInterface;
use Drupal\ai\OperationType\ImageClassification\ImageClassificationItem;
use Drupal\ai\OperationType\ImageClassification\ImageClassificationOutput;
use Drupal\ai\Traits\OperationType\ChatTrait;
use Drupal\provider_huggingface\HuggingfaceApi;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Plugin implementation of the 'huggingface' provider.
 */
#[AiProvider(
  id: 'huggingface',
  label: new TranslatableMarkup('Huggingface'),
)]
class HuggingfaceProvider extends AiProviderClientBase implements
  ContainerFactoryPluginInterface,
  ChatInterface,
  EmbeddingsInterface,
  ImageClassificationInterface {

  use StringTranslationTrait;
  use ChatTrait;

  /**
   * The Huggingface Client.
   *
   * @var \Drupal\provider_huggingface\HuggingfaceApi
   */
  protected HuggingfaceApi $client;

  /**
   * API Key.
   *
   * @var string
   */
  protected string $apiKey = '';

  /**
   * We want to add models to the provider dynamically.
   *
   * @var bool
   */
  protected bool $hasPredefinedModels = FALSE;

  /**
   * Supported Types.
   *
   * @var array
   */
  protected $supportedTypes = [
    'chat' => [
      'label' => 'Chat',
      'filter' => 'text-generation',
    ],
    'embeddings' => [
      'label' => 'Embeddings',
      'filter' => 'feature-extraction',
    ],
    'image_classification' => [
      'label' => 'Image Classification',
      'filter' => 'image-classification',
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->client = $container->get('provider_huggingface.api');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function isUsable(?string $operation_type = NULL, array $capabilities = []): bool {
    // If its not configured, it is not usable.
    if (!$this->getConfig()->get('api_key')) {
      return FALSE;
    }
    // If its one of the bundles that Mistral supports its usable.
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
      'image_classification',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(): ImmutableConfig {
    return $this->configFactory->get('provider_huggingface.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getApiDefinition(): array {
    // Load the configuration.
    $definition = Yaml::parseFile($this->moduleHandler->getModule('provider_huggingface')->getPath() . '/definitions/api_defaults.yml');
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
    $this->client->setApiToken($this->apiKey);
  }

  /**
   * Gets the raw client.
   *
   * This is the client for inference.
   *
   * @return \Drupal\provider_huggingface\HuggingfaceApi
   *   The Huggingface client.
   */
  public function getClient(): HuggingfaceApi {
    $this->loadClient();
    return $this->client;
  }

  /**
   * Loads the Huggingface Client with authentication if not initialized.
   */
  protected function loadClient(): void {
    if (!$this->apiKey) {
      $this->setAuthentication($this->loadApiKey());
    }
    $this->client->setApiToken($this->apiKey);
  }

  /**
   * {@inheritdoc}
   */
  public function chat(array|string|ChatInput $input, string $model_id, array $tags = []): ChatOutput {
    $info = $this->getModelInfo('chat', $model_id);
    if (!$info['huggingface_endpoint']) {
      throw new AiMissingFeatureException('Huggingface endpoint is missing.');
    }
    $this->loadClient();
    // Normalize the input if needed.
    $chat_input = $input;
    if ($input instanceof ChatInput) {
      $chat_input = "";
      // Add a warning log if they set an system role.
      if ($this->chatSystemRole) {
        $this->loggerFactory->get('ai')->warning('A chat message with system role was sent with Huggingface provider. Huggingface does not support system roles and this was removed.');
      }
      foreach ($input->getMessages() as $message) {
        $chat_input .= $message->getRole() . ': ' . $message->getText() . "\n";
        if (count($message->getImages())) {
          throw new AiMissingFeatureException('Images are not supported by Huggingface.');
        }
      }
    }
    try {
      $response = json_decode($this->client->textGeneration($info['huggingface_endpoint'], $chat_input), TRUE);
    }
    catch (\Exception $e) {
      // If the rate limit is reach, special error.
      if (strpos($e->getMessage(), 'Rate limit reached') !== FALSE) {
        throw new AiRateLimitException($e->getMessage());
      }
      throw $e;
    }
    // We remove the inputted text.
    $message = new ChatMessage('', str_replace($chat_input, '', $response[0]['generated_text']));
    return new ChatOutput($message, $response, []);
  }

  /**
   * {@inheritdoc}
   */
  public function embeddings(string|EmbeddingsInput $input, string $model_id, array $tags = []): EmbeddingsOutput {
    $info = $this->getModelInfo('embeddings', $model_id);
    if (!$info['huggingface_endpoint']) {
      throw new AiMissingFeatureException('Huggingface endpoint is missing.');
    }
    $this->loadClient();
    // Normalize the input if needed.
    if ($input instanceof EmbeddingsInput) {
      $input = $input->getPrompt();
    }
    // Send the request.
    $response = json_decode($this->client->featureExtraction($info['huggingface_endpoint'], $input), TRUE);

    return new EmbeddingsOutput($response, $response, []);
  }

  /**
   * {@inheritdoc}
   */
  public function imageClassification(string|array|ImageClassificationInput $input, string $model_id, array $tags = []): ImageClassificationOutput {
    $info = $this->getModelInfo('image_classification', $model_id);
    if (!$info['huggingface_endpoint']) {
      throw new AiMissingFeatureException('Huggingface endpoint is missing.');
    }
    $this->loadClient();
    // Normalize the input if needed.
    if ($input instanceof ImageClassificationInput) {
      $input = $input->getImageFile()->getBinary();
    }
    // Store temporary file.
    $temp_file = tempnam(sys_get_temp_dir(), 'ai_image_classification');
    file_put_contents($temp_file, $input);
    // Send the request.
    $response = json_decode($this->client->imageClassification($info['huggingface_endpoint'], $temp_file), TRUE);
    // Remove the temporary file.
    unlink($temp_file);
    $classifications = [];
    if (is_array($response)) {
      foreach ($response as $row) {
        $classifications[] = new ImageClassificationItem($row['label'], $row['score']);
      }
    }
    else {
      throw new AiResponseErrorException('Invalid response from Huggingface.');
    }

    return new ImageClassificationOutput($classifications, $response, []);
  }

  /**
   * {@inheritdoc}
   */
  public function maxEmbeddingsInput($model_id = ''): int {
    // @todo this is playing safe. Ideally, we should provide real number per model.
    return 1024;
  }

  /**
   * {@inheritdoc}
   */
  public function loadModelsForm(array $form, $form_state, string $operation_type, string|NULL $model_id = NULL): array {
    $form = parent::loadModelsForm($form, $form_state, $operation_type, $model_id);
    $config = $this->loadModelConfig($operation_type, $model_id);

    $form['model_data']['huggingface_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint'),
      '#description' => $this->t('The endpoint needed to access the Azure API. Can be found in Azure AI Studio under the Target URI label. NOTE that some models have different versions, its the endpoints with completions in the end you need to copy.'),
      '#default_value' => $config['huggingface_endpoint'] ?? '',
      '#required' => TRUE,
      '#weight' => -10,
      '#autocomplete_route_name' => 'provider_huggingface.autocomplete.models',
      '#autocomplete_route_parameters' => [
        'model_type' => $this->supportedTypes[$operation_type]['filter'],
      ],
    ];

    return $form;
  }

}
