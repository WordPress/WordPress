<?php
/**
 * Bookmark Template Functions for usage in Themes.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * The formatted output of a list of bookmarks.
 *
 * The $bookmarks array must contain bookmark objects and will be iterated over
 * to retrieve the bookmark to be used in the output.
 *
 * The output is formatted as HTML with no way to change that format. However,
 * what is between, before, and after can be changed. The link itself will be
 * HTML.
 *
 * This function is used internally by wp_list_bookmarks() and should not be
 * used by themes.
 *
 * @since 2.1.0
 * @access private
 *
 * @param array        $bookmarks List of bookmarks to traverse.
 * @param string|array $args {
 *     Optional. Bookmarks arguments.
 *
 *     @type int|bool $show_updated     Whether to show the time the bookmark was last updated.
 *                                      Accepts 1|true or 0|false. Default 0|false.
 *     @type int|bool $show_description Whether to show the bookmark description. Accepts 1|true,
 *                                      Accepts 1|true or 0|false. Default 0|false.
 *     @type int|bool $show_images      Whether to show the link image if available. Accepts 1|true
 *                                      or 0|false. Default 1|true.
 *     @type int|bool $show_name        Whether to show link name if available. Accepts 1|true or
 *                                      0|false. Default 0|false.
 *     @type string   $before           The HTML or text to prepend to each bookmark. Default `<li>`.
 *     @type string   $after            The HTML or text to append to each bookmark. Default `</li>`.
 *     @type string   $link_before      The HTML or text to prepend to each bookmark inside the anchor
 *                                      tags. Default empty.
 *     @type string   $link_after       The HTML or text to append to each bookmark inside the anchor
 *                                      tags. Default empty.
 *     @type string   $between          The string for use in between the link, description, and image.
 *                                      Default "\n".
 *     @type int|bool $show_rating      Whether to show the link rating. Accepts 1|true or 0|false.
 *                                      Default 0|false.
 *
 * }
 * @return string Formatted output in HTML
 */
function _walk_bookmarks( $bookmarks, $args = '' ) {
	$defaults = array(
		'show_updated'     => 0,
		'show_description' => 0,
		'show_images'      => 1,
		'show_name'        => 0,
		'before'           => '<li>',
		'after'            => '</li>',
		'between'          => "\n",
		'show_rating'      => 0,
		'link_before'      => '',
		'link_after'       => '',
	);

	$parsed_args = wp_parse_args( $args, $defaults );

	$output = ''; // Blank string to start with.

	foreach ( (array) $bookmarks as $bookmark ) {
		if ( ! isset( $bookmark->recently_updated ) ) {
			$bookmark->recently_updated = false;
		}
		$output .= $parsed_args['before'];
		if ( $parsed_args['show_updated'] && $bookmark->recently_updated ) {
			$output .= '<em>';
		}
		$the_link = '#';
		if ( ! empty( $bookmark->link_url ) ) {
			$the_link = esc_url( $bookmark->link_url );
		}
		$desc  = esc_attr( sanitize_bookmark_field( 'link_description', $bookmark->link_description, $bookmark->link_id, 'display' ) );
		$name  = esc_attr( sanitize_bookmark_field( 'link_name', $bookmark->link_name, $bookmark->link_id, 'display' ) );
		$title = $desc;

		if ( $parsed_args['show_updated'] ) {
			if ( ! str_starts_with( $bookmark->link_updated_f, '00' ) ) {
				$title .= ' (';
				$title .= sprintf(
					/* translators: %s: Date and time of last update. */
					__( 'Last updated: %s' ),
					gmdate(
						get_option( 'links_updated_date_format' ),
						$bookmark->link_updated_f + (int) ( (float) get_option( 'gmt_offset' ) * HOUR_IN_SECONDS )
					)
				);
				$title .= ')';
			}
		}
		$alt = ' alt="' . $name . ( $parsed_args['show_description'] ? ' ' . $title : '' ) . '"';

		if ( '' !== $title ) {
			$title = ' title="' . $title . '"';
		}
		$rel = $bookmark->link_rel;

		$target = $bookmark->link_target;
		if ( '' !== $target ) {
			if ( is_string( $rel ) && '' !== $rel ) {
				if ( ! str_contains( $rel, 'noopener' ) ) {
					$rel = trim( $rel ) . ' noopener';
				}
			} else {
				$rel = 'noopener';
			}

			$target = ' target="' . $target . '"';
		}

		if ( '' !== $rel ) {
			$rel = ' rel="' . esc_attr( $rel ) . '"';
		}

		$output .= '<a href="' . $the_link . '"' . $rel . $title . $target . '>';

		$output .= $parsed_args['link_before'];

		if ( '' !== $bookmark->link_image && $parsed_args['show_images'] ) {
			if ( str_starts_with( $bookmark->link_image, 'http' ) ) {
				$output .= '<img src="' . $bookmark->link_image . '"' . $alt . $title . ' />';
			} else { // If it's a relative path.
				$output .= '<img src="' . get_option( 'siteurl' ) . $bookmark->link_image . '"' . $alt . $title . ' />';
			}
			if ( $parsed_args['show_name'] ) {
				$output .= " $name";
			}
		} else {
			$output .= $name;
		}

		$output .= $parsed_args['link_after'];

		$output .= '</a>';

		if ( $parsed_args['show_updated'] && $bookmark->recently_updated ) {
			$output .= '</em>';
		}

		if ( $parsed_args['show_description'] && '' !== $desc ) {
			$output .= $parsed_args['between'] . $desc;
		}

		if ( $parsed_args['show_rating'] ) {
			$output .= $parsed_args['between'] . sanitize_bookmark_field(
				'link_rating',
				$bookmark->link_rating,
				$bookmark->link_id,
				'display'
			);
		}
		$output .= $parsed_args['after'] . "\n";
	} // End while.

	return $output;
}

/**
 * Retrieves or echoes all of the bookmarks.
 *
 * List of default arguments are as follows:
 *
 * These options define how the Category name will appear before the category
 * links are displayed, if 'categorize' is 1. If 'categorize' is 0, then it will
 * display for only the 'title_li' string and only if 'title_li' is not empty.
 *
 * @since 2.1.0
 *
 * @see _walk_bookmarks()
 *
 * @param string|array $args {
 *     Optional. String or array of arguments to list bookmarks.
 *
 *     @type string       $orderby          How to order the links by. Accepts post fields. Default 'name'.
 *     @type string       $order            Whether to order bookmarks in ascending or descending order.
 *                                          Accepts 'ASC' (ascending) or 'DESC' (descending). Default 'ASC'.
 *     @type int          $limit            Amount of bookmarks to display. Accepts 1+ or -1 for all.
 *                                          Default -1.
 *     @type string       $category         Comma-separated list of category IDs to include links from.
 *                                          Default empty.
 *     @type string       $category_name    Category to retrieve links for by name. Default empty.
 *     @type int|bool     $hide_invisible   Whether to show or hide links marked as 'invisible'. Accepts
 *                                          1|true or 0|false. Default 1|true.
 *     @type int|bool     $show_updated     Whether to display the time the bookmark was last updated.
 *                                          Accepts 1|true or 0|false. Default 0|false.
 *     @type int|bool     $echo             Whether to echo or return the formatted bookmarks. Accepts
 *                                          1|true (echo) or 0|false (return). Default 1|true.
 *     @type int|bool     $categorize       Whether to show links listed by category or in a single column.
 *                                          Accepts 1|true (by category) or 0|false (one column). Default 1|true.
 *     @type int|bool     $show_description Whether to show the bookmark descriptions. Accepts 1|true or 0|false.
 *                                          Default 0|false.
 *     @type string       $title_li         What to show before the links appear. Default 'Bookmarks'.
 *     @type string       $title_before     The HTML or text to prepend to the $title_li string. Default '<h2>'.
 *     @type string       $title_after      The HTML or text to append to the $title_li string. Default '</h2>'.
 *     @type string|array $class            The CSS class or an array of classes to use for the $title_li.
 *                                          Default 'linkcat'.
 *     @type string       $category_before  The HTML or text to prepend to $title_before if $categorize is true.
 *                                          String must contain '%id' and '%class' to inherit the category ID and
 *                                          the $class argument used for formatting in themes.
 *                                          Default '<li id="%id" class="%class">'.
 *     @type string       $category_after   The HTML or text to append to $title_after if $categorize is true.
 *                                          Default '</li>'.
 *     @type string       $category_orderby How to order the bookmark category based on term scheme if $categorize
 *                                          is true. Default 'name'.
 *     @type string       $category_order   Whether to order categories in ascending or descending order if
 *                                          $categorize is true. Accepts 'ASC' (ascending) or 'DESC' (descending).
 *                                          Default 'ASC'.
 * }
 * @return void|string Void if 'echo' argument is true, HTML list of bookmarks if 'echo' is false.
 */
function wp_list_bookmarks( $args = '' ) {
	$defaults = array(
		'orderby'          => 'name',
		'order'            => 'ASC',
		'limit'            => -1,
		'category'         => '',
		'exclude_category' => '',
		'category_name'    => '',
		'hide_invisible'   => 1,
		'show_updated'     => 0,
		'echo'             => 1,
		'categorize'       => 1,
		'title_li'         => __( 'Bookmarks' ),
		'title_before'     => '<h2>',
		'title_after'      => '</h2>',
		'category_orderby' => 'name',
		'category_order'   => 'ASC',
		'class'            => 'linkcat',
		'category_before'  => '<li id="%id" class="%class">',
		'category_after'   => '</li>',
	);

	$parsed_args = wp_parse_args( $args, $defaults );

	$output = '';

	if ( ! is_array( $parsed_args['class'] ) ) {
		$parsed_args['class'] = explode( ' ', $parsed_args['class'] );
	}
	$parsed_args['class'] = array_map( 'sanitize_html_class', $parsed_args['class'] );
	$parsed_args['class'] = trim( implode( ' ', $parsed_args['class'] ) );

	if ( $parsed_args['categorize'] ) {
		$cats = get_terms(
			array(
				'taxonomy'     => 'link_category',
				'name__like'   => $parsed_args['category_name'],
				'include'      => $parsed_args['category'],
				'exclude'      => $parsed_args['exclude_category'],
				'orderby'      => $parsed_args['category_orderby'],
				'order'        => $parsed_args['category_order'],
				'hierarchical' => 0,
			)
		);
		if ( empty( $cats ) ) {
			$parsed_args['categorize'] = false;
		}
	}

	if ( $parsed_args['categorize'] ) {
		// Split the bookmarks into ul's for each category.
		foreach ( (array) $cats as $cat ) {
			$params    = array_merge( $parsed_args, array( 'category' => $cat->term_id ) );
			$bookmarks = get_bookmarks( $params );
			if ( empty( $bookmarks ) ) {
				continue;
			}
			$output .= str_replace(
				array( '%id', '%class' ),
				array( "linkcat-$cat->term_id", $parsed_args['class'] ),
				$parsed_args['category_before']
			);
			/**
			 * Filters the category name.
			 *
			 * @since 2.2.0
			 *
			 * @param string $cat_name The category name.
			 */
			$catname = apply_filters( 'link_category', $cat->name );

			$output .= $parsed_args['title_before'];
			$output .= $catname;
			$output .= $parsed_args['title_after'];
			$output .= "\n\t<ul class='xoxo blogroll'>\n";
			$output .= _walk_bookmarks( $bookmarks, $parsed_args );
			$output .= "\n\t</ul>\n";
			$output .= $parsed_args['category_after'] . "\n";
		}
	} else {
		// Output one single list using title_li for the title.
		$bookmarks = get_bookmarks( $parsed_args );

		if ( ! empty( $bookmarks ) ) {
			if ( ! empty( $parsed_args['title_li'] ) ) {
				$output .= str_replace(
					array( '%id', '%class' ),
					array( 'linkcat-' . $parsed_args['category'], $parsed_args['class'] ),
					$parsed_args['category_before']
				);
				$output .= $parsed_args['title_before'];
				$output .= $parsed_args['title_li'];
				$output .= $parsed_args['title_after'];
				$output .= "\n\t<ul class='xoxo blogroll'>\n";
				$output .= _walk_bookmarks( $bookmarks, $parsed_args );
				$output .= "\n\t</ul>\n";
				$output .= $parsed_args['category_after'] . "\n";
			} else {
				$output .= _walk_bookmarks( $bookmarks, $parsed_args );
			}
		}
	}

	/**
	 * Filters the bookmarks list before it is echoed or returned.
	 *
	 * @since 2.5.0
	 *
	 * @param string $html The HTML list of bookmarks.
	 */
	$html = apply_filters( 'wp_list_bookmarks', $output );

	if ( $parsed_args['echo'] ) {
		echo $html;
	} else {
		return $html;
	}
}
