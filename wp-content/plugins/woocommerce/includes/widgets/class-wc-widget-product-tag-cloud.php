<?php
/**
 * Tag Cloud Widget.
 *
 * @package WooCommerce\Widgets
 * @version 3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget product tag cloud
 */
class WC_Widget_Product_Tag_Cloud extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_product_tag_cloud';
		$this->widget_description = __( 'A cloud of your most used product tags.', 'woocommerce' );
		$this->widget_id          = 'woocommerce_product_tag_cloud';
		$this->widget_name        = __( 'Product Tag Cloud', 'woocommerce' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Product tags', 'woocommerce' ),
				'label' => __( 'Title', 'woocommerce' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		$current_taxonomy = $this->get_current_taxonomy( $instance );

		if ( empty( $instance['title'] ) ) {
			$taxonomy          = get_taxonomy( $current_taxonomy );
			$instance['title'] = $taxonomy->labels->name;
		}

		$this->widget_start( $args, $instance );

		echo '<div class="tagcloud">';

		wp_tag_cloud(
			apply_filters(
				'woocommerce_product_tag_cloud_widget_args',
				array(
					'taxonomy'                  => $current_taxonomy,
					'topic_count_text_callback' => array( $this, 'topic_count_text' ),
				)
			)
		);

		echo '</div>';

		$this->widget_end( $args );
	}

	/**
	 * Return the taxonomy being displayed.
	 *
	 * @param  object $instance Widget instance.
	 * @return string
	 */
	public function get_current_taxonomy( $instance ) {
		return 'product_tag';
	}

	/**
	 * Returns topic count text.
	 *
	 * @since 3.4.0
	 * @param int $count Count text.
	 * @return string
	 */
	public function topic_count_text( $count ) {
		/* translators: %s: product count */
		return sprintf( _n( '%s product', '%s products', $count, 'woocommerce' ), number_format_i18n( $count ) );
	}

	// Ignore whole block to avoid warnings about PSR2.Methods.MethodDeclaration.Underscore violation.
	// @codingStandardsIgnoreStart
	/**
	 * Return the taxonomy being displayed.
	 *
	 * @deprecated 3.4.0
	 * @param  object $instance Widget instance.
	 * @return string
	 */
	public function _get_current_taxonomy( $instance ) {
		wc_deprecated_function( '_get_current_taxonomy', '3.4.0', 'WC_Widget_Product_Tag_Cloud->get_current_taxonomy' );
		return $this->get_current_taxonomy( $instance );
	}

	/**
	 * Returns topic count text.
	 *
	 * @deprecated 3.4.0
	 * @since 2.6.0
	 * @param int $count Count text.
	 * @return string
	 */
	public function _topic_count_text( $count ) {
		wc_deprecated_function( '_topic_count_text', '3.4.0', 'WC_Widget_Product_Tag_Cloud->topic_count_text' );
		return $this->topic_count_text( $count );
	}
	// @codingStandardsIgnoreEnd
}
