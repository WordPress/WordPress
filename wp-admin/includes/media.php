<?php

function image_upload_tabs() {
	$_default_tabs = array(
		'image_upload_handler' => __('From Computer'), // handler function name => tab text
	);

	return apply_filters('image_upload_tabs', $_default_tabs);
}

function image_upload_form( $action_url, $values = array(), $error = null ) {
	$action_url = attribute_escape( $action_url );
	$image_alt = attribute_escape( @$values['image-alt'] );
	$image_url = attribute_escape( @$values['image-url'] );
	$image_title = attribute_escape( @$values['image-title'] );
	$image_align = @$values['image-url'];

?>
<div id="media-upload-header">
<h3>Add Image</h3>
<ul id="media-upload-tabs">
	<li><?php _e('From Computer'); ?></li>
	<li><?php _e('Media Library'); ?></li>
	<li class="last"><?php _e('Flickr'); ?></li>
</ul>
</div>
<?php if ($error) { ?>
	<div id="media-upload-error">
	<?php echo $error->get_error_message(); ?>
	</div>
<?php } ?>
<form enctype="multipart/form-data" method="post" action="<?php echo attribute_escape($action_url); ?>" id="image-upload" class="media-upload-form">
<p><label for="image-file"><?php _e('Choose image'); ?></label>
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
	<a href="#" onclick="return top.tb_remove();" id="image-cancel" class="button-cancel"><?php _e('Cancel'); ?></a>
</p>
	<input type="hidden" name="parent_post_id" value="<?php echo attribute_escape('parent_post_id'); ?>" />
	<?php wp_nonce_field( 'inlineuploading' ); ?>
</form>
<?php
}

function image_upload_handler() {
	
	if ( !current_user_can('upload_files') ) {
		return new wp_error( 'upload_not_allowed', __('You are not allowed to upload files.') );
	}

	check_admin_referer('inlineuploading');
	
	if ( empty($_POST['image-add']) ) {
		// no button click, we're just displaying the form
		wp_iframe( 'image_upload_form', get_option('siteurl') . '/wp-admin/media-upload.php?type=image' );
	}
	else {
		// Add Image button was clicked
		$id = image_upload_post();
		
		// if the input was invalid, redisplay the form with its current values
		if ( is_wp_error($id) )
			wp_iframe( 'image_upload_form', get_option('siteurl') . '/wp-admin/media-upload.php?type=image', $_POST, $id );
		else {
			image_send_to_editor($id, $_POST['image-alt'], $_POST['image-title'], $_POST['image-align'], $_POST['image-url']);
		}
	}
}

function image_send_to_editor($id, $alt, $title, $align, $url='') {
	
	$img_src = wp_get_attachment_url($id);
	$meta = wp_get_attachment_metadata($id);
	
	$hwstring = '';
	if ( isset($meta['width'], $meta['height']) )
		$hwstring = ' width="'.intval($meta['width']).'" height="'.intval($meta['height']).'"';

	$html = '<img src="'.attribute_escape($img_src).'" alt="'.attribute_escape($alt).'" title="'.attribute_escape($title).'"'.$hwstring.' class="align-'.attribute_escape($align).'" />';

	if ( $url )
		$html = '<a href="'.attribute_escape($url).'">'.$html.'</a>';

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

	wp_redirect( get_option('siteurl') . "/wp-admin/upload.php?style=$style&tab=browse&action=view&ID=$id&post_id=$post_id");
		
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
<?php wp_admin_css(); ?>
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
	$uploading_iframe_src = wp_nonce_url("media-upload.php?type=image&amp;&amp;post_id=$uploading_iframe_ID", 'inlineuploading');
	$uploading_iframe_src = apply_filters('uploading_iframe_src', $uploading_iframe_src);
	$out = <<<EOF
<a href="{$uploading_iframe_src}&TB_iframe=true&height=500&width=460" class="thickbox">
<img src="./images/media-buttons.gif" alt="" />
</a>
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

add_action('media_upload_image', 'image_upload_handler');
add_action('admin_head_image_upload_form', 'media_admin_css');

?>