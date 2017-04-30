<?php

/**
 * Mijireh_Rest is a REST client for PHP.
 *
 * This code is licensed for use, modification, and distribution
 * under the terms of the MIT License (see http://en.wikipedia.org/wiki/MIT_License)
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Mijireh_Rest {

  public $curl_opts = array(
  	CURLOPT_RETURNTRANSFER => true,  // return result instead of echoing
  	CURLOPT_SSL_VERIFYPEER => false, // stop cURL from verifying the peer's certificate
  	CURLOPT_MAXREDIRS      => 10     // but don't redirect more than 10 times
  );

  public $base_url;

  public $last_response;
  public $last_request;

  public $throw_exceptions = true;

  public function __construct($base_url, $curl_options=null) {
    if (!function_exists('curl_init')) {
  	    throw new Exception('CURL module not available! Mijireh_Rest requires CURL. See http://php.net/manual/en/book.curl.php');
  	}

    if(isset($curl_options) && is_array($curl_options)) {
      foreach($curl_options as $key => $value) {

        if($key == 'CURLOPT_FOLLOWLOCATION') {
          // only enable CURLOPT_FOLLOWLOCATION if safe_mode and open_base_dir are not in use
        	if(ini_get('open_basedir') == '' && !ini_get('safe_mode')) {
        	  $this->curl_opts['CURLOPT_FOLLOWLOCATION'] = true;
        	}
        }
        else {
          $this->curl_opts[$key] = $value;
        }

      }
    }


    $this->base_url = $base_url;
  }

  // $auth can be 'basic' or 'digest'
  public function setupAuth($user, $pass, $auth = 'basic') {
    $this->curl_opts[CURLOPT_HTTPAUTH] = constant('CURLAUTH_'.strtoupper($auth));
    $this->curl_opts[CURLOPT_USERPWD] = $user . ":" . $pass;
  }

  public function get($url) {
    $curl = $this->prepRequest($this->curl_opts, $url);
    $body = $this->doRequest($curl);

    $body = $this->processBody($body);

    return $body;
  }

  public function post($url, $data, $headers=array()) {
    $data = (is_array($data)) ? http_build_query($data) : $data;

    $curl_opts = $this->curl_opts;
    $curl_opts[CURLOPT_CUSTOMREQUEST] = 'POST';
    $headers[] = 'Content-Length: '.strlen($data);
    $curl_opts[CURLOPT_HTTPHEADER] = $headers;
    $curl_opts[CURLOPT_POSTFIELDS] = $data;

    $curl = $this->prepRequest($curl_opts, $url);
    $body = $this->doRequest($curl);

    $body = $this->processBody($body);

    return $body;
  }

  public function put($url, $data, $headers=array()) {
    $data = (is_array($data)) ? http_build_query($data) : $data;

    $curl_opts = $this->curl_opts;
    $curl_opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
    $headers[] = 'Content-Length: '.strlen($data);
    $curl_opts[CURLOPT_HTTPHEADER] = $headers;
    $curl_opts[CURLOPT_POSTFIELDS] = $data;

    $curl = $this->prepRequest($curl_opts, $url);
    $body = $this->doRequest($curl);

    $body = $this->processBody($body);

    return $body;
  }

  public function delete($url) {
    $curl_opts = $this->curl_opts;
    $curl_opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';

    $curl = $this->prepRequest($curl_opts, $url);
    $body = $this->doRequest($curl);

    $body = $this->processBody($body);

    return $body;
  }

  public function lastBody() {
    return $this->last_response['body'];
  }

  public function lastStatus() {
    return $this->last_response['meta']['http_code'];
  }

  protected function processBody($body) {
    // Override this in classes that extend Mijireh_Rest.
    // The body of every GET/POST/PUT/DELETE response goes through
    // here prior to being returned.
    return $body;
  }

  protected function processError($body) {
    // Override this in classes that extend Mijireh_Rest.
    // The body of every erroneous (non-2xx/3xx) GET/POST/PUT/DELETE
    // response goes through here prior to being used as the 'message'
    // of the resulting Mijireh_Rest_Exception
    return $body;
  }


  protected function prepRequest($opts, $url) {
    if (strncmp($url, $this->base_url, strlen($this->base_url)) != 0) {
      $url = $this->base_url . $url;
    }
    $curl = curl_init($url);

    foreach ($opts as $opt => $val) {
      @curl_setopt($curl, $opt, $val);
    }

    $this->last_request = array(
      'url' => $url
    );

    if (isset($opts[CURLOPT_CUSTOMREQUEST]))
      $this->last_request['method'] = $opts[CURLOPT_CUSTOMREQUEST];
    else
      $this->last_request['method'] = 'GET';

    if (isset($opts[CURLOPT_POSTFIELDS]))
      $this->last_request['data'] = $opts[CURLOPT_POSTFIELDS];

    return $curl;
  }

  private function doRequest($curl) {

    $body = curl_exec($curl);
    $meta = curl_getinfo($curl);

    $this->last_response = array(
      'body' => $body,
      'meta' => $meta
    );

    curl_close($curl);

    $this->checkLastResponseForError();

    return $body;
  }

  protected function checkLastResponseForError() {
    if ( !$this->throw_exceptions)
      return;

    $meta = $this->last_response['meta'];
    $body = $this->last_response['body'];

    if (!$meta)
      return;

    $err = null;
    switch ($meta['http_code']) {
      case 400:
        throw new Mijireh_Rest_BadRequest($this->processError($body));
        break;
      case 401:
        throw new Mijireh_Rest_Unauthorized($this->processError($body));
        break;
      case 403:
        throw new Mijireh_Rest_Forbidden($this->processError($body));
        break;
      case 404:
        throw new Mijireh_Rest_NotFound($this->processError($body));
        break;
      case 405:
        throw new Mijireh_Rest_MethodNotAllowed($this->processError($body));
        break;
      case 409:
        throw new Mijireh_Rest_Conflict($this->processError($body));
        break;
      case 410:
        throw new Mijireh_Rest_Gone($this->processError($body));
        break;
      case 422:
        // Unprocessable Entity -- see http://www.iana.org/assignments/http-status-codes
        // This is now commonly used (in Rails, at least) to indicate
        // a response to a request that is syntactically correct,
        // but semantically invalid (for example, when trying to
        // create a resource with some required fields missing)
        throw new Mijireh_Rest_InvalidRecord($this->processError($body));
        break;
      default:
        if ($meta['http_code'] >= 400 && $meta['http_code'] <= 499)
          throw new Mijireh_Rest_ClientError($this->processError($body));
        elseif ($meta['http_code'] >= 500 && $meta['http_code'] <= 599)
          throw new Mijireh_Rest_ServerError($this->processError($body));
        elseif (!$meta['http_code'] || $meta['http_code'] >= 600) {
          throw new Mijireh_Rest_UnknownResponse($this->processError($body));
        }
    }
  }
}


class Mijireh_Rest_Exception extends Exception { }
class Mijireh_Rest_UnknownResponse extends Mijireh_Rest_Exception { }

/* 401-499 */ class Mijireh_Rest_ClientError extends Mijireh_Rest_Exception {}
/* 400 */ class Mijireh_Rest_BadRequest extends Mijireh_Rest_ClientError {}
/* 401 */ class Mijireh_Rest_Unauthorized extends Mijireh_Rest_ClientError {}
/* 403 */ class Mijireh_Rest_Forbidden extends Mijireh_Rest_ClientError {}
/* 404 */ class Mijireh_Rest_NotFound extends Mijireh_Rest_ClientError {}
/* 405 */ class Mijireh_Rest_MethodNotAllowed extends Mijireh_Rest_ClientError {}
/* 409 */ class Mijireh_Rest_Conflict extends Mijireh_Rest_ClientError {}
/* 410 */ class Mijireh_Rest_Gone extends Mijireh_Rest_ClientError {}
/* 422 */ class Mijireh_Rest_InvalidRecord extends Mijireh_Rest_ClientError {}

/* 500-599 */ class Mijireh_Rest_ServerError extends Mijireh_Rest_Exception {}