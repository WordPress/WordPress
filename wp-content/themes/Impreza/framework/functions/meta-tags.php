<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

add_action( 'wp_head', 'us_output_meta_tags', 5 );
function us_output_meta_tags() {
	// Some of the tags might be defined previously
	global $us_meta_tags;
	$us_meta_tags = apply_filters( 'us_meta_tags', isset( $us_meta_tags ) ? $us_meta_tags : array() );

	// Some must-have general tags
	if ( ! isset( $us_meta_tags['viewport'] ) ) {
		$us_meta_tags['viewport'] = 'width=device-width';
		if ( us_get_option( 'responsive_layout' ) ) {
			$us_meta_tags['viewport'] .= ', initial-scale=1';
		}
		$us_meta_tags['viewport'] = apply_filters( 'us_meta_viewport', $us_meta_tags['viewport'] );
	}
	if ( ! isset( $us_meta_tags['SKYPE_TOOLBAR'] ) ) {
		$us_meta_tags['SKYPE_TOOLBAR'] = 'SKYPE_TOOLBAR_PARSER_COMPATIBLE';
	}

	// Open Graph meta tags when needed
	if ( us_get_option( 'og_enabled' ) AND is_singular() AND isset( $GLOBALS['post'] ) ) {
		if ( ! isset( $us_meta_tags['og:title'] ) ) {
			$us_meta_tags['og:title'] = get_the_title();
		}
		if ( ! isset( $us_meta_tags['og:type'] ) ) {
			$us_meta_tags['og:type'] = 'website';
		}
		if ( ! isset( $us_meta_tags['og:url'] ) ) {
			$us_meta_tags['og:url'] = site_url( $_SERVER['REQUEST_URI'] );
		}
		if ( ! isset( $us_meta_tags['og:image'] ) AND ( $the_post_thumbnail_id = get_post_thumbnail_id() ) ) {
			$the_post_thumbnail_src = wp_get_attachment_image_src( $the_post_thumbnail_id, 'medium' );
			if ( $the_post_thumbnail_src ) {
				$us_meta_tags['og:image'] = $the_post_thumbnail_src[0];
			}
		}
		if ( ! isset( $us_meta_tags['og:description'] ) AND ( $the_excerpt = get_the_excerpt() ) ) {
			$us_meta_tags['og:description'] = $the_excerpt;
		}
	}

	// Outputting the tags
	if ( isset( $us_meta_tags ) AND is_array( $us_meta_tags ) ) {
		foreach ( $us_meta_tags as $meta_name => $meta_content ) {
			echo '<meta name="' . esc_attr( $meta_name ) . '" content="' . esc_attr( $meta_content ) . '">' . "\n";
		}
	}
}
