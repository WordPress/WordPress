<?php
/* ------------------------------------------------------------------------- *
 *  Dynamic styles
/* ------------------------------------------------------------------------- */

/*  Convert hexadecimal to rgb
/* ------------------------------------ */
if ( ! function_exists( 'alx_hex2rgb' ) ) {

	function alx_hex2rgb( $hex, $array=false ) {
		$hex = str_replace("#", "", $hex);

		if ( strlen($hex) == 3 ) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}

		$rgb = array( $r, $g, $b );
		if ( !$array ) { $rgb = implode(",", $rgb); }
		return $rgb;
	}
	
}	


/*  Google fonts
/* ------------------------------------ */
if ( ! function_exists( 'alx_google_fonts' ) ) {

	function alx_google_fonts () {
		if ( ot_get_option('dynamic-styles') != 'off' ) {
			if ( ot_get_option( 'font' ) == 'titillium-web-ext' ) { echo '<link href="http://fonts.googleapis.com/css?family=Titillium+Web:400,400italic,300italic,300,600&subset=latin,latin-ext" rel="stylesheet" type="text/css">'. "\n"; }
			if ( ot_get_option( 'font' ) == 'droid-serif' ) { echo '<link href="http://fonts.googleapis.com/css?family=Droid+Serif:400,400italic,700" rel="stylesheet" type="text/css">'. "\n"; }
			if ( ot_get_option( 'font' ) == 'source-sans-pro' ) { echo '<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300italic,300,400italic,600&subset=latin,latin-ext" rel="stylesheet" type="text/css">'. "\n"; }
			if ( ot_get_option( 'font' ) == 'lato' ) { echo '<link href="http://fonts.googleapis.com/css?family=Lato:400,300,300italic,400italic,700" rel="stylesheet" type="text/css">'. "\n"; }
			if ( ot_get_option( 'font' ) == 'ubuntu' ) { echo '<link href="http://fonts.googleapis.com/css?family=Ubuntu:400,400italic,300italic,300,700&subset=latin,latin-ext" rel="stylesheet" type="text/css">'. "\n"; }
			if ( ot_get_option( 'font' ) == 'ubuntu-cyr' ) { echo '<link href="http://fonts.googleapis.com/css?family=Ubuntu:400,400italic,300italic,300,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">'. "\n"; }
			if ( ot_get_option( 'font' ) == 'roboto-condensed' ) { echo '<link href="http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300italic,300,400italic,700&subset=latin,latin-ext" rel="stylesheet" type="text/css">'. "\n"; }
			if ( ot_get_option( 'font' ) == 'roboto-condensed-cyr' ) { echo '<link href="http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300italic,300,400italic,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">'. "\n"; }
			if ( ot_get_option( 'font' ) == 'open-sans' ) { echo '<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,300italic,300,600&subset=latin,latin-ext" rel="stylesheet" type="text/css">'. "\n"; }
			if ( ot_get_option( 'font' ) == 'open-sans-cyr' ) { echo '<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,300italic,300,600&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">'. "\n"; }
			if ( ot_get_option( 'font' ) == 'pt-serif' ) { echo '<link href="http://fonts.googleapis.com/css?family=PT+Serif:400,700,400italic&subset=latin,latin-ext" rel="stylesheet" type="text/css">'. "\n"; }
			if ( ot_get_option( 'font' ) == 'pt-serif-cyr' ) { echo '<link href="http://fonts.googleapis.com/css?family=PT+Serif:400,700,400italic&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">'. "\n"; }
		}
	}	
	
}
add_action( 'wp_head', 'alx_google_fonts', 2 );	


/*  Dynamic css output
/* ------------------------------------ */
if ( ! function_exists( 'alx_dynamic_css' ) ) {

	function alx_dynamic_css() {
		if ( ot_get_option('dynamic-styles') != 'off' ) {
		
			// rgb values
			$color_1 = ot_get_option('color-1');
			$color_1_rgb = alx_hex2rgb($color_1);
			
			// start output
			$styles = '<style type="text/css">'."\n";
			$styles .= '/* Dynamic CSS: For no styles in head, copy and put the css below in your child theme\'s style.css, disable dynamic styles */'."\n";		
			
			// google fonts
			if ( ot_get_option( 'font' ) == 'titillium-web-ext' ) { $styles .= 'body { font-family: "Titillium Web", Arial, sans-serif; }'."\n"; }
			if ( ot_get_option( 'font' ) == 'droid-serif' ) { $styles .= 'body { font-family: "Droid Serif", serif; }'."\n"; }
			if ( ot_get_option( 'font' ) == 'source-sans-pro' ) { $styles .= 'body { font-family: "Source Sans Pro", Arial, sans-serif; }'."\n"; }
			if ( ot_get_option( 'font' ) == 'lato' ) { $styles .= 'body { font-family: "Lato", Arial, sans-serif; }'."\n"; }
			if ( ( ot_get_option( 'font' ) == 'ubuntu' ) || ( ot_get_option( 'font' ) == 'ubuntu-cyr' ) ) { $styles .= 'body { font-family: "Ubuntu", Arial, sans-serif; }'."\n"; }	
			if ( ( ot_get_option( 'font' ) == 'roboto-condensed' ) || ( ot_get_option( 'font' ) == 'roboto-condensed-cyr' ) ) { $styles .= 'body { font-family: "Roboto Condensed", Arial, sans-serif; }'."\n"; }			
			if ( ( ot_get_option( 'font' ) == 'open-sans' ) || ( ot_get_option( 'font' ) == 'open-sans-cyr' ) )	{ $styles .= 'body { font-family: "Open Sans", Arial, sans-serif; }'."\n"; }
			if ( ( ot_get_option( 'font' ) == 'pt-serif' ) || ( ot_get_option( 'font' ) == 'pt-serif-cyr' ) ) { $styles .= 'body { font-family: "PT Serif", serif; }'."\n"; }
			if ( ot_get_option( 'font' ) == 'arial' ) { $styles .= 'body { font-family: Arial, sans-serif; }'."\n"; }
			if ( ot_get_option( 'font' ) == 'georgia' ) { $styles .= 'body { font-family: Georgia, serif; }'."\n"; }
			
			// container width
			if ( ot_get_option('container-width') != '1380' ) {			
				if ( ot_get_option( 'boxed' ) ) { 
					$styles .= '.boxed #wrapper, .container-inner { max-width: '.ot_get_option('container-width').'px; }'."\n";
				}
				else {
					$styles .= '.container-inner { max-width: '.ot_get_option('container-width').'px; }'."\n";
				}
			}
			// sidebar padding
			if ( ot_get_option('sidebar-padding') != '30' ) {
				$styles .= '.sidebar .widget { padding-left: '.ot_get_option('sidebar-padding').'px; padding-right: '.ot_get_option('sidebar-padding').'px; padding-top: '.ot_get_option('sidebar-padding').'px; }'."\n";
			}
			// primary color
			if ( ot_get_option('color-1') != '#3b8dbd' ) {
				$styles .= '
::selection { background-color: '.ot_get_option('color-1').'; }
::-moz-selection { background-color: '.ot_get_option('color-1').'; }

a,
.themeform label .required,
#flexslider-featured .flex-direction-nav .flex-next:hover,
#flexslider-featured .flex-direction-nav .flex-prev:hover,
.post-hover:hover .post-title a,
.post-title a:hover,
.s1 .post-nav li a:hover i,
.content .post-nav li a:hover i,
.post-related a:hover,
.s1 .widget_rss ul li a,
#footer .widget_rss ul li a,
.s1 .widget_calendar a,
#footer .widget_calendar a,
.s1 .alx-tab .tab-item-category a,
.s1 .alx-posts .post-item-category a,
.s1 .alx-tab li:hover .tab-item-title a,
.s1 .alx-tab li:hover .tab-item-comment a,
.s1 .alx-posts li:hover .post-item-title a,
#footer .alx-tab .tab-item-category a,
#footer .alx-posts .post-item-category a,
#footer .alx-tab li:hover .tab-item-title a,
#footer .alx-tab li:hover .tab-item-comment a,
#footer .alx-posts li:hover .post-item-title a,
.comment-tabs li.active a,
.comment-awaiting-moderation,
.child-menu a:hover,
.child-menu .current_page_item > a,
.wp-pagenavi a { color: '.ot_get_option('color-1').'; }

.themeform input[type="submit"],
.themeform button[type="submit"],
.s1 .sidebar-top,
.s1 .sidebar-toggle,
#flexslider-featured .flex-control-nav li a.flex-active,
.post-tags a:hover,
.s1 .widget_calendar caption,
#footer .widget_calendar caption,
.author-bio .bio-avatar:after,
.commentlist li.bypostauthor > .comment-body:after,
.commentlist li.comment-author-admin > .comment-body:after { background-color: '.ot_get_option('color-1').'; }

.post-format .format-container { border-color: '.ot_get_option('color-1').'; }

.s1 .alx-tabs-nav li.active a,
#footer .alx-tabs-nav li.active a,
.comment-tabs li.active a,
.wp-pagenavi a:hover,
.wp-pagenavi a:active,
.wp-pagenavi span.current { border-bottom-color: '.ot_get_option('color-1').'!important; }				
				'."\n";
			}		
			// secondary color
			if ( ot_get_option('color-2') != '#82b965' ) {
				$styles .= '
.s2 .post-nav li a:hover i,
.s2 .widget_rss ul li a,
.s2 .widget_calendar a,
.s2 .alx-tab .tab-item-category a,
.s2 .alx-posts .post-item-category a,
.s2 .alx-tab li:hover .tab-item-title a,
.s2 .alx-tab li:hover .tab-item-comment a,
.s2 .alx-posts li:hover .post-item-title a { color: '.ot_get_option('color-2').'; }

.s2 .sidebar-top,
.s2 .sidebar-toggle,
.post-comments,
.jp-play-bar,
.jp-volume-bar-value,
.s2 .widget_calendar caption { background-color: '.ot_get_option('color-2').'; }

.s2 .alx-tabs-nav li.active a { border-bottom-color: '.ot_get_option('color-2').'; }
.post-comments span:before { border-right-color: '.ot_get_option('color-2').'; }				
				'."\n";
			}			
			// topbar color
			if ( ot_get_option('color-topbar') != '#26272b' ) {
				$styles .= '
.search-expand,
#nav-topbar.nav-container { background-color: '.ot_get_option('color-topbar').'; }
@media only screen and (min-width: 720px) {
	#nav-topbar .nav ul { background-color: '.ot_get_option('color-topbar').'; }
}			
				'."\n";
			}			
			// header color
			if ( ot_get_option('color-header') != '#33363b' ) {
				$styles .= '
#header { background-color: '.ot_get_option('color-header').'; }
@media only screen and (min-width: 720px) {
	#nav-header .nav ul { background-color: '.ot_get_option('color-header').'; }
}			
				'."\n";
			}
			// header menu color
			if ( ot_get_option('color-header-menu') != '' ) {
				$styles .= '
#nav-header.nav-container { background-color: '.ot_get_option('color-header-menu').'; }
@media only screen and (min-width: 720px) {
	#nav-header .nav ul { background-color: '.ot_get_option('color-header-menu').'; }
}			
				'."\n";
			}				
			// footer color
			if ( ot_get_option('color-footer') != '#33363b' ) {
				$styles .= '#footer-bottom { background-color: '.ot_get_option('color-footer').'; }'."\n";
			}			
			// header logo max-height
			if ( ot_get_option('logo-max-height') != '60' ) {
				$styles .= '.site-title a img { max-height: '.ot_get_option('logo-max-height').'px; }'."\n";
			}
			// image border radius
			if ( ot_get_option('image-border-radius') != '0' ) {
				$styles .= 'img { -webkit-border-radius: '.ot_get_option('image-border-radius').'px; border-radius: '.ot_get_option('image-border-radius').'px; }'."\n";
			}
			// body background
			if ( ot_get_option('body-background') != '#eaeaea' ) {
				$styles .= 'body { background-color: '.ot_get_option('body-background').'; }'."\n";
			}
			
			$styles .= '</style>'."\n";
			// end output
			
			echo $styles;		
		}
	}
	
}
add_action( 'wp_head', 'alx_dynamic_css', 100 );
