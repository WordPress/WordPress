<?php
/**
 * Download handler
 *
 * Handle digital downloads.
 *
 * @package WooCommerce\Classes
 * @version 2.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Download handler class.
 */
class WC_Download_Handler {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		if ( isset( $_GET['download_file'], $_GET['order'] ) && ( isset( $_GET['email'] ) || isset( $_GET['uid'] ) ) ) { // WPCS: input var ok, CSRF ok.
			add_action( 'init', array( __CLASS__, 'download_product' ) );
		}
		add_action( 'woocommerce_download_file_redirect', array( __CLASS__, 'download_file_redirect' ), 10, 2 );
		add_action( 'woocommerce_download_file_xsendfile', array( __CLASS__, 'download_file_xsendfile' ), 10, 2 );
		add_action( 'woocommerce_download_file_force', array( __CLASS__, 'download_file_force' ), 10, 2 );
	}

	/**
	 * Check if we need to download a file and check validity.
	 */
	public static function download_product() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$product_id = absint( $_GET['download_file'] ); // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected, WordPress.VIP.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$product    = wc_get_product( $product_id );
		$downloads  = $product ? $product->get_downloads() : array();
		$data_store = WC_Data_Store::load( 'customer-download' );

		$key = empty( $_GET['key'] ) ? '' : sanitize_text_field( wp_unslash( $_GET['key'] ) );

		if (
			! $product
			|| empty( $key )
			|| empty( $_GET['order'] )
			|| ! isset( $downloads[ $key ] )
			|| ! $downloads[ $key ]->get_enabled()
		) {
			self::download_error( __( 'Invalid download link.', 'woocommerce' ) );
		}

		// Fallback, accept email address if it's passed.
		if ( empty( $_GET['email'] ) && empty( $_GET['uid'] ) ) { // WPCS: input var ok, CSRF ok.
			self::download_error( __( 'Invalid download link.', 'woocommerce' ) );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		$order_id = wc_get_order_id_by_order_key( wc_clean( wp_unslash( $_GET['order'] ) ) ); // WPCS: input var ok, CSRF ok.
		$order    = wc_get_order( $order_id );

		if ( isset( $_GET['email'] ) ) { // WPCS: input var ok, CSRF ok.
			$email_address = wp_unslash( $_GET['email'] ); // WPCS: input var ok, CSRF ok, sanitization ok.
		} else {
			// Get email address from order to verify hash.
			$email_address = is_a( $order, 'WC_Order' ) ? $order->get_billing_email() : null;

			// Prepare email address hash.
			$email_hash = function_exists( 'hash' ) ? hash( 'sha256', $email_address ) : sha1( $email_address );

			if ( is_null( $email_address ) || ! hash_equals( wp_unslash( $_GET['uid'] ), $email_hash ) ) { // WPCS: input var ok, CSRF ok, sanitization ok.
				self::download_error( __( 'Invalid download link.', 'woocommerce' ) );
			}
		}

		$download_ids = $data_store->get_downloads(
			array(
				'user_email'  => sanitize_email( str_replace( ' ', '+', $email_address ) ),
				'order_key'   => wc_clean( wp_unslash( $_GET['order'] ) ), // WPCS: input var ok, CSRF ok.
				'product_id'  => $product_id,
				'download_id' => wc_clean( preg_replace( '/\s+/', ' ', wp_unslash( $_GET['key'] ) ) ), // WPCS: input var ok, CSRF ok, sanitization ok.
				'orderby'     => 'downloads_remaining',
				'order'       => 'DESC',
				'limit'       => 1,
				'return'      => 'ids',
			)
		);

		if ( empty( $download_ids ) ) {
			self::download_error( __( 'Invalid download link.', 'woocommerce' ) );
		}

		$download = new WC_Customer_Download( current( $download_ids ) );

		/**
		 * Filter download filepath.
		 *
		 * @since 4.0.0
		 * @param string $file_path File path.
		 * @param string $email_address Email address.
		 * @param WC_Order|bool $order Order object or false.
		 * @param WC_Product $product Product object.
		 * @param WC_Customer_Download $download Download data.
		 */
		$file_path = apply_filters(
			'woocommerce_download_product_filepath',
			$product->get_file_download_path( $download->get_download_id() ),
			$email_address,
			$order,
			$product,
			$download
		);

		$parsed_file_path = self::parse_file_path( $file_path );
		$download_range   = self::get_download_range( @filesize( $parsed_file_path['file_path'] ) );  // @codingStandardsIgnoreLine.

		self::check_order_is_valid( $download );
		if ( ! $download_range['is_range_request'] ) {
			// If the remaining download count goes to 0, allow range requests to be able to finish streaming from iOS devices.
			self::check_downloads_remaining( $download );
		}
		self::check_download_expiry( $download );
		self::check_download_login_required( $download );

		do_action(
			'woocommerce_download_product',
			$download->get_user_email(),
			$download->get_order_key(),
			$download->get_product_id(),
			$download->get_user_id(),
			$download->get_download_id(),
			$download->get_order_id()
		);
		$download->save();

		// Track the download in logs and change remaining/counts.
		$current_user_id = get_current_user_id();
		$ip_address      = WC_Geolocation::get_ip_address();
		if ( ! $download_range['is_range_request'] ) {
			$download->track_download( $current_user_id > 0 ? $current_user_id : null, ! empty( $ip_address ) ? $ip_address : null );
		}

		self::download( $file_path, $download->get_product_id() );
	}

	/**
	 * Check if an order is valid for downloading from.
	 *
	 * @param WC_Customer_Download $download Download instance.
	 */
	private static function check_order_is_valid( $download ) {
		if ( $download->get_order_id() ) {
			$order = wc_get_order( $download->get_order_id() );

			if ( $order && ! $order->is_download_permitted() ) {
				self::download_error( __( 'Invalid order.', 'woocommerce' ), '', 403 );
			}
		}
	}

	/**
	 * Check if there are downloads remaining.
	 *
	 * @param WC_Customer_Download $download Download instance.
	 */
	private static function check_downloads_remaining( $download ) {
		if ( '' !== $download->get_downloads_remaining() && 0 >= $download->get_downloads_remaining() ) {
			self::download_error( __( 'Sorry, you have reached your download limit for this file', 'woocommerce' ), '', 403 );
		}
	}

	/**
	 * Check if the download has expired.
	 *
	 * @param WC_Customer_Download $download Download instance.
	 */
	private static function check_download_expiry( $download ) {
		if ( ! is_null( $download->get_access_expires() ) && $download->get_access_expires()->getTimestamp() < strtotime( 'midnight', time() ) ) {
			self::download_error( __( 'Sorry, this download has expired', 'woocommerce' ), '', 403 );
		}
	}

	/**
	 * Check if a download requires the user to login first.
	 *
	 * @param WC_Customer_Download $download Download instance.
	 */
	private static function check_download_login_required( $download ) {
		if ( $download->get_user_id() && 'yes' === get_option( 'woocommerce_downloads_require_login' ) ) {
			if ( ! is_user_logged_in() ) {
				if ( wc_get_page_id( 'myaccount' ) ) {
					wp_safe_redirect( add_query_arg( 'wc_error', rawurlencode( __( 'You must be logged in to download files.', 'woocommerce' ) ), wc_get_page_permalink( 'myaccount' ) ) );
					exit;
				} else {
					self::download_error( __( 'You must be logged in to download files.', 'woocommerce' ) . ' <a href="' . esc_url( wp_login_url( wc_get_page_permalink( 'myaccount' ) ) ) . '" class="wc-forward">' . __( 'Login', 'woocommerce' ) . '</a>', __( 'Log in to Download Files', 'woocommerce' ), 403 );
				}
			} elseif ( ! current_user_can( 'download_file', $download ) ) {
				self::download_error( __( 'This is not your download link.', 'woocommerce' ), '', 403 );
			}
		}
	}

	/**
	 * Count download.
	 *
	 * @deprecated 4.4.0
	 * @param array $download_data Download data.
	 */
	public static function count_download( $download_data ) {
		wc_deprecated_function( 'WC_Download_Handler::count_download', '4.4.0', '' );
	}

	/**
	 * Download a file - hook into init function.
	 *
	 * @param string  $file_path  URL to file.
	 * @param integer $product_id Product ID of the product being downloaded.
	 */
	public static function download( $file_path, $product_id ) {
		if ( ! $file_path ) {
			self::download_error( __( 'No file defined', 'woocommerce' ) );
		}

		$filename = basename( $file_path );

		if ( strstr( $filename, '?' ) ) {
			$filename = current( explode( '?', $filename ) );
		}

		$filename = apply_filters( 'woocommerce_file_download_filename', $filename, $product_id );

		/**
		 * Filter download method.
		 *
		 * @since 4.5.0
		 * @param string $method     Download method.
		 * @param int    $product_id Product ID.
		 * @param string $file_path  URL to file.
		 */
		$file_download_method = apply_filters( 'woocommerce_file_download_method', get_option( 'woocommerce_file_download_method', 'force' ), $product_id, $file_path );

		// Add action to prevent issues in IE.
		add_action( 'nocache_headers', array( __CLASS__, 'ie_nocache_headers_fix' ) );

		// Trigger download via one of the methods.
		do_action( 'woocommerce_download_file_' . $file_download_method, $file_path, $filename );
	}

	/**
	 * Redirect to a file to start the download.
	 *
	 * @param string $file_path File path.
	 * @param string $filename  File name.
	 */
	public static function download_file_redirect( $file_path, $filename = '' ) {
		header( 'Location: ' . $file_path );
		exit;
	}

	/**
	 * Parse file path and see if its remote or local.
	 *
	 * @param  string $file_path File path.
	 * @return array
	 */
	public static function parse_file_path( $file_path ) {
		$wp_uploads     = wp_upload_dir();
		$wp_uploads_dir = $wp_uploads['basedir'];
		$wp_uploads_url = $wp_uploads['baseurl'];

		/**
		 * Replace uploads dir, site url etc with absolute counterparts if we can.
		 * Note the str_replace on site_url is on purpose, so if https is forced
		 * via filters we can still do the string replacement on a HTTP file.
		 */
		$replacements = array(
			$wp_uploads_url                                                   => $wp_uploads_dir,
			network_site_url( '/', 'https' )                                  => ABSPATH,
			str_replace( 'https:', 'http:', network_site_url( '/', 'http' ) ) => ABSPATH,
			site_url( '/', 'https' )                                          => ABSPATH,
			str_replace( 'https:', 'http:', site_url( '/', 'http' ) )         => ABSPATH,
		);

		$count            = 0;
		$file_path        = str_replace( array_keys( $replacements ), array_values( $replacements ), $file_path, $count );
		$parsed_file_path = wp_parse_url( $file_path );
		$remote_file      = null === $count || 0 === $count; // Remote file only if there were no replacements.

		// Paths that begin with '//' are always remote URLs.
		if ( '//' === substr( $file_path, 0, 2 ) ) {
			$file_path = ( is_ssl() ? 'https:' : 'http:' ) . $file_path;

			/**
			 * Filter the remote filepath for download.
			 *
			 * @since 6.5.0
			 * @param string $file_path File path.
			 */
			return array(
				'remote_file' => true,
				'file_path'   => apply_filters( 'woocommerce_download_parse_remote_file_path', $file_path ),
			);
		}

		// See if path needs an abspath prepended to work.
		if ( file_exists( ABSPATH . $file_path ) ) {
			$remote_file = false;
			$file_path   = ABSPATH . $file_path;

		} elseif ( '/wp-content' === substr( $file_path, 0, 11 ) ) {
			$remote_file = false;
			$file_path   = realpath( WP_CONTENT_DIR . substr( $file_path, 11 ) );

			// Check if we have an absolute path.
		} elseif ( ( ! isset( $parsed_file_path['scheme'] ) || ! in_array( $parsed_file_path['scheme'], array( 'http', 'https', 'ftp' ), true ) ) && isset( $parsed_file_path['path'] ) ) {
			$remote_file = false;
			$file_path   = $parsed_file_path['path'];
		}

		/**
		* Filter the filepath for download.
		*
		* @since 6.5.0
		* @param string  $file_path File path.
		* @param bool $remote_file Remote File Indicator.
		*/
		return array(
			'remote_file' => $remote_file,
			'file_path'   => apply_filters( 'woocommerce_download_parse_file_path', $file_path, $remote_file ),
		);
	}

	/**
	 * Download a file using X-Sendfile, X-Lighttpd-Sendfile, or X-Accel-Redirect if available.
	 *
	 * @param string $file_path File path.
	 * @param string $filename  File name.
	 */
	public static function download_file_xsendfile( $file_path, $filename ) {
		$parsed_file_path = self::parse_file_path( $file_path );

		/**
		 * Fallback on force download method for remote files. This is because:
		 * 1. xsendfile needs proxy configuration to work for remote files, which cannot be assumed to be available on most hosts.
		 * 2. Force download method is more secure than redirect method if `allow_url_fopen` is enabled in `php.ini`.
		 */
		if ( $parsed_file_path['remote_file'] && ! apply_filters( 'woocommerce_use_xsendfile_for_remote', false ) ) {
			do_action( 'woocommerce_download_file_force', $file_path, $filename );
			return;
		}

		if ( function_exists( 'apache_get_modules' ) && in_array( 'mod_xsendfile', apache_get_modules(), true ) ) {
			self::download_headers( $parsed_file_path['file_path'], $filename );
			$filepath = apply_filters( 'woocommerce_download_file_xsendfile_file_path', $parsed_file_path['file_path'], $file_path, $filename, $parsed_file_path );
			header( 'X-Sendfile: ' . $filepath );
			exit;
		} elseif ( stristr( getenv( 'SERVER_SOFTWARE' ), 'lighttpd' ) ) {
			self::download_headers( $parsed_file_path['file_path'], $filename );
			$filepath = apply_filters( 'woocommerce_download_file_xsendfile_lighttpd_file_path', $parsed_file_path['file_path'], $file_path, $filename, $parsed_file_path );
			header( 'X-Lighttpd-Sendfile: ' . $filepath );
			exit;
		} elseif ( stristr( getenv( 'SERVER_SOFTWARE' ), 'nginx' ) || stristr( getenv( 'SERVER_SOFTWARE' ), 'cherokee' ) ) {
			self::download_headers( $parsed_file_path['file_path'], $filename );
			$xsendfile_path = trim( preg_replace( '`^' . str_replace( '\\', '/', getcwd() ) . '`', '', $parsed_file_path['file_path'] ), '/' );
			$xsendfile_path = apply_filters( 'woocommerce_download_file_xsendfile_x_accel_redirect_file_path', $xsendfile_path, $file_path, $filename, $parsed_file_path );
			header( "X-Accel-Redirect: /$xsendfile_path" );
			exit;
		}

		// Fallback.
		wc_get_logger()->warning(
			sprintf(
				/* translators: %1$s contains the filepath of the digital asset. */
				__( '%1$s could not be served using the X-Accel-Redirect/X-Sendfile method. A Force Download will be used instead.', 'woocommerce' ),
				$file_path
			)
		);
		self::download_file_force( $file_path, $filename );
	}

	/**
	 * Parse the HTTP_RANGE request from iOS devices.
	 * Does not support multi-range requests.
	 *
	 * @param int $file_size Size of file in bytes.
	 * @return array {
	 *     Information about range download request: beginning and length of
	 *     file chunk, whether the range is valid/supported and whether the request is a range request.
	 *
	 *     @type int  $start            Byte offset of the beginning of the range. Default 0.
	 *     @type int  $length           Length of the requested file chunk in bytes. Optional.
	 *     @type bool $is_range_valid   Whether the requested range is a valid and supported range.
	 *     @type bool $is_range_request Whether the request is a range request.
	 * }
	 */
	protected static function get_download_range( $file_size ) {
		$start          = 0;
		$download_range = array(
			'start'            => $start,
			'is_range_valid'   => false,
			'is_range_request' => false,
		);

		if ( ! $file_size ) {
			return $download_range;
		}

		$end                      = $file_size - 1;
		$download_range['length'] = $file_size;

		if ( isset( $_SERVER['HTTP_RANGE'] ) ) { // @codingStandardsIgnoreLine.
			$http_range                         = sanitize_text_field( wp_unslash( $_SERVER['HTTP_RANGE'] ) ); // WPCS: input var ok.
			$download_range['is_range_request'] = true;

			$c_start = $start;
			$c_end   = $end;
			// Extract the range string.
			list( , $range ) = explode( '=', $http_range, 2 );
			// Make sure the client hasn't sent us a multibyte range.
			if ( strpos( $range, ',' ) !== false ) {
				return $download_range;
			}

			/*
			 * If the range starts with an '-' we start from the beginning.
			 * If not, we forward the file pointer
			 * and make sure to get the end byte if specified.
			 */
			if ( '-' === $range[0] ) {
				// The n-number of the last bytes is requested.
				$c_start = $file_size - substr( $range, 1 );
			} else {
				$range   = explode( '-', $range );
				$c_start = ( isset( $range[0] ) && is_numeric( $range[0] ) ) ? (int) $range[0] : 0;
				$c_end   = ( isset( $range[1] ) && is_numeric( $range[1] ) ) ? (int) $range[1] : $file_size;
			}

			/*
			 * Check the range and make sure it's treated according to the specs: http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html.
			 * End bytes can not be larger than $end.
			 */
			$c_end = ( $c_end > $end ) ? $end : $c_end;
			// Validate the requested range and return an error if it's not correct.
			if ( $c_start > $c_end || $c_start > $file_size - 1 || $c_end >= $file_size ) {
				return $download_range;
			}
			$start  = $c_start;
			$end    = $c_end;
			$length = $end - $start + 1;

			$download_range['start']          = $start;
			$download_range['length']         = $length;
			$download_range['is_range_valid'] = true;
		}
		return $download_range;
	}

	/**
	 * Force download - this is the default method.
	 *
	 * @param string $file_path File path.
	 * @param string $filename  File name.
	 */
	public static function download_file_force( $file_path, $filename ) {
		$parsed_file_path = self::parse_file_path( $file_path );
		$download_range   = self::get_download_range( @filesize( $parsed_file_path['file_path'] ) ); // @codingStandardsIgnoreLine.

		self::download_headers( $parsed_file_path['file_path'], $filename, $download_range );

		$start  = isset( $download_range['start'] ) ? $download_range['start'] : 0;
		$length = isset( $download_range['length'] ) ? $download_range['length'] : 0;
		if ( ! self::readfile_chunked( $parsed_file_path['file_path'], $start, $length ) ) {
			if ( $parsed_file_path['remote_file'] && 'yes' === get_option( 'woocommerce_downloads_redirect_fallback_allowed' ) ) {
				wc_get_logger()->warning(
					sprintf(
						/* translators: %1$s contains the filepath of the digital asset. */
						__( '%1$s could not be served using the Force Download method. A redirect will be used instead.', 'woocommerce' ),
						$file_path
					)
				);
				self::download_file_redirect( $file_path );
			} else {
				self::download_error( __( 'File not found', 'woocommerce' ) );
			}
		}

		exit;
	}

	/**
	 * Get content type of a download.
	 *
	 * @param  string $file_path File path.
	 * @return string
	 */
	private static function get_download_content_type( $file_path ) {
		$file_extension = strtolower( substr( strrchr( $file_path, '.' ), 1 ) );
		$ctype          = 'application/force-download';

		foreach ( get_allowed_mime_types() as $mime => $type ) {
			$mimes = explode( '|', $mime );
			if ( in_array( $file_extension, $mimes, true ) ) {
				$ctype = $type;
				break;
			}
		}

		return $ctype;
	}

	/**
	 * Set headers for the download.
	 *
	 * @param string $file_path      File path.
	 * @param string $filename       File name.
	 * @param array  $download_range Array containing info about range download request (see {@see get_download_range} for structure).
	 */
	private static function download_headers( $file_path, $filename, $download_range = array() ) {
		self::check_server_config();
		self::clean_buffers();
		wc_nocache_headers();

		header( 'X-Robots-Tag: noindex, nofollow', true );
		header( 'Content-Type: ' . self::get_download_content_type( $file_path ) );
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: ' . self::get_content_disposition() . '; filename="' . $filename . '";' );
		header( 'Content-Transfer-Encoding: binary' );

		$file_size = @filesize( $file_path ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		if ( ! $file_size ) {
			return;
		}

		if ( isset( $download_range['is_range_request'] ) && true === $download_range['is_range_request'] ) {
			if ( false === $download_range['is_range_valid'] ) {
				header( 'HTTP/1.1 416 Requested Range Not Satisfiable' );
				header( 'Content-Range: bytes 0-' . ( $file_size - 1 ) . '/' . $file_size );
				exit;
			}

			$start  = $download_range['start'];
			$end    = $download_range['start'] + $download_range['length'] - 1;
			$length = $download_range['length'];

			header( 'HTTP/1.1 206 Partial Content' );
			header( "Accept-Ranges: 0-$file_size" );
			header( "Content-Range: bytes $start-$end/$file_size" );
			header( "Content-Length: $length" );
		} else {
			header( 'Content-Length: ' . $file_size );
		}
	}

	/**
	 * Check and set certain server config variables to ensure downloads work as intended.
	 */
	private static function check_server_config() {
		wc_set_time_limit( 0 );
		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_apache_setenv
		}
		@ini_set( 'zlib.output_compression', 'Off' ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_ini_set
		@session_write_close(); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.VIP.SessionFunctionsUsage.session_session_write_close
	}

	/**
	 * Clean all output buffers.
	 *
	 * Can prevent errors, for example: transfer closed with 3 bytes remaining to read.
	 */
	private static function clean_buffers() {
		if ( ob_get_level() ) {
			$levels = ob_get_level();
			for ( $i = 0; $i < $levels; $i++ ) {
				@ob_end_clean(); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			}
		} else {
			@ob_end_clean(); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		}
	}

	/**
	 *
	 * Get selected content disposition
	 *
	 * Defaults to attachment if `woocommerce_downloads_deliver_inline` setting is not selected.
	 *
	 * @return string Content disposition value.
	 */
	private static function get_content_disposition() : string {
		$disposition = 'attachment';
		if ( 'yes' === get_option( 'woocommerce_downloads_deliver_inline' ) ) {
			$disposition = 'inline';
		}
		return $disposition;
	}

	/**
	 * Read file chunked.
	 *
	 * Reads file in chunks so big downloads are possible without changing PHP.INI - http://codeigniter.com/wiki/Download_helper_for_large_files/.
	 *
	 * @param  string $file   File.
	 * @param  int    $start  Byte offset/position of the beginning from which to read from the file.
	 * @param  int    $length Length of the chunk to be read from the file in bytes, 0 means full file.
	 * @return bool Success or fail
	 */
	public static function readfile_chunked( $file, $start = 0, $length = 0 ) {
		if ( ! defined( 'WC_CHUNK_SIZE' ) ) {
			define( 'WC_CHUNK_SIZE', 1024 * 1024 );
		}
		$handle = @fopen( $file, 'r' ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_fopen

		if ( false === $handle ) {
			return false;
		}

		if ( ! $length ) {
			$length = @filesize( $file ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		}

		$read_length = (int) WC_CHUNK_SIZE;

		if ( $length ) {
			$end = $start + $length - 1;

			@fseek( $handle, $start ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			$p = @ftell( $handle ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

			while ( ! @feof( $handle ) && $p <= $end ) { // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				// Don't run past the end of file.
				if ( $p + $read_length > $end ) {
					$read_length = $end - $p + 1;
				}

				echo @fread( $handle, $read_length ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.XSS.EscapeOutput.OutputNotEscaped, WordPress.WP.AlternativeFunctions.file_system_read_fread
				$p = @ftell( $handle ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

				if ( ob_get_length() ) {
					ob_flush();
					flush();
				}
			}
		} else {
			while ( ! @feof( $handle ) ) { // @codingStandardsIgnoreLine.
				echo @fread( $handle, $read_length ); // @codingStandardsIgnoreLine.
				if ( ob_get_length() ) {
					ob_flush();
					flush();
				}
			}
		}

		return @fclose( $handle ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_fclose
	}

	/**
	 * Filter headers for IE to fix issues over SSL.
	 *
	 * IE bug prevents download via SSL when Cache Control and Pragma no-cache headers set.
	 *
	 * @param array $headers HTTP headers.
	 * @return array
	 */
	public static function ie_nocache_headers_fix( $headers ) {
		if ( is_ssl() && ! empty( $GLOBALS['is_IE'] ) ) {
			$headers['Cache-Control'] = 'private';
			unset( $headers['Pragma'] );
		}
		return $headers;
	}

	/**
	 * Die with an error message if the download fails.
	 *
	 * @param string  $message Error message.
	 * @param string  $title   Error title.
	 * @param integer $status  Error status.
	 */
	private static function download_error( $message, $title = '', $status = 404 ) {
		/*
		 * Since we will now render a message instead of serving a download, we should unwind some of the previously set
		 * headers.
		 */
		if ( headers_sent() ) {
			wc_get_logger()->log( 'warning', __( 'Headers already sent when generating download error message.', 'woocommerce' ) );
		} else {
			header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );
			header_remove( 'Content-Description;' );
			header_remove( 'Content-Disposition' );
			header_remove( 'Content-Transfer-Encoding' );
		}

		if ( ! strstr( $message, '<a ' ) ) {
			$message .= ' <a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '" class="wc-forward">' . esc_html__( 'Go to shop', 'woocommerce' ) . '</a>';
		}
		wp_die( $message, $title, array( 'response' => $status ) ); // WPCS: XSS ok.
	}
}

WC_Download_Handler::init();
