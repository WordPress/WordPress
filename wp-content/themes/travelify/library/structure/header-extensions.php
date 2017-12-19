<?php
/**
 * Adds header structures.
 *
 */

/****************************************************************************************/

add_action( 'wp_head', 'travelify_add_meta', 5 );
/**
 * Add meta tags.
 */
function travelify_add_meta() {
?>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<?php
}

/****************************************************************************************/

add_action( 'travelify_links', 'travelify_add_links', 10 );
/**
 * Adding link to stylesheet file
 *
 * @uses get_stylesheet_uri()
 */
function travelify_add_links() {
?>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
}

/****************************************************************************************/

add_action( 'travelify_header', 'travelify_headerdetails', 10 );
/**
 * Shows Header Part Content
 *
 * Shows the site logo, title, description, searchbar, social icons etc.
 */
function travelify_headerdetails() {
?>
	<?php
		global $travelify_theme_options_settings;
   	$options = $travelify_theme_options_settings;

   	$elements = array();
		$elements = array(
			$options[ 'social_facebook' ],
			$options[ 'social_twitter' ],
			$options[ 'social_googleplus' ],
			$options[ 'social_linkedin' ],
			$options[ 'social_pinterest' ],
			$options[ 'social_youtube' ],
			$options[ 'social_vimeo' ],
			$options[ 'social_flickr' ],
			$options[ 'social_tumblr' ],
			$options[ 'social_instagram' ],
			$options[ 'social_rss' ],
			$options[ 'social_github' ]
		);

		$flag = 0;
		if( !empty( $elements ) ) {
			foreach( $elements as $option) {
				if( !empty( $option ) ) {
					$flag = 1;
				}
				else {
					$flag = 0;
				}
				if( 1 == $flag ) {
					break;
				}
			}
		}
	?>

	<div class="container clearfix">
		<div class="hgroup-wrap clearfix">
					<section class="hgroup-right">
						<?php travelify_socialnetworks( $flag ); ?>
					</section><!-- .hgroup-right -->
				<hgroup id="site-logo" class="clearfix">
					<?php
						if( $options[ 'header_show' ] != 'disable-both' && $options[ 'header_show' ] == 'header-text' ) {
						?>
							<h1 id="site-title">
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
									<?php bloginfo( 'name' ); ?>
								</a>
							</h1>
							<h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>
						<?php
						}
						elseif( $options[ 'header_show' ] != 'disable-both' && $options[ 'header_show' ] == 'header-logo' ) {
						?>
							<h1 id="site-title">
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
									<img src="<?php echo $options[ 'header_logo' ]; ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
								</a>
							</h1>
						<?php
						}
						?>

				</hgroup><!-- #site-logo -->

		</div><!-- .hgroup-wrap -->
	</div><!-- .container -->
	<?php $header_image = get_header_image();
			if( !empty( $header_image ) ) :?>
				<img src="<?php echo esc_url( $header_image ); ?>" class="header-image" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
			<?php endif; ?>
	<?php
		if ( has_nav_menu( 'primary' ) ) {
			$args = array(
				'theme_location'    => 'primary',
				'container'         => '',
				'items_wrap'        => '<ul class="root">%3$s</ul>'
			);
			echo '<nav id="main-nav" class="clearfix">
					<div class="container clearfix">';
				wp_nav_menu( $args );
			echo '</div><!-- .container -->
					</nav><!-- #main-nav -->';
		}
		else {
			echo '<nav id="main-nav" class="clearfix">
					<div class="container clearfix">';
				wp_page_menu( array( 'menu_class'  => 'root' ) );
			echo '</div><!-- .container -->
					</nav><!-- #main-nav -->';
		}
	?>
		<?php
		if( is_home() || is_front_page() ) {
			if( "0" == $options[ 'disable_slider' ] ) {
				if( function_exists( 'travelify_pass_cycle_parameters' ) )
   				travelify_pass_cycle_parameters();
   			if( function_exists( 'travelify_featured_post_slider' ) )
   				travelify_featured_post_slider();
   		}
   		}

		else {
			if( ( '' != travelify_header_title() ) || function_exists( 'bcn_display_list' ) ) {
		?>
			<div class="page-title-wrap">
	    		<div class="container clearfix">
	    			<?php
		    		if( function_exists( 'travelify_breadcrumb' ) )
						travelify_breadcrumb();
					?>
				   <h3 class="page-title"><?php echo travelify_header_title(); ?></h3><!-- .page-title -->
				</div>
	    	</div>
	   <?php
	   	}
		}
}

/****************************************************************************************/

if ( ! function_exists( 'travelify_socialnetworks' ) ) :
/**
 * This function for social links display on header
 *
 * Get links through Theme Options
 */
function travelify_socialnetworks( $flag ) {

	global $travelify_theme_options_settings;
   $options = $travelify_theme_options_settings;

	$travelify_socialnetworks = '';
	if ( ( !$travelify_socialnetworks = get_transient( 'travelify_socialnetworks' ) ) && ( 1 == $flag ) )  {

		$travelify_socialnetworks .='
			<div class="social-icons clearfix">
				<ul>';

				$social_links = array(
					'Facebook'    => 'social_facebook',
					'Twitter'     => 'social_twitter',
					'Google-Plus' => 'social_googleplus',
					'Pinterest'   => 'social_pinterest',
					'YouTube'     => 'social_youtube',
					'Vimeo'       => 'social_vimeo',
					'LinkedIn'    => 'social_linkedin',
					'Flickr'      => 'social_flickr',
					'Tumblr'      => 'social_tumblr',
					'Instagram'   => 'social_instagram',
					'RSS'         => 'social_rss',
					'GitHub'      => 'social_github'
				);

				foreach( $social_links as $key => $value ) {
					if ( !empty( $options[ $value ] ) ) {
						$travelify_socialnetworks .=
							'<li class="'.strtolower($key).'"><a href="'.esc_url( $options[ $value ] ).'" title="'.sprintf( esc_attr__( '%1$s on %2$s', 'travelify' ), get_bloginfo( 'name' ), $key ).'" target="_blank"></a></li>';
					}
				}

				$travelify_socialnetworks .='
			</ul>
			</div><!-- .social-icons -->';

		set_transient( 'travelify_socialnetworks', $travelify_socialnetworks, 86940 );
	}
	echo $travelify_socialnetworks;
}
endif;


/****************************************************************************************/

if ( ! function_exists( 'travelify_featured_post_slider' ) ) :
/**
 * display featured post slider
 *
 */
function travelify_featured_post_slider() {
	global $post;

	global $travelify_theme_options_settings;
  	$options = $travelify_theme_options_settings;

  $travelify_featured_post_slider = '';
	if (!empty( $options[ 'featured_post_slider' ] ) ) {
		$travelify_featured_post_slider .= '
		<section class="featured-slider"><div class="slider-cycle">';
			$get_featured_posts = new WP_Query( array(
				'posts_per_page' 		    => $options[ 'slider_quantity' ],
				'post_type'					    => array( 'post', 'page' ),
				'post__in'		 			    => $options[ 'featured_post_slider' ],
				'orderby' 		 			    => 'post__in',
				'suppress_filters' 	    => false,
				'ignore_sticky_posts' 	=> 1 						// ignore sticky posts
			));
			$i=0; while ( $get_featured_posts->have_posts()) : $get_featured_posts->the_post(); $i++;
				$title_attribute = apply_filters( 'the_title', get_the_title( $post->ID ) );
				$excerpt = get_the_excerpt();
				if ( 1 == $i ) { $classes = "slides displayblock"; } else { $classes = "slides displaynone"; }
				$travelify_featured_post_slider .= '
				<div class="'.$classes.'">';
						if( has_post_thumbnail() ) {

							$travelify_featured_post_slider .= '<figure><a href="' . get_permalink() . '" title="'.the_title('','',false).'">';

							$travelify_featured_post_slider .= get_the_post_thumbnail( $post->ID, 'slider', array( 'title' => esc_attr( $title_attribute ), 'alt' => esc_attr( $title_attribute ), 'class'	=> 'pngfix' ) ).'</a></figure>';
						}
						if( $title_attribute != '' || $excerpt !='' ) {
						$travelify_featured_post_slider .= '
							<article class="featured-text">';
							if( $title_attribute !='' ) {
									$travelify_featured_post_slider .= '<div class="featured-title"><a href="' . get_permalink() . '" title="'.the_title('','',false).'">'. get_the_title() . '</a></div><!-- .featured-title -->';
							}
							if( $excerpt !='' ) {
								$travelify_featured_post_slider .= '<div class="featured-content">'.$excerpt.'</div><!-- .featured-content -->';
							}
						$travelify_featured_post_slider .= '
							</article><!-- .featured-text -->';
						}
				$travelify_featured_post_slider .= '
				</div><!-- .slides -->';
			endwhile; wp_reset_query();
		$travelify_featured_post_slider .= '</div>
		<nav id="controllers" class="clearfix">
		</nav><!-- #controllers --></section><!-- .featured-slider -->';
	}
	echo $travelify_featured_post_slider;
}
endif;

/****************************************************************************************/

if ( ! function_exists( 'travelify_breadcrumb' ) ) :
/**
 * Display breadcrumb on header.
 *
 * If the page is home or front page, slider is displayed.
 * In other pages, breadcrumb will display if breadcrumb NavXT plugin exists.
 */
function travelify_breadcrumb() {
	if( function_exists( 'bcn_display_list' ) ) {
		echo '<div class="breadcrumb">
		<ul>';
		bcn_display_list();
		echo '</ul>
		</div> <!-- .breadcrumb -->';
	}

}
endif;

/****************************************************************************************/

if ( ! function_exists( 'travelify_header_title' ) ) :
/**
 * Show the title in header
 */
function travelify_header_title() {
	if( is_archive() ) {
		$travelify_header_title = single_cat_title( '', FALSE );
	}
	elseif( is_search() ) {
		$travelify_header_title = __( 'Search Results', 'travelify' );
	}
	elseif( is_page_template()  ) {
		$travelify_header_title = get_the_title();
	}
	else {
		$travelify_header_title = '';
	}

	return $travelify_header_title;

}
endif;
?>