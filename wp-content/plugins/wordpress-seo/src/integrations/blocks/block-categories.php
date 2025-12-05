<?php

namespace Yoast\WP\SEO\Integrations\Blocks;

use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Internal_Linking_Category block class.
 */
class Internal_Linking_Category implements Integration_Interface {

	/**
	 * {@inheritDoc}
	 */
	public static function get_conditionals() {
		return [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function register_hooks() {
		\add_filter( 'block_categories_all', [ $this, 'add_block_categories' ] );
	}

	/**
	 * Adds Yoast block categories.
	 *
	 * @param array $categories The categories.
	 * @return array The filtered categories.
	 */
	public function add_block_categories( $categories ) {
		$categories[] = [
			'slug'  => 'yoast-structured-data-blocks',
			'title' => \sprintf(
				/* translators: %1$s expands to Yoast. */
				\__( '%1$s Structured Data Blocks', 'wordpress-seo' ),
				'Yoast'
			),
		];
		$categories[] = [
			'slug'  => 'yoast-internal-linking-blocks',
			'title' => \sprintf(
				/* translators: %1$s expands to Yoast. */
				\__( '%1$s Internal Linking Blocks', 'wordpress-seo' ),
				'Yoast'
			),
		];

		return $categories;
	}
}
