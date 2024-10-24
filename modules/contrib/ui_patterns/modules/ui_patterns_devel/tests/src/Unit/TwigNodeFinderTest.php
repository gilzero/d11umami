<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns_devel\Unit;

use Drupal\Core\Template\Loader\StringLoader;
use Drupal\Tests\UnitTestCase;
use Drupal\ui_patterns_devel\TwigValidator\SetParentNodeAsAttribute;
use Drupal\ui_patterns_devel\TwigValidator\TwigNodeFinder;
use Twig\Environment;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\NodeTraverser;
use Twig\NodeVisitor\NodeVisitorInterface;

/**
 * Simple test for Twig Finder helper class.
 *
 * @coversDefaultClass \Drupal\ui_patterns_devel\TwigValidator\TwigNodeFinder
 *
 * @group ui_patterns_devel
 * @internal
 *
 * phpcs:disable Drupal.Commenting.VariableComment.Missing
 */
class TwigNodeFinderTest extends UnitTestCase {

  private Environment $twig;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $loader = new StringLoader();
    $this->twig = new Environment($loader);
    $this->twig->enableDebug();
  }

  /**
   * Data provider for testFindParentIs.
   */
  public function findParentIsDataProvider(): array {
    return [
      // Test random() not in default().
      [
        '{{ random() }}',
        'Twig\Node\Expression\FunctionExpression',
        'Twig\Node\Expression\Filter\DefaultFilter',
        'random',
        FALSE,
      ],
      // Test random() in default().
      [
        '{{ foo | default(random()) }}',
        'Twig\Node\Expression\FunctionExpression',
        'Twig\Node\Expression\Filter\DefaultFilter',
        'random',
        TRUE,
      ],
    ];
  }

  /**
   * Test the method findParentIs().
   *
   * @param string $twigText
   *   The twig template content.
   * @param string $nodeClass
   *   The node class to filter.
   * @param string $findClass
   *   The node class to find.
   * @param string $attributeValue
   *   The attribute 'name' value to look.
   * @param bool $expected
   *   The result expected.
   *
   * @covers ::findParentIs
   * @dataProvider findParentIsDataProvider
   */
  public function testFindParentRandomIsDefault(string $twigText, string $nodeClass, string $findClass, string $attributeValue, bool $expected): void {

    $nodeTree = $this->createNodeTree($twigText);
    $nodeTraverser = new NodeTraverser($this->twig, [new SetParentNodeAsAttribute()]);
    $nodeTraverser->traverse($nodeTree);

    $testVisitor = new TestVisitor($nodeClass, $findClass, $attributeValue);
    $nodeTraverser = new NodeTraverser($this->twig);
    $nodeTraverser->addVisitor($testVisitor);
    $nodeTraverser->traverse($nodeTree);

    $found = FALSE;
    if ($testVisitor->resultList) {
      $found = TRUE;
    }
    $this->assertSame($expected, $found);
  }

  /**
   * Data provider for testFilterParents.
   */
  public function filterParentsDataProvider(): array {
    return [
      // A variable is inside a macro.
      [
        '{% macro test(macro_1, macro_unused_1) %}{{ macro_1 }}{% endmacro %}',
        'Twig\Node\Expression\NameExpression',
        'Twig\Node\MacroNode',
        1,
      ],
      // A variable is not set in the template.
      [
        '{{ foo }}',
        'Twig\Node\Expression\AssignNameExpression',
        'Twig\Node\SetNode',
        0,
      ],
      // A variable is set before.
      [
        '{% set foo = "foo" %}{{ foo }}',
        'Twig\Node\Expression\AssignNameExpression',
        'Twig\Node\SetNode',
        1,
      ],
    ];
  }

  /**
   * Test the filterParents method.
   *
   * @param string $twigText
   *   The twig template content.
   * @param string $nodeClass
   *   The class for node to filter.
   * @param string $findClass
   *   The class to find in the tree.
   * @param int $expectedCount
   *   The count result expected.
   *
   * @covers ::filterParents
   * @dataProvider filterParentsDataProvider
   */
  public function testFilterParents(string $twigText, string $nodeClass, string $findClass, int $expectedCount): void {
    $nodeTree = $this->createNodeTree($twigText);
    $nodeTraverser = new NodeTraverser($this->twig, [new SetParentNodeAsAttribute()]);
    $nodeTraverser->traverse($nodeTree);

    $testVisitor = new TestVisitor($nodeClass, $findClass);
    $nodeTraverser = new NodeTraverser($this->twig);
    $nodeTraverser->addVisitor($testVisitor);
    $nodeTraverser->traverse($nodeTree);

    $this->assertSame($expectedCount, $testVisitor->resultList ? \count($testVisitor->resultList) : 0);
  }

  /**
   * Helper to create the NodeTree.
   *
   * @param string $raw
   *   The source of the Twig template.
   *
   * @return \Twig\Node\ModuleNode
   *   The NodeTree.
   */
  private function createNodeTree(string $raw): ModuleNode {
    $template = $this->twig->createTemplate($raw);
    $source = $template->getSourceContext();
    return $this->twig->parse($this->twig->tokenize($source));
  }

}

/**
 * Test class for the Node visitor.
 */
final class TestVisitor implements NodeVisitorInterface {

  public function __construct(
    private string $nodeClass,
    private string $findClass,
    private ?string $attributeValue = NULL,
    public ?array $resultList = NULL,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function enterNode(Node $node, Environment $env): Node {
    if (!\is_a($node, $this->nodeClass)) {
      return $node;
    }

    if ($this->attributeValue) {
      if (!$node->hasAttribute('name')) {
        return $node;
      }
      if ($node->getAttribute('name') !== $this->attributeValue) {
        return $node;
      }
    }

    $twigNodeFinder = new TwigNodeFinder();
    if ($this->attributeValue) {
      $result = $twigNodeFinder->findParentIs($node, $this->findClass);
      if (!empty($result)) {
        $this->resultList[] = $result;
      }
    }
    else {
      // For test the closure is a little different than real world use case in
      // TwigVariableCollectorVisitor.
      $class = $this->findClass;
      $fn = static function (Node $node) use (&$class) {
        return ($class === \get_class($node));
      };
      $result = $twigNodeFinder->filterParents($node, $fn);
      if (!empty($result)) {
        $this->resultList[] = $result;
      }
    }

    return $node;
  }

  /**
   * {@inheritdoc}
   */
  public function getPriority(): int {
    return 10;
  }

  /**
   * {@inheritdoc}
   */
  public function leaveNode(Node $node, Environment $env): Node {
    return $node;
  }

}
