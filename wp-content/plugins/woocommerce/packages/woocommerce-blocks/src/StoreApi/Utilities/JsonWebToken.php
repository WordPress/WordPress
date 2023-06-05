<?php

namespace Automattic\WooCommerce\StoreApi\Utilities;

/**
 * JsonWebToken class.
 *
 * Simple Json Web Token generator & verifier static utility class, currently supporting only HS256 signatures.
 */
final class JsonWebToken {

	/**
	 * JWT header type.
	 *
	 * @var string
	 */
	private static $type = 'JWT';

	/**
	 * JWT algorithm to generate signature.
	 *
	 * @var string
	 */
	private static $algorithm = 'HS256';

	/**
	 * Generates a token from provided data and secret.
	 *
	 * @param array  $payload Payload data.
	 * @param string $secret The secret used to generate the signature.
	 *
	 * @return string
	 */
	public static function create( array $payload, string $secret ) {
		$header    = self::to_base_64_url( self::generate_header() );
		$payload   = self::to_base_64_url( self::generate_payload( $payload ) );
		$signature = self::to_base_64_url( self::generate_signature( $header . '.' . $payload, $secret ) );

		return $header . '.' . $payload . '.' . $signature;
	}

	/**
	 * Validates a provided token against the provided secret.
	 * Checks for format, valid header for our class, expiration claim validity and signature.
	 * https://datatracker.ietf.org/doc/html/rfc7519#section-7.2
	 *
	 * @param string $token Full token string.
	 * @param string $secret The secret used to generate the signature.
	 *
	 * @return bool
	 */
	public static function validate( string $token, string $secret ) {
		/**
		 * Confirm the structure of a JSON Web Token, it has three parts separated
		 * by dots and complies with Base64URL standards.
		 */
		if ( preg_match( '/^[a-zA-Z\d\-_=]+\.[a-zA-Z\d\-_=]+\.[a-zA-Z\d\-_=]+$/', $token ) !== 1 ) {
			return false;
		}

		$parts = self::get_parts( $token );

		/**
		 * Check if header declares a supported JWT by this class.
		 */
		if (
			! is_object( $parts->header ) ||
			! property_exists( $parts->header, 'typ' ) ||
			! property_exists( $parts->header, 'alg' ) ||
			self::$type !== $parts->header->typ ||
			self::$algorithm !== $parts->header->alg
		) {
			return false;
		}

		/**
		 * Check if token is expired.
		 */
		if ( ! property_exists( $parts->payload, 'exp' ) || time() > (int) $parts->payload->exp ) {
			return false;
		}

		/**
		 * Check if the token is based on our secret.
		 */
		$encoded_regenerated_signature = self::to_base_64_url(
			self::generate_signature( $parts->header_encoded . '.' . $parts->payload_encoded, $secret )
		);

		return hash_equals( $encoded_regenerated_signature, $parts->signature_encoded );
	}

	/**
	 * Returns the decoded/encoded header, payload and signature from a token string.
	 *
	 * @param string $token Full token string.
	 *
	 * @return object
	 */
	public static function get_parts( string $token ) {
		$parts = explode( '.', $token );

		return (object) array(
			'header'            => json_decode( self::from_base_64_url( $parts[0] ) ),
			'header_encoded'    => $parts[0],
			'payload'           => json_decode( self::from_base_64_url( $parts[1] ) ),
			'payload_encoded'   => $parts[1],
			'signature'         => self::from_base_64_url( $parts[2] ),
			'signature_encoded' => $parts[2],

		);
	}

	/**
	 * Generates the json formatted header for our HS256 JWT token.
	 *
	 * @return string|bool
	 */
	private static function generate_header() {
		return wp_json_encode(
			array(
				'alg' => self::$algorithm,
				'typ' => self::$type,
			)
		);
	}

	/**
	 * Generates a sha256 signature for the provided string using the provided secret.
	 *
	 * @param string $string Header + Payload token substring.
	 * @param string $secret The secret used to generate the signature.
	 *
	 * @return false|string
	 */
	private static function generate_signature( string $string, string $secret ) {
		return hash_hmac(
			'sha256',
			$string,
			$secret,
			true
		);
	}

	/**
	 * Generates the payload in json formatted string.
	 *
	 * @param array $payload Payload data.
	 *
	 * @return string|bool
	 */
	private static function generate_payload( array $payload ) {
		return wp_json_encode( array_merge( $payload, [ 'iat' => time() ] ) );
	}

	/**
	 * Encodes a string to url safe base64.
	 *
	 * @param string $string The string to be encoded.
	 *
	 * @return string
	 */
	private static function to_base_64_url( string $string ) {
		return str_replace(
			array( '+', '/', '=' ),
			array( '-', '_', '' ),
			base64_encode( $string ) // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		);
	}

	/**
	 * Decodes a string encoded using url safe base64, supporting auto padding.
	 *
	 * @param string $string the string to be decoded.
	 *
	 * @return string
	 */
	private static function from_base_64_url( string $string ) {
		/**
		 * Add padding to base64 strings which require it. Some base64 URL strings
		 * which are decoded will have missing padding which is represented by the
		 * equals sign.
		 */
		if ( strlen( $string ) % 4 !== 0 ) {
			return self::from_base_64_url( $string . '=' );
		}

		return base64_decode( // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			str_replace(
				array( '-', '_' ),
				array( '+', '/' ),
				$string
			)
		);
	}
}
