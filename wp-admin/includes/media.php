<?php

function media_upload_tabs() {
	$_default_tabs = array(
		'type' => __('Choose File'), // handler action suffix => tab text
		'gallery' => __('Gallery'),
		'library' => __('Media Library'),
	);

	return apply_filters('media_upload_tabs', $_default_tabs);
}

function update_gallery_tab($tabs) {
	global $wpdb;
	if ( !isset($_REQUEST['post_id']) ) {
		unset($tabs['gallery']);
		return $tabs;
	}
	if ( intval($_REQUEST['post_id']) )
		$attachments = intval($wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_parent = %d", $_REQUEST['post_id'])));

	$tabs['gallery'] = sprintf(__('Gallery (%s)'), "<span id='attachments-count'>$attachments</span>");

	return $tabs;
}
add_filter('media_upload_tabs', 'update_gallery_tab');

function the_media_upload_tabs() {
	global $redir_tab;
	$tabs = media_upload_tabs();

	if ( !empty($tabs) ) {
		echo "<ul id='sidemenu'>\n";
		if ( isset($redir_tab) && array_key_exists($redir_tab, $tabs) )
			$current = $redir_tab;
		elseif ( isset($_GET['tab']) && array_key_exists($_GET['tab'], $tabs) )
			$current = $_GET['tab'];
		else {
			$keys = array_keys($tabs);
			$current = array_shift($keys);
		}
		foreach ( $tabs as $callback => $text ) {
			$class = '';
			if ( $current == $callback )
				$class = " class='current'";
			$href = add_query_arg(array('tab'=>$callback, 's'=>false, 'paged'=>false, 'post_mime_type'=>false, 'm'=>false));
			$link = "<a href='" . clean_url($href) . "'$class>$text</a>";
			echo "\t<li id='" . attribute_escape("tab-$callback") . "'>$link</li>\n";
		}
		echo "</ul>\n";
	}
}

function get_image_send_to_editor($id, $alt, $title, $align, $url='', $rel = false, $size='medium') {

	$html = get_image_tag($id, $alt, $title, $align, $size);

	$rel = $rel ? ' rel="attachment wp-att-'.attribute_escape($id).'"' : '';

	if ( $url )
		$html = '<a href="' . clean_url($url) . "\"$rel>$html</a>";

	$html = apply_filters( 'image_send_to_editor', $html, $id, $alt, $title, $align, $url, $size );

	return $html;
}

function image_add_caption( $html, $id, $alt, $title, $align, $url, $size ) {

	if ( empty($alt) || apply_filters( 'disable_captions', '' ) ) return $html;
	$id = ( 0 < (int) $id ) ? 'attachment_' . $id : '';

	preg_match( '/width="([0-9]+)/', $html, $matches );
	if ( ! isset($matches[1]) ) return $html;
	$width = $matches[1];

	$html = preg_replace( '/align[^\s\'"]+\s?/', '', $html );
	if ( empty($align) ) $align = 'none';

	$shcode = '[caption id="' . $id . '" align="align' . $align
	. '" width="' . $width . '" caption="' . $alt . '"]' . $html . '[/caption]';

	return apply_filters( 'image_add_caption_shortcode', $shcode, $html );
}
add_filter( 'image_send_to_editor', 'image_add_caption', 20, 7 );

function media_send_to_editor($html) {
	?>
<script type="text/javascript">
/* <![CDATA[ */
var win = window.dialogArguments || opener || parent || top;
win.send_to_editor('<?php echo addslashes($html); ?>');
/* ]]> */
</script>
	<?php
	exit;
}

// this handles the file upload POST itself, creating the attachment post
function media_handle_upload($file_id, $post_id, $post_data = array()) {
	$overrides = array('test_form'=>false);
	$file = wp_handle_upload($_FILES[$file_id], $overrides);

	if ( isset($file['error']) )
		return new WP_Error( 'upload_error', $file['error'] );

	$url = $file['url'];
	$type = $file['type'];
	$file = $file['file'];
	$title = preg_replace('/\.[^.]+$/', '', basename($file));
	$content = '';

	// use image exif/iptc data for title and caption defaults if possible
	if ( $image_meta = @wp_read_image_metadata($file) ) {
		if ( trim($image_meta['title']) )
			$title = $image_meta['title'];
		if ( trim($image_meta['caption']) )
			$content = $image_meta['caption'];
	}

	// Construct the attachment array
	$attachment = array_merge( array(
		'post_mime_type' => $type,
		'guid' => $url,
		'post_parent' => $post_id,
		'post_title' => $title,
		'post_content' => $content,
	), $post_data );

	// Save the data
	$id = wp_insert_attachment($attachment, $file, $post_parent);
	if ( !is_wp_error($id) ) {
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
	}

	return $id;

}

function media_handle_sideload($file_array, $post_id, $desc = null, $post_data = array()) {
	$overrides = array('test_form'=>false);
	$file = wp_handle_sideload($file_array, $overrides);

	if ( isset($file['error']) )
		return new WP_Error( 'upload_error', $file['error'] );

	$url = $file['url'];
	$type = $file['type'];
	$file = $file['file'];
	$title = preg_replace('/\.[^.]+$/', '', basename($file));
	$content = '';

	// use image exif/iptc data for title and caption defaults if possible
	if ( $image_meta = @wp_read_image_metadata($file) ) {
		if ( trim($image_meta['title']) )
			$title = $image_meta['title'];
		if ( trim($image_meta['caption']) )
			$content = $image_meta['caption'];
	}

	$title = @$desc;

	// Construct the attachment array
	$attachment = array_merge( array(
		'post_mime_type' => $type,
		'guid' => $url,
		'post_parent' => $post_id,
		'post_title' => $title,
		'post_content' => $content,
	), $post_data );

	// Save the data
	$id = wp_insert_attachment($attachment, $file, $post_parent);
	if ( !is_wp_error($id) ) {
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
		return $url;
	}
	return $id;
}


// wrap iframe content (produced by $content_func) in a doctype, html head/body etc
// any additional function args will be passed to content_func
function wp_iframe($content_func /* ... */) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php _e('Uploads'); ?> &#8212; <?php _e('WordPress'); ?></title>
<?php
wp_enqueue_style( 'global' );
wp_enqueue_style( 'wp-admin' );
wp_enqueue_style( 'colors' );
if ( 0 === strpos( $content_func, 'media' ) )
	wp_enqueue_style( 'media' );

?>
<script type="text/javascript">
//<![CDATA[
function addLoadEvent(func) {if ( typeof wpOnload!='function'){wpOnload=func;}else{ var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}}
//]]>
</script>
<?php
do_action('admin_print_styles');
do_action('admin_print_scripts');
do_action('admin_head');
if ( is_string($content_func) )
	do_action( "admin_head_{$content_func}" );
?>
</head>
<body<?php if ( isset($GLOBALS['body_id']) ) echo ' id="' . $GLOBALS['body_id'] . '"'; ?>>
<?php
	$args = func_get_args();
	$args = array_slice($args, 1);
	call_user_func_array($content_func, $args);
?>
</body>
</html>
<?php
}

function media_buttons() {
	global $post_ID, $temp_ID;
	$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);
	$context = apply_filters('media_buttons_context', __('Add media: %s'));
	$media_upload_iframe_src = "media-upload.php?post_id=$uploading_iframe_ID";
	$media_title = __('Add Media');
	$image_upload_iframe_src = apply_filters('image_upload_iframe_src', "$media_upload_iframe_src&amp;type=image");
	$image_title = __('Add an Image');
	$video_upload_iframe_src = apply_filters('video_upload_iframe_src', "$media_upload_iframe_src&amp;type=video");
	$video_title = __('Add Video');
	$audio_upload_iframe_src = apply_filters('audio_upload_iframe_src', "$media_upload_iframe_src&amp;type=audio");
	$audio_title = __('Add Audio');
	$out = <<<EOF

	<a href="{$image_upload_iframe_src}&amp;TB_iframe=true" id="add_image" class="thickbox" title='$image_title'><img src='images/media-button-image.gif' alt='$image_title' /></a>
	<a href="{$video_upload_iframe_src}&amp;TB_iframe=true" id="add_video" class="thickbox" title='$video_title'><img src='images/media-button-video.gif' alt='$video_title' /></a>
	<a href="{$audio_upload_iframe_src}&amp;TB_iframe=true" id="add_audio" class="thickbox" title='$audio_title'><img src='images/media-button-music.gif' alt='$audio_title' /></a>
	<a href="{$media_upload_iframe_src}&amp;TB_iframe=true" id="add_media" class="thickbox" title='$media_title'><img src='images/media-button-other.gif' alt='$media_title' /></a>

EOF;
	printf($context, $out);
}
add_action( 'media_buttons', 'media_buttons' );
add_action('media_upload_media', 'media_upload_handler');

function media_upload_form_handler() {
	check_admin_referer('media-form');

	if ( !empty($_POST['attachments']) ) foreach ( $_POST['attachments'] as $attachment_id => $attachment ) {
		$post = $_post = get_post($attachment_id, ARRAY_A);
		if ( isset($attachment['post_content']) )
			$post['post_content'] = $attachment['post_content'];
		if ( isset($attachment['post_title']) )
			$post['post_title'] = $attachment['post_title'];
		if ( isset($attachment['post_excerpt']) )
			$post['post_excerpt'] = $attachment['post_excerpt'];
		if ( isset($attachment['menu_order']) )
			$post['menu_order'] = $attachment['menu_order'];

		$post = apply_filters('attachment_fields_to_save', $post, $attachment);

		if ( isset($post['errors']) ) {
			$errors[$attachment_id] = $post['errors'];
			unset($post['errors']);
		}

		if ( $post != $_post )
			wp_update_post($post);

		foreach ( get_attachment_taxonomies($post) as $t )
			if ( isset($attachment[$t]) )
				wp_set_object_terms($attachment_id, array_map('trim', preg_split('/,+/', $attachment[$t])), $t, false);
	}

	if ( isset($_POST['insert-gallery']) )
		return media_send_to_editor('[gallery]');

	if ( isset($_POST['send']) ) {
		$keys = array_keys($_POST['send']);
		$send_id = (int) array_shift($keys);
		$attachment = stripslashes_deep( $_POST['attachments'][$send_id] );
		$html = $attachment['post_title'];
		if ( !empty($attachment['url']) ) {
			if ( strpos($attachment['url'], 'attachment_id') || false !== strpos($attachment['url'], get_permalink($_POST['post_id'])) )
				$rel = " rel='attachment wp-att-".attribute_escape($send_id)."'";
			$html = "<a href='{$attachment['url']}'$rel>$html</a>";
		}
		$html = apply_filters('media_send_to_editor', $html, $send_id, $attachment);
		return media_send_to_editor($html);
	}

	return $errors;
}

function media_upload_image() {
	if ( isset($_POST['html-upload']) && !empty($_FILES) ) {
		// Upload File button was clicked
		$id = media_handle_upload('async-upload', $_REQUEST['post_id']);
		unset($_FILES);
		if ( is_wp_error($id) ) {
			$errors['upload_error'] = $id;
			$id = false;
		}
	}

	if ( !empty($_POST['insertonlybutton']) ) {
		$src = $_POST['insertonly']['src'];
		if ( !empty($src) && !strpos($src, '://') )
			$src = "http://$src";
		$alt = attribute_escape($_POST['insertonly']['alt']);
		if ( isset($_POST['insertonly']['align']) ) {
			$align = attribute_escape($_POST['insertonly']['align']);
			$class = " class='align$align'";
		}
		if ( !empty($src) )
			$html = "<img src='$src' alt='$alt'$class />";
		return media_send_to_editor($html);
	}

	if ( !empty($_POST) ) {
		$return = media_upload_form_handler();

		if ( is_string($return) )
			return $return;
		if ( is_array($return) )
			$errors = $return;
	}

	if ( isset($_POST['save']) ) {
		$errors['upload_notice'] = __('Saved.');
		return media_upload_gallery();
	}

	return wp_iframe( 'media_upload_type_form', 'image', $errors, $id );
}

function media_sideload_image($file, $post_id, $desc = null) {
	if (!empty($file) ) {
		$file_array['name'] = basename($file);
		$file_array['tmp_name'] = download_url($file);
		$desc = @$desc;

		$id = media_handle_sideload($file_array, $post_id, $desc);
		$src = $id;

		if ( is_wp_error($id) ) {
			@unlink($file_array['tmp_name']);
			return $id;
		}
	}

	if ( !empty($src) ) {
		$alt = @$desc;
		$html = "<img src='$src' alt='$alt' />";
		return $html;
	}
}

function media_upload_audio() {
	if ( isset($_POST['html-upload']) && !empty($_FILES) ) {
		// Upload File button was clicked
		$id = media_handle_upload('async-upload', $_REQUEST['post_id']);
		unset($_FILES);
		if ( is_wp_error($id) ) {
			$errors['upload_error'] = $id;
			$id = false;
		}
	}

	if ( !empty($_POST['insertonlybutton']) ) {
		$href = $_POST['insertonly']['href'];
		if ( !empty($href) && !strpos($href, '://') )
			$href = "http://$href";
		$title = attribute_escape($_POST['insertonly']['title']);
		if ( empty($title) )
			$title = basename($href);
		if ( !empty($title) && !empty($href) )
			$html = "<a href='$href' >$title</a>";
		return media_send_to_editor($html);
	}

	if ( !empty($_POST) ) {
		$return = media_upload_form_handler();

		if ( is_string($return) )
			return $return;
		if ( is_array($return) )
			$errors = $return;
	}

	if ( isset($_POST['save']) ) {
		$errors['upload_notice'] = __('Saved.');
		return media_upload_gallery();
	}

	return wp_iframe( 'media_upload_type_form', 'audio', $errors, $id );
}

function media_upload_video() {
	if ( isset($_POST['html-upload']) && !empty($_FILES) ) {
		// Upload File button was clicked
		$id = media_handle_upload('async-upload', $_REQUEST['post_id']);
		unset($_FILES);
		if ( is_wp_error($id) ) {
			$errors['upload_error'] = $id;
			$id = false;
		}
	}

	if ( !empty($_POST['insertonlybutton']) ) {
		$href = $_POST['insertonly']['href'];
		if ( !empty($href) && !strpos($href, '://') )
			$href = "http://$href";
		$title = attribute_escape($_POST['insertonly']['title']);
		if ( empty($title) )
			$title = basename($href);
		if ( !empty($title) && !empty($href) )
			$html = "<a href='$href' >$title</a>";
		return media_send_to_editor($html);
	}

	if ( !empty($_POST) ) {
		$return = media_upload_form_handler();

		if ( is_string($return) )
			return $return;
		if ( is_array($return) )
			$errors = $return;
	}

	if ( isset($_POST['save']) ) {
		$errors['upload_notice'] = __('Saved.');
		return media_upload_gallery();
	}

	return wp_iframe( 'media_upload_type_form', 'video', $errors, $id );
}

function media_upload_file() {
	if ( isset($_POST['html-upload']) && !empty($_FILES) ) {
		// Upload File button was clicked
		$id = media_handle_upload('async-upload', $_REQUEST['post_id']);
		unset($_FILES);
		if ( is_wp_error($id) ) {
			$errors['upload_error'] = $id;
			$id = false;
		}
	}

	if ( !empty($_POST['insertonlybutton']) ) {
		$href = $_POST['insertonly']['href'];
		if ( !empty($href) && !strpos($href, '://') )
			$href = "http://$href";
		$title = attribute_escape($_POST['insertonly']['title']);
		if ( empty($title) )
			$title = basename($href);
		if ( !empty($title) && !empty($href) )
			$html = "<a href='$href' >$title</a>";
		return media_send_to_editor($html);
	}

	if ( !empty($_POST) ) {
		$return = media_upload_form_handler();

		if ( is_string($return) )
			return $return;
		if ( is_array($return) )
			$errors = $return;
	}

	if ( isset($_POST['save']) ) {
		$errors['upload_notice'] = __('Saved.');
		return media_upload_gallery();
	}

	return wp_iframe( 'media_upload_type_form', 'file', $errors, $id );
}

function media_upload_gallery() {
	if ( !empty($_POST) ) {
		$return = media_upload_form_handler();

		if ( is_string($return) )
			return $return;
		if ( is_array($return) )
			$errors = $return;
	}

	wp_enqueue_script('admin-gallery');
	return wp_iframe( 'media_upload_gallery_form', $errors );
}

function media_upload_library() {
	if ( !empty($_POST) ) {
		$return = media_upload_form_handler();

		if ( is_string($return) )
			return $return;
		if ( is_array($return) )
			$errors = $return;
	}

	return wp_iframe( 'media_upload_library_form', $errors );
}

function image_attachment_fields_to_edit($form_fields, $post) {
	if ( substr($post->post_mime_type, 0, 5) == 'image' ) {
		$form_fields['post_title']['required'] = true;

		$form_fields['post_excerpt']['label'] = __('Caption');
		$form_fields['post_excerpt']['helps'][] = __('Also used as alternate text for the image');

		$form_fields['post_content']['label'] = __('Description');

		$thumb = wp_get_attachment_thumb_url($post->ID);

		$form_fields['align'] = array(
			'label' => __('Alignment'),
			'input' => 'html',
			'html'  => "
				<input type='radio' name='attachments[$post->ID][align]' id='image-align-none-$post->ID' value='none' checked='checked' />
				<label for='image-align-none-$post->ID' class='align image-align-none-label'>" . __('None') . "</label>
				<input type='radio' name='attachments[$post->ID][align]' id='image-align-left-$post->ID' value='left' />
				<label for='image-align-left-$post->ID' class='align image-align-left-label'>" . __('Left') . "</label>
				<input type='radio' name='attachments[$post->ID][align]' id='image-align-center-$post->ID' value='center' />
				<label for='image-align-center-$post->ID' class='align image-align-center-label'>" . __('Center') . "</label>
				<input type='radio' name='attachments[$post->ID][align]' id='image-align-right-$post->ID' value='right' />
				<label for='image-align-right-$post->ID' class='align image-align-right-label'>" . __('Right') . "</label>\n",
		);
		$form_fields['image-size'] = array(
			'label' => __('Size'),
			'input' => 'html',
			'html'  => "
				" . ( $thumb ? "<input type='radio' name='attachments[$post->ID][image-size]' id='image-size-thumb-$post->ID' value='thumbnail' />
				<label for='image-size-thumb-$post->ID'>" . __('Thumbnail') . "</label>
				" : '' ) . "<input type='radio' name='attachments[$post->ID][image-size]' id='image-size-medium-$post->ID' value='medium' checked='checked' />
				<label for='image-size-medium-$post->ID'>" . __('Medium') . "</label>
				<input type='radio' name='attachments[$post->ID][image-size]' id='image-size-full-$post->ID' value='full' />
				<label for='image-size-full-$post->ID'>" . __('Full size') . "</label>",
		);
	}
	return $form_fields;
}

add_filter('attachment_fields_to_edit', 'image_attachment_fields_to_edit', 10, 2);

function media_single_attachment_fields_to_edit( $form_fields, $post ) {
	unset($form_fields['url'], $form_fields['align'], $form_fields['image-size']);
	return $form_fields;
}

function image_attachment_fields_to_save($post, $attachment) {
	if ( substr($post['post_mime_type'], 0, 5) == 'image' ) {
		if ( strlen(trim($post['post_title'])) == 0 ) {
			$post['post_title'] = preg_replace('/\.\w+$/', '', basename($post['guid']));
			$post['errors']['post_title']['errors'][] = __('Empty Title filled from filename.');
		}
	}

	return $post;
}

add_filter('attachment_fields_to_save', 'image_attachment_fields_to_save', 10, 2);

function image_media_send_to_editor($html, $attachment_id, $attachment) {
	$post =& get_post($attachment_id);
	if ( substr($post->post_mime_type, 0, 5) == 'image' ) {
		$url = $attachment['url'];

		if ( isset($attachment['align']) )
			$align = $attachment['align'];
		else
			$align = 'none';

		if ( !empty($attachment['image-size']) )
			$size = $attachment['image-size'];
		else
			$size = 'medium';

		$rel = ( $url == get_attachment_link($attachment_id) );

		return get_image_send_to_editor($attachment_id, $attachment['post_excerpt'], $attachment['post_title'], $align, $url, $rel, $size);
	}

	return $html;
}

add_filter('media_send_to_editor', 'image_media_send_to_editor', 10, 3);

function get_attachment_fields_to_edit($post, $errors = null) {
	if ( is_int($post) )
		$post =& get_post($post);
	if ( is_array($post) )
		$post = (object) $post;

	$edit_post = sanitize_post($post, 'edit');
	$file = wp_get_attachment_url($post->ID);
	$link = get_attachment_link($post->ID);

	$form_fields = array(
		'post_title'   => array(
			'label'      => __('Title'),
			'value'      => $edit_post->post_title,
		),
		'post_excerpt' => array(
			'label'      => __('Caption'),
			'value'      => $edit_post->post_excerpt,
		),
		'post_content' => array(
			'label'      => __('Description'),
			'value'      => $edit_post->post_content,
			'input'      => 'textarea',
		),
		'url'          => array(
			'label'      => __('Link URL'),
			'input'      => 'html',
			'html'       => "
				<input type='text' name='attachments[$post->ID][url]' value='" . attribute_escape($file) . "' /><br />
				<button type='button' class='button url-$post->ID' title=''>" . __('None') . "</button>
				<button type='button' class='button url-$post->ID' title='" . attribute_escape($file) . "'>" . __('File URL') . "</button>
				<button type='button' class='button url-$post->ID' title='" . attribute_escape($link) . "'>" . __('Post URL') . "</button>
				<script type='text/javascript'>
				jQuery('button.url-$post->ID').bind('click', function(){jQuery(this).siblings('input').val(jQuery(this).attr('title'));});
				</script>\n",
			'helps'      => __('Enter a link URL or click above for presets.'),
		),
    	'menu_order'   => array(
			'label'      => __('Order'),
			'value'      => $edit_post->menu_order
		),
	);

	foreach ( get_attachment_taxonomies($post) as $taxonomy ) {
		$t = (array) get_taxonomy($taxonomy);
		if ( empty($t['label']) )
			$t['label'] = $taxonomy;
		if ( empty($t['args']) )
			$t['args'] = array();

		$terms = get_object_term_cache($post->ID, $taxonomy);
		if ( empty($terms) )
			$terms = wp_get_object_terms($post->ID, $taxonomy, $t['args']);

		$values = array();

		foreach ( $terms as $term )
			$values[] = $term->name;
		$t['value'] = join(', ', $values);

		$form_fields[$taxonomy] = $t;
	}

	// Merge default fields with their errors, so any key passed with the error (e.g. 'error', 'helps', 'value') will replace the default
	// The recursive merge is easily traversed with array casting: foreach( (array) $things as $thing )
	$form_fields = array_merge_recursive($form_fields, (array) $errors);

	$form_fields = apply_filters('attachment_fields_to_edit', $form_fields, $post);

	return $form_fields;
}

function get_media_items( $post_id, $errors ) {
	if ( $post_id ) {
		$post = get_post($post_id);
		if ( $post && $post->post_type == 'attachment' )
			$attachments = array($post->ID => $post);
		else
			$attachments = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'orderby' => 'menu_order ASC, ID', 'order' => 'DESC') );
	} else {
		if ( is_array($GLOBALS['wp_the_query']->posts) )
			foreach ( $GLOBALS['wp_the_query']->posts as $attachment )
				$attachments[$attachment->ID] = $attachment;
	}

	if ( empty($attachments) )
		return '';

	foreach ( $attachments as $id => $attachment )
		if ( $item = get_media_item( $id, array( 'errors' => isset($errors[$id]) ? $errors[$id] : null) ) )
			$output .= "\n<div id='media-item-$id' class='media-item child-of-$attachment->post_parent preloaded'><div class='progress'><div class='bar'></div></div><div id='media-upload-error-$id'></div><div class='filename'></div>$item\n</div>";

	return $output;
}

function get_media_item( $attachment_id, $args = null ) {
	global $redir_tab;

	$default_args = array( 'errors' => null, 'send' => true, 'delete' => true, 'toggle' => true );
	$args = wp_parse_args( $args, $default_args );
	extract( $args, EXTR_SKIP );

	global $post_mime_types;
	if ( ( $attachment_id = intval($attachment_id) ) && $thumb_url = get_attachment_icon_src( $attachment_id ) )
		$thumb_url = $thumb_url[0];
	else
		return false;

	$title_label = __('Title');
	$description_label = __('Description');
	$tags_label = __('Tags');

	$toggle_on = __('Show');
	$toggle_off = __('Hide');

	$post = get_post($attachment_id);

	$filename = basename($post->guid);
	$title = attribute_escape($post->post_title);
	$description = attribute_escape($post->post_content);
	if ( $_tags = get_the_tags($attachment_id) ) {
		foreach ( $_tags as $tag )
			$tags[] = $tag->name;
		$tags = attribute_escape(join(', ', $tags));
	}

	if ( isset($post_mime_types) ) {
		$keys = array_keys(wp_match_mime_types(array_keys($post_mime_types), $post->post_mime_type));
		$type = array_shift($keys);
		$type = "<input type='hidden' id='type-of-$attachment_id' value='" . attribute_escape( $type ) . "' />";
	}

	$form_fields = get_attachment_fields_to_edit($post, $errors);

	if ( $toggle ) {
		$class = empty($errors) ? 'startclosed' : 'startopen';
		$toggle_links = "
	<a class='toggle describe-toggle-on' href='#'>$toggle_on</a>
	<a class='toggle describe-toggle-off' href='#'>$toggle_off</a>";
	} else {
		$class = 'form-table';
		$toggle_links = '';
	}

	$display_title = ( !empty( $title ) ) ? $title : $filename; // $title shouldn't ever be empty, but just in case
	$display_title = wp_html_excerpt($display_title, 60);

	$gallery = ( (isset($_REQUEST['tab']) && 'gallery' == $_REQUEST['tab']) || (isset($redir_tab) && 'gallery' == $redir_tab) ) ? true : false;
	$order = '';

	foreach ( $form_fields as $key => $val ) {
		if ( 'menu_order' == $key ) {
			if ( $gallery )
				$order = '<div class="menu_order"> <input class="menu_order_input" type="text" id="attachments['.$attachment_id.'][menu_order]" name="attachments['.$attachment_id.'][menu_order]" value="'.$val['value'].'" /></div>';
			else
				$order = '<input type="hidden" name="attachments['.$attachment_id.'][menu_order]" value="'.$val['value'].'" />';

			unset($form_fields['menu_order']);
			break;
		}
	}

	$item = "
	$type
	$toggle_links
	$order
	<div class='filename new'>$display_title</div>
	<table class='slidetoggle describe $class'>
		<thead class='media-item-info'>
		<tr>
			<td class='A1B1' rowspan='4'><img class='thumbnail' src='$thumb_url' alt='' /></td>
			<td>$filename</td>
		</tr>
		<tr><td>$post->post_mime_type</td></tr>
		<tr><td>" . mysql2date($post->post_date, get_option('time_format')) . "</td></tr>
		<tr><td>" . apply_filters('media_meta', '', $post) . "</td></tr>
		</thead>
		<tbody>\n";

	$defaults = array(
		'input'      => 'text',
		'required'   => false,
		'value'      => '',
		'extra_rows' => array(),
	);

	$delete_href = wp_nonce_url("post.php?action=delete-post&amp;post=$attachment_id", 'delete-post_' . $attachment_id);
	if ( $send )
		$send = "<input type='submit' class='button' name='send[$attachment_id]' value='" . attribute_escape( __( 'Insert into Post' ) ) . "' />";
	if ( $delete )
		$delete = "<a href=\"#\" class=\"del-link\" onclick=\"document.getElementById('del_attachment_$attachment_id').style.display='block';return false;\">" . __('Delete') . "</a>";
	if ( ( $send || $delete ) && !isset($form_fields['buttons']) )
		$form_fields['buttons'] = array('tr' => "\t\t<tr class='submit'><td></td><td class='savesend'>$send $delete
		<div id=\"del_attachment_$attachment_id\" class=\"del-attachment\" style=\"display:none;\">" . sprintf(__("You are about to delete <strong>%s</strong>."), $filename) . " <a href=\"$delete_href\" id=\"del[$attachment_id]\" class=\"delete\">" . __('Continue') . "</a>
		<a href=\"#\" class=\"del-link\" onclick=\"this.parentNode.style.display='none';return false;\">" . __('Cancel') . "</a></div></td></tr>\n");

	$hidden_fields = array();

	foreach ( $form_fields as $id => $field ) {
		if ( $id{0} == '_' )
			continue;

		if ( !empty($field['tr']) ) {
			$item .= $field['tr'];
			continue;
		}

		$field = array_merge($defaults, $field);
		$name = "attachments[$attachment_id][$id]";

		if ( $field['input'] == 'hidden' ) {
			$hidden_fields[$name] = $field['value'];
			continue;
		}

		$required = $field['required'] ? '<abbr title="required" class="required">*</abbr>' : '';
		$aria_required = $field['required'] ? " aria-required='true' " : '';
		$class  = $id;
		$class .= $field['required'] ? ' form-required' : '';

		$item .= "\t\t<tr class='$class'>\n\t\t\t<th valign='top' scope='row' class='label'><label for='$name'><span class='alignleft'>{$field['label']}</span><span class='alignright'>$required</span><br class='clear' /></label></th>\n\t\t\t<td class='field'>";
		if ( !empty($field[$field['input']]) )
			$item .= $field[$field['input']];
		elseif ( $field['input'] == 'textarea' ) {
			$item .= "<textarea type='text' id='$name' name='$name'" . $aria_required . ">" . htmlspecialchars( $field['value'] ) . "</textarea>";
		} else {
			$item .= "<input type='text' id='$name' name='$name' value='" . attribute_escape( $field['value'] ) . "'" . $aria_required . "/>";
		}
		if ( !empty($field['helps']) )
			$item .= "<p class='help'>" . join( "</p>\n<p class='help'>", array_unique((array) $field['helps']) ) . '</p>';
		$item .= "</td>\n\t\t</tr>\n";

		$extra_rows = array();

		if ( !empty($field['errors']) )
			foreach ( array_unique((array) $field['errors']) as $error )
				$extra_rows['error'][] = $error;

		if ( !empty($field['extra_rows']) )
			foreach ( $field['extra_rows'] as $class => $rows )
				foreach ( (array) $rows as $html )
					$extra_rows[$class][] = $html;

		foreach ( $extra_rows as $class => $rows )
			foreach ( $rows as $html )
				$item .= "\t\t<tr><td></td><td class='$class'>$html</td></tr>\n";
	}

	if ( !empty($form_fields['_final']) )
		$item .= "\t\t<tr class='final'><td colspan='2'>{$form_fields['_final']}</td></tr>\n";
	$item .= "\t</tbody>\n";
	$item .= "\t</table>\n";

	foreach ( $hidden_fields as $name => $value )
		$item .= "\t<input type='hidden' name='$name' id='$name' value='" . attribute_escape( $value ) . "' />\n";

	return $item;
}

function media_upload_header() {
	?>
	<script type="text/javascript">post_id = <?php echo intval($_REQUEST['post_id']); ?>;</script>
	<div id="media-upload-header">
	<?php the_media_upload_tabs(); ?>
	</div>
	<?php
}

function media_upload_form( $errors = null ) {
	global $type, $tab;

	$flash_action_url = admin_url('async-upload.php');

	// If Mac and mod_security, no Flash. :(
	$flash = true;
	if ( false !== strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'mac') && apache_mod_loaded('mod_security') )
		$flash = false;

	$flash = apply_filters('flash_uploader', $flash);
	$post_id = intval($_REQUEST['post_id']);

?>
<div id="media-upload-notice">
<?php if (isset($errors['upload_notice']) ) { ?>
	<?php echo $errors['upload_notice']; ?>
<?php } ?>
</div>
<div id="media-upload-error">
<?php if (isset($errors['upload_error']) && is_wp_error($errors['upload_error'])) { ?>
	<?php echo $errors['upload_error']->get_error_message(); ?>
<?php } ?>
</div>

<?php do_action('pre-upload-ui'); ?>

<?php if ( $flash ) : ?>
<script type="text/javascript">
<!--
jQuery(function($){
	swfu = new SWFUpload({
			upload_url : "<?php echo attribute_escape( $flash_action_url ); ?>",
			flash_url : "<?php echo includes_url('js/swfupload/swfupload_f9.swf'); ?>",
			file_post_name: "async-upload",
			file_types: "<?php echo apply_filters('upload_file_glob', '*.*'); ?>",
			post_params : {
				"post_id" : "<?php echo $post_id; ?>",
				"auth_cookie" : "<?php if ( is_ssl() ) echo $_COOKIE[SECURE_AUTH_COOKIE]; else echo $_COOKIE[AUTH_COOKIE]; ?>",
				"_wpnonce" : "<?php echo wp_create_nonce('media-form'); ?>",
				"type" : "<?php echo $type; ?>",
				"tab" : "<?php echo $tab; ?>",
				"short" : "1"
			},
			file_size_limit : "<?php echo wp_max_upload_size(); ?>b",
			swfupload_element_id : "flash-upload-ui", // id of the element displayed when swfupload is available
			degraded_element_id : "html-upload-ui",   // when swfupload is unavailable
			file_dialog_start_handler : fileDialogStart,
			file_queued_handler : fileQueued,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,

			debug: false
		});
	$("#flash-browse-button").bind( "click", function(){swfu.selectFiles();});
});
//-->
</script>

<div id="flash-upload-ui">
<?php do_action('pre-flash-upload-ui'); ?>
	<p><input id="flash-browse-button" type="button" value="<?php echo attribute_escape( __( 'Choose files to upload' ) ); ?>" class="button" /></p>
<?php do_action('post-flash-upload-ui'); ?>
	<p class="howto"><?php _e('After a file has been uploaded, you can add titles and descriptions.'); ?></p>
</div>

<?php endif; // $flash ?>

<div id="html-upload-ui">
<?php do_action('pre-html-upload-ui'); ?>
	<p>
	<input type="file" name="async-upload" id="async-upload" /> <input type="submit" class="button" name="html-upload" value="<?php echo attribute_escape(__('Upload')); ?>" /> <a href="#" onclick="return top.tb_remove();"><?php _e('Cancel'); ?></a>
	</p>
	<br class="clear" />
	<?php if ( is_lighttpd_before_150() ): ?>
	<p><?php _e('If you want to use all capabilities of the uploader, like uploading multiple files at once, please upgrade to lighttpd 1.5.'); ?></p>
	<?php endif;?>
<?php do_action('post-html-upload-ui'); ?>
</div>
<?php do_action('post-upload-ui'); ?>
<?php
}

function media_upload_type_form($type = 'file', $errors = null, $id = null) {
	media_upload_header();

	$post_id = intval($_REQUEST['post_id']);

	$form_action_url = admin_url("media-upload.php?type=$type&tab=type&post_id=$post_id");
	$form_action_url = apply_filters('media_upload_form_url', $form_action_url, $type);

	$callback = "type_form_$type";
?>

<form enctype="multipart/form-data" method="post" action="<?php echo attribute_escape($form_action_url); ?>" class="media-upload-form type-form validate" id="<?php echo $type; ?>-form">
<input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
<?php wp_nonce_field('media-form'); ?>
<h3><?php _e('From Computer'); ?></h3>
<?php media_upload_form( $errors ); ?>

<script type="text/javascript">
<!--
jQuery(function($){
	var preloaded = $(".media-item.preloaded");
	if ( preloaded.length > 0 ) {
		preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
	}
	updateMediaForm();
});
-->
</script>
<?php if ( $id && !is_wp_error($id) ) : ?>
<div id="media-items">
<?php echo get_media_items( $id, $errors ); ?>
</div>
<input type="submit" class="button savebutton" name="save" value="<?php echo attribute_escape( __( 'Save all changes' ) ); ?>" />

<?php elseif ( is_callable($callback) ) : ?>

<div class="media-blank">
<p style="text-align:center"><?php _e('&mdash; OR &mdash;'); ?></p>
<h3><?php _e('From URL'); ?></h3>
</div>

<script type="text/javascript">
//<![CDATA[
var addExtImage = {

	width : '',
	height : '',
	align : 'alignnone',

	insert : function() {
		var t = this, html, f = document.forms[0], cls, title = '', alt = '', caption = null;

		if ( '' == f.src.value || '' == t.width ) return false;

		if ( f.title.value ) {
			title = f.title.value.replace(/['"<>]+/g, '');
			title = ' title="'+title+'"';
		}

		if ( f.alt.value ) {
			alt = f.alt.value.replace(/['"<>]+/g, '');
<?php if ( ! apply_filters( 'disable_captions', '' ) ) { ?>
			caption = f.alt.value.replace(/'/g, '&#39;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
<?php } ?>
		}

		cls = caption ? '' : ' class="'+t.align+'"';

		html = '<img alt="'+alt+'" src="'+f.src.value+'"'+title+cls+' width="'+t.width+'" height="'+t.height+'" />';

		if ( f.url.value )
			html = '<a href="'+f.url.value+'">'+html+'</a>';

		if ( caption )
			html = '[caption id="" align="'+t.align+'" width="'+t.width+'" caption="'+caption+'"]'+html+'[/caption]';

		var win = window.dialogArguments || opener || parent || top;
		win.send_to_editor(html);
	},

	resetImageData : function() {
		var t = addExtImage;

		t.width = t.height = '';
		document.getElementById('go_button').style.color = '#bbb';
		if ( ! document.forms[0].src.value )
			document.getElementById('status_img').src = 'images/required.gif';
		else document.getElementById('status_img').src = 'images/no.png';
	},

	updateImageData : function() {
		var t = addExtImage;

		t.width = t.preloadImg.width;
		t.height = t.preloadImg.height;
		document.getElementById('go_button').style.color = '#333';
		document.getElementById('status_img').src = 'images/yes.png';
	},

	getImageData : function() {
		var t = addExtImage, src = document.forms[0].src.value;

		if ( ! src ) {
			t.resetImageData();
			return false;
		}
		document.getElementById('status_img').src = 'images/loading.gif';
		t.preloadImg = new Image();
		t.preloadImg.onload = t.updateImageData;
		t.preloadImg.onerror = t.resetImageData;
		t.preloadImg.src = src;
	}
}
//]]>
</script>

<div id="media-items">
<div class="media-item media-blank">
<?php echo call_user_func($callback); ?>
</div>
</div>
<input type="submit" class="button savebutton" name="save" value="<?php echo attribute_escape( __( 'Save all changes' ) ); ?>" />
</form>
<?php
	endif;
}

function media_upload_gallery_form($errors) {
	global $redir_tab;

	$redir_tab = 'gallery';
	media_upload_header();

	$post_id = intval($_REQUEST['post_id']);
	$form_action_url = admin_url("media-upload.php?type={$GLOBALS['type']}&tab=gallery&post_id=$post_id");
?>

<script type="text/javascript">
<!--
jQuery(function($){
	var preloaded = $(".media-item.preloaded");
	if ( preloaded.length > 0 ) {
		preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
		updateMediaForm();
	}
});
-->
</script>

<form enctype="multipart/form-data" method="post" action="<?php echo attribute_escape($form_action_url); ?>" class="media-upload-form validate" id="gallery-form">
<?php wp_nonce_field('media-form'); ?>
<?php //media_upload_form( $errors ); ?>
<table class="widefat">
<thead><tr>
<th><?php _e('Media'); ?></th>
<th class="order-head"><?php _e('Order'); ?></th>
</tr></thead>
</table>
<div id="media-items">
<?php echo get_media_items($post_id, $errors); ?>
</div>
<p class="ml-submit">
<input type="submit" class="button savebutton" name="save" value="<?php echo attribute_escape( __( 'Save all changes' ) ); ?>" />
<input type="submit" class="button insert-gallery" name="insert-gallery" value="<?php echo attribute_escape( __( 'Insert gallery into post' ) ); ?>" />
<input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
<input type="hidden" name="type" value="<?php echo attribute_escape( $GLOBALS['type'] ); ?>" />
<input type="hidden" name="tab" value="<?php echo attribute_escape( $GLOBALS['tab'] ); ?>" />
</p>
</form>
<?php
}

function media_upload_library_form($errors) {
	global $wpdb, $wp_query, $wp_locale, $type, $tab, $post_mime_types;

	media_upload_header();

	$post_id = intval($_REQUEST['post_id']);

	$form_action_url = admin_url("media-upload.php?type={$GLOBALS['type']}&tab=library&post_id=$post_id");

	$_GET['paged'] = intval($_GET['paged']);
	if ( $_GET['paged'] < 1 )
		$_GET['paged'] = 1;
	$start = ( $_GET['paged'] - 1 ) * 10;
	if ( $start < 1 )
		$start = 0;
	add_filter( 'post_limits', $limit_filter = create_function( '$a', "return 'LIMIT $start, 10';" ) );

	list($post_mime_types, $avail_post_mime_types) = wp_edit_attachments_query();

?>

<form id="filter" action="" method="get">
<input type="hidden" name="type" value="<?php echo attribute_escape( $type ); ?>" />
<input type="hidden" name="tab" value="<?php echo attribute_escape( $tab ); ?>" />
<input type="hidden" name="post_id" value="<?php echo (int) $post_id; ?>" />
<input type="hidden" name="post_mime_type" value="<?php echo attribute_escape( $_GET['post_mime_type'] ); ?>" />

<div id="search-filter">
	<label class="hidden" for="post-search-input"><?php _e('Search Media');?>:</label>
	<input type="text" id="post-search-input" name="s" value="<?php the_search_query(); ?>" />
	<input type="submit" value="<?php echo attribute_escape( __( 'Search Media' ) ); ?>" class="button" />
</div>

<ul class="subsubsub">
<?php
$type_links = array();
$_num_posts = (array) wp_count_attachments();
$matches = wp_match_mime_types(array_keys($post_mime_types), array_keys($_num_posts));
foreach ( $matches as $_type => $reals )
	foreach ( $reals as $real )
		$num_posts[$_type] += $_num_posts[$real];
// If available type specified by media button clicked, filter by that type
if ( empty($_GET['post_mime_type']) && !empty($num_posts[$type]) ) {
	$_GET['post_mime_type'] = $type;
	list($post_mime_types, $avail_post_mime_types) = wp_edit_attachments_query();
}
if ( empty($_GET['post_mime_type']) || $_GET['post_mime_type'] == 'all' )
	$class = ' class="current"';
$type_links[] = "<li><a href='" . clean_url(add_query_arg(array('post_mime_type'=>'all', 'paged'=>false, 'm'=>false))) . "'$class>".__('All Types')."</a>";
foreach ( $post_mime_types as $mime_type => $label ) {
	$class = '';

	if ( !wp_match_mime_types($mime_type, $avail_post_mime_types) )
		continue;

	if ( wp_match_mime_types($mime_type, $_GET['post_mime_type']) )
		$class = ' class="current"';

	$type_links[] = "<li><a href='" . clean_url(add_query_arg(array('post_mime_type'=>$mime_type, 'paged'=>false))) . "'$class>" . sprintf(__ngettext($label[2][0], $label[2][1], $num_posts[$mime_type]), "<span id='$mime_type-counter'>" . number_format_i18n( $num_posts[$mime_type] ) . '</span>') . '</a>';
}
echo implode(' | </li>', $type_links) . '</li>';
unset($type_links);
?>
</ul>

<div class="tablenav">

<?php
$page_links = paginate_links( array(
	'base' => add_query_arg( 'paged', '%#%' ),
	'format' => '',
	'total' => ceil($wp_query->found_posts / 10),
	'current' => $_GET['paged']
));

if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft">
<?php

$arc_query = "SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts WHERE post_type = 'attachment' ORDER BY post_date DESC";

$arc_result = $wpdb->get_results( $arc_query );

$month_count = count($arc_result);

if ( $month_count && !( 1 == $month_count && 0 == $arc_result[0]->mmonth ) ) { ?>
<select name='m'>
<option<?php selected( @$_GET['m'], 0 ); ?> value='0'><?php _e('Show all dates'); ?></option>
<?php
foreach ($arc_result as $arc_row) {
	if ( $arc_row->yyear == 0 )
		continue;
	$arc_row->mmonth = zeroise( $arc_row->mmonth, 2 );

	if ( $arc_row->yyear . $arc_row->mmonth == $_GET['m'] )
		$default = ' selected="selected"';
	else
		$default = '';

	echo "<option$default value='" . attribute_escape( $arc_row->yyear . $arc_row->mmonth ) . "'>";
	echo wp_specialchars( $wp_locale->get_month($arc_row->mmonth) . " $arc_row->yyear" );
	echo "</option>\n";
}
?>
</select>
<?php } ?>

<input type="submit" id="post-query-submit" value="<?php echo attribute_escape( __( 'Filter &#187;' ) ); ?>" class="button-secondary" />

</div>

<br class="clear" />
</div>
</form>

<form enctype="multipart/form-data" method="post" action="<?php echo attribute_escape($form_action_url); ?>" class="media-upload-form validate" id="library-form">

<?php wp_nonce_field('media-form'); ?>
<?php //media_upload_form( $errors ); ?>

<script type="text/javascript">
<!--
jQuery(function($){
	var preloaded = $(".media-item.preloaded");
	if ( preloaded.length > 0 ) {
		preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
		updateMediaForm();
	}
});
-->
</script>

<div id="media-items">
<?php echo get_media_items(null, $errors); ?>
</div>
<p class="ml-submit">
<input type="submit" class="button savebutton" name="save" value="<?php echo attribute_escape( __( 'Save all changes' ) ); ?>" />
<input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
</p>
</form>
<?php
}

function type_form_image() {

	if ( apply_filters( 'disable_captions', '' ) ) {
		$alt = __('Alternate Text');
		$alt_help = __('Alt text for the image, e.g. "The Mona Lisa"');
	} else {
		$alt = __('Image Caption');
		$alt_help = __('Also used as alternate text for the image');
	}

	return '
	<table class="describe"><tbody>
		<tr>
			<th valign="top" scope="row" class="label" style="width:120px;">
				<span class="alignleft"><label for="src">' . __('Source') . '</label></span>
				<span class="alignright"><img id="status_img" src="images/required.gif" title="required" alt="required" /></span>
			</th>
			<td class="field"><input id="src" name="src" value="" type="text" aria-required="true" onblur="addExtImage.getImageData()" /></td>
		</tr>

		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="title">' . __('Image Title') . '</label></span>
				<span class="alignright"><abbr title="required" class="required">*</abbr></span>
			</th>
			<td class="field"><p><input id="title" name="title" value="" type="text" aria-required="true" /></p></td>
		</tr>

		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="alt">' . $alt . '</label></span>
			</th>
			<td class="field"><input id="alt" name="alt" value="" type="text" aria-required="true" />
			<p class="help">' . $alt_help . '</p></td>
		</tr>

		<tr class="align">
			<th valign="top" scope="row" class="label"><p><label for="align">' . __('Alignment') . '</label></p></th>
			<td class="field">
				<input name="align" id="align-none" value="alignnone" onclick="addExtImage.align=this.value" type="radio" checked="checked" />
				<label for="align-none" class="align image-align-none-label">' . __('None') . '</label>
				<input name="align" id="align-left" value="alignleft" onclick="addExtImage.align=this.value" type="radio" />
				<label for="align-left" class="align image-align-left-label">' . __('Left') . '</label>
				<input name="align" id="align-center" value="aligncenter" onclick="addExtImage.align=this.value" type="radio" />
				<label for="align-center" class="align image-align-center-label">' . __('Center') . '</label>
				<input name="align" id="align-right" value="alignright" onclick="addExtImage.align=this.value" type="radio" />
				<label for="align-right" class="align image-align-right-label">' . __('Right') . '</label>
			</td>
		</tr>

		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="url">' . __('Link URL') . '</label></span>
			</th>
			<td class="field"><input id="url" name="url" value="" type="text" /><br />

			<button type="button" class="button" value="" onclick="document.forms[0].url.value=null">' . __('None') . '</button>
			<button type="button" class="button" value="" onclick="document.forms[0].url.value=document.forms[0].src.value">' . __('Link to image') . '</button>
			<p class="help">' . __('Enter a link URL or click above for presets.') . '</p></td>
		</tr>

		<tr>
			<td></td>
			<td>
				<input type="button" class="button" id="go_button" style="color:#bbb;" onclick="addExtImage.insert()" value="' . attribute_escape(__('Insert into Post')) . '" />
			</td>
		</tr>
	</tbody></table>
';

}

function type_form_audio() {
	return '
	<table class="describe"><tbody>
		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="insertonly[href]">' . __('Audio File URL') . '</label></span>
				<span class="alignright"><abbr title="required" class="required">*</abbr></span>
			</th>
			<td class="field"><input id="insertonly[href]" name="insertonly[href]" value="" type="text" aria-required="true"></td>
		</tr>
		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="insertonly[title]">' . __('Title') . '</label></span>
				<span class="alignright"><abbr title="required" class="required">*</abbr></span>
			</th>
			<td class="field"><input id="insertonly[title]" name="insertonly[title]" value="" type="text" aria-required="true"></td>
		</tr>
		<tr><td></td><td class="help">' . __('Link text, e.g. "Still Alive by Jonathan Coulton"') . '</td></tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" class="button" name="insertonlybutton" value="' . attribute_escape(__('Insert into Post')) . '" />
			</td>
		</tr>
	</tbody></table>
';
}

function type_form_video() {
	return '
	<table class="describe"><tbody>
		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="insertonly[href]">' . __('Video URL') . '</label></span>
				<span class="alignright"><abbr title="required" class="required">*</abbr></span>
			</th>
			<td class="field"><input id="insertonly[href]" name="insertonly[href]" value="" type="text" aria-required="true"></td>
		</tr>
		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="insertonly[title]">' . __('Title') . '</label></span>
				<span class="alignright"><abbr title="required" class="required">*</abbr></span>
			</th>
			<td class="field"><input id="insertonly[title]" name="insertonly[title]" value="" type="text" aria-required="true"></td>
		</tr>
		<tr><td></td><td class="help">' . __('Link text, e.g. "Lucy on YouTube"') . '</td></tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" class="button" name="insertonlybutton" value="' . attribute_escape(__('Insert into Post')) . '" />
			</td>
		</tr>
	</tbody></table>
';
}

function type_form_file() {
	return '
	<table class="describe"><tbody>
		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="insertonly[href]">' . __('URL') . '</label></span>
				<span class="alignright"><abbr title="required" class="required">*</abbr></span>
			</th>
			<td class="field"><input id="insertonly[href]" name="insertonly[href]" value="" type="text" aria-required="true"></td>
		</tr>
		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="insertonly[title]">' . __('Title') . '</label></span>
				<span class="alignright"><abbr title="required" class="required">*</abbr></span>
			</th>
			<td class="field"><input id="insertonly[title]" name="insertonly[title]" value="" type="text" aria-required="true"></td>
		</tr>
		<tr><td></td><td class="help">' . __('Link text, e.g. "Ransom Demands (PDF)"') . '</td></tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" class="button" name="insertonlybutton" value="' . attribute_escape(__('Insert into Post')) . '" />
			</td>
		</tr>
	</tbody></table>
';
}

// support a GET parameter for disabling the flash uploader
function media_upload_use_flash($flash) {
        if ( array_key_exists('flash', $_REQUEST) )
                $flash = !empty($_REQUEST['flash']);
        return $flash;
}

add_filter('flash_uploader', 'media_upload_use_flash');

function media_upload_flash_bypass() {
        echo '<p class="upload-flash-bypass">';
        printf( __('You are using the Flash uploader.  Problems?  Try the <a href="%s">Browser uploader</a> instead.'), clean_url(add_query_arg('flash', 0)) );
        echo '</p>';
}

add_action('post-flash-upload-ui', 'media_upload_flash_bypass');

function media_upload_html_bypass() {
        echo '<p class="upload-html-bypass">';
        if ( array_key_exists('flash', $_REQUEST) )
                // the user manually selected the browser uploader, so let them switch back to Flash
                printf( __('You are using the Browser uploader.  Try the <a href="%s">Flash uploader</a> instead.'), clean_url(add_query_arg('flash', 1)) );
        else
                // the user probably doesn't have Flash
                printf( __('You are using the Browser uploader.') );

        echo '</p>';
}

add_action('post-flash-upload-ui', 'media_upload_flash_bypass');
add_action('post-html-upload-ui', 'media_upload_html_bypass');

// make sure the GET parameter sticks when we submit a form
function media_upload_bypass_url($url) {
        if ( array_key_exists('flash', $_REQUEST) )
                $url = add_query_arg('flash', intval($_REQUEST['flash']));
        return $url;
}

add_filter('media_upload_form_url', 'media_upload_bypass_url');



add_filter('async_upload_image', 'get_media_item', 10, 2);
add_filter('async_upload_audio', 'get_media_item', 10, 2);
add_filter('async_upload_video', 'get_media_item', 10, 2);
add_filter('async_upload_file', 'get_media_item', 10, 2);

add_action('media_upload_image', 'media_upload_image');
add_action('media_upload_audio', 'media_upload_audio');
add_action('media_upload_video', 'media_upload_video');
add_action('media_upload_file', 'media_upload_file');

add_filter('media_upload_gallery', 'media_upload_gallery');

add_filter('media_upload_library', 'media_upload_library');

?>
