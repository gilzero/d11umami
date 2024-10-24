<?php

namespace Drupal\provider_huggingface\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Routes for autocomplete responses.
 */
class HuggingfaceAutocomplete extends ControllerBase {

  /**
   * Handler for autocomplete request.
   */
  public function models(Request $request) {
    $type = $request->query->get('model_type');
    $search = $request->query->get('q');
    $results = [];

    // Try to get models.
    if (!empty($type) && strlen($search) > 2) {
      $url = "https://huggingface.co/api/models?pipeline_tag=$type&search=$search&sort=likes&direction=-1&limit=5&full=false";
      $response = json_decode(file_get_contents($url), TRUE);
      foreach ($response as $model) {
        $results[] = $model['id'];
      }
    }

    // Response.
    return new JsonResponse($results);
  }

}
