<?php

declare(strict_types=1);

namespace Drupal\Tests\typed_data\Kernel;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\ListDataDefinitionInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\typed_data\Exception\InvalidArgumentException;

/**
 * Tests that data fetcher definition fetching functions work correctly.
 *
 * @coversDefaultClass \Drupal\typed_data\DataFetcher
 *
 * @group typed_data
 */
class DataDefinitionFetcherTest extends KernelTestBase {

  /**
   * The data fetcher object we want to test.
   *
   * @var \Drupal\typed_data\DataFetcherInterface
   */
  protected $dataFetcher;

  /**
   * The typed data manager.
   *
   * @var \Drupal\Core\TypedData\TypedDataManagerInterface
   */
  protected $typedDataManager;

  /**
   * The data definition of our page node used for testing.
   *
   * @var \Drupal\Core\Entity\TypedData\EntityDataDefinitionInterface
   */
  protected $nodeDefinition;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['typed_data', 'system', 'node', 'field', 'user'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('node');

    $this->dataFetcher = $this->container->get('typed_data.data_fetcher');
    $this->typedDataManager = $this->container->get('typed_data_manager');

    $entity_type_manager = $this->container->get('entity_type.manager');
    $entity_type_manager->getStorage('node_type')
      ->create(['type' => 'page'])
      ->save();

    // Create a multi-value integer field for testing.
    FieldStorageConfig::create([
      'field_name' => 'field_integer',
      'type' => 'integer',
      'entity_type' => 'node',
      'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
    ])->save();
    // Bundle the field with an entity.
    FieldConfig::create([
      'field_name' => 'field_integer',
      'entity_type' => 'node',
      'bundle' => 'page',
    ])->save();

    $node = $entity_type_manager->getStorage('node')
      ->create([
        'title' => 'test',
        'type' => 'page',
      ]);
    $this->nodeDefinition = $node->getTypedData()->getDataDefinition();
  }

  /**
   * @covers ::fetchDefinitionByPropertyPath
   */
  public function testFetchingByBasicPropertyPath(): void {
    $definition = $this->nodeDefinition->getPropertyDefinition('title');
    if ($definition->isList()) {
      $this->assertInstanceOf(ListDataDefinitionInterface::class, $definition);
      /** @var \Drupal\Core\TypedData\ListDataDefinitionInterface $definition */
      $definition = $definition->getItemDefinition();
    }
    /** @var \Drupal\Core\Field\TypedData\FieldItemDataDefinitionInterface $definition */
    $target_definition = $definition->getPropertyDefinition('value');

    // List syntax.
    $fetched_definition = $this->dataFetcher->fetchDefinitionByPropertyPath(
      $this->nodeDefinition,
      'title.0.value'
    );
    $this->assertSame($target_definition, $fetched_definition);

    // Single-valued property syntax.
    $fetched_definition = $this->dataFetcher->fetchDefinitionByPropertyPath(
      $this->nodeDefinition,
      'title.value'
    );
    $this->assertSame($target_definition, $fetched_definition);
  }

  /**
   * @covers ::fetchDefinitionBySubPaths
   */
  public function testFetchingByBasicSubPath(): void {
    /** @var \Drupal\Core\Field\BaseFieldDefinition $field_definition */
    $field_definition = $this->nodeDefinition->getPropertyDefinition('title');
    /** @var \Drupal\Core\Field\TypedData\FieldItemDataDefinition $item_definition */
    $item_definition = $field_definition->getItemDefinition();
    /** @var \Drupal\Core\TypedData\DataDefinition $target_definition */
    $target_definition = $item_definition->getPropertyDefinition('value');

    $fetched_definition = $this->dataFetcher->fetchDefinitionBySubPaths(
      $this->nodeDefinition,
      ['title', '0', 'value']
    );

    $this->assertSame($target_definition, $fetched_definition);
  }

  /**
   * @covers ::fetchDefinitionByPropertyPath
   */
  public function testFetchingEntityReference(): void {
    /** @var \Drupal\Core\Field\BaseFieldDefinition $field_definition */
    $field_definition = $this->nodeDefinition->getPropertyDefinition('uid');
    /** @var \Drupal\Core\Field\TypedData\FieldItemDataDefinition $item_definition */
    $item_definition = $field_definition->getItemDefinition();
    /** @var \Drupal\Core\TypedData\DataReferenceDefinition $target_definition */
    $target_definition = $item_definition->getPropertyDefinition('entity');

    $fetched_definition = $this->dataFetcher->fetchDefinitionByPropertyPath(
      $this->nodeDefinition,
      'uid.entity'
    );

    $this->assertSame($target_definition, $fetched_definition);
  }

  /**
   * @covers ::fetchDefinitionByPropertyPath
   */
  public function testFetchingAcrossReferences(): void {
    /** @var \Drupal\Core\Field\BaseFieldDefinition $field_definition */
    $field_definition = $this->nodeDefinition->getPropertyDefinition('uid');
    /** @var \Drupal\Core\Field\TypedData\FieldItemDataDefinition $item_definition */
    $item_definition = $field_definition->getItemDefinition();
    /** @var \Drupal\Core\TypedData\DataReferenceDefinition $reference_definition */
    $reference_definition = $item_definition->getPropertyDefinition('entity');
    /** @var \Drupal\Core\Entity\TypedData\EntityDataDefinition $entity_definition */
    $entity_definition = $reference_definition->getTargetDefinition();
    /** @var \Drupal\Core\Field\BaseFieldDefinition $field_definition */
    $field_definition = $entity_definition->getPropertyDefinition('name');
    /** @var \Drupal\Core\Field\TypedData\FieldItemDataDefinition $item_definition */
    $item_definition = $field_definition->getItemDefinition();
    /** @var \Drupal\Core\Entity\TypedData\EntityDataDefinition $target_definition */
    $target_definition = $item_definition->getPropertyDefinition('value');

    $fetched_definition = $this->dataFetcher->fetchDefinitionByPropertyPath(
      $this->nodeDefinition,
      'uid.entity.name.value'
    );

    $this->assertSame($target_definition, $fetched_definition);
  }

  /**
   * @covers ::fetchDefinitionByPropertyPath
   */
  public function testFetchingAtValidPositions(): void {
    /** @var \Drupal\Core\Field\BaseFieldDefinition $field_definition */
    $field_definition = $this->nodeDefinition->getPropertyDefinition('field_integer');
    /** @var \Drupal\Core\Field\TypedData\FieldItemDataDefinition $item_definition */
    $item_definition = $field_definition->getItemDefinition();
    /** @var \Drupal\Core\TypedData\DataReferenceDefinition $target_definition */
    $target_definition = $item_definition->getPropertyDefinition('value');

    $fetched_definition = $this->dataFetcher->fetchDefinitionByPropertyPath(
      $this->nodeDefinition,
      'field_integer.0.value'
    );

    $this->assertSame($target_definition, $fetched_definition);

    $fetched_definition = $this->dataFetcher->fetchDefinitionByPropertyPath(
      $this->nodeDefinition,
      'field_integer.1.value'
    );

    $this->assertSame($target_definition, $fetched_definition);
  }

  /**
   * @covers ::fetchDefinitionByPropertyPath
   */
  public function testFetchingInvalidProperty(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage("Unable to apply data selector 'field_invalid.0.value' at 'field_invalid'");
    // This should trigger an exception.
    $this->dataFetcher->fetchDefinitionByPropertyPath(
      $this->nodeDefinition,
      'field_invalid.0.value'
    );
  }

  /**
   * @covers ::fetchDefinitionByPropertyPath
   */
  public function testFetchingField(): void {
    $target_definition = $this->nodeDefinition->getPropertyDefinition('field_integer');

    $fetched_definition = $this->dataFetcher->fetchDefinitionByPropertyPath(
      $this->nodeDefinition,
      'field_integer'
    );

    $this->assertSame($target_definition, $fetched_definition);
  }

  /**
   * @covers ::fetchDefinitionByPropertyPath
   */
  public function testFetchingReferenceField(): void {
    $target_definition = $this->nodeDefinition->getPropertyDefinition('uid');

    $fetched_definition = $this->dataFetcher->fetchDefinitionByPropertyPath(
      $this->nodeDefinition,
      'uid'
    );

    $this->assertSame($target_definition, $fetched_definition);
  }

  /**
   * @covers ::fetchDefinitionByPropertyPath
   */
  public function testFetchingNonComplexType(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage("The data selector 'field_integer.0.value.not_existing' cannot be applied because the parent property 'value' is not a list or a complex structure");
    // This should trigger an exception.
    $this->dataFetcher->fetchDefinitionByPropertyPath(
      $this->nodeDefinition,
      'field_integer.0.value.not_existing'
    );
  }

  /**
   * @covers ::fetchDefinitionByPropertyPath
   */
  public function testFetchingFromPrimitive(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage("The data selector 'unknown_property' cannot be applied because the definition of type 'string' is not a list or a complex structure");

    /** @var \Drupal\Core\Field\BaseFieldDefinition $field_definition */
    $field_definition = $this->nodeDefinition->getPropertyDefinition('title');
    /** @var \Drupal\Core\Field\TypedData\FieldItemDataDefinition $item_definition */
    $item_definition = $field_definition->getItemDefinition();
    /** @var \Drupal\Core\TypedData\DataDefinition $target_definition */
    $target_definition = $item_definition->getPropertyDefinition('value');

    // This should trigger an exception.
    $this->dataFetcher->fetchDefinitionByPropertyPath(
      $target_definition,
      'unknown_property'
    );
  }

  /**
   * @covers ::fetchDefinitionByPropertyPath
   */
  public function testFetchingAtInvalidPosition(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage("The data selector 'unknown_property' cannot be applied because the definition of type 'integer' is not a list or a complex structure");
    $list_definition = $this->typedDataManager->createListDataDefinition('integer');

    // This should trigger an exception.
    $this->dataFetcher->fetchDefinitionByPropertyPath(
      $list_definition,
      'unknown_property'
    );
  }

}
