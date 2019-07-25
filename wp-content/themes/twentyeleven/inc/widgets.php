<?php
/**
 * Widget For displaying post format posts
 *
 * Handles displaying Aside, Link, Status, and Quote Posts available with Twenty Eleven.
 *
 * @link https://developer.wordpress.org/themes/functionality/widgets/#developing-widgets
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
class Twenty_Eleven_Ephemera_Widget extends WP_Widget {

	/**
	 * PHP5 constructor.
	 *
	 * @since Twenty Eleven 2.2
	 */
	function __construct() {
		parent::__construct(
			'widget_twentyeleven_ephemera',
			__( 'Twenty Eleven Ephemera', 'twentyeleven' ),
			array(
				'classname'                   => 'widget_twentyeleven_ephemera',
				'description'                 => __( 'Use this widget to list your recent Aside, Status, Quote, and Link posts', 'twentyeleven' ),
				'customize_selective_refresh' => true,
			)
		);
		$this->alt_option_name = 'widget_twentyeleven_ephemera';

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}

	/**
	 * PHP4 constructor.
	 *
	 * @since Twenty Eleven 1.0
	 */
	function Twenty_Eleven_Ephemera_Widget() {
		self::__construct();
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @since Twenty Eleven 1.0
	 *
	 * @param array $args     An array of standard parameters for widgets in this theme.
	 * @param array $instance An array of settings for this widget instance.
	 */
	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_twentyeleven_ephemera', 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = null;
		}

		if ( ! is_customize_preview() && isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();

		/** This filter is documented in wp-includes/default-widgets.php */
		$args['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Ephemera', 'twentyeleven' ) : $instance['title'], $instance, $this->id_base );

		if ( ! isset( $instance['number'] ) ) {
			$instance['number'] = '10';
		}

		$args['number'] = absint( $instance['number'] );
		if ( ! $args['number'] ) {
			$args['number'] = 10;
		}

		$ephemera_args = array(
			'order'          => 'DESC',
			'posts_per_page' => $args['number'],
			'no_found_rows'  => true,
			'post_status'    => 'publish',
			'post__not_in'   => get_option( 'sticky_posts' ),
			'tax_query'      => array(
				array(
					'taxonomy' => 'post_format',
					'terms'    => array( 'post-format-aside', 'post-format-link', 'post-format-status', 'post-format-quote' ),
					'field'    => 'slug',
					'operator' => 'IN',
				),
			),
		);
		$ephemera      = new WP_Query( $ephemera_args );

		if ( $ephemera->have_posts() ) :
			echo $args['before_widget'];
			echo $args['before_title'];
			echo $args['title'];
			echo $args['after_title'];
			?>
			<ol>
			<?php
			while ( $ephemera->have_posts() ) :
				$ephemera->the_post();
				?>

				<?php if ( 'link' != get_post_format() ) : ?>

				<li class="widget-entry-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php the_title(); ?></a>
					<span class="comments-link">
						<?php comments_popup_link( __( '0 <span class="reply">comments &rarr;</span>', 'twentyeleven' ), __( '1 <span class="reply">comment &rarr;</span>', 'twentyeleven' ), __( '% <span class="reply">comments &rarr;</span>', 'twentyeleven' ) ); ?>
					</span>
				</li>

				<?php else : ?>

				<li class="widget-entry-title">
					<a href="<?php echo esc_url( twentyeleven_get_first_url() ); ?>" rel="bookmark"><?php the_title(); ?>&nbsp;<span>&rarr;</span></a>
					<span class="comments-link">
						<?php comments_popup_link( __( '0 <span class="reply">comments &rarr;</span>', 'twentyeleven' ), __( '1 <span class="reply">comment &rarr;</span>', 'twentyeleven' ), __( '% <span class="reply">comments &rarr;</span>', 'twentyeleven' ) ); ?>
					</span>
				</li>

				<?php endif; ?>

			<?php endwhile; ?>
			</ol>
			<?php

			echo $args['after_widget'];

			// Reset the post globals as this query will have stomped on it
			wp_reset_postdata();

			// end check for ephemeral posts
		endif;

		$cache[ $args['widget_id'] ] = ob_get_flush();
		if ( ! is_customize_preview() ) {
			wp_cache_set( 'widget_twentyeleven_ephemera', $cache, 'widget' );
		}

	}

	/**
	 * Update widget settings.
	 *
	 * Deals with the settings when they are saved by the admin. Here is
	 * where any validation should be dealt with.
	 *
	 * @since Twenty Eleven 1.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance           = $old_instance;
		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_twentyeleven_ephemera'] ) ) {
			delete_option( 'widget_twentyeleven_ephemera' );
		}

		return $instance;
	}

	/**
	 * Flush widget cache.
	 *
	 * @since Twenty Eleven 1.0
	 */
	function flush_widget_cache() {
		wp_cache_delete( 'widget_twentyeleven_ephemera', 'widget' );
	}

	/**
	 * Set up the widget form.
	 *
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @since Twenty Eleven 1.0
	 */
	function form( $instance ) {
		$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 10;
		?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'twentyeleven' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of posts to show:', 'twentyeleven' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>
		<?php
	}
}
