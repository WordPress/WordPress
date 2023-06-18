<?php
/**
 * Product Categories meta box
 *
 * Display the product categories meta box.
 *
 * @package     WooCommerce\Admin\Meta Boxes
 * @version     7.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Meta_Box_Product_Categories Class.
 */
class WC_Meta_Box_Product_Categories {

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post Current post object.
	 * @param array   $box {
	 *     Categories meta box arguments.
	 *
	 *     @type string   $id       Meta box 'id' attribute.
	 *     @type string   $title    Meta box title.
	 *     @type callable $callback Meta box display callback.
	 *     @type array    $args {
	 *         Extra meta box arguments.
	 *
	 *         @type string $taxonomy Taxonomy. Default 'category'.
	 *     }
	 * }
	 */
	public static function output( $post, $box ) {
		$categories_count = (int) wp_count_terms( 'product_cat' );

		/**
		 * Filters the category metabox search threshold, for when to render the typeahead field.
		 *
		 * @since 7.6.0
		 *
		 * @param number $threshold The default threshold.
		 * @returns number The threshold that will be used.
		 */
		if ( $categories_count <= apply_filters( 'woocommerce_product_category_metabox_search_threshold', 100 ) && function_exists( 'post_categories_meta_box' ) ) {
			return post_categories_meta_box( $post, $box );
		}

		$defaults = array( 'taxonomy' => 'category' );
		if ( ! isset( $box['args'] ) || ! is_array( $box['args'] ) ) {
			$args = array();
		} else {
			$args = $box['args'];
		}
		$parsed_args         = wp_parse_args( $args, $defaults );
		$tax_name            = $parsed_args['taxonomy'];
		$selected_categories = wp_get_object_terms( $post->ID, 'product_cat' );
		?>
		<div id="taxonomy-<?php echo esc_attr( $tax_name ); ?>-metabox"></div>
		<?php foreach ( (array) $selected_categories as $term ) { ?>
			<input
				type="hidden"
				value="<?php echo esc_attr( $term->term_id ); ?>"
				name="tax_input[<?php esc_attr( $tax_name ); ?>][]"
				data-name="<?php echo esc_attr( $term->name ); ?>"
			/>
			<?php
		}
	}
}
