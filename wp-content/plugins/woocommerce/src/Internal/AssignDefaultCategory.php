<?php
/**
 * AssignDefaultCategory class file.
 */

namespace Automattic\WooCommerce\Internal;

defined( 'ABSPATH' ) || exit;

/**
 * Class to assign default category to products.
 */
class AssignDefaultCategory {
	/**
	 * Class initialization, to be executed when the class is resolved by the container.
	 *
	 * @internal
	 */
	final public function init() {
		add_action( 'wc_schedule_update_product_default_cat', array( $this, 'maybe_assign_default_product_cat' ) );
	}

	/**
	 * When a product category is deleted, we need to check
	 * if the product has no categories assigned. Then assign
	 * it a default category. We delay this with a scheduled
	 * action job to not block the response.
	 *
	 * @return void
	 */
	public function schedule_action() {
		WC()->queue()->schedule_single(
			time(),
			'wc_schedule_update_product_default_cat',
			array(),
			'wc_update_product_default_cat'
		);
	}

	/**
	 * Assigns default product category for products
	 * that have no categories.
	 *
	 * @return void
	 */
	public function maybe_assign_default_product_cat() {
		global $wpdb;

		$default_category = get_option( 'default_product_cat', 0 );

		if ( $default_category ) {
			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO {$wpdb->term_relationships} (object_id, term_taxonomy_id)
					SELECT DISTINCT posts.ID, %s FROM {$wpdb->posts} posts
					LEFT JOIN
						(
							SELECT object_id FROM {$wpdb->term_relationships} term_relationships
							LEFT JOIN {$wpdb->term_taxonomy} term_taxonomy ON term_relationships.term_taxonomy_id = term_taxonomy.term_taxonomy_id
							WHERE term_taxonomy.taxonomy = 'product_cat'
						) AS tax_query
					ON posts.ID = tax_query.object_id
					WHERE posts.post_type = 'product'
					AND tax_query.object_id IS NULL",
					$default_category
				)
			);
			wp_cache_flush();
			delete_transient( 'wc_term_counts' );
			wp_update_term_count_now( array( $default_category ), 'product_cat' );
		}
	}
}
