<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\Render\Component\Exception\InvalidComponentException;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Twig\Error\Error as TwigError;
use Twig\Node\Node;
use Twig\Source;

/**
 * Class ValidatorMessage to handle and format errors.
 */
final class ValidatorMessage {

  private const TYPE_DEFINITION = 1;
  private const TYPE_TWIG = 2;

  private function __construct(
    // @phpstan-ignore-next-line
    private string $id,
    private int $type,
    private TranslatableMarkup $message,
    private int $level,
    private int $line = 0,
    private int $length = 0,
    private ?Source $source = NULL,
    private ?TranslatableMarkup $tip = NULL,
  ) {}

  /**
   * Create a ValidatorMessage instance for a Component exception.
   *
   * @param string $id
   *   The identifier for the component.
   * @param \Drupal\Core\Render\Component\Exception\InvalidComponentException $error
   *   The Component error object.
   * @param int $line
   *   The message line.
   * @param int $length
   *   The source length.
   * @param string|null $source
   *   The source string.
   * @param int $level
   *   The message level (default: RfcLogLevel::ERROR).
   *
   * @return self
   *   A new ValidatorMessage instance.
   */
  public static function createForInvalidComponentException(string $id, InvalidComponentException $error, int $line = 0, int $length = 0, ?string $source = NULL, int $level = RfcLogLevel::ERROR): self {
    // phpcs:disable Drupal.Semantics.FunctionT.NotLiteralString
    return new self($id, self::TYPE_DEFINITION, new TranslatableMarkup($error->getMessage()), $level, $line, $length, self::createSource($source), NULL);
    // phpcs:enable Drupal.Semantics.FunctionT.NotLiteralString
  }

  /**
   * Create a ValidatorMessage instance for a Twig Node.
   *
   * @param string $id
   *   The identifier for the error.
   * @param \Twig\Node\Node $node
   *   The Twig Node object.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $message
   *   The message message.
   * @param int $level
   *   The message level (default: RfcLogLevel::ERROR).
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $tip
   *   An optional tip to provide additional information (default: NULL).
   *
   * @return self
   *   A new ValidatorMessage instance.
   */
  public static function createForNode(string $id, Node $node, TranslatableMarkup $message, int $level = RfcLogLevel::ERROR, ?TranslatableMarkup $tip = NULL): self {
    return new self($id, self::TYPE_TWIG, $message, $level, $node->getTemplateLine(), 1, $node->getSourceContext(), $tip);
  }

  /**
   * Create a ValidatorMessage instance for a custom string error.
   *
   * @param string $id
   *   The identifier for the error.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $message
   *   The message message.
   *
   * @return self
   *   A new ValidatorMessage instance.
   */
  public static function createForTwigString(string $id, TranslatableMarkup $message): self {
    return new self($id, self::TYPE_TWIG, $message, RfcLogLevel::ERROR);
  }

  /**
   * Create a ValidatorMessage instance for a custom string error.
   *
   * @param string $id
   *   The identifier for the error.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $message
   *   The message message.
   * @param int $level
   *   The message level (default: RfcLogLevel::ERROR).
   * @param int $line
   *   The message line.
   * @param int $length
   *   The source length.
   * @param string|null $source
   *   The source string.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $tip
   *   An optional tip to provide additional information (default: NULL).
   *
   * @return self
   *   A new ValidatorMessage instance.
   */
  public static function createForString(string $id, TranslatableMarkup $message, int $level = RfcLogLevel::ERROR, int $line = 0, int $length = 0, ?string $source = NULL, ?TranslatableMarkup $tip = NULL): self {
    return new self($id, self::TYPE_DEFINITION, $message, $level, $line, $length, self::createSource($source), $tip);
  }

  /**
   * Create a ValidatorMessage instance for a Twig Error.
   *
   * @param string $id
   *   The identifier for the error.
   * @param \Twig\Error\Error $error
   *   The Twig Error object.
   * @param int $level
   *   The message level (default: RfcLogLevel::ERROR).
   *
   * @return self
   *   A new ValidatorMessage instance.
   */
  public static function createFromTwigError(string $id, TwigError $error, int $level = RfcLogLevel::CRITICAL): self {
    // phpcs:disable Drupal.Semantics.FunctionT.NotLiteralString
    return new self($id, self::TYPE_TWIG, new TranslatableMarkup($error->getMessage()), $level, $error->getTemplateLine(), 1, $error->getSourceContext());
  }

  /**
   * Getter for the level.
   *
   * @return int
   *   The message level.
   */
  public function level(): int {
    return $this->level;
  }

  /**
   * Getter for the line.
   *
   * @return int
   *   The message line number.
   */
  public function line(): int {
    return $this->line;
  }

  /**
   * Getter for the message.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The message message.
   */
  public function message(): TranslatableMarkup {
    return $this->message;
  }

  /**
   * Getter for the type.
   *
   * @return int
   *   The message type.
   */
  public function type(): int {
    return $this->type;
  }

  /**
   * Getter for the type as string.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The message type.
   */
  public function getType(): TranslatableMarkup {
    return new TranslatableMarkup(
      $this->type === self::TYPE_TWIG ? 'Twig' : 'Schema',
      [],
      ['context' => 'ui_patterns_devel']
    );
  }

  /**
   * Getter for the message with tip.
   *
   * @return \Drupal\Component\Render\FormattableMarkup
   *   The message message.
   */
  public function messageWithTip(): FormattableMarkup {
    if ($this->tip) {
      return new FormattableMarkup(sprintf('%s<br>%s', $this->message, $this->tip), []);
    }
    return $this->message;
  }

  /**
   * Getter for the message code line.
   *
   * @return string
   *   The source code line.
   */
  public function getSourceCode(): string {
    if ($this->source === NULL) {
      return '';
    }

    $lines = \explode(\PHP_EOL, $this->source->getCode());
    if ($this->length > 1) {
      return \implode(\PHP_EOL, \array_slice($lines, $this->line - 1, $this->length));
    }

    return $lines[$this->line - 1] ?? '';
  }

  /**
   * Generate the source.
   *
   * @param string|null $source
   *   The source code.
   *
   * @return \Twig\Source|null
   *   The source code for Twig.
   */
  private static function createSource(?string $source): ?Source {
    return $source !== NULL ? new Source($source, 'definition') : NULL;
  }

}
