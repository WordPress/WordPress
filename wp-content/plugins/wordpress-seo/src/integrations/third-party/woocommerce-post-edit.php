<?php

namespace Yoast\WP\SEO\Integrations\Third_Party;

use WP_Post;
use Yoast\WP\SEO\Conditionals\Admin\Post_Conditional;
use Yoast\WP\SEO\Conditionals\WooCommerce_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * A WooCommerce integration that runs in the post editor.
 */
class WooCommerce_Post_Edit implements Integration_Interface {

	/**
	 * Register the hooks for this integration to work.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'wpseo_post_edit_values', [ $this, 'remove_meta_description_date' ], 10, 2 );
	}

	/**
	 * Only run this integration when WooCommerce is active and the user is in the post editor.
	 *
	 * @return string[] The conditionals that should be met before this integration is loaded.
	 */
	public static function get_conditionals() {
		return [ WooCommerce_Conditional::class, Post_Conditional::class ];
	}

	/**
	 * Don't show the date in the Google preview for WooCommerce products,
	 * since Google does not show dates for product pages in the search results.
	 *
	 * @param array   $values Key-value map of variables we enqueue in the JavaScript of the post editor.
	 * @param WP_Post $post   The post currently opened in the editor.
	 *
	 * @return array The values, where the `metaDescriptionDate` is set to the empty string.
	 */
	public function remove_meta_description_date( $values, $post ) {
		if ( $post->post_type === 'product' ) {
			$values['metaDescriptionDate'] = '';
		}

		return $values;
	}
}
