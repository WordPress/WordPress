<?php
/**
 * WooCommerce Navigation Favorite
 *
 * @package Woocommerce Navigation
 */

namespace Automattic\WooCommerce\Admin\Features\Navigation;

use Automattic\WooCommerce\Internal\Admin\WCAdminUser;

/**
 * Contains logic for the WooCommerce Navigation menu.
 */
class Favorites {

	/**
	 * Array index of menu capability.
	 *
	 * @var int
	 */
	const META_NAME = 'navigation_favorites';

	/**
	 * Get class instance.
	 */
	final public static function instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Set given favorites string to the user meta data.
	 *
	 * @param string|number $user_id User id.
	 * @param array         $favorites Array of favorite values to set.
	 */
	private static function set_meta_value( $user_id, $favorites ) {
		WCAdminUser::update_user_data_field( $user_id, self::META_NAME, wp_json_encode( (array) $favorites ) );
	}

	/**
	 * Add item to favorites
	 *
	 * @param string        $item_id Identifier of item to add.
	 * @param string|number $user_id Identifier of user to add to.
	 * @return WP_Error|Boolean   Throws exception if item already exists.
	 */
	public static function add_item( $item_id, $user_id ) {

		$all_favorites = self::get_all( $user_id );

		if ( in_array( $item_id, $all_favorites, true ) ) {
			return new \WP_Error(
				'woocommerce_favorites_already_exists',
				__( 'Favorite already exists', 'woocommerce' )
			);
		}

		$all_favorites[] = $item_id;

		self::set_meta_value( $user_id, $all_favorites );

		return true;
	}

	/**
	 * Remove item from favorites
	 *
	 * @param string        $item_id Identifier of item to remove.
	 * @param string|number $user_id Identifier of user to remove from.
	 * @return \WP_Error|Boolean   Throws exception if item does not exist.
	 */
	public static function remove_item( $item_id, $user_id ) {
		$all_favorites = self::get_all( $user_id );

		if ( ! in_array( $item_id, $all_favorites, true ) ) {
			return new \WP_Error(
				'woocommerce_favorites_does_not_exist',
				__( 'Favorite item not found', 'woocommerce' )
			);
		}

		$remaining = array_values( array_diff( $all_favorites, [ $item_id ] ) );

		self::set_meta_value( $user_id, $remaining );

		return true;
	}

	/**
	 * Get all registered favorites.
	 *
	 * @param string|number $user_id Identifier of user to query.
	 * @return WP_Error|Array
	 */
	public static function get_all( $user_id ) {
		$response = WCAdminUser::get_user_data_field( $user_id, self::META_NAME );

		return $response ? json_decode( $response, true ) : array();
	}

}
