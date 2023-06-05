<?php
/**
 * Represents a file which can be downloaded.
 *
 * @package WooCommerce\Classes
 * @version 3.0.0
 * @since   3.0.0
 */

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Register as Download_Directories;
use Automattic\WooCommerce\Internal\Utilities\URL;

defined( 'ABSPATH' ) || exit;

/**
 * Product download class.
 */
class WC_Product_Download implements ArrayAccess {

	/**
	 * Data array.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $data = array(
		'id'      => '',
		'name'    => '',
		'file'    => '',
		'enabled' => true,
	);

	/**
	 * Returns all data for this object.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Get allowed mime types.
	 *
	 * @return array
	 */
	public function get_allowed_mime_types() {
		return apply_filters( 'woocommerce_downloadable_file_allowed_mime_types', get_allowed_mime_types() );
	}

	/**
	 * Get type of file path set.
	 *
	 * @param  string $file_path optional.
	 * @return string absolute, relative, or shortcode.
	 */
	public function get_type_of_file_path( $file_path = '' ) {
		$file_path  = $file_path ? $file_path : $this->get_file();
		$parsed_url = wp_parse_url( $file_path );
		if (
			$parsed_url &&
			isset( $parsed_url['host'] ) && // Absolute url means that it has a host.
			( // Theoretically we could permit any scheme (like ftp as well), but that has not been the case before. So we allow none or http(s).
				! isset( $parsed_url['scheme'] ) ||
				in_array( $parsed_url['scheme'], array( 'http', 'https' ), true )
			)
		) {
			return 'absolute';
		} elseif ( '[' === substr( $file_path, 0, 1 ) && ']' === substr( $file_path, -1 ) ) {
			return 'shortcode';
		} else {
			return 'relative';
		}
	}

	/**
	 * Get file type.
	 *
	 * @return string
	 */
	public function get_file_type() {
		$type = wp_check_filetype( strtok( $this->get_file(), '?' ), $this->get_allowed_mime_types() );
		return $type['type'];
	}

	/**
	 * Get file extension.
	 *
	 * @return string
	 */
	public function get_file_extension() {
		$parsed_url = wp_parse_url( $this->get_file(), PHP_URL_PATH );
		return pathinfo( $parsed_url, PATHINFO_EXTENSION );
	}

	/**
	 * Confirms that the download is of an allowed filetype, that it exists and that it is
	 * contained within an approved directory. Used before adding to a product's list of
	 * downloads.
	 *
	 * @internal
	 * @throws Exception If the download is determined to be invalid.
	 *
	 * @param bool $auto_add_to_approved_directory_list If the download is not already in the approved directory list, automatically add it if possible.
	 */
	public function check_is_valid( bool $auto_add_to_approved_directory_list = true ) {
		$download_file = $this->get_file();

		if ( ! $this->data['enabled'] ) {
			throw new Exception(
				sprintf(
					/* translators: %s: Downloadable file. */
					__( 'The downloadable file %s cannot be used as it has been disabled.', 'woocommerce' ),
					'<code>' . basename( $download_file ) . '</code>'
				)
			);
		}

		if ( ! $this->is_allowed_filetype() ) {
			throw new Exception(
				sprintf(
					/* translators: 1: Downloadable file, 2: List of allowed filetypes. */
					__( 'The downloadable file %1$s cannot be used as it does not have an allowed file type. Allowed types include: %2$s', 'woocommerce' ),
					'<code>' . basename( $download_file ) . '</code>',
					'<code>' . implode( ', ', array_keys( $this->get_allowed_mime_types() ) ) . '</code>'
				)
			);
		}

		// Validate the file exists.
		if ( ! $this->file_exists() ) {
			throw new Exception(
				sprintf(
					/* translators: %s: Downloadable file */
					__( 'The downloadable file %s cannot be used as it does not exist on the server.', 'woocommerce' ),
					'<code>' . $download_file . '</code>'
				)
			);
		}

		$this->approved_directory_checks( $auto_add_to_approved_directory_list );
	}

	/**
	 * Check if file is allowed.
	 *
	 * @return boolean
	 */
	public function is_allowed_filetype() {
		$file_path = $this->get_file();

		// File types for URL-based files located on the server should get validated.
		$parsed_file_path  = WC_Download_Handler::parse_file_path( $file_path );
		$is_file_on_server = ! $parsed_file_path['remote_file'];
		$file_path_type    = $this->get_type_of_file_path( $file_path );

		// Shortcodes are allowed, validations should be done by the shortcode provider in this case.
		if ( 'shortcode' === $file_path_type ) {
			return true;
		}

		// Remote paths are allowed.
		if ( ! $is_file_on_server && 'relative' !== $file_path_type ) {
			return true;
		}

		// On windows system, local files ending with `.` are not allowed.
		// @link https://docs.microsoft.com/en-us/windows/win32/fileio/naming-a-file?redirectedfrom=MSDN#naming-conventions.
		if ( $is_file_on_server && ! $this->get_file_extension() && 'WIN' === strtoupper( substr( Constants::get_constant( 'PHP_OS' ), 0, 3 ) ) ) {
			if ( '.' === substr( $file_path, -1 ) ) {
				return false;
			}
		}

		return ! $this->get_file_extension() || in_array( $this->get_file_type(), $this->get_allowed_mime_types(), true );
	}

	/**
	 * Validate file exists.
	 *
	 * @return boolean
	 */
	public function file_exists() {
		if ( 'relative' !== $this->get_type_of_file_path() ) {
			return true;
		}
		$file_url = $this->get_file();
		if ( '..' === substr( $file_url, 0, 2 ) || '/' !== substr( $file_url, 0, 1 ) ) {
			$file_url = realpath( ABSPATH . $file_url );
		} elseif ( substr( WP_CONTENT_DIR, strlen( untrailingslashit( ABSPATH ) ) ) === substr( $file_url, 0, strlen( substr( WP_CONTENT_DIR, strlen( untrailingslashit( ABSPATH ) ) ) ) ) ) {
			$file_url = realpath( WP_CONTENT_DIR . substr( $file_url, 11 ) );
		}
		return apply_filters( 'woocommerce_downloadable_file_exists', file_exists( $file_url ), $this->get_file() );
	}

	/**
	 * Confirms that the download exists within an approved directory.
	 *
	 * If it is not within an approved directory but the current user has sufficient
	 * capabilities, then the method will try to add the download's directory to the
	 * approved directory list.
	 *
	 * @throws Exception If the download is not in an approved directory.
	 *
	 * @param bool $auto_add_to_approved_directory_list If the download is not already in the approved directory list, automatically add it if possible.
	 */
	private function approved_directory_checks( bool $auto_add_to_approved_directory_list = true ) {
		$download_directories = wc_get_container()->get( Download_Directories::class );

		if ( $download_directories->get_mode() !== Download_Directories::MODE_ENABLED ) {
			return;
		}

		$download_file = $this->get_file();

		/**
		 * Controls whether shortcodes should be resolved and validated using the Approved Download Directory feature.
		 *
		 * @param bool $should_validate
		 */
		if ( apply_filters( 'woocommerce_product_downloads_approved_directory_validation_for_shortcodes', true ) && 'shortcode' === $this->get_type_of_file_path() ) {
			$download_file = do_shortcode( $download_file );
		}

		$is_site_administrator   = is_multisite() ? current_user_can( 'manage_sites' ) : current_user_can( 'manage_options' );
		$valid_storage_directory = $download_directories->is_valid_path( $download_file );

		if ( $valid_storage_directory ) {
			return;
		}

		if ( $auto_add_to_approved_directory_list ) {
			try {
				// Add the parent URL to the approved directories list, but *do not enable it* unless the current user is a site admin.
				$download_directories->add_approved_directory( ( new URL( $download_file ) )->get_parent_url(), $is_site_administrator );
				$valid_storage_directory = $download_directories->is_valid_path( $download_file );
			} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
				// At this point, $valid_storage_directory will be false. Fall-through so the appropriate exception is
				// triggered (same as if the storage directory was invalid and $auto_add_to_approved_directory_list was false.
			}
		}

		if ( ! $valid_storage_directory ) {
			throw new Exception(
				sprintf(
					/* translators: %1$s is the downloadable file path, %2$s is an opening link tag, %3%s is a closing link tag. */
					__( 'The downloadable file %1$s cannot be used: it is not located in an approved directory. Please contact a site administrator for help. %2$sLearn more.%3$s', 'woocommerce' ),
					'<code>' . $download_file . '</code>',
					'<a href="https://woocommerce.com/document/approved-download-directories">',
					'</a>'
				)
			);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set ID.
	 *
	 * @param string $value Download ID.
	 */
	public function set_id( $value ) {
		$this->data['id'] = wc_clean( $value );
	}

	/**
	 * Set name.
	 *
	 * @param string $value Download name.
	 */
	public function set_name( $value ) {
		$this->data['name'] = wc_clean( $value );
	}

	/**
	 * Set previous_hash.
	 *
	 * @deprecated 3.3.0 No longer using filename based hashing to keep track of files.
	 * @param string $value Previous hash.
	 */
	public function set_previous_hash( $value ) {
		wc_deprecated_function( __FUNCTION__, '3.3' );
		$this->data['previous_hash'] = wc_clean( $value );
	}

	/**
	 * Set file.
	 *
	 * @param string $value File URL/Path.
	 */
	public function set_file( $value ) {
		// A `///` is recognized as an "absolute", but on the filesystem, so it bypasses the mime check in `self::is_allowed_filetype`.
		// This will strip extra prepending / to the maximum of 2.
		if ( preg_match( '#^//+(/[^/].+)$#i', $value, $matches ) ) {
			$value = $matches[1];
		}
		switch ( $this->get_type_of_file_path( $value ) ) {
			case 'absolute':
				$this->data['file'] = esc_url_raw( $value );
				break;
			default:
				$this->data['file'] = wc_clean( $value );
				break;
		}
	}

	/**
	 * Sets the status of the download to enabled (true) or disabled (false).
	 *
	 * @param bool $enabled True indicates the downloadable file is enabled, false indicates it is disabled.
	 */
	public function set_enabled( bool $enabled = true ) {
		$this->data['enabled'] = $enabled;
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get id.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->data['id'];
	}

	/**
	 * Get name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->data['name'];
	}

	/**
	 * Get previous_hash.
	 *
	 * @deprecated 3.3.0 No longer using filename based hashing to keep track of files.
	 * @return string
	 */
	public function get_previous_hash() {
		wc_deprecated_function( __FUNCTION__, '3.3' );
		return $this->data['previous_hash'];
	}

	/**
	 * Get file.
	 *
	 * @return string
	 */
	public function get_file() {
		return $this->data['file'];
	}

	/**
	 * Get status of the download.
	 *
	 * @return bool
	 */
	public function get_enabled(): bool {
		return $this->data['enabled'];
	}

	/*
	|--------------------------------------------------------------------------
	| ArrayAccess/Backwards compatibility.
	|--------------------------------------------------------------------------
	*/

	/**
	 * OffsetGet.
	 *
	 * @param string $offset Offset.
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		switch ( $offset ) {
			default:
				if ( is_callable( array( $this, "get_$offset" ) ) ) {
					return $this->{"get_$offset"}();
				}
				break;
		}
		return '';
	}

	/**
	 * OffsetSet.
	 *
	 * @param string $offset Offset.
	 * @param mixed  $value Offset value.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		switch ( $offset ) {
			default:
				if ( is_callable( array( $this, "set_$offset" ) ) ) {
					$this->{"set_$offset"}( $value );
				}
				break;
		}
	}

	/**
	 * OffsetUnset.
	 *
	 * @param string $offset Offset.
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $offset ) {}

	/**
	 * OffsetExists.
	 *
	 * @param string $offset Offset.
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		return in_array( $offset, array_keys( $this->data ), true );
	}
}
