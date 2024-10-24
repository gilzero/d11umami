<?php

declare(strict_types=1);

namespace Drupal\Tests\vdb_provider_milvus\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\vdb_provider_milvus\MilvusV2;
use GuzzleHttp\Client;

/**
 * @coversDefaultClass \Drupal\vdb_provider_milvus\MilvusV2
 * @group ai
 */
class MilvusV2Test extends UnitTestCase {

  /**
   * Tests the isZilliz method.
   *
   * @param mixed $base_url
   *   The base URL to test.
   * @param bool $expected
   *   The value returned from isZilliz method.
   *
   * @dataProvider baseUrls
   *
   * @return void
   *   Nothing.
   */
  public function testIsZilliz(string $base_url, bool $expected): void {
    /** @var \GuzzleHttp\Client */
    $client = $this->createMock(Client::class);
    $milvusv2 = new MilvusV2($client);
    $milvusv2->setBaseUrl($base_url);
    $this->assertSame($expected, $milvusv2->isZilliz());
  }

  /**
   * Provides base urls and values for baseURL testing.
   *
   * @return array
   *   Types, values and expected values.
   */
  public static function baseUrls(): array {
    return [
      ["https://ab12-3456789.serverless.gcp-us-west1.cloud.zilliz.com", TRUE],
      ["https://ab12-3456789.serverless.gcp-us-west1.zillizcloud.com", TRUE],
      ["http://milvus.ddev.site", FALSE],
      ["http://192.168.1.10", FALSE],
      ["https://mymilvus.com", FALSE],
      ["https://milvus-saas.com", FALSE],
      ["http://milvus", FALSE],
      ["https://cloud.milvus.com", FALSE],
    ];
  }

}
