<?php
/**
 * Widget API: WP_Widget_Tag_Cloud class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core class used to implement a Tag cloud widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class WP_Widget_Tag_Cloud extends WP_Widget {

	/**
	 * Sets up a new Tag Cloud widget instance.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		$widget_ops = array(
			'description'                 => __( 'A cloud of your most used tags.' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'tag_cloud', __( 'Tag Cloud' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current Tag Cloud widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Tag Cloud widget instance.
	 */
	public function widget( $args, $instance ) {
		$current_taxonomy = $this->_get_current_taxonomy( $instance );

		if ( ! empty( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			if ( 'post_tag' === $current_taxonomy ) {
				$title = __( 'Tags' );
			} else {
				$tax   = get_taxonomy( $current_taxonomy );
				$title = $tax->labels->name;
			}
		}

		$show_count = ! empty( $instance['count'] );

		$tag_cloud = wp_tag_cloud(
			/**
			 * Filters the taxonomy used in the Tag Cloud widget.
			 *
			 * @since 2.8.0
			 * @since 3.0.0 Added taxonomy drop-down.
			 * @since 4.9.0 Added the `$instance` parameter.
			 *
			 * @see wp_tag_cloud()
			 *
			 * @param array $args     Args used for the tag cloud widget.
			 * @param array $instance Array of settings for the current widget.
			 */
			apply_filters(
				'widget_tag_cloud_args',
				array(
					'taxonomy'   => $current_taxonomy,
					'echo'       => false,
					'show_count' => $show_count,
				),
				$instance
			)
		);

		if ( empty( $tag_cloud ) ) {
			return;
		}

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$format = current_theme_supports( 'html5', 'navigation-widgets' ) ? 'html5' : 'xhtml';

		/** This filter is documented in wp-includes/widgets/class-wp-nav-menu-widget.php */
		$format = apply_filters( 'navigation_widgets_format', $format );

		if ( 'html5' === $format ) {
			// The title may be filtered: Strip out HTML and make sure the aria-label is never empty.
			$title      = trim( strip_tags( $title ) );
			$aria_label = $title ? $title : $default_title;
			echo '<nav role="navigation" aria-label="' . esc_attr( $aria_label ) . '">';
		}

		echo '<div class="tagcloud">';

		echo $tag_cloud;

		echo "</div>\n";

		if ( 'html5' === $format ) {
			echo '</nav>';
		}

		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current Tag Cloud widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance             = array();
		$instance['title']    = sanitize_text_field( $new_instance['title'] );
		$instance['count']    = ! empty( $new_instance['count'] ) ? 1 : 0;
		$instance['taxonomy'] = stripslashes( $new_instance['taxonomy'] );
		return $instance;
	}

	/**
	 * Outputs the Tag Cloud widget settings form.
	 *
	 * @since 2.8.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$count = isset( $instance['count'] ) ? (bool) $instance['count'] : false;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
		$taxonomies       = get_taxonomies( array( 'show_tagcloud' => true ), 'object' );
		$current_taxonomy = $this->_get_current_taxonomy( $instance );

		switch ( count( $taxonomies ) ) {

			// No tag cloud supporting taxonomies found, display error message.
			case 0:
				?>
				<input type="hidden" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" value="" />
				<p>
					<?php _e( 'The tag cloud will not be displayed since there are no taxonomies that support the tag cloud widget.' ); ?>
				</p>
				<?php
				break;

			// Just a single tag cloud supporting taxonomy found, no need to display a select.
			case 1:
				$keys     = array_keys( $taxonomies );
				$taxonomy = reset( $keys );
				?>
				<input type="hidden" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" value="<?php echo esc_attr( $taxonomy ); ?>" />
				<?php
				break;

			// More than one tag cloud supporting taxonomy found, display a select.
			default:
				?>
				<p>
					<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomy:' ); ?></label>
					<select class="widefat" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>">
					<?php foreach ( $taxonomies as $taxonomy => $tax ) : ?>
						<option value="<?php echo esc_attr( $taxonomy ); ?>" <?php selected( $taxonomy, $current_taxonomy ); ?>>
							<?php echo esc_html( $tax->labels->name ); ?>
						</option>
					<?php endforeach; ?>
					</select>
				</p>
				<?php
		}

		if ( count( $taxonomies ) > 0 ) {
			?>
			<p>
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" <?php checked( $count, true ); ?> />
				<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show tag counts' ); ?></label>
			</p>
			<?php
		}
	}

	/**
	 * Retrieves the taxonomy for the current Tag cloud widget instance.
	 *
	 * @since 4.4.0
	 *
	 * @param array $instance Current settings.
	 * @return string Name of the current taxonomy if set, otherwise 'post_tag'.
	 */
	public function _get_current_taxonomy( $instance ) {
		if ( ! empty( $instance['taxonomy'] ) && taxonomy_exists( $instance['taxonomy'] ) ) {
			return $instance['taxonomy'];
		}

		return 'post_tag';
	}
}
