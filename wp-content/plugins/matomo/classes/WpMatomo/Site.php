<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Site {
	const SITE_MAPPING_PREFIX = 'matomo-site-id-';

	/**
	 * @api
	 */
	public function get_current_matomo_site_id() {
		return self::get_matomo_site_id( get_current_blog_id() );
	}

	public static function get_matomo_site_id( $blog_id ) {
		return (int) get_site_option( self::SITE_MAPPING_PREFIX . $blog_id );
	}

	public static function map_matomo_site_id( $blog_id, $matomo_id_site ) {
		$key = self::SITE_MAPPING_PREFIX . $blog_id;

		if ( null === $matomo_id_site || false === $matomo_id_site ) {
			delete_site_option( $key );
		} else {
			update_site_option( $key, $matomo_id_site );
		}
	}

	public function uninstall() {
		Uninstaller::uninstall_site_meta( self::SITE_MAPPING_PREFIX );
	}
}
