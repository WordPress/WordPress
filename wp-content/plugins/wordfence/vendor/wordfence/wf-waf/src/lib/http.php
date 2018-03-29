<?php

class wfWAFHTTP {

	private $url;
	private $auth;
	private $body;
	private $cookies;
//	private $fileNames;
//	private $files;
	private $headers;
	private $method;
	private $queryString;

	/**
	 * @var wfWAFHTTPTransport
	 */
	private $transport;

	/**
	 * @param string $url
	 * @param wfWAFHTTP $request
	 * @return wfWAFHTTPResponse|bool
	 * @throws wfWAFHTTPTransportException
	 */
	public static function get($url, $request = null) {
		if (!$request) {
			$request = new self();
		}
		$request->setUrl($url);
		$request->setMethod('GET');
		$request->setTransport(wfWAFHTTPTransport::getInstance());
		// $request->setCookies("XDEBUG_SESSION=netbeans-xdebug");
		return $request->send();
	}

	/**
	 * @param string $url
	 * @param array $post
	 * @param wfWAFHTTP $request
	 * @return wfWAFHTTPResponse|bool
	 * @throws wfWAFHTTPTransportException
	 */
	public static function post($url, $post = array(), $request = null) {
		if (!$request) {
			$request = new self();
		}
		$request->setUrl($url);
		$request->setMethod('POST');
		$request->setBody($post);
		$request->setTransport(wfWAFHTTPTransport::getInstance());
		return $request->send();
	}

	/**
	 * @return wfWAFHTTPResponse|bool
	 * @throws wfWAFHTTPTransportException
	 */
	public function send() {
		if (!$this->getTransport()) {
			throw new wfWAFHTTPTransportException('Need to provide a valid HTTP transport before calling ' . __METHOD__);
		}
		return $this->getTransport()->send($this);
	}

	/**
	 * @return mixed
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param mixed $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * @return mixed
	 */
	public function getAuth() {
		return $this->auth;
	}

	/**
	 * @param mixed $auth
	 */
	public function setAuth($auth) {
		$this->auth = $auth;
	}

	/**
	 * @return mixed
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @param mixed $body
	 */
	public function setBody($body) {
		$this->body = $body;
	}

	/**
	 * @return mixed
	 */
	public function getCookies() {
		return $this->cookies;
	}

	/**
	 * @param mixed $cookies
	 */
	public function setCookies($cookies) {
		$this->cookies = $cookies;
	}

	/**
	 * @return mixed
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * @param mixed $headers
	 */
	public function setHeaders($headers) {
		$this->headers = $headers;
	}

	/**
	 * @return mixed
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @param mixed $method
	 */
	public function setMethod($method) {
		$this->method = $method;
	}

	/**
	 * @return mixed
	 */
	public function getQueryString() {
		return $this->queryString;
	}

	/**
	 * @param mixed $queryString
	 */
	public function setQueryString($queryString) {
		$this->queryString = $queryString;
	}

	/**
	 * @return wfWAFHTTPTransport
	 */
	public function getTransport() {
		return $this->transport;
	}

	/**
	 * @param wfWAFHTTPTransport $transport
	 */
	public function setTransport($transport) {
		$this->transport = $transport;
	}
}

class wfWAFHTTPResponse {

	private $body;
	private $headers;
	private $statusCode;

	/**
	 * @return mixed
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @param mixed $body
	 */
	public function setBody($body) {
		$this->body = $body;
	}

	/**
	 * @return mixed
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * @param mixed $headers
	 */
	public function setHeaders($headers) {
		$this->headers = $headers;
	}

	/**
	 * @return mixed
	 */
	public function getStatusCode() {
		return $this->statusCode;
	}

	/**
	 * @param mixed $statusCode
	 */
	public function setStatusCode($statusCode) {
		$this->statusCode = $statusCode;
	}
}

abstract class wfWAFHTTPTransport {

	private static $instance;

	/**
	 * @return mixed
	 */
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = self::getFirstTransport();
		}
		return self::$instance;
	}

	/**
	 * @param mixed $instance
	 */
	public static function setInstance($instance) {
		self::$instance = $instance;
	}

	/**
	 * @return wfWAFHTTPTransport
	 * @throws wfWAFHTTPTransportException
	 */
	public static function getFirstTransport() {
		if (function_exists('curl_init')) {
			return new wfWAFHTTPTransportCurl();
		} else if (function_exists('file_get_contents')) {
			return new wfWAFHTTPTransportStreams();
		}
		throw new wfWAFHTTPTransportException('No valid HTTP transport found.');
	}

	/**
	 * @param array $cookieArray
	 * @return string
	 */
	public static function buildCookieString($cookieArray) {
		$cookies = '';
		foreach ($cookieArray as $cookieName => $value) {
			$cookies .= "$cookieName=" . urlencode($value) . '; ';
		}
		$cookies = rtrim($cookies);
		return $cookies;
	}

	/**
	 * @param wfWAFHTTP $request
	 * @return wfWAFHTTPResponse|bool
	 */
	abstract public function send($request);
}

class wfWAFHTTPTransportCurl extends wfWAFHTTPTransport {

	/**
	 * @todo Proxy settings
	 * @param wfWAFHTTP $request
	 * @return wfWAFHTTPResponse|bool
	 */
	public function send($request) {
		$url = $request->getUrl();
		if ($queryString = $request->getQueryString()) {
			if (is_array($queryString)) {
				$queryString = http_build_query($queryString, null, '&');
			}
			$url .= (wfWAFUtils::strpos($url, '?') !== false ? '&' : '?') . $queryString;
		}

		$ch = curl_init($url);
		switch (wfWAFUtils::strtolower($request->getMethod())) {
			case 'post':
				curl_setopt($ch, CURLOPT_POST, 1);
				break;
		}
		if ($body = $request->getBody()) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		}
		if ($auth = $request->getAuth()) {
			curl_setopt($ch, CURLOPT_USERPWD, $auth['user'] . ':' . $auth['password']);
		}
		if ($cookies = $request->getCookies()) {
			if (is_array($cookies)) {
				$cookies = self::buildCookieString($cookies);
			}
			curl_setopt($ch, CURLOPT_COOKIE, $cookies);
		}
		if ($headers = $request->getHeaders()) {
			if (is_array($headers)) {
				$_headers = array();
				foreach ($headers as $header => $value) {
					$_headers[] = $header . ': ' . $value;
				}
				curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers);
			}
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_CAINFO, WFWAF_PATH . 'cacert.pem'); //On some systems curl uses an outdated root certificate chain file
		$curlResponse = curl_exec($ch);
		
		if ($curlResponse !== false) {
			$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = wfWAFUtils::substr($curlResponse, 0, $headerSize);
			$body = wfWAFUtils::substr($curlResponse, $headerSize);

			$response = new wfWAFHTTPResponse();
			$response->setBody($body);
			$response->setHeaders($header);
			return $response;
		}
		return false;
	}
}

class wfWAFHTTPTransportStreams extends wfWAFHTTPTransport {

	/**
	 * @todo Implement wfWAFHTTPTransportStreams::send.
	 * @param wfWAFHTTP $request
	 * @return mixed
	 * @throws wfWAFHTTPTransportException
	 */
	public function send($request) {
		$timeout = 5;

		$url = $request->getUrl();
		if ($queryString = $request->getQueryString()) {
			if (is_array($queryString)) {
				$queryString = http_build_query($queryString, null, '&');
			}
			$url .= (wfWAFUtils::strpos($url, '?') !== false ? '&' : '?') . $queryString;
		}

		$urlParsed = parse_url($request->getUrl());

		$headers = "Host: $urlParsed[host]\r\n";
		if ($auth = $request->getAuth()) {
			$headers .= 'Authorization: Basic ' . base64_encode($auth['user'] . ':' . $auth['password']) . "\r\n";
		}
		if ($cookies = $request->getCookies()) {
			if (is_array($cookies)) {
				$cookies = self::buildCookieString($cookies);
			}
			$headers .= "Cookie: $cookies\r\n";
		}
		$hasUA = false;
		if ($_headers = $request->getHeaders()) {
			if (is_array($_headers)) {
				foreach ($_headers as $header => $value) {
					if (trim(wfWAFUtils::strtolower($header)) === 'user-agent') {
						$hasUA = true;
					}
					$headers .= $header . ': ' . $value . "\r\n";
				}
			}
		}
		if (!$hasUA) {
			$headers .= "User-Agent: Wordfence Streams UA\r\n";
		}

		$httpOptions = array(
			'method'          => $request->getMethod(),
			'ignore_errors'   => true,
			'timeout'         => $timeout,
			'follow_location' => 1,
			'max_redirects'   => 5,
		);
		if (wfWAFUtils::strlen($request->getBody()) > 0) {
			$httpOptions['content'] = $request->getBody();
			$headers .= 'Content-Length: ' . wfWAFUtils::strlen($httpOptions['content']) . "\r\n";
		}
		$httpOptions['header'] = $headers;

		$options = array(
			wfWAFUtils::strtolower($urlParsed['scheme']) => $httpOptions,
		);

		$context = stream_context_create($options);
		$stream = fopen($request->getUrl(), 'r', false, $context);
		if (!is_resource($stream)) {
			return false;
		}

		$metaData = stream_get_meta_data($stream);

		// Get the HTTP response code
		$httpResponse = array_shift($metaData['wrapper_data']);

		if (preg_match_all('/(\w+\/\d\.\d) (\d{3})/', $httpResponse, $matches) !== false) {
			// $protocol = $matches[1][0];
			$status = (int) $matches[2][0];
		} else {
			// $protocol = null;
			$status = null;
		}

		$responseObj = new wfWAFHTTPResponse();
		$responseObj->setHeaders(join("\r\n", $metaData['wrapper_data']));
		$responseObj->setBody(stream_get_contents($stream));
		$responseObj->setStatusCode($status);

		// Close the stream after use
		fclose($stream);

		return $responseObj;
	}
}

class wfWAFHTTPTransportException extends wfWAFException {
}
