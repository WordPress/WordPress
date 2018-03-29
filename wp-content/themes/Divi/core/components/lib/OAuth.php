<?php
/**
 * ET Core OAuth Library
 *
 * Copyright © 2016-2017 Elegant Themes, Inc.
 *
 * Based on code from the TwitterOAuth Library:
 *   - Copyright © 2009-2016 Abraham Williams
 *   - Copyright © 2007-2009 Andy Smith
 *
 * @license
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial
 * portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
 * OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */


class ET_Core_LIB_OAuthBase {
	/**
	 * Writes a message to the PHP error log.
	 *
	 * @since 1.1.0
	 *
	 * @param mixed $msg
	 */
	public static function write_log( $msg, $level = 'DEBUG', $_this = null ) {
		$name = ( null !== $_this ) ? get_class( $_this ) : 'ET_Core_LIB_OAuthBase';

		if ( ! is_string( $msg ) ) {
			$msg = print_r( $msg, true );
		}

		error_log( "{$name} [{$level}]: $msg" );
	}
}


class ET_Core_LIB_OAuthUtil {

	public static function build_http_query( $params, $return_json=false ) {
		if ( ! $params ) {
			return '';
		}

		if ( $return_json ) {
			// return json string without further processing if needed
			return json_encode( $params );
		}

		// Url encode both the keys and the values
		$keys   = ET_Core_LIB_OAuthUtil::urlencode_rfc3986( array_keys( $params ) );
		$values = ET_Core_LIB_OAuthUtil::urlencode_rfc3986( array_values( $params ) );
		$params = array_combine( $keys, $values );

		// Parameters are sorted by name, using lexicographical byte value ordering.
		// Ref: Spec: 9.1.1 (1)
		uksort( $params, 'strcmp' );
		$pairs = array();

		foreach ( $params as $parameter => $value ) {
			if ( is_array( $value ) ) {
				// When two or more parameters share the same name, they are sorted by their value
				// Ref: Spec: 9.1.1 (1)
				// June 12th, 2010 - changed to sort because of issue 164 by hidetaka
				sort( $value, SORT_STRING );

				foreach ( $value as $duplicate_value ) {
					$pairs[] = "{$parameter}={$duplicate_value}";
				}

			} else {
				$pairs[] = "{$parameter}={$value}";
			}
		}

		// For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
		// Each name-value pair is separated by an '&' character (ASCII code 38)
		return implode( '&', $pairs );
	}

	public static function parse_parameters( $input = '' ) {
		if ( '' === $input ) {
			return array();
		}

		$pairs             = explode( '&', $input );
		$parsed_parameters = array();

		foreach ( $pairs as $pair ) {
			$split     = explode( '=', $pair, 2 );
			$parameter = ET_Core_LIB_OAuthUtil::urldecode_rfc3986( $split[0] );
			$value     = isset( $split[1] ) ? ET_Core_LIB_OAuthUtil::urldecode_rfc3986( $split[1] ) : '';

			if ( isset( $parsed_parameters[ $parameter ] ) ) {
				// We have already received parameter(s) with this name, so add to the list
				// of parameters with this name
				if ( is_scalar( $parsed_parameters[ $parameter ] ) ) {
					// This is the first duplicate, so transform scalar (string) into an array
					// so we can add the duplicates
					$parsed_parameters[ $parameter ] = array( $parsed_parameters[ $parameter ] );
				}
				$parsed_parameters[ $parameter ][] = $value;
			} else {
				$parsed_parameters[ $parameter ] = $value;
			}
		}
		return $parsed_parameters;
	}

	public static function urldecode_rfc3986( $string ) {
		return rawurldecode( $string );
	}

	public static function urlencode_rfc3986( $input ) {
		$output = '';

		if ( is_array( $input ) ) {
			$output = array_map( array( 'ET_Core_LIB_OAuthUtil', 'urlencode_rfc3986' ), $input );

		} else if ( is_scalar( $input ) ) {
			$output = rawurlencode( utf8_encode( $input ) );
		}

		return $output;
	}
}


/**
 * A base class for implementing a Signature Method
 * See section 9 ("Signing Requests") in the spec
 */
abstract class ET_Core_LIB_OAuthSignatureMethod {
	/**
	 * Build the signature
	 * NOTE: The output of this function MUST NOT be urlencoded. The encoding is handled in
	 * {@link ET_Core_OAuth_Request} when the final request is serialized.
	 *
	 * @param ET_Core_LIB_OAuthRequest    $request
	 * @param ET_Core_LIB_OAuthConsumer   $consumer
	 * @param ET_Core_LIB_OAuthToken|null $token
	 *
	 * @return string
	 */
	abstract public function build_signature( $request, $consumer, $token = null );

	/**
	 * Verifies that a given signature is correct
	 *
	 * @param ET_Core_LIB_OAuthRequest  $request
	 * @param ET_Core_LIB_OAuthConsumer $consumer
	 * @param ET_Core_LIB_OAuthToken    $token
	 * @param string                    $signature
	 *
	 * @return bool
	 */
	public function check_signature( $request, $consumer, $token, $signature ) {
		$built = $this->build_signature( $request, $consumer, $token );

		// Check for zero length, although its unlikely here
		if ( empty( $built ) || empty( $signature ) ) {
			return false;
		}

		if ( strlen( $built ) !== strlen( $signature ) ) {
			return false;
		}

		// Avoid a timing leak with a (hopefully) time insensitive compare
		$result = 0;

		for ( $i = 0; $i < strlen( $signature ); $i ++ ) {
			$result |= ord( $built{$i} ) ^ ord( $signature{$i} );
		}

		return 0 === $result;
	}

	/**
	 * Returns the name of this Signature Method (ie HMAC-SHA1)
	 *
	 * @return string
	 */
	abstract public function get_name();
}


/**
 * The HMAC-SHA1 signature method uses the HMAC-SHA1 signature algorithm as defined in [RFC2104] where
 * the Signature Base String is the text and the key is the concatenated values of the Consumer Secret and
 * Token Secret (each encoded per Parameter Encoding first), separated by an '&' character (ASCII code 38)
 * even if empty. As per Chapter 9.2 of the HMAC-SHA1 spec.
 */
class ET_Core_LIB_OAuthHMACSHA1 extends ET_Core_LIB_OAuthSignatureMethod {
	/**
	 * @inheritDoc
	 */
	public function build_signature( $request, $consumer, $token = null ) {
		$base_string          = $request->get_signature_base_string();
		$token                = $token ? $token->secret : '';
		$request->base_string = $base_string;
		$key_parts            = array( $consumer->secret, $token );
		$key                  = implode( '&', $key_parts );

		return base64_encode( hash_hmac( 'sha1', $base_string, $key, true ) );
	}

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'HMAC-SHA1';
	}
}


class ET_Core_LIB_OAuthConsumer {
	public $callback_url;
	public $id;
	public $key;
	public $secret;

	public function __construct( $id, $secret, $callback_url = '' ) {
		$this->id           = $this->key = $id;
		$this->secret       = $secret;
		$this->callback_url = $callback_url;
	}

	function __toString() {
		$name = get_class( $this );
		$key  = 'ET_Core_LIB_OAuthConsumer' === $name ? 'key' : 'id';

		return "{$name}[{$key}={$this->key}, secret={$this->secret}]";
	}
}


class ET_Core_LIB_OAuthToken {
	public $key;
	public $secret;
	public $refresh_token;

	/**
	 * @param string $key    The OAuth Token
	 * @param string $secret The OAuth Token Secret
	 */
	public function __construct( $key, $secret ) {
		$this->key    = $key;
		$this->secret = $secret;
	}

	/**
	 * Generates the basic string serialization of a token that a server
	 * would respond to 'request_token' and 'access_token' calls with
	 *
	 * @return string
	 */
	public function __toString() {
		return sprintf( "oauth_token=%s&oauth_token_secret=%s",
			ET_Core_LIB_OAuthUtil::urlencode_rfc3986( $this->key ),
			ET_Core_LIB_OAuthUtil::urlencode_rfc3986( $this->secret )
		);
	}
}


class ET_Core_LIB_OAuthRequest extends ET_Core_LIB_OAuthBase {
	protected $parameters;
	protected $http_method;
	protected $http_url;
	public static $version = '1.0';
	public $base_string;

	/**
	 * ET_Core_OAuth_Request Constructor
	 *
	 * @param string     $http_method
	 * @param string     $http_url
	 * @param array|null $parameters
	 */
	public function __construct( $http_method, $http_url, $parameters = array() ) {
		$this->parameters  = $parameters;
		$this->http_method = $http_method;
		$this->http_url    = $http_url;
	}

	/**
	 * pretty much a helper function to set up the request
	 *
	 * @param ET_Core_LIB_OAuthConsumer $consumer
	 * @param ET_Core_LIB_OAuthToken    $token
	 * @param string                    $http_method
	 * @param string                    $http_url
	 * @param array                     $parameters
	 *
	 * @return ET_Core_LIB_OAuthRequest
	 */
	public static function from_consumer_and_token( $consumer, $token = null, $http_method, $http_url, $parameters = array() ) {
		$defaults = array(
			"oauth_version"      => ET_Core_LIB_OAuthRequest::$version,
			"oauth_nonce"        => ET_Core_LIB_OAuthRequest::generate_nonce(),
			"oauth_timestamp"    => time(),
			"oauth_consumer_key" => $consumer->key
		);

		if ( $token ) {
			$defaults['oauth_token'] = $token->key;
		}

		$parameters = wp_parse_args( $parameters, $defaults );

		return new ET_Core_LIB_OAuthRequest( $http_method, $http_url, $parameters );
	}

	/**
	 * Returns the HTTP Method in uppercase
	 *
	 * @return string
	 */
	public function get_normalized_http_method() {
		return strtoupper( $this->http_method );
	}

	/**
	 * parses the url and rebuilds it to be
	 * scheme://host/path
	 *
	 * @return string
	 */
	public function get_normalized_http_url() {
		$parts  = parse_url( $this->http_url );
		$scheme = $parts['scheme'];
		$host   = strtolower( $parts['host'] );
		$path   = $parts['path'];

		return "{$scheme}://{$host}{$path}";
	}

	/**
	 * @param $name
	 *
	 * @return string|null
	 */
	public function get_parameter( $name ) {
		return isset( $this->parameters[ $name ] ) ? $this->parameters[ $name ] : null;
	}

	/**
	 * @return array
	 */
	public function get_parameters() {
		return $this->parameters;
	}

	/**
	 * The request parameters, sorted and concatenated into a normalized string.
	 *
	 * @return string
	 */
	public function get_signable_parameters() {
		// Grab a copy of all parameters
		$params = $this->parameters;

		// Remove oauth_signature if present
		// Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
		if ( isset( $params['oauth_signature'] ) ) {
			unset( $params['oauth_signature'] );
		}

		return ET_Core_LIB_OAuthUtil::build_http_query( $params );
	}

	/**
	 * Returns the base string of this request
	 *
	 * The base string defined as the method, the url, and the parameters (normalized),
	 * each urlencoded and then concatenated with '&'.
	 *
	 * @return string
	 */
	public function get_signature_base_string() {
		$parts = array(
			$this->get_normalized_http_method(),
			$this->get_normalized_http_url(),
			$this->get_signable_parameters()
		);

		$parts = ET_Core_LIB_OAuthUtil::urlencode_rfc3986( $parts );

		return implode( '&', $parts );
	}

	/**
	 * @param $name
	 */
	public function remove_parameter( $name ) {
		unset( $this->parameters[ $name ] );
	}

	/**
	 * @param string $name
	 * @param string $value
	 */
	public function set_parameter( $name, $value ) {
		$this->parameters[ $name ] = $value;
	}

	/**
	 * Builds the data one would send in a POST request
	 * @param bool $need_json indicates the query data format ( http query string or json string )
	 *
	 * @return string
	 */
	public function to_post_data( $need_json = false ) {
		return ET_Core_LIB_OAuthUtil::build_http_query( $this->parameters, $need_json );
	}

	/**
	 * Builds a url usable for a GET request
	 *
	 * @return string
	 */
	public function to_url() {
		$postData = $this->to_post_data();
		$out      = $this->get_normalized_http_url();

		if ( $postData ) {
			$out .= '?' . $postData;
		}

		return $out;
	}

	/**
	 * Builds the HTTP Authorization Header
	 *
	 * @return string
	 */
	public function to_header() {
		$out = '';

		foreach ( $this->parameters as $parameter => $value ) {
			if ( 0 !== strpos( 'oauth', $parameter ) ) {
				continue;
			}

			if ( is_array( $value ) ) {
				self::write_log( 'Arrays not supported in headers!', 'ERROR' );
				continue;
			}

			$out .= ( '' === $out ) ? 'OAuth ' : ', ';
			$out .= ET_Core_LIB_OAuthUtil::urlencode_rfc3986( $parameter );
			$out .= '="' . ET_Core_LIB_OAuthUtil::urlencode_rfc3986( $value ) . '"';
		}

		return $out;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->to_url();
	}

	/**
	 * @param ET_Core_LIB_OAuthSignatureMethod $signature_method
	 * @param ET_Core_LIB_OAuthConsumer        $consumer
	 * @param ET_Core_LIB_OAuthToken           $token
	 */
	public function sign_request( $signature_method, $consumer, $token = null ) {
		$this->set_parameter( 'oauth_signature_method', $signature_method->get_name() );
		$signature = $this->build_signature( $signature_method, $consumer, $token );
		$this->set_parameter( 'oauth_signature', $signature );
	}

	/**
	 * @param ET_Core_LIB_OAuthSignatureMethod $signatureMethod
	 * @param ET_Core_LIB_OAuthConsumer        $consumer
	 * @param ET_Core_LIB_OAuthToken           $token
	 *
	 * @return string
	 */
	public function build_signature( $signatureMethod, $consumer, $token = null ) {
		return $signatureMethod->build_signature( $this, $consumer, $token );
	}

	/**
	 * @return string
	 */
	public static function generate_nonce() {
		return md5( microtime() . mt_rand() );
	}
}
