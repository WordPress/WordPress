<?php
/**
 * Adds content structures.
 *
 */

/****************************************************************************************/

add_action( 'travelify_main_container', 'travelify_content', 10 );
/**
 * Function to display the content for the single post, single page, archive page, index page etc.
 */
function travelify_content() {
	global $post;
	global $travelify_theme_options_settings;
	$options = $travelify_theme_options_settings;
	if( $post ) {
		$layout = get_post_meta( $post->ID, 'travelify_sidebarlayout', true );
	}
	if( empty( $layout ) || is_archive() || is_search() || is_home() ) {
		$layout = 'default';
	}
   if( 'default' == $layout ) {
		$themeoption_layout = $options[ 'default_layout' ];

		if( 'left-sidebar' == $themeoption_layout ) {
			get_template_part( 'content','leftsidebar' );
		}
		elseif( 'right-sidebar' == $themeoption_layout ) {
			get_template_part( 'content','rightsidebar' );
		}
		else {
			get_template_part( 'content','nosidebar' );
		}
   }
   elseif( 'left-sidebar' == $layout ) {
      get_template_part( 'content','leftsidebar' );
   }
   elseif( 'right-sidebar' == $layout ) {
      get_template_part( 'content','rightsidebar' );
   }
   else {
      get_template_part( 'content','nosidebar' );
   }

}

/****************************************************************************************/

add_action( 'travelify_before_loop_content', 'travelify_loop_before', 10 );
/**
 * Contains the opening div
 */
function travelify_loop_before() {
	echo '<div id="content">';
}

/****************************************************************************************/

add_action( 'travelify_loop_content', 'travelify_theloop', 10 );
/**
 * Shows the loop content
 */
function travelify_theloop() {
	if( is_page() ) {
		if( is_page_template( 'templates/template-blog-large-image.php' ) ) {
			travelify_theloop_for_template_blog_image_large();
		}
		elseif( is_page_template( 'templates/template-blog-medium-image.php' ) ) {
			travelify_theloop_for_template_blog_image_medium();
		}
		elseif( is_page_template( 'templates/template-blog-full-content.php' ) ) {
			travelify_theloop_for_template_blog_full_content();
		}
		else {
			travelify_theloop_for_page();
		}
	}
	elseif( is_single() ) {
		travelify_theloop_for_single();
	}
	elseif( is_search() ) {
		travelify_theloop_for_search();
	}
	else {
		travelify_theloop_for_archive();
	}
}

/****************************************************************************************/

if ( ! function_exists( 'travelify_theloop_for_archive' ) ) :
/**
 * Function to show the archive loop content.
 */
function travelify_theloop_for_archive() {
	global $post;

	if( have_posts() ) {
		while( have_posts() ) {
			the_post();

			do_action( 'travelify_before_post' );
?>
	<section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<article>

			<?php do_action( 'travelify_before_post_header' ); ?>

  			<?php do_action( 'travelify_after_post_header' ); ?>

  			<?php do_action( 'travelify_before_post_content' ); ?>

			<?php
			if( has_post_thumbnail() ) {
				$image = '';
	     		$title_attribute = apply_filters( 'the_title', get_the_title( $post->ID ) );
	     		$image .= '<figure class="post-featured-image">';
	  			$image .= '<a href="' . get_permalink() . '" title="'.the_title( '', '', false ).'">';
	  			$image .= get_the_post_thumbnail( $post->ID, 'featured', array( 'title' => esc_attr( $title_attribute ), 'alt' => esc_attr( $title_attribute ) ) ).'</a>';
	  			$image .= '</figure>';

	  			echo $image;
	  		}
  			?>
			<header class="entry-header">
    			<h2 class="entry-title">
    				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a>
    			</h2><!-- .entry-title -->
  			</header>

  			<div class="entry-content clearfix">
    			<?php the_excerpt(); ?>
  			</div>

  			<?php do_action( 'travelify_after_post_content' ); ?>

  			<?php do_action( 'travelify_before_post_meta' ); ?>

  			<div class="entry-meta-bar clearfix">
    			<div class="entry-meta">
	    				<?php travelify_posted_on(); ?>
	    				<?php if( has_category() ) { ?>
	             		<span class="category"><?php the_category(', '); ?></span>
	             	<?php } ?>
	    				<?php if ( comments_open() ) { ?>
	             		<span class="comments"><?php comments_popup_link( __( 'No Comments', 'travelify' ), __( '1 Comment', 'travelify' ), __( '% Comments', 'travelify' ), '', __( 'Comments Off', 'travelify' ) ); ?></span>
	             	<?php } ?>
    			</div><!-- .entry-meta -->
    			<?php
    			echo '<a class="readmore" href="' . get_permalink() . '" title="'.the_title( '', '', false ).'">'.__( 'Read more', 'travelify' ).'</a>';
    			?>
    		</div>

    		<?php do_action( 'travelify_after_post_meta' ); ?>

		</article>
	</section>
<?php
			do_action( 'travelify_after_post' );

		}
	}
	else {
		?>
		<h1 class="entry-title"><?php _e( 'No Posts Found.', 'travelify' ); ?></h1>
      <?php
   }
}
endif;

/****************************************************************************************/

if ( ! function_exists( 'travelify_theloop_for_page' ) ) :
/**
 * Function to show the page content.
 */
function travelify_theloop_for_page() {
	global $post;

	if( have_posts() ) {
		while( have_posts() ) {
			the_post();

			do_action( 'travelify_before_post' );
?>
	<section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<article>

			<?php do_action( 'travelify_before_post_header' ); ?>

			<header class="entry-header">
    			<h2 class="entry-title">
    				<?php the_title(); ?>
    			</h2><!-- .entry-title -->
  			</header>

  			<?php do_action( 'travelify_after_post_header' ); ?>

  			<?php do_action( 'travelify_before_post_content' ); ?>

  			<div class="entry-content clearfix">
    			<?php the_content(); ?>
    			<?php
    				wp_link_pages( array(
						'before'            => '<div style="clear: both;"></div><div class="pagination clearfix">'.__( 'Pages:', 'travelify' ),
						'after'             => '</div>',
						'link_before'       => '<span>',
						'link_after'        => '</span>',
						'pagelink'          => '%',
						'echo'              => 1
               ) );
    			?>
  			</div>

  			<?php

  			do_action( 'travelify_after_post_content' );

  			do_action( 'travelify_before_comments_template' );

         comments_template();

         do_action ( 'travelify_after_comments_template' );

         ?>

		</article>
	</section>
<?php
			do_action( 'travelify_after_post' );

		}
	}
	else {
		?>
		<h1 class="entry-title"><?php _e( 'No Posts Found.', 'travelify' ); ?></h1>
      <?php
   }
}
endif;

/****************************************************************************************/

if ( ! function_exists( 'travelify_theloop_for_single' ) ) :
/**
 * Function to show the single post content.
 */
function travelify_theloop_for_single() {
	global $post;

	if( have_posts() ) {
		while( have_posts() ) {
			the_post();

			do_action( 'travelify_before_post' );
?>
	<section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<article>

			<?php do_action( 'travelify_before_post_header' ); ?>

			<header class="entry-header">
    			<h2 class="entry-title">
    				<?php the_title(); ?>
    			</h2><!-- .entry-title -->
  		</header>

  		<?php do_action( 'travelify_after_post_header' ); ?>

  		<?php do_action( 'travelify_before_post_meta' ); ?>

  			<div class="entry-meta-bar clearfix">
    			<div class="entry-meta">
	    				<?php travelify_posted_on(); ?>
	    				<?php if( has_category() ) { ?>
	             		<span class="category"><?php the_category(', '); ?></span>
	             	<?php } ?>
	    				<?php if ( comments_open() ) { ?>
	             		<span class="comments"><?php comments_popup_link( __( 'No Comments', 'travelify' ), __( '1 Comment', 'travelify' ), __( '% Comments', 'travelify' ), '', __( 'Comments Off', 'travelify' ) ); ?></span>
	             	<?php } ?>
    			</div><!-- .entry-meta -->
    		</div>

				<?php do_action( 'travelify_after_post_meta' ); ?>

				<?php do_action( 'travelify_before_post_content' ); ?>

  			<div class="entry-content clearfix">
    			<?php the_content();
    			if( is_single() ) {
						$tag_list = get_the_tag_list( '', __( ', ', 'travelify' ) );

						if( !empty( $tag_list ) ) {
							?>
							<div class="tags">
								<?php echo $tag_list; ?>
							</div>
							<?php
						}
					}

               wp_link_pages( array(
						'before'            => '<div style="clear: both;"></div><div class="pagination clearfix">'.__( 'Pages:', 'travelify' ),
						'after'             => '</div>',
						'link_before'       => '<span>',
						'link_after'        => '</span>',
						'pagelink'          => '%',
						'echo'              => 1
               ) );
               ?>
  			</div>

  			<?php

  			do_action( 'travelify_after_post_content' );

  			do_action( 'travelify_before_comments_template' );

         comments_template();

         do_action ( 'travelify_after_comments_template' );

         ?>

		</article>
	</section>
<?php
			do_action( 'travelify_after_post' );

		}
	}
	else {
		?>
		<h1 class="entry-title"><?php _e( 'No Posts Found.', 'travelify' ); ?></h1>
      <?php
   }
}
endif;

/****************************************************************************************/

if ( ! function_exists( 'travelify_theloop_for_search' ) ) :
/**
 * Function to show the search results.
 */
function travelify_theloop_for_search() {
	global $post;

	if( have_posts() ) {
		while( have_posts() ) {
			the_post();

			do_action( 'travelify_before_post' );
?>
	<section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<article>

			<?php do_action( 'travelify_before_post_header' ); ?>

			<header class="entry-header">
    			<h2 class="entry-title">
    				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a>
    			</h2><!-- .entry-title -->
  		</header>

  		<?php do_action( 'travelify_after_post_header' ); ?>

  			<?php do_action( 'travelify_before_post_content' ); ?>

  			<div class="entry-content clearfix">
    			<?php the_excerpt(); ?>
  			</div>

  			<?php do_action( 'travelify_after_post_content' ); ?>

		</article>
	</section>
<?php
			do_action( 'travelify_after_post' );

		}
	}
	else {
		?>
		<h1 class="entry-title"><?php _e( 'No Posts Found.', 'travelify' ); ?></h1>
      <?php
   }
}
endif;

/****************************************************************************************/

if ( ! function_exists( 'travelify_theloop_for_template_blog_image_large' ) ) :
/**
 * Function to show the content of page template blog image large content.
 */
function travelify_theloop_for_template_blog_image_large() {
	global $post;

   global $wp_query, $paged;
	if( get_query_var( 'paged' ) ) {
		$paged = get_query_var( 'paged' );
	}
	elseif( get_query_var( 'page' ) ) {
		$paged = get_query_var( 'page' );
	}
	else {
		$paged = 1;
	}
	$blog_query = new WP_Query( array( 'post_type' => 'post', 'paged' => $paged ) );
	$temp_query = $wp_query;
	$wp_query = null;
	$wp_query = $blog_query;

	if( $blog_query->have_posts() ) {
		while( $blog_query->have_posts() ) {
			$blog_query->the_post();

			do_action( 'travelify_before_post' );
?>
	<section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<article>

			<?php do_action( 'travelify_before_post_header' ); ?>

  			<?php do_action( 'travelify_after_post_header' ); ?>

  			<?php do_action( 'travelify_before_post_content' ); ?>

			<?php
			if( has_post_thumbnail() ) {
				$image = '';
	     		$title_attribute = apply_filters( 'the_title', get_the_title( $post->ID ) );
	     		$image .= '<figure class="post-featured-image">';
	  			$image .= '<a href="' . get_permalink() . '" title="'.the_title( '', '', false ).'">';
	  			$image .= get_the_post_thumbnail( $post->ID, 'featured', array( 'title' => esc_attr( $title_attribute ), 'alt' => esc_attr( $title_attribute ) ) ).'</a>';
	  			$image .= '</figure>';

	  			echo $image;
	  		}
  			?>
  			<header class="entry-header">
    			<h2 class="entry-title">
    				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a>
    			</h2><!-- .entry-title -->
  			</header>
  			<div class="entry-content clearfix">
    			<?php the_excerpt(); ?>
  			</div>

  			<?php do_action( 'travelify_after_post_content' ); ?>

  			<?php do_action( 'travelify_before_post_meta' ); ?>

  			<div class="entry-meta-bar clearfix">
    			<div class="entry-meta">
	    				<?php travelify_posted_on(); ?>
	    				<?php if( has_category() ) { ?>
	             		<span class="category"><?php the_category(', '); ?></span>
	             	<?php } ?>
	    				<?php if ( comments_open() ) { ?>
	             		<span class="comments"><?php comments_popup_link( __( 'No Comments', 'travelify' ), __( '1 Comment', 'travelify' ), __( '% Comments', 'travelify' ), '', __( 'Comments Off', 'travelify' ) ); ?></span>
	             	<?php } ?>
    			</div><!-- .entry-meta -->
    			<?php
    			echo '<a class="readmore" href="' . get_permalink() . '" title="'.the_title( '', '', false ).'">'.__( 'Read more', 'travelify' ).'</a>';
    			?>
    		</div>

    		<?php do_action( 'travelify_after_post_meta' ); ?>

		</article>
	</section>
<?php
			do_action( 'travelify_after_post' );

		}
		if ( function_exists('wp_pagenavi' ) ) {
			wp_pagenavi();
		}
		else {
			if ( $wp_query->max_num_pages > 1 ) {
			?>
				<ul class="default-wp-page clearfix">
					<li class="previous"><?php next_posts_link( __( '&laquo; Previous', 'travelify' ), $wp_query->max_num_pages ); ?></li>
					<li class="next"><?php previous_posts_link( __( 'Next &raquo;', 'travelify' ), $wp_query->max_num_pages ); ?></li>
				</ul>
				<?php
			}
		}
	}
	else {
		?>
		<h1 class="entry-title"><?php _e( 'No Posts Found.', 'travelify' ); ?></h1>
      <?php
   }
   $wp_query = $temp_query;
	wp_reset_postdata();
}
endif;

/****************************************************************************************/

if ( ! function_exists( 'travelify_theloop_for_template_blog_image_medium' ) ) :
/**
 * Function to show the content of page template blog image medium content.
 */
function travelify_theloop_for_template_blog_image_medium() {
	global $post;

	global $wp_query, $paged;
	if( get_query_var( 'paged' ) ) {
		$paged = get_query_var( 'paged' );
	}
	elseif( get_query_var( 'page' ) ) {
		$paged = get_query_var( 'page' );
	}
	else {
		$paged = 1;
	}
	$blog_query = new WP_Query( array( 'post_type' => 'post', 'paged' => $paged ) );
	$temp_query = $wp_query;
	$wp_query = null;
	$wp_query = $blog_query;

	if( $blog_query->have_posts() ) {
		while( $blog_query->have_posts() ) {
			$blog_query->the_post();

			do_action( 'travelify_before_post' );
?>
	<section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<article>
		<div class="medium-wrap">
			<?php do_action( 'travelify_before_post_header' ); ?>
			<header class="entry-header">
    			<h2 class="entry-title">
    				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a>
    			</h2><!-- .entry-title -->
  			</header>

  			<?php do_action( 'travelify_after_post_header' ); ?>

  			<?php do_action( 'travelify_before_post_content' ); ?>

			<?php
			if( has_post_thumbnail() ) {
				$image = '';
	     		$title_attribute = apply_filters( 'the_title', get_the_title( $post->ID ) );
	     		$image .= '<figure class="post-featured-image">';
	  			$image .= '<a href="' . get_permalink() . '" title="'.the_title( '', '', false ).'">';
	  			$image .= get_the_post_thumbnail( $post->ID, 'featured-medium', array( 'title' => esc_attr( $title_attribute ), 'alt' => esc_attr( $title_attribute ) ) ).'</a>';
	  			$image .= '</figure>';

	  			echo $image;
	  		}
  			?>

    		<?php the_excerpt(); ?>


  			<?php do_action( 'travelify_after_post_content' ); ?>

  			<?php do_action( 'travelify_before_post_meta' ); ?>
		</div>
  			<div class="entry-meta-bar clearfix">
    			<div class="entry-meta">
	    				<?php travelify_posted_on(); ?>
	    				<?php if( has_category() ) { ?>
	             		<span class="category"><?php the_category(', '); ?></span>
	             	<?php } ?>
	    				<?php if ( comments_open() ) { ?>
	             		<span class="comments"><?php comments_popup_link( __( 'No Comments', 'travelify' ), __( '1 Comment', 'travelify' ), __( '% Comments', 'travelify' ), '', __( 'Comments Off', 'travelify' ) ); ?></span>
	             	<?php } ?>
    			</div><!-- .entry-meta -->
    			<?php
    			echo '<a class="readmore" href="' . get_permalink() . '" title="'.the_title( '', '', false ).'">'.__( 'Read more', 'travelify' ).'</a>';
    			?>
    		</div>

    		<?php do_action( 'travelify_after_post_meta' ); ?>
		</article>
	</section>
<?php
			do_action( 'travelify_after_post' );

		}
		if ( function_exists('wp_pagenavi' ) ) {
			wp_pagenavi();
		}
		else {
			if ( $wp_query->max_num_pages > 1 ) {
			?>
				<ul class="default-wp-page clearfix">
					<li class="previous"><?php next_posts_link( __( '&laquo; Previous', 'travelify' ), $wp_query->max_num_pages ); ?></li>
					<li class="next"><?php previous_posts_link( __( 'Next &raquo;', 'travelify' ), $wp_query->max_num_pages ); ?></li>
				</ul>
				<?php
			}
		}
	}
	else {
		?>
		<h1 class="entry-title"><?php _e( 'No Posts Found.', 'travelify' ); ?></h1>
      <?php
   }
   $wp_query = $temp_query;
	wp_reset_postdata();
}
endif;
/****************************************************************************************/

if ( ! function_exists( 'travelify_theloop_for_template_blog_full_content' ) ) :
/**
 * Function to show the content of page template full content display.
 */
function travelify_theloop_for_template_blog_full_content() {
	global $post;

	global $wp_query, $paged;
	if( get_query_var( 'paged' ) ) {
		$paged = get_query_var( 'paged' );
	}
	elseif( get_query_var( 'page' ) ) {
		$paged = get_query_var( 'page' );
	}
	else {
		$paged = 1;
	}
	$blog_query = new WP_Query( array( 'post_type' => 'post', 'paged' => $paged ) );
	$temp_query = $wp_query;
	$wp_query = null;
	$wp_query = $blog_query;

	global $more;    // Declare global $more (before the loop).

	if( $blog_query->have_posts() ) {
		while( $blog_query->have_posts() ) {
			$blog_query->the_post();

			do_action( 'travelify_before_post' );
?>
	<section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<article>

			<?php do_action( 'travelify_before_post_header' ); ?>

			<header class="entry-header">
    			<h2 class="entry-title">
    				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a>
    			</h2><!-- .entry-title -->
  			</header>

  			<?php do_action( 'travelify_after_post_header' ); ?>

  			<?php do_action( 'travelify_before_post_content' ); ?>

  			<div class="entry-content clearfix">
    			<?php
    				$more = 0;       // Set (inside the loop) to display content above the more tag.

    				the_content( __( 'Read more', 'travelify' ) );

    				wp_link_pages( array(
						'before'            => '<div style="clear: both;"></div><div class="pagination clearfix">'.__( 'Pages:', 'travelify' ),
						'after'             => '</div>',
						'link_before'       => '<span>',
						'link_after'        => '</span>',
						'pagelink'          => '%',
						'echo'              => 1
               ) );
    			 ?>
  			</div>

  			<?php do_action( 'travelify_after_post_content' ); ?>

  			<?php do_action( 'travelify_before_post_meta' ); ?>

  			<div class="entry-meta-bar clearfix">
    			<div class="entry-meta">
	    				<?php travelify_posted_on(); ?>
	    				<?php if( has_category() ) { ?>
	             		<span class="category"><?php the_category(', '); ?></span>
	             	<?php } ?>
	    				<?php if ( comments_open() ) { ?>
	             		<span class="comments"><?php comments_popup_link( __( 'No Comments', 'travelify' ), __( '1 Comment', 'travelify' ), __( '% Comments', 'travelify' ), '', __( 'Comments Off', 'travelify' ) ); ?></span>
	             	<?php } ?>
    			</div><!-- .entry-meta -->
    		</div>

    		<?php do_action( 'travelify_after_post_meta' ); ?>

		</article>
	</section>
<?php
			do_action( 'travelify_after_post' );

		}
		if ( function_exists('wp_pagenavi' ) ) {
			wp_pagenavi();
		}
		else {
			if ( $wp_query->max_num_pages > 1 ) {
			?>
				<ul class="default-wp-page clearfix">
					<li class="previous"><?php next_posts_link( __( '&laquo; Previous', 'travelify' ), $wp_query->max_num_pages ); ?></li>
					<li class="next"><?php previous_posts_link( __( 'Next &raquo;', 'travelify' ), $wp_query->max_num_pages ); ?></li>
				</ul>
				<?php
			}
		}
	}
	else {
		?>
		<h1 class="entry-title"><?php _e( 'No Posts Found.', 'travelify' ); ?></h1>
      <?php
   }
   $wp_query = $temp_query;
	wp_reset_postdata();
}
endif;

/****************************************************************************************/

add_action( 'travelify_after_loop_content', 'travelify_next_previous', 5 );
/**
 * Shows the next or previous posts
 */
function travelify_next_previous() {
	if( is_archive() || is_home() || is_search() ) {
		/**
		 * Checking WP-PageNaviplugin exist
		 */
		if ( function_exists('wp_pagenavi' ) ) :
			wp_pagenavi();

		else:
			global $wp_query;
			if ( $wp_query->max_num_pages > 1 ) :
			?>
			<ul class="default-wp-page clearfix">
				<li class="previous"><?php next_posts_link( __( '&laquo; Previous', 'travelify' ) ); ?></li>
				<li class="next"><?php previous_posts_link( __( 'Next &raquo;', 'travelify' ) ); ?></li>
			</ul>
			<?php
			endif;
		endif;
	}
}

/****************************************************************************************/

add_action( 'travelify_after_post_content', 'travelify_next_previous_post_link', 10 );
/**
 * Shows the next or previous posts link with respective names.
 */
function travelify_next_previous_post_link() {
	if ( is_single() ) {
		if( is_attachment() ) {
		?>
			<ul class="default-wp-page clearfix">
				<li class="previous"><?php previous_image_link( false, __( '&laquo; Previous', 'travelify' ) ); ?></li>
				<li class="next"><?php next_image_link( false, __( 'Next &raquo;', 'travelify' ) ); ?></li>
			</ul>
		<?php
		}
		else {
		?>
			<ul class="default-wp-page clearfix">
				<li class="previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'travelify' ) . '</span> %title' ); ?></li>
				<li class="next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'travelify' ) . '</span>' ); ?></li>
			</ul>
		<?php
		}
	}
}

/****************************************************************************************/

add_action( 'travelify_after_loop_content', 'travelify_loop_after', 10 );
/**
 * Contains the closing div
 */
function travelify_loop_after() {
	echo '</div><!-- #content -->';
}

/****************************************************************************************/

if ( ! function_exists( 'travelify_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own travelify_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
function travelify_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'travelify' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'travelify' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<header class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 44 );
					printf( '<cite class="fn">%1$s %2$s</cite>',
						get_comment_author_link(),
						// If current post author is also comment author, make it known visually.
						( $comment->user_id === $post->post_author ) ? '<span> ' . __( 'Post author', 'travelify' ) . '</span>' : ''
					);
					printf( '<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						/* translators: 1: date, 2: time */
						sprintf( __( '%1$s at %2$s', 'travelify' ), get_comment_date(), get_comment_time() )
					);
				?>
			</header><!-- .comment-meta -->

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'travelify' ); ?></p>
			<?php endif; ?>

			<section class="comment-content comment">
				<?php comment_text(); ?>
				<?php edit_comment_link( __( 'Edit', 'travelify' ), '<p class="edit-link">', '</p>' ); ?>
			</section><!-- .comment-content -->

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'travelify' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
endif;

?>