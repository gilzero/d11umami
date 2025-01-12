<?php

namespace Drupal\openapi\Plugin\openapi;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Authentication\AuthenticationCollectorInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityTypeInterface;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Defines base class for OpenApi Generator plugins.
 */
abstract class OpenApiGeneratorBase extends PluginBase implements OpenApiGeneratorInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * Separator for using in definition id strings.
   *
   * @var string
   */
  // phpcs:ignore
  public static string $DEFINITION_SEPARATOR = ':';

  /**
   * The generator label.
   *
   * @var string
   */
  public $label;

  /**
   * The configuration object factory.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * The request options parameter.
   *
   * @var mixed|array
   */
  protected $options;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected RouteProviderInterface $routingProvider,
    protected EntityFieldManagerInterface $fieldManager,
    protected SerializerInterface $serializer,
    RequestStack $request_stack,
    protected ConfigFactoryInterface $configFactory,
    protected AuthenticationCollectorInterface $authenticationCollector,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->label = $this->getPluginDefinition()["label"];
    $this->request = $request_stack->getCurrentRequest();
    $this->options = [];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('router.route_provider'),
      $container->get('entity_field.manager'),
      $container->get('serializer'),
      $container->get('request_stack'),
      $container->get('config.factory'),
      $container->get('authentication_collector')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setOptions($options) {
    $this->options = $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
    return $this->getPluginId();
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    // Load value from definition.
    return $this->label ?? $this->getPluginDefinition()['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSpecification() {
    $basePath = $this->getBasePath();
    $spec = [
      'swagger' => "2.0",
      'schemes' => [$this->request->getScheme()],
      'info' => $this->getInfo(),
      'host' => $this->request->getHttpHost(),
      'basePath' => empty($basePath) ? '/' : $basePath,
      'securityDefinitions' => $this->getSecurityDefinitions(),
      'security' => $this->getSecurity(),
      'tags' => $this->getTags(),
      'definitions' => $this->getDefinitions(),
      'consumes' => $this->getConsumes(),
      'produces' => $this->getProduces(),
      'paths' => $this->getPaths(),
    ];

    // Strip any empty arrays which aren't required.
    $required = ['swagger', 'info', 'paths'];
    foreach ($spec as $key => $item) {
      if (!in_array($key, $required) && is_array($item) && !count($item)) {
        unset($spec[$key]);
      }
    }

    return $spec;
  }

  /**
   * Creates the 'info' portion of the API.
   *
   * @return array
   *   The info elements.
   */
  protected function getInfo() {
    $site_name = $this->configFactory->get('system.site')->get('name');
    return [
      'description' => $this->getApiDescription(),
      'title' => $site_name . ' - ' . $this->getApiName(),
      'version' => 'Versioning not supported',
    ];
  }

  /**
   * Gets the API name.
   *
   * @return string
   *   The API name.
   */
  abstract public function getApiName();

  /**
   * {@inheritdoc}
   */
  public function getBasePath() {
    return $this->request->getBasePath();
  }

  /**
   * {@inheritdoc}
   */
  public function getSecurityDefinitions() {
    $base_url = $this->request->getSchemeAndHttpHost() . '/' . $this->request->getBasePath();
    $auth_providers = $this->authenticationCollector->getSortedProviders();
    $security_definitions = [];

    foreach ($auth_providers as $provider => $info) {
      $def = NULL;
      switch ($provider) {
        case 'basic_auth':
          $def = [
            'type' => 'basic',
          ];
          break;

        case 'oauth2':
          $def = [
            'type' => 'oauth2',
            'description' => 'For more information see https://developers.getbase.com/docs/rest/articles/oauth2/requests',
            'flows' => [
              'password' => [
                'tokenUrl' => $base_url . 'oauth/token',
                'refreshUrl' => $base_url . 'oauth/token',
              ],
              'authorizationCode' => [
                'authorizationUrl' => $base_url . 'oauth/authorize',
                'tokenUrl' => $base_url . 'oauth/token',
                'refreshUrl' => $base_url . 'oauth/token',
              ],
              'implicit' => [
                'authorizationUrl' => $base_url . 'oauth/authorize',
                'refreshUrl' => $base_url . 'oauth/token',
              ],
              'clientCredentials' => [
                'tokenUrl' => $base_url . 'oauth/token',
                'refreshUrl' => $base_url . 'oauth/token',
              ],
            ],
          ];
          break;

        default:
          continue 2;
      }
      if ($def !== NULL) {
        $security_definitions[$provider] = $def;
      }
    }

    // Core's CSRF token doesn't have an auth provider.
    $security_definitions['csrf_token'] = [
      'type' => 'apiKey',
      'name' => 'X-CSRF-Token',
      'in' => 'header',
      'x-tokenUrl' => $base_url . 'session/token',
    ];

    return $security_definitions;
  }

  /**
   * {@inheritdoc}
   */
  public function getSecurity() {
    // @todo #2977109 - Calculate oauth scopes required.
    $security = [];
    foreach (array_keys($this->getSecurityDefinitions()) as $method) {
      $security[] = [$method => []];
    }
    return $security;
  }

  /**
   * {@inheritdoc}
   */
  public function getTags() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getPaths() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getConsumes() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getProduces() {
    return [];
  }

  /**
   * Gets the JSON Schema for an entity type or entity type and bundle.
   *
   * @param string $described_format
   *   The format that will be described, json, json_api, etc.
   * @param string $entity_type_id
   *   The entity type id.
   * @param string $bundle_name
   *   The bundle name.
   *
   * @return array
   *   The JSON schema.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  abstract protected function getJsonSchema($described_format, $entity_type_id, $bundle_name = NULL);

  /**
   * Cleans JSON schema definitions for OpenAPI.
   *
   * @todo Just to test if fixes
   *       https://github.com/OAI/OpenAPI-Specification/issues/229
   *
   * @param array $json_schema
   *   The JSON Schema elements.
   *
   * @return array
   *   The cleaned JSON Schema elements.
   */
  protected function cleanSchema(array $json_schema) {
    foreach ($json_schema as &$value) {
      if ($value === NULL) {
        $value = '';
      }
      else {
        if (is_array($value)) {
          $this->fixDefaultFalse($value);
          $value = $this->cleanSchema($value);
        }
      }
    }
    return $json_schema;
  }

  /**
   * Fix default field value as zero instead of FALSE.
   *
   * @param array $value
   *   JSON Schema field value.
   */
  protected function fixDefaultFalse(array &$value) {
    $type_is_array = isset($value['type']) && $value['type'] === 'array';
    $has_properties = isset($value['items']['properties']) && is_array($value['items']['properties']);
    $has_default = isset($value['default']) && is_array($value['default']);
    if ($type_is_array && $has_properties && $has_default) {
      foreach ($value['items']['properties'] as $property_key => $property) {
        if ($property['type'] === 'boolean') {
          foreach ($value['default'] as &$default_values) {
            if (isset($default_values[$property_key]) && empty($default_values[$property_key])) {
              $default_values[$property_key] = FALSE;
            }
          }
        }
      }
    }
  }

  /**
   * Get possible responses for an entity type.
   *
   * @param string $entity_type_id
   *   The entity type.
   * @param string $method
   *   The method.
   * @param string $bundle_name
   *   The bundle name.
   *
   * @return array
   *   The entity responses.
   */
  protected function getEntityResponses($entity_type_id, $method, $bundle_name = NULL) {
    $method = strtolower($method);
    $responses = [];

    $schema_response = [];
    if ($definition_ref = $this->getDefinitionReference($entity_type_id, $bundle_name)) {
      $schema_response = [
        'schema' => [
          '$ref' => $definition_ref,
        ],
      ];
    }

    switch ($method) {
      case 'get':
        $responses['200'] = [
          'description' => 'successful operation',
        ] + $schema_response;
        break;

      case 'post':
        // @phpstan-ignore-next-line
        unset($responses['200']);
        $responses['201'] = [
          'description' => 'Entity created',
        ] + $schema_response;
        break;

      case 'delete':
        // @phpstan-ignore-next-line
        unset($responses['200']);
        $responses['204'] = [
          'description' => 'Entity deleted',
        ];
        break;
    }
    return $responses;
  }

  /**
   * Gets the reference to the definition in the document.
   *
   * @param string $entity_type_id
   *   The entity type id.
   * @param string $bundle_name
   *   The bundle name.
   *
   * @return string
   *   The reference to the definition.
   */
  protected function getDefinitionReference($entity_type_id, $bundle_name = NULL) {
    $definition_key = $this->getEntityDefinitionKey($entity_type_id, $bundle_name);
    if ($this->definitionExists($definition_key)) {
      $definition_ref = '#/definitions/' . $definition_key;
      return $definition_ref;
    }
    return '';
  }

  /**
   * Gets the entity definition key.
   *
   * @param string $entity_type_id
   *   The entity type.
   * @param string $bundle_name
   *   The bundle name.
   *
   * @return string
   *   The entity definition key. Either [entity_type] or
   *   [entity_type]:[bundle_name]
   */
  protected function getEntityDefinitionKey($entity_type_id, $bundle_name = NULL) {
    $definition_key = $entity_type_id;
    if ($bundle_name) {
      $definition_key .= static::$DEFINITION_SEPARATOR . $bundle_name;
    }
    return $definition_key;
  }

  /**
   * Check whether a definitions exists for a key.
   *
   * @param string $definition_key
   *   The definition to check.
   *
   * @return bool
   *   TRUE if it exists.
   */
  protected function definitionExists($definition_key) {
    $definitions = $this->getDefinitions();
    return isset($definitions[$definition_key]);
  }

  /**
   * Determines if an entity type and/or bundle show be included.
   *
   * @param string $entity_type_id
   *   The entity type id.
   * @param string|null $bundle_name
   *   The bundle name.
   *
   * @return bool
   *   True if the entity type or bundle should be included.
   */
  protected function includeEntityTypeBundle($entity_type_id, $bundle_name = NULL) {
    // Entity types or a specific bundle be can excluded.
    if (isset($this->options['exclude'])) {
      if (array_intersect([$entity_type_id, $this->getEntityDefinitionKey($entity_type_id, $bundle_name)], $this->options['exclude'])) {
        return FALSE;
      }
    }
    if (isset($this->options['entity_mode'])) {
      $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
      if ($this->options['entity_mode'] == 'content_entities') {
        return $entity_type instanceof ContentEntityTypeInterface;
      }
      if ($this->options['entity_mode'] == 'config_entities') {
        return $entity_type instanceof ConfigEntityTypeInterface;
      }
    }
    if (isset($this->options['entity_type_id']) && $this->options['entity_type_id'] !== $entity_type_id) {
      return FALSE;
    }
    if (isset($bundle_name) && isset($this->options['bundle_name']) && $this->options['bundle_name'] !== $bundle_name) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Gets API description.
   *
   * @return string
   *   The API Description.
   */
  abstract protected function getApiDescription();

}
