<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Mijireh_RestJSON extends Mijireh_Rest {

  public function post($url, $data, $headers=array()) {
    return parent::post($url, json_encode($data), $headers);
  }

  public function put($url, $data, $headers=array()) {
    return parent::put($url, json_encode($data), $headers);
  }

  protected function prepRequest($opts, $url) {
    $opts[CURLOPT_HTTPHEADER][] = 'Accept: application/json';
    $opts[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
    return parent::prepRequest($opts, $url);
  }

  public function processBody($body) {
    return json_decode($body, true);
  }

}