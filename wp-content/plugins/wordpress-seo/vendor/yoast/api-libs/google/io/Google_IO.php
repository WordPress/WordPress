<?php
/**
 * Copyright 2010 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Abstract IO class
 *
 * @author Chris Chabot <chabotc@google.com>
 */
abstract class Yoast_Google_IO {
  const CONNECTION_ESTABLISHED = "HTTP/1.0 200 Connection established\r\n\r\n";
  const FORM_URLENCODED = 'application/x-www-form-urlencoded';
  /**
   * An utility function that first calls $this->auth->sign($request) and then executes makeRequest()
   * on that signed request. Used for when a request should be authenticated
   * @param Yoast_Google_HttpRequest $request
   * @return Yoast_Google_HttpRequest $request
   */
  abstract function authenticatedRequest(Yoast_Google_HttpRequest $request);

  /**
   * Executes a apIHttpRequest and returns the resulting populated httpRequest
   * @param Yoast_Google_HttpRequest $request
   * @return Yoast_Google_HttpRequest $request
   */
  abstract function makeRequest(Yoast_Google_HttpRequest $request);

  /**
   * Set options that update the transport implementation's behavior.
   * @param $options
   */
  abstract function setOptions($options);

  /**
   * @visible for testing.
   * Cache the response to an HTTP request if it is cacheable.
   * @param Yoast_Google_HttpRequest $request
   * @return bool Returns true if the insertion was successful.
   * Otherwise, return false.
   */
  protected function setCachedRequest(Yoast_Google_HttpRequest $request) {
    // Determine if the request is cacheable.
    if (Yoast_Google_CacheParser::isResponseCacheable($request)) {
      Yoast_Google_Client::$cache->set($request->getCacheKey(), $request);
      return true;
    }

    return false;
  }

  /**
   * @visible for testing.
   * @param Yoast_Google_HttpRequest $request
   * @return Yoast_Google_HttpRequest|bool Returns the cached object or
   * false if the operation was unsuccessful.
   */
  protected function getCachedRequest(Yoast_Google_HttpRequest $request) {
    if (false == Yoast_Google_CacheParser::isRequestCacheable($request)) {
      false;
    }

    return Yoast_Google_Client::$cache->get($request->getCacheKey());
  }

  /**
   * @visible for testing
   * Process an http request that contains an enclosed entity.
   * @param Yoast_Google_HttpRequest $request
   * @return Yoast_Google_HttpRequest Processed request with the enclosed entity.
   */
  protected function processEntityRequest(Yoast_Google_HttpRequest $request) {
    $postBody = $request->getPostBody();
    $contentType = $request->getRequestHeader("content-type");

    // Set the default content-type as application/x-www-form-urlencoded.
    if (false == $contentType) {
      $contentType = self::FORM_URLENCODED;
      $request->setRequestHeaders(array('content-type' => $contentType));
    }

    // Force the payload to match the content-type asserted in the header.
    if ($contentType == self::FORM_URLENCODED && is_array($postBody)) {
      $postBody = http_build_query($postBody, '', '&');
      $request->setPostBody($postBody);
    }

    // Make sure the content-length header is set.
    if (!$postBody || is_string($postBody)) {
      $postsLength = strlen($postBody);
      $request->setRequestHeaders(array('content-length' => $postsLength));
    }

    return $request;
  }

  /**
   * Check if an already cached request must be revalidated, and if so update
   * the request with the correct ETag headers.
   * @param Yoast_Google_HttpRequest $cached A previously cached response.
   * @param Yoast_Google_HttpRequest $request The outbound request.
   * return bool If the cached object needs to be revalidated, false if it is
   * still current and can be re-used.
   */
  protected function checkMustRevaliadateCachedRequest($cached, $request) {
    if (Yoast_Google_CacheParser::mustRevalidate($cached)) {
      $addHeaders = array();
      if ($cached->getResponseHeader('etag')) {
        // [13.3.4] If an entity tag has been provided by the origin server,
        // we must use that entity tag in any cache-conditional request.
        $addHeaders['If-None-Match'] = $cached->getResponseHeader('etag');
      } elseif ($cached->getResponseHeader('date')) {
        $addHeaders['If-Modified-Since'] = $cached->getResponseHeader('date');
      }

      $request->setRequestHeaders($addHeaders);
      return true;
    } else {
      return false;
    }
  }

  /**
   * Update a cached request, using the headers from the last response.
   * @param Yoast_Google_HttpRequest $cached A previously cached response.
   * @param mixed Associative array of response headers from the last request.
   */
  protected function updateCachedRequest($cached, $responseHeaders) {
    if (isset($responseHeaders['connection'])) {
      $hopByHop = array_merge(
        self::$HOP_BY_HOP,
        explode(',', $responseHeaders['connection'])
      );

      $endToEnd = array();
      foreach($hopByHop as $key) {
        if (isset($responseHeaders[$key])) {
          $endToEnd[$key] = $responseHeaders[$key];
        }
      }
      $cached->setResponseHeaders($endToEnd);
    }
  }
}
