<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\PropType;

use Drupal\Component\Plugin\Definition\PluginDefinitionInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Drupal\ui_patterns\Attribute\PropType;
use Drupal\ui_patterns\PropTypePluginBase;
use Drupal\ui_patterns\SchemaManager\ReferencesResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'links' PropType.
 */
#[PropType(
  id: 'links',
  label: new TranslatableMarkup('Links'),
  description: new TranslatableMarkup('A list of link objects.'),
  default_source: 'menu',
  schema: [
    'type' => 'array',
    'items' => [
      'type' => 'object',
      'properties' => [
        'title' => ['type' => 'string'],
        'url' => ['$ref' => 'ui-patterns://url'],
        'attributes' => ['$ref' => 'ui-patterns://attributes'],
        'link_attributes' => ['$ref' => 'ui-patterns://attributes'],
        'below' => [
          'type' => 'array',
          'items' => [
            'type' => 'object',
          ],
        ],
      ],
    ],
  ],
  priority: 10
)]
class LinksPropType extends PropTypePluginBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, protected ReferencesResolver $referenceResolver) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ui_patterns.schema_reference_solver')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getSchema(): array {
    // Resolve prop type schema, because the resolver run by
    // JsonSchema\Validator (so, by SDC ComponentValidator) is not
    // recursive.
    $plugin_definition = $this->getPluginDefinition();
    $schema = ($plugin_definition instanceof PluginDefinitionInterface) ? [] : ($plugin_definition['schema'] ?? []);
    $schema = $this->referenceResolver->resolve($schema);
    return (array) $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function normalize(mixed $value): mixed {
    if (!is_array($value)) {
      return [];
    }
    return self::normalizeLinks($value);
  }

  /**
   * Normalize links.
   */
  protected static function normalizeLinks(array $value): array {
    // Don't inject URL object into patterns templates, use "title" as item
    // label and "url" as item target.
    foreach ($value as $index => $item) {
      if (!is_array($item)) {
        unset($value[$index]);
        continue;
      }
      $item = self::normalizeAttributes($item);
      if (array_key_exists("text", $item)) {
        // Examples: links.html.twig, breadcrumb.html.twig, pager.html.twig,
        // views_mini_pager.html.twig.
        if (is_scalar($item["text"]) || $item["text"] instanceof \Stringable) {
          $item["title"] = (string) $item["text"];
        }
        else {
          $item["title"] = $item["text"];
        }
        unset($item["text"]);
      }
      if (!array_key_exists("title", $item)) {
        $item["title"] = (string) $index;
      }
      if (array_key_exists("href", $item)) {
        // Examples: pager.html.twig, views_mini_pager.html.twig.
        $item["url"] = $item["href"];
        unset($item["href"]);
      }
      $item = self::extractLinkData($item);
      $item = self::normalizeUrl($item);
      $item = static::normalizeAttributes($item, "link_attributes");
      if (array_key_exists("below", $item)) {
        $item["below"] = self::normalize($item["below"]);
      }
      $value[$index] = $item;
    }
    return array_values($value);
  }

  /**
   * Normalize attributes in an item.
   */
  protected static function normalizeAttributes(array $item, string $property = "attributes"): array {
    if (!array_key_exists($property, $item)) {
      return $item;
    }
    if (is_a($item[$property], '\Drupal\Core\Template\Attribute')) {
      $item[$property] = $item[$property]->toArray();
    }
    // Empty PHP arrays are converted in JSON arrays instead of JSON objects
    // by json_encode(), so it is better to remove them.
    if (is_array($item[$property]) && empty($item[$property])) {
      unset($item[$property]);
      return $item;
    }
    // Convert URL objects to strings.
    foreach ($item[$property] as $attr => $value) {
      if ($value instanceof Url) {
        $item[$property][$attr] = $value->toString();
      }
    }
    return $item;
  }

  /**
   * Extract data from link.
   *
   * Useful for: links.html.twig.
   */
  protected static function extractLinkData(array $item): array {
    if (isset($item["url"])) {
      return $item;
    }
    if (!isset($item["link"])) {
      return $item;
    }
    $item["url"] = $item["link"]["#url"];
    if (isset($item["link"]["#options"])) {
      $item["url"]->mergeOptions($item["link"]["#options"]);
    }
    unset($item["link"]);
    return $item;
  }

  /**
   * Normalize URL in an item.
   *
   * Useful for: menu.html.twig, links.html.twig.
   */
  private static function normalizeUrl(array $item): array {
    if (!array_key_exists("url", $item)) {
      return $item;
    }
    $url = $item["url"];
    if (!($url instanceof Url)) {
      return $item;
    }
    if ($url->isRouted() && ($url->getRouteName() === '<nolink>')) {
      unset($item["url"]);
    }
    elseif ($url->isRouted() && ($url->getRouteName() === '<button>')) {
      unset($item["url"]);
    }
    else {
      $item["url"] = $url->toString();
    }
    $item = self::extractUrlOptions($url, $item);
    return $item;
  }

  /**
   * Extract URL options.
   */
  private static function extractUrlOptions(Url $url, array $item): array {
    $options = $url->getOptions();
    self::setHrefLang($options);
    self::setActiveClass($options, $url);
    if (isset($options["attributes"])) {
      $item = self:: mergeUrlAttributes($options["attributes"], $item);
    }
    return $item;
  }

  /**
   * Merge Url attributes.
   *
   * $options["attributes"] is always an associative array of HTML attributes.
   * But $item["link_attributes"] can vary.
   */
  private static function mergeUrlAttributes(array $url_attributes, array $item): array {
    if (!isset($item["link_attributes"])) {
      $item["link_attributes"] = $url_attributes;
      return $item;
    }
    if (is_a($item["link_attributes"], '\Drupal\Core\Template\Attribute')) {
      $item["link_attributes"] = $item["link_attributes"]->toArray();
    }
    if (is_array($item["link_attributes"])) {
      $item["link_attributes"] = array_merge(
        $item["link_attributes"],
        $url_attributes,
      );
      return $item;
    }
    return $item;
  }

  /**
   * Set hreflang attribute.
   *
   * Add a hreflang attribute if we know the language of this link's URL and
   * hreflang has not already been set.
   *
   * @param array $options
   *   The URL options.
   *
   * @see \Drupal\Core\Utility\LinkGenerator::generate()
   */
  private static function setHrefLang(array &$options): void {
    if (isset($options['language'])
      && ($options['language'] instanceof LanguageInterface)
      && !isset($options['attributes']['hreflang'])
    ) {
      $options['attributes']['hreflang'] = $options['language']->getId();
    }
  }

  /**
   * Set the attributes to have the active class placed by JS.
   *
   * @param array $options
   *   The URL options.
   * @param \Drupal\Core\Url $url
   *   The URL object.
   *
   * @see \Drupal\Core\Utility\LinkGenerator::generate()
   */
  private static function setActiveClass(array &$options, Url $url): void {
    // Set the "active" class if the 'set_active_class' option is not empty.
    if (!empty($options['set_active_class']) && !$url->isExternal()) {
      // Add a "data-drupal-link-query" attribute to let the
      // drupal.active-link library know the query in a standardized manner.
      if (!empty($options['query'])) {
        $query = $options['query'];
        ksort($query);
        $options['attributes']['data-drupal-link-query'] = Json::encode($query);
      }

      // Add a "data-drupal-link-system-path" attribute to let the
      // drupal.active-link library know the path in a standardized manner.
      if ($url->isRouted() && !isset($options['attributes']['data-drupal-link-system-path'])) {
        // @todo System path is deprecated - use the route name and parameters.
        $system_path = $url->getInternalPath();

        // Special case for the front page.
        if ($url->getRouteName() === '<front>') {
          $system_path = '<front>';
        }

        if (!empty($system_path)) {
          $options['attributes']['data-drupal-link-system-path'] = $system_path;
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function preprocess(mixed $value): mixed {
    foreach ($value as $index => &$item) {
      if (!is_array($item)) {
        continue;
      }
      if (array_key_exists("attributes", $item) && is_array($item['attributes'])) {
        $item["attributes"] = new Attribute($item["attributes"]);
      }
      if (array_key_exists("link_attributes", $item) && is_array($item['link_attributes'])) {
        $item["link_attributes"] = new Attribute($item["link_attributes"]);
      }
      if (array_key_exists("below", $item)) {
        $item["below"] = self::preprocess($item["below"]);
      }
      $value[$index] = $item;
    }
    return $value;
  }

}
