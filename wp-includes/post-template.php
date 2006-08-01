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
	if ( strlen($title) > 0 ) {
		$title = apply_filters('the_title', $before . $title . $after, $before, $after);
		if ( $echo )
			echo $title;
		else
			return $title;
	}
}


function get_the_title($id = 0) {
	$post = &get_post($id);

	$title = $post->post_title;
	if ( !empty($post->post_password) )
		$title = sprintf(__('Protected: %s'), $title);
	else if ( 'private' == $post->post_status )
		$title = sprintf(__('Private: %s'), $title);

	return $title;
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
	global $id, $post, $more, $single, $withcomments, $page, $pages, $multipage, $numpages;
	global $preview;
	global $pagenow;
	$output = '';

	if ( !empty($post->post_password) ) { // if there's a password
		if ( stripslashes($_COOKIE['wp-postpass_'.COOKIEHASH]) != $post->post_password ) {	// and it doesn't match the cookie
			$output = get_the_password_form();
			return $output;
		}
	}

	if ( $more_file != '' )
		$file = $more_file;
	else
		$file = $pagenow; //$_SERVER['PHP_SELF'];

	$content = $pages[$page-1];
	if ( preg_match('/<!--more(.+?)?-->/', $content, $matches) ) {
		$content = explode($matches[0], $content, 2);
		if ( !empty($matches[1]) )
			$more_link_text = strip_tags(wp_kses_no_null(trim($matches[1])));
	}
	if ( (false !== strpos($post->post_content, '<!--noteaser-->') && ((!$multipage) || ($page==1))) )
		$stripteaser = 1;
	$teaser = $content[0];
	if ( ($more) && ($stripteaser) )
		$teaser = '';
	$output .= $teaser;
	if ( count($content) > 1 ) {
		if ( $more )
			$output .= '<a id="more-'.$id.'"></a>'.$content[1];
		else
			$output = balanceTags($output . ' <a href="'. get_permalink() . "#more-$id\">$more_link_text</a>");
	}
	if ( $preview ) // preview fix for javascript bug with foreign languages
		$output =	preg_replace('/\%u([0-9A-F]{4,4})/e',	"'&#'.base_convert('\\1',16,10).';'", $output);

	return $output;
}


function the_excerpt() {
	echo apply_filters('the_excerpt', get_the_excerpt());
}


function get_the_excerpt($fakeit = true) {
	global $id, $post;
	$output = '';
	$output = $post->post_excerpt;
	if ( !empty($post->post_password) ) { // if there's a password
		if ( $_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password ) {  // and it doesn't match the cookie
			$output = __('There is no excerpt because this is a protected post.');
			return $output;
		}
	}

	return apply_filters('get_the_excerpt', $output);
}


function wp_link_pages($args = '') {
	parse_str($args, $r);
	if ( !isset($r['before']) )
		$r['before'] = '<p>' . __('Pages:');
	if ( !isset($r['after']) )
		$r['after'] = '</p>';
	if ( !isset($r['next_or_number']) )
		$r['next_or_number'] = 'number';
	if ( !isset($r['nextpagelink']) )
		$r['nextpagelink'] = 'Next page';
	if ( !isset($r['previouspagelink']) )
		$r['previouspagelink'] = 'Previous page';
	if ( !isset($r['pagelink']) )
		$r['pagelink'] = '%';
	if ( !isset($r['more_file']) )
		$r['more_file'] = '';

	link_pages($r['before'], $r['after'], $r['next_or_number'], $r['nextpagelink'], $r['previouspagelink'], $r['pagelink'], $r['more_file']);
}


function link_pages($before='<br />', $after='<br />', $next_or_number='number', $nextpagelink='next page', $previouspagelink='previous page', $pagelink='%', $more_file='') {
	global $id, $page, $numpages, $multipage, $more, $pagenow;
	if ( $more_file != '' )
		$file = $more_file;
	else
		$file = $pagenow;
	if ( $multipage ) {
		if ( 'number' == $next_or_number ) {
			echo $before;
			for ( $i = 1; $i < ($numpages+1); $i = $i + 1 ) {
				$j = str_replace('%',"$i",$pagelink);
				echo ' ';
				if ( ($i != $page) || ((!$more) && ($page==1)) ) {
					if ( '' == get_settings('permalink_structure') )
						echo '<a href="' . get_permalink() . '&amp;page=' . $i . '">';
					else
						echo '<a href="' . trailingslashit( get_permalink() ) . $i . '/">';
				}
				echo $j;
				if ( ($i != $page) || ((!$more) && ($page==1)) )
					echo '</a>';
			}
			echo $after;
		} else {
			if ( $more ) {
				echo $before;
				$i = $page - 1;
				if ( $i && $more ) {
					if ( '' == get_settings('permalink_structure') )
						echo '<a href="' . get_permalink() . '&amp;page=' . $i . '">'.$previouspagelink.'</a>';
					else
						echo '<a href="' . get_permalink() . $i . '/">'.$previouspagelink.'</a>';
				}
				$i = $page + 1;
				if ( $i <= $numpages && $more ) {
					if ( '' == get_settings('permalink_structure') )
						echo '<a href="'.get_permalink() . '&amp;page=' . $i . '">'.$nextpagelink.'</a>';
					else
						echo '<a href="'.get_permalink().$i.'/">'.$nextpagelink.'</a>';
				}
				echo $after;
			}
		}
	}
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
	global $id, $post_meta_cache;

	if ( $keys = get_post_custom_keys() ) {
		echo "<ul class='post-meta'>\n";
		foreach ( $keys as $key ) {
			$keyt = trim($key);
			if ( '_' == $keyt{0} )
				continue;
			$values = array_map('trim', get_post_custom_values($key));
			$value = implode($values,', ');
			echo "<li><span class='post-meta-key'>$key:</span> $value</li>\n";
		}
		echo "</ul>\n";
	}
}


//
// Pages
//

function wp_dropdown_pages($args = '') {
	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);

	$defaults = array('depth' => 0, 'child_of' => 0, 'selected' => 0, 'echo' => 1,
		'name' => 'page_id', 'show_option_none' => '');
	$r = array_merge($defaults, $r);
	extract($r);

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
	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);

	$defaults = array('depth' => 0, 'show_date' => '', 'date_format' => get_settings('date_format'),
		'child_of' => 0, 'title_li' => __('Pages'), 'echo' => 1);
	$r = array_merge($defaults, $r);

	$output = '';

	// Query pages.
	$pages = get_pages($r);

	if ( !empty($pages) ) {
		if ( $r['title_li'] )
			$output .= '<li class="pagenav">' . $r['title_li'] . '<ul>';

		global $wp_query;
		$current_page = $wp_query->get_queried_object_id();
		$output .= walk_page_tree($pages, $r['depth'], $current_page, $r['show_date'], $r['date_format']);

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

function the_attachment_link($id = 0, $fullsize = false, $max_dims = false) {
	echo get_the_attachment_link($id, $fullsize, $max_dims);
}

function get_the_attachment_link($id = 0, $fullsize = false, $max_dims = false) {
	$id = (int) $id;
	$_post = & get_post($id);

	if ( ('attachment' != $_post->post_type) || ('' == $_post->guid) )
		return __('Missing Attachment');

	if (! empty($_post->guid) ) {
		$innerHTML = get_attachment_innerHTML($_post->ID, $fullsize, $max_dims);

		return "<a href=\"{$_post->guid}\" title=\"{$_post->post_title}\" >{$innerHTML}</a>";

	} else {
		$p .= __('Missing attachment');
	}
	return $p;
}

function get_attachment_icon($id = 0, $fullsize = false, $max_dims = false) {
	$id = (int) $id;
	$post = & get_post($id);

	$mime = $post->post_mime_type;

	$imagedata = get_post_meta($post->ID, '_wp_attachment_metadata', true);

	$file = get_post_meta($post->ID, '_wp_attached_file', true);

	if ( !$fullsize && !empty($imagedata['thumb'])
			&& ($thumbfile = str_replace(basename($file), $imagedata['thumb'], $file))
			&& file_exists($thumbfile) ) {

		// We have a thumbnail desired, specified and existing

		$src = str_replace(basename($post->guid), $imagedata['thumb'], $post->guid);
		$src_file = $thumbfile;
		$class = 'attachmentthumb';

	} elseif ( substr($mime, 0, 6) == 'image/'
			&& file_exists($file) ) {

		// We have an image without a thumbnail

		$src = $post->guid;
		$src_file = & $file;
		$class = 'attachmentimage';
	} elseif (! empty($mime) ) {

		// No thumb, no image. We'll look for a mime-related icon instead.
		$icon_dir = apply_filters('icon_dir', get_template_directory().'/images');
		$icon_dir_uri = apply_filters('icon_dir_uri', get_template_directory_uri().'/images');

		$types = array(substr($mime, 0, strpos($mime, '/')), substr($mime, strpos($mime, '/') + 1), str_replace('/', '_', $mime));
		$exts = array('jpg', 'gif', 'png');
		foreach ($types as $type) {
			foreach ($exts as $ext) {
				$src_file = "$icon_dir/$type.$ext";
				if ( file_exists($src_file) ) {
					$src = "$icon_dir_uri/$type.$ext";
					break 2;
				}
			}
		}
	}

	if (! isset($src) )
		return false;

	// Do we need to constrain the image?
	if ( ($max_dims = apply_filters('attachment_max_dims', $max_dims)) && file_exists($src_file) ) {

		$imagesize = getimagesize($src_file);

		if (($imagesize[0] > $max_dims[0]) || $imagesize[1] > $max_dims[1] ) {
			$actual_aspect = $imagesize[0] / $imagesize[1];
			$desired_aspect = $max_dims[0] / $max_dims[1];

			if ( $actual_aspect >= $desired_aspect ) {
				$height = $actual_aspect * $max_dims[0];
				$constraint = "width=\"{$max_dims[0]}\" ";
				$post->iconsize = array($max_dims[0], $height);
			} else {
				$width = $max_dims[1] / $actual_aspect;
				$constraint = "height=\"{$max_dims[1]}\" ";
				$post->iconsize = array($width, $max_dims[1]);
			}
		} else {
			$post->iconsize = array($imagesize[0], $imagesize[1]);
		}
	}

	$icon = "<img src=\"{$src}\" title=\"{$post->post_title}\" alt=\"{$post->post_title}\" {$constraint}/>";

	return apply_filters('attachment_icon', $icon, $post->ID);
}

function get_attachment_innerHTML($id = 0, $fullsize = false, $max_dims = false) {
	$id = (int) $id;

	if ( $innerHTML = get_attachment_icon($id, $fullsize, $max_dims))
		return $innerHTML;

	$post = & get_post($id);

	$innerHTML = $post->post_title;

	return apply_filters('attachment_innerHTML', $innerHTML, $post->ID);
}

function prepend_attachment($content) {
	$p = '<p class="attachment">';
	$p .= get_the_attachment_link(false, true, array(400, 300));
	$p .= '</p>';
	$p = apply_filters('prepend_attachment', $p);

	return "$p\n$content";
}

//
// Misc
//

function get_the_password_form() {
	$output = '<form action="' . get_settings('siteurl') . '/wp-pass.php" method="post">
	<p>' . __("This post is password protected. To view it please enter your password below:") . '</p>
	<p><label>' . __("Password:") . ' <input name="post_password" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . __("Submit") . '" /></p>
	</form>
	';
	return $output;
}

?>
