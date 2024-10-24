<?php

namespace Drupal\provider_huggingface;

use GuzzleHttp\Client;

/**
 * Basic Huggingface API.
 */
class HuggingfaceApi {

  /**
   * The http client.
   */
  protected Client $client;

  /**
   * API Token.
   */
  private string $apiToken;

  /**
   * The serverless base path.
   */
  private string $serverless = 'https://api-inference.huggingface.co/models/';

  /**
   * Constructs a new Huggingface object.
   *
   * @param \GuzzleHttp\Client $client
   *   Http client.
   */
  public function __construct(Client $client) {
    $this->client = $client;
  }

  /**
   * Set the API token.
   *
   * @param string $apiToken
   *   The API token.
   */
  public function setApiToken($apiToken) {
    $this->apiToken = $apiToken;
  }

  /**
   * Checks if the api is set.
   *
   * @return bool
   *   If the api is set.
   */
  public function isApiSet() {
    return !empty($this->apiToken);
  }

  /**
   * Makes a Text-To-Image call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $prompt
   *   The prompt to send.
   * @param bool $useCache
   *   Use cache to speed up inference requests.
   * @param bool $waitForModel
   *   Wait for model instead of 503.
   *
   * @return string|null
   *   The image binary or nothing.
   */
  public function textToImage($endpoint, $prompt, $useCache = TRUE, $waitForModel = FALSE) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    $response = $this->makeRequest($apiEndPoint, [
      'inputs' => $prompt,
      'use_cache' => $useCache,
      'wait_for_model' => $waitForModel,
    ]);
    return $response;
  }

  /**
   * Makes an Fill Mask task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $prompt
   *   The prompt to send.
   * @param bool $useCache
   *   Use cache to speed up inference requests.
   * @param bool $waitForModel
   *   Wait for model instead of 503.
   *
   * @return string
   *   The return response undecoded.
   */
  public function fillMask($endpoint, $prompt, $useCache = TRUE, $waitForModel = FALSE) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, [
      'inputs' => $prompt,
      'use_cache' => $useCache,
      'wait_for_model' => $waitForModel,
    ]);
  }

  /**
   * Makes an Feature Extraction task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $prompt
   *   The prompt to send.
   * @param bool $useCache
   *   Use cache to speed up inference requests.
   * @param bool $waitForModel
   *   Wait for model instead of 503.
   *
   * @return string
   *   The return response undecoded.
   */
  public function featureExtraction($endpoint, $prompt, $useCache = TRUE, $waitForModel = FALSE) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, [
      'inputs' => $prompt,
      'use_cache' => $useCache,
      'wait_for_model' => $waitForModel,
    ]);
  }

  /**
   * Makes an Summarization task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $prompt
   *   The prompt to send.
   * @param array $parameters
   *   The parameters to send.
   * @param bool $useCache
   *   Use cache to speed up inference requests.
   * @param bool $waitForModel
   *   Wait for model instead of 503.
   *
   * @return string
   *   The return response undecoded.
   */
  public function summarization($endpoint, $prompt, array $parameters = [], $useCache = TRUE, $waitForModel = FALSE) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, [
      'inputs' => $prompt,
      'parameters' => $parameters,
      'use_cache' => $useCache,
      'wait_for_model' => $waitForModel,
    ]);
  }

  /**
   * Makes an Question Answering task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $question
   *   The question to send.
   * @param string $context
   *   The context to ask about.
   *
   * @return string
   *   The return response undecoded.
   */
  public function questionAnswering($endpoint, $question, $context) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, [
      'inputs' => [
        'question' => $question,
        'context' => $context,
      ],
    ]);
  }

  /**
   * Makes a Table Question Answering task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $question
   *   The question to send.
   * @param array $table
   *   The table in an associative array.
   * @param bool $useCache
   *   Use cache to speed up inference requests.
   * @param bool $waitForModel
   *   Wait for model instead of 503.
   *
   * @return string
   *   The return response undecoded.
   */
  public function tableQuestionAnswering($endpoint, $question, array $table, $useCache = TRUE, $waitForModel = FALSE) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, [
      'inputs' => [
        'question' => $question,
        'table' => $table,
      ],
      'use_cache' => $useCache,
      'wait_for_model' => $waitForModel,
    ]);
  }

  /**
   * Makes an Sentence Similarity task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $sourceSentence
   *   The source sentence.
   * @param array $sentences
   *   An array of sentences to compare with.
   * @param bool $useCache
   *   Use cache to speed up inference requests.
   * @param bool $waitForModel
   *   Wait for model instead of 503.
   *
   * @return string
   *   The return response undecoded.
   */
  public function sentenceSimilarity($endpoint, $sourceSentence, array $sentences, $useCache = TRUE, $waitForModel = FALSE) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, [
      'inputs' => [
        'source_sentence' => $sourceSentence,
        'sentences' => $sentences,
      ],
      'use_cache' => $useCache,
      'wait_for_model' => $waitForModel,
    ]);
  }

  /**
   * Makes an Text Classification task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $text
   *   The text to classify.
   * @param array $parameters
   *   The parameters to send.
   * @param bool $useCache
   *   Use cache to speed up inference requests.
   * @param bool $waitForModel
   *   Wait for model instead of 503.
   *
   * @return string
   *   The return response undecoded.
   */
  public function textClassification($endpoint, $text, array $parameters = [], $useCache = TRUE, $waitForModel = FALSE) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, [
      'inputs' => $text,
      'parameters' => $parameters,
      'use_cache' => $useCache,
      'wait_for_model' => $waitForModel,
    ]);
  }

  /**
   * Makes an Text Generation task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $prompt
   *   The prompt to send.
   * @param array $parameters
   *   The parameters to send.
   * @param bool $useCache
   *   Use cache to speed up inference requests.
   * @param bool $waitForModel
   *   Wait for model instead of 503.
   *
   * @return string
   *   The return response undecoded.
   */
  public function textGeneration($endpoint, $prompt, array $parameters = [], $useCache = TRUE, $waitForModel = FALSE) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, [
      'inputs' => $prompt,
      'parameters' => $parameters,
      'use_cache' => $useCache,
      'wait_for_model' => $waitForModel,
    ]);
  }

  /**
   * Makes an Token Classification task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $text
   *   The text to classify.
   * @param array $parameters
   *   The parameters to send.
   * @param bool $useCache
   *   Use cache to speed up inference requests.
   * @param bool $waitForModel
   *   Wait for model instead of 503.
   *
   * @return string
   *   The return response undecoded.
   */
  public function tokenClassification($endpoint, $text, array $parameters = [], $useCache = TRUE, $waitForModel = FALSE) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, [
      'inputs' => $text,
      'parameters' => $parameters,
      'use_cache' => $useCache,
      'wait_for_model' => $waitForModel,
    ]);
  }

  /**
   * Makes an Translation task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $source
   *   The text to translate.
   * @param bool $useCache
   *   Use cache to speed up inference requests.
   * @param bool $waitForModel
   *   Wait for model instead of 503.
   *
   * @return string
   *   The return response undecoded.
   */
  public function translation($endpoint, $source, $useCache = TRUE, $waitForModel = FALSE) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, [
      'inputs' => $source,
      'use_cache' => $useCache,
      'wait_for_model' => $waitForModel,
    ]);
  }

  /**
   * Makes an Zero Shot Classification task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $source
   *   The text to classify.
   * @param array $parameters
   *   The parameters to send.
   * @param bool $useCache
   *   Use cache to speed up inference requests.
   * @param bool $waitForModel
   *   Wait for model instead of 503.
   *
   * @return string
   *   The return response undecoded.
   */
  public function zeroShotClassification($endpoint, $source, array $parameters = [], $useCache = TRUE, $waitForModel = FALSE) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, [
      'inputs' => $source,
      'parameters' => $parameters,
      'use_cache' => $useCache,
      'wait_for_model' => $waitForModel,
    ]);
  }

  /**
   * Makes an Conversational task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $input
   *   The text to classify.
   * @param array $parameters
   *   The parameters to send.
   * @param bool $useCache
   *   Use cache to speed up inference requests.
   * @param bool $waitForModel
   *   Wait for model instead of 503.
   *
   * @return string
   *   The return response undecoded.
   */
  public function conversational($endpoint, $input, array $parameters = [], $useCache = TRUE, $waitForModel = FALSE) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, [
      'inputs' => $input,
      'parameters' => $parameters,
      'use_cache' => $useCache,
      'wait_for_model' => $waitForModel,
    ]);
  }

  /**
   * Makes an Automatic Speech Recogniztion task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $filePath
   *   The audio file path to classify.
   *
   * @return string
   *   The return response undecoded.
   */
  public function automaticSpeechRecognition($endpoint, $filePath) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, NULL, $filePath);
  }

  /**
   * Makes an Audio Classification task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $filePath
   *   The audio file path to classify.
   *
   * @return string
   *   The return response undecoded.
   */
  public function audioClassification($endpoint, $filePath) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, NULL, $filePath);
  }

  /**
   * Makes an Image Classification task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $filePath
   *   The image file path to look at.
   *
   * @return string
   *   The return response undecoded.
   */
  public function imageClassification($endpoint, $filePath) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, NULL, $filePath);
  }

  /**
   * Makes a Object Detection task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $filePath
   *   The image file path to look at.
   *
   * @return string
   *   The return response undecoded.
   */
  public function objectDetection($endpoint, $filePath) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, NULL, $filePath);
  }

  /**
   * Makes a Image Segmentation task call.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   * @param string $filePath
   *   The image file path to look at.
   *
   * @return string
   *   The return response undecoded.
   */
  public function imageSegmentation($endpoint, $filePath) {
    $apiEndPoint = $this->finalEndpoint($endpoint);
    return $this->makeRequest($apiEndPoint, NULL, $filePath);
  }

  /**
   * Check if the model exists.
   *
   * @param string $namespace
   *   The model namespace.
   *
   * @return string
   *   The return response undecoded.
   */
  public function getModel($namespace) {
    $endpoint = 'https://huggingface.co/api/models/' . $namespace;
    return $this->makeRequest($endpoint, NULL, NULL, 'GET');
  }

  /**
   * Get user data.
   *
   * @return string
   *   The return response undecoded.
   */
  public function getUserData() {
    $endpoint = 'https://huggingface.co/api/whoami-v2';
    return $this->makeRequest($endpoint, NULL, NULL, 'GET');
  }

  /**
   * Is endpoint a serverless endpoint or a dedicated url.
   *
   * @param string $endpoint
   *   The endpoint url or model name.
   *
   * @return string
   *   The final endpoint.
   */
  protected function finalEndpoint($endpoint) {
    // If it has a protocol, it's a dedicated url.
    if (strpos($endpoint, 'https://') === 0 || strpos($endpoint, 'http://') === 0) {
      return $endpoint;
    }
    // Otherwise, it's a serverless endpoint.
    return $this->serverless . $endpoint;
  }

  /**
   * Make Huggingface call.
   *
   * @param string $apiEndPoint
   *   The api endpoint.
   * @param string $json
   *   JSON params.
   * @param string $file
   *   A (real) filepath.
   * @param string $method
   *   The http method.
   *
   * @return string|object
   *   The return response.
   */
  protected function makeRequest($apiEndPoint, $json = NULL, $file = NULL, $method = 'POST') {
    if (empty($this->apiToken)) {
      throw new \Exception('No Huggingface API token found.');
    }

    // We can wait some.
    $options['connect_timeout'] = 120;
    $options['read_timeout'] = 120;
    // Set authorization header.
    $options['headers']['Authorization'] = 'Bearer ' . $this->apiToken;

    if ($json) {
      $options['body'] = json_encode($json);
      $options['headers']['Content-Type'] = 'application/json';
    }

    if ($file) {
      $options['body'] = fopen($file, 'r');
    }

    $res = $this->client->request($method, $apiEndPoint, $options);
    return $res->getBody();
  }

}
