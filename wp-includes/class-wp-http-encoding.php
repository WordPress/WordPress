<?php
/**
 * HTTP API: WP_Http_Encoding class
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 4.4.0
 */

/**
 * Core class used to implement deflate and gzip transfer encoding support for HTTP requests.
 *
 * Includes RFC 1950, RFC 1951, and RFC 1952.
 *
 * @since 2.8.0
 */
class WP_Http_Encoding {

	/**
	 * Compress raw string using the deflate format.
	 *
	 * Supports the RFC 1951 standard.
	 *
	 * @since 2.8.0
	 *
	 * @param string $raw String to compress.
	 * @param int $level Optional, default is 9. Compression level, 9 is highest.
	 * @param string $supports Optional, not used. When implemented it will choose the right compression based on what the server supports.
	 * @return string|false False on failure.
	 */
	public static function compress( $raw, $level = 9, $supports = null ) {
		return gzdeflate( $raw, $level );
	}

	/**
	 * Decompression of deflated string.
	 *
	 * Will attempt to decompress using the RFC 1950 standard, and if that fails
	 * then the RFC 1951 standard deflate will be attempted. Finally, the RFC
	 * 1952 standard gzip decode will be attempted. If all fail, then the
	 * original compressed string will be returned.
	 *
	 * @since 2.8.0
	 *
	 * @param string $compressed String to decompress.
	 * @param int $length The optional length of the compressed data.
	 * @return string|bool False on failure.
	 */
	public static function decompress( $compressed, $length = null ) {

		if ( empty( $compressed ) ) {
			return $compressed;
		}

		if ( false !== ( $decompressed = @gzinflate( $compressed ) ) ) {
			return $decompressed;
		}

		if ( false !== ( $decompressed = self::compatible_gzinflate( $compressed ) ) ) {
			return $decompressed;
		}

		if ( false !== ( $decompressed = @gzuncompress( $compressed ) ) ) {
			return $decompressed;
		}

		if ( function_exists( 'gzdecode' ) ) {
			$decompressed = @gzdecode( $compressed );

			if ( false !== $decompressed ) {
				return $decompressed;
			}
		}

		return $compressed;
	}

	/**
	 * Decompression of deflated string while staying compatible with the majority of servers.
	 *
	 * Certain Servers will return deflated data with headers which PHP's gzinflate()
	 * function cannot handle out of the box. The following function has been created from
	 * various snippets on the gzinflate() PHP documentation.
	 *
	 * Warning: Magic numbers within. Due to the potential different formats that the compressed
	 * data may be returned in, some "magic offsets" are needed to ensure proper decompression
	 * takes place. For a simple progmatic way to determine the magic offset in use, see:
	 * https://core.trac.wordpress.org/ticket/18273
	 *
	 * @since 2.8.1
	 * @link https://core.trac.wordpress.org/ticket/18273
	 * @link https://secure.php.net/manual/en/function.gzinflate.php#70875
	 * @link https://secure.php.net/manual/en/function.gzinflate.php#77336
	 *
	 * @param string $gzData String to decompress.
	 * @return string|bool False on failure.
	 */
	public static function compatible_gzinflate( $gzData ) {

		// Compressed data might contain a full header, if so strip it for gzinflate().
		if ( substr( $gzData, 0, 3 ) == "\x1f\x8b\x08" ) {
			$i   = 10;
			$flg = ord( substr( $gzData, 3, 1 ) );
			if ( $flg > 0 ) {
				if ( $flg & 4 ) {
					list($xlen) = unpack( 'v', substr( $gzData, $i, 2 ) );
					$i          = $i + 2 + $xlen;
				}
				if ( $flg & 8 ) {
					$i = strpos( $gzData, "\0", $i ) + 1;
				}
				if ( $flg & 16 ) {
					$i = strpos( $gzData, "\0", $i ) + 1;
				}
				if ( $flg & 2 ) {
					$i = $i + 2;
				}
			}
			$decompressed = @gzinflate( substr( $gzData, $i, -8 ) );
			if ( false !== $decompressed ) {
				return $decompressed;
			}
		}

		// Compressed data from java.util.zip.Deflater amongst others.
		$decompressed = @gzinflate( substr( $gzData, 2 ) );
		if ( false !== $decompressed ) {
			return $decompressed;
		}

		return false;
	}

	/**
	 * What encoding types to accept and their priority values.
	 *
	 * @since 2.8.0
	 *
	 * @param string $url
	 * @param array  $args
	 * @return string Types of encoding to accept.
	 */
	public static function accept_encoding( $url, $args ) {
		$type                = array();
		$compression_enabled = self::is_available();

		if ( ! $args['decompress'] ) { // Decompression specifically disabled.
			$compression_enabled = false;
		} elseif ( $args['stream'] ) { // Disable when streaming to file.
			$compression_enabled = false;
		} elseif ( isset( $args['limit_response_size'] ) ) { // If only partial content is being requested, we won't be able to decompress it.
			$compression_enabled = false;
		}

		if ( $compression_enabled ) {
			if ( function_exists( 'gzinflate' ) ) {
				$type[] = 'deflate;q=1.0';
			}

			if ( function_exists( 'gzuncompress' ) ) {
				$type[] = 'compress;q=0.5';
			}

			if ( function_exists( 'gzdecode' ) ) {
				$type[] = 'gzip;q=0.5';
			}
		}

		/**
		 * Filters the allowed encoding types.
		 *
		 * @since 3.6.0
		 *
		 * @param array  $type Encoding types allowed. Accepts 'gzinflate',
		 *                     'gzuncompress', 'gzdecode'.
		 * @param string $url  URL of the HTTP request.
		 * @param array  $args HTTP request arguments.
		 */
		$type = apply_filters( 'wp_http_accept_encoding', $type, $url, $args );

		return implode( ', ', $type );
	}

	/**
	 * What encoding the content used when it was compressed to send in the headers.
	 *
	 * @since 2.8.0
	 *
	 * @return string Content-Encoding string to send in the header.
	 */
	public static function content_encoding() {
		return 'deflate';
	}

	/**
	 * Whether the content be decoded based on the headers.
	 *
	 * @since 2.8.0
	 *
	 * @param array|string $headers All of the available headers.
	 * @return bool
	 */
	public static function should_decode( $headers ) {
		if ( is_array( $headers ) ) {
			if ( array_key_exists( 'content-encoding', $headers ) && ! empty( $headers['content-encoding'] ) ) {
				return true;
			}
		} elseif ( is_string( $headers ) ) {
			return ( stripos( $headers, 'content-encoding:' ) !== false );
		}

		return false;
	}

	/**
	 * Whether decompression and compression are supported by the PHP version.
	 *
	 * Each function is tested instead of checking for the zlib extension, to
	 * ensure that the functions all exist in the PHP version and aren't
	 * disabled.
	 *
	 * @since 2.8.0
	 *
	 * @return bool
	 */
	public static function is_available() {
		return ( function_exists( 'gzuncompress' ) || function_exists( 'gzdeflate' ) || function_exists( 'gzinflate' ) );
	}
}
