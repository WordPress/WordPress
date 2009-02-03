<?php
/**
 * WordPress Post Template Functions.
 *
 * Gets content for the current post in the loop.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Display the ID of the current item in the WordPress Loop.
 *
 * @since 0.71
 * @uses $id
 */
function the_ID() {
	global $id;
	echo $id;
}

/**
 * Retrieve the ID of the current item in the WordPress Loop.
 *
 * @since 2.1.0
 * @uses $id
 *
 * @return unknown
 */
function get_the_ID() {
	global $id;
	return $id;
}

/**
 * Display or retrieve the current post title with optional content.
 *
 * @since 0.71
 *
 * @param string $before Optional. Content to prepend to the title.
 * @param string $after Optional. Content to append to the title.
 * @param bool $echo Optional, default to true.Whether to display or return.
 * @return null|string Null on no title. String if $echo parameter is false.
 */
function the_title($before = '', $after = '', $echo = true) {
	$title = get_the_title();

	if ( strlen($title) == 0 )
		return;

	$title = $before . $title . $after;

	if ( $echo )
		echo $title;
	else
		return $title;
}

/**
 * Sanitize the current title when retrieving or displaying.
 *
 * Works like {@link the_title()}, except the parameters can be in a string or
 * an array. See the function for what can be override in the $args parameter.
 *
 * The title before it is displayed will have the tags stripped and {@link
 * attribute_escape()} before it is passed to the user or displayed. The default
 * as with {@link the_title()}, is to display the title.
 *
 * @since 2.3.0
 *
 * @param string|array $args Optional. Override the defaults.
 * @return string|null Null on failure or display. String when echo is false.
 */
function the_title_attribute( $args = '' ) {
	$title = get_the_title();

	if ( strlen($title) == 0 )
		return;

	$defaults = array('before' => '', 'after' =>  '', 'echo' => true);
	$r = wp_parse_args($args, $defaults);
	extract( $r, EXTR_SKIP );


	$title = $before . $title . $after;
	$title = attribute_escape(strip_tags($title));

	if ( $echo )
		echo $title;
	else
		return $title;
}

/**
 * Retrieve post title.
 *
 * If the post is protected and the visitor is not an admin, then "Protected"
 * will be displayed before the post title. If the post is private, then
 * "Private" will be located before the post title.
 *
 * @since 0.71
 *
 * @param int $id Optional. Post ID.
 * @return string
 */
function get_the_title( $id = 0 ) {
	$post = &get_post($id);

	$title = $post->post_title;

	if ( !is_admin() ) {
		if ( !empty($post->post_password) )
			$title = sprintf(__('Protected: %s'), $title);
		else if ( isset($post->post_status) && 'private' == $post->post_status )
			$title = sprintf(__('Private: %s'), $title);
	}
	return apply_filters( 'the_title', $title );
}

/**
 * Display the Post Global Unique Identifier (guid).
 *
 * The guid will appear to be a link, but should not be used as an link to the
 * post. The reason you should not use it as a link, is because of moving the
 * blog across domains.
 *
 * @since 1.5.0
 *
 * @param int $id Optional. Post ID.
 */
function the_guid( $id = 0 ) {
	echo get_the_guid($id);
}

/**
 * Retrieve the Post Global Unique Identifier (guid).
 *
 * The guid will appear to be a link, but should not be used as an link to the
 * post. The reason you should not use it as a link, is because of moving the
 * blog across domains.
 *
 * @since 1.5.0
 *
 * @param int $id Optional. Post ID.
 * @return string
 */
function get_the_guid( $id = 0 ) {
	$post = &get_post($id);

	return apply_filters('get_the_guid', $post->guid);
}

/**
 * Display the post content.
 *
 * @since 0.71
 *
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param string $stripteaser Optional. Teaser content before the more text.
 * @param string $more_file Optional. Not used.
 */
function the_content($more_link_text = null, $stripteaser = 0, $more_file = '') {
	$content = get_the_content($more_link_text, $stripteaser, $more_file);
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	echo $content;
}

/**
 * Retrieve the post content.
 *
 * @since 0.71
 *
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param string $stripteaser Optional. Teaser content before the more text.
 * @param string $more_file Optional. Not used.
 * @return string
 */
function get_the_content($more_link_text = null, $stripteaser = 0, $more_file = '') {
	global $id, $post, $more, $page, $pages, $multipage, $preview, $pagenow;

	if ( null === $more_link_text )
		$more_link_text = __( '(more...)' );

	$output = '';

	// If post password required and it doesn't match the cookie.
	if ( post_password_required($post) ) {
		$output = get_the_password_form();
		return $output;
	}

	if ( $more_file != '' )
		$file = $more_file;
	else
		$file = $pagenow; //$_SERVER['PHP_SELF'];

	if ( $page > count($pages) ) // if the requested page doesn't exist
		$page = count($pages); // give them the highest numbered page that DOES exist

	$content = $pages[$page-1];
	if ( preg_match('/<!--more(.*?)?-->/', $content, $matches) ) {
		$content = explode($matches[0], $content, 2);
		if ( !empty($matches[1]) && !empty($more_link_text) )
			$more_link_text = strip_tags(wp_kses_no_null(trim($matches[1])));
	} else {
		$content = array($content);
	}
	if ( (false !== strpos($post->post_content, '<!--noteaser-->') && ((!$multipage) || ($page==1))) )
		$stripteaser = 1;
	$teaser = $content[0];
	if ( ($more) && ($stripteaser) )
		$teaser = '';
	$output .= $teaser;
	if ( count($content) > 1 ) {
		if ( $more ) {
			$output .= '<span id="more-'.$id.'"></span>'.$content[1];
		} else {
			$output = balanceTags($output);
			if ( ! empty($more_link_text) )
				$output .= ' <a href="'. get_permalink() . "#more-$id\" class=\"more-link\">$more_link_text</a>";
		}

	}
	if ( $preview ) // preview fix for javascript bug with foreign languages
		$output =	preg_replace_callback('/\%u([0-9A-F]{4})/', create_function('$match', 'return "&#" . base_convert($match[1], 16, 10) . ";";'), $output);

	return $output;
}

/**
 * Display the post excerpt.
 *
 * @since 0.71
 * @uses apply_filters() Calls 'the_excerpt' hook on post excerpt.
 */
function the_excerpt() {
	echo apply_filters('the_excerpt', get_the_excerpt());
}

/**
 * Retrieve the post excerpt.
 *
 * @since 0.71
 *
 * @param mixed $deprecated Not used.
 * @return string
 */
function get_the_excerpt($deprecated = '') {
	global $post;
	$output = '';
	$output = $post->post_excerpt;
	if ( post_password_required($post) ) {
		$output = __('There is no excerpt because this is a protected post.');
		return $output;
	}

	return apply_filters('get_the_excerpt', $output);
}

/**
 * Whether post has excerpt.
 *
 * @since 2.3.0
 *
 * @param int $id Optional. Post ID.
 * @return bool
 */
function has_excerpt( $id = 0 ) {
	$post = &get_post( $id );
	return ( !empty( $post->post_excerpt ) );
}

/**
 * Display the classes for the post div.
 *
 * @since 2.7.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param int $post_id An optional post ID.
 */
function post_class( $class = '', $post_id = null ) {
	// Separates classes with a single space, collates classes for post DIV
	echo 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
}

/**
 * Retrieve the classes for the post div as an array.
 *
 * The class names are add are many. If the post is a sticky, then the 'sticky'
 * class name. The class 'hentry' is always added to each post. For each
 * category, the class will be added with 'category-' with category slug is
 * added. The tags are the same way as the categories with 'tag-' before the tag
 * slug. All classes are passed through the filter, 'post_class' with the list
 * of classes, followed by $class parameter value, with the post ID as the last
 * parameter.
 *
 * @since 2.7.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param int $post_id An optional post ID.
 * @return array Array of classes.
 */
function get_post_class( $class = '', $post_id = null ) {
	$post = get_post($post_id);

	$classes = array();

	$classes[] = $post->post_type;

	// sticky for Sticky Posts
	if ( is_sticky($post->ID) && is_home())
		$classes[] = 'sticky';

	// hentry for hAtom compliace
	$classes[] = 'hentry';

	// Categories
	foreach ( (array) get_the_category($post->ID) as $cat ) {
		if ( empty($cat->slug ) )
			continue;
		$classes[] = 'category-' . $cat->slug;
	}

	// Tags
	foreach ( (array) get_the_tags($post->ID) as $tag ) {
		if ( empty($tag->slug ) )
			continue;
		$classes[] = 'tag-' . $tag->slug;
	}

	if ( !empty($class) ) {
		if ( !is_array( $class ) )
			$class = preg_split('#\s+#', $class);
		$classes = array_merge($classes, $class);
	}

	return apply_filters('post_class', $classes, $class, $post_id);
}

/**
 * Whether post requires password and correct password has been provided.
 *
 * @since 2.7.0
 *
 * @param int|object $post An optional post.  Global $post used if not provided.
 * @return bool false if a password is not required or the correct password cookie is present, true otherwise.
 */
function post_password_required( $post = null ) {
	$post = get_post($post);

	if ( empty($post->post_password) )
		return false;

	if ( !isset($_COOKIE['wp-postpass_' . COOKIEHASH]) )
		return true;

	if ( $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password )
		return true;

	return false;
}

/**
 * Display "sticky" CSS class, if a post is sticky.
 *
 * @since 2.7.0
 *
 * @param int $post_id An optional post ID.
 */
function sticky_class( $post_id = null ) {
	if ( !is_sticky($post_id) )
		return;

	echo " sticky";
}

/**
 * Page Template Functions for usage in Themes
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * The formatted output of a list of pages.
 *
 * Displays page links for paginated posts (i.e. includes the <!--nextpage-->.
 * Quicktag one or more times). This tag must be within The Loop.
 *
 * The defaults for overwriting are:
 * 'next_or_number' - Default is 'number' (string). Indicates whether page
 *      numbers should be used. Valid values are number and next.
 * 'nextpagelink' - Default is 'Next Page' (string). Text for link to next page.
 *      of the bookmark.
 * 'previouspagelink' - Default is 'Previous Page' (string). Text for link to
 *      previous page, if available.
 * 'pagelink' - Default is '%' (String).Format string for page numbers. The % in
 *      the parameter string will be replaced with the page number, so Page %
 *      generates "Page 1", "Page 2", etc. Defaults to %, just the page number.
 * 'before' - Default is '<p> Pages:' (string). The html or text to prepend to
 *      each bookmarks.
 * 'after' - Default is '</p>' (string). The html or text to append to each
 *      bookmarks.
 * 'more_file' - Default is '' (string) Page the links should point to. Defaults
 *      to the current page.
 * 'link_before' - Default is '' (string). The html or text to prepend to each
 *      Pages link inside the <a> tag.
 * 'link_after' - Default is '' (string). The html or text to append to each
 *      Pages link inside the <a> tag.
 *
 * @since 1.2.0
 * @access private
 *
 * @param string|array $args Optional. Overwrite the defaults.
 * @return string Formatted output in HTML.
 */
function wp_link_pages($args = '') {
	$defaults = array(
		'before' => '<p>' . __('Pages:'), 'after' => '</p>',
		'link_before' => '', 'link_after' => '',
		'next_or_number' => 'number', 'nextpagelink' => __('Next page'),
		'previouspagelink' => __('Previous page'), 'pagelink' => '%',
		'more_file' => '', 'echo' => 1
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	global $post, $page, $numpages, $multipage, $more, $pagenow;
	if ( $more_file != '' )
		$file = $more_file;
	else
		$file = $pagenow;

	$output = '';
	if ( $multipage ) {
		if ( 'number' == $next_or_number ) {
			$output .= $before;
			for ( $i = 1; $i < ($numpages+1); $i = $i + 1 ) {
				$j = str_replace('%',"$i",$pagelink);
				$output .= ' ';
				if ( ($i != $page) || ((!$more) && ($page==1)) ) {
					if ( 1 == $i ) {
						$output .= '<a href="' . get_permalink() . '">';
					} else {
						if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
							$output .= '<a href="' . get_permalink() . '&amp;page=' . $i . '">';
						else
							$output .= '<a href="' . trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged') . '">';
					}

				}
				$output .= $link_before;
				$output .= $j;
				$output .= $link_after;
				if ( ($i != $page) || ((!$more) && ($page==1)) )
					$output .= '</a>';
			}
			$output .= $after;
		} else {
			if ( $more ) {
				$output .= $before;
				$i = $page - 1;
				if ( $i && $more ) {
					if ( 1 == $i ) {
						$output .= '<a href="' . get_permalink() . '">' . $link_before. $previouspagelink . $link_after . '</a>';
					} else {
						if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
							$output .= '<a href="' . get_permalink() . '&amp;page=' . $i . '">' . $link_before. $previouspagelink . $link_after . '</a>';
						else
							$output .= '<a href="' . trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged') . '">' . $link_before. $previouspagelink . $link_after . '</a>';
					}
				}
				$i = $page + 1;
				if ( $i <= $numpages && $more ) {
					if ( 1 == $i ) {
						$output .= '<a href="' . get_permalink() . '">' . $link_before. $nextpagelink . $link_after . '</a>';
					} else {
						if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
							$output .= '<a href="' . get_permalink() . '&amp;page=' . $i . '">' . $link_before. $nextpagelink . $link_after . '</a>';
						else
							$output .= '<a href="' . trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged') . '">' . $link_before. $nextpagelink . $link_after . '</a>';
					}
				}
				$output .= $after;
			}
		}
	}

	if ( $echo )
		echo $output;

	return $output;
}


//
// Post-meta: Custom per-post fields.
//

/**
 * Retrieve post custom meta data field.
 *
 * @since 1.5.0
 *
 * @param string $key Meta data key name.
 * @return string|array Array of values or single value, if only one element exists.
 */
function post_custom( $key = '' ) {
	$custom = get_post_custom();

	if ( 1 == count($custom[$key]) )
		return $custom[$key][0];
	else
		return $custom[$key];
}

/**
 * Display list of post custom fields.
 *
 * @internal This will probably change at some point...
 * @since 1.2.0
 * @uses apply_filters() Calls 'the_meta_key' on list item HTML content, with key and value as separate parameters.
 */
function the_meta() {
	if ( $keys = get_post_custom_keys() ) {
		echo "<ul class='post-meta'>\n";
		foreach ( (array) $keys as $key ) {
			$keyt = trim($key);
			if ( '_' == $keyt{0} )
				continue;
			$values = array_map('trim', get_post_custom_values($key));
			$value = implode($values,', ');
			echo apply_filters('the_meta_key', "<li><span class='post-meta-key'>$key:</span> $value</li>\n", $key, $value);
		}
		echo "</ul>\n";
	}
}

//
// Pages
//

/**
 * Retrieve or display list of pages as a dropdown (select list).
 *
 * @since 2.1.0
 *
 * @param array|string $args Optional. Override default arguments.
 * @return string HTML content, if not displaying.
 */
function wp_dropdown_pages($args = '') {
	$defaults = array(
		'depth' => 0, 'child_of' => 0,
		'selected' => 0, 'echo' => 1,
		'name' => 'page_id', 'show_option_none' => '', 'show_option_no_change' => '',
		'option_none_value' => ''
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$pages = get_pages($r);
	$output = '';

	if ( ! empty($pages) ) {
		$output = "<select name=\"$name\" id=\"$name\">\n";
		if ( $show_option_no_change )
			$output .= "\t<option value=\"-1\">$show_option_no_change</option>";
		if ( $show_option_none )
			$output .= "\t<option value=\"$option_none_value\">$show_option_none</option>\n";
		$output .= walk_page_dropdown_tree($pages, $depth, $r);
		$output .= "</select>\n";
	}

	$output = apply_filters('wp_dropdown_pages', $output);

	if ( $echo )
		echo $output;

	return $output;
}

/**
 * Retrieve or display list of pages in list (li) format.
 *
 * @since 1.5.0
 *
 * @param array|string $args Optional. Override default arguments.
 * @return string HTML content, if not displaying.
 */
function wp_list_pages($args = '') {
	$defaults = array(
		'depth' => 0, 'show_date' => '',
		'date_format' => get_option('date_format'),
		'child_of' => 0, 'exclude' => '',
		'title_li' => __('Pages'), 'echo' => 1,
		'authors' => '', 'sort_column' => 'menu_order, post_title',
		'link_before' => '', 'link_after' => ''
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$output = '';
	$current_page = 0;

	// sanitize, mostly to keep spaces out
	$r['exclude'] = preg_replace('/[^0-9,]/', '', $r['exclude']);

	// Allow plugins to filter an array of excluded pages
	$r['exclude'] = implode(',', apply_filters('wp_list_pages_excludes', explode(',', $r['exclude'])));

	// Query pages.
	$r['hierarchical'] = 0;
	$pages = get_pages($r);

	if ( !empty($pages) ) {
		if ( $r['title_li'] )
			$output .= '<li class="pagenav">' . $r['title_li'] . '<ul>';

		global $wp_query;
		if ( is_page() || $wp_query->is_posts_page )
			$current_page = $wp_query->get_queried_object_id();
		$output .= walk_page_tree($pages, $r['depth'], $current_page, $r);

		if ( $r['title_li'] )
			$output .= '</ul></li>';
	}

	$output = apply_filters('wp_list_pages', $output);

	if ( $r['echo'] )
		echo $output;
	else
		return $output;
}

/**
 * Display or retrieve list of pages with optional home link.
 *
 * The arguments are listed below and part of the arguments are for {@link
 * wp_list_pages()} function. Check that function for more info on those
 * arguments.
 *
 * <ul>
 * <li><strong>sort_column</strong> - How to sort the list of pages. Defaults
 * to page title. Use column for posts table.</li>
 * <li><strong>menu_class</strong> - Class to use for the div ID which contains
 * the page list. Defaults to 'menu'.</li>
 * <li><strong>echo</strong> - Whether to echo list or return it. Defaults to
 * echo.</li>
 * <li><strong>link_before</strong> - Text before show_home argument text.</li>
 * <li><strong>link_after</strong> - Text after show_home argument text.</li>
 * <li><strong>show_home</strong> - If you set this argument, then it will
 * display the link to the home page. The show_home argument really just needs
 * to be set to the value of the text of the link.</li>
 * </ul>
 *
 * @since 2.7.0
 *
 * @param array|string $args
 */
function wp_page_menu( $args = array() ) {
	$defaults = array('sort_column' => 'post_title', 'menu_class' => 'menu', 'echo' => true, 'link_before' => '', 'link_after' => '');
	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_page_menu_args', $args );

	$menu = '';

	$list_args = $args;

	// Show Home in the menu
	if ( isset($args['show_home']) && ! empty($args['show_home']) ) {
		if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
			$text = __('Home');
		else
			$text = $args['show_home'];
		$class = '';
		if ( is_front_page() && !is_paged() )
			$class = 'class="current_page_item"';
		$menu .= '<li ' . $class . '><a href="' . get_option('home') . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';
		// If the front page is a page, add it to the exclude list
		if (get_option('show_on_front') == 'page') {
			if ( !empty( $list_args['exclude'] ) ) {
				$list_args['exclude'] .= ',';
			} else {
				$list_args['exclude'] = '';
			}
			$list_args['exclude'] .= get_option('page_on_front');
		}
	}

	$list_args['echo'] = false;
	$list_args['title_li'] = '';
	$menu .= str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages($list_args) );

	if ( $menu )
		$menu = '<ul>' . $menu . '</ul>';

	$menu = '<div class="' . $args['menu_class'] . '">' . $menu . "</div>\n";
	$menu = apply_filters( 'wp_page_menu', $menu, $args );
	if ( $args['echo'] )
		echo $menu;
	else
		return $menu;
}

//
// Page helpers
//

/**
 * Retrieve HTML list content for page list.
 *
 * @uses Walker_Page to create HTML list content.
 * @since 2.1.0
 * @see Walker_Page::walk() for parameters and return description.
 */
function walk_page_tree($pages, $depth, $current_page, $r) {
	$walker = new Walker_Page;
	$args = array($pages, $depth, $r, $current_page);
	return call_user_func_array(array(&$walker, 'walk'), $args);
}

/**
 * Retrieve HTML dropdown (select) content for page list.
 *
 * @uses Walker_PageDropdown to create HTML dropdown content.
 * @since 2.1.0
 * @see Walker_PageDropdown::walk() for parameters and return description.
 */
function walk_page_dropdown_tree() {
	$walker = new Walker_PageDropdown;
	$args = func_get_args();
	return call_user_func_array(array(&$walker, 'walk'), $args);
}

//
// Attachments
//

/**
 * Display an attachment page link using an image or icon.
 *
 * @since 2.0.0
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default is false. Whether to use full size.
 * @param bool $deprecated Deprecated. Not used.
 * @param bool $permalink Optional, default is false. Whether to include permalink.
 */
function the_attachment_link($id = 0, $fullsize = false, $deprecated = false, $permalink = false) {
	if ( $fullsize )
		echo wp_get_attachment_link($id, 'full', $permalink);
	else
		echo wp_get_attachment_link($id, 'thumbnail', $permalink);
}

/**
 * Retrieve an attachment page link using an image or icon, if possible.
 *
 * @since 2.5.0
 * @uses apply_filters() Calls 'wp_get_attachment_link' filter on HTML content with same parameters as function.
 *
 * @param int $id Optional. Post ID.
 * @param string $size Optional. Image size.
 * @param bool $permalink Optional, default is false. Whether to add permalink to image.
 * @param bool $icon Optional, default is false. Whether to include icon.
 * @return string HTML content.
 */
function wp_get_attachment_link($id = 0, $size = 'thumbnail', $permalink = false, $icon = false) {
	$id = intval($id);
	$_post = & get_post( $id );

	if ( ('attachment' != $_post->post_type) || !$url = wp_get_attachment_url($_post->ID) )
		return __('Missing Attachment');

	if ( $permalink )
		$url = get_attachment_link($_post->ID);

	$post_title = attribute_escape($_post->post_title);

	$link_text = wp_get_attachment_image($id, $size, $icon);
	if ( !$link_text )
		$link_text = $_post->post_title;

	return apply_filters( 'wp_get_attachment_link', "<a href='$url' title='$post_title'>$link_text</a>", $id, $size, $permalink, $icon );
}

/**
 * Retrieve HTML content of attachment image with link.
 *
 * @since 2.0.0
 * @deprecated Use {@link wp_get_attachment_link()}
 * @see wp_get_attachment_link() Use instead.
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default is false. Whether to use full size image.
 * @param array $max_dims Optional. Max image dimensions.
 * @param bool $permalink Optional, default is false. Whether to include permalink to image.
 * @return string
 */
function get_the_attachment_link($id = 0, $fullsize = false, $max_dims = false, $permalink = false) {
	$id = (int) $id;
	$_post = & get_post($id);

	if ( ('attachment' != $_post->post_type) || !$url = wp_get_attachment_url($_post->ID) )
		return __('Missing Attachment');

	if ( $permalink )
		$url = get_attachment_link($_post->ID);

	$post_title = attribute_escape($_post->post_title);

	$innerHTML = get_attachment_innerHTML($_post->ID, $fullsize, $max_dims);
	return "<a href='$url' title='$post_title'>$innerHTML</a>";
}

/**
 * Retrieve icon URL and Path.
 *
 * @since 2.1.0
 * @deprecated Use {@link wp_get_attachment_image_src()}
 * @see wp_get_attachment_image_src() Use instead.
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default to false. Whether to have full image.
 * @return array Icon URL and full path to file, respectively.
 */
function get_attachment_icon_src( $id = 0, $fullsize = false ) {
	$id = (int) $id;
	if ( !$post = & get_post($id) )
		return false;

	$file = get_attached_file( $post->ID );

	if ( !$fullsize && $src = wp_get_attachment_thumb_url( $post->ID ) ) {
		// We have a thumbnail desired, specified and existing

		$src_file = basename($src);
		$class = 'attachmentthumb';
	} elseif ( wp_attachment_is_image( $post->ID ) ) {
		// We have an image without a thumbnail

		$src = wp_get_attachment_url( $post->ID );
		$src_file = & $file;
		$class = 'attachmentimage';
	} elseif ( $src = wp_mime_type_icon( $post->ID ) ) {
		// No thumb, no image. We'll look for a mime-related icon instead.

		$icon_dir = apply_filters( 'icon_dir', get_template_directory() . '/images' );
		$src_file = $icon_dir . '/' . basename($src);
	}

	if ( !isset($src) || !$src )
		return false;

	return array($src, $src_file);
}

/**
 * Retrieve HTML content of icon attachment image element.
 *
 * @since 2.0.0
 * @deprecated Use {@link wp_get_attachment_image()}
 * @see wp_get_attachment_image() Use instead of.
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default to false. Whether to have full size image.
 * @param array $max_dims Optional. Dimensions of image.
 * @return string HTML content.
 */
function get_attachment_icon( $id = 0, $fullsize = false, $max_dims = false ) {
	$id = (int) $id;
	if ( !$post = & get_post($id) )
		return false;

	if ( !$src = get_attachment_icon_src( $post->ID, $fullsize ) )
		return false;

	list($src, $src_file) = $src;

	// Do we need to constrain the image?
	if ( ($max_dims = apply_filters('attachment_max_dims', $max_dims)) && file_exists($src_file) ) {

		$imagesize = getimagesize($src_file);

		if (($imagesize[0] > $max_dims[0]) || $imagesize[1] > $max_dims[1] ) {
			$actual_aspect = $imagesize[0] / $imagesize[1];
			$desired_aspect = $max_dims[0] / $max_dims[1];

			if ( $actual_aspect >= $desired_aspect ) {
				$height = $actual_aspect * $max_dims[0];
				$constraint = "width='{$max_dims[0]}' ";
				$post->iconsize = array($max_dims[0], $height);
			} else {
				$width = $max_dims[1] / $actual_aspect;
				$constraint = "height='{$max_dims[1]}' ";
				$post->iconsize = array($width, $max_dims[1]);
			}
		} else {
			$post->iconsize = array($imagesize[0], $imagesize[1]);
			$constraint = '';
		}
	} else {
		$constraint = '';
	}

	$post_title = attribute_escape($post->post_title);

	$icon = "<img src='$src' title='$post_title' alt='$post_title' $constraint/>";

	return apply_filters( 'attachment_icon', $icon, $post->ID );
}

/**
 * Retrieve HTML content of image element.
 *
 * @since 2.0.0
 * @deprecated Use {@link wp_get_attachment_image()}
 * @see wp_get_attachment_image() Use instead.
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default to false. Whether to have full size image.
 * @param array $max_dims Optional. Dimensions of image.
 * @return string
 */
function get_attachment_innerHTML($id = 0, $fullsize = false, $max_dims = false) {
	$id = (int) $id;
	if ( !$post = & get_post($id) )
		return false;

	if ( $innerHTML = get_attachment_icon($post->ID, $fullsize, $max_dims))
		return $innerHTML;


	$innerHTML = attribute_escape($post->post_title);

	return apply_filters('attachment_innerHTML', $innerHTML, $post->ID);
}

/**
 * Wrap attachment in <<p>> element before content.
 *
 * @since 2.0.0
 * @uses apply_filters() Calls 'prepend_attachment' hook on HTML content.
 *
 * @param string $content
 * @return string
 */
function prepend_attachment($content) {
	global $post;

	if ( empty($post->post_type) || $post->post_type != 'attachment' )
		return $content;

	$p = '<p class="attachment">';
	// show the medium sized image representation of the attachment if available, and link to the raw file
	$p .= wp_get_attachment_link(0, 'medium', false);
	$p .= '</p>';
	$p = apply_filters('prepend_attachment', $p);

	return "$p\n$content";
}

//
// Misc
//

/**
 * Retrieve protected post password form content.
 *
 * @since 1.0.0
 * @uses apply_filters() Calls 'the_password_form' filter on output.
 *
 * @return string HTML content for password form for password protected post.
 */
function get_the_password_form() {
	global $post;
	$label = 'pwbox-'.(empty($post->ID) ? rand() : $post->ID);
	$output = '<form action="' . get_option('siteurl') . '/wp-pass.php" method="post">
	<p>' . __("This post is password protected. To view it please enter your password below:") . '</p>
	<p><label for="' . $label . '">' . __("Password:") . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . __("Submit") . '" /></p>
	</form>
	';
	return apply_filters('the_password_form', $output);
}

/**
 * Whether currently in a page template.
 *
 * This template tag allows you to determine whether or not you are in a page
 * template. You can optional provide a template name and then the check will be
 * specific to that template.
 *
 * @since 2.5.0
 * @uses $wp_query
 *
 * @param string $template The specific template name if specific matching is required.
 * @return bool False on failure, true if success.
 */
function is_page_template($template = '') {
	if (!is_page()) {
		return false;
	}

	global $wp_query;

	$page = $wp_query->get_queried_object();
	$custom_fields = get_post_custom_values('_wp_page_template',$page->ID);
	$page_template = $custom_fields[0];

	// We have no argument passed so just see if a page_template has been specified
	if ( empty( $template ) ) {
		if (!empty( $page_template ) ) {
			return true;
		}
	} elseif ( $template == $page_template) {
		return true;
	}

	return false;
}

/**
 * Retrieve formatted date timestamp of a revision (linked to that revisions's page).
 *
 * @package WordPress
 * @subpackage Post_Revisions
 * @since 2.6.0
 *
 * @uses date_i18n()
 *
 * @param int|object $revision Revision ID or revision object.
 * @param bool $link Optional, default is true. Link to revisions's page?
 * @return string i18n formatted datetimestamp or localized 'Current Revision'.
 */
function wp_post_revision_title( $revision, $link = true ) {
	if ( !$revision = get_post( $revision ) )
		return $revision;

	if ( !in_array( $revision->post_type, array( 'post', 'page', 'revision' ) ) )
		return false;

	$datef = _c( 'j F, Y @ G:i|revision date format');
	$autosavef = __( '%s [Autosave]' );
	$currentf  = __( '%s [Current Revision]' );

	$date = date_i18n( $datef, strtotime( $revision->post_modified ) );
	if ( $link && current_user_can( 'edit_post', $revision->ID ) && $link = get_edit_post_link( $revision->ID ) )
		$date = "<a href='$link'>$date</a>";

	if ( !wp_is_post_revision( $revision ) )
		$date = sprintf( $currentf, $date );
	elseif ( wp_is_post_autosave( $revision ) )
		$date = sprintf( $autosavef, $date );

	return $date;
}

/**
 * Display list of a post's revisions.
 *
 * Can output either a UL with edit links or a TABLE with diff interface, and
 * restore action links.
 *
 * Second argument controls parameters:
 *   (bool)   parent : include the parent (the "Current Revision") in the list.
 *   (string) format : 'list' or 'form-table'.  'list' outputs UL, 'form-table'
 *                     outputs TABLE with UI.
 *   (int)    right  : what revision is currently being viewed - used in
 *                     form-table format.
 *   (int)    left   : what revision is currently being diffed against right -
 *                     used in form-table format.
 *
 * @package WordPress
 * @subpackage Post_Revisions
 * @since 2.6.0
 *
 * @uses wp_get_post_revisions()
 * @uses wp_post_revision_title()
 * @uses get_edit_post_link()
 * @uses get_author_name()
 *
 * @todo split into two functions (list, form-table) ?
 *
 * @param int|object $post_id Post ID or post object.
 * @param string|array $args See description {@link wp_parse_args()}.
 * @return null
 */
function wp_list_post_revisions( $post_id = 0, $args = null ) {
	if ( !$post = get_post( $post_id ) )
		return;

	$defaults = array( 'parent' => false, 'right' => false, 'left' => false, 'format' => 'list', 'type' => 'all' );
	extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );

	switch ( $type ) {
	case 'autosave' :
		if ( !$autosave = wp_get_post_autosave( $post->ID ) )
			return;
		$revisions = array( $autosave );
		break;
	case 'revision' : // just revisions - remove autosave later
	case 'all' :
	default :
		if ( !$revisions = wp_get_post_revisions( $post->ID ) )
			return;
		break;
	}

	$titlef = _c( '%1$s by %2$s|post revision 1:datetime, 2:name' );

	if ( $parent )
		array_unshift( $revisions, $post );

	$rows = '';
	$class = false;
	$can_edit_post = current_user_can( 'edit_post', $post->ID );
	foreach ( $revisions as $revision ) {
		if ( !current_user_can( 'read_post', $revision->ID ) )
			continue;
		if ( 'revision' === $type && wp_is_post_autosave( $revision ) )
			continue;

		$date = wp_post_revision_title( $revision );
		$name = get_author_name( $revision->post_author );

		if ( 'form-table' == $format ) {
			if ( $left )
				$left_checked = $left == $revision->ID ? ' checked="checked"' : '';
			else
				$left_checked = $right_checked ? ' checked="checked"' : ''; // [sic] (the next one)
			$right_checked = $right == $revision->ID ? ' checked="checked"' : '';

			$class = $class ? '' : " class='alternate'";

			if ( $post->ID != $revision->ID && $can_edit_post )
				$actions = '<a href="' . wp_nonce_url( add_query_arg( array( 'revision' => $revision->ID, 'diff' => false, 'action' => 'restore' ) ), "restore-post_$post->ID|$revision->ID" ) . '">' . __( 'Restore' ) . '</a>';
			else
				$actions = '';

			$rows .= "<tr$class>\n";
			$rows .= "\t<th style='white-space: nowrap' scope='row'><input type='radio' name='left' value='$revision->ID'$left_checked /><input type='radio' name='right' value='$revision->ID'$right_checked /></th>\n";
			$rows .= "\t<td>$date</td>\n";
			$rows .= "\t<td>$name</td>\n";
			$rows .= "\t<td class='action-links'>$actions</td>\n";
			$rows .= "</tr>\n";
		} else {
			$title = sprintf( $titlef, $date, $name );
			$rows .= "\t<li>$title</li>\n";
		}
	}

	if ( 'form-table' == $format ) : ?>

<form action="revision.php" method="get">

<div class="tablenav">
	<div class="alignleft">
		<input type="submit" class="button-secondary" value="<?php _e( 'Compare Revisions' ); ?>" />
		<input type="hidden" name="action" value="diff" />
	</div>
</div>

<br class="clear" />

<table class="widefat post-revisions" cellspacing="0">
	<col />
	<col style="width: 33%" />
	<col style="width: 33%" />
	<col style="width: 33%" />
<thead>
<tr>
	<th scope="col"></th>
	<th scope="col"><?php _e( 'Date Created' ); ?></th>
	<th scope="col"><?php _e( 'Author' ); ?></th>
	<th scope="col" class="action-links"><?php _e( 'Actions' ); ?></th>
</tr>
</thead>
<tbody>

<?php echo $rows; ?>

</tbody>
</table>

</form>

<?php
	else :
		echo "<ul class='post-revisions'>\n";
		echo $rows;
		echo "</ul>";
	endif;

}
