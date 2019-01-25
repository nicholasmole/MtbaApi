<?php
namespace Drupal\MtbaApi\Controller;

require __DIR__.'/../../../../../../vendor/autoload.php';

use \NicholasMole\JsonApiRequest\JsonApiRequest;

/**
 * @file
 * 
 * @name: MtbaApiController.php
 * 
 * Display list view for available train routes
 * 
 * Contains \Drupal\hello_world\Controller\HelloController.
 * Contains \NicholasMole\JsonApiRequest\JsonApiRequest.
 */
class MtbaApiController {

  /**
   * @param object $request - Api Request Response data
   */
  private $request;
  
  /**
   * Sort array by attributes type
   *
   * @param array $a - first array to reorder
   * @param array $b - seconde array to reorder
   * @return array reordered array.
   */
  public static function sort_by_attributes_type($a, $b): array
  {
    return strcmp($a->attributes->type, $b->attributes->type);
  }

  /**
   * Set the MtbaApiController->request param to the json api request data
   */
  private function setRequest() {
    $this->request  = new JsonApiRequest('https://api-v3.mbta.com/routes?filter[type]=0,1,2');
  }

  /**
   * Decode the api request
   * 
   * @param object $request - Api Request Response data
   * @return array Json on api data
   */
  private function getDecodedResponse($request): array {

    return json_decode($request->getResult(), true);

  }

  private function getData(): array {

    // Set Request Data
    $this->setRequest();

    // Decode Request
    $data =  $this->getDecodedResponse($this->request);

    // Sort Request Data
    usort($data[data], array($this, $this->sort_by_attributes_type));

    // Return the data
    return $data[data];

  }

  /**
   * Display template for mtba_index view
   * 
   * @return array display twig theme
   */
  public function content() {

    return array(
      '#theme' => 'mtba_index',
      '#route_data' => $this->getData()
    );

  }


}