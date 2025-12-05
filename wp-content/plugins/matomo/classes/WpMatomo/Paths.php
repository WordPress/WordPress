<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo;

use stdClass;
use WP_Filesystem_Direct;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Paths {

	private static $host_init_filesystem           = false;
	private static $host_init_filesystem_succeeded = false;

	public function get_file_system() {
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
		}

		if ( ! self::$host_init_filesystem ) {
			self::$host_init_filesystem           = true;
			self::$host_init_filesystem_succeeded = WP_Filesystem();
		}

		if ( ! class_exists( '\WP_Filesystem_Direct' ) ) {
			require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php';
			require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php';
		}

		return new WP_Filesystem_Direct( new stdClass() );
	}

	public function get_host_init_filesystem_succeeded() {
		return self::$host_init_filesystem_succeeded;
	}

	public function get_upload_base_url() {
		$upload_dir      = wp_upload_dir();
		$path_upload_url = $upload_dir['baseurl'];

		return rtrim( $path_upload_url, '/' ) . '/' . MATOMO_UPLOAD_DIR;
	}

	public function get_upload_base_dir() {
		$upload_dir      = wp_upload_dir();
		$path_upload_dir = $upload_dir['basedir'];
		$path_upload_dir = rtrim( $path_upload_dir, '/' ) . '/' . MATOMO_UPLOAD_DIR;

		return $path_upload_dir;
	}

	public function get_matomo_js_upload_path() {
		return $this->get_upload_base_dir() . '/' . MATOMO_JS_NAME;
	}

	public function get_config_ini_path() {
		return $this->get_upload_base_dir() . '/' . MATOMO_CONFIG_PATH;
	}

	public function get_tracker_api_rest_api_endpoint() {
		return path_join( get_rest_url(), API::VERSION . '/' . API::ROUTE_HIT . '/' );
	}

	public function get_tracker_api_url_in_matomo_dir() {
		return plugins_url( 'app/matomo.php', MATOMO_ANALYTICS_FILE );
	}

	public function get_js_tracker_rest_api_endpoint() {
		return $this->get_tracker_api_rest_api_endpoint();
	}

	public function get_js_tracker_url_in_matomo_dir() {
		$paths = new Paths();

		if ( file_exists( $paths->get_matomo_js_upload_path() ) ) {
			return $this->get_upload_base_url() . '/' . MATOMO_JS_NAME;
		}

		return plugins_url( 'app/matomo.js', MATOMO_ANALYTICS_FILE );
	}

	public function get_tmp_dir() {
		$is_multi_site = function_exists( 'is_multisite' ) && is_multisite();

		$cache_dir_alternative = $this->get_upload_base_dir() . '/tmp';
		$base_cache_dir        = WP_CONTENT_DIR . '/cache';
		$default_cache_dir     = $base_cache_dir . '/' . MATOMO_UPLOAD_DIR;

		if ( ! $is_multi_site &&
			 ( ( is_writable( WP_CONTENT_DIR ) && ! is_dir( $base_cache_dir ) )
			   || is_writable( $base_cache_dir ) ) ) {
			// we prefer wp-content/cache
			$cache_dir = $default_cache_dir;

			if ( ! is_dir( $cache_dir ) ) {
				wp_mkdir_p( $cache_dir );
			}

			if ( ! is_writable( $cache_dir ) ) {
				// wasn't made writable for some reason so we prefer to use the upload dir just to be safe
				$cache_dir = $cache_dir_alternative;
			}
		} else {
			// fallback wp-content/uploads/matomo/tmp if $defaultCacheDir is not writable or if multisite is used
			// with multisite we need to make sure to cache files per site
			$cache_dir = $cache_dir_alternative;

			if ( ! is_dir( $cache_dir ) ) {
				wp_mkdir_p( $cache_dir );
			}
		}

		return $cache_dir;
	}

	/**
	 * parameter matomo_file is required for the unit test cases (when checking with a path including the string matomo)
	 *
	 * @param string $target_dir
	 * @param string $matomo_file
	 *
	 * @return string
	 */
	public function get_relative_dir_to_matomo( $target_dir, $matomo_file = MATOMO_ANALYTICS_FILE ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		return matomo_rel_path( $target_dir, dirname( $matomo_file ) . '/app' );
	}

	public function get_gloal_upload_dir_if_possible( $file_to_look_for = '' ) {
		if ( defined( 'MATOMO_GLOBAL_UPLOAD_DIR' ) ) {
			return MATOMO_GLOBAL_UPLOAD_DIR;
		}

		$path_upload_dir = $this->get_upload_base_dir();

		if ( ! is_multisite() || is_network_admin() ) {
			return $path_upload_dir;
		}

		if ( preg_match( '/sites\/(\d)+$/', $path_upload_dir ) ) {
			$path_upload_dir = preg_replace( '/sites\/(\d)+$/', '', $path_upload_dir );
		} else {
			// re-implement _wp_upload_dir to find hopefully the upload_dir for the network site
			$upload_path = trim( get_option( 'upload_path' ) );
			if ( empty( $upload_path ) || 'wp-content/uploads' === $upload_path ) {
				$path_upload_dir = WP_CONTENT_DIR . '/uploads';
			} elseif ( 0 !== strpos( $upload_path, ABSPATH ) ) {
				// $dir is absolute, $upload_path is (maybe) relative to ABSPATH
				$path_upload_dir = path_join( ABSPATH, $upload_path );
			} else {
				$path_upload_dir = $upload_path;
			}
		}

		if ( ! empty( $file_to_look_for ) ) {
			$file_to_look_for = MATOMO_UPLOAD_DIR . '/' . ltrim( $file_to_look_for, '/' );
		}

		$path_upload_dir = rtrim( $path_upload_dir, '/' ) . '/';
		if ( ! empty( $file_to_look_for )
			 && ! file_exists( $path_upload_dir . $file_to_look_for ) ) {
			// seems we haven't auto detected the right one yet... (or it is not yet installed)
			// we go up the site upload dir step by step to try and find the network upload dir
			$parent_dir = $path_upload_dir;
			do {
				$parent_dir = dirname( $parent_dir );
				if ( file_exists( $parent_dir . $file_to_look_for ) ) {
					return $parent_dir;
				}
			} while ( strpos( $parent_dir, ABSPATH ) === 0 ); // we don't go outside WP dir
		}

		$path_upload_dir = rtrim( $path_upload_dir, '/' ) . '/' . MATOMO_UPLOAD_DIR;

		return $path_upload_dir;
	}

	public function clear_assets_dir() {
		$tmp_dir = $this->get_tmp_dir() . '/assets';
		if ( $tmp_dir && is_dir( $tmp_dir ) ) {
			$file_system_direct = $this->get_file_system();
			$file_system_direct->rmdir( $tmp_dir, true );
		}
	}

	public function clear_cache_dir() {
		$tmp_dir = $this->get_tmp_dir();
		if ( $tmp_dir
			 && is_dir( $tmp_dir )
			 && is_dir( $tmp_dir . '/cache' ) ) {
			// we make sure it's a matomo cache dir to not delete something falsely
			$file_system_direct = $this->get_file_system();
			$file_system_direct->rmdir( $tmp_dir, true );
		}
	}

	public function uninstall() {
		$this->clear_cache_dir();

		$dir = $this->get_upload_base_dir();

		$file_system_direct = $this->get_file_system();
		$file_system_direct->rmdir( $dir, true );

		$global_dir = $this->get_upload_base_dir();
		if ( $global_dir && $global_dir !== $dir ) {
			$file_system_direct->rmdir( $dir );
		}
	}

	/**
	 * Must be called after Paths::get_file_system().
	 *
	 * @return bool
	 */
	public function is_upload_dir_writable() {
		$upload_dir = $this->get_upload_base_dir();

		// the WP direct filesystem abstraction may not be available based on user
		// configuration. in this case, we can't write to the upload dir using WP_Filesystem
		$is_filesystem_direct_available = defined( 'FS_CHMOD_FILE' );
		return is_dir( $upload_dir )
			&& is_writable( $upload_dir )
			&& $this->get_host_init_filesystem_succeeded()
			&& $is_filesystem_direct_available;
	}
}
