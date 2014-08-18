<?php
/**
 * Download handler
 *
 * Handle digital downloads.
 *
 * @class 		WC_Download_Handler
 * @version		2.1.0
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_Download_Handler {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'download_product' ) );
	}

	/**
	 * Check if we need to download a file and check validity
	 */
	public function download_product() {
		if ( isset( $_GET['download_file'] ) && isset( $_GET['order'] ) && isset( $_GET['email'] ) ) {

			global $wpdb;

			$product_id           = (int) $_GET['download_file'];
			$order_key            = $_GET['order'];
			$email                = sanitize_email( str_replace( ' ', '+', $_GET['email'] ) );
			$download_id          = isset( $_GET['key'] ) ? preg_replace( '/\s+/', ' ', $_GET['key'] ) : '';
			$_product             = get_product( $product_id );

			if ( ! is_email( $email) ) {
				wp_die( __( 'Invalid email address.', 'woocommerce' ) . ' <a href="' . esc_url( home_url() ) . '" class="wc-forward">' . __( 'Go to homepage', 'woocommerce' ) . '</a>' );
			}

			$query = "
				SELECT order_id,downloads_remaining,user_id,download_count,access_expires,download_id
				FROM " . $wpdb->prefix . "woocommerce_downloadable_product_permissions
				WHERE user_email = %s
				AND order_key = %s
				AND product_id = %s";

			$args = array(
				$email,
				$order_key,
				$product_id
			);

			if ( $download_id ) {
				// backwards compatibility for existing download URLs
				$query .= " AND download_id = %s";
				$args[] = $download_id;
			}

			$download_result = $wpdb->get_row( $wpdb->prepare( $query, $args ) );

			if ( ! $download_result ) {
				wp_die( __( 'Invalid download.', 'woocommerce' ) . ' <a href="' . esc_url( home_url() ) . '" class="wc-forward">' . __( 'Go to homepage', 'woocommerce' ) . '</a>' );
			}

			$download_id 			= $download_result->download_id;
			$order_id 				= $download_result->order_id;
			$downloads_remaining 	= $download_result->downloads_remaining;
			$download_count 		= $download_result->download_count;
			$user_id 				= $download_result->user_id;
			$access_expires 		= $download_result->access_expires;

			if ( $user_id && get_option( 'woocommerce_downloads_require_login' ) == 'yes' ) {

				if ( ! is_user_logged_in() ) {
					wp_die( __( 'You must be logged in to download files.', 'woocommerce' ) . ' <a href="' . esc_url( wp_login_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ) ) . '" class="wc-forward">' . __( 'Login', 'woocommerce' ) . '</a>', __( 'Log in to Download Files', 'woocommerce' ) );
				} elseif ( ! current_user_can( 'download_file', $download_result ) ) {
					wp_die( __( 'This is not your download link.', 'woocommerce' ) );
				}

			}

			if ( ! get_post( $product_id ) ) {
				wp_die( __( 'Product no longer exists.', 'woocommerce' ) . ' <a href="' . esc_url( home_url() ) . '" class="wc-forward">' . __( 'Go to homepage', 'woocommerce' ) . '</a>' );
			}

			if ( $order_id ) {
				$order = new WC_Order( $order_id );

				if ( ! $order->is_download_permitted() || $order->post_status != 'publish' ) {
					wp_die( __( 'Invalid order.', 'woocommerce' ) . ' <a href="' . esc_url( home_url() ) . '" class="wc-forward">' . __( 'Go to homepage', 'woocommerce' ) . '</a>' );
				}
			}

			if ( $downloads_remaining == '0' ) {
				wp_die( __( 'Sorry, you have reached your download limit for this file', 'woocommerce' ) . ' <a href="' . esc_url( home_url() ) . '" class="wc-forward">' . __( 'Go to homepage', 'woocommerce' ) . '</a>' );
			}

			if ( $access_expires > 0 && strtotime( $access_expires) < current_time( 'timestamp' ) ) {
				wp_die( __( 'Sorry, this download has expired', 'woocommerce' ) . ' <a href="' . esc_url( home_url() ) . '" class="wc-forward">' . __( 'Go to homepage', 'woocommerce' ) . '</a>' );
			}

			if ( $downloads_remaining > 0 ) {
				$wpdb->update( $wpdb->prefix . "woocommerce_downloadable_product_permissions", array(
					'downloads_remaining' => $downloads_remaining - 1,
				), array(
					'user_email' 	=> $email,
					'order_key' 	=> $order_key,
					'product_id' 	=> $product_id,
					'download_id' 	=> $download_id
				), array( '%d' ), array( '%s', '%s', '%d', '%s' ) );
			}

			// Count the download
			$wpdb->update( $wpdb->prefix . "woocommerce_downloadable_product_permissions", array(
				'download_count' => $download_count + 1,
			), array(
				'user_email' 	=> $email,
				'order_key' 	=> $order_key,
				'product_id' 	=> $product_id,
				'download_id' 	=> $download_id
			), array( '%d' ), array( '%s', '%s', '%d', '%s' ) );

			// Trigger action
			do_action( 'woocommerce_download_product', $email, $order_key, $product_id, $user_id, $download_id, $order_id );

			// Get the download URL and try to replace the url with a path
			$file_path = $_product->get_file_download_path( $download_id );

			// Download it!
			$this->download( $file_path, $product_id );
		}
	}

	/**
	 * Download a file - hook into init function.
	 */
	public function download( $file_path, $product_id ) {
		global $wpdb, $is_IE;

		$file_download_method = apply_filters( 'woocommerce_file_download_method', get_option( 'woocommerce_file_download_method' ), $product_id );

		if ( ! $file_path ) {
			wp_die( __( 'No file defined', 'woocommerce' ) . ' <a href="' . esc_url( home_url() ) . '" class="wc-forward">' . __( 'Go to homepage', 'woocommerce' ) . '</a>' );
		}

		// Redirect to the file...
		if ( $file_download_method == "redirect" ) {
			header( 'Location: ' . $file_path );
			exit;
		}

		// ...or serve it
		$remote_file      = true;
		$parsed_file_path = parse_url( $file_path );
		
		$wp_uploads       = wp_upload_dir();
		$wp_uploads_dir   = $wp_uploads['basedir'];
		$wp_uploads_url   = $wp_uploads['baseurl'];

		if ( ( ! isset( $parsed_file_path['scheme'] ) || ! in_array( $parsed_file_path['scheme'], array( 'http', 'https', 'ftp' ) ) ) && isset( $parsed_file_path['path'] ) && file_exists( $parsed_file_path['path'] ) ) {

			/** This is an absolute path */
			$remote_file  = false;

		} elseif( strpos( $file_path, $wp_uploads_url ) !== false ) {

			/** This is a local file given by URL so we need to figure out the path */
			$remote_file  = false;
			$file_path    = str_replace( $wp_uploads_url, $wp_uploads_dir, $file_path );

		} elseif( is_multisite() && ( strpos( $file_path, network_site_url( '/', 'http' ) ) !== false || strpos( $file_path, network_site_url( '/', 'https' ) ) !== false ) ) {

			/** This is a local file outside of wp-content so figure out the path */
			$remote_file = false;
			// Try to replace network url
            $file_path   = str_replace( network_site_url( '/', 'https' ), ABSPATH, $file_path );
            $file_path   = str_replace( network_site_url( '/', 'http' ), ABSPATH, $file_path );
            // Try to replace upload URL
            $file_path   = str_replace( $wp_uploads_url, $wp_uploads_dir, $file_path );

		} elseif( strpos( $file_path, site_url( '/', 'http' ) ) !== false || strpos( $file_path, site_url( '/', 'https' ) ) !== false ) {

			/** This is a local file outside of wp-content so figure out the path */
			$remote_file = false;
			$file_path   = str_replace( site_url( '/', 'https' ), ABSPATH, $file_path );
			$file_path   = str_replace( site_url( '/', 'http' ), ABSPATH, $file_path );

		} elseif ( file_exists( ABSPATH . $file_path ) ) {
			
			/** Path needs an abspath to work */
			$remote_file = false;
			$file_path   = ABSPATH . $file_path;
		}

		if ( ! $remote_file ) {
			// Remove Query String
			if ( strstr( $file_path, '?' ) ) {
				$file_path = current( explode( '?', $file_path ) );
			}

			// Run realpath
			$file_path = realpath( $file_path );
		}

		// Get extension and type
		$file_extension  = strtolower( substr( strrchr( $file_path, "." ), 1 ) );
		$ctype           = "application/force-download";

		foreach ( get_allowed_mime_types() as $mime => $type ) {
			$mimes = explode( '|', $mime );
			if ( in_array( $file_extension, $mimes ) ) {
				$ctype = $type;
				break;
			}
		}

		// Start setting headers
		if ( ! ini_get('safe_mode') ) {
			@set_time_limit(0);
		}

		if ( function_exists( 'get_magic_quotes_runtime' ) && get_magic_quotes_runtime() ) {
			@set_magic_quotes_runtime(0);
		}

		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 );
		}

		@session_write_close();
		@ini_set( 'zlib.output_compression', 'Off' );

		/**
		 * Prevents errors, for example: transfer closed with 3 bytes remaining to read
		 */
		@ob_end_clean(); // Clear the output buffer

		if ( ob_get_level() ) {

			$levels = ob_get_level();

			for ( $i = 0; $i < $levels; $i++ ) {
				@ob_end_clean(); // Zip corruption fix
			}

		}

		if ( $is_IE && is_ssl() ) {
			// IE bug prevents download via SSL when Cache Control and Pragma no-cache headers set.
			header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
			header( 'Cache-Control: private' );
		} else {
			nocache_headers();
		}

		$filename = basename( $file_path );

		if ( strstr( $filename, '?' ) ) {
			$filename = current( explode( '?', $filename ) );
		}

		$filename = apply_filters( 'woocommerce_file_download_filename', $filename, $product_id );

		header( "X-Robots-Tag: noindex, nofollow", true );
		header( "Content-Type: " . $ctype );
		header( "Content-Description: File Transfer" );
		header( "Content-Disposition: attachment; filename=\"" . $filename . "\";" );
		header( "Content-Transfer-Encoding: binary" );

        if ( $size = @filesize( $file_path ) ) {
        	header( "Content-Length: " . $size );
        }

		if ( $file_download_method == 'xsendfile' ) {

			// Path fix - kudos to Jason Judge
         	if ( getcwd() ) {
         		$file_path = trim( preg_replace( '`^' . str_replace( '\\', '/', getcwd() ) . '`' , '', $file_path ), '/' );
         	}

            header( "Content-Disposition: attachment; filename=\"" . $filename . "\";" );

            if ( function_exists( 'apache_get_modules' ) && in_array( 'mod_xsendfile', apache_get_modules() ) ) {

            	header("X-Sendfile: $file_path");
            	exit;

            } elseif ( stristr( getenv( 'SERVER_SOFTWARE' ), 'lighttpd' ) ) {

            	header( "X-Lighttpd-Sendfile: $file_path" );
            	exit;

            } elseif ( stristr( getenv( 'SERVER_SOFTWARE' ), 'nginx' ) || stristr( getenv( 'SERVER_SOFTWARE' ), 'cherokee' ) ) {

            	header( "X-Accel-Redirect: /$file_path" );
            	exit;

            }
        }

        if ( $remote_file ) {
        	$this->readfile_chunked( $file_path ) or header( 'Location: ' . $file_path );
        } else {
        	$this->readfile_chunked( $file_path ) or wp_die( __( 'File not found', 'woocommerce' ) . ' <a href="' . esc_url( home_url() ) . '" class="wc-forward">' . __( 'Go to homepage', 'woocommerce' ) . '</a>' );
        }

        exit;
	}

	/**
	 * readfile_chunked
	 * Reads file in chunks so big downloads are possible without changing PHP.INI - http://codeigniter.com/wiki/Download_helper_for_large_files/
	 * @param    string $file
	 * @param    bool   $retbytes return bytes of file
	 * @return bool|int
	 * @todo Meaning of the return value? Last return is status of fclose?
	 */
	public static function readfile_chunked( $file, $retbytes = true ) {

		$chunksize = 1 * ( 1024 * 1024 );
		$buffer = '';
		$cnt = 0;

		$handle = @fopen( $file, 'r' );
		if ( $handle === FALSE ) {
			return FALSE;
		}

		while ( ! feof( $handle ) ) {
			$buffer = fread( $handle, $chunksize );
			echo $buffer;
			@ob_flush();
			@flush();

			if ( $retbytes ) {
				$cnt += strlen( $buffer );
			}
		}

		$status = fclose( $handle );

		if ( $retbytes && $status ) {
			return $cnt;
		}

		return $status;
	}
}

new WC_Download_Handler();
