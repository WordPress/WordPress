<?php
/**
 * Tag Cloud Widget
 *
 * @author 		WooThemes
 * @category 	Widgets
 * @package 	WooCommerce/Widgets
 * @version 	2.1.0
 * @extends 	WC_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Widget_Product_Tag_Cloud extends WC_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_product_tag_cloud';
		$this->widget_description = __( 'Your most used product tags in cloud format.', 'woocommerce' );
		$this->widget_id          = 'woocommerce_product_tag_cloud';
		$this->widget_name        = __( 'WooCommerce Product Tags', 'woocommerce' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Product Tags', 'woocommerce' ),
				'label' => __( 'Title', 'woocommerce' )
			)
		);
		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		$current_taxonomy = $this->_get_current_taxonomy($instance);

		if ( empty( $instance['title'] ) ) {
			$tax   = get_taxonomy( $current_taxonomy );
			$title = apply_filters( 'widget_title', $tax->labels->name, $instance, $this->id_base );
		} else {
			$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		}

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		echo '<div class="tagcloud">';

		wp_tag_cloud( apply_filters( 'woocommerce_product_tag_cloud_widget_args', array( 'taxonomy' => $current_taxonomy ) ) );

		echo "</div>";

		echo $after_widget;
	}

	/**
	 * Return the taxonomy being displayed
	 *
	 * @param  object $instance
	 * @return string
	 */
	public function _get_current_taxonomy( $instance ) {
		return 'product_tag';
	}
}

register_widget( 'WC_Widget_Product_Tag_Cloud' );