<?php
/**
 * WordPress Administration Media API.
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
function media_upload_tabs() {
	$_default_tabs = array(
		'type' => __('From Computer'), // handler action suffix => tab text
		'type_url' => __('From URL'),
		'gallery' => __('Gallery'),
		'library' => __('Media Library')
	);

	return apply_filters('media_upload_tabs', $_default_tabs);
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $tabs
 * @return unknown
 */
function update_gallery_tab($tabs) {
	global $wpdb;

	if ( !isset($_REQUEST['post_id']) ) {
		unset($tabs['gallery']);
		return $tabs;
	}

	if ( intval($_REQUEST['post_id']) )
		$attachments = intval($wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_parent = %d", $_REQUEST['post_id'])));

	if ( empty($attachments) ) {
		unset($tabs['gallery']);
		return $tabs;
	}

	$tabs['gallery'] = sprintf(__('Gallery (%s)'), "<span id='attachments-count'>$attachments</span>");

	return $tabs;
}
add_filter('media_upload_tabs', 'update_gallery_tab');

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 */
function the_media_upload_tabs() {
	global $redir_tab;
	$tabs = media_upload_tabs();

	if ( !empty($tabs) ) {
		echo "<ul id='sidemenu'>\n";
		if ( isset($redir_tab) && array_key_exists($redir_tab, $tabs) )
			$current = $redir_tab;
		elseif ( isset($_GET['tab']) && array_key_exists($_GET['tab'], $tabs) )
			$current = $_GET['tab'];
		else
			$current = apply_filters('media_upload_default_tab', 'type');

		foreach ( $tabs as $callback => $text ) {
			$class = '';
			if ( $current == $callback )
				$class = " class='current'";
			$href = add_query_arg(array('tab'=>$callback, 's'=>false, 'paged'=>false, 'post_mime_type'=>false, 'm'=>false));
			$link = "<a href='" . esc_url($href) . "'$class>$text</a>";
			echo "\t<li id='" . esc_attr("tab-$callback") . "'>$link</li>\n";
		}
		echo "</ul>\n";
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $id
 * @param unknown_type $alt
 * @param unknown_type $title
 * @param unknown_type $align
 * @param unknown_type $url
 * @param unknown_type $rel
 * @param unknown_type $size
 * @return unknown
 */
function get_image_send_to_editor($id, $alt, $title, $align, $url='', $rel = false, $size='medium') {

	$htmlalt = ( empty($alt) ) ? $title : $alt;

	$html = get_image_tag($id, $htmlalt, $title, $align, $size);

	$rel = $rel ? ' rel="attachment wp-att-' . esc_attr($id).'"' : '';

	if ( $url )
		$html = '<a href="' . esc_url($url) . "\"$rel>$html</a>";

	$html = apply_filters( 'image_send_to_editor', $html, $id, $alt, $title, $align, $url, $size );

	return $html;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $html
 * @param unknown_type $id
 * @param unknown_type $alt
 * @param unknown_type $title
 * @param unknown_type $align
 * @param unknown_type $url
 * @param unknown_type $size
 * @return unknown
 */
function image_add_caption( $html, $id, $alt, $title, $align, $url, $size ) {

	if ( empty($alt) || apply_filters( 'disable_captions', '' ) )
		return $html;

	$id = ( 0 < (int) $id ) ? 'attachment_' . $id : '';

	preg_match( '/width="([0-9]+)/', $html, $matches );
	if ( ! isset($matches[1]) )
		return $html;

	$width = $matches[1];

	$html = preg_replace( '/(class=["\'][^\'"]*)align(none|left|right|center)\s?/', '$1', $html );
	if ( empty($align) )
		$align = 'none';

	$alt = ! empty($alt) ? addslashes($alt) : '';

	$shcode = '[caption id="' . $id . '" align="align' . $align
	. '" width="' . $width . '" caption="' . $alt . '"]' . $html . '[/caption]';

	return apply_filters( 'image_add_caption_shortcode', $shcode, $html );
}
add_filter( 'image_send_to_editor', 'image_add_caption', 20, 7 );

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $html
 */
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

/**
 * {@internal Missing Short Description}}
 *
 * This handles the file upload POST itself, creating the attachment post.
 *
 * @since unknown
 *
 * @param unknown_type $file_id
 * @param unknown_type $post_id
 * @param unknown_type $post_data
 * @return unknown
 */
function media_handle_upload($file_id, $post_id, $post_data = array()) {
	$overrides = array('test_form'=>false);

	$time = current_time('mysql');
	if ( $post = get_post($post_id) ) {
		if ( substr( $post->post_date, 0, 4 ) > 0 )
			$time = $post->post_date;
	}

	$name = $_FILES[$file_id]['name'];
	$file = wp_handle_upload($_FILES[$file_id], $overrides, $time);

	if ( isset($file['error']) )
		return new WP_Error( 'upload_error', $file['error'] );

	$name_parts = pathinfo($name);
	$name = trim( substr( $name, 0, -(1 + strlen($name_parts['extension'])) ) );

	$url = $file['url'];
	$type = $file['type'];
	$file = $file['file'];
	$title = $name;
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
	$id = wp_insert_attachment($attachment, $file, $post_id);
	if ( !is_wp_error($id) ) {
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
	}

	return $id;

}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $file_array
 * @param unknown_type $post_id
 * @param unknown_type $desc
 * @param unknown_type $post_data
 * @return unknown
 */
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
	$id = wp_insert_attachment($attachment, $file, $post_id);
	if ( !is_wp_error($id) ) {
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
		return $url;
	}
	return $id;
}

/**
 * {@internal Missing Short Description}}
 *
 * Wrap iframe content (produced by $content_func) in a doctype, html head/body
 * etc any additional function args will be passed to content_func.
 *
 * @since unknown
 *
 * @param unknown_type $content_func
 */
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
wp_enqueue_style( 'ie' );
?>
<script type="text/javascript">
//<![CDATA[
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
var userSettings = {'url':'<?php echo SITECOOKIEPATH; ?>','uid':'<?php if ( ! isset($current_user) ) $current_user = wp_get_current_user(); echo $current_user->ID; ?>','time':'<?php echo time(); ?>'};
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>', pagenow = 'media-upload-popup', adminpage = 'media-upload-popup';
//]]>
</script>
<?php
do_action('admin_enqueue_scripts', 'media-upload-popup');
do_action('admin_print_styles-media-upload-popup');
do_action('admin_print_styles');
do_action('admin_print_scripts-media-upload-popup');
do_action('admin_print_scripts');
do_action('admin_head-media-upload-popup');
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

	do_action('admin_print_footer_scripts');
?>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
</body>
</html>
<?php
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 */
function media_buttons() {
	global $post_ID, $temp_ID;
	$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);
	$context = apply_filters('media_buttons_context', __('Upload/Insert %s'));
	$media_upload_iframe_src = "media-upload.php?post_id=$uploading_iframe_ID";
	$media_title = __('Add Media');
	$image_upload_iframe_src = apply_filters('image_upload_iframe_src', "$media_upload_iframe_src&amp;type=image");
	$image_title = __('Add an Image');
	$video_upload_iframe_src = apply_filters('video_upload_iframe_src', "$media_upload_iframe_src&amp;type=video");
	$video_title = __('Add Video');
	$audio_upload_iframe_src = apply_filters('audio_upload_iframe_src', "$media_upload_iframe_src&amp;type=audio");
	$audio_title = __('Add Audio');
	$out = <<<EOF

	<a href="{$image_upload_iframe_src}&amp;TB_iframe=true" id="add_image" class="thickbox" title='$image_title' onclick="return false;"><img src='images/media-button-image.gif' alt='$image_title' /></a>
	<a href="{$video_upload_iframe_src}&amp;TB_iframe=true" id="add_video" class="thickbox" title='$video_title' onclick="return false;"><img src='images/media-button-video.gif' alt='$video_title' /></a>
	<a href="{$audio_upload_iframe_src}&amp;TB_iframe=true" id="add_audio" class="thickbox" title='$audio_title' onclick="return false;"><img src='images/media-button-music.gif' alt='$audio_title' /></a>
	<a href="{$media_upload_iframe_src}&amp;TB_iframe=true" id="add_media" class="thickbox" title='$media_title' onclick="return false;"><img src='images/media-button-other.gif' alt='$media_title' /></a>

EOF;
	printf($context, $out);
}
add_action( 'media_buttons', 'media_buttons' );

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function media_upload_form_handler() {
	check_admin_referer('media-form');

	$errors = null;

	if ( isset($_POST['send']) ) {
		$keys = array_keys($_POST['send']);
		$send_id = (int) array_shift($keys);
	}

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

		if ( isset($send_id) && $attachment_id == $send_id ) {
			if ( isset($attachment['post_parent']) )
				$post['post_parent'] = $attachment['post_parent'];
		}

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

	if ( isset($_POST['insert-gallery']) || isset($_POST['update-gallery']) ) { ?>
		<script type="text/javascript">
		/* <![CDATA[ */
		var win = window.dialogArguments || opener || parent || top;
		win.tb_remove();
		/* ]]> */
		</script>
		<?php
		exit;
	}

	if ( isset($send_id) ) {
		$attachment = stripslashes_deep( $_POST['attachments'][$send_id] );

		$html = $attachment['post_title'];
		if ( !empty($attachment['url']) ) {
			if ( strpos($attachment['url'], 'attachment_id') || false !== strpos($attachment['url'], get_permalink($_POST['post_id'])) )
				$rel = " rel='attachment wp-att-" . esc_attr($send_id)."'";
			$html = "<a href='{$attachment['url']}'$rel>$html</a>";
		}

		$html = apply_filters('media_send_to_editor', $html, $send_id, $attachment);
		return media_send_to_editor($html);
	}

	return $errors;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function media_upload_image() {
	$errors = array();
	$id = 0;

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
		$alt = esc_attr($_POST['insertonly']['alt']);
		if ( isset($_POST['insertonly']['align']) ) {
			$align = esc_attr($_POST['insertonly']['align']);
			$class = " class='align$align'";
		}
		if ( !empty($src) )
			$html = "<img src='$src' alt='$alt'$class />";
		$html = apply_filters('image_send_to_editor_url', $html, $src, $alt, $align);
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

	if ( isset($_GET['tab']) && $_GET['tab'] == 'type_url' )
		return wp_iframe( 'media_upload_type_url_form', 'image', $errors, $id );

	return wp_iframe( 'media_upload_type_form', 'image', $errors, $id );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $file
 * @param unknown_type $post_id
 * @param unknown_type $desc
 * @return unknown
 */
function media_sideload_image($file, $post_id, $desc = null) {
	if (!empty($file) ) {
		$file_array['name'] = basename($file);
		$tmp = download_url($file);
		$file_array['tmp_name'] = $tmp;
		$desc = @$desc;

		if ( is_wp_error($tmp) ) {
			@unlink($file_array['tmp_name']);
			$file_array['tmp_name'] = '';
		}

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

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function media_upload_audio() {
	$errors = array();
	$id = 0;

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
		$title = esc_attr($_POST['insertonly']['title']);
		if ( empty($title) )
			$title = basename($href);
		if ( !empty($title) && !empty($href) )
			$html = "<a href='$href' >$title</a>";
		$html = apply_filters('audio_send_to_editor_url', $html, $href, $title);
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

	if ( isset($_GET['tab']) && $_GET['tab'] == 'type_url' )
		return wp_iframe( 'media_upload_type_url_form', 'audio', $errors, $id );

	return wp_iframe( 'media_upload_type_form', 'audio', $errors, $id );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function media_upload_video() {
	$errors = array();
	$id = 0;

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
		$title = esc_attr($_POST['insertonly']['title']);
		if ( empty($title) )
			$title = basename($href);
		if ( !empty($title) && !empty($href) )
			$html = "<a href='$href' >$title</a>";
		$html = apply_filters('video_send_to_editor_url', $html, $href, $title);
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

	if ( isset($_GET['tab']) && $_GET['tab'] == 'type_url' )
		return wp_iframe( 'media_upload_type_url_form', 'video', $errors, $id );

	return wp_iframe( 'media_upload_type_form', 'video', $errors, $id );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function media_upload_file() {
	$errors = array();
	$id = 0;

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
		$title = esc_attr($_POST['insertonly']['title']);
		if ( empty($title) )
			$title = basename($href);
		if ( !empty($title) && !empty($href) )
			$html = "<a href='$href' >$title</a>";
		$html = apply_filters('file_send_to_editor_url', $html, $href, $title);
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

	if ( isset($_GET['tab']) && $_GET['tab'] == 'type_url' )
		return wp_iframe( 'media_upload_type_url_form', 'file', $errors, $id );

	return wp_iframe( 'media_upload_type_form', 'file', $errors, $id );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function media_upload_gallery() {
	$errors = array();

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

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function media_upload_library() {
	$errors = array();
	if ( !empty($_POST) ) {
		$return = media_upload_form_handler();

		if ( is_string($return) )
			return $return;
		if ( is_array($return) )
			$errors = $return;
	}

	return wp_iframe( 'media_upload_library_form', $errors );
}

/**
 * Retrieve HTML for the image alignment radio buttons with the specified one checked.
 *
 * @since unknown
 *
 * @param unknown_type $post
 * @param unknown_type $checked
 * @return unknown
 */
function image_align_input_fields( $post, $checked = '' ) {

	$alignments = array('none' => __('None'), 'left' => __('Left'), 'center' => __('Center'), 'right' => __('Right'));
	if ( !array_key_exists( (string) $checked, $alignments ) )
		$checked = 'none';

	$out = array();
	foreach ($alignments as $name => $label) {
		$name = esc_attr($name);
		$out[] = "<input type='radio' name='attachments[{$post->ID}][align]' id='image-align-{$name}-{$post->ID}' value='$name'".
		 	( $checked == $name ? " checked='checked'" : "" ) .
			" /><label for='image-align-{$name}-{$post->ID}' class='align image-align-{$name}-label'>" . $label . "</label>";
	}
	return join("\n", $out);
}

/**
 * Retrieve HTML for the size radio buttons with the specified one checked.
 *
 * @since unknown
 *
 * @param unknown_type $post
 * @param unknown_type $checked
 * @return unknown
 */
function image_size_input_fields( $post, $checked = '' ) {

		// get a list of the actual pixel dimensions of each possible intermediate version of this image
		$size_names = array('thumbnail' => __('Thumbnail'), 'medium' => __('Medium'), 'large' => __('Large'), 'full' => __('Full size'));

		foreach ( $size_names as $size => $name ) {
			$downsize = image_downsize($post->ID, $size);

			// is this size selectable?
			$enabled = ( $downsize[3] || 'full' == $size );
			$css_id = "image-size-{$size}-{$post->ID}";
			// if this size is the default but that's not available, don't select it
			if ( $checked && !$enabled )
				$checked = '';
			// if $checked was not specified, default to the first available size that's bigger than a thumbnail
			if ( !$checked && $enabled && 'thumbnail' != $size )
				$checked = $size;

			$html = "<div class='image-size-item'><input type='radio' ".( $enabled ? '' : "disabled='disabled'")."name='attachments[$post->ID][image-size]' id='{$css_id}' value='{$size}'".( $checked == $size ? " checked='checked'" : '') ." />";

			$html .= "<label for='{$css_id}'>" . __($name). "</label>";
			// only show the dimensions if that choice is available
			if ( $enabled )
				$html .= " <label for='{$css_id}' class='help'>" . sprintf( __("(%d&nbsp;&times;&nbsp;%d)"), $downsize[1], $downsize[2] ). "</label>";

			$html .= '</div>';

			$out[] = $html;
		}

		return array(
			'label' => __('Size'),
			'input' => 'html',
			'html'  => join("\n", $out),
		);
}

/**
 * Retrieve HTML for the Link URL buttons with the default link type as specified.
 *
 * @since unknown
 *
 * @param unknown_type $post
 * @param unknown_type $url_type
 * @return unknown
 */
function image_link_input_fields($post, $url_type='') {

	$file = wp_get_attachment_url($post->ID);
	$link = get_attachment_link($post->ID);

	$url = '';
	if ( $url_type == 'file' )
		$url = $file;
	elseif ( $url_type == 'post' )
		$url = $link;

	return "<input type='text' class='urlfield' name='attachments[$post->ID][url]' value='" . esc_attr($url) . "' /><br />
				<button type='button' class='button urlnone' title=''>" . __('None') . "</button>
				<button type='button' class='button urlfile' title='" . esc_attr($file) . "'>" . __('File URL') . "</button>
				<button type='button' class='button urlpost' title='" . esc_attr($link) . "'>" . __('Post URL') . "</button>
";
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $form_fields
 * @param unknown_type $post
 * @return unknown
 */
function image_attachment_fields_to_edit($form_fields, $post) {
	if ( substr($post->post_mime_type, 0, 5) == 'image' ) {
		$form_fields['post_title']['required'] = true;
		$file = wp_get_attachment_url($post->ID);

		$form_fields['image_url']['value'] = $file;

		$form_fields['post_excerpt']['label'] = __('Caption');
		$form_fields['post_excerpt']['helps'][] = __('Also used as alternate text for the image');

		$form_fields['post_content']['label'] = __('Description');

		$form_fields['align'] = array(
			'label' => __('Alignment'),
			'input' => 'html',
			'html'  => image_align_input_fields($post, get_option('image_default_align')),
		);

		$form_fields['image-size'] = image_size_input_fields($post, get_option('image_default_size'));
	}
	return $form_fields;
}

add_filter('attachment_fields_to_edit', 'image_attachment_fields_to_edit', 10, 2);

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $form_fields
 * @param unknown_type $post
 * @return unknown
 */
function media_single_attachment_fields_to_edit( $form_fields, $post ) {
	unset($form_fields['url'], $form_fields['align'], $form_fields['image-size']);
	return $form_fields;
}

function media_post_single_attachment_fields_to_edit( $form_fields, $post ) {
	unset($form_fields['image_url']);
	return $form_fields;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $post
 * @param unknown_type $attachment
 * @return unknown
 */
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

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $html
 * @param unknown_type $attachment_id
 * @param unknown_type $attachment
 * @return unknown
 */
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

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $post
 * @param unknown_type $errors
 * @return unknown
 */
function get_attachment_fields_to_edit($post, $errors = null) {
	if ( is_int($post) )
		$post =& get_post($post);
	if ( is_array($post) )
		$post = (object) $post;

	$image_url = wp_get_attachment_url($post->ID);

	$edit_post = sanitize_post($post, 'edit');

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
			'html'       => image_link_input_fields($post, get_option('image_default_link_type')),
			'helps'      => __('Enter a link URL or click above for presets.'),
		),
		'menu_order'   => array(
			'label'      => __('Order'),
			'value'      => $edit_post->menu_order
		),
		'image_url'	=> array(
			'label'      => __('File URL'),
			'input'      => 'html',
			'html'       => "<input type='text' class='urlfield' readonly='readonly' name='attachments[$post->ID][url]' value='" . esc_attr($image_url) . "' /><br />",
			'value'      => isset($edit_post->post_url) ? $edit_post->post_url : '',
			'helps'      => __('Location of the uploaded file.'),
		)
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

/**
 * Retrieve HTML for media items of post gallery.
 *
 * The HTML markup retrieved will be created for the progress of SWF Upload
 * component. Will also create link for showing and hiding the form to modify
 * the image attachment.
 *
 * @since unknown
 *
 * @param int $post_id Optional. Post ID.
 * @param array $errors Errors for attachment, if any.
 * @return string
 */
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

	$output = '';
	foreach ( (array) $attachments as $id => $attachment ) {
		if ( $attachment->post_status == 'trash' )
			continue;
		if ( $item = get_media_item( $id, array( 'errors' => isset($errors[$id]) ? $errors[$id] : null) ) )
			$output .= "\n<div id='media-item-$id' class='media-item child-of-$attachment->post_parent preloaded'><div class='progress'><div class='bar'></div></div><div id='media-upload-error-$id'></div><div class='filename'></div>$item\n</div>";
	}

	return $output;
}

/**
 * Retrieve HTML form for modifying the image attachment.
 *
 * @since unknown
 *
 * @param int $attachment_id Attachment ID for modification.
 * @param string|array $args Optional. Override defaults.
 * @return string HTML form for attachment.
 */
function get_media_item( $attachment_id, $args = null ) {
	global $redir_tab;

	$default_args = array( 'errors' => null, 'send' => true, 'delete' => true, 'toggle' => true, 'show_title' => true );
	$args = wp_parse_args( $args, $default_args );
	extract( $args, EXTR_SKIP );

	global $post_mime_types;
	if ( ( $attachment_id = intval($attachment_id) ) && $thumb_url = get_attachment_icon_src( $attachment_id ) )
		$thumb_url = $thumb_url[0];
	else
		return false;

	$toggle_on = __('Show');
	$toggle_off = __('Hide');

	$post = get_post($attachment_id);

	$filename = basename($post->guid);
	$title = esc_attr($post->post_title);

	if ( $_tags = get_the_tags($attachment_id) ) {
		foreach ( $_tags as $tag )
			$tags[] = $tag->name;
		$tags = esc_attr(join(', ', $tags));
	}

	$type = '';
	if ( isset($post_mime_types) ) {
		$keys = array_keys(wp_match_mime_types(array_keys($post_mime_types), $post->post_mime_type));
		$type = array_shift($keys);
		$type = "<input type='hidden' id='type-of-$attachment_id' value='" . esc_attr( $type ) . "' />";
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
	$display_title = $show_title ? "<div class='filename new'>" . wp_html_excerpt($display_title, 60) . "</div>" : '';

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
	$display_title
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

	$delete_href = wp_nonce_url("post.php?action=trash&amp;post=$attachment_id", 'trash-post_' . $attachment_id);
	if ( $send )
		$send = "<input type='submit' class='button' name='send[$attachment_id]' value='" . esc_attr__( 'Insert into Post' ) . "' />";
	if ( $delete )
		$delete = current_user_can('delete_post', $attachment_id) ? "<a href=\"$delete_href\" id=\"del[$attachment_id]\" class=\"delete\">" . __('Move to Trash') . "</a>" : "";
	if ( ( $send || $delete ) && !isset($form_fields['buttons']) )
		$form_fields['buttons'] = array('tr' => "\t\t<tr class='submit'><td></td><td class='savesend'>$send $delete</td></tr>\n");

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
			$item .= "<textarea type='text' id='$name' name='$name'" . $aria_required . ">" . esc_html( $field['value'] ) . "</textarea>";
		} else {
			$item .= "<input type='text' id='$name' name='$name' value='" . esc_attr( $field['value'] ) . "'" . $aria_required . "/>";
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
		$item .= "\t<input type='hidden' name='$name' id='$name' value='" . esc_attr( $value ) . "' />\n";

	if ( $post->post_parent < 1 && isset($_REQUEST['post_id']) ) {
		$parent = (int) $_REQUEST['post_id'];
		$parent_name = "attachments[$attachment_id][post_parent]";

		$item .= "\t<input type='hidden' name='$parent_name' id='$parent_name' value='" . $parent . "' />\n";
	}

	return $item;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 */
function media_upload_header() {
	?>
	<script type="text/javascript">post_id = <?php echo intval($_REQUEST['post_id']); ?>;</script>
	<div id="media-upload-header">
	<?php the_media_upload_tabs(); ?>
	</div>
	<?php
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $errors
 */
function media_upload_form( $errors = null ) {
	global $type, $tab;

	$flash_action_url = admin_url('async-upload.php');

	// If Mac and mod_security, no Flash. :(
	$flash = true;
	if ( false !== strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'mac') && apache_mod_loaded('mod_security') )
		$flash = false;

	$flash = apply_filters('flash_uploader', $flash);
	$post_id = isset($_REQUEST['post_id']) ? intval($_REQUEST['post_id']) : 0;

?>
<script type="text/javascript">
//<![CDATA[
var uploaderMode = 0;
jQuery(document).ready(function($){
	uploaderMode = getUserSetting('uploader');
	$('.upload-html-bypass a').click(function(){deleteUserSetting('uploader');uploaderMode=0;swfuploadPreLoad();return false;});
	$('.upload-flash-bypass a').click(function(){setUserSetting('uploader', '1');uploaderMode=1;swfuploadPreLoad();return false;});
});
//]]>
</script>
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
//<![CDATA[
var swfu;
SWFUpload.onload = function() {
	var settings = {
			button_text: '<span class="button"><?php _e('Select Files'); ?></span>',
			button_text_style: '.button { text-align: center; font-weight: bold; font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif; }',
			button_height: "24",
			button_width: "132",
			button_text_top_padding: 2,
			button_image_url: '<?php echo includes_url('images/upload.png'); ?>',
			button_placeholder_id: "flash-browse-button",
			upload_url : "<?php echo esc_attr( $flash_action_url ); ?>",
			flash_url : "<?php echo includes_url('js/swfupload/swfupload.swf'); ?>",
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
			file_dialog_start_handler : fileDialogStart,
			file_queued_handler : fileQueued,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			swfupload_pre_load_handler: swfuploadPreLoad,
			swfupload_load_failed_handler: swfuploadLoadFailed,
			custom_settings : {
				degraded_element_id : "html-upload-ui", // id of the element displayed when swfupload is unavailable
				swfupload_element_id : "flash-upload-ui" // id of the element displayed when swfupload is available
			},
			debug: false
		};
		swfu = new SWFUpload(settings);
};
//]]>
</script>

<div id="flash-upload-ui">
<?php do_action('pre-flash-upload-ui'); ?>

	<div>
	<?php _e( 'Choose files to upload' ); ?>
	<div id="flash-browse-button"></div>
	<span><input id="cancel-upload" disabled="disabled" onclick="cancelUpload()" type="button" value="<?php esc_attr_e('Cancel Upload'); ?>" class="button" /></span>
	</div>
<?php do_action('post-flash-upload-ui'); ?>
	<p class="howto"><?php _e('After a file has been uploaded, you can add titles and descriptions.'); ?></p>
</div>
<?php endif; // $flash ?>

<div id="html-upload-ui">
<?php do_action('pre-html-upload-ui'); ?>
	<p id="async-upload-wrap">
	<label class="screen-reader-text" for="async-upload"><?php _e('Upload'); ?></label>
	<input type="file" name="async-upload" id="async-upload" /> <input type="submit" class="button" name="html-upload" value="<?php esc_attr_e('Upload'); ?>" /> <a href="#" onclick="return top.tb_remove();"><?php _e('Cancel'); ?></a>
	</p>
	<div class="clear"></div>
	<?php if ( is_lighttpd_before_150() ): ?>
	<p><?php _e('If you want to use all capabilities of the uploader, like uploading multiple files at once, please upgrade to lighttpd 1.5.'); ?></p>
	<?php endif;?>
<?php do_action('post-html-upload-ui', $flash); ?>
</div>
<?php do_action('post-upload-ui'); ?>
<?php
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $type
 * @param unknown_type $errors
 * @param unknown_type $id
 */
function media_upload_type_form($type = 'file', $errors = null, $id = null) {
	media_upload_header();

	$post_id = intval($_REQUEST['post_id']);

	$form_action_url = admin_url("media-upload.php?type=$type&tab=type&post_id=$post_id");
	$form_action_url = apply_filters('media_upload_form_url', $form_action_url, $type);
?>

<form enctype="multipart/form-data" method="post" action="<?php echo esc_attr($form_action_url); ?>" class="media-upload-form type-form validate" id="<?php echo $type; ?>-form">
<input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
<?php wp_nonce_field('media-form'); ?>

<h3 class="media-title"><?php _e('Add media files from your computer'); ?></h3>

<?php media_upload_form( $errors ); ?>

<script type="text/javascript">
//<![CDATA[
jQuery(function($){
	var preloaded = $(".media-item.preloaded");
	if ( preloaded.length > 0 ) {
		preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
	}
	updateMediaForm();
});
//]]>
</script>
<div id="media-items">
<?php
if ( $id ) {
	if ( !is_wp_error($id) ) {
		add_filter('attachment_fields_to_edit', 'media_post_single_attachment_fields_to_edit', 10, 2);
		echo get_media_items( $id, $errors );
	} else {
		echo '<div id="media-upload-error">'.esc_html($id->get_error_message()).'</div>';
		exit;
	}
}
?>
</div>
<p class="savebutton ml-submit">
<input type="submit" class="button" name="save" value="<?php esc_attr_e( 'Save all changes' ); ?>" />
</p>
<?php
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $type
 * @param unknown_type $errors
 * @param unknown_type $id
 */
function media_upload_type_url_form($type = 'file', $errors = null, $id = null) {
	media_upload_header();

	$post_id = intval($_REQUEST['post_id']);

	$form_action_url = admin_url("media-upload.php?type=$type&tab=type&post_id=$post_id");
	$form_action_url = apply_filters('media_upload_form_url', $form_action_url, $type);

	$callback = "type_url_form_$type";
?>

<form enctype="multipart/form-data" method="post" action="<?php echo esc_attr($form_action_url); ?>" class="media-upload-form type-form validate" id="<?php echo $type; ?>-form">
<input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
<?php wp_nonce_field('media-form'); ?>

<?php if ( is_callable($callback) ) { ?>

<h3 class="media-title"><?php _e('Add media file from URL'); ?></h3>

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
		document.getElementById('status_img').src = 'images/wpspin_light.gif';
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
<?php echo apply_filters($callback, call_user_func($callback)); ?>
</div>
</div>
</form>
<?php
	} else {
		wp_die( __('Unknown action.') );
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $errors
 */
function media_upload_gallery_form($errors) {
	global $redir_tab, $type;

	$redir_tab = 'gallery';
	media_upload_header();

	$post_id = intval($_REQUEST['post_id']);
	$form_action_url = admin_url("media-upload.php?type=$type&tab=gallery&post_id=$post_id");
	$form_action_url = apply_filters('media_upload_form_url', $form_action_url, $type);
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
<div id="sort-buttons" class="hide-if-no-js">
<span>
<?php _e('All Tabs:'); ?>
<a href="#" id="showall"><?php _e('Show'); ?></a>
<a href="#" id="hideall" style="display:none;"><?php _e('Hide'); ?></a>
</span>
<?php _e('Sort Order:'); ?>
<a href="#" id="asc"><?php _e('Ascending'); ?></a> |
<a href="#" id="desc"><?php _e('Descending'); ?></a> |
<a href="#" id="clear"><?php _e('Clear'); ?></a>
</div>
<form enctype="multipart/form-data" method="post" action="<?php echo esc_attr($form_action_url); ?>" class="media-upload-form validate" id="gallery-form">
<?php wp_nonce_field('media-form'); ?>
<?php //media_upload_form( $errors ); ?>
<table class="widefat" cellspacing="0">
<thead><tr>
<th><?php _e('Media'); ?></th>
<th class="order-head"><?php _e('Order'); ?></th>
</tr></thead>
</table>
<div id="media-items">
<?php add_filter('attachment_fields_to_edit', 'media_post_single_attachment_fields_to_edit', 10, 2); ?>
<?php echo get_media_items($post_id, $errors); ?>
</div>

<p class="ml-submit">
<input type="submit" class="button savebutton" style="display:none;" name="save" id="save-all" value="<?php esc_attr_e( 'Save all changes' ); ?>" />
<input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
<input type="hidden" name="type" value="<?php echo esc_attr( $GLOBALS['type'] ); ?>" />
<input type="hidden" name="tab" value="<?php echo esc_attr( $GLOBALS['tab'] ); ?>" />
</p>

<div id="gallery-settings" style="display:none;">
<div class="title"><?php _e('Gallery Settings'); ?></div>
<table id="basic" class="describe"><tbody>
	<tr>
	<th scope="row" class="label">
		<label>
		<span class="alignleft"><?php _e('Link thumbnails to:'); ?></span>
		</label>
	</th>
	<td class="field">
		<input type="radio" name="linkto" id="linkto-file" value="file" />
		<label for="linkto-file" class="radio"><?php _e('Image File'); ?></label>

		<input type="radio" checked="checked" name="linkto" id="linkto-post" value="post" />
		<label for="linkto-post" class="radio"><?php _e('Attachment Page'); ?></label>
	</td>
	</tr>

	<tr>
	<th scope="row" class="label">
		<label>
		<span class="alignleft"><?php _e('Order images by:'); ?></span>
		</label>
	</th>
	<td class="field">
		<select id="orderby" name="orderby">
			<option value="menu_order" selected="selected"><?php _e('Menu order'); ?></option>
			<option value="title"><?php _e('Title'); ?></option>
			<option value="ID"><?php _e('Date/Time'); ?></option>
			<option value="rand"><?php _e('Random'); ?></option>
		</select>
	</td>
	</tr>

	<tr>
	<th scope="row" class="label">
		<label>
		<span class="alignleft"><?php _e('Order:'); ?></span>
		</label>
	</th>
	<td class="field">
		<input type="radio" checked="checked" name="order" id="order-asc" value="asc" />
		<label for="order-asc" class="radio"><?php _e('Ascending'); ?></label>

		<input type="radio" name="order" id="order-desc" value="desc" />
		<label for="order-desc" class="radio"><?php _e('Descending'); ?></label>
	</td>
	</tr>

	<tr>
	<th scope="row" class="label">
		<label>
		<span class="alignleft"><?php _e('Gallery columns:'); ?></span>
		</label>
	</th>
	<td class="field">
		<select id="columns" name="columns">
			<option value="2"><?php _e('2'); ?></option>
			<option value="3" selected="selected"><?php _e('3'); ?></option>
			<option value="4"><?php _e('4'); ?></option>
			<option value="5"><?php _e('5'); ?></option>
			<option value="6"><?php _e('6'); ?></option>
			<option value="7"><?php _e('7'); ?></option>
			<option value="8"><?php _e('8'); ?></option>
			<option value="9"><?php _e('9'); ?></option>
		</select>
	</td>
	</tr>
</tbody></table>

<p class="ml-submit">
<input type="button" class="button" style="display:none;" onmousedown="wpgallery.update();" name="insert-gallery" id="insert-gallery" value="<?php esc_attr_e( 'Insert gallery' ); ?>" />
<input type="button" class="button" style="display:none;" onmousedown="wpgallery.update();" name="update-gallery" id="update-gallery" value="<?php esc_attr_e( 'Update gallery settings' ); ?>" />
</p>
</div>
</form>
<?php
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $errors
 */
function media_upload_library_form($errors) {
	global $wpdb, $wp_query, $wp_locale, $type, $tab, $post_mime_types;

	media_upload_header();

	$post_id = intval($_REQUEST['post_id']);

	$form_action_url = admin_url("media-upload.php?type=$type&tab=library&post_id=$post_id");
	$form_action_url = apply_filters('media_upload_form_url', $form_action_url, $type);

	$_GET['paged'] = isset( $_GET['paged'] ) ? intval($_GET['paged']) : 0;
	if ( $_GET['paged'] < 1 )
		$_GET['paged'] = 1;
	$start = ( $_GET['paged'] - 1 ) * 10;
	if ( $start < 1 )
		$start = 0;
	add_filter( 'post_limits', $limit_filter = create_function( '$a', "return 'LIMIT $start, 10';" ) );

	list($post_mime_types, $avail_post_mime_types) = wp_edit_attachments_query();

?>

<form id="filter" action="" method="get">
<input type="hidden" name="type" value="<?php echo esc_attr( $type ); ?>" />
<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>" />
<input type="hidden" name="post_id" value="<?php echo (int) $post_id; ?>" />
<input type="hidden" name="post_mime_type" value="<?php echo isset( $_GET['post_mime_type'] ) ? esc_attr( $_GET['post_mime_type'] ) : ''; ?>" />

<p id="media-search" class="search-box">
	<label class="screen-reader-text" for="media-search-input"><?php _e('Search Media');?>:</label>
	<input type="text" id="media-search-input" name="s" value="<?php the_search_query(); ?>" />
	<input type="submit" value="<?php esc_attr_e( 'Search Media' ); ?>" class="button" />
</p>

<ul class="subsubsub">
<?php
$type_links = array();
$_num_posts = (array) wp_count_attachments();
$matches = wp_match_mime_types(array_keys($post_mime_types), array_keys($_num_posts));
foreach ( $matches as $_type => $reals )
	foreach ( $reals as $real )
		if ( isset($num_posts[$_type]) )
			$num_posts[$_type] += $_num_posts[$real];
		else
			$num_posts[$_type] = $_num_posts[$real];
// If available type specified by media button clicked, filter by that type
if ( empty($_GET['post_mime_type']) && !empty($num_posts[$type]) ) {
	$_GET['post_mime_type'] = $type;
	list($post_mime_types, $avail_post_mime_types) = wp_edit_attachments_query();
}
if ( empty($_GET['post_mime_type']) || $_GET['post_mime_type'] == 'all' )
	$class = ' class="current"';
else
	$class = '';
$type_links[] = "<li><a href='" . esc_url(add_query_arg(array('post_mime_type'=>'all', 'paged'=>false, 'm'=>false))) . "'$class>".__('All Types')."</a>";
foreach ( $post_mime_types as $mime_type => $label ) {
	$class = '';

	if ( !wp_match_mime_types($mime_type, $avail_post_mime_types) )
		continue;

	if ( isset($_GET['post_mime_type']) && wp_match_mime_types($mime_type, $_GET['post_mime_type']) )
		$class = ' class="current"';

	$type_links[] = "<li><a href='" . esc_url(add_query_arg(array('post_mime_type'=>$mime_type, 'paged'=>false))) . "'$class>" . sprintf(_n($label[2][0], $label[2][1], $num_posts[$mime_type]), "<span id='$mime_type-counter'>" . number_format_i18n( $num_posts[$mime_type] ) . '</span>') . '</a>';
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
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => ceil($wp_query->found_posts / 10),
	'current' => $_GET['paged']
));

if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft actions">
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

	echo "<option$default value='" . esc_attr( $arc_row->yyear . $arc_row->mmonth ) . "'>";
	echo esc_html( $wp_locale->get_month($arc_row->mmonth) . " $arc_row->yyear" );
	echo "</option>\n";
}
?>
</select>
<?php } ?>

<input type="submit" id="post-query-submit" value="<?php echo esc_attr( __( 'Filter &#187;' ) ); ?>" class="button-secondary" />

</div>

<br class="clear" />
</div>
</form>

<form enctype="multipart/form-data" method="post" action="<?php echo esc_attr($form_action_url); ?>" class="media-upload-form validate" id="library-form">

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
<?php add_filter('attachment_fields_to_edit', 'media_post_single_attachment_fields_to_edit', 10, 2); ?>
<?php echo get_media_items(null, $errors); ?>
</div>
<p class="ml-submit">
<input type="submit" class="button savebutton" name="save" value="<?php esc_attr_e( 'Save all changes' ); ?>" />
<input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
</p>
</form>
<?php
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function type_url_form_image() {

	if ( apply_filters( 'disable_captions', '' ) ) {
		$alt = __('Alternate Text');
		$alt_help = __('Alt text for the image, e.g. &#8220;The Mona Lisa&#8221;');
	} else {
		$alt = __('Image Caption');
		$alt_help = __('Also used as alternate text for the image');
	}

	$default_align = get_option('image_default_align');
	if ( empty($default_align) )
		$default_align = 'none';

	return '
	<table class="describe"><tbody>
		<tr>
			<th valign="top" scope="row" class="label" style="width:120px;">
				<span class="alignleft"><label for="src">' . __('Image URL') . '</label></span>
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
				<input name="align" id="align-none" value="none" onclick="addExtImage.align=\'align\'+this.value" type="radio"' . ($default_align == 'none' ? ' checked="checked"' : '').' />
				<label for="align-none" class="align image-align-none-label">' . __('None') . '</label>
				<input name="align" id="align-left" value="left" onclick="addExtImage.align=\'align\'+this.value" type="radio"' . ($default_align == 'left' ? ' checked="checked"' : '').' />
				<label for="align-left" class="align image-align-left-label">' . __('Left') . '</label>
				<input name="align" id="align-center" value="center" onclick="addExtImage.align=\'align\'+this.value" type="radio"' . ($default_align == 'center' ? ' checked="checked"' : '').' />
				<label for="align-center" class="align image-align-center-label">' . __('Center') . '</label>
				<input name="align" id="align-right" value="right" onclick="addExtImage.align=\'align\'+this.value" type="radio"' . ($default_align == 'right' ? ' checked="checked"' : '').' />
				<label for="align-right" class="align image-align-right-label">' . __('Right') . '</label>
			</td>
		</tr>

		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="url">' . __('Link Image To:') . '</label></span>
			</th>
			<td class="field"><input id="url" name="url" value="" type="text" /><br />

			<button type="button" class="button" value="" onclick="document.forms[0].url.value=null">' . __('None') . '</button>
			<button type="button" class="button" value="" onclick="document.forms[0].url.value=document.forms[0].src.value">' . __('Link to image') . '</button>
			<p class="help">' . __('Enter a link URL or click above for presets.') . '</p></td>
		</tr>

		<tr>
			<td></td>
			<td>
				<input type="button" class="button" id="go_button" style="color:#bbb;" onclick="addExtImage.insert()" value="' . esc_attr__('Insert into Post') . '" />
			</td>
		</tr>
	</tbody></table>
';

}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function type_url_form_audio() {
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
		<tr><td></td><td class="help">' . __('Link text, e.g. &#8220;Still Alive by Jonathan Coulton&#8221;') . '</td></tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" class="button" name="insertonlybutton" value="' . esc_attr__('Insert into Post') . '" />
			</td>
		</tr>
	</tbody></table>
';
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function type_url_form_video() {
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
		<tr><td></td><td class="help">' . __('Link text, e.g. &#8220;Lucy on YouTube&#8221;') . '</td></tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" class="button" name="insertonlybutton" value="' . esc_attr__('Insert into Post') . '" />
			</td>
		</tr>
	</tbody></table>
';
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function type_url_form_file() {
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
		<tr><td></td><td class="help">' . __('Link text, e.g. &#8220;Ransom Demands (PDF)&#8221;') . '</td></tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" class="button" name="insertonlybutton" value="' . esc_attr__('Insert into Post') . '" />
			</td>
		</tr>
	</tbody></table>
';
}

/**
 * {@internal Missing Short Description}}
 *
 * Support a GET parameter for disabling the flash uploader.
 *
 * @since unknown
 *
 * @param unknown_type $flash
 * @return unknown
 */
function media_upload_use_flash($flash) {
	if ( array_key_exists('flash', $_REQUEST) )
		$flash = !empty($_REQUEST['flash']);
	return $flash;
}

add_filter('flash_uploader', 'media_upload_use_flash');

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 */
function media_upload_flash_bypass() {
	echo '<p class="upload-flash-bypass">';
	printf( __('You are using the Flash uploader.  Problems?  Try the <a href="%s">Browser uploader</a> instead.'), esc_url(add_query_arg('flash', 0)) );
	echo '</p>';
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 */
function media_upload_html_bypass($flash = true) {
	echo '<p class="upload-html-bypass">';
	_e('You are using the Browser uploader.');
	if ( $flash ) {
		// the user manually selected the browser uploader, so let them switch back to Flash
		echo ' ';
		printf( __('Try the <a href="%s">Flash uploader</a> instead.'), esc_url(add_query_arg('flash', 1)) );
	}
	echo "</p>\n";
}

add_action('post-flash-upload-ui', 'media_upload_flash_bypass');
add_action('post-html-upload-ui', 'media_upload_html_bypass');

/**
 * {@internal Missing Short Description}}
 *
 * Make sure the GET parameter sticks when we submit a form.
 *
 * @since unknown
 *
 * @param unknown_type $url
 * @return unknown
 */
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
