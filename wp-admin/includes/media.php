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

function get_image_send_to_editor($id, $alt, $title, $align, $url='', $rel = false, $size='medium') {

	$html = get_image_tag($id, $alt, $title, $align, $rel, $size);

	$rel = $rel ? ' rel="attachment wp-att-'.attribute_escape($id).'"' : '';
	if ( $url )
		$html = "<a href='".attribute_escape($url)."'$rel>$html</a>";
	elseif ( $size == 'thumb' || $size == 'medium' )
		$html = '<a href="'.get_attachment_link($id).'"'.$rel.'>'.$html.'</a>';
		
	$html = apply_filters( 'image_send_to_editor', $html, $id, $alt, $title, $align, $url );

	return $html;
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
	exit;
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

function media_buttons() {
	global $post_ID, $temp_ID;
	$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);
	$multimedia_upload_iframe_src = "media-upload.php?type=multimedia&amp;post_id=$uploading_iframe_ID";
	$multimedia_upload_iframe_src = apply_filters('multimedia_upload_iframe_src', $multimedia_upload_iframe_src);
	echo "<a href='$multimedia_upload_iframe_src&amp;TB_iframe=true&amp;height=500&amp;width=640' class='button-secondary thickbox'>" . __('Add media'). '</a>';
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

	// no button click, we're just displaying the form
	if ( empty($_POST) )
		return wp_iframe( 'multimedia_upload_form' );

	check_admin_referer('multimedia-form');

	// Insert multimedia button was clicked
	if ( !empty($_FILES) ) {
		// Upload File button was clicked

		$id = media_handle_upload('async-upload', $_REQUEST['post_id']);

		if ( is_wp_error($id) )
			$errors['upload_error'] = $id;
	}

	if ( !empty($_POST['attachments']) ) foreach ( $_POST['attachments'] as $attachment_id => $attachment ) {
		$post = $_post = get_post($attachment_id, ARRAY_A);
		if ( isset($attachment['post_content']) )
			$post['post_content'] = $attachment['post_content'];
		if ( isset($attachment['post_title']) )
			$post['post_title'] = $attachment['post_title'];
		if ( isset($attachment['post_excerpt']) )
			$post['post_excerpt'] = $attachment['post_excerpt'];

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

	if ( isset($_POST['insert-multimedia']) )
		return media_send_to_editor('[gallery]');

	if ( isset($_POST['send']) ) {
		$send_id = (int) array_shift(array_keys($_POST['send']));
		$attachment = $_POST['attachments'][$send_id];
		$html = apply_filters('media_send_to_editor', get_the_attachment_link($send_id, 0, array(125,125), !empty($attachment['post_content'])), $send_id, $attachment);
		return media_send_to_editor($html);
	}

	wp_iframe( 'multimedia_upload_form', $errors );
}

function get_multimedia_items( $post_id, $errors ) {
	$attachments = get_children("post_parent=$post_id&post_type=attachment&orderby=\"menu_order ASC, ID ASC\"");

	if ( empty($attachments) )
		return '';

	foreach ( $attachments as $id => $attachment ) {
		$output .= "\n<div id='multimedia-item-$id' class='multimedia-item preloaded'><div id='media-upload-error-$id'></div><span class='filename'></span><div class='progress'><div class='bar'></div></div>";
		$output .= get_multimedia_item($id, isset($errors[$id]) ? $errors[$id] : null);
		$output .= "	<div class='progress clickmask'></div>\n</div>";
	}

	return $output;
}

function get_attachment_taxonomies($attachment) {
	if ( is_int( $attachment ) )
		$attachment = get_post($attachment);
	else if ( is_array($attachment) )
		$attachment = (object) $attachment;

	if ( ! is_object($attachment) )
		return array();

	$filename = basename($attachment->guid);

	$objects = array('attachment');

	if ( false !== strpos($filename, '.') )
		$objects[] = 'attachment:' . substr($filename, strrpos($filename, '.') + 1);
	if ( !empty($attachment->post_mime_type) ) {
		$objects[] = 'attachment:' . $attachment->post_mime_type;
		if ( false !== strpos($attachment->post_mime_type, '/') )
			foreach ( explode('/', $attachment->post_mime_type) as $token )
				if ( !empty($token) )
					$objects[] = "attachment:$token";
	}

	$taxonomies = array();
	foreach ( $objects as $object )
		if ( $taxes = get_object_taxonomies($object) )
			$taxonomies = array_merge($taxonomies, $taxes);

	return array_unique($taxonomies);
}

function image_attachment_fields_to_edit($form_fields, $post) {
	if ( substr($post->post_mime_type, 0, 5) == 'image' ) {
		$form_fields['post_title']['required'] = true;
		$form_fields['post_excerpt']['label'] = __('Description');
		$form_fields['post_excerpt']['helps'][] = __('Alternate text, e.g. "The Mona Lisa"');

		$form_fields['post_content']['label'] = __('Long Description');

		$thumb = wp_get_attachment_thumb_url();

		$form_fields['_send']['url'] = array(
			'label' => __('Link URL'),
			'input' => 'html',
			'html'  => '',
			'helps'  => __('If filled, this will override the default link URL.'),
		);
		$form_fields['_send']['align'] = array(
			'label' => __('Alignment'),
			'input' => 'html',
			'html'  => "
				<input type='radio' name='attachments[$post->ID][align]' id='image-align-none-$post->ID' value='none' />
				<label for='image-align-none-$post->ID' class='align image-align-none-label'>" . __('None') . "</label>
				<input type='radio' name='attachments[$post->ID][align]' id='image-align-left-$post->ID' value='left' />
				<label for='image-align-left-$post->ID' class='align image-align-left-label'>" . __('Left') . "</label>
				<input type='radio' name='attachments[$post->ID][align]' id='image-align-center-$post->ID' value='center' />
				<label for='image-align-center-$post->ID' class='align image-align-center-label'>" . __('Center') . "</label>
				<input type='radio' name='attachments[$post->ID][align]' id='image-align-right-$post->ID' value='right' />
				<label for='image-align-right-$post->ID' class='align image-align-right-label'>" . __('Right') . "</label>\n",
		);
		$form_fields['_send']['image-size'] = array(
			'label' => __('Size'),
			'input' => 'html',
			'html'  => "
				" . ( $thumb ? "<input type='radio' name='attachments[$post->ID][image-size]' id='image-size-thumb-$post->ID' value='thumb' />
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
		if ( !empty($attachment['url']) )
			$url = $attachment['url'];
		elseif ( $rel = strlen(trim($post->post_content)) )
			$url = get_attachment_link($attachment_id);
		else
			$url = wp_get_attachment_url($attachment_id);

		if ( isset($attachment['align']) )
			$align = $attachment['align'];
		else
			$align = 'none';

		if ( !empty($attachment['image-size']) )
			$size = $attachment['image-size'];
		else
			$size = 'medium';

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

	$form_fields = array(
		'post_title'   => array(
			'label'      => __('Title'),
			'value'      => $edit_post->post_title,
		),
		'post_excerpt' => array(
			'label'      => __('Description'),
			'value'      => $edit_post->post_excerpt,
		),
		'post_content' => array(
			'label'      => __('Long description'),
			'value'      => $edit_post->post_content,
			'input'      => 'textarea',
			'helps'      => array(__('If filled, the default link URL will be the attachment permalink.')),
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

function get_multimedia_item( $attachment_id, $errors = null, $send = true ) {
	$thumb_url = array_shift(get_attachment_icon_src( $attachment_id ));

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

	$form_fields = get_attachment_fields_to_edit($post, $errors);

	$class = empty($errors) ? 'startclosed' : 'startopen';
	$item = "
	<a class='toggle describe-toggle-on' href='#'>$toggle_on</a>
	<a class='toggle describe-toggle-off' href='#'>$toggle_off</a>
	<span class='filename new'>$filename</span>
	<table class='slidetoggle describe $class'><tbody>
		<tr>
			<td class='A1B1' rowspan='4' colspan='2'><img class='thumbnail' src='$thumb_url' alt='' /></td>
			<td>$filename</td>
		</tr>
		<tr><td>$post->post_mime_type</td></tr>
		<tr><td>" . mysql2date($post->post_date, get_option('time_format')) . "</td></tr>
		<tr><td>" . apply_filters('multimedia_meta', '', $post) . "</tr></td>\n";

	$defaults = array(
		'input'      => 'text',
		'required'   => false,
		'value'      => '',
		'extra_rows' => array(),
	);

	$delete_href = wp_nonce_url("post.php?action=delete-post&amp;post=$attachment_id", 'delete-post_' . $attachment_id);
	$delete = __('Delete');
	$save = "<input type='submit' value='" . wp_specialchars(__('Save'), 1) . "' />";
	$send = "<input type='submit' value='" . wp_specialchars(__('Send to Editor'), 1) . "' id='send[$attachment_id]' name='send[$attachment_id]' />";

	if ( empty($form_fields['save']) && empty($form_fields['_send']) ) {
		$form_fields['save'] = array('tr' => "\t\t<tr class='submit'><td colspan='2' class='del'><a id='del[$attachment_id]' class='delete button' href='$delete_href'>$delete</a></td><td class='savesend'>$save$send</td></tr>\n");
	} elseif ( empty($form_fields['save']) ) {
		$form_fields['save'] = array('tr' => "\t\t<tr class='submit'><td></td><td></td><td class='savesend'>$save</td></tr>\n");
		foreach ( $form_fields['_send'] as $id => $field )
			$form_fields[$id] = $field;
		$form_fields['send'] = array('tr' => "\t\t<tr class='submit'><td colspan='2' class='del'><a id='del[$attachment_id]' class='delete button' href='$delete_href'>$delete</a></td><td class='savesend'>$send</td>");
	}

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

		$required = $field['required'] ? '<abbr title="required">*</abbr>' : '';
		$item .= "\t\t<tr class='$id'>\n\t\t\t<td class='label'><label for='$name'>{$field['label']}</label></td>\n\t\t\t<td class='required'>$required</td>\n\t\t\t<td class='field'>";
		if ( !empty($field[$field['input']]) )
			$item .= $field[$field['input']];
		elseif ( $field['input'] == 'textarea' ) {
			$item .= "<textarea type='text' id='$name' name='$name'>" . wp_specialchars($field['value'], 1) . "</textarea>";
		} else {
			$item .= "<input type='text' id='$name' name='$name' value='" . wp_specialchars($field['value'], 1) . "' />";
		}
		$item .= "</td>\n\t\t</tr>\n";

		$extra_rows = array();

		if ( !empty($field['errors']) )
			foreach ( array_unique((array) $field['errors']) as $error )
				$extra_rows['error'][] = $error;

		if ( !empty($field['helps']) )
			foreach ( array_unique((array) $field['helps']) as $help )
				$extra_rows['help'][] = $help;

		if ( !empty($field['extra_rows']) )
			foreach ( $field['extra_rows'] as $class => $rows )
				foreach ( (array) $rows as $html )
					$extra_rows[$class][] = $html;

		foreach ( $extra_rows as $class => $rows )
			foreach ( $rows as $html )
				$item .= "\t\t<tr><td colspan='2'></td><td class='$class'>$html</td></tr>\n";
	}

	if ( !empty($form_fields['_final']) )
		$item .= "\t\t<tr class='final'><td colspan='3'>{$form_fields['_final']}</td></tr>\n";
	$item .= "\t</table>\n";

	foreach ( $hidden_fields as $name => $value )
		$item .= "\t<input type='hidden' name='$name' id='$name' value='" . wp_specialchars($value, 1) . "' />\n";

	return $item;
}

function multimedia_upload_form( $errors = null ) {
	$flash_action_url = get_option('siteurl') . '/wp-admin/async-upload.php?type=multimedia';
	$form_action_url = get_option('siteurl') . '/wp-admin/media-upload.php?type=multimedia';

	$post_id = intval($_REQUEST['post_id']);

?>
<div id="media-upload-header">
<h3><?php _e('Add Media'); ?></h3>
<?php the_image_upload_tabs(); ?>
</div>

<div id="media-upload-error">
<?php if (isset($errors['upload_error']) && is_wp_error($errors['upload_error'])) { ?>
	<?php echo $errors['upload_error']->get_error_message(); ?>
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
			swfupload_loaded_handler : uploadLoaded,
			file_dialog_start_handler : fileDialogStart,
			file_queued_handler : fileQueued,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,

			debug: false,
		});
	$("#flash-browse-button").bind( "click", function(){swfu.selectFiles();});
	var preloaded = $(".multimedia-item.preloaded");
	if ( preloaded.length > 0 ) {
		jQuery('#insert-multimedia').attr('disabled', '');
		preloaded.each(function(){uploadSuccess({id:this.id.replace(/[^0-9]/g, '')},'');});
	}
});
//-->
</script>

<form enctype="multipart/form-data" method="post" action="<?php echo attribute_escape($form_action_url); ?>" class="media-upload-form">

<div id="flash-upload-ui">
	<p><input id="flash-browse-button" type="button" value="<?php _e('Choose files to upload'); ?>" class="button" /></p>
	<p><?php _e('As each upload completes, you can add titles and descriptions below.'); ?></p>
</div>

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

<?php echo get_multimedia_items($post_id, $errors); ?>

</div>

<p class="submit">
	<input type="submit" class="submit insert-gallery" name="insert-multimedia" value="<?php _e('Insert gallery into post'); ?>" />
</p>

<?php wp_nonce_field('multimedia-form'); ?>

</form>

<?php
}

add_action('admin_head_multimedia_upload_form', 'media_admin_css');
add_filter('async_upload_multimedia', 'get_multimedia_item', 10, 2);
add_filter('media_upload_multimedia', 'multimedia_upload_handler');


// Any 'attachment' taxonomy will be included in the description input form for the multi uploader
// Example:
/*
register_taxonomy(
	'image_people',
	'attachment:image',
	array(
		'label' => __('People'),
		'template' => __('People: %l'),
		'helps' => __('Left to right, top to bottom.'),
		'sort' => true,
		'args' => array(
			'orderby' => 'term_order'
		)
	)
);
*/
/*
register_taxonomy('movie_director', 'attachment:video', array('label'=>__('Directors'), 'template'=>__('Directed by %l.')));
register_taxonomy('movie_producer', 'attachment:video', array('label'=>__('Producers'), 'template'=>__('Produced by %l.')));
register_taxonomy('movie_screenwriter', 'attachment:video', array('label'=>__('Screenwriter'), 'template'=>__('Screenplay by %l.')));
register_taxonomy('movie_actor', 'attachment:video', array('label'=>__('Cast'), 'template'=>array(__('Cast: %l.')));
register_taxonomy('movie_crew', 'attachment:video', array('label'=>__('Crew'), 'template'=>array(__('Crew: %l.')));
*/

?>
