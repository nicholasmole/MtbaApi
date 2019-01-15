<?php

namespace Drupal\MtbaApi\Controller;

require __DIR__.'/../../../../../../vendor/autoload.php';

use \NicholasMole\JsonApiRequest\JsonApiRequest;

/**
 * @file
 * 
 * @name: MtbaApiTrainRoutesController.php
 * 
 * Display schedule for train routes
 * 
 * Contains \Drupal\hello_world\Controller\HelloController.
 * Contains \NicholasMole\JsonApiRequest\JsonApiRequest.
 */
class MtbaApiTrainRoutesController {

  /**
   * Generate Train Schedule table
   *
   * Turns api schedule data into 
   * 
   * @param array $schedule_data - Json API schedule data
   * @param array $included_data - Json API included data
   * 
   * @return array $new_table - new table contains data to construct Routes Schedule page
   */
  private static function generate_schedule_table($schedule_data, $included_data): array
  {

    $new_table = [];

    $collect_names = [];

    for ($i = 0; $i < count($included_data); $i++) {
      $collect_names[$included_data[$i][id]] = $included_data[$i][attributes][name];
    }

    for ($i = 0; $i < count($schedule_data); $i++) {
      $id = $schedule_data[$i][relationships][stop][data][id];

      $arrival_time_unconverted = $schedule_data[$i][attributes][arrival_time];

      $arrival_time = date('h:i', strtotime($arrival_time_unconverted));

      array_push($new_table, [
        "title"=>$collect_names[$id],
        "arrival_time"=>$arrival_time
      ]);
    }
    return $new_table;
  }

  /**
   * Display template for mtba_list view
   * 
   * @param array $trainroutes - train route path in URL
   * 
   * @return array display twig theme
   */
  public function content($trainroutes = NULL) {

    date_default_timezone_set('America/New_York');

    $schedule = new JsonApiRequest("https://api-v3.mbta.com/schedules?page[limit]=12&include=stop&filter[min_time]=".date("H:i")."&filter[route]=$trainroutes");

    $schedule_data = json_decode($schedule->getResult(), true);

    $constructTable = $this->generate_schedule_table($schedule_data[data], $schedule_data[included]);

    return array(
      '#theme' => 'mtba_list',
      '#route_data' => $constructTable,
      '#route_title' => $trainroutes
    );
  }
}