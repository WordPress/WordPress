<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Generates and outputs theme options' generated styleshets
 *
 * @action Before the template: us_before_template:templates/theme-options.css
 * @action After the template: us_after_template:templates/theme-options.css
 */

$prefixes = array( 'heading', 'body', 'menu' );
$font_families = array();
$default_font_weights = array_fill_keys( $prefixes, 400 );
foreach ( $prefixes as $prefix ) {
	$font = explode( '|', us_get_option( $prefix . '_font_family', 'none' ), 2 );
	if ( $font[0] == 'none' ) {
		// Use the default font
		$font_families[ $prefix ] = '';
	} elseif ( strpos( $font[0], ',' ) === FALSE ) {
		// Use some specific font from Google Fonts
		if ( ! isset( $font[1] ) OR empty( $font[1] ) ) {
			// Fault tolerance for missing font-variants
			$font[1] = '400,700';
		}
		// The first active font-weight will be used for "normal" weight
		$default_font_weights[ $prefix ] = intval( $font[1] );
		$fallback_font_family = us_config( 'google-fonts.' . $font[0] . '.fallback', 'sans-serif' );
		$font_families[ $prefix ] = 'font-family: "' . $font[0] . '", ' . $fallback_font_family . ";\n";
	} else {
		// Web-safe font combination
		$font_families[ $prefix ] = 'font-family: ' . $font[0] . ";\n";
	}
}

?>

<?php if ( FALSE ): ?><style>/* Setting IDE context */<?php endif; ?>


/* Typography
   ========================================================================== */

body {
	<?php echo $font_families['body'] ?>
	font-size: <?php echo us_get_option( 'body_fontsize' ) ?>px;
	line-height: <?php echo us_get_option( 'body_lineheight' ) ?>px;
	font-weight: <?php echo $default_font_weights['body'] ?>;
	}
.w-blog-post {
	font-size: <?php echo us_get_option( 'body_fontsize' ) ?>px;
	}

.w-text.font_main_menu,
.w-nav .menu-item-language,
.w-nav-item {
	<?php echo $font_families['menu'] ?>
	font-weight: <?php echo $default_font_weights['menu'] ?>;
	}

h1, h2, h3, h4, h5, h6,
.w-text.font_heading,
.w-blog-post.format-quote blockquote,
.w-counter-number,
.w-pricing-item-price,
.w-tabs-item-title,
.ult_price_figure,
.ult_countdown-amount,
.ultb3-box .ultb3-title,
.stats-block .stats-desc .stats-number {
	<?php echo $font_families['heading'] ?>
	font-weight: <?php echo $default_font_weights['heading'] ?>;
	}
h1 {
	font-size: <?php echo us_get_option( 'h1_fontsize' ) ?>px;
	letter-spacing: <?php echo us_get_option( 'h1_letterspacing' ) ?>px;
	text-transform: <?php $h1_transform = us_get_option( 'h1_transform' ); echo $h1_transform[0]; ?>;
	}
h2 {
	font-size: <?php echo us_get_option( 'h2_fontsize' ) ?>px;
	letter-spacing: <?php echo us_get_option( 'h2_letterspacing' ) ?>px;
	text-transform: <?php $h2_transform = us_get_option( 'h2_transform' ); echo $h2_transform[0]; ?>;
	}
h3 {
	font-size: <?php echo us_get_option( 'h3_fontsize' ) ?>px;
	letter-spacing: <?php echo us_get_option( 'h3_letterspacing' ) ?>px;
	text-transform: <?php $h3_transform = us_get_option( 'h3_transform' ); echo $h3_transform[0]; ?>;
	}
h4,
.widgettitle,
.comment-reply-title,
.woocommerce #reviews h2,
.woocommerce .related > h2,
.woocommerce .upsells > h2,
.woocommerce .cross-sells > h2 {
	font-size: <?php echo us_get_option( 'h4_fontsize' ) ?>px;
	letter-spacing: <?php echo us_get_option( 'h4_letterspacing' ) ?>px;
	text-transform: <?php $h4_transform = us_get_option( 'h4_transform' ); echo $h4_transform[0]; ?>;
	}
h5,
.w-blog:not(.cols_1) .w-blog-list .w-blog-post-title {
	font-size: <?php echo us_get_option( 'h5_fontsize' ) ?>px;
	letter-spacing: <?php echo us_get_option( 'h5_letterspacing' ) ?>px;
	text-transform: <?php $h5_transform = us_get_option( 'h5_transform' ); echo $h5_transform[0]; ?>;
	}
h6 {
	font-size: <?php echo us_get_option( 'h6_fontsize' ) ?>px;
	letter-spacing: <?php echo us_get_option( 'h6_letterspacing' ) ?>px;
	text-transform: <?php $h6_transform = us_get_option( 'h6_transform' ); echo $h6_transform[0]; ?>;
	}
@media (max-width: 767px) {
body {
	font-size: <?php echo us_get_option( 'body_fontsize_mobile' ) ?>px;
	line-height: <?php echo us_get_option( 'body_lineheight_mobile' ) ?>px;
	}
.w-blog-post {
	font-size: <?php echo us_get_option( 'body_fontsize_mobile' ) ?>px;
	}
h1 {
	font-size: <?php echo us_get_option( 'h1_fontsize_mobile' ) ?>px;
	}
h2 {
	font-size: <?php echo us_get_option( 'h2_fontsize_mobile' ) ?>px;
	}
h3 {
	font-size: <?php echo us_get_option( 'h3_fontsize_mobile' ) ?>px;
	}
h4,
.widgettitle,
.comment-reply-title,
.woocommerce #reviews h2,
.woocommerce .related > h2,
.woocommerce .upsells > h2,
.woocommerce .cross-sells > h2 {
	font-size: <?php echo us_get_option( 'h4_fontsize_mobile' ) ?>px;
	}
h5 {
	font-size: <?php echo us_get_option( 'h5_fontsize_mobile' ) ?>px;
	}
h6 {
	font-size: <?php echo us_get_option( 'h6_fontsize_mobile' ) ?>px;
	}
}

/* Layout Options
   ========================================================================== */

body,
.header_hor .l-header.pos_fixed {
	min-width: <?php echo us_get_option( 'site_canvas_width' ) ?>px;
	}
.l-canvas.type_boxed,
.l-canvas.type_boxed .l-subheader,
.l-canvas.type_boxed ~ .l-footer .l-subfooter {
	max-width: <?php echo us_get_option( 'site_canvas_width' ) ?>px;
	}
.header_hor .l-subheader-h,
.l-titlebar-h,
.l-main-h,
.l-section-h,
.l-subfooter-h,
.w-tabs-section-content-h,
.w-blog-post-body {
	max-width: <?php echo us_get_option( 'site_content_width' ) ?>px;
	}
.l-sidebar {
	width: <?php echo us_get_option( 'sidebar_width' ) ?>%;
	}
.l-content {
	width: <?php echo us_get_option( 'content_width' ) ?>%;
	}
@media (max-width: <?php echo us_get_option( 'columns_stacking_width' ) ?>px) {
.g-cols > div:not([class*="-xs-"]) {
	float: none;
	width: 100%;
	margin: 0 0 25px;
	}
.g-cols.offset_none > div,
.g-cols > div:last-child,
.g-cols > div.vc_col-has-fill {
	margin-bottom: 0;
	}
}

/* Button Options
   ========================================================================== */
   
.w-btn,
.button,
.l-body .cl-btn,
.l-body .ubtn,
.l-body .ultb3-btn,
.l-body .btn-modal,
.l-body .flip-box-wrap .flip_link a,
.l-body .ult_pricing_table_wrap .ult_price_link .ult_price_action_button,
.tribe-events-button,
button,
input[type="submit"] {
	<?php if ( in_array( 'bold', us_get_option( 'button_text_style' ) ) ): ?>
	font-weight: bold;
	<?php endif; ?>
	<?php if ( in_array( 'uppercase', us_get_option( 'button_text_style' ) ) ): ?>
	text-transform: uppercase;
	<?php endif; ?>
	font-size: <?php echo us_get_option( 'button_fontsize' ) ?>px;
	line-height: <?php echo us_get_option( 'button_height' ) ?>;
    padding: 0 <?php echo us_get_option( 'button_width' ) ?>em;
	border-radius: <?php echo us_get_option( 'button_border_radius' ) ?>em;
	letter-spacing: <?php echo us_get_option( 'button_letterspacing' ) ?>px;
	<?php if ( us_get_option( 'button_font' ) == 'heading' ) { echo $font_families['heading']; } ?>
	<?php if ( us_get_option( 'button_font' ) == 'menu' ) { echo $font_families['menu']; } ?>
	}
.w-btn.icon_atleft i {
	left: <?php echo us_get_option( 'button_width' ) ?>em;
	}
.w-btn.icon_atright i {
	right: <?php echo us_get_option( 'button_width' ) ?>em;
	}
	
/* Header Settings
   ========================================================================== */

/* Default state */
@media (min-width: 901px) {
	
	<?php if ( ! us_get_header_option( 'top_show' ) ): ?>
	.l-subheader.at_top { display: none; }
	<?php endif; ?>
	
	.header_hor .l-subheader.at_top {
		line-height: <?php echo us_get_header_option( 'top_height' ) ?>px;
		height: <?php echo us_get_header_option( 'top_height' ) ?>px;
		}
	.header_hor .l-header.sticky .l-subheader.at_top {
		line-height: <?php echo us_get_header_option( 'top_sticky_height' ) ?>px;
		height: <?php echo us_get_header_option( 'top_sticky_height' ) ?>px;
		<?php if ( us_get_header_option( 'top_sticky_height' ) == 0 ): ?>
		overflow: hidden;
		<?php endif; ?>
		}
		
	.header_hor .l-subheader.at_middle {
		line-height: <?php echo us_get_header_option( 'middle_height' ) ?>px;
		height: <?php echo us_get_header_option( 'middle_height' ) ?>px;
		}
	.header_hor .l-header.sticky .l-subheader.at_middle {
		line-height: <?php echo us_get_header_option( 'middle_sticky_height' ) ?>px;
		height: <?php echo us_get_header_option( 'middle_sticky_height' ) ?>px;
		<?php if ( us_get_header_option( 'middle_sticky_height' ) == 0 ): ?>
		overflow: hidden;
		<?php endif; ?>
		}
		
	<?php if ( ! us_get_header_option( 'bottom_show' ) ): ?>
	.l-subheader.at_bottom { display: none; }
	<?php endif; ?>
	
	.header_hor .l-subheader.at_bottom {
		line-height: <?php echo us_get_header_option( 'bottom_height' ) ?>px;
		height: <?php echo us_get_header_option( 'bottom_height' ) ?>px;
		}
	.header_hor .l-header.sticky .l-subheader.at_bottom {
		line-height: <?php echo us_get_header_option( 'bottom_sticky_height' ) ?>px;
		height: <?php echo us_get_header_option( 'bottom_sticky_height' ) ?>px;
		<?php if ( us_get_header_option( 'bottom_sticky_height' ) == 0 ): ?>
		overflow: hidden;
		<?php endif; ?>
		}
		
	.header_hor .l-header.pos_fixed ~ .l-titlebar,
	.header_hor .titlebar_none.sidebar_left .l-header.pos_fixed ~ .l-main,
	.header_hor .titlebar_none.sidebar_right .l-header.pos_fixed ~ .l-main,
	.header_hor .titlebar_none.sidebar_none .l-header.pos_fixed ~ .l-main .l-section:first-child,
	.header_hor.header_inpos_below .l-header.pos_fixed ~ .l-main .l-section:nth-child(2),
	.header_hor .l-header.pos_static.bg_transparent ~ .l-titlebar,
	.header_hor .titlebar_none.sidebar_left .l-header.pos_static.bg_transparent ~ .l-main,
	.header_hor .titlebar_none.sidebar_right .l-header.pos_static.bg_transparent ~ .l-main,
	.header_hor .titlebar_none.sidebar_none .l-header.pos_static.bg_transparent ~ .l-main .l-section:first-child {
		<?php
		$header_height = us_get_header_option( 'top_show' ) ? intval( us_get_header_option( 'top_height' ) ) : 0;
		$header_height += intval( us_get_header_option( 'middle_height' ) );
		$header_height += us_get_header_option( 'bottom_show' ) ? intval( us_get_header_option( 'bottom_height' ) ) : 0;
		?>
		padding-top: <?php echo $header_height ?>px;
		}
	.header_hor .l-header.pos_static.bg_solid + .l-main .l-section.preview_trendy .w-blog-post-preview {
		top: -<?php echo $header_height ?>px;
		}
	.header_hor.header_inpos_bottom .l-header.pos_fixed ~ .l-main .l-section:first-child {
		padding-bottom: <?php echo $header_height ?>px;
		}
	/* Fix vertical centering of first section when header is transparent */
	.header_hor .l-header.bg_transparent ~ .l-main .l-section.valign_center:first-child > .l-section-h {
		top: -<?php echo $header_height/2 ?>px;
		}
	.header_hor.header_inpos_bottom .l-header.pos_fixed.bg_transparent ~ .l-main .l-section.valign_center:first-child > .l-section-h {
		top: <?php echo $header_height/2 ?>px;
		}
	
	.header_hor .l-header.pos_fixed ~ .l-main .l-section.height_full:not(:first-child) {
		<?php
		$header_sticky_height = us_get_header_option( 'top_show' ) ? intval( us_get_header_option( 'top_sticky_height' ) ) : 0;
		$header_sticky_height += intval( us_get_header_option( 'middle_sticky_height' ) );
		$header_sticky_height += us_get_header_option( 'bottom_show' ) ? intval( us_get_header_option( 'bottom_sticky_height' ) ) : 0;
		?>
		min-height: calc(100vh - <?php echo $header_sticky_height ?>px);
		}
	.admin-bar.header_hor .l-header.pos_fixed ~ .l-main .l-section.height_full:not(:first-child) {
		min-height: calc(100vh - <?php echo $header_sticky_height ?>px - 32px);
		}
		
	<?php if ( us_get_header_option( 'bg_img' ) AND $bg_image = usof_get_image_src( us_get_header_option( 'bg_img' ) ) ): ?>
	.l-subheader.at_middle {
		background-image: url(<?php echo $bg_image[0] ?>);
		background-attachment: <?php echo us_get_header_option( 'bg_img_attachment' ) ?>;
		background-position: <?php echo us_get_header_option( 'bg_img_position' ) ?>;
		background-repeat: <?php echo us_get_header_option( 'bg_img_repeat' ) ?>;
		background-size: <?php echo us_get_header_option( 'bg_img_size' ) ?>;
	}
	<?php endif; ?>
	
	.header_ver {
		padding-left: <?php echo us_get_header_option( 'width' ) ?>px;
		position: relative;
		}
	.rtl.header_ver {
		padding-left: 0;
		padding-right: <?php echo us_get_header_option( 'width' ) ?>px;
		}
	.header_ver .l-header,
	.header_ver .l-header .w-cart-notification {
		width: <?php echo us_get_header_option( 'width' ) ?>px;
		}
	.header_ver .l-navigation-item.to_next {
		left: <?php echo us_get_header_option( 'width' ) - 200 ?>px;
		}
	.no-touch .header_ver .l-navigation-item.to_next:hover {
		left: <?php echo us_get_header_option( 'width' ) ?>px;
		}
	.rtl.header_ver .l-navigation-item.to_next {
		right: <?php echo us_get_header_option( 'width' ) - 200 ?>px;
		}
	.no-touch .rtl.header_ver .l-navigation-item.to_next:hover {
		right: <?php echo us_get_header_option( 'width' ) ?>px;
		}
	.header_ver .w-nav.type_desktop [class*="columns"] .w-nav-list.level_2 {
		width: calc(100vw - <?php echo us_get_header_option( 'width' ) ?>px);
		max-width: 980px;
		}
}

/* Tablets state */
@media (min-width: 601px) and (max-width: 900px) {
	
	<?php if ( ! us_get_header_option( 'top_show', 'tablets' ) ): ?>
	.l-subheader.at_top { display: none; }
	<?php endif; ?>
	
	.header_hor .l-subheader.at_top {
		line-height: <?php echo us_get_header_option( 'top_height', 'tablets' ) ?>px;
		height: <?php echo us_get_header_option( 'top_height', 'tablets' ) ?>px;
		}
	.header_hor .l-header.sticky .l-subheader.at_top {
		line-height: <?php echo us_get_header_option( 'top_sticky_height', 'tablets' ) ?>px;
		height: <?php echo us_get_header_option( 'top_sticky_height', 'tablets' ) ?>px;
		<?php if ( us_get_header_option( 'top_sticky_height', 'tablets' ) == 0 ): ?>
		overflow: hidden;
		<?php endif; ?>
		}
		
	.header_hor .l-subheader.at_middle {
		line-height: <?php echo us_get_header_option( 'middle_height', 'tablets' ) ?>px;
		height: <?php echo us_get_header_option( 'middle_height', 'tablets' ) ?>px;
		}
	.header_hor .l-header.sticky .l-subheader.at_middle {
		line-height: <?php echo us_get_header_option( 'middle_sticky_height', 'tablets' ) ?>px;
		height: <?php echo us_get_header_option( 'middle_sticky_height', 'tablets' ) ?>px;
		<?php if ( us_get_header_option( 'middle_sticky_height', 'tablets' ) == 0 ): ?>
		overflow: hidden;
		<?php endif; ?>
		}
		
	<?php if ( ! us_get_header_option( 'bottom_show', 'tablets' ) ): ?>
	.l-subheader.at_bottom { display: none; }
	<?php endif; ?>
	
	.header_hor .l-subheader.at_bottom {
		line-height: <?php echo us_get_header_option( 'bottom_height', 'tablets' ) ?>px;
		height: <?php echo us_get_header_option( 'bottom_height', 'tablets' ) ?>px;
		}
	.header_hor .l-header.sticky .l-subheader.at_bottom {
		line-height: <?php echo us_get_header_option( 'bottom_sticky_height', 'tablets' ) ?>px;
		height: <?php echo us_get_header_option( 'bottom_sticky_height', 'tablets' ) ?>px;
		<?php if ( us_get_header_option( 'bottom_sticky_height', 'tablets' ) == 0 ): ?>
		overflow: hidden;
		<?php endif; ?>
		}
		
	.header_hor .l-header.pos_fixed ~ .l-titlebar,
	.header_hor .titlebar_none.sidebar_left .l-header.pos_fixed ~ .l-main,
	.header_hor .titlebar_none.sidebar_right .l-header.pos_fixed ~ .l-main,
	.header_hor .titlebar_none.sidebar_none .l-header.pos_fixed ~ .l-main .l-section:first-child,
	.header_hor .l-header.pos_static.bg_transparent ~ .l-titlebar,
	.header_hor .titlebar_none.sidebar_left .l-header.pos_static.bg_transparent ~ .l-main,
	.header_hor .titlebar_none.sidebar_right .l-header.pos_static.bg_transparent ~ .l-main,
	.header_hor .titlebar_none.sidebar_none .l-header.pos_static.bg_transparent ~ .l-main .l-section:first-child {
		<?php
		$header_height = us_get_header_option( 'top_show', 'tablets' ) ? intval( us_get_header_option( 'top_height', 'tablets' ) ) : 0;
		$header_height += intval( us_get_header_option( 'middle_height', 'tablets' ) );
		$header_height += us_get_header_option( 'bottom_show', 'tablets' ) ? intval( us_get_header_option( 'bottom_height', 'tablets' ) ) : 0;
		?>
		padding-top: <?php echo $header_height ?>px;
		}
	.header_hor .l-header.pos_static.bg_solid + .l-main .l-section.preview_trendy .w-blog-post-preview {
		top: -<?php echo $header_height ?>px;
		}
		
	<?php if ( us_get_header_option( 'bg_img', 'tablets' ) AND $bg_image = usof_get_image_src( us_get_header_option( 'bg_img', 'tablets' ) ) ): ?>
	.l-subheader.at_middle {
		background-image: url(<?php echo $bg_image[0] ?>);
		background-attachment: <?php echo us_get_header_option( 'bg_img_attachment', 'tablets' ) ?>;
		background-position: <?php echo us_get_header_option( 'bg_img_position', 'tablets' ) ?>;
		background-repeat: <?php echo us_get_header_option( 'bg_img_repeat', 'tablets' ) ?>;
		background-size: <?php echo us_get_header_option( 'bg_img_size', 'tablets' ) ?>;
	}
	<?php endif; ?>
	
	.header_ver .l-header {
		width: <?php echo us_get_header_option( 'width', 'tablets' ) ?>px;
		}
}

/* Mobiles state */
@media (max-width: 600px) {
	
	<?php if ( ! us_get_header_option( 'top_show', 'mobiles' ) ): ?>
	.l-subheader.at_top { display: none; }
	<?php endif; ?>
	
	.header_hor .l-subheader.at_top {
		line-height: <?php echo us_get_header_option( 'top_height', 'mobiles' ) ?>px;
		height: <?php echo us_get_header_option( 'top_height', 'mobiles' ) ?>px;
		}
	.header_hor .l-header.sticky .l-subheader.at_top {
		line-height: <?php echo us_get_header_option( 'top_sticky_height', 'mobiles' ) ?>px;
		height: <?php echo us_get_header_option( 'top_sticky_height', 'mobiles' ) ?>px;
		<?php if ( us_get_header_option( 'top_sticky_height', 'mobiles' ) == 0 ): ?>
		overflow: hidden;
		<?php endif; ?>
		}
		
	.header_hor .l-subheader.at_middle {
		line-height: <?php echo us_get_header_option( 'middle_height', 'mobiles' ) ?>px;
		height: <?php echo us_get_header_option( 'middle_height', 'mobiles' ) ?>px;
		}
	.header_hor .l-header.sticky .l-subheader.at_middle {
		line-height: <?php echo us_get_header_option( 'middle_sticky_height', 'mobiles' ) ?>px;
		height: <?php echo us_get_header_option( 'middle_sticky_height', 'mobiles' ) ?>px;
		<?php if ( us_get_header_option( 'middle_sticky_height', 'mobiles' ) == 0 ): ?>
		overflow: hidden;
		<?php endif; ?>
		}
		
	<?php if ( ! us_get_header_option( 'bottom_show', 'mobiles' ) ): ?>
	.l-subheader.at_bottom { display: none; }
	<?php endif; ?>
	
	.header_hor .l-subheader.at_bottom {
		line-height: <?php echo us_get_header_option( 'bottom_height', 'mobiles' ) ?>px;
		height: <?php echo us_get_header_option( 'bottom_height', 'mobiles' ) ?>px;
		}
	.header_hor .l-header.sticky .l-subheader.at_bottom {
		line-height: <?php echo us_get_header_option( 'bottom_sticky_height', 'mobiles' ) ?>px;
		height: <?php echo us_get_header_option( 'bottom_sticky_height', 'mobiles' ) ?>px;
		<?php if ( us_get_header_option( 'bottom_sticky_height', 'mobiles' ) == 0 ): ?>
		overflow: hidden;
		<?php endif; ?>
		}
		
	.header_hor .l-header.pos_fixed ~ .l-titlebar,
	.header_hor .titlebar_none.sidebar_left .l-header.pos_fixed ~ .l-main,
	.header_hor .titlebar_none.sidebar_right .l-header.pos_fixed ~ .l-main,
	.header_hor .titlebar_none.sidebar_none .l-header.pos_fixed ~ .l-main .l-section:first-child,
	.header_hor .l-header.pos_static.bg_transparent ~ .l-titlebar,
	.header_hor .titlebar_none.sidebar_left .l-header.pos_static.bg_transparent ~ .l-main,
	.header_hor .titlebar_none.sidebar_right .l-header.pos_static.bg_transparent ~ .l-main,
	.header_hor .titlebar_none.sidebar_none .l-header.pos_static.bg_transparent ~ .l-main .l-section:first-child {
		<?php
		$header_height = us_get_header_option( 'top_show', 'mobiles' ) ? intval( us_get_header_option( 'top_height', 'mobiles' ) ) : 0;
		$header_height += intval( us_get_header_option( 'middle_height', 'mobiles' ) );
		$header_height += us_get_header_option( 'bottom_show', 'mobiles' ) ? intval( us_get_header_option( 'bottom_height', 'mobiles' ) ) : 0;
		?>
		padding-top: <?php echo $header_height ?>px;
		}
	.header_hor .l-header.pos_static.bg_solid + .l-main .l-section.preview_trendy .w-blog-post-preview {
		top: -<?php echo $header_height ?>px;
		}
		
	<?php if ( us_get_header_option( 'bg_img', 'mobiles' ) AND $bg_image = usof_get_image_src( us_get_header_option( 'bg_img', 'mobiles' ) ) ): ?>
	.l-subheader.at_middle {
		background-image: url(<?php echo $bg_image[0] ?>);
		background-attachment: <?php echo us_get_header_option( 'bg_img_attachment', 'mobiles' ) ?>;
		background-position: <?php echo us_get_header_option( 'bg_img_position', 'mobiles' ) ?>;
		background-repeat: <?php echo us_get_header_option( 'bg_img_repeat', 'mobiles' ) ?>;
		background-size: <?php echo us_get_header_option( 'bg_img_size', 'mobiles' ) ?>;
	}
	<?php endif; ?>
	
}

/* Header Elements Settings
   ========================================================================== */

/* Image */
<?php foreach ( us_get_header_elms_of_a_type( 'image' ) as $class => $param ): ?>
@media (min-width: 901px) {
	.<?php echo $class ?> { height: <?php echo $param['height'] ?>px; }
	.l-header.sticky .<?php echo $class ?> { height: <?php echo $param['height_sticky'] ?>px; }
}
@media (min-width: 601px) and (max-width: 900px) {
	.<?php echo $class ?> { height: <?php echo $param['height_tablets'] ?>px; }
	.l-header.sticky .<?php echo $class ?> { height: <?php echo $param['height_sticky_tablets'] ?>px; }
}
@media (max-width: 600px) {
	.<?php echo $class ?> { height: <?php echo $param['height_mobiles'] ?>px; }
	.l-header.sticky .<?php echo $class ?> { height: <?php echo $param['height_sticky_mobiles'] ?>px; }
}
<?php endforeach; ?>

/* Text */
<?php foreach ( us_get_header_elms_of_a_type( 'text' ) as $class => $param ): ?>
.<?php echo $class ?> .w-text-value { color: <?php echo $param['color'] ?>; }
@media (min-width: 901px) {
	.<?php echo $class ?> { font-size: <?php echo $param['size'] ?>px; }
}
@media (min-width: 601px) and (max-width: 900px) {
	.<?php echo $class ?> { font-size: <?php echo $param['size_tablets'] ?>px; }
}
@media (max-width: 600px) {
	.<?php echo $class ?> { font-size: <?php echo $param['size_mobiles'] ?>px; }
}

<?php if ( ! $param['wrap'] ): ?>
.<?php echo $class ?> { white-space: nowrap; }
<?php endif; ?>

<?php endforeach; ?>

/* Button */
<?php foreach ( us_get_header_elms_of_a_type( 'btn' ) as $class => $param ): ?>
@media (min-width: 901px) {
	.<?php echo $class ?> .w-btn { font-size: <?php echo $param['size'] ?>px; }
}
@media (min-width: 601px) and (max-width: 900px) {
	.<?php echo $class ?> .w-btn { font-size: <?php echo $param['size_tablets'] ?>px; }
}
@media (max-width: 600px) {
	.<?php echo $class ?> .w-btn { font-size: <?php echo $param['size_mobiles'] ?>px; }
}
.l-header .<?php echo $class ?> .w-btn.style_solid {
	background-color: <?php echo $param['color_bg'] ?>;
	color: <?php echo $param['color_text'] ?>;
	}
.l-header .<?php echo $class ?> .w-btn.style_outlined {
	box-shadow: 0 0 0 2px <?php echo $param['color_bg'] ?> inset;
	color: <?php echo $param['color_text'] ?>;
	}
.no-touch .l-header .<?php echo $class ?> .w-btn:before {
	background-color: <?php echo $param['color_hover_bg'] ?>;
	}
.no-touch .l-header .<?php echo $class ?> .w-btn:hover {
	color: <?php echo $param['color_hover_text'] ?> !important;
	}
<?php endforeach; ?>

/* Main Menu */
<?php foreach ( us_get_header_elms_of_a_type( 'menu' ) as $class => $param ): ?>
.header_hor .<?php echo $class ?>.type_desktop .w-nav-list.level_1 > .menu-item > a {
	padding: 0 <?php echo $param['indents']/2 ?>px;
	}
.header_ver .<?php echo $class ?>.type_desktop {
	line-height: <?php echo $param['indents'] ?>px;
	}
.<?php echo $class ?>.type_desktop .btn.w-nav-item.level_1 > .w-nav-anchor {
	margin: <?php echo $param['indents']/4 ?>px;
	}
.<?php echo $class ?>.type_desktop .w-nav-list.level_1 > .menu-item > a,
.<?php echo $class ?>.type_desktop [class*="columns"] .menu-item-has-children .w-nav-anchor.level_2 {
	font-size: <?php echo $param['font_size'] ?>px;
	}
.<?php echo $class ?>.type_desktop .submenu-languages .menu-item-language > a,
.<?php echo $class ?>.type_desktop .w-nav-anchor:not(.level_1) {
	font-size: <?php echo $param['dropdown_font_size'] ?>px;
	}
.<?php echo $class ?>.type_mobile .w-nav-anchor.level_1 {
	font-size: <?php echo $param['mobile_font_size'] ?>px;
	}
.<?php echo $class ?>.type_mobile .menu-item-language > a,
.<?php echo $class ?>.type_mobile .w-nav-anchor:not(.level_1) {
	font-size: <?php echo $param['mobile_dropdown_font_size'] ?>px;
	}
<?php endforeach; ?>

/* Additional Menu */
<?php foreach ( us_get_header_elms_of_a_type( 'additional_menu' ) as $class => $param ): ?>
@media (min-width: 901px) {
.<?php echo $class ?> {
	font-size: <?php echo $param['size'] ?>px;
	}
.header_hor .<?php echo $class ?> .w-menu-list {
	margin: 0 -<?php echo $param['indents']/2 ?>px;
	}
.header_hor .<?php echo $class ?> .w-menu-item {
	padding: 0 <?php echo $param['indents']/2 ?>px;
	}
.header_ver .<?php echo $class ?> .w-menu-list {
	line-height: <?php echo $param['indents'] ?>px;
	}
}
@media (min-width: 601px) and (max-width: 900px) {
.<?php echo $class ?> {
	font-size: <?php echo $param['size_tablets'] ?>px;
	}
.header_hor .<?php echo $class ?> .w-menu-list {
	margin: 0 -<?php echo $param['indents_tablets']/2 ?>px;
	}
.header_hor .<?php echo $class ?> .w-menu-item {
	padding: 0 <?php echo $param['indents_tablets']/2 ?>px;
	}
.header_ver .<?php echo $class ?> .w-menu-list {
	line-height: <?php echo $param['indents_tablets'] ?>px;
	}
}
@media (max-width: 600px) {
.<?php echo $class ?> {
	font-size: <?php echo $param['size_mobiles'] ?>px;
	}
.header_hor .<?php echo $class ?> .w-menu-list {
	margin: 0 -<?php echo $param['indents_mobiles']/2 ?>px;
	}
.header_hor .<?php echo $class ?> .w-menu-item {
	padding: 0 <?php echo $param['indents_mobiles']/2 ?>px;
	}
.header_ver .<?php echo $class ?> .w-menu-list {
	line-height: <?php echo $param['indents_mobiles'] ?>px;
	}
}
<?php endforeach; ?>

/* Search */
<?php foreach ( us_get_header_elms_of_a_type( 'search' ) as $class => $param ): ?>
@media (min-width: 901px) {
.<?php echo $class ?>.layout_simple {
	max-width: <?php echo $param['width'] ?>px;
	}
.<?php echo $class ?>.layout_modern.active {
	width: <?php echo $param['width'] ?>px;
	}
}
@media (min-width: 601px) and (max-width: 900px) {
.<?php echo $class ?>.layout_simple {
	max-width: <?php echo $param['width_tablets'] ?>px;
	}
.<?php echo $class ?>.layout_modern.active {
	width: <?php echo $param['width_tablets'] ?>px;
	}
}
<?php endforeach; ?>

/* Socials */
<?php foreach ( us_get_header_elms_of_a_type( 'socials' ) as $class => $param ): ?>
@media (min-width: 901px) {
.<?php echo $class ?> {
	font-size: <?php echo $param['size'] ?>px;
	}
}
@media (min-width: 601px) and (max-width: 900px) {
.<?php echo $class ?> {
	font-size: <?php echo $param['size_tablets'] ?>px;
	}
}
@media (max-width: 600px) {
.<?php echo $class ?> {
	font-size: <?php echo $param['size_mobiles'] ?>px;
	}
}
.<?php echo $class ?> .custom .w-socials-item-link-hover {
	background-color: <?php echo $param['custom_color'] ?>;
	}
.<?php echo $class ?>.style_colored .custom .w-socials-item-link {
	color: <?php echo $param['custom_color'] ?>;
	}
<?php endforeach; ?>

/* Dropdown */
<?php foreach ( us_get_header_elms_of_a_type( 'dropdown' ) as $class => $param ): ?>
@media (min-width: 901px) {
.<?php echo $class ?> .w-dropdown-h {
	font-size: <?php echo $param['size'] ?>px;
	}
}
@media (min-width: 601px) and (max-width: 900px) {
.<?php echo $class ?> .w-dropdown-h {
	font-size: <?php echo $param['size_tablets'] ?>px;
	}
}
@media (max-width: 600px) {
.<?php echo $class ?> .w-dropdown-h {
	font-size: <?php echo $param['size_mobiles'] ?>px;
	}
}
<?php endforeach; ?>

/* Cart */
<?php foreach ( us_get_header_elms_of_a_type( 'cart' ) as $class => $param ): ?>
@media (min-width: 901px) {
.<?php echo $class ?> .w-cart-link {
	font-size: <?php echo $param['size'] ?>px;
	}
}
@media (min-width: 601px) and (max-width: 900px) {
.<?php echo $class ?> .w-cart-link {
	font-size: <?php echo $param['size_tablets'] ?>px;
	}
}
@media (max-width: 600px) {
.<?php echo $class ?> .w-cart-link {
	font-size: <?php echo $param['size_mobiles'] ?>px;
	}
}
<?php endforeach; ?>

/* Design Options */
<?php echo us_get_header_design_options_css() ?>

/* Color Styles
   ========================================================================== */

html {
	background-color: <?php echo us_get_option( 'color_body_bg' ) ?>;
	}

/*************************** HEADER ***************************/

/* Top Header Colors */
.l-subheader.at_top,
.l-subheader.at_top .w-dropdown-list,
.header_hor .l-subheader.at_top .type_mobile .w-nav-list.level_1 {
	background-color: <?php echo us_get_option( 'color_header_top_bg' ) ?>;
	}
.l-subheader.at_top,
.l-subheader.at_top .w-dropdown.active,
.header_hor .l-subheader.at_top .type_mobile .w-nav-list.level_1 {
	color: <?php echo us_get_option( 'color_header_top_text' ) ?>;
	}
.no-touch .l-subheader.at_top a:hover,
.no-touch .l-subheader.at_top .w-cart-quantity,
.no-touch .l-header.bg_transparent .l-subheader.at_top .w-dropdown.active a:hover {
	color: <?php echo us_get_option( 'color_header_top_text_hover' ) ?>;
	}

/* Middle Header Colors */
.header_ver .l-header,
.header_hor .l-subheader.at_middle,
.l-subheader.at_middle .w-dropdown-list,
.header_hor .l-subheader.at_middle .type_mobile .w-nav-list.level_1 {
	background-color: <?php echo us_get_option( 'color_header_middle_bg' ) ?>;
	}
.l-subheader.at_middle,
.l-subheader.at_middle .w-dropdown.active,
.header_hor .l-subheader.at_middle .type_mobile .w-nav-list.level_1 {
	color: <?php echo us_get_option( 'color_header_middle_text' ) ?>;
	}
.no-touch .l-subheader.at_middle a:hover,
.no-touch .l-subheader.at_middle .w-cart-quantity,
.no-touch .l-header.bg_transparent .l-subheader.at_middle .w-dropdown.active a:hover {
	color: <?php echo us_get_option( 'color_header_middle_text_hover' ) ?>;
	}

/* Bottom Header Colors */
.l-subheader.at_bottom,
.l-subheader.at_bottom .w-dropdown-list,
.header_hor .l-subheader.at_bottom .type_mobile .w-nav-list.level_1 {
	background-color: <?php echo us_get_option( 'color_header_bottom_bg' ) ?>;
	}
.l-subheader.at_bottom,
.l-subheader.at_bottom .w-dropdown.active,
.header_hor .l-subheader.at_bottom .type_mobile .w-nav-list.level_1 {
	color: <?php echo us_get_option( 'color_header_bottom_text' ) ?>;
	}
.no-touch .l-subheader.at_bottom a:hover,
.no-touch .l-subheader.at_bottom .w-cart-quantity,
.no-touch .l-header.bg_transparent .l-subheader.at_bottom .w-dropdown.active a:hover {
	color: <?php echo us_get_option( 'color_header_bottom_text_hover' ) ?>;
	}

/* Transparent Header Colors */
.l-header.bg_transparent:not(.sticky) .l-subheader {
	color: <?php echo us_get_option( 'color_header_transparent_text' ) ?>;
	}
.no-touch .l-header.bg_transparent:not(.sticky) a:not(.w-nav-anchor):hover,
.no-touch .l-header.bg_transparent:not(.sticky) .type_desktop .menu-item-language > a:hover,
.no-touch .l-header.bg_transparent:not(.sticky) .type_desktop .menu-item-language:hover > a,
.no-touch .l-header.bg_transparent:not(.sticky) .type_desktop .w-nav-item.level_1:hover > .w-nav-anchor {
	color: <?php echo us_get_option( 'color_header_transparent_text_hover' ) ?>;
	}
.l-header.bg_transparent:not(.sticky) .w-nav-title:after {
	background-color: <?php echo us_get_option( 'color_header_transparent_text_hover' ) ?>;
	}
	
/* Search Colors */
.w-search-form {
	background-color: <?php echo us_get_option( 'color_header_search_bg' ) ?>;
	color: <?php echo us_get_option( 'color_header_search_text' ) ?>;
	}
.w-search.layout_fullscreen .w-search-form:before {
	background-color: <?php echo us_get_option( 'color_header_search_bg' ) ?>;
	}

/*************************** MAIN MENU ***************************/

/* Menu Hover Colors */
.no-touch .w-nav.type_desktop .menu-item-language:hover > a,
.no-touch .w-nav-item.level_1:hover > .w-nav-anchor {
	background-color: <?php echo us_get_option( 'color_menu_hover_bg' ) ?>;
	color: <?php echo us_get_option( 'color_menu_hover_text' ) ?>;
	}
.w-nav-title:after {
	background-color: <?php echo us_get_option( 'color_menu_hover_text' ) ?>;
	}

/* Menu Active Colors */
.w-nav-item.level_1.current-menu-item > .w-nav-anchor,
.w-nav-item.level_1.current-menu-parent > .w-nav-anchor,
.w-nav-item.level_1.current-menu-ancestor > .w-nav-anchor {
	background-color: <?php echo us_get_option( 'color_menu_active_bg' ) ?>;
	color: <?php echo us_get_option( 'color_menu_active_text' ) ?>;
	}

/* Transparent Menu Active Text Color */
.l-header.bg_transparent:not(.sticky) .type_desktop .w-nav-item.level_1.current-menu-item > .w-nav-anchor,
.l-header.bg_transparent:not(.sticky) .type_desktop .w-nav-item.level_1.current-menu-ancestor > .w-nav-anchor {
	color: <?php echo us_get_option( 'color_menu_transparent_active_text' ) ?>;
	}

/* Dropdown Colors */
.w-nav.type_desktop .submenu-languages,
.w-nav-list:not(.level_1) {
	background-color: <?php echo us_get_option( 'color_drop_bg' ) ?>;
	color: <?php echo us_get_option( 'color_drop_text' ) ?>;
	}

/* Dropdown Hover Colors */
.no-touch .w-nav.type_desktop .submenu-languages .menu-item-language:hover > a,
.no-touch .w-nav-item:not(.level_1):hover > .w-nav-anchor {
	background-color: <?php echo us_get_option( 'color_drop_hover_bg' ) ?>;
	color: <?php echo us_get_option( 'color_drop_hover_text' ) ?>;
	}

/* Dropdown Active Colors */
.w-nav-item:not(.level_1).current-menu-item > .w-nav-anchor,
.w-nav-item:not(.level_1).current-menu-parent > .w-nav-anchor,
.w-nav-item:not(.level_1).current-menu-ancestor > .w-nav-anchor {
	background-color: <?php echo us_get_option( 'color_drop_active_bg' ) ?>;
	color: <?php echo us_get_option( 'color_drop_active_text' ) ?>;
	}

/* Header Button */
.btn.w-menu-item,
.btn.w-nav-item .w-nav-anchor.level_1 {
	background-color: <?php echo us_get_option( 'color_menu_button_bg' ) ?> !important;
	color: <?php echo us_get_option( 'color_menu_button_text' ) ?> !important;
	}
.no-touch .btn.w-menu-item:hover,
.no-touch .btn.w-nav-item .w-nav-anchor.level_1:before {
	background-color: <?php echo us_get_option( 'color_menu_button_hover_bg' ) ?> !important;
	}
.no-touch .btn.w-menu-item:hover,
.no-touch .btn.w-nav-item .w-nav-anchor.level_1:hover {
	color: <?php echo us_get_option( 'color_menu_button_hover_text' ) ?> !important;
	}

/*************************** MAIN CONTENT ***************************/

/* Background Color */
.l-preloader,
.l-canvas,
.w-blog.layout_flat .w-blog-post-h,
.w-blog.layout_cards .w-blog-post-h,
.w-cart-dropdown,
.g-filters.style_1 .g-filters-item.active,
.no-touch .g-filters-item.active:hover,
.w-tabs.layout_default .w-tabs-item.active,
.w-tabs.layout_ver .w-tabs-item.active,
.no-touch .w-tabs.layout_default .w-tabs-item.active:hover,
.no-touch .w-tabs.layout_ver .w-tabs-item.active:hover,
.w-tabs.layout_timeline .w-tabs-item,
.w-tabs.layout_timeline .w-tabs-section-header-h,
.no-touch #lang_sel ul ul a:hover,
.no-touch #lang_sel_click ul ul a:hover,
#lang_sel_footer,
.us-woo-shop_modern .product-h,
.us-woo-shop_modern .product-meta,
.no-touch .us-woo-shop_trendy .product:hover .product-h,
.woocommerce-tabs .tabs li.active,
.no-touch .woocommerce-tabs .tabs li.active:hover,
.woocommerce .stars span:after,
.woocommerce .stars span a:after,
.woocommerce .shipping-calculator-form,
.woocommerce #payment .payment_box,
#bbp-user-navigation li.current,
.gform_wrapper .chosen-container-single .chosen-search input[type="text"],
.gform_wrapper .chosen-container-multi .chosen-choices li.search-choice {
	background-color: <?php echo us_get_option( 'color_content_bg' ) ?>;
	}
.woocommerce #payment .payment_methods li > input:checked + label,
.woocommerce .blockUI.blockOverlay {
	background-color: <?php echo us_get_option( 'color_content_bg' ) ?> !important;
	}
.w-tabs.layout_modern .w-tabs-item:after {
	border-bottom-color: <?php echo us_get_option( 'color_content_bg' ) ?>;
	}
a.w-btn.color_contrast,
.w-btn.color_contrast,
.no-touch a.w-btn.color_contrast:hover,
.no-touch .w-btn.color_contrast:hover,
.no-touch a.w-btn.color_contrast.style_outlined:hover,
.no-touch .w-btn.color_contrast.style_outlined:hover,
.w-iconbox.style_circle.color_contrast .w-iconbox-icon,
.tribe-events-calendar thead th {
	color: <?php echo us_get_option( 'color_content_bg' ) ?>;
	}

/* Alternate Background Color */
input,
textarea,
select,
.l-section.for_blogpost .w-blog-post-preview,
.w-actionbox.color_light,
.g-filters.style_1,
.g-filters.style_2 .g-filters-item.active,
.w-iconbox.style_circle.color_light .w-iconbox-icon,
.g-loadmore-btn,
.w-pricing-item-header,
.w-progbar-bar,
.w-progbar.style_3 .w-progbar-bar:before,
.w-progbar.style_3 .w-progbar-bar-count,
.w-tabs.layout_default .w-tabs-list,
.w-tabs.layout_ver .w-tabs-list,
.w-testimonial.style_4:before,
.no-touch .l-main .widget_nav_menu a:hover,
.l-content .wp-caption-text,
#lang_sel a,
#lang_sel_click a,
.smile-icon-timeline-wrap .timeline-wrapper .timeline-block,
.smile-icon-timeline-wrap .timeline-feature-item.feat-item,
.us-woo-shop_trendy .products .product-category > a,
.woocommerce .quantity .plus,
.woocommerce .quantity .minus,
.select2-container a.select2-choice,
.select2-drop .select2-search input,
.woocommerce-tabs .tabs,
.woocommerce .cart_totals,
.woocommerce-checkout #order_review,
.woocommerce ul.order_details,
#subscription-toggle,
#favorite-toggle,
#bbp-user-navigation,
.tablepress .row-hover tr:hover td,
.tribe-bar-views-list,
.tribe-events-day-time-slot h5,
.tribe-events-present,
.tribe-events-single-section,
.gform_wrapper .chosen-container-single .chosen-single,
.gform_wrapper .chosen-container .chosen-drop,
.gform_wrapper .chosen-container-multi .chosen-choices {
	background-color: <?php echo us_get_option( 'color_content_bg_alt' ) ?>;
	}
.timeline-wrapper .timeline-post-right .ult-timeline-arrow l,
.timeline-wrapper .timeline-post-left .ult-timeline-arrow l,
.timeline-feature-item.feat-item .ult-timeline-arrow l {
	border-color: <?php echo us_get_option( 'color_content_bg_alt' ) ?>;
	}

/* Border Color */
hr,
td,
th,
.l-section,
.g-cols > div,
.w-author,
.w-comments-list,
.w-pricing-item-h,
.w-profile,
.w-separator,
.w-sharing-item,
.w-tabs-list,
.w-tabs-section,
.w-tabs-section-header:before,
.w-tabs.layout_timeline.accordion .w-tabs-section-content,
.g-tags > a,
.w-testimonial.style_1,
.widget_calendar #calendar_wrap,
.l-main .widget_nav_menu > div,
.l-main .widget_nav_menu .menu-item a,
.widget_nav_menu .menu-item.menu-item-has-children + .menu-item > a,
.select2-container a.select2-choice,
.smile-icon-timeline-wrap .timeline-line,
.woocommerce .login,
.woocommerce .track_order,
.woocommerce .checkout_coupon,
.woocommerce .lost_reset_password,
.woocommerce .register,
.woocommerce .cart.variations_form,
.woocommerce .commentlist .comment-text,
.woocommerce .comment-respond,
.woocommerce .related,
.woocommerce .upsells,
.woocommerce .cross-sells,
.woocommerce .checkout #order_review,
.widget_price_filter .ui-slider-handle,
.widget_layered_nav ul,
.widget_layered_nav ul li,
#bbpress-forums fieldset,
.bbp-login-form fieldset,
#bbpress-forums .bbp-body > ul,
#bbpress-forums li.bbp-header,
.bbp-replies .bbp-body,
div.bbp-forum-header,
div.bbp-topic-header,
div.bbp-reply-header,
.bbp-pagination-links a,
.bbp-pagination-links span.current,
span.bbp-topic-pagination a.page-numbers,
.bbp-logged-in,
.tribe-events-list-separator-month span:before,
.tribe-events-list-separator-month span:after,
.type-tribe_events + .type-tribe_events,
.gform_wrapper .gsection,
.gform_wrapper .gf_page_steps,
.gform_wrapper li.gfield_creditcard_warning,
.form_saved_message {
	border-color: <?php echo us_get_option( 'color_content_border' ) ?>;
	}
.w-separator,
.w-iconbox.color_light .w-iconbox-icon,
.w-testimonial.style_3 .w-testimonial-text:after,
.w-testimonial.style_3 .w-testimonial-text:before {
	color: <?php echo us_get_option( 'color_content_border' ) ?>;
	}
a.w-btn.color_light,
.w-btn.color_light,
.w-btn.color_light.style_outlined:before,
.w-btn.w-blog-post-more:before,
.w-iconbox.style_circle.color_light .w-iconbox-icon,
.no-touch .g-loadmore-btn:hover,
.woocommerce .button,
.no-touch .woocommerce .quantity .plus:hover,
.no-touch .woocommerce .quantity .minus:hover,
.no-touch .woocommerce #payment .payment_methods li > label:hover,
.widget_price_filter .ui-slider,
#tribe-bar-collapse-toggle,
.gform_wrapper .gform_page_footer .gform_previous_button {
	background-color: <?php echo us_get_option( 'color_content_border' ) ?>;
	}
a.w-btn.color_light.style_outlined,
.w-btn.color_light.style_outlined,
.w-btn.w-blog-post-more,
.w-iconbox.style_outlined.color_light .w-iconbox-icon,
.w-person-links-item,
.w-socials-item-link,
.pagination .page-numbers {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_content_border' ) ?> inset;
	}
.w-tabs.layout_trendy .w-tabs-list {
	box-shadow: 0 -1px 0 <?php echo us_get_option( 'color_content_border' ) ?> inset;
	}

/* Heading Color */
h1, h2, h3, h4, h5, h6,
.w-counter-number,
.w-pricing-item-header,
.woocommerce .product .price,
.gform_wrapper .chosen-container-single .chosen-single {
	color: <?php echo us_get_option( 'color_content_heading' ) ?>;
	}
.w-progbar.color_contrast .w-progbar-bar-h {
	background-color: <?php echo us_get_option( 'color_content_heading' ) ?>;
	}

/* Text Color */
input,
textarea,
select,
.l-canvas,
a.w-btn.color_contrast.style_outlined,
.w-btn.color_contrast.style_outlined,
.w-cart-dropdown,
.w-form-row-field:before,
.w-iconbox.color_contrast .w-iconbox-icon,
.w-iconbox.color_light.style_circle .w-iconbox-icon,
.w-tabs.layout_timeline .w-tabs-item,
.w-tabs.layout_timeline .w-tabs-section-header-h,
.woocommerce .button {
	color: <?php echo us_get_option( 'color_content_text' ) ?>;
	}
a.w-btn.color_light,
.w-btn.color_light,
.w-btn.w-blog-post-more {
	color: <?php echo us_get_option( 'color_content_text' ) ?> !important;
	}
a.w-btn.color_contrast,
.w-btn.color_contrast,
.w-btn.color_contrast.style_outlined:before,
.w-iconbox.style_circle.color_contrast .w-iconbox-icon,
.tribe-mobile #tribe-events-footer a,
.tribe-events-calendar thead th {
	background-color: <?php echo us_get_option( 'color_content_text' ) ?>;
	}
.tribe-events-calendar thead th {
	border-color: <?php echo us_get_option( 'color_content_text' ) ?>;
	}
a.w-btn.color_contrast.style_outlined,
.w-btn.color_contrast.style_outlined,
.w-iconbox.style_outlined.color_contrast .w-iconbox-icon {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_content_text' ) ?> inset;
	}

/* Link Color */
a {
	color: <?php echo us_get_option( 'color_content_link' ) ?>;
	}

/* Link Hover Color */
.no-touch a:hover,
.no-touch a:hover + .w-blog-post-body .w-blog-post-title a,
.no-touch .w-blog-post-title a:hover {
	color: <?php echo us_get_option( 'color_content_link_hover' ) ?>;
	}
.no-touch .w-cart-dropdown a:not(.button):hover {
	color: <?php echo us_get_option( 'color_content_link_hover' ) ?> !important;
	}

/* Primary Color */
.highlight_primary,
.l-preloader,
.no-touch .l-titlebar .g-nav-item:hover,
a.w-btn.color_primary.style_outlined,
.w-btn.color_primary.style_outlined,
.l-main .w-contacts-item:before,
.w-counter.color_primary .w-counter-number,
.g-filters-item.active,
.no-touch .g-filters.style_1 .g-filters-item.active:hover,
.no-touch .g-filters.style_2 .g-filters-item.active:hover,
.w-form-row.focused .w-form-row-field:before,
.w-iconbox.color_primary .w-iconbox-icon,
.no-touch .w-iconbox-link:hover .w-iconbox-title,
.no-touch .w-logos .owl-prev:hover,
.no-touch .w-logos .owl-next:hover,
.w-separator.color_primary,
.w-sharing.type_outlined.color_primary .w-sharing-item,
.no-touch .w-sharing.type_simple.color_primary .w-sharing-item:hover .w-sharing-icon,
.w-tabs.layout_default .w-tabs-item.active,
.w-tabs.layout_trendy .w-tabs-item.active,
.w-tabs.layout_ver .w-tabs-item.active,
.w-tabs-section.active .w-tabs-section-header,
.no-touch .g-tags > a:hover,
.w-testimonial.style_2:before,
.us-woo-shop_standard .product-h .button,
.woocommerce .star-rating span:before,
.woocommerce-tabs .tabs li.active,
.no-touch .woocommerce-tabs .tabs li.active:hover,
.woocommerce .stars span a:after,
.woocommerce #payment .payment_methods li > input:checked + label,
#subscription-toggle span.is-subscribed:before,
#favorite-toggle span.is-favorite:before {
	color: <?php echo us_get_option( 'color_content_primary' ) ?>;
	}
.l-section.color_primary,
.l-titlebar.color_primary,
.no-touch .l-navigation-item:hover .l-navigation-item-arrow,
.highlight_primary_bg,
.w-actionbox.color_primary,
.w-blog-post-preview-icon,
.w-blog.layout_cards .format-quote .w-blog-post-h,
button,
input[type="submit"],
a.w-btn.color_primary,
.w-btn.color_primary,
.w-btn.color_primary.style_outlined:before,
.no-touch .g-filters-item:hover,
.w-iconbox.style_circle.color_primary .w-iconbox-icon,
.no-touch .w-iconbox.style_circle .w-iconbox-icon:before,
.no-touch .w-iconbox.style_outlined .w-iconbox-icon:before,
.no-touch .w-person-links,
.w-pricing-item.type_featured .w-pricing-item-header,
.w-progbar.color_primary .w-progbar-bar-h,
.w-sharing.type_solid.color_primary .w-sharing-item,
.w-sharing.type_fixed.color_primary .w-sharing-item,
.w-sharing.type_outlined.color_primary .w-sharing-item:before,
.w-tabs.layout_modern .w-tabs-list,
.w-tabs.layout_trendy .w-tabs-item:after,
.w-tabs.layout_timeline .w-tabs-item:before,
.w-tabs.layout_timeline .w-tabs-section-header-h:before,
.no-touch .w-header-show:hover,
.no-touch .w-toplink.active:hover,
.no-touch .pagination .page-numbers:before,
.pagination .page-numbers.current,
.l-main .widget_nav_menu .menu-item.current-menu-item > a,
.rsDefault .rsThumb.rsNavSelected,
.no-touch .tp-leftarrow.tparrows.custom:before,
.no-touch .tp-rightarrow.tparrows.custom:before,
.smile-icon-timeline-wrap .timeline-separator-text .sep-text,
.smile-icon-timeline-wrap .timeline-wrapper .timeline-dot,
.smile-icon-timeline-wrap .timeline-feature-item .timeline-dot,
.tablepress .sorting:hover,
.tablepress .sorting_asc,
.tablepress .sorting_desc,
p.demo_store,
.woocommerce .button.alt,
.woocommerce .button.checkout,
.woocommerce .product-h .button.loading,
.no-touch .woocommerce .product-h .button:hover,
.woocommerce .onsale,
.widget_price_filter .ui-slider-range,
.widget_layered_nav ul li.chosen,
.widget_layered_nav_filters ul li a,
.no-touch .bbp-pagination-links a:hover,
.bbp-pagination-links span.current,
.no-touch span.bbp-topic-pagination a.page-numbers:hover,
.tribe-events-calendar td.mobile-active,
.tribe-events-button,
.datepicker td.day.active,
.datepicker td span.active,
.gform_wrapper .gform_page_footer .gform_next_button,
.gform_wrapper .gf_progressbar_percentage,
.gform_wrapper .chosen-container .chosen-results li.highlighted,
.l-body .cl-btn {
	background-color: <?php echo us_get_option( 'color_content_primary' ) ?>;
	}
.l-content blockquote,
.no-touch .l-titlebar .g-nav-item:hover,
.g-filters.style_3 .g-filters-item.active,
.no-touch .w-logos .owl-prev:hover,
.no-touch .w-logos .owl-next:hover,
.no-touch .w-logos.style_1 .w-logos-item:hover,
.w-separator.color_primary,
.w-tabs.layout_default .w-tabs-item.active,
.w-tabs.layout_ver .w-tabs-item.active,
.no-touch .g-tags > a:hover,
.no-touch .w-testimonial.style_1:hover,
.l-main .widget_nav_menu .menu-item.current-menu-item > a,
.woocommerce-tabs .tabs li.active,
.no-touch .woocommerce-tabs .tabs li.active:hover,
.widget_layered_nav ul li.chosen,
.bbp-pagination-links span.current,
.no-touch #bbpress-forums .bbp-pagination-links a:hover,
.no-touch #bbpress-forums .bbp-topic-pagination a:hover,
#bbp-user-navigation li.current {
	border-color: <?php echo us_get_option( 'color_content_primary' ) ?>;
	}
a.w-btn.color_primary.style_outlined,
.w-btn.color_primary.style_outlined,
.l-main .w-contacts-item:before,
.w-iconbox.color_primary.style_outlined .w-iconbox-icon,
.w-sharing.type_outlined.color_primary .w-sharing-item,
.w-tabs.layout_timeline .w-tabs-item,
.w-tabs.layout_timeline .w-tabs-section-header-h,
.us-woo-shop_standard .product-h .button {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_content_primary' ) ?> inset;
	}
input:focus,
textarea:focus,
select:focus,
.tp-bullets.custom .tp-bullet.selected {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_content_primary' ) ?>;
	}

/* Secondary Color */
.highlight_secondary,
.no-touch .w-blognav-prev:hover .w-blognav-title,
.no-touch .w-blognav-next:hover .w-blognav-title,
a.w-btn.color_secondary.style_outlined,
.w-btn.color_secondary.style_outlined,
.w-counter.color_secondary .w-counter-number,
.w-iconbox.color_secondary .w-iconbox-icon,
.w-separator.color_secondary,
.w-sharing.type_outlined.color_secondary .w-sharing-item,
.no-touch .w-sharing.type_simple.color_secondary .w-sharing-item:hover .w-sharing-icon {
	color: <?php echo us_get_option( 'color_content_secondary' ) ?>;
	}
.l-section.color_secondary,
.l-titlebar.color_secondary,
.highlight_secondary_bg,
.no-touch .w-blog.layout_cards .w-blog-post-meta-category a:hover,
.no-touch .w-blog.layout_tiles .w-blog-post-meta-category a:hover,
.no-touch .l-section.preview_trendy .w-blog-post-meta-category a:hover,
.no-touch input[type="submit"]:hover,
a.w-btn.color_secondary,
.w-btn.color_secondary,
.w-btn.color_secondary.style_outlined:before,
.w-actionbox.color_secondary,
.w-iconbox.style_circle.color_secondary .w-iconbox-icon,
.w-progbar.color_secondary .w-progbar-bar-h,
.w-sharing.type_solid.color_secondary .w-sharing-item,
.w-sharing.type_fixed.color_secondary .w-sharing-item,
.w-sharing.type_outlined.color_secondary .w-sharing-item:before,
.no-touch .woocommerce .button:hover,
.no-touch .woocommerce .product-remove a.remove:hover,
.no-touch .tribe-events-button:hover,
.no-touch .widget_layered_nav_filters ul li a:hover {
	background-color: <?php echo us_get_option( 'color_content_secondary' ) ?>;
	}
.w-separator.color_secondary {
	border-color: <?php echo us_get_option( 'color_content_secondary' ) ?>;
	}
a.w-btn.color_secondary.style_outlined,
.w-btn.color_secondary.style_outlined,
.w-iconbox.color_secondary.style_outlined .w-iconbox-icon,
.w-sharing.type_outlined.color_secondary .w-sharing-item {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_content_secondary' ) ?> inset;
	}

/* Fade Elements Color */
.highlight_faded,
.w-author-url,
.w-blog-post-meta > *,
.w-profile-link.for_logout,
.w-testimonial-person-meta,
.w-testimonial.style_4:before,
.l-main .widget_tag_cloud,
.l-main .widget_product_tag_cloud,
.woocommerce-breadcrumb,
.woocommerce .star-rating:before,
.woocommerce .stars span:after,
p.bbp-topic-meta,
.bbp_widget_login .logout-link {
	color: <?php echo us_get_option( 'color_content_faded' ) ?>;
	}
.w-blog.layout_latest .w-blog-post-meta-date {
	border-color: <?php echo us_get_option( 'color_content_faded' ) ?>;
	}
.tribe-events-cost,
.tribe-events-list .tribe-events-event-cost {
	background-color: <?php echo us_get_option( 'color_content_faded' ) ?>;
	}

/*************************** ALTERNATE CONTENT ***************************/

/* Background Color */
.l-section.color_alternate,
.l-titlebar.color_alternate,
.color_alternate .g-filters.style_1 .g-filters-item.active,
.no-touch .color_alternate .g-filters-item.active:hover,
.color_alternate .w-tabs.layout_default .w-tabs-item.active,
.no-touch .color_alternate .w-tabs.layout_default .w-tabs-item.active:hover,
.color_alternate .w-tabs.layout_ver .w-tabs-item.active,
.no-touch .color_alternate .w-tabs.layout_ver .w-tabs-item.active:hover,
.color_alternate .w-tabs.layout_timeline .w-tabs-item,
.color_alternate .w-tabs.layout_timeline .w-tabs-section-header-h {
	background-color: <?php echo us_get_option( 'color_alt_content_bg' ) ?>;
	}
.color_alternate a.w-btn.color_contrast,
.color_alternate .w-btn.color_contrast,
.no-touch .color_alternate a.w-btn.color_contrast:hover,
.no-touch .color_alternate .w-btn.color_contrast:hover,
.no-touch .color_alternate a.w-btn.color_contrast.style_outlined:hover,
.no-touch .color_alternate .w-btn.color_contrast.style_outlined:hover,
.color_alternate .w-iconbox.style_circle.color_contrast .w-iconbox-icon {
	color: <?php echo us_get_option( 'color_alt_content_bg' ) ?>;
	}
.color_alternate .w-tabs.layout_modern .w-tabs-item:after {
	border-bottom-color: <?php echo us_get_option( 'color_alt_content_bg' ) ?>;
	}

/* Alternate Background Color */
.color_alternate input,
.color_alternate textarea,
.color_alternate select,
.color_alternate .w-blog-post-preview-icon,
.color_alternate .w-blog.layout_flat .w-blog-post-h,
.color_alternate .w-blog.layout_cards .w-blog-post-h,
.color_alternate .g-filters.style_1,
.color_alternate .g-filters.style_2 .g-filters-item.active,
.color_alternate .w-iconbox.style_circle.color_light .w-iconbox-icon,
.color_alternate .g-loadmore-btn,
.color_alternate .w-pricing-item-header,
.color_alternate .w-progbar-bar,
.color_alternate .w-tabs.layout_default .w-tabs-list,
.color_alternate .w-testimonial.style_4:before,
.l-content .color_alternate .wp-caption-text {
	background-color: <?php echo us_get_option( 'color_alt_content_bg_alt' ) ?>;
	}

/* Border Color */
.l-section.color_alternate,
.l-section.color_alternate hr,
.l-section.color_alternate th,
.l-section.color_alternate td,
.color_alternate .g-cols > div,
.color_alternate .w-blog-post,
.color_alternate .w-comments-list,
.color_alternate .w-pricing-item-h,
.color_alternate .w-profile,
.color_alternate .w-separator,
.color_alternate .w-tabs-list,
.color_alternate .w-tabs-section,
.color_alternate .w-tabs-section-header:before,
.color_alternate .w-tabs.layout_timeline.accordion .w-tabs-section-content,
.color_alternate .w-testimonial.style_1 {
	border-color: <?php echo us_get_option( 'color_alt_content_border' ) ?>;
	}
.color_alternate .w-separator,
.color_alternate .w-iconbox.color_light .w-iconbox-icon,
.color_alternate .w-testimonial.style_3 .w-testimonial-text:after,
.color_alternate .w-testimonial.style_3 .w-testimonial-text:before {
	color: <?php echo us_get_option( 'color_alt_content_border' ) ?>;
	}
.color_alternate a.w-btn.color_light,
.color_alternate .w-btn.color_light,
.color_alternate .w-btn.color_light.style_outlined:before,
.color_alternate .w-btn.w-blog-post-more:before,
.color_alternate .w-iconbox.style_circle.color_light .w-iconbox-icon,
.no-touch .color_alternate .g-loadmore-btn:hover {
	background-color: <?php echo us_get_option( 'color_alt_content_border' ) ?>;
	}
.color_alternate a.w-btn.color_light.style_outlined,
.color_alternate .w-btn.color_light.style_outlined,
.color_alternate .w-btn.w-blog-post-more,
.color_alternate .w-iconbox.style_outlined.color_light .w-iconbox-icon,
.color_alternate .w-person-links-item,
.color_alternate .w-socials-item-link,
.color_alternate .pagination .page-numbers {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_alt_content_border' ) ?> inset;
	}
.color_alternate .w-tabs.layout_trendy .w-tabs-list {
	box-shadow: 0 -1px 0 <?php echo us_get_option( 'color_alt_content_border' ) ?> inset;
	}

/* Heading Color */
.color_alternate h1,
.color_alternate h2,
.color_alternate h3,
.color_alternate h4,
.color_alternate h5,
.color_alternate h6,
.color_alternate .w-counter-number,
.color_alternate .w-pricing-item-header {
	color: <?php echo us_get_option( 'color_alt_content_heading' ) ?>;
	}
.color_alternate .w-progbar.color_contrast .w-progbar-bar-h {
	background-color: <?php echo us_get_option( 'color_alt_content_heading' ) ?>;
	}

/* Text Color */
.l-titlebar.color_alternate,
.l-section.color_alternate,
.color_alternate input,
.color_alternate textarea,
.color_alternate select,
.color_alternate a.w-btn.color_contrast.style_outlined,
.color_alternate .w-btn.color_contrast.style_outlined,
.color_alternate .w-form-row-field:before,
.color_alternate .w-iconbox.color_contrast .w-iconbox-icon,
.color_alternate .w-iconbox.color_light.style_circle .w-iconbox-icon,
.color_alternate .w-tabs.layout_timeline .w-tabs-item,
.color_alternate .w-tabs.layout_timeline .w-tabs-section-header-h {
	color: <?php echo us_get_option( 'color_alt_content_text' ) ?>;
	}
.color_alternate a.w-btn.color_light,
.color_alternate .w-btn.color_light,
.color_alternate .w-btn.w-blog-post-more {
	color: <?php echo us_get_option( 'color_alt_content_text' ) ?> !important;
	}
.color_alternate a.w-btn.color_contrast,
.color_alternate .w-btn.color_contrast,
.color_alternate .w-btn.color_contrast.style_outlined:before,
.color_alternate .w-iconbox.style_circle.color_contrast .w-iconbox-icon {
	background-color: <?php echo us_get_option( 'color_alt_content_text' ) ?>;
	}
.color_alternate a.w-btn.color_contrast.style_outlined,
.color_alternate .w-btn.color_contrast.style_outlined,
.color_alternate .w-iconbox.style_outlined.color_contrast .w-iconbox-icon {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_alt_content_text' ) ?> inset;
	}
	
/* Link Color */
.color_alternate a {
	color: <?php echo us_get_option( 'color_alt_content_link' ) ?>;
	}

/* Link Hover Color */
.no-touch .color_alternate a:hover,
.no-touch .color_alternate a:hover + .w-blog-post-body .w-blog-post-title a,
.no-touch .color_alternate .w-blog-post-title a:hover {
	color: <?php echo us_get_option( 'color_alt_content_link_hover' ) ?>;
	}

/* Primary Color */
.color_alternate .highlight_primary,
.no-touch .l-titlebar.color_alternate .g-nav-item:hover,
.color_alternate a.w-btn.color_primary.style_outlined,
.color_alternate .w-btn.color_primary.style_outlined,
.l-main .color_alternate .w-contacts-item:before,
.color_alternate .w-counter.color_primary .w-counter-number,
.color_alternate .g-filters-item.active,
.no-touch .color_alternate .g-filters-item.active:hover,
.color_alternate .w-form-row.focused .w-form-row-field:before,
.color_alternate .w-iconbox.color_primary .w-iconbox-icon,
.no-touch .color_alternate .w-iconbox-link:hover .w-iconbox-title,
.no-touch .color_alternate .w-logos .owl-prev:hover,
.no-touch .color_alternate .w-logos .owl-next:hover,
.color_alternate .w-separator.color_primary,
.color_alternate .w-tabs.layout_default .w-tabs-item.active,
.color_alternate .w-tabs.layout_trendy .w-tabs-item.active,
.color_alternate .w-tabs.layout_ver .w-tabs-item.active,
.color_alternate .w-tabs-section.active .w-tabs-section-header,
.color_alternate .w-testimonial.style_2:before {
	color: <?php echo us_get_option( 'color_alt_content_primary' ) ?>;
	}
.color_alternate .highlight_primary_bg,
.color_alternate .w-actionbox.color_primary,
.color_alternate .w-blog-post-preview-icon,
.color_alternate .w-blog.layout_cards .format-quote .w-blog-post-h,
.color_alternate button,
.color_alternate input[type="submit"],
.color_alternate a.w-btn.color_primary,
.color_alternate .w-btn.color_primary,
.color_alternate .w-btn.color_primary.style_outlined:before,
.no-touch .color_alternate .g-filters-item:hover,
.color_alternate .w-iconbox.style_circle.color_primary .w-iconbox-icon,
.no-touch .color_alternate .w-iconbox.style_circle .w-iconbox-icon:before,
.no-touch .color_alternate .w-iconbox.style_outlined .w-iconbox-icon:before,
.no-touch .color_alternate .w-person-links,
.color_alternate .w-pricing-item.type_featured .w-pricing-item-header,
.color_alternate .w-progbar.color_primary .w-progbar-bar-h,
.color_alternate .w-tabs.layout_modern .w-tabs-list,
.color_alternate .w-tabs.layout_trendy .w-tabs-item:after,
.color_alternate .w-tabs.layout_timeline .w-tabs-item:before,
.color_alternate .w-tabs.layout_timeline .w-tabs-section-header-h:before,
.no-touch .color_alternate .pagination .page-numbers:before,
.color_alternate .pagination .page-numbers.current {
	background-color: <?php echo us_get_option( 'color_alt_content_primary' ) ?>;
	}
.l-content .color_alternate blockquote,
.no-touch .l-titlebar.color_alternate .g-nav-item:hover,
.color_alternate .g-filters.style_3 .g-filters-item.active,
.no-touch .color_alternate .w-logos .owl-prev:hover,
.no-touch .color_alternate .w-logos .owl-next:hover,
.no-touch .color_alternate .w-logos.style_1 .w-logos-item:hover,
.color_alternate .w-separator.color_primary,
.color_alternate .w-tabs.layout_default .w-tabs-item.active,
.color_alternate .w-tabs.layout_ver .w-tabs-item.active,
.no-touch .color_alternate .w-tabs.layout_default .w-tabs-item.active:hover,
.no-touch .color_alternate .w-tabs.layout_ver .w-tabs-item.active:hover,
.no-touch .color_alternate .g-tags > a:hover,
.no-touch .color_alternate .w-testimonial.style_1:hover {
	border-color: <?php echo us_get_option( 'color_alt_content_primary' ) ?>;
	}
.color_alternate a.w-btn.color_primary.style_outlined,
.color_alternate .w-btn.color_primary.style_outlined,
.l-main .color_alternate .w-contacts-item:before,
.color_alternate .w-iconbox.color_primary.style_outlined .w-iconbox-icon,
.color_alternate .w-tabs.layout_timeline .w-tabs-item,
.color_alternate .w-tabs.layout_timeline .w-tabs-section-header-h {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_alt_content_primary' ) ?> inset;
	}
.color_alternate input:focus,
.color_alternate textarea:focus,
.color_alternate select:focus {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_alt_content_primary' ) ?>;
	}

/* Secondary Color */
.color_alternate .highlight_secondary,
.color_alternate a.w-btn.color_secondary.style_outlined,
.color_alternate .w-btn.color_secondary.style_outlined,
.color_alternate .w-counter.color_secondary .w-counter-number,
.color_alternate .w-iconbox.color_secondary .w-iconbox-icon,
.color_alternate .w-separator.color_secondary {
	color: <?php echo us_get_option( 'color_alt_content_secondary' ) ?>;
	}
.color_alternate .highlight_secondary_bg,
.no-touch .color_alternate input[type="submit"]:hover,
.color_alternate a.w-btn.color_secondary,
.color_alternate .w-btn.color_secondary,
.color_alternate .w-btn.color_secondary.style_outlined:before,
.color_alternate .w-actionbox.color_secondary,
.color_alternate .w-iconbox.style_circle.color_secondary .w-iconbox-icon,
.color_alternate .w-progbar.color_secondary .w-progbar-bar-h {
	background-color: <?php echo us_get_option( 'color_alt_content_secondary' ) ?>;
	}
.color_alternate .w-separator.color_secondary {
	border-color: <?php echo us_get_option( 'color_alt_content_secondary' ) ?>;
	}
.color_alternate a.w-btn.color_secondary.style_outlined,
.color_alternate .w-btn.color_secondary.style_outlined,
.color_alternate .w-iconbox.color_secondary.style_outlined .w-iconbox-icon {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_alt_content_secondary' ) ?> inset;
	}

/* Fade Elements Color */
.color_alternate .highlight_faded,
.color_alternate .w-blog-post-meta > *,
.color_alternate .w-profile-link.for_logout,
.color_alternate .w-testimonial-person-meta,
.color_alternate .w-testimonial.style_4:before {
	color: <?php echo us_get_option( 'color_alt_content_faded' ) ?>;
	}
.color_alternate .w-blog.layout_latest .w-blog-post-meta-date {
	border-color: <?php echo us_get_option( 'color_alt_content_faded' ) ?>;
	}

/*************************** SUBFOOTER ***************************/

/* Background Color */
.l-subfooter.at_top,
.no-touch .l-subfooter.at_top #lang_sel ul ul a:hover,
.no-touch .l-subfooter.at_top #lang_sel_click ul ul a:hover {
	background-color: <?php echo us_get_option( 'color_subfooter_bg' ) ?>;
	}

/* Alternate Background Color */
.l-subfooter.at_top input,
.l-subfooter.at_top textarea,
.l-subfooter.at_top select,
.no-touch .l-subfooter.at_top #lang_sel a,
.no-touch .l-subfooter.at_top #lang_sel_click a {
	background-color: <?php echo us_get_option( 'color_subfooter_bg_alt' ) ?>;
	}

/* Border Color */
.l-subfooter.at_top,
.l-subfooter.at_top .w-profile,
.l-subfooter.at_top .widget_calendar #calendar_wrap {
	border-color: <?php echo us_get_option( 'color_subfooter_border' ) ?>;
	}
.l-subfooter.at_top .w-socials-item-link {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_subfooter_border' ) ?> inset;
	}

/* Heading Color */
.l-subfooter.at_top h1,
.l-subfooter.at_top h2,
.l-subfooter.at_top h3,
.l-subfooter.at_top h4,
.l-subfooter.at_top h5,
.l-subfooter.at_top h6,
.l-subfooter.at_top input,
.l-subfooter.at_top textarea,
.l-subfooter.at_top select,
.l-subfooter.at_top .w-form-row-field:before {
	color: <?php echo us_get_option( 'color_subfooter_heading' ) ?>;
	}

/* Text Color */
.l-subfooter.at_top {
	color: <?php echo us_get_option( 'color_subfooter_text' ) ?>;
	}

/* Link Color */
.l-subfooter.at_top a,
.l-subfooter.at_top .widget_tag_cloud .tagcloud a,
.l-subfooter.at_top .widget_product_tag_cloud .tagcloud a {
	color: <?php echo us_get_option( 'color_subfooter_link' ) ?>;
	}

/* Link Hover Color */
.no-touch .l-subfooter.at_top a:hover,
.no-touch .l-subfooter.at_top .w-form-row.focused .w-form-row-field:before,
.no-touch .l-subfooter.at_top .widget_tag_cloud .tagcloud a:hover,
.no-touch .l-subfooter.at_top .widget_product_tag_cloud .tagcloud a:hover {
	color: <?php echo us_get_option( 'color_subfooter_link_hover' ) ?>;
	}
.l-subfooter.at_top input:focus,
.l-subfooter.at_top textarea:focus,
.l-subfooter.at_top select:focus {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_subfooter_link_hover' ) ?>;
	}

/*************************** FOOTER ***************************/

/* Background Color */
.l-subfooter.at_bottom {
	background-color: <?php echo us_get_option( 'color_footer_bg' ) ?>;
	}

/* Text Color */
.l-subfooter.at_bottom {
	color: <?php echo us_get_option( 'color_footer_text' ) ?>;
	}

/* Link Color */
.l-subfooter.at_bottom a {
	color: <?php echo us_get_option( 'color_footer_link' ) ?>;
	}

/* Link Hover Color */
.no-touch .l-subfooter.at_bottom a:hover {
	color: <?php echo us_get_option( 'color_footer_link_hover' ) ?>;
	}

<?php echo us_get_option( 'custom_css', '' ) ?>

<?php if ( FALSE ): ?>/* Setting IDE context */</style><?php endif; ?>
