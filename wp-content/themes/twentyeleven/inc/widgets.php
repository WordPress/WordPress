<?php
/**
 * Makes a custom Widget for displaying Aside, Link, Status, and Quote Posts available with Twenty Eleven
 *
 * Learn more: http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
class Twenty_Eleven_Ephemera_Widget extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function Twenty_Eleven_Ephemera_Widget() {
		$widget_ops = array( 'classname' => 'widget_twentyeleven_ephemera', 'description' => __( 'Use this widget to list your recent Aside, Status, Quote, and Link posts', 'twentyeleven' ) );
		$this->WP_Widget( 'widget_twentyeleven_ephemera', __( 'Twenty Eleven Ephemera', 'twentyeleven' ), $widget_ops );
		$this->alt_option_name = 'widget_twentyeleven_ephemera';

		add_action( 'save_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache' ) );
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @param array An array of standard parameters for widgets in this theme
	 * @param array An array of settings for this widget instance
	 * @return void Echoes its output
	 **/
	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_twentyeleven_ephemera', 'widget' );

		if ( !is_array( $cache ) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = null;

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract( $args, EXTR_SKIP );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Ephemera', 'twentyeleven' ) : $instance['title'], $instance, $this->id_base);

		if ( ! isset( $instance['number'] ) )
			$instance['number'] = '10';

		if ( ! $number = absint( $instance['number'] ) )
 			$number = 10;

		$ephemera_args = array(
			'order' => 'DESC',
			'posts_per_page' => $number,
			'no_found_rows' => true,
			'post_status' => 'publish',
			'post__not_in' => get_option( 'sticky_posts' ),
			'tax_query' => array(
				array(
					'taxonomy' => 'post_format',
					'terms' => array( 'post-format-aside', 'post-format-link', 'post-format-status', 'post-format-quote' ),
					'field' => 'slug',
					'operator' => 'IN',
				),
			),
		);
		$ephemera = new WP_Query( $ephemera_args );

		if ( $ephemera->have_posts() ) :
			echo $before_widget;
			echo $before_title;
			echo $title; // Can set this with a widget option, or omit altogether
			echo $after_title;
			?>
			<ol>
			<?php while ( $ephemera->have_posts() ) : $ephemera->the_post(); ?>

				<?php if ( 'link' != get_post_format() ) : ?>

				<li class="widget-entry-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentyeleven' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
					<span class="comments-link">
						<?php comments_popup_link( __( '0 <span class="reply">comments &rarr;</span>', 'twentyeleven' ), __( '1 <span class="reply">comment &rarr;</span>', 'twentyeleven' ), __( '% <span class="reply">comments &rarr;</span>', 'twentyeleven' ) ); ?>
					</span>
				</li>

				<?php else : ?>

				<li class="widget-entry-title">
					<?php
						// Grab first link from the post content. If none found, use the post permalink as fallback.
						$link_url = twentyeleven_url_grabber();

						if ( empty( $link_url ) )
							$link_url = get_permalink();
					?>
					<a href="<?php echo esc_url( $link_url ); ?>" title="<?php echo esc_attr( sprintf( __( 'Link to %s', 'twentyeleven' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?>&nbsp;<span>&rarr;</span></a>
					<span class="comments-link">
						<?php comments_popup_link( __( '0 <span class="reply">comments &rarr;</span>', 'twentyeleven' ), __( '1 <span class="reply">comment &rarr;</span>', 'twentyeleven' ), __( '% <span class="reply">comments &rarr;</span>', 'twentyeleven' ) ); ?>
					</span>
				</li>

				<?php endif; ?>

			<?php endwhile; ?>
			</ol>
			<?php

			echo $after_widget;

			// Reset the post globals as this query will have stomped on it
			wp_reset_postdata();

		// end check for ephemeral posts
		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_twentyeleven_ephemera', $cache, 'widget' );
	}

	/**
	 * Deals with the settings when they are saved by the admin. Here is
	 * where any validation should be dealt with.
	 **/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_twentyeleven_ephemera'] ) )
			delete_option( 'widget_twentyeleven_ephemera' );

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_twentyeleven_ephemera', 'widget' );
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 **/
	function form( $instance ) {
		$title = isset( $instance['title']) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 10;
?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'twentyeleven' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of posts to show:', 'twentyeleven' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>
		<?php
	}
}