<?php
/**
 *
 *
 * Please browse readme.txt for credits and forking information
 *
 * @package noteblog
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses noteblog_header_style()
 */
function noteblog_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'noteblog_custom_header_args', array(
		'default-image'          => '%s/images/headers/background.png',
		'default-text-color'     => 'fff',
		'width'                  => 1800,
		'height'                 => 848,
		'flex-height'            => true,
		'flex-width'	         => true,
		'wp-head-callback'       => 'noteblog_header_style',
	) ) );


	/*
	 * Default custom headers packaged with the theme.
	 * %s is a placeholder for the theme template directory URI.
	 */
	register_default_headers( array(
		'mountains' => array(
			'url'           => '%s/images/headers/background.png',
			'thumbnail_url' => '%s/images/headers/background_thumbnail.png',
			'description'   => _x( 'Mountains', 'header image description', 'noteblog' )
		),		
	) );
}
add_action( 'after_setup_theme', 'noteblog_custom_header_setup' );

if ( ! function_exists( 'noteblog_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see noteblog_custom_header_setup().
 */
function noteblog_header_style() {
	$header_image = get_header_image();
	$header_text_color   = get_header_textcolor();
		
	// If no custom options for text are set, let's bail.
	if ( empty( $header_image ) && $header_text_color == get_theme_support( 'custom-header', 'default-text-color' ) ){
		return;
	}

	// If we get this far, we have custom styles.
	?>
	<style type="text/css" id="noteblog-header-css">
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
						background-position:top;
					<?php }else{ ?>
						background-size: cover;
					<?php } ?>
				}

				@media (min-width: 768px) and (max-width: 1024px){
					.site-header {
						<?php if($header_height2 > 170){ ?>
							background-size: cover;
							background-position:top;
						<?php }else{ ?>
							background-size: cover;
						<?php }	?>				
					}
				}

				@media (max-width: 767px) {
					.site-header {
						<?php if($header_height2 > 170){ ?>
							background-size: cover;
							background-position:top;
						<?php }else{ ?>
							background-size: cover;
						<?php }	?>				
					}
				}
				@media (max-width: 359px) {
					.site-header {
						<?php if($header_height3 > 80){ ?>
							background-size: cover;
							background-position:top;
						<?php }else{ ?>
							background-size: cover;
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

		.site-title,
		.site-description {
			color: #<?php echo esc_attr( $header_text_color ); ?>;
		}
		.site-title::after{
			background: #<?php echo esc_attr( $header_text_color ); ?>;
			content:"";       
		}
	<?php endif; ?>

	</style>
	<?php
}
endif; // noteblog_header_style




