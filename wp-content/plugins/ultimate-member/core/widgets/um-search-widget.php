<?php

class um_search_widget extends WP_Widget {

	function __construct() {

		parent::__construct(

		// Base ID of your widget
		'um_search_widget',

		// Widget name will appear in UI
		__('Ultimate Member - Search', 'ultimate-member'),

		// Widget description
		array( 'description' => __( 'Shows users they follow in a widget.', 'ultimate-member'), )
		);

	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// display the search form
		um_search_form();

		echo $args['after_widget'];
	}

	// Widget Backend
	public function form( $instance ) {
		global $ultimatemember;

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'Search Users', 'ultimate-member');
		}

		if ( isset( $instance[ 'max' ] ) ) {
			$max = $instance[ 'max' ];
		} else {
			$max = 11;
		}

		// Widget admin form
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}

}
