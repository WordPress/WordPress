<?php
/**
 * Widget API: WP_Widget_RSS class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core class used to implement a RSS widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class WP_Widget_RSS extends WP_Widget {

	/**
	 * Sets up a new RSS widget instance.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		$widget_ops = array(
			'description'                 => __( 'Entries from any RSS or Atom feed.' ),
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,

		);
		$control_ops = array(
			'width'  => 400,
			'height' => 200,
		);
		parent::__construct( 'rss', __( 'RSS' ), $widget_ops, $control_ops );
	}

	/**
	 * Outputs the content for the current RSS widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current RSS widget instance.
	 */
	public function widget( $args, $instance ) {
		if ( isset( $instance['error'] ) && $instance['error'] ) {
			return;
		}

		$url = ! empty( $instance['url'] ) ? $instance['url'] : '';
		while ( ! empty( $url ) && stristr( $url, 'http' ) !== $url ) {
			$url = substr( $url, 1 );
		}

		if ( empty( $url ) ) {
			return;
		}

		// Self-URL destruction sequence.
		if ( in_array( untrailingslashit( $url ), array( site_url(), home_url() ), true ) ) {
			return;
		}

		$rss   = fetch_feed( $url );
		$title = $instance['title'];
		$desc  = '';
		$link  = '';

		if ( ! is_wp_error( $rss ) ) {
			$desc = esc_attr( strip_tags( html_entity_decode( $rss->get_description(), ENT_QUOTES, get_option( 'blog_charset' ) ) ) );
			if ( empty( $title ) ) {
				$title = strip_tags( $rss->get_title() );
			}
			$link = strip_tags( $rss->get_permalink() );
			while ( ! empty( $link ) && stristr( $link, 'http' ) !== $link ) {
				$link = substr( $link, 1 );
			}
		}

		if ( empty( $title ) ) {
			$title = ! empty( $desc ) ? $desc : __( 'Unknown Feed' );
		}

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		if ( $title ) {
			$feed_link = '';
			$feed_url  = strip_tags( $url );
			$feed_icon = includes_url( 'images/rss.png' );
			$feed_link = sprintf(
				'<a class="rsswidget rss-widget-feed" href="%1$s"><img class="rss-widget-icon" style="border:0" width="14" height="14" src="%2$s" alt="%3$s"%4$s /></a> ',
				esc_url( $feed_url ),
				esc_url( $feed_icon ),
				esc_attr__( 'RSS' ),
				( wp_lazy_loading_enabled( 'img', 'rss_widget_feed_icon' ) ? ' loading="lazy"' : '' )
			);

			/**
			 * Filters the classic RSS widget's feed icon link.
			 *
			 * Themes can remove the icon link by using `add_filter( 'rss_widget_feed_link', '__return_false' );`.
			 *
			 * @since 5.9.0
			 *
			 * @param string $feed_link HTML for link to RSS feed.
			 * @param array  $instance  Array of settings for the current widget.
			 */
			$feed_link = apply_filters( 'rss_widget_feed_link', $feed_link, $instance );

			$title = $feed_link . '<a class="rsswidget rss-widget-title" href="' . esc_url( $link ) . '">' . esc_html( $title ) . '</a>';
		}

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
			$aria_label = $title ? $title : __( 'RSS Feed' );
			echo '<nav aria-label="' . esc_attr( $aria_label ) . '">';
		}

		wp_widget_rss_output( $rss, $instance );

		if ( 'html5' === $format ) {
			echo '</nav>';
		}

		echo $args['after_widget'];

		if ( ! is_wp_error( $rss ) ) {
			$rss->__destruct();
		}
		unset( $rss );
	}

	/**
	 * Handles updating settings for the current RSS widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$testurl = ( isset( $new_instance['url'] ) && ( ! isset( $old_instance['url'] ) || ( $new_instance['url'] !== $old_instance['url'] ) ) );
		return wp_widget_rss_process( $new_instance, $testurl );
	}

	/**
	 * Outputs the settings form for the RSS widget.
	 *
	 * @since 2.8.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		if ( empty( $instance ) ) {
			$instance = array(
				'title'        => '',
				'url'          => '',
				'items'        => 10,
				'error'        => false,
				'show_summary' => 0,
				'show_author'  => 0,
				'show_date'    => 0,
			);
		}
		$instance['number'] = $this->number;

		wp_widget_rss_form( $instance );
	}
}
