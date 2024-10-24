<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel\Plugin\TwigValidatorRule;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns_devel\Attribute\TwigValidatorRule;
use Drupal\ui_patterns_devel\TwigValidator\TwigNodeFinder;
use Drupal\ui_patterns_devel\TwigValidatorRulePluginBase;
use Drupal\ui_patterns_devel\ValidatorMessage;
use Twig\Node\Node;

/**
 * Plugin implementation of the twig_validator_rule.
 *
 * @see https://www.drupal.org/docs/develop/theming-drupal/twig-in-drupal/functions-in-twig-templates
 * @see web/core/lib/Drupal/Core/Template/TwigExtension.php
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 */
#[TwigValidatorRule(
  id: 'function',
  twig_node_type: 'Twig\Node\Expression\FunctionExpression',
  rule_on_name: [
    self::RULE_NAME_IGNORE => [
      // Internal drupal render function that run on all variables.
      'render_var',
    ],
    self::RULE_NAME_ALLOW => [
      // @todo warning for create_attribute()?
      'create_attribute',
      'random',
      'range',
    ],
    self::RULE_NAME_WARN => [
      'source' => 'Bad architecture, but sometimes needed for shared static files.',
      'component_story' => 'Not expected in real usage components.',
      'include' => 'Use slots instead of hard embedding a component in the template with `include`.',
    ],
    self::RULE_NAME_FORBID => [
      'active_theme' => 'Keep components sandboxed by avoiding functions calling Drupal application.',
      'active_theme_path' => 'Keep components sandboxed by avoiding functions calling Drupal application.',
      'attach_library' => 'The asset library attachment would be more discoverable if declared in the component definition.',
      'attribute' => 'Useless and confusing, see https://www.drupal.org/project/ui_suite_bootstrap/issues/3382230.',
      'constant' => 'Keep components sandboxed by avoiding functions calling Drupal application.',
      'date' => 'Too business & l10n related.',
      'file_url' => 'Should avoid using.',
      'link' => 'PHP URL object, or useless if URL string.',
      'path' => 'Keep components sandboxed by avoiding functions calling Drupal application.',
      'url' => 'Keep components sandboxed by avoiding functions calling Drupal application.',
      'block' => 'Use slots instead of hard embedding a component in the template with `block`.',
      'parent' => 'Use slots instead of hard embedding a component in the template with `parent`.',
      'pattern_preview' => 'Legacy UI Patterns 1, not expected in real usage components.',
      'help_route_link' => 'Bad architecture: Help Drupal module only.',
      'help_topic_link' => 'Bad architecture: Help Drupal module only.',
      'dump' => 'Development only.',
      'devel_dump' => 'Development only.',
      'kpr' => 'Development only.',
      'kint' => 'Development only.',
      'devel_message' => 'Development only.',
      'dpm' => 'Development only.',
      'dsm' => 'Development only.',
      'add_component_context' => 'Development only.',
      'validate_component_props' => 'Development only.',
      'sdc_additional_context' => 'Deprecated and development only.',
      'sdc_validate_props' => 'Deprecated and development only.',
      'country_names' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'country_timezones' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'currency_names' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'html_classes' => 'Needs specific Twig extension.',
      'language_names' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'locale_names' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'script_names' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'template_from_string' => 'Bad architecture.',
      'timezone_names' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'wp_dump' => 'Development only.',
      'query_type' => 'Development only.',
      'query_executable' => 'Development only.',
    ],
    self::RULE_NAME_DEPRECATE => [
      'pattern' => 'Replace with Twig function component().',
    ],
  ],
  label: new TranslatableMarkup('Function rules'),
  description: new TranslatableMarkup('Rules around Twig functions.'),
)]
final class TwigValidatorRuleFunction extends TwigValidatorRulePluginBase {

  /**
   * {@inheritdoc}
   */
  public function processNode(string $id, Node $node, array $definition, array $variableSet): array {
    if (!$node->hasAttribute('name')) {
      return [];
    }

    $name = $node->getAttribute('name');

    $errors = $this->ruleAllowedForbiddenDeprecated($id, $node, $name, 'function');

    if ($func = $this->getRuleMethodToCall($name)) {
      $errors = \array_merge($errors, $this::$func($id, $node));
    }

    return $errors;
  }

  /**
   * Check function random.
   *
   * @param string $id
   *   The ID of the node.
   * @param \Twig\Node\Node $node
   *   The Twig node being processed.
   *
   * @return \Drupal\ui_patterns_devel\ValidatorMessage[]
   *   An array of errors encountered during the validation process.
   */
  private static function random(string $id, Node $node): array {
    $errors = [];

    $inDefaultFilter = TwigNodeFinder::findParentIs(
      $node,
      'Twig\Node\Expression\Filter\DefaultFilter'
    );

    if (!$inDefaultFilter) {
      $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Function `random()` must be used in a `default()` filter!'));
    }
    return $errors;
  }

}
