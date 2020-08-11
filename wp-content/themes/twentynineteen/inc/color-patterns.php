<?php
/**
 * Twenty Nineteen: Color Patterns
 *
 * @package WordPress
 * @subpackage TwentyNineteen
 * @since Twenty Nineteen 1.0
 */

/**
 * Generate the CSS for the current primary color.
 */
function twentynineteen_custom_colors_css() {

	$primary_color = 199;
	if ( 'default' !== get_theme_mod( 'primary_color', 'default' ) ) {
		$primary_color = absint( get_theme_mod( 'primary_color_hue', 199 ) );
	}

	/**
	 * Filters Twenty Nineteen default saturation level.
	 *
	 * @since Twenty Nineteen 1.0
	 *
	 * @param int $saturation Color saturation level.
	 */
	$saturation = apply_filters( 'twentynineteen_custom_colors_saturation', 100 );
	$saturation = absint( $saturation ) . '%';

	/**
	 * Filters Twenty Nineteen default selection saturation level.
	 *
	 * @since Twenty Nineteen 1.0
	 *
	 * @param int $saturation_selection Selection color saturation level.
	 */
	$saturation_selection = absint( apply_filters( 'twentynineteen_custom_colors_saturation_selection', 50 ) );
	$saturation_selection = $saturation_selection . '%';

	/**
	 * Filters Twenty Nineteen default lightness level.
	 *
	 * @since Twenty Nineteen 1.0
	 *
	 * @param int $lightness Color lightness level.
	 */
	$lightness = apply_filters( 'twentynineteen_custom_colors_lightness', 33 );
	$lightness = absint( $lightness ) . '%';

	/**
	 * Filters Twenty Nineteen default hover lightness level.
	 *
	 * @since Twenty Nineteen 1.0
	 *
	 * @param int $lightness_hover Hover color lightness level.
	 */
	$lightness_hover = apply_filters( 'twentynineteen_custom_colors_lightness_hover', 23 );
	$lightness_hover = absint( $lightness_hover ) . '%';

	/**
	 * Filters Twenty Nineteen default selection lightness level.
	 *
	 * @since Twenty Nineteen 1.0
	 *
	 * @param int $lightness_selection Selection color lightness level.
	 */
	$lightness_selection = apply_filters( 'twentynineteen_custom_colors_lightness_selection', 90 );
	$lightness_selection = absint( $lightness_selection ) . '%';

	$theme_css = '
		/*
		 * Set background for:
		 * - featured image :before
		 * - featured image :before
		 * - post thumbmail :before
		 * - post thumbmail :before
		 * - Submenu
		 * - Sticky Post
		 * - buttons
		 * - WP Block Button
		 * - Blocks
		 */
		.image-filters-enabled .site-header.featured-image .site-featured-image:before,
		.image-filters-enabled .site-header.featured-image .site-featured-image:after,
		.image-filters-enabled .entry .post-thumbnail:before,
		.image-filters-enabled .entry .post-thumbnail:after,
		.main-navigation .sub-menu,
		.sticky-post,
		.entry .entry-content .wp-block-button .wp-block-button__link:not(.has-background),
		.entry .button, button, input[type="button"], input[type="reset"], input[type="submit"],
		.entry .entry-content > .has-primary-background-color,
		.entry .entry-content > *[class^="wp-block-"].has-primary-background-color,
		.entry .entry-content > *[class^="wp-block-"] .has-primary-background-color,
		.entry .entry-content > *[class^="wp-block-"].is-style-solid-color,
		.entry .entry-content > *[class^="wp-block-"].is-style-solid-color.has-primary-background-color,
		.entry .entry-content .wp-block-file .wp-block-file__button {
			background-color: hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness . ' ); /* base: #0073a8; */
		}

		/*
		 * Set Color for:
		 * - all links
		 * - main navigation links
		 * - Post navigation links
		 * - Post entry meta hover
		 * - Post entry header more-link hover
		 * - main navigation svg
		 * - comment navigation
		 * - Comment edit link hover
		 * - Site Footer Link hover
		 * - Widget links
		 */
		a,
		a:visited,
		.main-navigation .main-menu > li,
		.main-navigation ul.main-menu > li > a,
		.post-navigation .post-title,
		.entry .entry-meta a:hover,
		.entry .entry-footer a:hover,
		.entry .entry-content .more-link:hover,
		.main-navigation .main-menu > li > a + svg,
		.comment .comment-metadata > a:hover,
		.comment .comment-metadata .comment-edit-link:hover,
		#colophon .site-info a:hover,
		.widget a,
		.entry .entry-content .wp-block-button.is-style-outline .wp-block-button__link:not(.has-text-color),
		.entry .entry-content > .has-primary-color,
		.entry .entry-content > *[class^="wp-block-"] .has-primary-color,
		.entry .entry-content > *[class^="wp-block-"].is-style-solid-color blockquote.has-primary-color,
		.entry .entry-content > *[class^="wp-block-"].is-style-solid-color blockquote.has-primary-color p {
			color: hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness . ' ); /* base: #0073a8; */
		}

		/*
		 * Set border color for:
		 * wp block quote
		 * :focus
		 */
		blockquote,
		.entry .entry-content blockquote,
		.entry .entry-content .wp-block-quote:not(.is-large),
		.entry .entry-content .wp-block-quote:not(.is-style-large),
		input[type="text"]:focus,
		input[type="email"]:focus,
		input[type="url"]:focus,
		input[type="password"]:focus,
		input[type="search"]:focus,
		input[type="number"]:focus,
		input[type="tel"]:focus,
		input[type="range"]:focus,
		input[type="date"]:focus,
		input[type="month"]:focus,
		input[type="week"]:focus,
		input[type="time"]:focus,
		input[type="datetime"]:focus,
		input[type="datetime-local"]:focus,
		input[type="color"]:focus,
		textarea:focus {
			border-color: hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness . ' ); /* base: #0073a8; */
		}

		.gallery-item > div > a:focus {
			box-shadow: 0 0 0 2px hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness . ' ); /* base: #0073a8; */
		}

		/* Hover colors */
		a:hover, a:active,
		.main-navigation .main-menu > li > a:hover,
		.main-navigation .main-menu > li > a:hover + svg,
		.post-navigation .nav-links a:hover,
		.post-navigation .nav-links a:hover .post-title,
		.author-bio .author-description .author-link:hover,
		.entry .entry-content > .has-secondary-color,
		.entry .entry-content > *[class^="wp-block-"] .has-secondary-color,
		.entry .entry-content > *[class^="wp-block-"].is-style-solid-color blockquote.has-secondary-color,
		.entry .entry-content > *[class^="wp-block-"].is-style-solid-color blockquote.has-secondary-color p,
		.comment .comment-author .fn a:hover,
		.comment-reply-link:hover,
		.comment-navigation .nav-previous a:hover,
		.comment-navigation .nav-next a:hover,
		#cancel-comment-reply-link:hover,
		.widget a:hover {
			color: hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness_hover . ' ); /* base: #005177; */
		}

		.main-navigation .sub-menu > li > a:hover,
		.main-navigation .sub-menu > li > a:focus,
		.main-navigation .sub-menu > li > a:hover:after,
		.main-navigation .sub-menu > li > a:focus:after,
		.main-navigation .sub-menu > li > .menu-item-link-return:hover,
		.main-navigation .sub-menu > li > .menu-item-link-return:focus,
		.main-navigation .sub-menu > li > a:not(.submenu-expand):hover,
		.main-navigation .sub-menu > li > a:not(.submenu-expand):focus,
		.entry .entry-content > .has-secondary-background-color,
		.entry .entry-content > *[class^="wp-block-"].has-secondary-background-color,
		.entry .entry-content > *[class^="wp-block-"] .has-secondary-background-color,
		.entry .entry-content > *[class^="wp-block-"].is-style-solid-color.has-secondary-background-color {
			background-color: hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness_hover . ' ); /* base: #005177; */
		}

		/* Text selection colors */
		::selection {
			background-color: hsl( ' . $primary_color . ', ' . $saturation_selection . ', ' . $lightness_selection . ' ); /* base: #005177; */
		}
		::-moz-selection {
			background-color: hsl( ' . $primary_color . ', ' . $saturation_selection . ', ' . $lightness_selection . ' ); /* base: #005177; */
		}';

	$editor_css = '
		/*
		 * Set colors for:
		 * - links
		 * - blockquote
		 * - pullquote (solid color)
		 * - buttons
		 */
		.editor-block-list__layout .editor-block-list__block a,
		.editor-block-list__layout .editor-block-list__block .wp-block-button.is-style-outline .wp-block-button__link:not(.has-text-color),
		.editor-block-list__layout .editor-block-list__block .wp-block-button.is-style-outline:hover .wp-block-button__link:not(.has-text-color),
		.editor-block-list__layout .editor-block-list__block .wp-block-button.is-style-outline:focus .wp-block-button__link:not(.has-text-color),
		.editor-block-list__layout .editor-block-list__block .wp-block-button.is-style-outline:active .wp-block-button__link:not(.has-text-color),
		.editor-block-list__layout .editor-block-list__block .wp-block-file .wp-block-file__textlink {
			color: hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness . ' ); /* base: #0073a8; */
		}

		.editor-block-list__layout .editor-block-list__block .wp-block-quote:not(.is-large):not(.is-style-large),
		.editor-styles-wrapper .editor-block-list__layout .wp-block-freeform blockquote {
			border-color: hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness . ' ); /* base: #0073a8; */
		}

		.editor-block-list__layout .editor-block-list__block .wp-block-pullquote.is-style-solid-color:not(.has-background-color) {
			background-color: hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness . ' ); /* base: #0073a8; */
		}

		.editor-block-list__layout .editor-block-list__block .wp-block-file .wp-block-file__button,
		.editor-block-list__layout .editor-block-list__block .wp-block-button:not(.is-style-outline) .wp-block-button__link,
		.editor-block-list__layout .editor-block-list__block .wp-block-button:not(.is-style-outline) .wp-block-button__link:active,
		.editor-block-list__layout .editor-block-list__block .wp-block-button:not(.is-style-outline) .wp-block-button__link:focus,
		.editor-block-list__layout .editor-block-list__block .wp-block-button:not(.is-style-outline) .wp-block-button__link:hover {
			background-color: hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness . ' ); /* base: #0073a8; */
		}

		/* Hover colors */
		.editor-block-list__layout .editor-block-list__block a:hover,
		.editor-block-list__layout .editor-block-list__block a:active,
		.editor-block-list__layout .editor-block-list__block .wp-block-file .wp-block-file__textlink:hover {
			color: hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness_hover . ' ); /* base: #005177; */
		}

		/* Do not overwrite solid color pullquote or cover links */
		.editor-block-list__layout .editor-block-list__block .wp-block-pullquote.is-style-solid-color a,
		.editor-block-list__layout .editor-block-list__block .wp-block-cover a {
			color: inherit;
		}
		';

	if ( function_exists( 'register_block_type' ) && is_admin() ) {
		$theme_css = $editor_css;
	}

	/**
	 * Filters Twenty Nineteen custom colors CSS.
	 *
	 * @since Twenty Nineteen 1.0
	 *
	 * @param string $css           Base theme colors CSS.
	 * @param int    $primary_color The user's selected color hue.
	 * @param string $saturation    Filtered theme color saturation level.
	 */
	return apply_filters( 'twentynineteen_custom_colors_css', $theme_css, $primary_color, $saturation );
}
