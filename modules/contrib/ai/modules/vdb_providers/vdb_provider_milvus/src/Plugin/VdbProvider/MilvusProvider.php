<?php

namespace Drupal\vdb_provider_milvus\Plugin\VdbProvider;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ai\Attribute\AiVdbProvider;
use Drupal\ai\Base\AiVdbProviderClientBase;
use Drupal\ai\Enum\VdbSimilarityMetrics;
use Drupal\key\KeyRepositoryInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Query\ConditionGroupInterface;
use Drupal\search_api\Query\QueryInterface;
use Drupal\vdb_provider_milvus\MilvusV2;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Plugin implementation of the 'Milvus DB' provider.
 */
#[AiVdbProvider(
  id: 'milvus',
  label: new TranslatableMarkup('Milvus DB'),
)]
class MilvusProvider extends AiVdbProviderClientBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The API key.
   *
   * @var string
   */
  protected string $apiKey = '';

  /**
   * Constructs an override for the AiVdbClientBase class to add Milvus V2.
   *
   * @param string $pluginId
   *   Plugin ID.
   * @param mixed $pluginDefinition
   *   Plugin definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\key\KeyRepositoryInterface $keyRepository
   *   The key repository.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   The event dispatcher.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entityFieldManager
   *   The entity field manager.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   * @param \Drupal\vdb_provider_milvus\MilvusV2 $client
   *   The Milvus V2 API client.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   */
  public function __construct(
    protected string $pluginId,
    protected mixed $pluginDefinition,
    protected ConfigFactoryInterface $configFactory,
    protected KeyRepositoryInterface $keyRepository,
    protected EventDispatcherInterface $eventDispatcher,
    protected EntityFieldManagerInterface $entityFieldManager,
    protected MessengerInterface $messenger,
    protected MilvusV2 $client,
    protected Request $request,
  ) {
    parent::__construct(
      $this->pluginId,
      $this->pluginDefinition,
      $this->configFactory,
      $this->keyRepository,
      $this->eventDispatcher,
      $this->entityFieldManager,
      $this->messenger,
    );
  }

  /**
   * Load from dependency injection container.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): AiVdbProviderClientBase|static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('key.repository'),
      $container->get('event_dispatcher'),
      $container->get('entity_field.manager'),
      $container->get('messenger'),
      $container->get('milvus_v2.api'),
      $container->get('request_stack')->getCurrentRequest(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(): ImmutableConfig {
    return $this->configFactory->get('vdb_provider_milvus.settings');
  }

  /**
   * Set key for authentication of the client.
   *
   * @param mixed $authentication
   *   The authentication.
   */
  public function setAuthentication(mixed $authentication): void {
    $this->apiKey = $authentication;
    $this->client = NULL;
  }

  /**
   * Get v2 client.
   *
   * This is needed for creating collections.
   *
   * @return \Drupal\vdb_provider_milvus\MilvusV2
   *   The Milvus v2 client.
   */
  public function getClient(): MilvusV2 {
    $config = $this->getConnectionData();
    $this->client->setBaseUrl($config['server']);
    $this->client->setPort($config['port']);
    $this->client->setApiKey($config['api_key']);
    return $this->client;
  }

  /**
   * Get connection data.
   *
   * @return array
   *   The connection data.
   */
  public function getConnectionData() {
    $config = $this->getConfig();
    $output['server'] = $this->configuration['server'] ?? $config->get('server');
    // Fail if server is not set.
    if (!$output['server']) {
      throw new \Exception('Milvus server is not configured');
    }
    $token = $config->get('api_key');
    $output['api_key'] = '';
    if ($token) {
      $output['api_key'] = $this->keyRepository->getKey($token)->getKeyValue();
    }
    if (!empty($this->configuration['api_key'])) {
      $output['api_key'] = $this->configuration['api_key'];
    }

    $output['port'] = $this->configuration['port'] ?? $config->get('port');
    if (!$output['port']) {
      $output['port'] = (substr($output['server'], 0, 5) === 'https') ? 443 : 80;
    }
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function ping(): bool {
    try {
      $return = $this->getClient()->listCollections();
      // Wrong API Key.
      if (isset($return['code']) && $return['code'] === 80001) {
        return FALSE;
      }
      return TRUE;
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isSetup(): bool {
    if ($this->getConfig()->get('server')) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function validateSettingsForm(array &$form, FormStateInterface $form_state): void {
    parent::validateSettingsForm($form, $form_state);

    $database_settings = $form_state->getValue('database_settings');
    if (empty($database_settings['database_name'])) {
      $form_state->setErrorByName('backend_config][database_name', $this->t('Ensure that your Pinecone API key is correct and that you have created at least one Index in the Pinecone UI.'));
      return;
    }

    $described = $this->getClient()->describeCollection(
      database_name: $database_settings['database_name'],
      collection_name: $database_settings['collection'],
    );
    if (!empty($described['data']) && !empty($described['data']['fields'])) {
      foreach ($described['data']['fields'] as $field) {
        if ($field['type'] !== 'FloatVector') {
          continue;
        }

        if (!isset($field['params'][0]['key']) || $field['params'][0]['key'] !== 'dim') {
          continue;
        }

        $embedding_configuration = $form_state->getValue('embeddings_engine_configuration');
        if ($embedding_configuration['dimensions'] !== (int) $field['params'][0]['value']) {
          $form_state->setErrorByName('embeddings_engine_configuration][dimensions', $this->t('The dimensions found in Milvus/Zilliz are "@dimensions" which does not match the dimensions set here of "@here".', [
            '@dimensions' => (int) $field['params'][0]['value'],
            '@here' => $embedding_configuration['dimensions'],
          ]));
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function viewIndexSettings(array $database_settings): array {
    $results = [];
    $results['ping'] = [
      'label' => $this->t('Ping'),
      'info' => $this->t('Able to reach Milvus/Zilliz via their API.'),
      'status' => $this->ping() ? 'success' : 'error',
    ];

    $results['is_zilliz'] = [
      'label' => $this->t('Is this Milvus or Zilliz?'),
      'info' => $this->getClient()->isZilliz() ? $this->t('Zilliz') : $this->t('Milvus'),
    ];

    $described = $this->getClient()->describeCollection(
      database_name: $database_settings['database_name'],
      collection_name: $database_settings['collection'],
    );
    if (!empty($described['data'])) {
      if (!empty($described['data']['autoId'])) {
        $results['auto_id'] = [
          'label' => $this->t('Auto ID'),
          'info' => $this->t('Uses Auto ID: @value', [
            '@value' => ($described['data']['autoId'] ? $this->t('True') : $this->t('False')),
          ]),
        ];
      }
      if (!empty($described['data']['partitionsNum'])) {
        $results['partitions'] = [
          'label' => $this->t('Partitions'),
          'info' => $described['data']['partitionsNum'],
        ];
      }
      if (!empty($described['data']['shardsNum'])) {
        $results['shards'] = [
          'label' => $this->t('Shards'),
          'info' => $described['data']['shardsNum'],
        ];
      }
      if (!empty($described['data']['collectionID'])) {
        $results['collection_id'] = [
          'label' => $this->t('Collection ID'),
          'info' => $described['data']['collectionID'],
        ];
      }
      if (!empty($described['data']['collectionName'])) {
        $results['collection_name'] = [
          'label' => $this->t('Collection Name'),
          'info' => $described['data']['collectionName'],
        ];
      }
      if (!empty($described['data']['consistencyLevel'])) {
        $results['consistency_level'] = [
          'label' => $this->t('Consistency Level'),
          'info' => $described['data']['consistencyLevel'],
        ];
      }
      if (!empty($described['data']['fields'])) {
        foreach ($described['data']['fields'] as $key => $field) {
          $results['field_' . $key] = [
            'label' => $this->t('Field "@name"', [
              '@name' => $field['name'],
            ]),
            'info' => $field['type'] . (!empty($field['params']) ? Json::encode($field['params']) : ''),
          ];
        }
      }
      if (!empty($described['data']['indexes'])) {
        foreach ($described['data']['indexes'] as $key => $index) {
          $results['index_' . $key] = [
            'label' => $this->t('Index "@number" field name "@fieldName" metric type:', [
              '@number' => $key,
              '@fieldName' => $index['fieldName'],
            ]),
            'info' => $index['metricType'],
          ];
        }
      }
      if (getenv('IS_DDEV_PROJECT') == 'true' && !$this->getClient()->isZilliz()) {
        $results['ddev_ui'] = [
          'label' => $this->t('Milvus DDEV UI'),
          'info' => $this->t('<a href="@milvus" target="_blank">Milvus DDEV UI</a>', [
            '@milvus' => 'https://' . $this->request->getHost() . ':8521',
          ]),
        ];
      }
    }

    return $results;
  }

  /**
   * {@inheritdoc}
   */
  public function getCollections(string $database = 'default'): array {
    return $this->getClient()->listCollections($database);
  }

  /**
   * {@inheritdoc}
   */
  public function createCollection(
    string $collection_name,
    int $dimension,
    VdbSimilarityMetrics $metric_type = VdbSimilarityMetrics::CosineSimilarity,
    string $database = 'default',
  ): void {
    $metric_name = match ($metric_type) {
      VdbSimilarityMetrics::EuclideanDistance => 'L2',
      VdbSimilarityMetrics::CosineSimilarity => 'COSINE',
      VdbSimilarityMetrics::InnerProduct => 'IP',
    };
    $client = $this->getClient();
    $client->createCollection(
      $collection_name,
      $database,
      $dimension,
      $metric_name,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function dropCollection(
    string $collection_name,
    string $database = 'default',
  ): void {
    $this->getClient()->dropCollection($collection_name);
  }

  /**
   * {@inheritdoc}
   */
  public function insertIntoCollection(
    string $collection_name,
    array $data,
    string $database = 'default',
  ): void {
    $response = $this->getClient()->insertIntoCollection($collection_name, $data, $database);

    if (!isset($response['code']) || ($response['code'] !== 0 && $response['code'] !== 200)) {
      throw new \Exception("Failed to insert into collection: " . $response['message']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function deleteFromCollection(
    string $collection_name,
    array $ids,
    string $database = 'default',
  ): void {
    $this->getClient()->deleteFromCollection($collection_name, $ids, $database);
  }

  /**
   * {@inheritdoc}
   */
  public function prepareFilters(QueryInterface $query): string {
    $filters = [];
    $index = $query->getIndex();
    $condition_group = $query->getConditionGroup();
    $filters = $this->processConditionGroup($filters, $index, $condition_group);
    if ($filters) {
      return implode(' && ', $filters);
    }
    return '';
  }

  /**
   * Processes a condition group, including handling nested condition groups.
   *
   * @param array $filters
   *   The filters built thus far.
   * @param \Drupal\search_api\IndexInterface $index
   *   The Search API Index.
   * @param \Drupal\search_api\Query\ConditionGroupInterface $condition_group
   *   The condition group.
   *
   * @return array
   *   The updated build of the filters.
   */
  protected function processConditionGroup(array $filters, IndexInterface $index, ConditionGroupInterface $condition_group): array {

    foreach ($condition_group->getConditions() as $condition) {

      // Check if the current condition is actually a nested ConditionGroup.
      if ($condition instanceof ConditionGroupInterface) {
        // Recursively process the nested ConditionGroup.
        $filters = $this->processConditionGroup($filters, $index, $condition);
        continue;
      }

      $fieldData = $index->getField($condition->getField());
      // Get the field type or its intrinsic field, like drupal_entity_id.
      $fieldType = $fieldData ? $fieldData->getType() : 'string';
      $isMultiple = $fieldData ? $this->isMultiple($fieldData) : FALSE;
      $values = is_array($condition->getValue()) ? $condition->getValue() : [$condition->getValue()];
      if (in_array($fieldType, ['string', 'full_text'])) {
        $normalizedValues = '"' . implode('","', $values) . '"';
      }
      else {
        $normalizedValues = implode(',', $values);
      }
      if ($isMultiple) {
        if ($condition->getOperator() === '=') {
          $filters[] = 'JSON_CONTAINS_ALL(' . $fieldData->getFieldIdentifier() . ', [' . $normalizedValues . '])';
        }
        if ($condition->getOperator() === 'IN') {
          $filters[] = 'JSON_CONTAINS_ANY(' . $fieldData->getFieldIdentifier() . ', [' . $normalizedValues . '])';
        }
        else {
          $this->messenger->addWarning('The vector database @name does not support negative operator on multiple fields.', [
            '@name' => $this->getClient()->getPluginId(),
          ]);
        }
      }
      else {
        $operator = $condition->getOperator();
        if ($operator === '=') {
          $operator = '==';
        }
        $filters[] = '(' . $fieldData->getFieldIdentifier() . ' ' . $operator . ' ' . $normalizedValues . ')';
      }
    }
    return $filters;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \JsonException
   */
  public function querySearch(
    string $collection_name,
    array $output_fields,
    mixed $filters = 'id not in [0]',
    int $limit = 10,
    int $offset = 0,
    string $database = 'default',
  ): array {
    $data = $this->getClient()->query(
      $collection_name,
      $output_fields,
      $filters,
      $limit,
      $offset,
      $database
    );
    return $data['data'] ?? [];
  }

  /**
   * {@inheritdoc}
   *
   * @throws \JsonException
   */
  public function vectorSearch(
    string $collection_name,
    array $vector_input,
    array $output_fields,
    mixed $filters = '',
    int $limit = 10,
    int $offset = 0,
    string $database = 'default',
  ): array {
    $data = $this->getClient()->search(
      $collection_name,
      $vector_input,
      $output_fields,
      $filters,
      $limit,
      $offset,
      $database
    );
    return $data['data'] ?? [];
  }

  /**
   * {@inheritdoc}
   *
   * @throws \JsonException
   */
  public function getVdbIds(
    string $collection_name,
    array $drupalIds,
    string $database = 'default',
  ): array {
    $data = $this->querySearch(
      collection_name: $collection_name,
      output_fields: ['id'],
      filters: "drupal_entity_id in [\"" . implode('","', $drupalIds) . "\"]",
      database: $database
    );
    $ids = [];
    if (!empty($data)) {
      foreach ($data as $item) {
        $ids[] = $item['id'];
      }
    }
    return $ids;
  }

}
