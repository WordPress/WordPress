<?php
/**
 * Layered Navigation Filters Widget.
 *
 * @package WooCommerce\Widgets
 * @version 2.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Widget layered nav filters.
 */
class WC_Widget_Layered_Nav_Filters extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_layered_nav_filters';
		$this->widget_description = __( 'Display a list of active product filters.', 'woocommerce' );
		$this->widget_id          = 'woocommerce_layered_nav_filters';
		$this->widget_name        = __( 'Active Product Filters', 'woocommerce' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Active filters', 'woocommerce' ),
				'label' => __( 'Title', 'woocommerce' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		if ( ! is_shop() && ! is_product_taxonomy() ) {
			return;
		}

		$_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
		$min_price          = isset( $_GET['min_price'] ) ? wc_clean( wp_unslash( $_GET['min_price'] ) ) : 0; // WPCS: input var ok, CSRF ok.
		$max_price          = isset( $_GET['max_price'] ) ? wc_clean( wp_unslash( $_GET['max_price'] ) ) : 0; // WPCS: input var ok, CSRF ok.
		$rating_filter      = isset( $_GET['rating_filter'] ) ? array_filter( array_map( 'absint', explode( ',', wp_unslash( $_GET['rating_filter'] ) ) ) ) : array(); // WPCS: sanitization ok, input var ok, CSRF ok.
		$base_link          = $this->get_current_page_url();

		if ( 0 < count( $_chosen_attributes ) || 0 < $min_price || 0 < $max_price || ! empty( $rating_filter ) ) {

			$this->widget_start( $args, $instance );

			echo '<ul>';

			/**
			 * Allow 3rd party developers to add their own filters to start the Layered Navigation Filters Widget.
			 *
			 * @since 7.6.0
			 *
			 * @param array $args     Arguments.
			 * @param array $instance Widget instance.
			 */
			do_action( 'woocommerce_widget_layered_nav_filters_start', $args, $instance );

			// Attributes.
			if ( ! empty( $_chosen_attributes ) ) {
				foreach ( $_chosen_attributes as $taxonomy => $data ) {
					foreach ( $data['terms'] as $term_slug ) {
						$term = get_term_by( 'slug', $term_slug, $taxonomy );
						if ( ! $term ) {
							continue;
						}

						$filter_name    = 'filter_' . wc_attribute_taxonomy_slug( $taxonomy );
						$current_filter = isset( $_GET[ $filter_name ] ) ? explode( ',', wc_clean( wp_unslash( $_GET[ $filter_name ] ) ) ) : array(); // WPCS: input var ok, CSRF ok.
						$current_filter = array_map( 'sanitize_title', $current_filter );
						$new_filter     = array_diff( $current_filter, array( $term_slug ) );

						$link = remove_query_arg( array( 'add-to-cart', $filter_name ), $base_link );

						if ( count( $new_filter ) > 0 ) {
							$link = add_query_arg( $filter_name, implode( ',', $new_filter ), $link );
						}

						$filter_classes = array( 'chosen', 'chosen-' . sanitize_html_class( str_replace( 'pa_', '', $taxonomy ) ), 'chosen-' . sanitize_html_class( str_replace( 'pa_', '', $taxonomy ) . '-' . $term_slug ) );

						echo '<li class="' . esc_attr( implode( ' ', $filter_classes ) ) . '"><a rel="nofollow" aria-label="' . esc_attr__( 'Remove filter', 'woocommerce' ) . '" href="' . esc_url( $link ) . '">' . esc_html( $term->name ) . '</a></li>';
					}
				}
			}

			if ( $min_price ) {
				$link = remove_query_arg( 'min_price', $base_link );
				/* translators: %s: minimum price */
				echo '<li class="chosen"><a rel="nofollow" aria-label="' . esc_attr__( 'Remove filter', 'woocommerce' ) . '" href="' . esc_url( $link ) . '">' . sprintf( __( 'Min %s', 'woocommerce' ), wc_price( $min_price ) ) . '</a></li>'; // WPCS: XSS ok.
			}

			if ( $max_price ) {
				$link = remove_query_arg( 'max_price', $base_link );
				/* translators: %s: maximum price */
				echo '<li class="chosen"><a rel="nofollow" aria-label="' . esc_attr__( 'Remove filter', 'woocommerce' ) . '" href="' . esc_url( $link ) . '">' . sprintf( __( 'Max %s', 'woocommerce' ), wc_price( $max_price ) ) . '</a></li>'; // WPCS: XSS ok.
			}

			if ( ! empty( $rating_filter ) ) {
				foreach ( $rating_filter as $rating ) {
					$link_ratings = implode( ',', array_diff( $rating_filter, array( $rating ) ) );
					$link         = $link_ratings ? add_query_arg( 'rating_filter', $link_ratings ) : remove_query_arg( 'rating_filter', $base_link );

					/* translators: %s: rating */
					echo '<li class="chosen"><a rel="nofollow" aria-label="' . esc_attr__( 'Remove filter', 'woocommerce' ) . '" href="' . esc_url( $link ) . '">' . sprintf( esc_html__( 'Rated %s out of 5', 'woocommerce' ), esc_html( $rating ) ) . '</a></li>';
				}
			}

			/**
			 * Allow 3rd party developers to add their own filters to end the Layered Navigation Filters Widget.
			 *
			 * @since 7.6.0
			 *
			 * @param array $args     Arguments.
			 * @param array $instance Widget instance.
			 */
			do_action( 'woocommerce_widget_layered_nav_filters_end', $args, $instance );

			echo '</ul>';

			$this->widget_end( $args );
		}
	}
}
