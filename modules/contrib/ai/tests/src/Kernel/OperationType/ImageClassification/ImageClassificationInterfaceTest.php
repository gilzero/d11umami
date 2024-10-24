<?php

declare(strict_types=1);

namespace Drupal\Tests\ai\Kernel\OperationType\ImageClassification;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\media\Traits\MediaTypeCreationTrait;
use Drupal\ai\Exception\AiBadRequestException;
use Drupal\ai\OperationType\GenericType\ImageFile;
use Drupal\ai\OperationType\ImageClassification\ImageClassificationInput;
use Drupal\ai\OperationType\ImageClassification\ImageClassificationItem;
use Drupal\ai\OperationType\ImageClassification\ImageClassificationOutput;

/**
 * This tests the Image classification calling.
 *
 * @coversDefaultClass \Drupal\ai\OperationType\ImageClassification\ImageClassificationInterface
 *
 * @group ai
 */
class ImageClassificationInterfaceTest extends KernelTestBase {

  use MediaTypeCreationTrait;

  /**
   * Model for the setup.
   *
   * @var string
   */
  protected $model;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'ai',
    'provider_huggingface',
    'key',
    'file',
    'system',
  ];

  /**
   * Setup the test.
   */
  protected function setUp(): void {
    parent::setUp();

    // Create an Huggingface mockup key.
    /** @var \Drupal\key\Entity\Key */
    $key = \Drupal::entityTypeManager()
      ->getStorage('key')
      ->create([
        'id' => 'mockup_huggingface',
        'label' => 'Mockup Huggingface',
        'key_provider' => 'config',
      ]);
    $key->setKeyValue('abc123');
    $key->save();

    // DDEV or local.
    $host = getenv('DDEV_PROJECT') ? 'http://mockoon:3010/huggingface' : 'http://localhost:3010/huggingface';
    $this->model = 'nsfw_image_detection';
    // Setup Huggingface as the provider.
    \Drupal::configFactory()
      ->getEditable('provider_huggingface.settings')
      ->set('api_key', 'mockup_huggingface')
      ->save();

    \Drupal::configFactory()
      ->getEditable('ai_models.settings')
      ->set('models', [
        'huggingface' => [
          'image_classification' => [
            $this->model => [
              'model_id' => $this->model,
              'label' => 'NSFW Image Detection',
              'huggingface_endpoint' => $host . '/Falconsai/nsfw_image_detection',
              'operation_type' => 'image_classification',
              'provider' => 'huggingface',
            ],
          ],
        ],
      ])
      ->save();
  }

  /**
   * Test the image classification.
   */
  public function testImageClassificationNormal(): void {
    $binary = 'testsetestt';
    $provider = \Drupal::service('ai.provider')->createInstance('huggingface');
    $input = new ImageClassificationInput(new ImageFile($binary, 'image/jpeg', 'test.jpg'));
    $classification = $provider->imageClassification($input, $this->model);
    // Should be a ImageClassificationOutput object.
    $this->assertInstanceOf(ImageClassificationOutput::class, $classification);

    $normalized = $classification->getNormalized();
    // Normalized output should be an array.
    $this->assertIsArray($normalized);
    // The array should have 2 elements.
    $this->assertCount(2, $normalized);
    // The first object should be an ImageClassificationItem object.
    $this->assertInstanceOf(ImageClassificationItem::class, $normalized[0]);
    // The first object label should be normal.
    $this->assertEquals('normal', $normalized[0]->getLabel());
    // The first object confidence should be 0.99988055229187.
    $this->assertEquals(0.99988055229187, $normalized[0]->getConfidenceScore());
    // The second object should be an ImageClassificationItem object.
    $this->assertInstanceOf(ImageClassificationItem::class, $normalized[1]);
    // The second object label should be nsfw.
    $this->assertEquals('nsfw', $normalized[1]->getLabel());
    // The second object confidence should be 0.00011939688556595.
    $this->assertEquals(0.00011939688556595, $normalized[1]->getConfidenceScore());
  }

  /**
   * Test the image classification without a binary.
   */
  public function testImageClassificationBroken(): void {
    $provider = \Drupal::service('ai.provider')->createInstance('huggingface');
    $input = new ImageClassificationInput(new ImageFile('', 'image/jpeg', 'test.jpg'));
    $this->expectException(AiBadRequestException::class);
    $provider->imageClassification($input, $this->model);
  }

  /**
   * Test the image classification with a wrong api key.
   */
  public function testImageClassificationWrongKey(): void {
    $binary = 'testsetestt';
    $provider = \Drupal::service('ai.provider')->createInstance('huggingface');
    $input = new ImageClassificationInput(new ImageFile($binary, 'image/jpeg', 'test.jpg'));
    $provider->setAuthentication('faulty_key');
    $this->expectException(AiBadRequestException::class);
    $provider->imageClassification($input, $this->model);
  }

}
