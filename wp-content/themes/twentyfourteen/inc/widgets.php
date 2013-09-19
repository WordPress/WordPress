<?php
/**
 * Makes a custom Widget for displaying Aside, Quote, Video, Image, Gallery,
 * and Link posts, available with Twenty Fourteen.
 *
 * Learn more: http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */

class Twenty_Fourteen_Ephemera_Widget extends WP_Widget {

	/**
	 * The supported post formats.
	 *
	 * @var array
	 */
	private $formats = array( 'aside', 'image', 'video', 'quote', 'link', 'gallery' );

	/**
	 * Pluralized post format strings.
	 *
	 * @var array
	 */
	private $format_strings;

	/**
	 * Constructor.
	 *
	 * @return Twenty_Fourteen_Ephemera_Widget
	 */
	public function __construct() {
		parent::__construct( 'widget_twentyfourteen_ephemera', __( 'Twenty Fourteen Ephemera', 'twentyfourteen' ), array(
			'classname'   => 'widget_twentyfourteen_ephemera',
			'description' => __( 'Use this widget to list your recent Aside, Quote, Video, Image, Gallery, and Link posts', 'twentyfourteen' ),
		) );

		/**
		 * @todo http://core.trac.wordpress.org/ticket/23257
		 */
		$this->format_strings = array(
			'aside'   => __( 'Asides',    'twentyfourteen' ),
			'image'   => __( 'Images',    'twentyfourteen' ),
			'video'   => __( 'Videos',    'twentyfourteen' ),
			'quote'   => __( 'Quotes',    'twentyfourteen' ),
			'link'    => __( 'Links',     'twentyfourteen' ),
			'gallery' => __( 'Galleries', 'twentyfourteen' ),
		);

		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @param array $args An array of standard parameters for widgets in this theme.
	 * @param array $instance An array of settings for this widget instance.
	 * @return void Echoes its output.
	 */
	public function widget( $args, $instance ) {
		// If called directly, assign an unique index for caching.
		if ( -1 == $this->number ) {
			static $num = -1;
			$this->_set( --$num );
		}

		$content = get_transient( $this->id );

		if ( false !== $content ) {
			echo $content;
			return;
		}

		ob_start();
		extract( $args, EXTR_SKIP );

		$format = $instance['format'];
		$number = empty( $instance['number'] ) ? 2 : absint( $instance['number'] );
		$title  = apply_filters( 'widget_title', empty( $instance['title'] ) ? $this->format_strings[ $format ] : $instance['title'], $instance, $this->id_base );

		$ephemera = new WP_Query( array(
			'order'          => 'DESC',
			'posts_per_page' => $number,
			'no_found_rows'  => true,
			'post_status'    => 'publish',
			'post__not_in'   => get_option( 'sticky_posts' ),
			'tax_query'      => array(
				array(
					'taxonomy' => 'post_format',
					'terms'    => array( "post-format-$format" ),
					'field'    => 'slug',
					'operator' => 'IN',
				),
			),
		) );

		if ( $ephemera->have_posts() ) :
			$tmp_content_width = $GLOBALS['content_width'];
			$GLOBALS['content_width'] = 306;

			echo $before_widget;
			?>
			<h1 class="widget-title <?php echo esc_attr( $format ); ?>">
				<a class="entry-format" href="<?php echo esc_url( get_post_format_link( $format ) ); ?>"><?php echo $title; ?></a>
			</h1>
			<ol>

				<?php while ( $ephemera->have_posts() ) : $ephemera->the_post(); ?>
				<li>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="entry-content">
						<?php
							if ( has_post_format( 'gallery' ) ) :
								$images = get_posts( array(
									'post_parent'    => get_post()->post_parent,
									'fields'         => 'ids',
									'numberposts'    => -1,
									'post_status'    => 'inherit',
									'post_type'      => 'attachment',
									'post_mime_type' => 'image',
									'order'          => 'ASC',
									'orderby'        => 'menu_order ID'
								) );
								$total_images = count( $images );

								if ( has_post_thumbnail() ) :
									$featured_image = get_the_post_thumbnail( get_the_ID(), 'featured-thumbnail-formatted' );
								elseif ( $total_images > 0 ) :
									$image          = array_shift( $images );
									$featured_image = wp_get_attachment_image( $image, 'featured-thumbnail-formatted' );
								endif;
						?>
						<a href="<?php the_permalink(); ?>"><?php echo $featured_image; ?></a>
						<p class="wp-caption-text">
							<?php
								printf( _n( 'This gallery contains <a href="%1$s" rel="bookmark">%2$s photo</a>.', 'This gallery contains <a href="%1$s" rel="bookmark">%2$s photos</a>.', $total_images, 'twentyfourteen' ),
									esc_url( get_permalink() ),
									number_format_i18n( $total_images )
								);
							?>
						</p>
						<?php
							else :
								the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ) );
							endif;
						?>
					</div><!-- .entry-content -->

					<header class="entry-header">
						<div class="entry-meta">
							<?php
								if ( ! has_post_format( 'link' ) ) :
									the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
								endif;

								printf( __( '<span class="entry-date"><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a></span> <span class="byline"><span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'twentyfourteen' ),
									esc_url( get_permalink() ),
									esc_attr( get_the_time() ),
									esc_attr( get_the_date( 'c' ) ),
									esc_html( get_the_date() ),
									esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
									esc_attr( sprintf( __( 'View all posts by %s', 'twentyfourteen' ), get_the_author() ) ),
									get_the_author()
								);

								if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) :
							?>
							<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyfourteen' ), __( '1 Comment', 'twentyfourteen' ), __( '% Comments', 'twentyfourteen' ) ); ?></span>
							<?php endif; ?>
						</div><!-- .entry-meta -->
					</header><!-- .entry-header -->
				</article><!-- #post-## -->
				</li>
				<?php endwhile; ?>

			</ol>
			<a class="post-format-archive-link" href="<?php echo esc_url( get_post_format_link( $format ) ); ?>"><?php printf( __( 'More %s <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ), $this->format_strings[ $format ] ); ?></a>
			<?php

			echo $after_widget;

			// Reset the post globals as this query will have stomped on it.
			wp_reset_postdata();

			$GLOBALS['content_width'] = $tmp_content_width;

		endif; // End check for ephemeral posts.

		set_transient( $this->id, ob_get_flush() );
	}

	/**
	 * Deals with the settings when they are saved by the admin. Here is where
	 * any validation should be dealt with.
	 *
	 * @param array $new_instance
	 * @param array $instance
	 * @return array
	 */
	function update( $new_instance, $instance ) {
		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['number'] = empty( $new_instance['number'] ) ? 2 : absint( $new_instance['number'] );
		if ( in_array( $new_instance['format'], $this->formats ) )
			$instance['format'] = $new_instance['format'];

		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * Deletes the transient.
	 *
	 * @return void
	 */
	function flush_widget_cache() {
		delete_transient( $this->id );
	}

	/**
	 * Displays the form for this widget on the Widgets page of the Admin area.
	 *
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {
		$title  = empty( $instance['title'] ) ? '' : esc_attr( $instance['title'] );
		$number = empty( $instance['number'] ) ? 2 : absint( $instance['number'] );
		$format = isset( $instance['format'] ) && in_array( $instance['format'], $this->formats ) ? $instance['format'] : 'aside';
		?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'twentyfourteen' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of posts to show:', 'twentyfourteen' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'format' ) ); ?>"><?php _e( 'Post format to show:', 'twentyfourteen' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'format' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'format' ) ); ?>">
				<?php foreach ( $this->formats as $slug ) : ?>
				<option value="<?php echo esc_attr( $slug ); ?>"<?php selected( $format, $slug ); ?>><?php echo get_post_format_string( $slug ); ?></option>
				<?php endforeach; ?>
			</select>
		<?php
	}
}
