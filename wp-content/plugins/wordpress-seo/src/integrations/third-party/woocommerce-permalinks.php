<?php

namespace Yoast\WP\SEO\Integrations\Third_Party;

use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Conditionals\WooCommerce_Conditional;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * The permalink watcher.
 */
class Woocommerce_Permalinks implements Integration_Interface {

	/**
	 * Represents the indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	protected $indexable_helper;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ WooCommerce_Conditional::class, Migrations_Conditional::class ];
	}

	/**
	 * Constructor.
	 *
	 * @param Indexable_Helper $indexable_helper Indexable Helper.
	 */
	public function __construct( Indexable_Helper $indexable_helper ) {
		$this->indexable_helper = $indexable_helper;
	}

	/**
	 * Registers the hooks.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'wpseo_post_types_reset_permalinks', [ $this, 'filter_product_from_post_types' ] );
		\add_action( 'update_option_woocommerce_permalinks', [ $this, 'reset_woocommerce_permalinks' ], 10, 2 );
	}

	/**
	 * Filters the product post type from the post type.
	 *
	 * @param array $post_types The post types to filter.
	 *
	 * @return array The filtered post types.
	 */
	public function filter_product_from_post_types( $post_types ) {
		unset( $post_types['product'] );

		return $post_types;
	}

	/**
	 * Resets the indexables for WooCommerce based on the changed permalink fields.
	 *
	 * @param array $old_value The old value.
	 * @param array $new_value The new value.
	 *
	 * @return void
	 */
	public function reset_woocommerce_permalinks( $old_value, $new_value ) {
		$changed_options = \array_diff( $old_value, $new_value );

		if ( \array_key_exists( 'product_base', $changed_options ) ) {
			$this->indexable_helper->reset_permalink_indexables( 'post', 'product' );
		}

		if ( \array_key_exists( 'attribute_base', $changed_options ) ) {
			$attribute_taxonomies = $this->get_attribute_taxonomies();

			foreach ( $attribute_taxonomies as $attribute_name ) {
				$this->indexable_helper->reset_permalink_indexables( 'term', $attribute_name );
			}
		}

		if ( \array_key_exists( 'category_base', $changed_options ) ) {
			$this->indexable_helper->reset_permalink_indexables( 'term', 'product_cat' );
		}

		if ( \array_key_exists( 'tag_base', $changed_options ) ) {
			$this->indexable_helper->reset_permalink_indexables( 'term', 'product_tag' );
		}
	}

	/**
	 * Retrieves the taxonomies based on the attributes.
	 *
	 * @return array The taxonomies.
	 */
	protected function get_attribute_taxonomies() {
		$taxonomies = [];
		foreach ( \wc_get_attribute_taxonomies() as $attribute_taxonomy ) {
			$taxonomies[] = \wc_attribute_taxonomy_name( $attribute_taxonomy->attribute_name );
		}

		$taxonomies = \array_filter( $taxonomies );

		return $taxonomies;
	}
}
