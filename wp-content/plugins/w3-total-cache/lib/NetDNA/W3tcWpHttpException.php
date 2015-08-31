<?php

class W3tcWpHttpException extends Exception {
  
  private $curl_headers;

  public function __construct($message, $code = 0, Exception $previous = null, $headers = null) {
      if (version_compare(PHP_VERSION, '5.3', '>='))
          parent::__construct($message, (int)$code, $previous);
      else
          parent::__construct($message, (int)$code);
      $this->curl_headers = $headers;
  }

  public function getHeaders() {
    return $this->curl_headers;
  }

}

