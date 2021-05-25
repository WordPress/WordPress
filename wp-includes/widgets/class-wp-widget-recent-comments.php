<?php
/**
 * Widget API: WP_Widget_Recent_Comments class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core class used to implement a Recent Comments widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class WP_Widget_Recent_Comments extends WP_Widget {

	/**
	 * Sets up a new Recent Comments widget instance.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_recent_comments',
			'description'                 => __( 'Your site&#8217;s most recent comments.' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'recent-comments', __( 'Recent Comments' ), $widget_ops );
		$this->alt_option_name = 'widget_recent_comments';

		if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
			add_action( 'wp_head', array( $this, 'recent_comments_style' ) );
		}
	}

	/**
	 * Outputs the default styles for the Recent Comments widget.
	 *
	 * @since 2.8.0
	 */
	public function recent_comments_style() {
		/**
		 * Filters the Recent Comments default widget styles.
		 *
		 * @since 3.1.0
		 *
		 * @param bool   $active  Whether the widget is active. Default true.
		 * @param string $id_base The widget ID.
		 */
		if ( ! current_theme_supports( 'widgets' ) // Temp hack #14876.
			|| ! apply_filters( 'show_recent_comments_widget_style', true, $this->id_base ) ) {
			return;
		}

		$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';

		printf(
			'<style%s>.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style>',
			$type_attr
		);
	}

	/**
	 * Outputs the content for the current Recent Comments widget instance.
	 *
	 * @since 2.8.0
	 * @since 5.4.0 Creates a unique HTML ID for the `<ul>` element
	 *              if more than one instance is displayed on the page.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Recent Comments widget instance.
	 */
	public function widget( $args, $instance ) {
		static $first_instance = true;

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$output = '';

		$default_title = __( 'Recent Comments' );
		$title         = ( ! empty( $instance['title'] ) ) ? $instance['title'] : $default_title;

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}

		$comments = get_comments(
			/**
			 * Filters the arguments for the Recent Comments widget.
			 *
			 * @since 3.4.0
			 * @since 4.9.0 Added the `$instance` parameter.
			 *
			 * @see WP_Comment_Query::query() for information on accepted arguments.
			 *
			 * @param array $comment_args An array of arguments used to retrieve the recent comments.
			 * @param array $instance     Array of settings for the current widget.
			 */
			apply_filters(
				'widget_comments_args',
				array(
					'number'      => $number,
					'status'      => 'approve',
					'post_status' => 'publish',
				),
				$instance
			)
		);

		$output .= $args['before_widget'];
		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		$recent_comments_id = ( $first_instance ) ? 'recentcomments' : "recentcomments-{$this->number}";
		$first_instance     = false;

		$format = current_theme_supports( 'html5', 'navigation-widgets' ) ? 'html5' : 'xhtml';

		/** This filter is documented in wp-includes/widgets/class-wp-nav-menu-widget.php */
		$format = apply_filters( 'navigation_widgets_format', $format );

		if ( 'html5' === $format ) {
			// The title may be filtered: Strip out HTML and make sure the aria-label is never empty.
			$title      = trim( strip_tags( $title ) );
			$aria_label = $title ? $title : $default_title;
			$output    .= '<nav role="navigation" aria-label="' . esc_attr( $aria_label ) . '">';
		}

		$output .= '<ul id="' . esc_attr( $recent_comments_id ) . '">';
		if ( is_array( $comments ) && $comments ) {
			// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
			$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
			_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

			foreach ( (array) $comments as $comment ) {
				$output .= '<li class="recentcomments">';
				$output .= sprintf(
					/* translators: Comments widget. 1: Comment author, 2: Post link. */
					_x( '%1$s on %2$s', 'widgets' ),
					'<span class="comment-author-link">' . get_comment_author_link( $comment ) . '</span>',
					'<a href="' . esc_url( get_comment_link( $comment ) ) . '">' . get_the_title( $comment->comment_post_ID ) . '</a>'
				);
				$output .= '</li>';
			}
		}
		$output .= '</ul>';

		if ( 'html5' === $format ) {
			$output .= '</nav>';
		}

		$output .= $args['after_widget'];

		echo $output;
	}

	/**
	 * Handles updating settings for the current Recent Comments widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance           = $old_instance;
		$instance['title']  = sanitize_text_field( $new_instance['title'] );
		$instance['number'] = absint( $new_instance['number'] );
		return $instance;
	}

	/**
	 * Outputs the settings form for the Recent Comments widget.
	 *
	 * @since 2.8.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title  = isset( $instance['title'] ) ? $instance['title'] : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of comments to show:' ); ?></label>
			<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
		</p>
		<?php
	}

	/**
	 * Flushes the Recent Comments widget cache.
	 *
	 * @since 2.8.0
	 *
	 * @deprecated 4.4.0 Fragment caching was removed in favor of split queries.
	 */
	public function flush_widget_cache() {
		_deprecated_function( __METHOD__, '4.4.0' );
	}
}
