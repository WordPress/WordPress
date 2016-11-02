<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * UpSolution Widget: Portfolio
 *
 * Class US_Widget_Login
 */
class US_Widget_Portfolio extends US_Widget {

	/**
	 * Output the widget
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	function widget( $args, $instance ) {

		parent::before_widget( $args, $instance );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		$output = $args['before_widget'];

		if ( $title ) {
			$output .= '<h4>' . $title . '</h4>';
		}



		$template_vars = array(
			'categories' => ( isset( $instance['categories'] ) AND is_array( $instance['categories'] ) ) ? implode( ', ', $instance['categories'] ) : null,
			'style_name' => 'style_1',
			'columns' => ( isset( $instance['columns'] ) AND in_array( $instance['columns'], array( 2, 3, 4, 5, ) ) ) ? $instance['columns'] : 3,
			'ratio' => '1x1',
			'metas' => array( 'title', ),
			'align' => 'center',
			'filter' => false,
			'with_indents' => false,
			'pagination' => 'none',
			'orderby' => ( in_array( $instance['orderby'], array ( 'date', 'date_asc', 'alpha', 'rand' ) ) ) ? $instance['orderby'] : 'date',
			'is_widget' => 'true'
		);

		$template_vars['perpage'] = ( isset( $instance['items'] ) ) ? $instance['items'] : $template_vars['columns'];

		ob_start();
		us_load_template( 'templates/portfolio/listing', $template_vars );
		$output .= ob_get_clean();

		$output .= $args['after_widget'];

		echo $output;
	}

	/**
	 * Output the settings update form.
	 *
	 * @param array $instance Current settings.
	 *
	 * @return string Form's output marker that could be used for further hooks
	 */
	public function form( $instance ) {
		$us_portfolio_categories = array();
		$us_portfolio_categories_raw = get_categories( array(
			'taxonomy' => 'us_portfolio_category',
			'hierarchical' => 0,
		) );
		if ( $us_portfolio_categories_raw ) {
			foreach ( $us_portfolio_categories_raw as $portfolio_category_raw ) {
				if ( is_object( $portfolio_category_raw ) ) {
					$us_portfolio_categories[ $portfolio_category_raw->name ] = $portfolio_category_raw->slug;
				}
			}
		}

		if ( ! empty( $us_portfolio_categories ) ) {
			$this->config['params']['categories'] = array(
				'type' => 'checkbox',
				'heading' => __( 'Display Items of selected categories', 'us' ),
				'value' => $us_portfolio_categories,
			);
		}

		return parent::form( $instance );
	}


}
