<?php
/**
 *
 *
 * Please browse readme.txt for credits and forking information
 *
 * @package Lighthouse
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses lighthouse_header_style()
 */
function lighthouse_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'lighthouse_custom_header_args', array(
		'default-image'          => '%s/images/headers/snow-mountains.png',
		'default-text-color'     => 'fff',
		'width'                  => 1600,
		'height'                 => 500,
		'flex-height'            => true,
		'flex-width'	         => true,
		'wp-head-callback'       => 'lighthouse_header_style',
	) ) );


	/*
	 * Default custom headers packaged with the theme.
	 * %s is a placeholder for the theme template directory URI.
	 */
	register_default_headers( array(
		'mountains' => array(
			'url'           => '%s/images/headers/snow-mountains.png',
			'thumbnail_url' => '%s/images/headers/snow-mountains_thumbnail.png',
			'description'   => _x( 'food', 'header image description', 'lighthouse' )
		),	
		'skyline' => array(
			'url'           => '%s/images/headers/skyline.png',
			'thumbnail_url' => '%s/images/headers/skyline_thumbnail.png',
			'description'   => _x( 'buildings', 'header image description', 'lighthouse' )
		),
	) );
}
add_action( 'after_setup_theme', 'lighthouse_custom_header_setup' );

if ( ! function_exists( 'lighthouse_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see lighthouse_custom_header_setup().
 */
function lighthouse_header_style() {
	$header_image = get_header_image();
	$header_text_color   = get_header_textcolor();
		
	// If no custom options for text are set, let's bail.
	if ( empty( $header_image ) && $header_text_color == get_theme_support( 'custom-header', 'default-text-color' ) ){
		return;
	}

	// If we get this far, we have custom styles.
	?>
	<style type="text/css" id="lighthouse-header-css">
	<?php
		if ( ! empty( $header_image ) ) :
			$header_width = get_custom_header()->width;
			$header_height = get_custom_header()->height;
			$header_height1 = ($header_height / $header_width * 1600);
			$header_height2 = ($header_height / $header_width * 768);
			$header_height3 = ($header_height / $header_width * 360);
			
	?>
				.site-header {
					background: url(<?php header_image(); ?>) no-repeat scroll top;
					<?php if($header_height1 > 200){ ?>
						background-size: cover;
						background-position:center;
						height: 500px
					<?php }else{ ?>
						background-size: cover;
						height: 500px
					<?php } ?>
				}

				@media (min-width: 768px) and (max-width: 1024px){
					.site-header {
						<?php if($header_height2 > 170){ ?>
							background-size: cover;
							background-position:center;
							height: 350px;
						<?php }else{ ?>
							background-size: cover;
							height: 350px;
						<?php }	?>				
					}
				}

				@media (max-width: 767px) {
					.site-header {
						<?php if($header_height2 > 170){ ?>
							background-size: cover;
							background-position:center;
							height: 300px;
						<?php }else{ ?>
							background-size: cover;
							height: 300px;
						<?php }	?>				
					}
				}
				@media (max-width: 359px) {
					.site-header {
						<?php if($header_height3 > 80){ ?>
							background-size: cover;
							background-position:center;
							height: 200px;
						<?php }else{ ?>
							background-size: cover;
							height: 200px;
						<?php } ?>
						
					}
					
				}
				.site-header{
					-webkit-box-shadow: 0px 0px 2px 1px rgba(182,182,182,0.3);
			    	-moz-box-shadow: 0px 0px 2px 1px rgba(182,182,182,0.3);
			    	-o-box-shadow: 0px 0px 2px 1px rgba(182,182,182,0.3);
			    	box-shadow: 0px 0px 2px 1px rgba(182,182,182,0.3);
				}
  <?php else: ?>
	.site-header{
		-webkit-box-shadow: 0px 0px 1px 1px rgba(182,182,182,0.3);
    	-moz-box-shadow: 0px 0px 1px 1px rgba(182,182,182,0.3);
    	-o-box-shadow: 0px 0px 1px 1px rgba(182,182,182,0.3);
    	box-shadow: 0px 0px 1px 1px rgba(182,182,182,0.3);
	}
	.site-header {
			height: 300px;
		}
		@media (max-width: 767px) {
			.site-header {
				height: 200px;
			}
		}
		@media (max-width: 359px) {
			.site-header {
				height: 150px;
			}
		}
		
	<?php endif; 

		// Has the text been hidden?
		if ( ! display_header_text() ) :

	?>
		.site-title,
		.site-description {
			position: absolute;
			clip: rect(1px 1px 1px 1px); /* IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		endif;
		if ( empty( $header_image ) ) :
	?>
		.site-header .home-link {
			min-height: 0;
		}
	<?php

		// If the user has set a custom color for the text, use that.

		else:
			
	?>

	<?php endif; ?>

	</style>
	<?php
}
endif; // lighthouse_header_style




