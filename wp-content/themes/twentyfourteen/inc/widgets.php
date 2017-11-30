<?php
/**
 * Custom Widget for displaying specific post formats
 *
 * Displays posts from Aside, Quote, Video, Audio, Image, Gallery, and Link formats.
 *
 * @link https://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

class Twenty_Fourteen_Ephemera_Widget extends WP_Widget {

	/**
	 * The supported post formats.
	 *
	 * @access private
	 * @since Twenty Fourteen 1.0
	 *
	 * @var array
	 */
	private $formats = array( 'aside', 'image', 'video', 'audio', 'quote', 'link', 'gallery' );

	/**
	 * Constructor.
	 *
	 * @since Twenty Fourteen 1.0
	 *
	 * @return Twenty_Fourteen_Ephemera_Widget
	 */
	public function __construct() {
		parent::__construct(
			'widget_twentyfourteen_ephemera', __( 'Twenty Fourteen Ephemera', 'twentyfourteen' ), array(
				'classname'                   => 'widget_twentyfourteen_ephemera',
				'description'                 => __( 'Use this widget to list your recent Aside, Quote, Video, Audio, Image, Gallery, and Link posts.', 'twentyfourteen' ),
				'customize_selective_refresh' => true,
			)
		);

		if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since Twenty Fourteen 1.7
	 */
	public function enqueue_scripts() {
		/** This filter is documented in wp-includes/media.php */
		$audio_library = apply_filters( 'wp_audio_shortcode_library', 'mediaelement' );
		/** This filter is documented in wp-includes/media.php */
		$video_library = apply_filters( 'wp_video_shortcode_library', 'mediaelement' );
		if ( in_array( 'mediaelement', array( $video_library, $audio_library ), true ) ) {
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'mediaelement-vimeo' );
			wp_enqueue_script( 'wp-mediaelement' );
		}
	}

	/**
	 * Output the HTML for this widget.
	 *
	 * @access public
	 * @since Twenty Fourteen 1.0
	 *
	 * @param array $args     An array of standard parameters for widgets in this theme.
	 * @param array $instance An array of settings for this widget instance.
	 */
	public function widget( $args, $instance ) {
		$format = isset( $instance['format'] ) && in_array( $instance['format'], $this->formats ) ? $instance['format'] : 'aside';

		switch ( $format ) {
			case 'image':
				$format_string      = __( 'Images', 'twentyfourteen' );
				$format_string_more = __( 'More images', 'twentyfourteen' );
				break;
			case 'video':
				$format_string      = __( 'Videos', 'twentyfourteen' );
				$format_string_more = __( 'More videos', 'twentyfourteen' );
				break;
			case 'audio':
				$format_string      = __( 'Audio', 'twentyfourteen' );
				$format_string_more = __( 'More audio', 'twentyfourteen' );
				break;
			case 'quote':
				$format_string      = __( 'Quotes', 'twentyfourteen' );
				$format_string_more = __( 'More quotes', 'twentyfourteen' );
				break;
			case 'link':
				$format_string      = __( 'Links', 'twentyfourteen' );
				$format_string_more = __( 'More links', 'twentyfourteen' );
				break;
			case 'gallery':
				$format_string      = __( 'Galleries', 'twentyfourteen' );
				$format_string_more = __( 'More galleries', 'twentyfourteen' );
				break;
			case 'aside':
			default:
				$format_string      = __( 'Asides', 'twentyfourteen' );
				$format_string_more = __( 'More asides', 'twentyfourteen' );
				break;
		}

		$number = empty( $instance['number'] ) ? 2 : absint( $instance['number'] );
		$title  = apply_filters( 'widget_title', empty( $instance['title'] ) ? $format_string : $instance['title'], $instance, $this->id_base );

		$ephemera = new WP_Query(
			array(
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
			)
		);

		if ( $ephemera->have_posts() ) :
			$tmp_content_width        = $GLOBALS['content_width'];
			$GLOBALS['content_width'] = 306;

			echo $args['before_widget'];
			?>
			<h1 class="widget-title <?php echo esc_attr( $format ); ?>">
				<a class="entry-format" href="<?php echo esc_url( get_post_format_link( $format ) ); ?>"><?php echo esc_html( $title ); ?></a>
			</h1>
			<ol>

				<?php
				while ( $ephemera->have_posts() ) :
					$ephemera->the_post();
					$tmp_more        = $GLOBALS['more'];
					$GLOBALS['more'] = 0;
				?>
				<li>
				<article <?php post_class(); ?>>
				<div class="entry-content">
					<?php
					if ( has_post_format( 'gallery' ) ) :

						if ( post_password_required() ) :
							the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ) );
							else :
								$images = array();

								$galleries = get_post_galleries( get_the_ID(), false );
								if ( isset( $galleries[0]['ids'] ) ) {
									$images = explode( ',', $galleries[0]['ids'] );
								}

								if ( ! $images ) :
									$images = get_posts(
										array(
											'fields'      => 'ids',
											'numberposts' => -1,
											'order'       => 'ASC',
											'orderby'     => 'menu_order',
											'post_mime_type' => 'image',
											'post_parent' => get_the_ID(),
											'post_type'   => 'attachment',
										)
									);
								endif;

								$total_images = count( $images );

								if ( has_post_thumbnail() ) :
									$post_thumbnail = get_the_post_thumbnail();
									elseif ( $total_images > 0 ) :
										$image          = reset( $images );
										$post_thumbnail = wp_get_attachment_image( $image, 'post-thumbnail' );
									endif;

									if ( ! empty( $post_thumbnail ) ) :
						?>
						<a href="<?php the_permalink(); ?>"><?php echo $post_thumbnail; ?></a>
						<?php endif; ?>
						<p class="wp-caption-text">
						<?php
							printf(
								_n( 'This gallery contains <a href="%1$s" rel="bookmark">%2$s photo</a>.', 'This gallery contains <a href="%1$s" rel="bookmark">%2$s photos</a>.', $total_images, 'twentyfourteen' ),
								esc_url( get_permalink() ),
								number_format_i18n( $total_images )
							);
						?>
						</p>
						<?php
						endif;

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

							printf(
								'<span class="entry-date"><a href="%1$s" rel="bookmark"><time class="entry-date" datetime="%2$s">%3$s</time></a></span> <span class="byline"><span class="author vcard"><a class="url fn n" href="%4$s" rel="author">%5$s</a></span></span>',
								esc_url( get_permalink() ),
								esc_attr( get_the_date( 'c' ) ),
								esc_html( get_the_date() ),
								esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
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
			<a class="post-format-archive-link" href="<?php echo esc_url( get_post_format_link( $format ) ); ?>">
				<?php
					/* translators: used with More archives link */
					printf( __( '%s <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ), $format_string_more );
				?>
			</a>
			<?php

			echo $args['after_widget'];

			// Reset the post globals as this query will have stomped on it.
			wp_reset_postdata();

			$GLOBALS['more']          = $tmp_more;
			$GLOBALS['content_width'] = $tmp_content_width;

		endif; // End check for ephemeral posts.
	}

	/**
	 * Deal with the settings when they are saved by the admin.
	 *
	 * Here is where any validation should happen.
	 *
	 * @since Twenty Fourteen 1.0
	 *
	 * @param array $new_instance New widget instance.
	 * @param array $instance     Original widget instance.
	 * @return array Updated widget instance.
	 */
	function update( $new_instance, $instance ) {
		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['number'] = empty( $new_instance['number'] ) ? 2 : absint( $new_instance['number'] );
		if ( in_array( $new_instance['format'], $this->formats ) ) {
			$instance['format'] = $new_instance['format'];
		}

		return $instance;
	}

	/**
	 * Display the form for this widget on the Widgets page of the Admin area.
	 *
	 * @since Twenty Fourteen 1.0
	 *
	 * @param array $instance
	 */
	function form( $instance ) {
		$title  = empty( $instance['title'] ) ? '' : esc_attr( $instance['title'] );
		$number = empty( $instance['number'] ) ? 2 : absint( $instance['number'] );
		$format = isset( $instance['format'] ) && in_array( $instance['format'], $this->formats ) ? $instance['format'] : 'aside';
		?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'twentyfourteen' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of posts to show:', 'twentyfourteen' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3"></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'format' ) ); ?>"><?php _e( 'Post format to show:', 'twentyfourteen' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'format' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'format' ) ); ?>">
				<?php foreach ( $this->formats as $slug ) : ?>
				<option value="<?php echo esc_attr( $slug ); ?>"<?php selected( $format, $slug ); ?>><?php echo esc_html( get_post_format_string( $slug ) ); ?></option>
				<?php endforeach; ?>
			</select>
		<?php
	}
}
