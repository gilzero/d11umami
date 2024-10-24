<?php

namespace Drupal\provider_ollama;

use GuzzleHttp\Client;

/**
 * Ollama Control API.
 */
class OllamaControlApi {

  /**
   * The http client.
   */
  protected Client $client;

  /**
   * The base host.
   */
  protected string $baseHost;

  /**
   * Constructs a new Ollama AI object.
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
   * Get all models in Ollama.
   *
   * @return array
   *   The response.
   */
  public function getModels() {
    $result = json_decode($this->makeRequest("api/tags", [], 'GET'), TRUE);
    return $result;
  }

  /**
   * Embeddings is not in the OpenAI Client.
   *
   * @param string $text
   *   The text.
   * @param string $model
   *   The model.
   *
   * @return array
   *   The response.
   */
  public function embeddings($text, $model) {
    $result = json_decode($this->makeRequest("api/embeddings", [], 'POST', [
      'prompt' => $text,
      'model' => $model,
    ]), TRUE);
    return $result;
  }

  /**
   * Make Ollama call.
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
