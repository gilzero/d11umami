<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel\Attribute;

use Drupal\Component\Plugin\Attribute\AttributeBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * The twig_validator_rule attribute.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class TwigValidatorRule extends AttributeBase {

  /**
   * Constructs a new TwigValidatorRule instance.
   *
   * @param string $id
   *   The plugin ID. There are some implementation bugs that make the plugin
   *   available only if the ID follows a specific pattern. It must be either
   *   identical to group or prefixed with the group. E.g. if the group is "foo"
   *   the ID must be either "foo" or "foo:bar".
   * @param string $twig_node_type
   *   The Plugin Twig Node Type to apply to.
   * @param array $rule_on_name
   *   The list of names rules indexed by type.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $label
   *   (optional) The human-readable name of the plugin.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $description
   *   (optional) A brief description of the plugin.
   * @param class-string|null $deriver
   *   (optional) The deriver class.
   */
  public function __construct(
    public readonly string $id,
    public readonly string $twig_node_type,
    public readonly array $rule_on_name,
    public readonly ?TranslatableMarkup $label,
    public readonly ?TranslatableMarkup $description = NULL,
    public readonly ?string $deriver = NULL,
  ) {}

}
