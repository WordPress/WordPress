<?php
/**
 * Functions which enhance the theme by hooking into WordPress.
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function twentynineteen_body_classes( $classes ) {

	if ( is_singular() ) {
		// Adds `singular` to singular pages.
		$classes[] = 'singular';
	} else {
		// Adds `hfeed` to non-singular pages.
		$classes[] = 'hfeed';
	}

	// Adds a class if image filters are enabled.
	if ( twentynineteen_image_filters_enabled() ) {
		$classes[] = 'image-filters-enabled';
	}

	return $classes;
}
add_filter( 'body_class', 'twentynineteen_body_classes' );

/**
 * Adds custom class to the array of posts classes.
 *
 * @param array $classes A list of existing post class values.
 * @return array The filtered post class list.
 */
function twentynineteen_post_classes( $classes ) {
	$classes[] = 'entry';

	return $classes;
}
add_filter( 'post_class', 'twentynineteen_post_classes' );

/**
 * Adds a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function twentynineteen_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'twentynineteen_pingback_header' );

/**
 * Changes comment form default fields.
 */
function twentynineteen_comment_form_defaults( $defaults ) {
	$comment_field = $defaults['comment_field'];

	// Adjust height of comment form.
	$defaults['comment_field'] = preg_replace( '/rows="\d+"/', 'rows="5"', $comment_field );

	return $defaults;
}
add_filter( 'comment_form_defaults', 'twentynineteen_comment_form_defaults' );

/**
 * Filters the default archive titles.
 */
function twentynineteen_get_the_archive_title() {
	if ( is_category() ) {
		$title = __( 'Category Archives: ', 'twentynineteen' ) . '<span class="page-description">' . single_term_title( '', false ) . '</span>';
	} elseif ( is_tag() ) {
		$title = __( 'Tag Archives: ', 'twentynineteen' ) . '<span class="page-description">' . single_term_title( '', false ) . '</span>';
	} elseif ( is_author() ) {
		$title = __( 'Author Archives: ', 'twentynineteen' ) . '<span class="page-description">' . get_the_author_meta( 'display_name' ) . '</span>';
	} elseif ( is_year() ) {
		$title = __( 'Yearly Archives: ', 'twentynineteen' ) . '<span class="page-description">' . get_the_date( _x( 'Y', 'yearly archives date format', 'twentynineteen' ) ) . '</span>';
	} elseif ( is_month() ) {
		$title = __( 'Monthly Archives: ', 'twentynineteen' ) . '<span class="page-description">' . get_the_date( _x( 'F Y', 'monthly archives date format', 'twentynineteen' ) ) . '</span>';
	} elseif ( is_day() ) {
		$title = __( 'Daily Archives: ', 'twentynineteen' ) . '<span class="page-description">' . get_the_date() . '</span>';
	} elseif ( is_post_type_archive() ) {
		$title = __( 'Post Type Archives: ', 'twentynineteen' ) . '<span class="page-description">' . post_type_archive_title( '', false ) . '</span>';
	} elseif ( is_tax() ) {
		$tax = get_taxonomy( get_queried_object()->taxonomy );
		/* translators: %s: Taxonomy singular name. */
		$title = sprintf( esc_html__( '%s Archives:', 'twentynineteen' ), $tax->labels->singular_name );
	} else {
		$title = __( 'Archives:', 'twentynineteen' );
	}
	return $title;
}
add_filter( 'get_the_archive_title', 'twentynineteen_get_the_archive_title' );

/**
 * Adds custom 'sizes' attribute to responsive image functionality for post thumbnails.
 *
 * @since Twenty Nineteen 1.0
 *
 * @param string[] $attr Array of attribute values for the image markup, keyed by attribute name.
 *                       See wp_get_attachment_image().
 * @return string[] The filtered attributes for the image markup.
 */
function twentynineteen_post_thumbnail_sizes_attr( $attr ) {

	if ( is_admin() ) {
		return $attr;
	}

	if ( ! is_singular() ) {
		$attr['sizes'] = '(max-width: 34.9rem) calc(100vw - 2rem), (max-width: 53rem) calc(8 * (100vw / 12)), (min-width: 53rem) calc(6 * (100vw / 12)), 100vw';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'twentynineteen_post_thumbnail_sizes_attr' );

/**
 * Adds an extra menu to our nav for our priority+ navigation to use.
 *
 * @param string $nav_menu Nav menu.
 * @param object $args     Nav menu args.
 * @return string More link for hidden menu items.
 */
function twentynineteen_add_ellipses_to_nav( $nav_menu, $args ) {

	if ( 'menu-1' === $args->theme_location ) :

		$nav_menu .= '
			<div class="main-menu-more">
				<ul class="main-menu">
					<li class="menu-item menu-item-has-children">
						<button class="submenu-expand main-menu-more-toggle is-empty" tabindex="-1"
							aria-label="' . esc_attr__( 'More', 'twentynineteen' ) . '" aria-haspopup="true" aria-expanded="false">' .
							twentynineteen_get_icon_svg( 'arrow_drop_down_ellipsis' ) . '
						</button>
						<ul class="sub-menu hidden-links">
							<li class="mobile-parent-nav-menu-item">
								<button class="menu-item-link-return">' .
									twentynineteen_get_icon_svg( 'chevron_left' ) .
									esc_html__( 'Back', 'twentynineteen' ) . '
								</button>
							</li>
						</ul>
					</li>
				</ul>
			</div>';

	endif;

	return $nav_menu;
}
add_filter( 'wp_nav_menu', 'twentynineteen_add_ellipses_to_nav', 10, 2 );

/**
 * Handles WCAG 2.0 attributes for dropdown menus.
 *
 * Adjustments to menu attributes to support WCAG 2.0 recommendations
 * for flyout and dropdown menus.
 *
 * @link https://www.w3.org/WAI/tutorials/menus/flyout/
 *
 * @param array   $atts {
 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
 *
 *     @type string $title        Title attribute.
 *     @type string $target       Target attribute.
 *     @type string $rel          The rel attribute.
 *     @type string $href         The href attribute.
 *     @type string $aria-current The aria-current attribute.
 * }
 * @param WP_Post $item The current menu item object.
 * @return string[] Modified attributes.
 */
function twentynineteen_nav_menu_link_attributes( $atts, $item ) {

	// Add [aria-haspopup] and [aria-expanded] to menu items that have children.
	$item_has_children = in_array( 'menu-item-has-children', $item->classes, true );
	if ( $item_has_children ) {
		$atts['aria-haspopup'] = 'true';
		$atts['aria-expanded'] = 'false';
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'twentynineteen_nav_menu_link_attributes', 10, 2 );

/**
 * Creates a nav menu item to be displayed on mobile to navigate from submenu back to the parent.
 *
 * This duplicates each parent nav menu item and makes it the first child of itself.
 *
 * @param array  $sorted_menu_items Sorted nav menu items.
 * @param object $args              Nav menu args.
 * @return array Amended nav menu items.
 */
function twentynineteen_add_mobile_parent_nav_menu_items( $sorted_menu_items, $args ) {
	static $pseudo_id = 0;
	if ( ! isset( $args->theme_location ) || 'menu-1' !== $args->theme_location ) {
		return $sorted_menu_items;
	}

	$amended_menu_items = array();
	foreach ( $sorted_menu_items as $nav_menu_item ) {
		$amended_menu_items[] = $nav_menu_item;
		if ( in_array( 'menu-item-has-children', $nav_menu_item->classes, true ) ) {
			$parent_menu_item                   = clone $nav_menu_item;
			$parent_menu_item->original_id      = $nav_menu_item->ID;
			$parent_menu_item->ID               = --$pseudo_id;
			$parent_menu_item->db_id            = $parent_menu_item->ID;
			$parent_menu_item->object_id        = $parent_menu_item->ID;
			$parent_menu_item->classes          = array( 'mobile-parent-nav-menu-item' );
			$parent_menu_item->menu_item_parent = $nav_menu_item->ID;

			$amended_menu_items[] = $parent_menu_item;
		}
	}

	return $amended_menu_items;
}
add_filter( 'wp_nav_menu_objects', 'twentynineteen_add_mobile_parent_nav_menu_items', 10, 2 );

/**
 * Adds a fragment identifier (to the content) to paginated links.
 *
 * @since Twenty Nineteen 2.6
 *
 * @param string $link The page number HTML output.
 * @param int    $i    Page number for paginated posts' page links.
 * @return string Formatted output in HTML.
 */
function twentynineteen_link_pages_link( $link, $i ) {
	if ( $i > 1 && preg_match( '/href="([^"]*)"/', $link, $matches ) ) {
		$link = str_replace( $matches[1], $matches[1] . '#content', $link );
	}
	return $link;
}
add_filter( 'wp_link_pages_link', 'twentynineteen_link_pages_link', 10, 2 );
