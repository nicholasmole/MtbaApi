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
   * Display template for mtba_index view
   * 
   * @return array display twig theme
   */
  public function content() {

    $request = new JsonApiRequest('https://api-v3.mbta.com/routes?filter[type]=0,1,2');

    $data = json_decode($request->getResult(), true);

    usort($data[data], array($this, $this->sort_by_attributes_type));

    return array(
      '#theme' => 'mtba_index',
      '#route_data' => $data[data]
    );
  }


}