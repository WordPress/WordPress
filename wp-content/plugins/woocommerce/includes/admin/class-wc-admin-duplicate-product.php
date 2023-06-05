<?php
/**
 * Duplicate product functionality
 *
 * @package     WooCommerce\Admin
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Admin_Duplicate_Product', false ) ) {
	return new WC_Admin_Duplicate_Product();
}

/**
 * WC_Admin_Duplicate_Product Class.
 */
class WC_Admin_Duplicate_Product {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_action_duplicate_product', array( $this, 'duplicate_product_action' ) );
		add_filter( 'post_row_actions', array( $this, 'dupe_link' ), 10, 2 );
		add_action( 'post_submitbox_start', array( $this, 'dupe_button' ) );
	}

	/**
	 * Show the "Duplicate" link in admin products list.
	 *
	 * @param array   $actions Array of actions.
	 * @param WP_Post $post Post object.
	 * @return array
	 */
	public function dupe_link( $actions, $post ) {
		global $the_product;

		if ( ! current_user_can( apply_filters( 'woocommerce_duplicate_product_capability', 'manage_woocommerce' ) ) ) {
			return $actions;
		}

		if ( 'product' !== $post->post_type ) {
			return $actions;
		}

		// Add Class to Delete Permanently link in row actions.
		if ( empty( $the_product ) || $the_product->get_id() !== $post->ID ) {
			$the_product = wc_get_product( $post );
		}

		if ( 'publish' === $post->post_status && $the_product && 0 < $the_product->get_total_sales() ) {
			$actions['trash'] = sprintf(
				'<a href="%s" class="submitdelete trash-product" aria-label="%s">%s</a>',
				get_delete_post_link( $the_product->get_id(), '', false ),
				/* translators: %s: post title */
				esc_attr( sprintf( __( 'Move &#8220;%s&#8221; to the Trash', 'woocommerce' ), $the_product->get_name() ) ),
				esc_html__( 'Trash', 'woocommerce' )
			);
		}

		$actions['duplicate'] = '<a href="' . wp_nonce_url( admin_url( 'edit.php?post_type=product&action=duplicate_product&amp;post=' . $post->ID ), 'woocommerce-duplicate-product_' . $post->ID ) . '" aria-label="' . esc_attr__( 'Make a duplicate from this product', 'woocommerce' )
			. '" rel="permalink">' . esc_html__( 'Duplicate', 'woocommerce' ) . '</a>';

		return $actions;
	}

	/**
	 * Show the dupe product link in admin.
	 */
	public function dupe_button() {
		global $post;

		if ( ! current_user_can( apply_filters( 'woocommerce_duplicate_product_capability', 'manage_woocommerce' ) ) ) {
			return;
		}

		if ( ! is_object( $post ) ) {
			return;
		}

		if ( 'product' !== $post->post_type ) {
			return;
		}

		$notify_url = wp_nonce_url( admin_url( 'edit.php?post_type=product&action=duplicate_product&post=' . absint( $post->ID ) ), 'woocommerce-duplicate-product_' . $post->ID );
		?>
		<div id="duplicate-action"><a class="submitduplicate duplication" href="<?php echo esc_url( $notify_url ); ?>"><?php esc_html_e( 'Copy to a new draft', 'woocommerce' ); ?></a></div>
		<?php
	}

	/**
	 * Duplicate a product action.
	 */
	public function duplicate_product_action() {
		if ( empty( $_REQUEST['post'] ) ) {
			wp_die( esc_html__( 'No product to duplicate has been supplied!', 'woocommerce' ) );
		}

		$product_id = isset( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : '';

		check_admin_referer( 'woocommerce-duplicate-product_' . $product_id );

		$product = wc_get_product( $product_id );

		if ( false === $product ) {
			/* translators: %s: product id */
			wp_die( sprintf( esc_html__( 'Product creation failed, could not find original product: %s', 'woocommerce' ), esc_html( $product_id ) ) );
		}

		$duplicate = $this->product_duplicate( $product );

		// Hook rename to match other woocommerce_product_* hooks, and to move away from depending on a response from the wp_posts table.
		do_action( 'woocommerce_product_duplicate', $duplicate, $product );
		wc_do_deprecated_action( 'woocommerce_duplicate_product', array( $duplicate->get_id(), $this->get_product_to_duplicate( $product_id ) ), '3.0', 'Use woocommerce_product_duplicate action instead.' );

		// Redirect to the edit screen for the new draft page.
		wp_redirect( admin_url( 'post.php?action=edit&post=' . $duplicate->get_id() ) );
		exit;
	}

	/**
	 * Function to create the duplicate of the product.
	 *
	 * @param WC_Product $product The product to duplicate.
	 * @return WC_Product The duplicate.
	 */
	public function product_duplicate( $product ) {
		/**
		 * Filter to allow us to exclude meta keys from product duplication..
		 *
		 * @param array $exclude_meta The keys to exclude from the duplicate.
		 * @param array $existing_meta_keys The meta keys that the product already has.
		 * @since 2.6
		 */
		$meta_to_exclude = array_filter(
			apply_filters(
				'woocommerce_duplicate_product_exclude_meta',
				array(),
				array_map(
					function ( $datum ) {
						return $datum->key;
					},
					$product->get_meta_data()
				)
			)
		);

		$duplicate = clone $product;
		$duplicate->set_id( 0 );
		/* translators: %s contains the name of the original product. */
		$duplicate->set_name( sprintf( esc_html__( '%s (Copy)', 'woocommerce' ), $duplicate->get_name() ) );
		$duplicate->set_total_sales( 0 );
		if ( '' !== $product->get_sku( 'edit' ) ) {
			$duplicate->set_sku( wc_product_generate_unique_sku( 0, $product->get_sku( 'edit' ) ) );
		}
		$duplicate->set_status( 'draft' );
		$duplicate->set_date_created( null );
		$duplicate->set_slug( '' );
		$duplicate->set_rating_counts( 0 );
		$duplicate->set_average_rating( 0 );
		$duplicate->set_review_count( 0 );

		foreach ( $meta_to_exclude as $meta_key ) {
			$duplicate->delete_meta_data( $meta_key );
		}

		/**
		 * This action can be used to modify the object further before it is created - it will be passed by reference.
		 *
		 * @since 3.0
		 */
		do_action( 'woocommerce_product_duplicate_before_save', $duplicate, $product );

		// Save parent product.
		$duplicate->save();

		// Duplicate children of a variable product.
		if ( ! apply_filters( 'woocommerce_duplicate_product_exclude_children', false, $product ) && $product->is_type( 'variable' ) ) {
			foreach ( $product->get_children() as $child_id ) {
				$child           = wc_get_product( $child_id );
				$child_duplicate = clone $child;
				$child_duplicate->set_parent_id( $duplicate->get_id() );
				$child_duplicate->set_id( 0 );
				$child_duplicate->set_date_created( null );

				// If we wait and let the insertion generate the slug, we will see extreme performance degradation
				// in the case where a product is used as a template. Every time the template is duplicated, each
				// variation will query every consecutive slug until it finds an empty one. To avoid this, we can
				// optimize the generation ourselves, avoiding the issue altogether.
				$this->generate_unique_slug( $child_duplicate );

				if ( '' !== $child->get_sku( 'edit' ) ) {
					$child_duplicate->set_sku( wc_product_generate_unique_sku( 0, $child->get_sku( 'edit' ) ) );
				}

				foreach ( $meta_to_exclude as $meta_key ) {
					$child_duplicate->delete_meta_data( $meta_key );
				}

				/**
				 * This action can be used to modify the object further before it is created - it will be passed by reference.
				 *
				 * @since 3.0
				 */
				do_action( 'woocommerce_product_duplicate_before_save', $child_duplicate, $child );

				$child_duplicate->save();
			}

			// Get new object to reflect new children.
			$duplicate = wc_get_product( $duplicate->get_id() );
		}

		return $duplicate;
	}

	/**
	 * Get a product from the database to duplicate.
	 *
	 * @deprecated 3.0.0
	 * @param mixed $id The ID of the product to duplicate.
	 * @return object|bool
	 * @see duplicate_product
	 */
	private function get_product_to_duplicate( $id ) {
		global $wpdb;

		$id = absint( $id );

		if ( ! $id ) {
			return false;
		}

		$post = $wpdb->get_row( $wpdb->prepare( "SELECT {$wpdb->posts}.* FROM {$wpdb->posts} WHERE ID = %d", $id ) );

		if ( isset( $post->post_type ) && 'revision' === $post->post_type ) {
			$id   = $post->post_parent;
			$post = $wpdb->get_row( $wpdb->prepare( "SELECT {$wpdb->posts}.* FROM {$wpdb->posts} WHERE ID = %d", $id ) );
		}

		return $post;
	}

	/**
	 * Generates a unique slug for a given product. We do this so that we can override the
	 * behavior of wp_unique_post_slug(). The normal slug generation will run single
	 * select queries on every non-unique slug, resulting in very bad performance.
	 *
	 * @param WC_Product $product The product to generate a slug for.
	 * @since 3.9.0
	 */
	private function generate_unique_slug( $product ) {
		global $wpdb;

		// We want to remove the suffix from the slug so that we can find the maximum suffix using this root slug.
		// This will allow us to find the next-highest suffix that is unique. While this does not support gap
		// filling, this shouldn't matter for our use-case.
		$root_slug = preg_replace( '/-[0-9]+$/', '', $product->get_slug() );

		$results = $wpdb->get_results(
			$wpdb->prepare( "SELECT post_name FROM $wpdb->posts WHERE post_name LIKE %s AND post_type IN ( 'product', 'product_variation' )", $root_slug . '%' )
		);

		// The slug is already unique!
		if ( empty( $results ) ) {
			return;
		}

		// Find the maximum suffix so we can ensure uniqueness.
		$max_suffix = 1;
		foreach ( $results as $result ) {
			// Pull a numerical suffix off the slug after the last hyphen.
			$suffix = intval( substr( $result->post_name, strrpos( $result->post_name, '-' ) + 1 ) );
			if ( $suffix > $max_suffix ) {
				$max_suffix = $suffix;
			}
		}

		$product->set_slug( $root_slug . '-' . ( $max_suffix + 1 ) );
	}
}

return new WC_Admin_Duplicate_Product();
