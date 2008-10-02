<?php
/**
 * WordPress Theme Administration API
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function current_theme_info() {
	$themes = get_themes();
	$current_theme = get_current_theme();
	$ct->name = $current_theme;
	$ct->title = $themes[$current_theme]['Title'];
	$ct->version = $themes[$current_theme]['Version'];
	$ct->parent_theme = $themes[$current_theme]['Parent Theme'];
	$ct->template_dir = $themes[$current_theme]['Template Dir'];
	$ct->stylesheet_dir = $themes[$current_theme]['Stylesheet Dir'];
	$ct->template = $themes[$current_theme]['Template'];
	$ct->stylesheet = $themes[$current_theme]['Stylesheet'];
	$ct->screenshot = $themes[$current_theme]['Screenshot'];
	$ct->description = $themes[$current_theme]['Description'];
	$ct->author = $themes[$current_theme]['Author'];
	$ct->tags = $themes[$current_theme]['Tags'];
	return $ct;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function get_broken_themes() {
	global $wp_broken_themes;

	get_themes();
	return $wp_broken_themes;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function get_page_templates() {
	$themes = get_themes();
	$theme = get_current_theme();
	$templates = $themes[$theme]['Template Files'];
	$page_templates = array ();

	if ( is_array( $templates ) ) {
		foreach ( $templates as $template ) {
			$template_data = implode( '', file( WP_CONTENT_DIR.$template ));

			$name = '';
			if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ) )
				$name = $name[1];

			$description = '';
			if( preg_match( '|Description:(.*)$|mi', $template_data, $description ) )
				$description = $description[1];

			if ( !empty( $name ) ) {
				$page_templates[trim( $name )] = basename( $template );
			}
		}
	}

	return $page_templates;
}

?>
