<?php
/**
 * @package Twenty Minutes
 * Setup the WordPress core custom header feature.
 *
 * @uses twenty_minutes_header_style()
 */
function twenty_minutes_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'twenty_minutes_custom_header_args', array(	
		'default-text-color'     => 'fff',
		'width'                  => 1400,
		'height'                 => 280,
		'wp-head-callback'       => 'twenty_minutes_header_style',		
	) ) );
}
add_action( 'after_setup_theme', 'twenty_minutes_custom_header_setup' );

if ( ! function_exists( 'twenty_minutes_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see twenty_minutes_custom_header_setup().
 */
function twenty_minutes_header_style() {
	$header_text_color = get_header_textcolor();
	?>
	<style type="text/css">
		<?php
			//Check if user has defined any header image.
			if ( get_header_image() || get_header_textcolor() ) :
		?>
			.header {
				background: url(<?php echo esc_url( get_header_image() ); ?>) no-repeat;
				background-position: center top;
				background-size:cover !important;
			}
		<?php endif; ?>	


		.info-box {
			background: <?php echo esc_attr(get_theme_mod('twenty_minutes_headerbgcol')); ?>;
		}

		.info-box i {
			color: <?php echo esc_attr(get_theme_mod('twenty_minutes_headericoncol')); ?>;
		}

		.info-box a {
			color: <?php echo esc_attr(get_theme_mod('twenty_minutes_headertextcol')); ?>;
		}

		.social-icons i {
			color: <?php echo esc_attr(get_theme_mod('twenty_minutes_socialiconcol')); ?>;
		}

		.social-icons i {
			color: <?php echo esc_attr(get_theme_mod('twenty_minutes_socialiconcol')); ?>;
		}

		.header-top {
			background: <?php echo esc_attr(get_theme_mod('twenty_minutes_socialbgcol')); ?>;
		}

		.page-template-template-home-page h1.site-title a {
			color: <?php echo esc_attr(get_theme_mod('twenty_minutes_sitetitle')); ?>;
		}

		.page-template-template-home-page span.site-description {
			color: <?php echo esc_attr(get_theme_mod('twenty_minutes_sitetagline')); ?>;
		}

		.copywrap, .copywrap p, .copywrap p a, #footer .copywrap a{
			color: <?php echo esc_attr(get_theme_mod('twenty_minutes_footer_coypright_col')); ?> !important;
		}

		#footer .copywrap a:hover, .copywrap p:hover, .copywrap:hover{
			color: <?php echo esc_attr(get_theme_mod('twenty_minutes_footer_coyprighthover_col')); ?> !important;
		}

		#footer .copywrap {
			background-color: <?php echo esc_attr(get_theme_mod('twenty_minutes_footer_coyprightbg_col')); ?>;
		}

		#footer {
			background-color: <?php echo esc_attr(get_theme_mod('twenty_minutes_footer_bg_col')); ?>;
		}

		#footer h1,#footer h2,#footer h3,#footer h4,#footer h5,#footer h6 {
			color: <?php echo esc_attr(get_theme_mod('twenty_minutes_footer_heading_col')); ?>;
		}

		#footer p {
			color: <?php echo esc_attr(get_theme_mod('twenty_minutes_footer_text_col')); ?>;
		}

		#footer li a {
			color: <?php echo esc_attr(get_theme_mod('twenty_minutes_footer_list_col')); ?>;
		}

		#footer li a:hover {
			color: <?php echo esc_attr(get_theme_mod('twenty_minutes_footer_listhover_col')); ?>;
		}
		
	</style>
	<?php 
}
endif;