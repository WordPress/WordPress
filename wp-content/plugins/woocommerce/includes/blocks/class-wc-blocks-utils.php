<?php
/**
 * Blocks Utils
 *
 * Used by core components that need to work with blocks.
 *
 * @package WooCommerce\Blocks\Utils
 * @version 5.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Blocks Utility class.
 */
class WC_Blocks_Utils {

	/**
	 * Get blocks from a woocommerce page.
	 *
	 * @param string $woo_page_name A woocommerce page e.g. `checkout` or `cart`.
	 * @return array Array of blocks as returned by parse_blocks().
	 */
	private static function get_all_blocks_from_page( $woo_page_name ) {
		$page_id = wc_get_page_id( $woo_page_name );

		$page = get_post( $page_id );
		if ( ! $page ) {
			return array();
		}

		$blocks = parse_blocks( $page->post_content );
		if ( ! $blocks ) {
			return array();
		}

		return $blocks;
	}

	/**
	 * Get all instances of the specified block on a specific woo page
	 * (e.g. `cart` or `checkout` page).
	 *
	 * @param string $block_name The name (id) of a block, e.g. `woocommerce/cart`.
	 * @param string $woo_page_name The woo page to search, e.g. `cart`.
	 * @return array Array of blocks as returned by parse_blocks().
	 */
	public static function get_blocks_from_page( $block_name, $woo_page_name ) {
		$page_blocks = self::get_all_blocks_from_page( $woo_page_name );

		// Get any instances of the specified block.
		return array_values(
			array_filter(
				$page_blocks,
				function ( $block ) use ( $block_name ) {
					return ( $block_name === $block['blockName'] );
				}
			)
		);
	}

	/**
	 * Check if a given page contains a particular block.
	 *
	 * @param int|WP_Post $page Page post ID or post object.
	 * @param string      $block_name The name (id) of a block, e.g. `woocommerce/cart`.
	 * @return bool Boolean value if the page contains the block or not. Null in case the page does not exist.
	 */
	public static function has_block_in_page( $page, $block_name ) {
		$page_to_check = get_post( $page );
		if ( null === $page_to_check ) {
			return false;
		}

		$blocks = parse_blocks( $page_to_check->post_content );
		foreach ( $blocks as $block ) {
			if ( $block_name === $block['blockName'] ) {
				return true;
			}
		}

		return false;
	}
}
