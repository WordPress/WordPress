<?php

function image_upload_tabs() {
	$_default_tabs = array(
		'image_upload_handler' => __('From Computer'), // handler function name => tab text
	);

	return apply_filters('image_upload_tabs', $_default_tabs);
}

function the_image_upload_tabs() {
	$tabs = image_upload_tabs();

	if ( !empty($tabs) ) {
		echo "<ul id='media-upload-tabs'>\n";
		if ( isset($_GET['tab']) && array_key_exists($_GET['tab'], $tabs) )
			$current = $_GET['tab'];
		else
			$current = array_shift(array_keys($tabs));
		foreach ( $tabs as $callback => $text ) {
			if ( ++$i == count($tabs) )
				$class = ' class="last"';
			if ( $callback == $current )
				$disabled = ' disabled="disabled"';
			else
				$disabled = '';
			$href = add_query_arg('tab', $callback);
			if ( $callback == $current )
				$link = $text;
			else
				$link = "<a href='$href'>$text</a>";
			echo "\t<li$class>$link</li>\n";
		}
		echo "</ul>\n";
	}
}

function image_upload_callback() {
	$tabs = image_upload_tabs();
	if ( isset($_GET['tab']) && array_key_exists($_GET['tab'], $tabs) && is_callable($_GET['tab']) )
		return $_GET['tab']();
	elseif ( is_callable($first = array_shift(array_keys($tabs))) )
		return $first();
	else
		return image_upload_handler();
}

add_action('media_upload_image', 'image_upload_callback');

function image_upload_form( $action_url, $values = array(), $error = null ) {
	$action_url = attribute_escape( $action_url );
	$image_alt = attribute_escape( @$values['image-alt'] );
	$image_url = attribute_escape( @$values['image-url'] );
	$image_title = attribute_escape( @$values['image-title'] );
	$image_align = @$values['image-url'];
	$post_id = $_GET['post_id'];

?>
<div id="media-upload-header">
<h3><?php _e('Add Image') ?></h3>
<?php the_image_upload_tabs(); ?>
</div>
<div id="media-upload-error">
<?php if ($error) {
	echo $error->get_error_message();
} ?>
</div>
<script type="text/javascript">
<!--

jQuery(document).ready(function(){
	var swfu = new SWFUpload({
			upload_url : "<?php echo get_option('siteurl').'/wp-admin/async-upload.php'; ?>",
			flash_url : "<?php echo get_option('siteurl').'/wp-includes/js/swfupload/swfupload_f9.swf'; ?>",
			file_post_name: "async-upload",
			swfupload_element_id : "flash-upload-ui", // id of the element displayed when swfupload is available
			degraded_element_id : "html-upload-ui",   // when swfupload is unavailable
			//file_types : "*.jpg;*.gif;*.png",
			file_size_limit : "<?php echo wp_max_upload_size(); ?> B",
			post_params : {
				"post_id" : "<?php echo $post_id; ?>",
				"auth_cookie" : "<?php echo $_COOKIE[AUTH_COOKIE]; ?>",
				"type" : "image",
			},
			swfupload_loaded_handler : uploadLoadedImage,
			upload_progress_handler : uploadProgressImage,
			upload_success_handler : uploadSuccessImage,
			upload_error_handler: uploadError,
			file_queued_handler : fileQueuedImage,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,

			custom_settings : {
				progressTarget : "flash-upload-ui",
				cancelButtonId : "btnCancel2"
			},

			debug: false,

		});

	document.getElementById("flash-browse-button").onclick = function () { swfu.selectFile(); };
});
//-->
</script>
<form enctype="multipart/form-data" method="post" action="<?php echo attribute_escape($action_url); ?>" id="image-upload" class="media-upload-form">
<p id="flash-upload-ui">
	<label for="flash-browse-button"><?php _e('Choose image'); ?></label>
	<input id="flash-browse-button" type="button" value="<?php _e('Browse'); ?>" />
	<label for="image-file" class="form-help"><?php _e('Only PNG, JPG, GIF'); ?></label></p>
<p id="html-upload-ui"><label for="image-file"><?php _e('Choose image'); ?></label>
	<input type="file" name="image-file" id="image-file" />
	<label for="image-file" class="form-help"><?php _e('Only PNG, JPG, GIF'); ?></label></p>
<p><label for="image-alt" class="required"><?php _e('&lt;alt&gt; (required)'); ?></label>
	<input type="text" name="image-alt" id="image-alt" />
	<label for="image-alt" class="form-help"><?php _e('e.g., The Mona Lisa'); ?></label></p>
<p><label for="image-url"><?php _e('URL'); ?></label>
	<input type="text" name="image-url" id="image-url" />
	<label for="image-url" class="form-help"><?php _e('e.g., http://www.wordpress.org/'); ?></label></p>
<p><label for="image-title"><?php _e('&lt;title&gt;'); ?></label>
	<input type="text" name="image-title" id="image-title" />
	<label for="image-url" class="form-help"><?php _e('e.g., The Mona Lisa, one of many paintings in the Louvre'); ?></label></p>
<fieldset id="image-align">
	<legend><?php _e('Alignment'); ?></legend>
	<input type="radio" name="image-align" id="image-align-none" value="none" <?php if ($image_align == 'none' || !$image_align) echo ' checked="checked"'; ?>/>
	<label for="image-align-none" id="image-align-none-label"><?php _e('None'); ?></label>
	<input type="radio" name="image-align" id="image-align-left" value="left" <?php if ($image_align == 'left') echo ' checked="checked"'; ?>/>
	<label for="image-align-left" id="image-align-left-label"><?php _e('Left'); ?></label>
	<input type="radio" name="image-align" id="image-align-center" value="center"  <?php if ($image_align == 'center') echo ' checked="checked"'; ?>/>
	<label for="image-align-center" id="image-align-center-label"><?php _e('Center'); ?></label>
	<input type="radio" name="image-align" id="image-align-right" value="right"  <?php if ($image_align == 'right') echo ' checked="checked"'; ?>/>
	<label for="image-align-right" id="image-align-right-label"><?php _e('Right'); ?></label>
</fieldset>
<p>
	<button name="image-add" id="image-add" class="button-ok" value="1"><?php _e('Add Image'); ?></button>
	<a href="#" onClick="return top.tb_remove();" id="image-cancel" class="button-cancel"><?php _e('Cancel'); ?></a>
</p>
	<input type="hidden" name="post_id" value="<?php echo attribute_escape($post_id); ?>" />
	<?php wp_nonce_field( 'inlineuploading' ); ?>
</form>
<?php
}

function image_upload_handler() {

	if ( !current_user_can('upload_files') ) {
		return new wp_error( 'upload_not_allowed', __('You are not allowed to upload files.') );
	}

	if ( empty($_POST['image-add']) ) {
		// no button click, we're just displaying the form
		wp_iframe( 'image_upload_form', get_option('siteurl') . '/wp-admin/media-upload.php?type=image' );
	}
	else {
		// Add Image button was clicked
		check_admin_referer('inlineuploading');

		// if the async flash uploader was used, the attachment has already been inserted and its ID is passed in post.
		// otherwise this is a regular form post and we still have to handle the upload and create the attachment.
		if ( !empty($_POST['attachment_id']) ) {
			$id = intval($_POST['attachment_id']);
			// store the title and alt into the attachment post
			wp_update_post(array(
				'ID' => $id,
				'post_title' => $_POST['image-title'],
				'post_content' => $_POST['image-alt'],
			));
		}
		else {
			$id = image_upload_post();
		}

		// if the input was invalid, redisplay the form with its current values
		if ( is_wp_error($id) )
			wp_iframe( 'image_upload_form', get_option('siteurl') . '/wp-admin/media-upload.php?type=image', $_POST, $id );
		else {
			image_send_to_editor($id, $_POST['image-alt'], $_POST['image-title'], $_POST['image-align'], $_POST['image-url']);
		}
	}
}

// this returns html to include in the single image upload form when the async flash upload has finished
// i.e. show a thumb of the image, and include the attachment id as a hidden input
function async_image_callback($id) {
	$thumb_url = wp_get_attachment_thumb_url($id);
	if ( empty($thumb_url) )
		$thumb_url = wp_mime_type_icon($id);

	if ($thumb_url) {
		$out = '<p><input type="hidden" name="attachment_id" id="attachment_id" value="'.intval($id).'" />'
			. '<img src="'.wp_get_attachment_thumb_url($id).'" class="pinkynail" /> '
			. basename(wp_get_attachment_url($id)).'</p>';
	}
	else {
		$out = '<p><input type="hidden" name="attachment_id" id="attachment_id" value="'.intval($id).'" />'
			. basename(wp_get_attachment_url($id)).'</p>';
	}

	$post = get_post($id);
	$title = addslashes($post->post_title);
	$alt = addslashes($post->post_content);

	// populate the input fields with post data (which in turn comes from exif/iptc)
	$out .= <<<EOF
<script type="text/javascript">
<!--
jQuery('#image-alt').val('{$alt}').attr('disabled', false);
jQuery('#image-title').val('{$title}').attr('disabled', false);
jQuery('#image-url').attr('disabled', false);
jQuery('#image-add').attr('disabled', false);
-->
</script>
EOF;

	return $out;
}

add_filter('async_upload_image', 'async_image_callback');

// scale down the default size of an image so it's a better fit for the editor and theme
function image_constrain_size_for_editor($width, $height) {
	// pick a reasonable default width for the image
	// $content_width might be set in the theme's functions.php
	if ( !empty($GLOBALS['content_width']) )
		$max_width = $GLOBALS['content_width'];
	else
		$max_width = 500;

	$max_width = apply_filters( 'editor_max_image_width', $max_width );
	$max_height = apply_filters( 'editor_max_image_height', $max_width );
	
	return wp_shrink_dimensions( $width, $height, $max_width, $max_height );
}

function image_send_to_editor($id, $alt, $title, $align, $url='') {

	$img_src = wp_get_attachment_url($id);
	$meta = wp_get_attachment_metadata($id);

	$hwstring = '';
	if ( isset($meta['width'], $meta['height']) ) {
		list( $width, $height ) = image_constrain_size_for_editor( $meta['width'], $meta['height'] );
		$hwstring = ' width="'.intval($width).'" height="'.intval($height).'"';
	}

	$html = '<img src="'.attribute_escape($img_src).'" rel="attachment wp-att-'.attribute_escape($id).'" alt="'.attribute_escape($alt).'" title="'.attribute_escape($title).'"'.$hwstring.' class="align-'.attribute_escape($align).'" />';

	if ( $url )
		$html = '<a href="'.attribute_escape($url).'">'.$html.'</a>';
		
	$html = apply_filters( 'image_send_to_editor', $html, $id, $alt, $title, $align, $url );

	media_send_to_editor($html);
}

function media_send_to_editor($html) {
	?>
<script type="text/javascript">
<!--
top.send_to_editor('<?php echo addslashes($html); ?>');
top.tb_remove();
-->
</script>
	<?php
}

// this handles the file upload POST itself, validating input and inserting the file if it's valid
function image_upload_post() {
	if ( empty($_FILES['image-file']['name']) )
		return new wp_error( 'image_file_required', __('Please choose an image file to upload') );
	if ( empty($_POST['image-alt']) )
		return new wp_error( 'image_alt_required', __('Please enter an &lt;alt&gt; description') );

	$overrides = array('test_form'=>false);
	$file = wp_handle_upload($_FILES['image-file'], $overrides);

	if ( isset($file['error']) )
		return new wp_error( 'upload_error', $file['error'] );

	$url = $file['url'];
	$type = $file['type'];
	$file = $file['file'];

	$post_title = trim($_POST['image-title']);
	$post_content = trim($_POST['image-alt']);
	$post_parent = intval($_POST['parent_post_id']);

	// Construct the attachment array
	$attachment = array(
		'post_title' => $post_title,
		'post_content' => $post_content,
		'post_type' => 'attachment',
		'post_parent' => $post_parent,
		'post_mime_type' => $type,
		'guid' => $url
	);

	// Save the data
	$id = wp_insert_attachment($attachment, $file, $post_parent);
	if ( !is_wp_error($id) )
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );

	return $id;
}

// this handles the file upload POST itself, creating the attachment post
function media_handle_upload($file_id, $post_id, $post_data = array()) {
	$overrides = array('test_form'=>false);
	$file = wp_handle_upload($_FILES[$file_id], $overrides);

	if ( isset($file['error']) )
		return new wp_error( 'upload_error', $file['error'] );

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


// wrap iframe content (produced by $content_func) in a doctype, html head/body etc
// any additional function args will be passed to content_func
function wp_iframe($content_func /* ... */) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php _e('Uploads'); ?> &#8212; WordPress</title>
<?php 
wp_admin_css( 'css/global' );
wp_admin_css();
?>
<script type="text/javascript">
//<![CDATA[
function addLoadEvent(func) {if ( typeof wpOnload!='function'){wpOnload=func;}else{ var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}}
//]]>
</script>
<?php
do_action('admin_print_scripts');
do_action('admin_head');
if ( is_string($content_func) )
	do_action( "admin_head_{$content_func}" );
?>
</head>
<body>
<?php
	$args = func_get_args();
	$args = array_slice($args, 1);
	call_user_func_array($content_func, $args);
?>
</body>
</html>
<?php
}

function media_buttons() { // just a placeholder for now
	global $post_ID, $temp_ID;
	$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);
	$image_upload_iframe_src = wp_nonce_url("media-upload.php?type=image&amp;post_id=$uploading_iframe_ID", 'inlineuploading');
	$image_upload_iframe_src = apply_filters('image_upload_iframe_src', $image_upload_iframe_src);
	$multimedia_upload_iframe_src = wp_nonce_url("media-upload.php?type=multimedia&amp;post_id=$uploading_iframe_ID", 'inlineuploading');
	$multimedia_upload_iframe_src = apply_filters('multimedia_upload_iframe_src', $multimedia_upload_iframe_src);
	$out = <<<EOF

<a href="{$image_upload_iframe_src}&TB_iframe=true&height=500&width=480" class="thickbox"><img src='images/media-button-image.gif' alt='' /></a>
<a href="{$multimedia_upload_iframe_src}&TB_iframe=true&height=500&width=640" class="thickbox"><img src='images/media-button-gallery.gif' alt='' /></a>
<a href="{$image_upload_iframe_src}&TB_iframe=true&height=500&width=640" class="thickbox"><img src='images/media-button-video.gif' alt='' /></a>
<a href="{$image_upload_iframe_src}&TB_iframe=true&height=500&width=640" class="thickbox"><img src='images/media-button-music.gif' alt='' /></a>
<a href="{$image_upload_iframe_src}&TB_iframe=true&height=500&width=640" class="thickbox"><img src='images/media-button-other.gif' alt='' /></a>


EOF;
	echo $out;
}
add_action( 'media_buttons', 'media_buttons' );

function media_buttons_head() {
$siteurl = get_option('siteurl');
echo "<style type='text/css' media='all'>
	@import '{$siteurl}/wp-includes/js/thickbox/thickbox.css?1';
	div#TB_title {
		background-color: #222222;
		color: #cfcfcf;
	}
	div#TB_title a, div#TB_title a:visited {
		color: #cfcfcf;
	}
</style>\n";
}

add_action( 'admin_print_scripts', 'media_buttons_head' );

function media_admin_css() {
	wp_admin_css('css/media');
}

add_action('media_upload_multimedia', 'multimedia_upload_handler');
add_action('admin_head_image_upload_form', 'media_admin_css');

function multimedia_upload_handler() {
	if ( !current_user_can('upload_files') ) {
		return new wp_error( 'upload_not_allowed', __('You are not allowed to upload files.') );
	}

	if ( empty($_POST) ) {
		// no button click, we're just displaying the form
			wp_iframe( 'multimedia_upload_form' );
	} elseif ( empty($_POST['upload-button']) ) {
		// Insert multimedia button was clicked
		check_admin_referer('multimedia-form');

		if ( !empty($_POST['attachments']) ) foreach ( $_POST['attachments'] as $attachment_id => $attachment ) {
			$post = $_post = get_post($attachment_id, ARRAY_A);
			$post['post_content'] = $attachment['post_content'];
			$post['post_title'] = $attachment['post_title'];
			if ( $post != $_post )
				wp_update_post($post);

			if ( $taxonomies = get_object_taxonomies('attachment') ) foreach ( $taxonomies as $t )
				if ( isset($attachment[$t]) )
					wp_set_object_terms($attachment_id, array_map('trim', preg_split('/,+/', $attachment[$t])), $t, false);
		}

		media_send_to_editor('[gallery]');
	} else {
		// Upload File button was clicked

		$id = media_handle_upload('async-upload', $_REQUEST['post_id']);

		wp_iframe( 'multimedia_upload_form' );
	}
}

function get_multimedia_items( $post_id ) {
	$attachments = get_children("post_parent=$post_id&post_type=attachment&orderby=\"menu_order ASC, ID ASC\"");

	if ( empty($attachments) )
		return '';

	foreach ( $attachments as $id => $attachment ) {
		$output .= "\n<div id='multimedia-item-$id' class='multimedia-item preloaded'><div id='media-upload-error-$id'></div><span class='filename'></span><div class='progress'><div class='bar'></div></div>";
		$output .= get_multimedia_item($id);
		$output .= "	<div class='progress clickmask'></div>\n</div>";
	}

	return $output;
}

function get_multimedia_item( $attachment_id ) {
	$thumb_url = wp_get_attachment_thumb_url( $attachment_id );
	if ( empty($thumb_url) )
		$thumb_url = wp_mime_type_icon( $attachment_id );
	if ( empty($thumb_url) && ( $post =& get_post( $attachment_id ) ) && substr($post->post_mime_type, 0, 5) == 'image' )
			$thumb_url = wp_get_attachment_url( $attachment_id );

	$title_label = __('Title');
	$description_label = __('Description');
	$tags_label = __('Tags');

	$toggle_on = __('Describe &raquo;');
	$toggle_off = __('Describe &laquo;');

	$post = get_post($attachment_id);

	$filename = basename($post->guid);
	$title = attribute_escape($post->post_title);
	$description = attribute_escape($post->post_content);
	if ( $_tags = get_the_tags($attachment_id) ) {
		foreach ( $_tags as $tag )
			$tags[] = $tag->name;
		$tags = attribute_escape(join(', ', $tags));
	}

	$delete_href = wp_nonce_url("post.php?action=delete-post&amp;post=$attachment_id", 'delete-post_' . $attachment_id);
	$delete = __('Delete');

	$item = "
	<a class='toggle describe-toggle-on' href='#'>$toggle_on</a>
	<a class='toggle describe-toggle-off' href='#'>$toggle_off</a>
	<span class='filename new'>$filename</span>
	<div class='slidetoggle describe'>
		<img class='thumbnail' src='$thumb_url' alt='' />
		<fieldset>
		<p><label for='attachments[$attachment_id][post_title]'>$title_label</label><input type='text' id='attachments[$attachment_id][post_title]' name='attachments[$attachment_id][post_title]' value='$title' /></p>
		<p><label for='attachments[$attachment_id][post_content]'>$description_label</label><input type='text' id='attachments[$attachment_id][post_content]' name='attachments[$attachment_id][post_content]' value='$description' /></p>
";

	if ( $taxonomies = get_object_taxonomies('attachment') ) foreach ( $taxonomies as $t ) {
		$tax = get_taxonomy($t);
		$t_title = !empty($tax->title) ? $tax->title : $t;
		if ( false === $terms = get_object_term_cache( $attachment_id, $t ) )
			$_terms = wp_get_object_terms($attachment_id, $t);
		if ( $_terms ) {
			foreach ( $_terms as $term )
				$terms[] = $term->name;
			$terms = join(', ', $terms);
		} else
			$terms = '';
		$item .= "<p><label for='attachments[$attachment_id][$t]'>$t_title</label><input type='text' id='attachments[$attachment_id][$t]' name='attachments[$attachment_id][$t]' value='$terms' /></p>\n";
	}

	$item .= "
		</fieldset>
		<p class='delete'><a id='del$attachment_id' class='delete' href='$delete_href'>$delete</a></p>
	</div>
";

	return $item;
}

function multimedia_upload_form( $error = null ) {
	$flash_action_url = get_option('siteurl') . '/wp-admin/async-upload.php?type=multimedia';
	$form_action_url = get_option('siteurl') . '/wp-admin/media-upload.php?type=multimedia';

	$post_id = intval($_REQUEST['post_id']);

?>
<div id="media-upload-header">
<h3><?php _e('Add Images'); ?></h3>
</div>
	<div id="media-upload-error">
<?php if ($error) { ?>
	<?php echo $error->get_error_message(); ?>
<?php } ?>
	</div>
<script type="text/javascript">
<!--
jQuery(function($){
	swfu = new SWFUpload({
			upload_url : "<?php echo attribute_escape( $flash_action_url ); ?>",
			flash_url : "<?php echo get_option('siteurl').'/wp-includes/js/swfupload/swfupload_f9.swf'; ?>",
			file_post_name: "async-upload",
			file_types: "*.*",
			post_params : {
				"post_id" : "<?php echo $post_id; ?>",
				"auth_cookie" : "<?php echo $_COOKIE[AUTH_COOKIE]; ?>",
				"type" : "multimedia"
			},
			swfupload_element_id : "flash-upload-ui", // id of the element displayed when swfupload is available
			degraded_element_id : "html-upload-ui",   // when swfupload is unavailable
			//upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgressMultimedia,
			//upload_error_handler : uploadError,
			upload_success_handler : uploadSuccessMultimedia,
			upload_complete_handler : uploadCompleteMultimedia,
			file_dialog_start_handler : fileDialogStart,
			file_queued_handler : fileQueuedMultimedia,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,

			debug: false,
		});
	$("#flash-browse-button").bind( "click", function(){swfu.selectFiles();});
	$("#insert-multimedia").bind( "click", function(){jQuery(this).parents('form').get(0).submit();});
	$(".multimedia-item.preloaded").each(function(){uploadSuccessMultimedia({id:this.id.replace(/[^0-9]/g, '')},'');jQuery('#insert-multimedia').attr('disabled', '');});
	$("a.delete").bind('click',function(){$.ajax({url:'admin-ajax.php',type:'post',data:{id:this.id.replace(/del/,''),action:'delete-post',_ajax_nonce:this.href.replace(/^.*wpnonce=/,'')}});$(this).parents(".multimedia-item").eq(0).slideToggle(300, function(){$(this).remove();});return false;});
});
//-->
</script>
<p id="flash-upload-ui" style="display:none">
	<input id="flash-browse-button" type="button" value="<?php _e('Choose Files'); ?>" />
	<label for="image-file" class="form-help"><?php _e('Only PNG, JPG, GIF'); ?></label>
</p>

<form enctype="multipart/form-data" method="post" action="<?php echo attribute_escape($form_action_url); ?>" class="media-upload-form">

<div id="html-upload-ui">
	<p><label for="async-upload"><?php _e('Choose image'); ?></label>
	<input type="file" name="async-upload" id="async-upload" />
	<label for="image-file" class="form-help"><?php _e('Only PNG, JPG, GIF'); ?></label>
	</p>
	<p>
	<button id="upload-button" name="upload-button" value="1" class="button-ok"><?php _e('Add Image'); ?></button>
	<a href="#" onClick="return top.tb_remove();" id="image-cancel" class="button-cancel"><?php _e('Cancel'); ?></a>
	</p>
	<input type="hidden" name="post_id" id="post_id" value="<?php echo $post_id; ?>" />
	<br style="clear:both" />
</div>



<div id="multimedia-items">

<?php echo get_multimedia_items($post_id); ?>

</div>

<p class="submit">
	<a href="#" onClick="return top.tb_remove();" id="image-cancel" class="button-cancel"><?php _e('Cancel'); ?></a>
	<input type="button" class="submit" id="insert-multimedia" value="<?php _e('Insert gallery into post'); ?>" disabled="disabled" />
</p>

<?php wp_nonce_field('multimedia-form'); ?>

</form>

<?php
}

add_action('admin_head_multimedia_upload_form', 'media_admin_css');
add_filter('async_upload_multimedia', 'get_multimedia_item');
add_filter('media_upload_multimedia', 'multimedia_upload_handler');

// Any 'attachment' taxonomy will be included in the description input form for the multi uploader
// Example:
//register_taxonomy('attachment_people', 'attachment', array('title' => 'People'));

?>
