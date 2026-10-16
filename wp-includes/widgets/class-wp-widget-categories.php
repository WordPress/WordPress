<?php
/**
 * Widget API: WP_Widget_Categories class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core class used to implement a Categories widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class WP_Widget_Categories extends WP_Widget {

	/**
	 * Sets up a new Categories widget instance.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_categories',
			'description'                 => __( 'A list or dropdown of categories.' ),
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		);
		parent::__construct( 'categories', __( 'Categories' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current Categories widget instance.
	 *
	 * @since 2.8.0
	 * @since 4.2.0 Creates a unique HTML ID for the `<select>` element
	 *              if more than one instance is displayed on the page.
	 *
	 * @param array $args {
	 *     Display arguments.
	 *
	 *     @type string $before_widget HTML markup to prepend to each widget in the sidebar.
	 *     @type string $after_widget  HTML markup to append to each widget in the sidebar.
	 *     @type string $before_title  HTML markup to prepend to the widget title when displayed.
	 *     @type string $after_title   HTML markup to append to the widget title when displayed.
	 * }
	 * @param array $instance {
	 *     Settings for the current Categories widget instance.
	 *
	 *     @type string $title        Widget title.
	 *     @type 0|1    $count        Whether to show post counts.
	 *     @type 0|1    $hierarchical Whether to show the category hierarchy.
	 *     @type 0|1    $dropdown     Whether to display the categories as a drop-down.
	 * }
	 */
	public function widget( $args, $instance ) {
		static $first_dropdown = true;

		$default_title = __( 'Categories' );
		$title         = ! empty( $instance['title'] ) ? $instance['title'] : $default_title;

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		/*
		 * Although update() stores these flags as 0 or 1, they are checked loosely for back-compat:
		 * the keys are absent for an instance that has not been saved yet, instances saved before
		 * WordPress 2.8 stored booleans, and the_widget() passes along whatever values its caller
		 * supplies.
		 */
		$count        = ! empty( $instance['count'] );
		$hierarchical = ! empty( $instance['hierarchical'] );
		$dropdown     = ! empty( $instance['dropdown'] );

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$cat_args = array(
			'orderby'      => 'name',
			'show_count'   => $count,
			'hierarchical' => $hierarchical,
		);

		if ( $dropdown ) {
			printf( '<form action="%s" method="get">', esc_url( home_url() ) );
			$dropdown_id    = ( $first_dropdown ) ? 'cat' : "{$this->id_base}-dropdown-{$this->number}";
			$first_dropdown = false;

			echo '<label class="screen-reader-text" for="' . esc_attr( $dropdown_id ) . '">' . $title . '</label>';

			$cat_args['show_option_none'] = __( 'Select Category' );
			$cat_args['id']               = $dropdown_id;

			/**
			 * Filters the arguments for the Categories widget drop-down.
			 *
			 * @since 2.8.0
			 * @since 4.9.0 Added the `$instance` parameter.
			 * @since 7.2.0 The `show_count` and `hierarchical` arguments in `$cat_args` are now booleans instead of the numeric strings '0' and '1'.
			 *
			 * @see wp_dropdown_categories()
			 *
			 * @param array $cat_args {
			 *     An array of Categories widget drop-down arguments. See {@see wp_dropdown_categories()} for
			 *     information on additional accepted arguments.
			 *
			 *     @type string $orderby          Which column to use for ordering categories. See {@see get_terms()}
			 *                                    for a list of accepted values. Default 'name'.
			 *     @type bool   $show_count       Whether to include post counts.
			 *     @type bool   $hierarchical     Whether to traverse the taxonomy hierarchy.
			 *     @type string $show_option_none Text to display for showing no categories. Set to 'Select Category'.
			 *     @type string $id               Value for the 'id' attribute of the select element.
			 * }
			 * @param array $instance {
			 *     Array of settings for the current widget.
			 *
			 *     @type string $title        Widget title.
			 *     @type 0|1    $count        Whether to show post counts.
			 *     @type 0|1    $hierarchical Whether to show the category hierarchy.
			 *     @type 0|1    $dropdown     Whether to display the categories as a drop-down.
			 * }
			 */
			wp_dropdown_categories( apply_filters( 'widget_categories_dropdown_args', $cat_args, $instance ) );

			echo '</form>';

			ob_start();
			?>

<script>
( ( dropdownId ) => {
	const dropdown = document.getElementById( dropdownId );
	function onSelectChange() {
		setTimeout( () => {
			if ( 'escape' === dropdown.dataset.lastkey ) {
				return;
			}
			if ( dropdown.value && parseInt( dropdown.value ) > 0 && dropdown instanceof HTMLSelectElement ) {
				dropdown.parentElement.submit();
			}
		}, 250 );
	}
	function onKeyUp( event ) {
		if ( 'Escape' === event.key ) {
			dropdown.dataset.lastkey = 'escape';
		} else {
			delete dropdown.dataset.lastkey;
		}
	}
	function onClick() {
		delete dropdown.dataset.lastkey;
	}
	dropdown.addEventListener( 'keyup', onKeyUp );
	dropdown.addEventListener( 'click', onClick );
	dropdown.addEventListener( 'change', onSelectChange );
})( <?php echo wp_json_encode( $dropdown_id, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ); ?> );
</script>

			<?php
			wp_print_inline_script_tag( wp_remove_surrounding_empty_script_tags( ob_get_clean() ) . "\n//# sourceURL=" . rawurlencode( __METHOD__ ) );
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
				$cat_args['title_li'] = '';

				/**
				 * Filters the arguments for the Categories widget.
				 *
				 * @since 2.8.0
				 * @since 4.9.0 Added the `$instance` parameter.
				 * @since 7.2.0 The `show_count` and `hierarchical` arguments in `$cat_args` are now booleans instead of the numeric strings '0' and '1'.
				 *
				 * @param array $cat_args {
				 *     An array of Categories widget options. See {@see wp_list_categories()} for information on
				 *     additional accepted arguments.
				 *
				 *     @type string $orderby      Which column to use for ordering categories. See {@see get_terms()}
				 *                                for a list of accepted values. Default 'name'.
				 *     @type bool   $show_count   Whether to include post counts.
				 *     @type bool   $hierarchical Whether to traverse the taxonomy hierarchy.
				 *     @type string $title_li     Text to use for the list title `<li>` element. Default empty string.
				 * }
				 * @param array $instance {
				 *     Array of settings for the current widget.
				 *
				 *     @type string $title        Widget title.
				 *     @type 0|1    $count        Whether to show post counts.
				 *     @type 0|1    $hierarchical Whether to show the category hierarchy.
				 *     @type 0|1    $dropdown     Whether to display the categories as a drop-down.
				 * }
				 */
				wp_list_categories( apply_filters( 'widget_categories_args', $cat_args, $instance ) );
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
	 * Handles updating settings for the current Categories widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $new_instance {
	 *     New settings for this instance as input by the user via {@see WP_Widget::form()}. A checkbox
	 *     submits 'on' when checked and is omitted when unchecked. A saved instance resubmitted
	 *     through the REST API passes 0 or 1 instead.
	 *
	 *     @type string   $title        Widget title.
	 *     @type 'on'|0|1 $count        Whether to show post counts.
	 *     @type 'on'|0|1 $hierarchical Whether to show the category hierarchy.
	 *     @type 'on'|0|1 $dropdown     Whether to display the categories as a drop-down.
	 * }
	 * @param array $old_instance {
	 *     Old settings for this instance.
	 *
	 *     @type string $title        Widget title.
	 *     @type 0|1    $count        Whether to show post counts.
	 *     @type 0|1    $hierarchical Whether to show the category hierarchy.
	 *     @type 0|1    $dropdown     Whether to display the categories as a drop-down.
	 * }
	 * @return array {
	 *     Updated settings to save.
	 *
	 *     @type string $title        Widget title.
	 *     @type 0|1    $count        Whether to show post counts.
	 *     @type 0|1    $hierarchical Whether to show the category hierarchy.
	 *     @type 0|1    $dropdown     Whether to display the categories as a drop-down.
	 * }
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['title']        = sanitize_text_field( $new_instance['title'] ?? '' );
		$instance['count']        = ! empty( $new_instance['count'] ) ? 1 : 0;
		$instance['hierarchical'] = ! empty( $new_instance['hierarchical'] ) ? 1 : 0;
		$instance['dropdown']     = ! empty( $new_instance['dropdown'] ) ? 1 : 0;

		return $instance;
	}

	/**
	 * Outputs the settings form for the Categories widget.
	 *
	 * @since 2.8.0
	 *
	 * @param array $instance {
	 *     Current settings.
	 *
	 *     @type string $title        Widget title.
	 *     @type 0|1    $count        Whether to show post counts.
	 *     @type 0|1    $hierarchical Whether to show the category hierarchy.
	 *     @type 0|1    $dropdown     Whether to display the categories as a drop-down.
	 * }
	 */
	public function form( $instance ) {
		// Defaults.
		$instance     = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$count        = isset( $instance['count'] ) ? (bool) $instance['count'] : false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		$dropdown     = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>"<?php checked( $dropdown ); ?> />
			<label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Display as dropdown' ); ?></label>
			<br />

			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>"<?php checked( $count ); ?> />
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show post counts' ); ?></label>
			<br />

			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>"<?php checked( $hierarchical ); ?> />
			<label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>"><?php _e( 'Show hierarchy' ); ?></label>
		</p>
		<?php
	}
}
