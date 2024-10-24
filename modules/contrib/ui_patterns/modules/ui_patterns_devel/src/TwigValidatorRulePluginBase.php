<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Twig\Node\Node;

/**
 * Base class for twig_validator_rule plugins.
 */
abstract class TwigValidatorRulePluginBase extends PluginBase implements TwigValidatorRuleInterface {

  protected const RULE_NAME_IGNORE = -1;
  protected const RULE_NAME_ALLOW = 0;
  protected const RULE_NAME_WARN = RfcLogLevel::WARNING;
  protected const RULE_NAME_DEPRECATE = RfcLogLevel::NOTICE;
  protected const RULE_NAME_FORBID = RfcLogLevel::ERROR;

  /**
   * {@inheritdoc}
   */
  public function getRulesByName(): array {
    // @phpstan-ignore-next-line
    return $this->pluginDefinition['rule_on_name'] ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function getNameIgnore(): array {
    return $this->getRulesByName()[self::RULE_NAME_IGNORE] ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function getNameAllow(): array {
    return $this->getRulesByName()[self::RULE_NAME_ALLOW] ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function getNameDeprecate(): array {
    return $this->getRulesByName()[self::RULE_NAME_DEPRECATE] ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function getNameWarn(): array {
    return $this->getRulesByName()[self::RULE_NAME_WARN] ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function getNameForbid(): array {
    return $this->getRulesByName()[self::RULE_NAME_FORBID] ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function label(): string {
    // Cast the label to a string since it's a TranslatableMarkup object.
    // @phpstan-ignore-next-line
    return (string) $this->pluginDefinition['label'];
  }

  /**
   * Find function to execute.
   *
   * @param string $name
   *   The function name of the node.
   *
   * @return string
   *   The function name.
   */
  protected static function getRuleMethodToCall(string $name): ?string {
    $func = \str_replace([' ', '-', '_'], '', \ucwords($name));
    return \method_exists(static::class, $func) ? $func : NULL;
  }

  /**
   * Get node attribute value.
   *
   * @param \Twig\Node\Node $node
   *   The Twig node being processed.
   * @param string $name
   *   The name of the node.
   * @param string $attribute
   *   The name of the attribute.
   *
   * @return mixed
   *   The value.
   */
  protected static function getValue(Node $node, string $name, string $attribute): mixed {
    if (!$node->hasNode($name)) {
      return NULL;
    }

    $target = $node->getNode($name);

    if ($target->hasAttribute($attribute)) {
      return $target->getAttribute($attribute);
    }

    return NULL;
  }

  /**
   * Check rules on forbidden, allowed or deprecated.
   *
   * @param string $id
   *   The ID of the node.
   * @param \Twig\Node\Node $node
   *   The Twig node being processed.
   * @param string $name
   *   The name to check.
   * @param string $type
   *   The type controlled, for message.
   *
   * @return \Drupal\ui_patterns_devel\ValidatorMessage[]
   *   An array of errors encountered.
   */
  protected function ruleAllowedForbiddenDeprecated(string $id, Node $node, string $name, string $type): array {
    if (\in_array($name, $this->getNameIgnore(), TRUE)) {
      return [];
    }

    if (\in_array($name, $this->getNameAllow(), TRUE)) {
      return [];
    }

    $errors = [];

    if (\in_array($name, \array_keys($this->getNameForbid()), TRUE)) {
      $errors[] = $this->handleNameCase($id, $node, $name, $type, $this->getNameForbid(), new TranslatableMarkup('Forbidden'), RfcLogLevel::ERROR);
    }
    elseif (\in_array($name, \array_keys($this->getNameWarn()), TRUE)) {
      $errors[] = $this->handleNameCase($id, $node, $name, $type, $this->getNameWarn(), new TranslatableMarkup('Careful with'), RfcLogLevel::WARNING);
    }
    elseif (\in_array($name, \array_keys($this->getNameDeprecate()), TRUE)) {
      $errors[] = $this->handleNameCase($id, $node, $name, $type, $this->getNameDeprecate(), new TranslatableMarkup('Deprecated'), RfcLogLevel::WARNING);
    }
    else {
      $errors[] = $this->handleNameCase($id, $node, $name, $type, [$name => ''], new TranslatableMarkup('Gray list'), RfcLogLevel::WARNING);
    }

    return \array_filter($errors);
  }

  /**
   * Handle a specific case.
   *
   * @param string $id
   *   The ID of the node.
   * @param \Twig\Node\Node $node
   *   The Twig node being processed.
   * @param string $name
   *   The name to check.
   * @param string $type
   *   The type controlled, for message.
   * @param array $rules
   *   Rule names to check.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $prefix
   *   Message prefix.
   * @param int $logLevel
   *   Error level.
   *
   * @return \Drupal\ui_patterns_devel\ValidatorMessage|null
   *   The error if any.
   */
  protected function handleNameCase(string $id, Node $node, string $name, string $type, array $rules, TranslatableMarkup $prefix, int $logLevel = RfcLogLevel::ERROR): ?ValidatorMessage {
    if (\in_array($name, \array_keys($rules), TRUE)) {
      return ValidatorMessage::createForNode($id, $node, new TranslatableMarkup(
            '@prefix Twig @type: `@name`. @tip',
            ['@prefix' => $prefix, '@type' => $type, '@name' => $name, '@tip' => $rules[$name] ?? '']
        ), $logLevel);
    }
    return NULL;
  }

}
