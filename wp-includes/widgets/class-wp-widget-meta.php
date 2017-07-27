<?php
/**
 * Widget API: WP_Widget_Meta class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core class used to implement a Meta widget.
 *
 * Displays log in/out, RSS feed links, etc.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class WP_Widget_Meta extends WP_Widget {

	/**
	 * Sets up a new Meta widget instance.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_meta',
			'description' => __( 'Login, RSS, &amp; WordPress.org links.' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'meta', __( 'Meta' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current Meta widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Meta widget instance.
	 */
	public function widget( $args, $instance ) {
		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', empty($instance['title']) ? __( 'Meta' ) : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
			?>
			<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<li><a href="<?php echo esc_url( get_bloginfo( 'rss2_url' ) ); ?>"><?php _e('Entries <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
			<li><a href="<?php echo esc_url( get_bloginfo( 'comments_rss2_url' ) ); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
			<?php
			/**
			 * Filters the "Powered by WordPress" text in the Meta widget.
			 *
			 * @since 3.6.0
			 *
			 * @param string $title_text Default title text for the WordPress.org link.
			 */
			echo apply_filters( 'widget_meta_poweredby', sprintf( '<li><a href="%s" title="%s">%s</a></li>',
				esc_url( __( 'https://wordpress.org/' ) ),
				esc_attr__( 'Powered by WordPress, state-of-the-art semantic personal publishing platform.' ),
				_x( 'WordPress.org', 'meta widget link text' )
			) );

			wp_meta();
			?>
			</ul>
			<?php
		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current Meta widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Outputs the settings form for the Meta widget.
	 *
	 * @since 2.8.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = sanitize_text_field( $instance['title'] );
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
<?php
	}
}
