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
   * @param object $request - Api Request Response data
   */
  private $request;

  /**
   * Set the MtbaApiController->request param to the json api request data
   */
  private function setRequest($trainroutes) {

    $this->request = new JsonApiRequest("https://api-v3.mbta.com/schedules?page[limit]=12&include=stop&filter[min_time]=".date("H:i")."&filter[route]=$trainroutes");

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

  /**
   * Set Time Zone for php file
   */
  private function setTimeZone() {

    date_default_timezone_set('America/New_York');

  }

  /**
   * Get Attributes Arrival Time from array data
   * 
   * @param array $data current data array from route data
   */
  private function getAttributesArrivalTime($data) {

    return $data[attributes][arrival_time];

  }

  /**
   * Get Attributes Name from array data
   * 
   * @param array $data current data array from route data
   */
  private function getAttributesName($data) {

    return $data[attributes][name];

  }

  /**
   * Get Relationships Stop Data ID from array data
   * 
   * @param array $data current data array from route data
   */
  private function getRelationshipsStopDataId($data) {

    return $data[relationships][stop][data][id];

  }

  /**
   * Get the hour:minute time stamp from arrival_time
   * 
   * @param string $arrival_time current data arrival time data
   */
  private function getArrivalTimeHourAndMinutes($arrival_time) {

    return date('h:i', strtotime($arrival_time));

  }



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
  private function generate_schedule_table($schedule_data, $included_data): array
  {

    $new_table = [];

    $collect_names = [];

    for ($i = 0; $i < count($included_data); $i++) {

      // Get names from request data
      $collect_names[$included_data[$i][id]] = $this->getAttributesName($included_data[$i]);

    }

    for ($i = 0; $i < count($schedule_data); $i++) {

      // get relationship stop id data
      $id = $this->getRelationshipsStopDataId($schedule_data[$i]);

      // get attributes arrival_time
      $arrival_time_unconverted = $this->getAttributesArrivalTime(schedule_data[$i]);

      // convert arrival time to hour:minute
      $arrival_time = $this->getArrivalTimeHourAndMinutes($arrival_time_unconverted);

      //create new array for twig templating
      array_push($new_table, [
        "title"=>$collect_names[$id],
        "arrival_time"=>$arrival_time
      ]);

    }

    return $new_table;
  }

  /**
   * Contruct the route data into a table to be used by twig
   * 
   * @param array $schedule_data - Json API schedule data
   */
  private function getRouteDatatoConstructScheduleTable($trainroutes) {

    // set time zone for php
    $this->setTimeZone();

    //Set request for class variable
    $this->setRequest($trainroutes);

    // schedule data for table display
    $schedule_data =  $this->getDecodedResponse($this->request);

    // return table for twig templating
    return  $this->generate_schedule_table($schedule_data[data], $schedule_data[included]);

  }

  /**
   * Display template for mtba_list view
   * 
   * @param array $trainroutes - train route path in URL
   * 
   * @return array display twig theme
   */
  public function content($trainroutes = NULL) {

    return array(
      '#theme' => 'mtba_list',
      '#route_data' => $this->getRouteDatatoConstructScheduleTable($trainroutes),
      '#route_title' => $trainroutes 
    );
  }

}