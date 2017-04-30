<?php
/**
 * List products. One widget to rule them all.
 *
 * @author 		WooThemes
 * @category 	Widgets
 * @package 	WooCommerce/Widgets
 * @version 	2.1.0
 * @extends 	WC_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Widget_Products extends WC_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_products';
		$this->widget_description = __( 'Display a list of your products on your site.', 'woocommerce' );
		$this->widget_id          = 'woocommerce_products';
		$this->widget_name        = __( 'WooCommerce Products', 'woocommerce' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Products', 'woocommerce' ),
				'label' => __( 'Title', 'woocommerce' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => __( 'Number of products to show', 'woocommerce' )
			),
			'show' => array(
				'type'  => 'select',
				'std'   => '',
				'label' => __( 'Show', 'woocommerce' ),
				'options' => array(
					''         => __( 'All Products', 'woocommerce' ),
					'featured' => __( 'Featured Products', 'woocommerce' ),
					'onsale'   => __( 'On-sale Products', 'woocommerce' ),
				)
			),
			'orderby' => array(
				'type'  => 'select',
				'std'   => 'date',
				'label' => __( 'Order by', 'woocommerce' ),
				'options' => array(
					'date'   => __( 'Date', 'woocommerce' ),
					'price'  => __( 'Price', 'woocommerce' ),
					'rand'   => __( 'Random', 'woocommerce' ),
					'sales'  => __( 'Sales', 'woocommerce' ),
				)
			),
			'order' => array(
				'type'  => 'select',
				'std'   => 'desc',
				'label' => _x( 'Order', 'Sorting order', 'woocommerce' ),
				'options' => array(
					'asc'  => __( 'ASC', 'woocommerce' ),
					'desc' => __( 'DESC', 'woocommerce' ),
				)
			),
			'hide_free' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Hide free products', 'woocommerce' )
			),
			'show_hidden' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show hidden products', 'woocommerce' )
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

		if ( $this->get_cached_widget( $args ) )
			return;

		ob_start();
		extract( $args );

		$title       = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$number      = absint( $instance['number'] );
		$show        = sanitize_title( $instance['show'] );
		$orderby     = sanitize_title( $instance['orderby'] );
		$order       = sanitize_title( $instance['order'] );
		$show_rating = false;

    	$query_args = array(
    		'posts_per_page' => $number,
    		'post_status' 	 => 'publish',
    		'post_type' 	 => 'product',
    		'no_found_rows'  => 1,
    		'order'          => $order == 'asc' ? 'asc' : 'desc'
    	);

    	$query_args['meta_query'] = array();

    	if ( empty( $instance['show_hidden'] ) ) {
			$query_args['meta_query'][] = WC()->query->visibility_meta_query();
			$query_args['post_parent']  = 0;
		}

		if ( ! empty( $instance['hide_free'] ) ) {
    		$query_args['meta_query'][] = array(
			    'key'     => '_price',
			    'value'   => 0,
			    'compare' => '>',
			    'type'    => 'DECIMAL',
			);
    	}

	    $query_args['meta_query'][] = WC()->query->stock_status_meta_query();
	    $query_args['meta_query']   = array_filter( $query_args['meta_query'] );

    	switch ( $show ) {
    		case 'featured' :
    			$query_args['meta_query'][] = array(
					'key'   => '_featured',
					'value' => 'yes'
				);
    			break;
    		case 'onsale' :
    			$product_ids_on_sale = wc_get_product_ids_on_sale();
				$product_ids_on_sale[] = 0;
				$query_args['post__in'] = $product_ids_on_sale;
    			break;
    	}

    	switch ( $orderby ) {
			case 'price' :
				$query_args['meta_key'] = '_price';
    			$query_args['orderby']  = 'meta_value_num';
				break;
			case 'rand' :
    			$query_args['orderby']  = 'rand';
				break;
			case 'sales' :
				$query_args['meta_key'] = 'total_sales';
    			$query_args['orderby']  = 'meta_value_num';
				break;
			default :
				$query_args['orderby']  = 'date';
    	}

		$r = new WP_Query( $query_args );

		if ( $r->have_posts() ) {

			echo $before_widget;

			if ( $title )
				echo $before_title . $title . $after_title;

			echo '<ul class="product_list_widget">';

			while ( $r->have_posts()) {
				$r->the_post();
				wc_get_template( 'content-widget-product.php', array( 'show_rating' => $show_rating ) );
			}

			echo '</ul>';

			echo $after_widget;
		}

		wp_reset_postdata();

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}

register_widget( 'WC_Widget_Products' );