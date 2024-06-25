<?php
/**
 * Widget API: WP_Widget_Archives class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core class used to implement the Archives widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class WP_Widget_Archives extends WP_Widget {

	/**
	 * Sets up a new Archives widget instance.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_archive',
			'description'                 => __( 'A monthly archive of your site&#8217;s Posts.' ),
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		);
		parent::__construct( 'archives', __( 'Archives' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current Archives widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Archives widget instance.
	 */
	public function widget( $args, $instance ) {
		$default_title = __( 'Archives' );
		$title         = ! empty( $instance['title'] ) ? $instance['title'] : $default_title;

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$count    = ! empty( $instance['count'] ) ? '1' : '0';
		$dropdown = ! empty( $instance['dropdown'] ) ? '1' : '0';

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		if ( $dropdown ) {
			$dropdown_id = "{$this->id_base}-dropdown-{$this->number}";
			?>
		<label class="screen-reader-text" for="<?php echo esc_attr( $dropdown_id ); ?>"><?php echo $title; ?></label>
		<select id="<?php echo esc_attr( $dropdown_id ); ?>" name="archive-dropdown">
			<?php
			/**
			 * Filters the arguments for the Archives widget drop-down.
			 *
			 * @since 2.8.0
			 * @since 4.9.0 Added the `$instance` parameter.
			 *
			 * @see wp_get_archives()
			 *
			 * @param array $args     An array of Archives widget drop-down arguments.
			 * @param array $instance Settings for the current Archives widget instance.
			 */
			$dropdown_args = apply_filters(
				'widget_archives_dropdown_args',
				array(
					'type'            => 'monthly',
					'format'          => 'option',
					'show_post_count' => $count,
				),
				$instance
			);

			switch ( $dropdown_args['type'] ) {
				case 'yearly':
					$label = __( 'Select Year' );
					break;
				case 'monthly':
					$label = __( 'Select Month' );
					break;
				case 'daily':
					$label = __( 'Select Day' );
					break;
				case 'weekly':
					$label = __( 'Select Week' );
					break;
				default:
					$label = __( 'Select Post' );
					break;
			}
			?>

			<option value=""><?php echo esc_html( $label ); ?></option>
			<?php wp_get_archives( $dropdown_args ); ?>

		</select>

			<?php ob_start(); ?>
<script>
(function() {
	var dropdown = document.getElementById( "<?php echo esc_js( $dropdown_id ); ?>" );
	function onSelectChange() {
		if ( dropdown.options[ dropdown.selectedIndex ].value !== '' ) {
			document.location.href = this.options[ this.selectedIndex ].value;
		}
	}
	dropdown.onchange = onSelectChange;
})();
</script>
			<?php
			wp_print_inline_script_tag( wp_remove_surrounding_empty_script_tags( ob_get_clean() ) );
		} else {
			$format = current_theme_supports( 'html5', 'navigation-widgets' ) ? 'html5' : 'xhtml';

			/** This filter is documented in wp-includes/widgets/class-wp-nav-menu-widget.php */
			$format = apply_filters( 'navigation_widgets_format', $format );

			if ( 'html5' === $format ) {
				// The title may be filtered: Strip out HTML and make sure the aria-label is never empty.
				$title      = trim( strip_tags( $title ) );
				$aria_label = $title ? $title : $default_title;
				echo '<nav aria-label="' . esc_attr( $aria_label ) . '">';
			}
			?>

			<ul>
				<?php
				wp_get_archives(
					/**
					 * Filters the arguments for the Archives widget.
					 *
					 * @since 2.8.0
					 * @since 4.9.0 Added the `$instance` parameter.
					 *
					 * @see wp_get_archives()
					 *
					 * @param array $args     An array of Archives option arguments.
					 * @param array $instance Array of settings for the current widget.
					 */
					apply_filters(
						'widget_archives_args',
						array(
							'type'            => 'monthly',
							'show_post_count' => $count,
						),
						$instance
					)
				);
				?>
			</ul>

			<?php
			if ( 'html5' === $format ) {
				echo '</nav>';
			}
		}

		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current Archives widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget_Archives::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$new_instance         = wp_parse_args(
			(array) $new_instance,
			array(
				'title'    => '',
				'count'    => 0,
				'dropdown' => '',
			)
		);
		$instance['title']    = sanitize_text_field( $new_instance['title'] );
		$instance['count']    = $new_instance['count'] ? 1 : 0;
		$instance['dropdown'] = $new_instance['dropdown'] ? 1 : 0;

		return $instance;
	}

	/**
	 * Outputs the settings form for the Archives widget.
	 *
	 * @since 2.8.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title'    => '',
				'count'    => 0,
				'dropdown' => '',
			)
		);
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<input class="checkbox" type="checkbox"<?php checked( $instance['dropdown'] ); ?> id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Display as dropdown' ); ?></label>
			<br />
			<input class="checkbox" type="checkbox"<?php checked( $instance['count'] ); ?> id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show post counts' ); ?></label>
		</p>
		<?php
	}
}
