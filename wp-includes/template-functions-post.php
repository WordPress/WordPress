<?php

function get_the_password_form() {
	$output = '<form action="' . get_settings('siteurl') . '/wp-pass.php" method="post">
	<p>' . __("This post is password protected. To view it please enter your password below:") . '</p>
	<p><label>' . __("Password:") . ' <input name="post_password" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . __("Submit") . '" /></p>
	</form>
	';
	return $output;
}


function the_ID() {
	global $id;
	echo $id;
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

	return $title;
}


function get_the_guid( $id = 0 ) {
	$post = &get_post($id);

	return apply_filters('get_the_guid', $post->guid);
}


function the_guid( $id = 0 ) {
	echo get_the_guid($id);
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
	$content = explode('<!--more-->', $content, 2);
	if ( (preg_match('/<!--noteaser-->/', $post->post_content) && ((!$multipage) || ($page==1))) )
		$stripteaser = 1;
	$teaser = $content[0];
	if ( ($more) && ($stripteaser) )
		$teaser = '';
	$output .= $teaser;
	if ( count($content) > 1 ) {
		if ( $more )
			$output .= '<a id="more-'.$id.'"></a>'.$content[1];
		else
			$output .= ' <a href="'. get_permalink() . "#more-$id\">$more_link_text</a>";
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


/*
Post-meta: Custom per-post fields.
*/


function get_post_custom( $post_id = 0 ) {
	global $id, $post_meta_cache, $wpdb;

	if ( ! $post_id )
		$post_id = $id;

	if ( isset($post_meta_cache[$post_id]) )
		return $post_meta_cache[$post_id];

	if ( $meta_list = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta	WHERE post_id = '$post_id' ORDER BY post_id, meta_key", ARRAY_A) ) {
		// Change from flat structure to hierarchical:
		$post_meta_cache = array();
		foreach ( $meta_list as $metarow ) {
			$mpid = $metarow['post_id'];
			$mkey = $metarow['meta_key'];
			$mval = $metarow['meta_value'];

			// Force subkeys to be array type:
			if ( !isset($post_meta_cache[$mpid]) || !is_array($post_meta_cache[$mpid]) )
				$post_meta_cache[$mpid] = array();

			if ( !isset($post_meta_cache[$mpid]["$mkey"]) || !is_array($post_meta_cache[$mpid]["$mkey"]) )
				$post_meta_cache[$mpid]["$mkey"] = array();

			// Add a value to the current pid/key:
			$post_meta_cache[$mpid][$mkey][] = $mval;
		}
		return $post_meta_cache[$mpid];
	}
}


function get_post_custom_keys() {
	$custom = get_post_custom();

	if ( ! is_array($custom) )
		return;

	if ( $keys = array_keys($custom) )
		return $keys;
}


function get_post_custom_values( $key = '' ) {
	$custom = get_post_custom();

	return $custom[$key];
}


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


/*
Pages
*/


function &get_page_children($page_id, $pages) {
	global $page_cache;

	if ( empty($pages) )
		$pages = &$page_cache;

	$page_list = array();
	foreach ( $pages as $page ) {
		if ( $page->post_parent == $page_id ) {
			$page_list[] = $page;
			if ( $children = get_page_children($page->ID, $pages) )
				$page_list = array_merge($page_list, $children);
		}
	}
	return $page_list;
}


function &get_pages($args = '') {
	global $wpdb;

	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);

	$defaults = array('child_of' => 0, 'sort_order' => 'ASC', 'sort_column' => 'post_title',
		'hierarchical' => 1);
	$r = array_merge($defaults, $r);

	$exclusions = '';
	if ( !empty($r['exclude']) ) {
		$expages = preg_split('/[\s,]+/',$r['exclude']);
		if ( count($expages) ) {
			foreach ( $expages as $expage ) {
				$exclusions .= ' AND ID <> ' . intval($expage) . ' ';
			}
		}
	}

	$pages = $wpdb->get_results("SELECT * " .
		"FROM $wpdb->posts " .
		"WHERE post_type = 'page' AND post_status = 'publish' " .
		"$exclusions " .
		"ORDER BY " . $r['sort_column'] . " " . $r['sort_order']);

	if ( empty($pages) )
		return array();

	// Update cache.
	update_page_cache($pages);

	if ( $r['child_of'] || $r['hierarchical'] )
		$pages = & get_page_children($r['child_of'], $pages);

	return $pages;
}

function wp_dropdown_pages($args = '') {
	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);

	$defaults = array('depth' => 0, 'child_of' => 0, 'selected' => 0, 'echo' => 1,
		'name' => 'page_id');
	$r = array_merge($defaults, $r);
	extract($r);

	$pages = get_pages($r);
	$output = '';

	if ( ! empty($pages) ) {
		$output = "<select name='$name'>\n";
		$output .= walk_page_tree($pages, $depth, '_page_dropdown_element', '', '', '', $selected);
		$output .= "</select>\n";
	}

	$output = apply_filters('wp_dropdown_pages', $output);

	if ( $echo )
		echo $output;

	return $output;
}

function _page_dropdown_element($output, $page, $depth, $selected) {
	$pad = str_repeat('&nbsp;', $depth * 3);

	$output .= "\t<option value=\"$page->ID\"";
	if ( $page->ID == $selected )
		$output .= ' selected="selected"';
	$output .= '>';
	$title = wp_specialchars($page->post_title);
	$output .= "$pad$title";
	$output .= "</option>\n";

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
		$output .= walk_page_tree($pages, $depth, '_page_list_element_start', '_page_list_element_end', '_page_list_level_start', '_page_list_level_end', $current_page, $r['show_date'], $r['date_format']);

		if ( $r['title_li'] )
			$output .= '</ul></li>';
	}

	$output = apply_filters('wp_list_pages', $output);

	if ( $r['echo'] )
		echo $output;
	else
		return $output;
}

function _page_list_level_start($output, $depth) {
	$indent = str_repeat("\t", $depth);
	$output .= "$indent<ul>\n";
	return $output;
}

function _page_list_level_end($output, $depth) {
	$indent = str_repeat("\t", $depth);
	$output .= "$indent</ul>\n";
	return $output;
}

function _page_list_element_start($output, $page, $depth, $current_page, $show_date, $date_format) {
	if ( $depth )
		$indent = str_repeat("\t", $depth);

	$css_class = 'page_item';
	if ( $page->ID == $current_page )
		$css_class .= ' current_page_item';

	$output .= $indent . '<li class="' . $css_class . '"><a href="' . get_page_link($page->ID) . '" title="' . wp_specialchars($page->post_title) . '">' . $page->post_title . '</a>';

	if ( !empty($show_date) ) {
		if ( 'modified' == $show_date )
			$time = $page->post_modified;
		else
			$time = $page->post_date;

		$output .= " " . mysql2date($date_format, $time);
	}

	return $output;
}

function _page_list_element_end($output, $page, $depth) {
	$output .= "</li>\n";

	return $output;
}

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

?>
