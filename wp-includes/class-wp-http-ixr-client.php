<?php
/**
 * WP_HTTP_IXR_Client
 *
 * @package WordPress
 * @since 3.1.0
 *
 */
class WP_HTTP_IXR_Client extends IXR_Client {
	public $scheme;
	/**
	 * @var IXR_Error
	 */
	public $error;

	/**
	 * @param string $server
	 * @param string|bool $path
	 * @param int|bool $port
	 * @param int $timeout
	 */
	public function __construct($server, $path = false, $port = false, $timeout = 15) {
		if ( ! $path ) {
			// Assume we have been given a URL instead
			$bits = parse_url($server);
			$this->scheme = $bits['scheme'];
			$this->server = $bits['host'];
			$this->port = isset($bits['port']) ? $bits['port'] : $port;
			$this->path = !empty($bits['path']) ? $bits['path'] : '/';

			// Make absolutely sure we have a path
			if ( ! $this->path ) {
				$this->path = '/';
			}

			if ( ! empty( $bits['query'] ) ) {
				$this->path .= '?' . $bits['query'];
			}
		} else {
			$this->scheme = 'http';
			$this->server = $server;
			$this->path = $path;
			$this->port = $port;
		}
		$this->useragent = 'The Incutio XML-RPC PHP Library';
		$this->timeout = $timeout;
	}

	/**
	 * @return bool
	 */
	public function query() {
		$args = func_get_args();
		$method = array_shift($args);
		$request = new IXR_Request($method, $args);
		$xml = $request->getXml();

		$port = $this->port ? ":$this->port" : '';
		$url = $this->scheme . '://' . $this->server . $port . $this->path;
		$args = array(
			'headers'    => array('Content-Type' => 'text/xml'),
			'user-agent' => $this->useragent,
			'body'       => $xml,
		);

		// Merge Custom headers ala #8145
		foreach ( $this->headers as $header => $value ) {
			$args['headers'][$header] = $value;
		}

		/**
		 * Filter to allow custom headers to be sent
		 *
		 * @since 4.4.0
		 *
		 * @param array $headers Header collection to be sent to the XML-RPC server.
		 */
		$args['headers'] = apply_filters( 'wp_http_ixr_client_headers', $args['headers'] );

		if ( $this->timeout !== false ) {
			$args['timeout'] = $this->timeout;
		}

		// Now send the request
		if ( $this->debug ) {
			echo '<pre class="ixr_request">' . htmlspecialchars($xml) . "\n</pre>\n\n";
		}

		$response = wp_remote_post($url, $args);

		if ( is_wp_error($response) ) {
			$errno    = $response->get_error_code();
			$errorstr = $response->get_error_message();
			$this->error = new IXR_Error(-32300, "transport error: $errno $errorstr");
			return false;
		}

		if ( 200 != wp_remote_retrieve_response_code( $response ) ) {
			$this->error = new IXR_Error(-32301, 'transport error - HTTP status code was not 200 (' . wp_remote_retrieve_response_code( $response ) . ')');
			return false;
		}

		if ( $this->debug ) {
			echo '<pre class="ixr_response">' . htmlspecialchars( wp_remote_retrieve_body( $response ) ) . "\n</pre>\n\n";
		}

		// Now parse what we've got back
		$this->message = new IXR_Message( wp_remote_retrieve_body( $response ) );
		if ( ! $this->message->parse() ) {
			// XML error
			$this->error = new IXR_Error(-32700, 'parse error. not well formed');
			return false;
		}

		// Is the message a fault?
		if ( $this->message->messageType == 'fault' ) {
			$this->error = new IXR_Error($this->message->faultCode, $this->message->faultString);
			return false;
		}

		// Message must be OK
		return true;
	}
}
