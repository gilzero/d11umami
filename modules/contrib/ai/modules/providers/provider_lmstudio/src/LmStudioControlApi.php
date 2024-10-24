<?php

namespace Drupal\provider_lmstudio;

use GuzzleHttp\Client;

/**
 * L; Studio Control API.
 */
class LmStudioControlApi {

  /**
   * The http client.
   */
  protected Client $client;

  /**
   * The base host.
   */
  protected string $baseHost;

  /**
   * Constructs a new LM Studio AI object.
   *
   * @param \GuzzleHttp\Client $client
   *   Http client.
   */
  public function __construct(Client $client) {
    $this->client = $client;
  }

  /**
   * Sets connect data.
   *
   * @param string $baseUrl
   *   The base url.
   */
  public function setConnectData($baseUrl) {
    $this->baseHost = $baseUrl;
  }

  /**
   * Get all models in LM Studio.
   *
   * @return array
   *   The response.
   */
  public function getModels() {
    $result = json_decode($this->makeRequest("v1/models", [], 'GET'), TRUE);
    return $result;
  }

  /**
   * Make LM Studio call.
   *
   * @param string $path
   *   The path.
   * @param array $query_string
   *   The query string.
   * @param string $method
   *   The method.
   * @param string $body
   *   Data to attach if POST/PUT/PATCH.
   * @param array $options
   *   Extra headers.
   *
   * @return string|object
   *   The return response.
   */
  protected function makeRequest($path, array $query_string = [], $method = 'GET', $body = '', array $options = []) {
    // Don't wait to long.
    $options['connect_timeout'] = 120;
    $options['read_timeout'] = 120;
    $options['timeout'] = 120;

    // JSON unless its multipart.
    if (empty($options['multipart'])) {
      $options['headers']['Content-Type'] = 'application/json';
    }
    if ($body) {
      $options['body'] = json_encode($body);
    }

    $new_url = rtrim($this->baseHost, '/') . '/' . $path;
    $new_url .= count($query_string) ? '?' . http_build_query($query_string) : '';

    $res = $this->client->request($method, $new_url, $options);

    return $res->getBody();
  }

}
