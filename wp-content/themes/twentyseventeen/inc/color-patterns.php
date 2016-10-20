<?php
/**
 * Twenty Seventeen: Color Patterns
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 */

/**
 * Generate the CSS for the current custom color scheme.
 */
function twentyseventeen_custom_colors_css() {
	$hue = get_theme_mod( 'colorscheme_hue', 250 );
	$saturation = apply_filters( 'twentyseventeen_custom_colors_saturation', 50 );
	$reduced_saturation = ( .8 * $saturation ) . '%';
	$saturation = $saturation . '%';
	$css = '
/**
 * Twenty Seventeen: Color Patterns
 */

.colors-custom button,
.colors-custom input[type="button"],
.colors-custom input[type="submit"],
.colors-custom .bypostauthor > .comment-body > .comment-meta > .comment-author:before,
.colors-custom .entry-footer .edit-link a.post-edit-link {
	background-color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 13% ); /* base: #222; */
}

.colors-custom input[type="text"]:focus,
.colors-custom input[type="email"]:focus,
.colors-custom input[type="url"]:focus,
.colors-custom input[type="password"]:focus,
.colors-custom input[type="search"]:focus,
.colors-custom input[type="number"]:focus,
.colors-custom input[type="tel"]:focus,
.colors-custom input[type="range"]:focus,
.colors-custom input[type="date"]:focus,
.colors-custom input[type="month"]:focus,
.colors-custom input[type="week"]:focus,
.colors-custom input[type="time"]:focus,
.colors-custom input[type="datetime"]:focus,
.colors-custom .colors-custom input[type="datetime-local"]:focus,
.colors-custom input[type="color"]:focus,
.colors-custom textarea:focus,
.colors-custom button.secondary,
.colors-custom input[type="reset"],
.colors-custom input[type="button"].secondary,
.colors-custom input[type="reset"].secondary,
.colors-custom input[type="submit"].secondary,
.colors-custom a,
.colors-custom a:visited,
.colors-custom .site-title,
.colors-custom .site-title a,
.colors-custom .navigation-top a,
.colors-custom .navigation-top a:visited,
.colors-custom .dropdown-toggle,
.colors-custom .menu-toggle,
.colors-custom .page .panel-content .entry-title,
.colors-custom .page-title,
.colors-custom.page:not(.twentyseventeen-front-page) .entry-title,
.colors-custom .page-links a .page-number,
.colors-custom .comment-metadata a.comment-edit-link,
.colors-custom .comment-reply-link .icon,
.colors-custom h2.widget-title,
.colors-custom mark {
	color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 13% ); /* base: #222; */
}

body.colors-custom,
.colors-custom button,
.colors-custom input,
.colors-custom select,
.colors-custom textarea,
.colors-custom h3,
.colors-custom h4,
.colors-custom h6,
.colors-custom label,
.colors-custom .entry-title a,
.colors-custom.twentyseventeen-front-page .panel-content .recent-posts article,
.colors-custom .entry-footer .cat-links a,
.colors-custom .entry-footer .tags-links a,
.colors-custom .format-quote blockquote,
.colors-custom .nav-title,
.colors-custom .comment-body {
	color: hsl( ' . esc_attr( $hue ) . ', ' . $reduced_saturation . ', 20% ); /* base: #333; */
}


.colors-custom input[type="text"]:focus,
.colors-custom input[type="email"]:focus,
.colors-custom input[type="url"]:focus,
.colors-custom input[type="password"]:focus,
.colors-custom input[type="search"]:focus,
.colors-custom input[type="number"]:focus,
.colors-custom input[type="tel"]:focus,
.colors-custom input[type="range"]:focus,
.colors-custom input[type="date"]:focus,
.colors-custom input[type="month"]:focus,
.colors-custom input[type="week"]:focus,
.colors-custom input[type="time"]:focus,
.colors-custom input[type="datetime"]:focus,
.colors-custom input[type="datetime-local"]:focus,
.colors-custom input[type="color"]:focus,
.colors-custom textarea:focus {
	border-color: hsl( ' . esc_attr( $hue ) . ', ' . $reduced_saturation . ', 20% ); /* base: #333; */
}

.colors-custom h2,
.colors-custom blockquote,
.colors-custom input[type="text"],
.colors-custom input[type="email"],
.colors-custom input[type="url"],
.colors-custom input[type="password"],
.colors-custom input[type="search"],
.colors-custom input[type="number"],
.colors-custom input[type="tel"],
.colors-custom input[type="range"],
.colors-custom input[type="date"],
.colors-custom input[type="month"],
.colors-custom input[type="week"],
.colors-custom input[type="time"],
.colors-custom input[type="datetime"],
.colors-custom input[type="datetime-local"],
.colors-custom input[type="color"],
.colors-custom textarea,
.colors-custom .entry-content blockquote.alignleft,
.colors-custom .entry-content blockquote.alignright,
.colors-custom .colors-custom .taxonomy-description,
.colors-custom .site-info a,
.colors-custom .wp-caption {
	color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 40% ); /* base: #666; */
}

.colors-custom abbr,
.colors-custom acronym {
	border-bottom-color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 40% ); /* base: #666; */
}

.colors-custom h5,
.colors-custom .entry-meta,
.colors-custom .entry-meta a,
.colors-custom .nav-subtitle,
.colors-custom .comment-metadata,
.colors-custom .comment-metadata a,
.colors-custom .no-comments,
.colors-custom .comment-awaiting-moderation,
.colors-custom .page-numbers.current,
.colors-custom .page-links .page-number,
.colors-custom .site-description,
.colors-custom .navigation-top .current-menu-item > a,
.colors-custom .navigation-top .current-menu-item > a:visited,
.colors-custom .navigation-top .current_page_item > a,
.colors-custom .navigation-top .current_page_item > a:visited {
	color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 46% ); /* base: #767676; */
}

.colors-custom button:hover,
.colors-custom button:focus,
.colors-custom input[type="button"]:hover,
.colors-custom input[type="button"]:focus,
.colors-custom input[type="submit"]:hover,
.colors-custom input[type="submit"]:focus,
.colors-custom .entry-content a:focus,
.colors-custom .entry-content a:hover,
.colors-custom .entry-summary a:focus,
.colors-custom .entry-summary a:hover,
.colors-custom .widget a:focus,
.colors-custom .widget a:hover,
.colors-custom .colors-custom .site-footer .widget-area a:focus,
.colors-custom .site-footer .widget-area a:hover,
.colors-custom .posts-navigation a:focus,
.colors-custom .posts-navigation a:hover,
.colors-custom .comment-navigation a:focus,
.colors-custom .comment-navigation a:hover,
.colors-custom .comment-metadata a:focus,
.colors-custom .comment-metadata a:hover,
.colors-custom .comment-metadata a.comment-edit-link:focus,
.colors-custom .comment-metadata a.comment-edit-link:hover,
.colors-custom .comment-reply-link:focus,
.colors-custom .comment-reply-link:hover,
.colors-custom .widget_authors a:focus strong,
.colors-custom .widget_authors a:hover strong,
.colors-custom .project-terms a:focus,
.colors-custom .project-terms a:hover,
.colors-custom .entry-title a:focus,
.colors-custom .entry-title a:hover,
.colors-custom .entry-meta a:focus,
.colors-custom .entry-meta a:hover,
.colors-custom .page-links a:focus .page-number,
.colors-custom .page-links a:hover .page-number,
.colors-custom .entry-footer a:focus,
.colors-custom .entry-footer a:hover,
.colors-custom .entry-footer .cat-links a:focus,
.colors-custom .entry-footer .cat-links a:hover,
.colors-custom .entry-footer .tags-links a:focus,
.colors-custom .entry-footer .tags-links a:hover,
.colors-custom .post-navigation a:focus,
.colors-custom .post-navigation a:hover,
.colors-custom .logged-in-as a:focus,
.colors-custom .logged-in-as a:hover,
.colors-custom .comment-navigation a:focus,
.colors-custom .comment-navigation a:hover,
.colors-custom a:focus .nav-title,
.colors-custom .colors-custom a:hover .nav-title,
.colors-custom .edit-link a:focus,
.colors-custom .edit-link a:hover,
.colors-custom .pagination a:focus,
.colors-custom .pagination a:hover,
.colors-custom .site-info a:focus,
.colors-custom .site-info a:hover,
.colors-custom .widget .widget-title a:focus,
.colors-custom .widget .widget-title a:hover,
.colors-custom .widget ul li a:focus,
.colors-custom .widget ul li a:hover,
.colors-custom .entry-footer .edit-link a.post-edit-link:hover,
.colors-custom .entry-footer .edit-link a.post-edit-link:focus,
.colors-custom .social-navigation a:hover,
.colors-custom .social-navigation a:focus {
	background: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 46% ); /* base: #767676; */
}

.colors-custom .entry-content a,
.colors-custom .entry-content a:visited,
.colors-custom .entry-summary a,
.colors-custom .entry-summary a:visited,
.colors-custom .widget a,
.colors-custom .widget a:visited,
.colors-custom .site-footer .widget-area a,
.colors-custom .site-footer .widget-area a:visited,
.colors-custom .posts-navigation a,
.colors-custom .posts-navigation a:visited,
.colors-custom .widget_authors a strong,
.colors-custom .widget_authors a:visited strong {
	border-bottom-color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 46% ); /* base: #767676; */
}

.colors-custom button.secondary:hover,
.colors-custom button.secondary:focus,
.colors-custom input[type="reset"]:hover,
.colors-custom input[type="reset"]:focus,
.colors-custom input[type="button"].secondary:hover,
.colors-custom input[type="button"].secondary:focus,
.colors-custom input[type="reset"].secondary:hover,
.colors-custom input[type="reset"].secondary:focus,
.colors-custom input[type="submit"].secondary:hover,
.colors-custom input[type="submit"].secondary:focus,
.colors-custom .social-navigation a,
.colors-custom hr {
	background: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 73% ); /* base: #bbb; */
}

.colors-custom input[type="text"],
.colors-custom input[type="email"],
.colors-custom input[type="url"],
.colors-custom input[type="password"],
.colors-custom input[type="search"],
.colors-custom input[type="number"],
.colors-custom input[type="tel"],
.colors-custom input[type="range"],
.colors-custom input[type="date"],
.colors-custom input[type="month"],
.colors-custom input[type="week"],
.colors-custom input[type="time"],
.colors-custom input[type="datetime"],
.colors-custom input[type="datetime-local"],
.colors-custom input[type="color"],
.colors-custom textarea,
.colors-custom select,
.colors-custom fieldset,
.colors-custom .widget .tagcloud a:hover,
.colors-custom .widget .tagcloud a:focus,
.colors-custom .widget.widget_tag_cloud a:hover,
.colors-custom .widget.widget_tag_cloud a:focus,
.colors-custom .wp_widget_tag_cloud a:hover,
.colors-custom .wp_widget_tag_cloud a:focus {
	border-color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 73% ); /* base: #bbb; */
}

.colors-custom .entry-footer .cat-links .icon,
.colors-custom .entry-footer .tags-links .icon {
	color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 73% ); /* base: #bbb; */
}

.colors-custom button.secondary,
.colors-custom input[type="reset"],
.colors-custom input[type="button"].secondary,
.colors-custom input[type="reset"].secondary,
.colors-custom input[type="submit"].secondary,
.colors-custom .prev.page-numbers,
.colors-custom .next.page-numbers {
	background-color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 87% ); /* base: #ddd; */
}

.colors-custom .widget .tagcloud a,
.colors-custom .widget.widget_tag_cloud a,
.colors-custom .wp_widget_tag_cloud a {
	border-color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 87% ); /* base: #ddd; */
}

.colors-custom.twentyseventeen-front-page article:not(.has-post-thumbnail):not(:first-child),
.colors-custom .widget ul li {
	border-top-color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 87% ); /* base: #ddd; */
}

.colors-custom .widget ul li {
	border-bottom-color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 87% ); /* base: #ddd; */
}

.colors-custom pre,
.colors-custom mark,
.colors-custom ins {
	background: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 93% ); /* base: #eee; */
}

.colors-custom .navigation-top,
.colors-custom .main-navigation > div > ul,
.colors-custom .pagination,
.colors-custom .comment-navigation,
.colors-custom .entry-footer,
.colors-custom .site-footer {
	border-top-color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 93% ); /* base: #eee; */
}

.colors-custom .navigation-top,
.colors-custom .main-navigation li,
.colors-custom .entry-footer,
.colors-custom #comments {
	border-bottom-color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 93% ); /* base: #eee; */
}

.colors-custom .site-header {
	background-color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 98% ); /* base: #fafafa; */
}

.colors-custom .entry-content a:focus,
.colors-custom .entry-content a:hover,
.colors-custom .entry-summary a:focus,
.colors-custom .entry-summary a:hover,
.colors-custom .widget a:focus,
.colors-custom .widget a:hover,
.colors-custom .site-footer .widget-area a:focus,
.colors-custom .site-footer .widget-area a:hover,
.colors-custom .posts-navigation a:focus,
.colors-custom .posts-navigation a:hover,
.colors-custom .comment-navigation a:focus,
.colors-custom .comment-navigation a:hover,
.colors-custom .comment-metadata a:focus,
.colors-custom .comment-metadata a:hover,
.colors-custom .comment-metadata a.comment-edit-link:focus,
.colors-custom .comment-metadata a.comment-edit-link:hover,
.colors-custom .comment-reply-link:focus,
.colors-custom .comment-reply-link:hover,
.colors-custom .widget_authors a:focus strong,
.colors-custom .widget_authors a:hover strong,
.colors-custom .project-terms a:focus,
.colors-custom .project-terms a:hover,
.colors-custom .colors-custom .entry-title a:focus,
.colors-custom .entry-title a:hover,
.colors-custom .entry-meta a:focus,
.colors-custom .entry-meta a:hover,
.colors-custom .page-links a:focus .page-number,
.colors-custom .page-links a:hover .page-number,
.colors-custom .entry-footer a:focus,
.colors-custom .entry-footer a:hover,
.colors-custom .entry-footer .cat-links a:focus,
.colors-custom .entry-footer .cat-links a:hover,
.colors-custom .entry-footer .tags-links a:focus,
.colors-custom .entry-footer .tags-links a:hover,
.colors-custom .post-navigation a:focus,
.colors-custom .post-navigation a:hover,
.colors-custom .logged-in-as a:focus,
.colors-custom .logged-in-as a:hover,
.colors-custom .comment-navigation a:focus,
.colors-custom .comment-navigation a:hover,
.colors-custom a:focus .nav-title,
.colors-custom a:hover .nav-title,
.colors-custom .edit-link a:focus,
.colors-custom .edit-link a:hover,
.colors-custom .pagination a:focus,
.colors-custom .pagination a:hover,
.colors-custom .site-info a:focus,
.colors-custom .site-info a:hover,
.colors-custom .widget .widget-title a:focus,
.colors-custom .widget .widget-title a:hover,
.colors-custom .widget ul li a:focus,
.colors-custom .widget ul li a:hover,
.colors-custom button,
.colors-custom input[type="button"],
.colors-custom input[type="submit"],
.colors-custom .entry-footer .edit-link a.post-edit-link,
.colors-custom .social-navigation a {
	color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 100% ); /* base: #fff; */
}

body.colors-custom,
.colors-custom .navigation-top,
.colors-custom .main-navigation ul {
	background: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 100% ); /* base: #fff; */
}

.colors-custom .bypostauthor > .comment-body > .comment-meta > .comment-author:before {
	border-color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 100% ); /* base: #fff; */
}

.colors-custom .menu-toggle,
.colors-custom .menu-toggle:hover,
.colors-custom .menu-toggle:focus,
.colors-custom .menu .dropdown-toggle
.colors-custom .menu-scroll-down,
.colors-custom .menu-scroll-down:hover,
.colors-custom .menu-scroll-down:focus {
	background-color: transparent;
}


@media screen and (min-width: 48em) {

	.colors-custom .nav-links .nav-previous .nav-title .icon,
	.colors-custom .nav-links .nav-next .nav-title .icon {
		color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 20% ); /* base: #222; */
	}

	.colors-custom .main-navigation li li:hover,
	.colors-custom .main-navigation li li.focus {
		background: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 46% ); /* base: #767676; */
	}

	.colors-custom .menu-scroll-down {
		color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 46% ); /* base: #767676; */;
	}

	.colors-custom .main-navigation ul ul {
		border-color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 93% ); /* base: #eee; */
		background: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 100% ); /* base: #fff; */
	}

	.colors-custom .main-navigation ul li.menu-item-has-children:before,
	.colors-custom .main-navigation ul li.page_item_has_children:before {
		border-bottom-color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 93% ); /* base: #eee; */
	}

	.colors-custom .main-navigation ul li.menu-item-has-children:after,
	.colors-custom .main-navigation ul li.page_item_has_children:after {
		border-bottom-color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 100% ); /* base: #fff; */
	}

	.colors-custom .main-navigation li li.focus > a,
	.colors-custom .main-navigation li li:focus > a,
	.colors-custom .main-navigation li li:hover > a,
	.colors-custom .main-navigation li li a:hover,
	.colors-custom .main-navigation li li a:focus,
	.colors-custom .main-navigation li li.current_page_item a:hover,
	.colors-custom .main-navigation li li.current-menu-item a:hover,
	.colors-custom .main-navigation li li.current_page_item a:focus,
	.colors-custom .main-navigation li li.current-menu-item a:focus {
		color: hsl( ' . esc_attr( $hue ) . ', ' . esc_attr( $saturation ) . ', 100% ); /* base: #fff; */
	}
}';

	return $css;
}
