<?php

//
// "The Loop" post functions
//

function the_ID() {
	global $id;
	echo $id;
}


function get_the_ID() {
	global $id;
	return $id;
}


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

function the_guid( $id = 0 ) {
	echo get_the_guid($id);
}

function get_the_guid( $id = 0 ) {
	$post = &get_post($id);

	return apply_filters('get_the_guid', $post->guid);
}

function the_content($more_link_text = '(more...)', $stripteaser = 0, $more_file = '') {
	$content = get_the_content($more_link_text, $stripteaser, $more_file);
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	echo $content;
}


function get_the_content($more_link_text = '(more...)', $stripteaser = 0, $more_file = '') {
	global $id, $post, $more, $page, $pages, $multipage, $preview, $pagenow;

	$output = '';

	if ( !empty($post->post_password) ) { // if there's a password
		if ( !isset($_COOKIE['wp-postpass_'.COOKIEHASH]) || stripslashes($_COOKIE['wp-postpass_'.COOKIEHASH]) != $post->post_password ) {	// and it doesn't match the cookie
			$output = get_the_password_form();
			return $output;
		}
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
		$output =	preg_replace('/\%u([0-9A-F]{4,4})/e',	"'&#'.base_convert('\\1',16,10).';'", $output);

	return $output;
}


function the_excerpt() {
	echo apply_filters('the_excerpt', get_the_excerpt());
}


function get_the_excerpt($deprecated = '') {
	global $post;
	$output = '';
	$output = $post->post_excerpt;
	if ( !empty($post->post_password) ) { // if there's a password
		if ( !isset($_COOKIE['wp-postpass_'.COOKIEHASH]) || $_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password ) {  // and it doesn't match the cookie
			$output = __('There is no excerpt because this is a protected post.');
			return $output;
		}
	}

	return apply_filters('get_the_excerpt', $output);
}

function has_excerpt( $id = 0 ) {
	$post = &get_post( $id );
	return ( !empty( $post->post_excerpt ) );
}

function wp_link_pages($args = '') {
	$defaults = array(
		'before' => '<p>' . __('Pages:'), 'after' => '</p>',
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
				$output .= $j;
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
						$output .= '<a href="' . get_permalink() . '">' . $previouspagelink . '</a>';
					} else {
						if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
							$output .= '<a href="' . get_permalink() . '&amp;page=' . $i . '">' . $previouspagelink . '</a>';
						else
							$output .= '<a href="' . trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged') . '">' . $previouspagelink . '</a>';
					}
				}
				$i = $page + 1;
				if ( $i <= $numpages && $more ) {
					if ( 1 == $i ) {
						$output .= '<a href="' . get_permalink() . '">' . $nextpagelink . '</a>';
					} else {
						if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
							$output .= '<a href="' . get_permalink() . '&amp;page=' . $i . '">' . $nextpagelink . '</a>';
						else
							$output .= '<a href="' . trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged') . '">' . $nextpagelink . '</a>';
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


function post_custom( $key = '' ) {
	$custom = get_post_custom();

	if ( 1 == count($custom[$key]) )
		return $custom[$key][0];
	else
		return $custom[$key];
}


// this will probably change at some point...
function the_meta() {
	if ( $keys = get_post_custom_keys() ) {
		echo "<ul class='post-meta'>\n";
		foreach ( $keys as $key ) {
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

function wp_dropdown_pages($args = '') {
	$defaults = array(
		'depth' => 0, 'child_of' => 0,
		'selected' => 0, 'echo' => 1,
		'name' => 'page_id', 'show_option_none' => ''
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$pages = get_pages($r);
	$output = '';

	if ( ! empty($pages) ) {
		$output = "<select name='$name'>\n";
		if ( $show_option_none )
			$output .= "\t<option value=''>$show_option_none</option>\n";
		$output .= walk_page_dropdown_tree($pages, $depth, $r);
		$output .= "</select>\n";
	}

	$output = apply_filters('wp_dropdown_pages', $output);

	if ( $echo )
		echo $output;

	return $output;
}

function wp_list_pages($args = '') {
	$defaults = array(
		'depth' => 0, 'show_date' => '',
		'date_format' => get_option('date_format'),
		'child_of' => 0, 'exclude' => '',
		'title_li' => __('Pages'), 'echo' => 1,
		'authors' => '', 'sort_column' => 'menu_order, post_title'
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$output = '';
	$current_page = 0;

	// sanitize, mostly to keep spaces out
	$r['exclude'] = preg_replace('[^0-9,]', '', $r['exclude']);

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

//
// Page helpers
//

function walk_page_tree() {
	$walker = new Walker_Page;
	$args = func_get_args();
	return call_user_func_array(array(&$walker, 'walk'), $args);
}

function walk_page_dropdown_tree() {
	$walker = new Walker_PageDropdown;
	$args = func_get_args();
	return call_user_func_array(array(&$walker, 'walk'), $args);
}

//
// Attachments
//

function the_attachment_link($id = 0, $fullsize = false, $deprecated = false, $permalink = false) {
	if ( $fullsize )
		echo wp_get_attachment_link($id, 'full', $permalink);
	else
		echo wp_get_attachment_link($id, 'thumbnail', $permalink);
}

// get an attachment page link using an image or icon if possible
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

	return "<a href='$url' title='$post_title'>$link_text</a>";

}

// deprecated - use wp_get_attachment_link()
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


// deprecated: use wp_get_attachment_image_src()
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

// deprecated: use wp_get_attachment_image()
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

// deprecated: use wp_get_attachment_image()
function get_attachment_innerHTML($id = 0, $fullsize = false, $max_dims = false) {
	$id = (int) $id;
	if ( !$post = & get_post($id) )
		return false;

	if ( $innerHTML = get_attachment_icon($post->ID, $fullsize, $max_dims))
		return $innerHTML;


	$innerHTML = attribute_escape($post->post_title);

	return apply_filters('attachment_innerHTML', $innerHTML, $post->ID);
}

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

function get_the_password_form() {
	global $post;
	$label = 'pwbox-'.(empty($post->ID) ? rand() : $post->ID);
	$output = '<form action="' . get_option('siteurl') . '/wp-pass.php" method="post">
	<p>' . __("This post is password protected. To view it please enter your password below:") . '</p>
	<p><label for="' . $label . '">' . __("Password:") . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . __("Submit") . '" /></p>
	</form>
	';
	return $output;
}

/**
 * is_page_template() - Determine wether or not we are in a page template
 *
 * This template tag allows you to determine wether or not you are in a page template.
 * You can optional provide a template name and then the check will be specific to
 * that template.
 *
 * @package Template Tags
 * @global object $wp_query
 * @param string $template The specific template name if specific matching is required
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

?>
