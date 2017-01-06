<?php
/**
 * WordPress Administration Media API.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Defines the default media upload tabs
 *
 * @since 2.5.0
 *
 * @return array default tabs
 */
function media_upload_tabs() {
	$_default_tabs = array(
		'type' => __('From Computer'), // handler action suffix => tab text
		'type_url' => __('From URL'),
		'gallery' => __('Gallery'),
		'library' => __('Media Library')
	);

	/**
	 * Filter the available tabs in the legacy (pre-3.5.0) media popup.
	 *
	 * @since 2.5.0
	 *
	 * @param array $_default_tabs An array of media tabs.
	 */
	return apply_filters( 'media_upload_tabs', $_default_tabs );
}

/**
 * Adds the gallery tab back to the tabs array if post has image attachments
 *
 * @since 2.5.0
 *
 * @param array $tabs
 * @return array $tabs with gallery if post has image attachment
 */
function update_gallery_tab($tabs) {
	global $wpdb;

	if ( !isset($_REQUEST['post_id']) ) {
		unset($tabs['gallery']);
		return $tabs;
	}

	$post_id = intval($_REQUEST['post_id']);

	if ( $post_id )
		$attachments = intval( $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status != 'trash' AND post_parent = %d", $post_id ) ) );

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
 * @since 2.5.0
 */
function the_media_upload_tabs() {
	global $redir_tab;
	$tabs = media_upload_tabs();
	$default = 'type';

	if ( !empty($tabs) ) {
		echo "<ul id='sidemenu'>\n";
		if ( isset($redir_tab) && array_key_exists($redir_tab, $tabs) ) {
			$current = $redir_tab;
		} elseif ( isset($_GET['tab']) && array_key_exists($_GET['tab'], $tabs) ) {
			$current = $_GET['tab'];
		} else {
			/** This filter is documented in wp-admin/media-upload.php */
			$current = apply_filters( 'media_upload_default_tab', $default );
		}

		foreach ( $tabs as $callback => $text ) {
			$class = '';

			if ( $current == $callback )
				$class = " class='current'";

			$href = add_query_arg(array('tab' => $callback, 's' => false, 'paged' => false, 'post_mime_type' => false, 'm' => false));
			$link = "<a href='" . esc_url($href) . "'$class>$text</a>";
			echo "\t<li id='" . esc_attr("tab-$callback") . "'>$link</li>\n";
		}
		echo "</ul>\n";
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.0
 *
 * @param integer $id image attachment id
 * @param string $caption image caption
 * @param string $alt image alt attribute
 * @param string $title image title attribute
 * @param string $align image css alignment property
 * @param string $url image src url
 * @param string|bool $rel image rel attribute
 * @param string $size image size (thumbnail, medium, large, full or added  with add_image_size() )
 * @return string the html to insert into editor
 */
function get_image_send_to_editor($id, $caption, $title, $align, $url='', $rel = false, $size='medium', $alt = '') {

	$html = get_image_tag($id, $alt, '', $align, $size);

	$rel = $rel ? ' rel="attachment wp-att-' . esc_attr($id).'"' : '';

	if ( $url )
		$html = '<a href="' . esc_attr($url) . "\"$rel>$html</a>";

	/**
	 * Filter the image HTML markup to send to the editor.
	 *
	 * @since 2.5.0
	 *
	 * @param string $html    The image HTML markup to send.
	 * @param int    $id      The attachment id.
	 * @param string $caption The image caption.
	 * @param string $title   The image title.
	 * @param string $align   The image alignment.
	 * @param string $url     The image source URL.
	 * @param string $size    The image size.
	 * @param string $alt     The image alternative, or alt, text.
	 */
	$html = apply_filters( 'image_send_to_editor', $html, $id, $caption, $title, $align, $url, $size, $alt );

	return $html;
}

/**
 * Adds image shortcode with caption to editor
 *
 * @since 2.6.0
 *
 * @param string $html
 * @param integer $id
 * @param string $caption image caption
 * @param string $alt image alt attribute
 * @param string $title image title attribute
 * @param string $align image css alignment property
 * @param string $url image src url
 * @param string $size image size (thumbnail, medium, large, full or added with add_image_size() )
 * @return string
 */
function image_add_caption( $html, $id, $caption, $title, $align, $url, $size, $alt = '' ) {

	/**
	 * Filter the caption text.
	 *
	 * Note: If the caption text is empty, the caption shortcode will not be appended
	 * to the image HTML when inserted into the editor.
	 *
	 * Passing an empty value also prevents the {@see 'image_add_caption_shortcode'}
	 * filter from being evaluated at the end of {@see image_add_caption()}.
	 *
	 * @since 4.1.0
	 *
	 * @param string $caption The original caption text.
	 * @param int    $id      The attachment ID.
	 */
	$caption = apply_filters( 'image_add_caption_text', $caption, $id );

	/**
	 * Filter whether to disable captions.
	 *
	 * Prevents image captions from being appended to image HTML when inserted into the editor.
	 *
	 * @since 2.6.0
	 *
	 * @param bool $bool Whether to disable appending captions. Returning true to the filter
	 *                   will disable captions. Default empty string.
	 */
	if ( empty($caption) || apply_filters( 'disable_captions', '' ) )
		return $html;

	$id = ( 0 < (int) $id ) ? 'attachment_' . $id : '';

	if ( ! preg_match( '/width=["\']([0-9]+)/', $html, $matches ) )
		return $html;

	$width = $matches[1];

	$caption = str_replace( array("\r\n", "\r"), "\n", $caption);
	$caption = preg_replace_callback( '/<[a-zA-Z0-9]+(?: [^<>]+>)*/', '_cleanup_image_add_caption', $caption );

	// Convert any remaining line breaks to <br>.
	$caption = preg_replace( '/[ \n\t]*\n[ \t]*/', '<br />', $caption );

	$html = preg_replace( '/(class=["\'][^\'"]*)align(none|left|right|center)\s?/', '$1', $html );
	if ( empty($align) )
		$align = 'none';

	$shcode = '[caption id="' . $id . '" align="align' . $align	. '" width="' . $width . '"]' . $html . ' ' . $caption . '[/caption]';

	/**
	 * Filter the image HTML markup including the caption shortcode.
	 *
	 * @since 2.6.0
	 *
	 * @param string $shcode The image HTML markup with caption shortcode.
	 * @param string $html   The image HTML markup.
	 */
	return apply_filters( 'image_add_caption_shortcode', $shcode, $html );
}
add_filter( 'image_send_to_editor', 'image_add_caption', 20, 8 );

/**
 * Private preg_replace callback used in image_add_caption()
 *
 * @access private
 * @since 3.4.0
 */
function _cleanup_image_add_caption( $matches ) {
	// Remove any line breaks from inside the tags.
	return preg_replace( '/[\r\n\t]+/', ' ', $matches[0] );
}

/**
 * Adds image html to editor
 *
 * @since 2.5.0
 *
 * @param string $html
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
 * This handles the file upload POST itself, creating the attachment post.
 *
 * @since 2.5.0
 *
 * @param string $file_id Index into the {@link $_FILES} array of the upload
 * @param int $post_id The post ID the media is associated with
 * @param array $post_data allows you to overwrite some of the attachment
 * @param array $overrides allows you to override the {@link wp_handle_upload()} behavior
 * @return int|WP_Error ID of the attachment or a WP_Error object on failure.
 */
function media_handle_upload($file_id, $post_id, $post_data = array(), $overrides = array( 'test_form' => false )) {

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
	$title = sanitize_text_field( $name );
	$content = '';

	if ( preg_match( '#^audio#', $type ) ) {
		$meta = wp_read_audio_metadata( $file );

		if ( ! empty( $meta['title'] ) )
			$title = $meta['title'];

		$content = '';

		if ( ! empty( $title ) ) {

			if ( ! empty( $meta['album'] ) && ! empty( $meta['artist'] ) ) {
				/* translators: 1: audio track title, 2: album title, 3: artist name */
				$content .= sprintf( __( '"%1$s" from %2$s by %3$s.' ), $title, $meta['album'], $meta['artist'] );
			} else if ( ! empty( $meta['album'] ) ) {
				/* translators: 1: audio track title, 2: album title */
				$content .= sprintf( __( '"%1$s" from %2$s.' ), $title, $meta['album'] );
			} else if ( ! empty( $meta['artist'] ) ) {
				/* translators: 1: audio track title, 2: artist name */
				$content .= sprintf( __( '"%1$s" by %2$s.' ), $title, $meta['artist'] );
			} else {
				$content .= sprintf( __( '"%s".' ), $title );
			}

		} else if ( ! empty( $meta['album'] ) ) {

			if ( ! empty( $meta['artist'] ) ) {
				/* translators: 1: audio album title, 2: artist name */
				$content .= sprintf( __( '%1$s by %2$s.' ), $meta['album'], $meta['artist'] );
			} else {
				$content .= $meta['album'] . '.';
			}

		} else if ( ! empty( $meta['artist'] ) ) {

			$content .= $meta['artist'] . '.';

		}

		if ( ! empty( $meta['year'] ) )
			$content .= ' ' . sprintf( __( 'Released: %d.' ), $meta['year'] );

		if ( ! empty( $meta['track_number'] ) ) {
			$track_number = explode( '/', $meta['track_number'] );
			if ( isset( $track_number[1] ) )
				$content .= ' ' . sprintf( __( 'Track %1$s of %2$s.' ), number_format_i18n( $track_number[0] ), number_format_i18n( $track_number[1] ) );
			else
				$content .= ' ' . sprintf( __( 'Track %1$s.' ), number_format_i18n( $track_number[0] ) );
		}

		if ( ! empty( $meta['genre'] ) )
			$content .= ' ' . sprintf( __( 'Genre: %s.' ), $meta['genre'] );

	// Use image exif/iptc data for title and caption defaults if possible.
	} elseif ( 0 === strpos( $type, 'image/' ) && $image_meta = @wp_read_image_metadata( $file ) ) {
		if ( trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) )
			$title = $image_meta['title'];
		if ( trim( $image_meta['caption'] ) )
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

	// This should never be set as it would then overwrite an existing attachment.
	if ( isset( $attachment['ID'] ) )
		unset( $attachment['ID'] );

	// Save the data
	$id = wp_insert_attachment($attachment, $file, $post_id);
	if ( !is_wp_error($id) ) {
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
	}

	return $id;

}

/**
 * This handles a sideloaded file in the same way as an uploaded file is handled by {@link media_handle_upload()}
 *
 * @since 2.6.0
 *
 * @param array $file_array Array similar to a {@link $_FILES} upload array
 * @param int $post_id The post ID the media is associated with
 * @param string $desc Description of the sideloaded file
 * @param array $post_data allows you to overwrite some of the attachment
 * @return int|object The ID of the attachment or a WP_Error on failure
 */
function media_handle_sideload($file_array, $post_id, $desc = null, $post_data = array()) {
	$overrides = array('test_form'=>false);

	$time = current_time( 'mysql' );
	if ( $post = get_post( $post_id ) ) {
		if ( substr( $post->post_date, 0, 4 ) > 0 )
			$time = $post->post_date;
	}

	$file = wp_handle_sideload( $file_array, $overrides, $time );
	if ( isset($file['error']) )
		return new WP_Error( 'upload_error', $file['error'] );

	$url = $file['url'];
	$type = $file['type'];
	$file = $file['file'];
	$title = preg_replace('/\.[^.]+$/', '', basename($file));
	$content = '';

	// Use image exif/iptc data for title and caption defaults if possible.
	if ( $image_meta = @wp_read_image_metadata($file) ) {
		if ( trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) )
			$title = $image_meta['title'];
		if ( trim( $image_meta['caption'] ) )
			$content = $image_meta['caption'];
	}

	if ( isset( $desc ) )
		$title = $desc;

	// Construct the attachment array.
	$attachment = array_merge( array(
		'post_mime_type' => $type,
		'guid' => $url,
		'post_parent' => $post_id,
		'post_title' => $title,
		'post_content' => $content,
	), $post_data );

	// This should never be set as it would then overwrite an existing attachment.
	if ( isset( $attachment['ID'] ) )
		unset( $attachment['ID'] );

	// Save the attachment metadata
	$id = wp_insert_attachment($attachment, $file, $post_id);
	if ( !is_wp_error($id) )
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );

	return $id;
}

/**
 * Adds the iframe to display content for the media upload page
 *
 * @since 2.5.0
 *
 * @param string|callable $content_func
 */
function wp_iframe($content_func /* ... */) {
	_wp_admin_html_begin();
?>
<title><?php bloginfo('name') ?> &rsaquo; <?php _e('Uploads'); ?> &#8212; <?php _e('WordPress'); ?></title>
<?php

wp_enqueue_style( 'colors' );
// Check callback name for 'media'
if ( ( is_array( $content_func ) && ! empty( $content_func[1] ) && 0 === strpos( (string) $content_func[1], 'media' ) )
	|| ( ! is_array( $content_func ) && 0 === strpos( $content_func, 'media' ) ) )
	wp_enqueue_style( 'media' );
wp_enqueue_style( 'ie' );
?>
<script type="text/javascript">
//<![CDATA[
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>', pagenow = 'media-upload-popup', adminpage = 'media-upload-popup',
isRtl = <?php echo (int) is_rtl(); ?>;
//]]>
</script>
<?php
	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_enqueue_scripts', 'media-upload-popup' );

	/**
	 * Fires when admin styles enqueued for the legacy (pre-3.5.0) media upload popup are printed.
	 *
	 * @since 2.9.0
	 */
	do_action( 'admin_print_styles-media-upload-popup' );

	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_print_styles' );

	/**
	 * Fires when admin scripts enqueued for the legacy (pre-3.5.0) media upload popup are printed.
	 *
	 * @since 2.9.0
	 */
	do_action( 'admin_print_scripts-media-upload-popup' );

	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_print_scripts' );

	/**
	 * Fires when scripts enqueued for the admin header for the legacy (pre-3.5.0)
	 * media upload popup are printed.
	 *
	 * @since 2.9.0
	 */
	do_action( 'admin_head-media-upload-popup' );

	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_head' );

if ( is_string( $content_func ) ) {
	/**
	 * Fires in the admin header for each specific form tab in the legacy
	 * (pre-3.5.0) media upload popup.
	 *
	 * The dynamic portion of the hook, `$content_func`, refers to the form
	 * callback for the media upload type. Possible values include
	 * 'media_upload_type_form', 'media_upload_type_url_form', and
	 * 'media_upload_library_form'.
	 *
	 * @since 2.5.0
	 */
	do_action( "admin_head_{$content_func}" );
}
?>
</head>
<body<?php if ( isset($GLOBALS['body_id']) ) echo ' id="' . $GLOBALS['body_id'] . '"'; ?> class="wp-core-ui no-js">
<script type="text/javascript">
document.body.className = document.body.className.replace('no-js', 'js');
</script>
<?php
	$args = func_get_args();
	$args = array_slice($args, 1);
	call_user_func_array($content_func, $args);

	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_print_footer_scripts' );
?>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
</body>
</html>
<?php
}

/**
 * Adds the media button to the editor
 *
 * @since 2.5.0
 *
 * @param string $editor_id
 */
function media_buttons($editor_id = 'content') {
	static $instance = 0;
	$instance++;

	$post = get_post();
	if ( ! $post && ! empty( $GLOBALS['post_ID'] ) )
		$post = $GLOBALS['post_ID'];

	wp_enqueue_media( array(
		'post' => $post
	) );

	$img = '<span class="wp-media-buttons-icon"></span> ';

	$id_attribute = $instance === 1 ? ' id="insert-media-button"' : '';
	printf( '<a href="#"%s class="button insert-media add_media" data-editor="%s" title="%s">%s</a>',
		$id_attribute,
		esc_attr( $editor_id ),
		esc_attr__( 'Add Media' ),
		$img . __( 'Add Media' )
	);
	/**
	 * Filter the legacy (pre-3.5.0) media buttons.
	 *
	 * @since 2.5.0
	 * @deprecated 3.5.0 Use 'media_buttons' action instead.
	 *
	 * @param string $string Media buttons context. Default empty.
	 */
	$legacy_filter = apply_filters( 'media_buttons_context', '' );

	if ( $legacy_filter ) {
		// #WP22559. Close <a> if a plugin started by closing <a> to open their own <a> tag.
		if ( 0 === stripos( trim( $legacy_filter ), '</a>' ) )
			$legacy_filter .= '</a>';
		echo $legacy_filter;
	}
}
add_action( 'media_buttons', 'media_buttons' );

/**
 *
 * @global int $post_ID
 * @param string $type
 * @param int $post_id
 * @param string $tab
 * @return string
 */
function get_upload_iframe_src( $type = null, $post_id = null, $tab = null ) {
	global $post_ID;

	if ( empty( $post_id ) )
		$post_id = $post_ID;

	$upload_iframe_src = add_query_arg( 'post_id', (int) $post_id, admin_url('media-upload.php') );

	if ( $type && 'media' != $type )
		$upload_iframe_src = add_query_arg('type', $type, $upload_iframe_src);

	if ( ! empty( $tab ) )
		$upload_iframe_src = add_query_arg('tab', $tab, $upload_iframe_src);

	/**
	 * Filter the upload iframe source URL for a specific media type.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the type
	 * of media uploaded.
	 *
	 * @since 3.0.0
	 *
	 * @param string $upload_iframe_src The upload iframe source URL by type.
	 */
	$upload_iframe_src = apply_filters( $type . '_upload_iframe_src', $upload_iframe_src );

	return add_query_arg('TB_iframe', true, $upload_iframe_src);
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.0
 *
 * @return mixed void|object WP_Error on failure
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

		if ( !current_user_can( 'edit_post', $attachment_id ) )
			continue;

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

		/**
		 * Filter the attachment fields to be saved.
		 *
		 * @since 2.5.0
		 *
		 * @see wp_get_attachment_metadata()
		 *
		 * @param WP_Post $post       The WP_Post object.
		 * @param array   $attachment An array of attachment metadata.
		 */
		$post = apply_filters( 'attachment_fields_to_save', $post, $attachment );

		if ( isset($attachment['image_alt']) ) {
			$image_alt = wp_unslash( $attachment['image_alt'] );
			if ( $image_alt != get_post_meta($attachment_id, '_wp_attachment_image_alt', true) ) {
				$image_alt = wp_strip_all_tags( $image_alt, true );

				// Update_meta expects slashed.
				update_post_meta( $attachment_id, '_wp_attachment_image_alt', wp_slash( $image_alt ) );
			}
		}

		if ( isset($post['errors']) ) {
			$errors[$attachment_id] = $post['errors'];
			unset($post['errors']);
		}

		if ( $post != $_post )
			wp_update_post($post);

		foreach ( get_attachment_taxonomies($post) as $t ) {
			if ( isset($attachment[$t]) )
				wp_set_object_terms($attachment_id, array_map('trim', preg_split('/,+/', $attachment[$t])), $t, false);
		}
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
		$attachment = wp_unslash( $_POST['attachments'][$send_id] );

		$html = isset( $attachment['post_title'] ) ? $attachment['post_title'] : '';
		if ( !empty($attachment['url']) ) {
			$rel = '';
			if ( strpos($attachment['url'], 'attachment_id') || get_attachment_link($send_id) == $attachment['url'] )
				$rel = " rel='attachment wp-att-" . esc_attr($send_id) . "'";
			$html = "<a href='{$attachment['url']}'$rel>$html</a>";
		}

		/**
		 * Filter the HTML markup for a media item sent to the editor.
		 *
		 * @since 2.5.0
		 *
		 * @see wp_get_attachment_metadata()
		 *
		 * @param string $html       HTML markup for a media item sent to the editor.
		 * @param int    $send_id    The first key from the $_POST['send'] data.
		 * @param array  $attachment Array of attachment metadata.
		 */
		$html = apply_filters( 'media_send_to_editor', $html, $send_id, $attachment );
		return media_send_to_editor($html);
	}

	return $errors;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.0
 *
 * @return null|string
 */
function wp_media_upload_handler() {
	$errors = array();
	$id = 0;

	if ( isset($_POST['html-upload']) && !empty($_FILES) ) {
		check_admin_referer('media-form');
		// Upload File button was clicked
		$id = media_handle_upload('async-upload', $_REQUEST['post_id']);
		unset($_FILES);
		if ( is_wp_error($id) ) {
			$errors['upload_error'] = $id;
			$id = false;
		}
	}

	if ( !empty($_POST['insertonlybutton']) ) {
		$src = $_POST['src'];
		if ( !empty($src) && !strpos($src, '://') )
			$src = "http://$src";

		if ( isset( $_POST['media_type'] ) && 'image' != $_POST['media_type'] ) {
			$title = esc_html( wp_unslash( $_POST['title'] ) );
			if ( empty( $title ) )
				$title = esc_html( basename( $src ) );

			if ( $title && $src )
				$html = "<a href='" . esc_url($src) . "'>$title</a>";

			$type = 'file';
			if ( ( $ext = preg_replace( '/^.+?\.([^.]+)$/', '$1', $src ) ) && ( $ext_type = wp_ext2type( $ext ) )
				&& ( 'audio' == $ext_type || 'video' == $ext_type ) )
					$type = $ext_type;

			/**
			 * Filter the URL sent to the editor for a specific media type.
			 *
			 * The dynamic portion of the hook name, `$type`, refers to the type
			 * of media being sent.
			 *
			 * @since 3.3.0
			 *
			 * @param string $html  HTML markup sent to the editor.
			 * @param string $src   Media source URL.
			 * @param string $title Media title.
			 */
			$html = apply_filters( $type . '_send_to_editor_url', $html, esc_url_raw( $src ), $title );
		} else {
			$align = '';
			$alt = esc_attr( wp_unslash( $_POST['alt'] ) );
			if ( isset($_POST['align']) ) {
				$align = esc_attr( wp_unslash( $_POST['align'] ) );
				$class = " class='align$align'";
			}
			if ( !empty($src) )
				$html = "<img src='" . esc_url($src) . "' alt='$alt'$class />";

			/**
			 * Filter the image URL sent to the editor.
			 *
			 * @since 2.8.0
			 *
			 * @param string $html  HTML markup sent to the editor for an image.
			 * @param string $src   Image source URL.
			 * @param string $alt   Image alternate, or alt, text.
			 * @param string $align The image alignment. Default 'alignnone'. Possible values include
			 *                      'alignleft', 'aligncenter', 'alignright', 'alignnone'.
			 */
			$html = apply_filters( 'image_send_to_editor_url', $html, esc_url_raw( $src ), $alt, $align );
		}

		return media_send_to_editor($html);
	}

	if ( isset( $_POST['save'] ) ) {
		$errors['upload_notice'] = __('Saved.');
		return media_upload_gallery();
	} elseif ( ! empty( $_POST ) ) {
		$return = media_upload_form_handler();

		if ( is_string($return) )
			return $return;
		if ( is_array($return) )
			$errors = $return;
	}

	if ( isset($_GET['tab']) && $_GET['tab'] == 'type_url' ) {
		$type = 'image';
		if ( isset( $_GET['type'] ) && in_array( $_GET['type'], array( 'video', 'audio', 'file' ) ) )
			$type = $_GET['type'];
		return wp_iframe( 'media_upload_type_url_form', $type, $errors, $id );
	}

	return wp_iframe( 'media_upload_type_form', 'image', $errors, $id );
}

/**
 * Download an image from the specified URL and attach it to a post.
 *
 * @since 2.6.0
 *
 * @param string $file The URL of the image to download
 * @param int $post_id The post ID the media is to be associated with
 * @param string $desc Optional. Description of the image
 * @return string|WP_Error Populated HTML img tag on success
 */
function media_sideload_image( $file, $post_id, $desc = null ) {
	if ( ! empty( $file ) ) {
		// Set variables for storage, fix file filename for query strings.
		preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
		$file_array = array();
		$file_array['name'] = basename( $matches[0] );

		// Download file to temp location.
		$file_array['tmp_name'] = download_url( $file );

		// If error storing temporarily, return the error.
		if ( is_wp_error( $file_array['tmp_name'] ) ) {
			return $file_array['tmp_name'];
		}

		// Do the validation and storage stuff.
		$id = media_handle_sideload( $file_array, $post_id, $desc );

		// If error storing permanently, unlink.
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] );
			return $id;
		}

		$src = wp_get_attachment_url( $id );
	}

	// Finally check to make sure the file has been saved, then return the HTML.
	if ( ! empty( $src ) ) {
		$alt = isset( $desc ) ? esc_attr( $desc ) : '';
		$html = "<img src='$src' alt='$alt' />";
		return $html;
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.0
 *
 * @return string|null
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
 * @since 2.5.0
 *
 * @return string|null
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
 * @since 2.7.0
 *
 * @param WP_Post $post
 * @param string $checked
 * @return string
 */
function image_align_input_fields( $post, $checked = '' ) {

	if ( empty($checked) )
		$checked = get_user_setting('align', 'none');

	$alignments = array('none' => __('None'), 'left' => __('Left'), 'center' => __('Center'), 'right' => __('Right'));
	if ( !array_key_exists( (string) $checked, $alignments ) )
		$checked = 'none';

	$out = array();
	foreach ( $alignments as $name => $label ) {
		$name = esc_attr($name);
		$out[] = "<input type='radio' name='attachments[{$post->ID}][align]' id='image-align-{$name}-{$post->ID}' value='$name'".
			( $checked == $name ? " checked='checked'" : "" ) .
			" /><label for='image-align-{$name}-{$post->ID}' class='align image-align-{$name}-label'>$label</label>";
	}
	return join("\n", $out);
}

/**
 * Retrieve HTML for the size radio buttons with the specified one checked.
 *
 * @since 2.7.0
 *
 * @param WP_Post $post
 * @param bool|string $check
 * @return array
 */
function image_size_input_fields( $post, $check = '' ) {

	/**
	 * Filter the names and labels of the default image sizes.
	 *
	 * @since 3.3.0
	 *
	 * @param array $size_names Array of image sizes and their names. Default values
	 *                          include 'Thumbnail', 'Medium', 'Large', 'Full Size'.
	 */
	$size_names = apply_filters( 'image_size_names_choose', array(
		'thumbnail' => __( 'Thumbnail' ),
		'medium'    => __( 'Medium' ),
		'large'     => __( 'Large' ),
		'full'      => __( 'Full Size' )
	) );

		if ( empty($check) )
			$check = get_user_setting('imgsize', 'medium');

		foreach ( $size_names as $size => $label ) {
			$downsize = image_downsize($post->ID, $size);
			$checked = '';

			// Is this size selectable?
			$enabled = ( $downsize[3] || 'full' == $size );
			$css_id = "image-size-{$size}-{$post->ID}";

			// If this size is the default but that's not available, don't select it.
			if ( $size == $check ) {
				if ( $enabled )
					$checked = " checked='checked'";
				else
					$check = '';
			} elseif ( !$check && $enabled && 'thumbnail' != $size ) {
				/*
				 * If $check is not enabled, default to the first available size
				 * that's bigger than a thumbnail.
				 */
				$check = $size;
				$checked = " checked='checked'";
			}

			$html = "<div class='image-size-item'><input type='radio' " . disabled( $enabled, false, false ) . "name='attachments[$post->ID][image-size]' id='{$css_id}' value='{$size}'$checked />";

			$html .= "<label for='{$css_id}'>$label</label>";

			// Only show the dimensions if that choice is available.
			if ( $enabled )
				$html .= " <label for='{$css_id}' class='help'>" . sprintf( "(%d&nbsp;&times;&nbsp;%d)", $downsize[1], $downsize[2] ). "</label>";

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
 * @since 2.7.0
 *
 * @param WP_Post $post
 * @param string $url_type
 * @return string
 */
function image_link_input_fields($post, $url_type = '') {

	$file = wp_get_attachment_url($post->ID);
	$link = get_attachment_link($post->ID);

	if ( empty($url_type) )
		$url_type = get_user_setting('urlbutton', 'post');

	$url = '';
	if ( $url_type == 'file' )
		$url = $file;
	elseif ( $url_type == 'post' )
		$url = $link;

	return "
	<input type='text' class='text urlfield' name='attachments[$post->ID][url]' value='" . esc_attr($url) . "' /><br />
	<button type='button' class='button urlnone' data-link-url=''>" . __('None') . "</button>
	<button type='button' class='button urlfile' data-link-url='" . esc_attr($file) . "'>" . __('File URL') . "</button>
	<button type='button' class='button urlpost' data-link-url='" . esc_attr($link) . "'>" . __('Attachment Post URL') . "</button>
";
}

function wp_caption_input_textarea($edit_post) {
	// Post data is already escaped.
	$name = "attachments[{$edit_post->ID}][post_excerpt]";

	return '<textarea name="' . $name . '" id="' . $name . '">' . $edit_post->post_excerpt . '</textarea>';
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.0
 *
 * @param array $form_fields
 * @param object $post
 * @return array
 */
function image_attachment_fields_to_edit($form_fields, $post) {
	return $form_fields;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.0
 *
 * @param array   $form_fields An array of attachment form fields.
 * @param WP_Post $post        The WP_Post attachment object.
 * @return array Filtered attachment form fields.
 */
function media_single_attachment_fields_to_edit( $form_fields, $post ) {
	unset($form_fields['url'], $form_fields['align'], $form_fields['image-size']);
	return $form_fields;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.8.0
 *
 * @param array   $form_fields An array of attachment form fields.
 * @param WP_Post $post        The WP_Post attachment object.
 * @return array Filtered attachment form fields.
 */
function media_post_single_attachment_fields_to_edit( $form_fields, $post ) {
	unset($form_fields['image_url']);
	return $form_fields;
}

/**
 * Filters input from media_upload_form_handler() and assigns a default
 * post_title from the file name if none supplied.
 *
 * Illustrates the use of the attachment_fields_to_save filter
 * which can be used to add default values to any field before saving to DB.
 *
 * @since 2.5.0
 *
 * @param array $post       The WP_Post attachment object converted to an array.
 * @param array $attachment An array of attachment metadata.
 * @return array Filtered attachment post object.
 */
function image_attachment_fields_to_save( $post, $attachment ) {
	if ( substr( $post['post_mime_type'], 0, 5 ) == 'image' ) {
		if ( strlen( trim( $post['post_title'] ) ) == 0 ) {
			$attachment_url = ( isset( $post['attachment_url'] ) ) ? $post['attachment_url'] : $post['guid'];
			$post['post_title'] = preg_replace( '/\.\w+$/', '', wp_basename( $attachment_url ) );
			$post['errors']['post_title']['errors'][] = __( 'Empty Title filled from filename.' );
		}
	}

	return $post;
}

add_filter( 'attachment_fields_to_save', 'image_attachment_fields_to_save', 10, 2 );

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.0
 *
 * @param string $html
 * @param integer $attachment_id
 * @param array $attachment
 * @return string
 */
function image_media_send_to_editor($html, $attachment_id, $attachment) {
	$post = get_post($attachment_id);
	if ( substr($post->post_mime_type, 0, 5) == 'image' ) {
		$url = $attachment['url'];
		$align = !empty($attachment['align']) ? $attachment['align'] : 'none';
		$size = !empty($attachment['image-size']) ? $attachment['image-size'] : 'medium';
		$alt = !empty($attachment['image_alt']) ? $attachment['image_alt'] : '';
		$rel = ( $url == get_attachment_link($attachment_id) );

		return get_image_send_to_editor($attachment_id, $attachment['post_excerpt'], $attachment['post_title'], $align, $url, $rel, $size, $alt);
	}

	return $html;
}

add_filter('media_send_to_editor', 'image_media_send_to_editor', 10, 3);

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.0
 *
 * @param WP_Post $post
 * @param array $errors
 * @return array
 */
function get_attachment_fields_to_edit($post, $errors = null) {
	if ( is_int($post) )
		$post = get_post($post);
	if ( is_array($post) )
		$post = new WP_Post( (object) $post );

	$image_url = wp_get_attachment_url($post->ID);

	$edit_post = sanitize_post($post, 'edit');

	$form_fields = array(
		'post_title'   => array(
			'label'      => __('Title'),
			'value'      => $edit_post->post_title
		),
		'image_alt'   => array(),
		'post_excerpt' => array(
			'label'      => __('Caption'),
			'input'      => 'html',
			'html'       => wp_caption_input_textarea($edit_post)
		),
		'post_content' => array(
			'label'      => __('Description'),
			'value'      => $edit_post->post_content,
			'input'      => 'textarea'
		),
		'url'          => array(
			'label'      => __('Link URL'),
			'input'      => 'html',
			'html'       => image_link_input_fields($post, get_option('image_default_link_type')),
			'helps'      => __('Enter a link URL or click above for presets.')
		),
		'menu_order'   => array(
			'label'      => __('Order'),
			'value'      => $edit_post->menu_order
		),
		'image_url'	=> array(
			'label'      => __('File URL'),
			'input'      => 'html',
			'html'       => "<input type='text' class='text urlfield' readonly='readonly' name='attachments[$post->ID][url]' value='" . esc_attr($image_url) . "' /><br />",
			'value'      => wp_get_attachment_url($post->ID),
			'helps'      => __('Location of the uploaded file.')
		)
	);

	foreach ( get_attachment_taxonomies($post) as $taxonomy ) {
		$t = (array) get_taxonomy($taxonomy);
		if ( ! $t['public'] || ! $t['show_ui'] )
			continue;
		if ( empty($t['label']) )
			$t['label'] = $taxonomy;
		if ( empty($t['args']) )
			$t['args'] = array();

		$terms = get_object_term_cache($post->ID, $taxonomy);
		if ( false === $terms )
			$terms = wp_get_object_terms($post->ID, $taxonomy, $t['args']);

		$values = array();

		foreach ( $terms as $term )
			$values[] = $term->slug;
		$t['value'] = join(', ', $values);

		$form_fields[$taxonomy] = $t;
	}

	// Merge default fields with their errors, so any key passed with the error (e.g. 'error', 'helps', 'value') will replace the default
	// The recursive merge is easily traversed with array casting: foreach( (array) $things as $thing )
	$form_fields = array_merge_recursive($form_fields, (array) $errors);

	// This was formerly in image_attachment_fields_to_edit().
	if ( substr($post->post_mime_type, 0, 5) == 'image' ) {
		$alt = get_post_meta($post->ID, '_wp_attachment_image_alt', true);
		if ( empty($alt) )
			$alt = '';

		$form_fields['post_title']['required'] = true;

		$form_fields['image_alt'] = array(
			'value' => $alt,
			'label' => __('Alternative Text'),
			'helps' => __('Alt text for the image, e.g. &#8220;The Mona Lisa&#8221;')
		);

		$form_fields['align'] = array(
			'label' => __('Alignment'),
			'input' => 'html',
			'html'  => image_align_input_fields($post, get_option('image_default_align')),
		);

		$form_fields['image-size'] = image_size_input_fields( $post, get_option('image_default_size', 'medium') );

	} else {
		unset( $form_fields['image_alt'] );
	}

	/**
	 * Filter the attachment fields to edit.
	 *
	 * @since 2.5.0
	 *
	 * @param array   $form_fields An array of attachment form fields.
	 * @param WP_Post $post        The WP_Post attachment object.
	 */
	$form_fields = apply_filters( 'attachment_fields_to_edit', $form_fields, $post );

	return $form_fields;
}

/**
 * Retrieve HTML for media items of post gallery.
 *
 * The HTML markup retrieved will be created for the progress of SWF Upload
 * component. Will also create link for showing and hiding the form to modify
 * the image attachment.
 *
 * @since 2.5.0
 *
 * @param int $post_id Optional. Post ID.
 * @param array $errors Errors for attachment, if any.
 * @return string
 */
function get_media_items( $post_id, $errors ) {
	$attachments = array();
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
			$output .= "\n<div id='media-item-$id' class='media-item child-of-$attachment->post_parent preloaded'><div class='progress hidden'><div class='bar'></div></div><div id='media-upload-error-$id' class='hidden'></div><div class='filename hidden'></div>$item\n</div>";
	}

	return $output;
}

/**
 * Retrieve HTML form for modifying the image attachment.
 *
 * @since 2.5.0
 *
 * @param int $attachment_id Attachment ID for modification.
 * @param string|array $args Optional. Override defaults.
 * @return string HTML form for attachment.
 */
function get_media_item( $attachment_id, $args = null ) {
	global $redir_tab;

	if ( ( $attachment_id = intval( $attachment_id ) ) && $thumb_url = wp_get_attachment_image_src( $attachment_id, 'thumbnail', true ) )
		$thumb_url = $thumb_url[0];
	else
		$thumb_url = false;

	$post = get_post( $attachment_id );
	$current_post_id = !empty( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0;

	$default_args = array(
		'errors' => null,
		'send' => $current_post_id ? post_type_supports( get_post_type( $current_post_id ), 'editor' ) : true,
		'delete' => true,
		'toggle' => true,
		'show_title' => true
	);
	$args = wp_parse_args( $args, $default_args );

	/**
	 * Filter the arguments used to retrieve an image for the edit image form.
	 *
	 * @since 3.1.0
	 *
	 * @see get_media_item
	 *
	 * @param array $args An array of arguments.
	 */
	$r = apply_filters( 'get_media_item_args', $args );

	$toggle_on  = __( 'Show' );
	$toggle_off = __( 'Hide' );

	$filename = esc_html( wp_basename( $post->guid ) );
	$title = esc_attr( $post->post_title );

	$post_mime_types = get_post_mime_types();
	$keys = array_keys( wp_match_mime_types( array_keys( $post_mime_types ), $post->post_mime_type ) );
	$type = array_shift( $keys );
	$type_html = "<input type='hidden' id='type-of-$attachment_id' value='" . esc_attr( $type ) . "' />";

	$form_fields = get_attachment_fields_to_edit( $post, $r['errors'] );

	if ( $r['toggle'] ) {
		$class = empty( $r['errors'] ) ? 'startclosed' : 'startopen';
		$toggle_links = "
	<a class='toggle describe-toggle-on' href='#'>$toggle_on</a>
	<a class='toggle describe-toggle-off' href='#'>$toggle_off</a>";
	} else {
		$class = '';
		$toggle_links = '';
	}

	$display_title = ( !empty( $title ) ) ? $title : $filename; // $title shouldn't ever be empty, but just in case
	$display_title = $r['show_title'] ? "<div class='filename new'><span class='title'>" . wp_html_excerpt( $display_title, 60, '&hellip;' ) . "</span></div>" : '';

	$gallery = ( ( isset( $_REQUEST['tab'] ) && 'gallery' == $_REQUEST['tab'] ) || ( isset( $redir_tab ) && 'gallery' == $redir_tab ) );
	$order = '';

	foreach ( $form_fields as $key => $val ) {
		if ( 'menu_order' == $key ) {
			if ( $gallery )
				$order = "<div class='menu_order'> <input class='menu_order_input' type='text' id='attachments[$attachment_id][menu_order]' name='attachments[$attachment_id][menu_order]' value='" . esc_attr( $val['value'] ). "' /></div>";
			else
				$order = "<input type='hidden' name='attachments[$attachment_id][menu_order]' value='" . esc_attr( $val['value'] ) . "' />";

			unset( $form_fields['menu_order'] );
			break;
		}
	}

	$media_dims = '';
	$meta = wp_get_attachment_metadata( $post->ID );
	if ( isset( $meta['width'], $meta['height'] ) )
		$media_dims .= "<span id='media-dims-$post->ID'>{$meta['width']}&nbsp;&times;&nbsp;{$meta['height']}</span> ";

	/**
	 * Filter the media metadata.
	 *
	 * @since 2.5.0
	 *
	 * @param string  $media_dims The HTML markup containing the media dimensions.
	 * @param WP_Post $post       The WP_Post attachment object.
	 */
	$media_dims = apply_filters( 'media_meta', $media_dims, $post );

	$image_edit_button = '';
	if ( wp_attachment_is_image( $post->ID ) && wp_image_editor_supports( array( 'mime_type' => $post->post_mime_type ) ) ) {
		$nonce = wp_create_nonce( "image_editor-$post->ID" );
		$image_edit_button = "<input type='button' id='imgedit-open-btn-$post->ID' onclick='imageEdit.open( $post->ID, \"$nonce\" )' class='button' value='" . esc_attr__( 'Edit Image' ) . "' /> <span class='spinner'></span>";
	}

	$attachment_url = get_permalink( $attachment_id );

	$item = "
	$type_html
	$toggle_links
	$order
	$display_title
	<table class='slidetoggle describe $class'>
		<thead class='media-item-info' id='media-head-$post->ID'>
		<tr>
			<td class='A1B1' id='thumbnail-head-$post->ID'>
			<p><a href='$attachment_url' target='_blank'><img class='thumbnail' src='$thumb_url' alt='' /></a></p>
			<p>$image_edit_button</p>
			</td>
			<td>
			<p><strong>" . __('File name:') . "</strong> $filename</p>
			<p><strong>" . __('File type:') . "</strong> $post->post_mime_type</p>
			<p><strong>" . __('Upload date:') . "</strong> " . mysql2date( get_option('date_format'), $post->post_date ). '</p>';
			if ( !empty( $media_dims ) )
				$item .= "<p><strong>" . __('Dimensions:') . "</strong> $media_dims</p>\n";

			$item .= "</td></tr>\n";

	$item .= "
		</thead>
		<tbody>
		<tr><td colspan='2' class='imgedit-response' id='imgedit-response-$post->ID'></td></tr>
		<tr><td style='display:none' colspan='2' class='image-editor' id='image-editor-$post->ID'></td></tr>\n";

	$defaults = array(
		'input'      => 'text',
		'required'   => false,
		'value'      => '',
		'extra_rows' => array(),
	);

	if ( $r['send'] ) {
		$r['send'] = get_submit_button( __( 'Insert into Post' ), 'button', "send[$attachment_id]", false );
	}

	$delete = empty( $r['delete'] ) ? '' : $r['delete'];
	if ( $delete && current_user_can( 'delete_post', $attachment_id ) ) {
		if ( !EMPTY_TRASH_DAYS ) {
			$delete = "<a href='" . wp_nonce_url( "post.php?action=delete&amp;post=$attachment_id", 'delete-post_' . $attachment_id ) . "' id='del[$attachment_id]' class='delete-permanently'>" . __( 'Delete Permanently' ) . '</a>';
		} elseif ( !MEDIA_TRASH ) {
			$delete = "<a href='#' class='del-link' onclick=\"document.getElementById('del_attachment_$attachment_id').style.display='block';return false;\">" . __( 'Delete' ) . "</a>
			 <div id='del_attachment_$attachment_id' class='del-attachment' style='display:none;'><p>" . sprintf( __( 'You are about to delete <strong>%s</strong>.' ), $filename ) . "</p>
			 <a href='" . wp_nonce_url( "post.php?action=delete&amp;post=$attachment_id", 'delete-post_' . $attachment_id ) . "' id='del[$attachment_id]' class='button'>" . __( 'Continue' ) . "</a>
			 <a href='#' class='button' onclick=\"this.parentNode.style.display='none';return false;\">" . __( 'Cancel' ) . "</a>
			 </div>";
		} else {
			$delete = "<a href='" . wp_nonce_url( "post.php?action=trash&amp;post=$attachment_id", 'trash-post_' . $attachment_id ) . "' id='del[$attachment_id]' class='delete'>" . __( 'Move to Trash' ) . "</a>
			<a href='" . wp_nonce_url( "post.php?action=untrash&amp;post=$attachment_id", 'untrash-post_' . $attachment_id ) . "' id='undo[$attachment_id]' class='undo hidden'>" . __( 'Undo' ) . "</a>";
		}
	} else {
		$delete = '';
	}

	$thumbnail = '';
	$calling_post_id = 0;
	if ( isset( $_GET['post_id'] ) ) {
		$calling_post_id = absint( $_GET['post_id'] );
	} elseif ( isset( $_POST ) && count( $_POST ) ) {// Like for async-upload where $_GET['post_id'] isn't set
		$calling_post_id = $post->post_parent;
	}
	if ( 'image' == $type && $calling_post_id && current_theme_supports( 'post-thumbnails', get_post_type( $calling_post_id ) )
		&& post_type_supports( get_post_type( $calling_post_id ), 'thumbnail' ) && get_post_thumbnail_id( $calling_post_id ) != $attachment_id ) {
		$ajax_nonce = wp_create_nonce( "set_post_thumbnail-$calling_post_id" );
		$thumbnail = "<a class='wp-post-thumbnail' id='wp-post-thumbnail-" . $attachment_id . "' href='#' onclick='WPSetAsThumbnail(\"$attachment_id\", \"$ajax_nonce\");return false;'>" . esc_html__( "Use as featured image" ) . "</a>";
	}

	if ( ( $r['send'] || $thumbnail || $delete ) && !isset( $form_fields['buttons'] ) ) {
		$form_fields['buttons'] = array( 'tr' => "\t\t<tr class='submit'><td></td><td class='savesend'>" . $r['send'] . " $thumbnail $delete</td></tr>\n" );
	}
	$hidden_fields = array();

	foreach ( $form_fields as $id => $field ) {
		if ( $id[0] == '_' )
			continue;

		if ( !empty( $field['tr'] ) ) {
			$item .= $field['tr'];
			continue;
		}

		$field = array_merge( $defaults, $field );
		$name = "attachments[$attachment_id][$id]";

		if ( $field['input'] == 'hidden' ) {
			$hidden_fields[$name] = $field['value'];
			continue;
		}

		$required      = $field['required'] ? '<span class="alignright"><abbr title="required" class="required">*</abbr></span>' : '';
		$aria_required = $field['required'] ? " aria-required='true' " : '';
		$class  = $id;
		$class .= $field['required'] ? ' form-required' : '';

		$item .= "\t\t<tr class='$class'>\n\t\t\t<th scope='row' class='label'><label for='$name'><span class='alignleft'>{$field['label']}</span>$required<br class='clear' /></label></th>\n\t\t\t<td class='field'>";
		if ( !empty( $field[ $field['input'] ] ) )
			$item .= $field[ $field['input'] ];
		elseif ( $field['input'] == 'textarea' ) {
			if ( 'post_content' == $id && user_can_richedit() ) {
				// Sanitize_post() skips the post_content when user_can_richedit.
				$field['value'] = htmlspecialchars( $field['value'], ENT_QUOTES );
			}
			// Post_excerpt is already escaped by sanitize_post() in get_attachment_fields_to_edit().
			$item .= "<textarea id='$name' name='$name' $aria_required>" . $field['value'] . '</textarea>';
		} else {
			$item .= "<input type='text' class='text' id='$name' name='$name' value='" . esc_attr( $field['value'] ) . "' $aria_required />";
		}
		if ( !empty( $field['helps'] ) )
			$item .= "<p class='help'>" . join( "</p>\n<p class='help'>", array_unique( (array) $field['helps'] ) ) . '</p>';
		$item .= "</td>\n\t\t</tr>\n";

		$extra_rows = array();

		if ( !empty( $field['errors'] ) )
			foreach ( array_unique( (array) $field['errors'] ) as $error )
				$extra_rows['error'][] = $error;

		if ( !empty( $field['extra_rows'] ) )
			foreach ( $field['extra_rows'] as $class => $rows )
				foreach ( (array) $rows as $html )
					$extra_rows[$class][] = $html;

		foreach ( $extra_rows as $class => $rows )
			foreach ( $rows as $html )
				$item .= "\t\t<tr><td></td><td class='$class'>$html</td></tr>\n";
	}

	if ( !empty( $form_fields['_final'] ) )
		$item .= "\t\t<tr class='final'><td colspan='2'>{$form_fields['_final']}</td></tr>\n";
	$item .= "\t</tbody>\n";
	$item .= "\t</table>\n";

	foreach ( $hidden_fields as $name => $value )
		$item .= "\t<input type='hidden' name='$name' id='$name' value='" . esc_attr( $value ) . "' />\n";

	if ( $post->post_parent < 1 && isset( $_REQUEST['post_id'] ) ) {
		$parent = (int) $_REQUEST['post_id'];
		$parent_name = "attachments[$attachment_id][post_parent]";
		$item .= "\t<input type='hidden' name='$parent_name' id='$parent_name' value='$parent' />\n";
	}

	return $item;
}

function get_compat_media_markup( $attachment_id, $args = null ) {
	$post = get_post( $attachment_id );

	$default_args = array(
		'errors' => null,
		'in_modal' => false,
	);

	$user_can_edit = current_user_can( 'edit_post', $attachment_id );

	$args = wp_parse_args( $args, $default_args );

	/** This filter is documented in wp-admin/includes/media.php */
	$args = apply_filters( 'get_media_item_args', $args );

	$form_fields = array();

	if ( $args['in_modal'] ) {
		foreach ( get_attachment_taxonomies($post) as $taxonomy ) {
			$t = (array) get_taxonomy($taxonomy);
			if ( ! $t['public'] || ! $t['show_ui'] )
				continue;
			if ( empty($t['label']) )
				$t['label'] = $taxonomy;
			if ( empty($t['args']) )
				$t['args'] = array();

			$terms = get_object_term_cache($post->ID, $taxonomy);
			if ( false === $terms )
				$terms = wp_get_object_terms($post->ID, $taxonomy, $t['args']);

			$values = array();

			foreach ( $terms as $term )
				$values[] = $term->slug;
			$t['value'] = join(', ', $values);
			$t['taxonomy'] = true;

			$form_fields[$taxonomy] = $t;
		}
	}

	// Merge default fields with their errors, so any key passed with the error (e.g. 'error', 'helps', 'value') will replace the default
	// The recursive merge is easily traversed with array casting: foreach( (array) $things as $thing )
	$form_fields = array_merge_recursive($form_fields, (array) $args['errors'] );

	/** This filter is documented in wp-admin/includes/media.php */
	$form_fields = apply_filters( 'attachment_fields_to_edit', $form_fields, $post );

	unset( $form_fields['image-size'], $form_fields['align'], $form_fields['image_alt'],
		$form_fields['post_title'], $form_fields['post_excerpt'], $form_fields['post_content'],
		$form_fields['url'], $form_fields['menu_order'], $form_fields['image_url'] );

	/** This filter is documented in wp-admin/includes/media.php */
	$media_meta = apply_filters( 'media_meta', '', $post );

	$defaults = array(
		'input'         => 'text',
		'required'      => false,
		'value'         => '',
		'extra_rows'    => array(),
		'show_in_edit'  => true,
		'show_in_modal' => true,
	);

	$hidden_fields = array();

	$item = '';
	foreach ( $form_fields as $id => $field ) {
		if ( $id[0] == '_' )
			continue;

		$name = "attachments[$attachment_id][$id]";
		$id_attr = "attachments-$attachment_id-$id";

		if ( !empty( $field['tr'] ) ) {
			$item .= $field['tr'];
			continue;
		}

		$field = array_merge( $defaults, $field );

		if ( ( ! $field['show_in_edit'] && ! $args['in_modal'] ) || ( ! $field['show_in_modal'] && $args['in_modal'] ) )
			continue;

		if ( $field['input'] == 'hidden' ) {
			$hidden_fields[$name] = $field['value'];
			continue;
		}

		$readonly      = ! $user_can_edit && ! empty( $field['taxonomy'] ) ? " readonly='readonly' " : '';
		$required      = $field['required'] ? '<span class="alignright"><abbr title="required" class="required">*</abbr></span>' : '';
		$aria_required = $field['required'] ? " aria-required='true' " : '';
		$class  = 'compat-field-' . $id;
		$class .= $field['required'] ? ' form-required' : '';

		$item .= "\t\t<tr class='$class'>";
		$item .= "\t\t\t<th scope='row' class='label'><label for='$id_attr'><span class='alignleft'>{$field['label']}</span>$required<br class='clear' /></label>";
		$item .= "</th>\n\t\t\t<td class='field'>";

		if ( !empty( $field[ $field['input'] ] ) )
			$item .= $field[ $field['input'] ];
		elseif ( $field['input'] == 'textarea' ) {
			if ( 'post_content' == $id && user_can_richedit() ) {
				// sanitize_post() skips the post_content when user_can_richedit.
				$field['value'] = htmlspecialchars( $field['value'], ENT_QUOTES );
			}
			$item .= "<textarea id='$id_attr' name='$name' $aria_required>" . $field['value'] . '</textarea>';
		} else {
			$item .= "<input type='text' class='text' id='$id_attr' name='$name' value='" . esc_attr( $field['value'] ) . "' $readonly $aria_required />";
		}
		if ( !empty( $field['helps'] ) )
			$item .= "<p class='help'>" . join( "</p>\n<p class='help'>", array_unique( (array) $field['helps'] ) ) . '</p>';
		$item .= "</td>\n\t\t</tr>\n";

		$extra_rows = array();

		if ( !empty( $field['errors'] ) )
			foreach ( array_unique( (array) $field['errors'] ) as $error )
				$extra_rows['error'][] = $error;

		if ( !empty( $field['extra_rows'] ) )
			foreach ( $field['extra_rows'] as $class => $rows )
				foreach ( (array) $rows as $html )
					$extra_rows[$class][] = $html;

		foreach ( $extra_rows as $class => $rows )
			foreach ( $rows as $html )
				$item .= "\t\t<tr><td></td><td class='$class'>$html</td></tr>\n";
	}

	if ( !empty( $form_fields['_final'] ) )
		$item .= "\t\t<tr class='final'><td colspan='2'>{$form_fields['_final']}</td></tr>\n";
	if ( $item )
		$item = '<table class="compat-attachment-fields">' . $item . '</table>';

	foreach ( $hidden_fields as $hidden_field => $value ) {
		$item .= '<input type="hidden" name="' . esc_attr( $hidden_field ) . '" value="' . esc_attr( $value ) . '" />' . "\n";
	}

	if ( $item )
		$item = '<input type="hidden" name="attachments[' . $attachment_id . '][menu_order]" value="' . esc_attr( $post->menu_order ) . '" />' . $item;

	return array(
		'item'   => $item,
		'meta'   => $media_meta,
	);
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.0
 */
function media_upload_header() {
	$post_id = isset( $_REQUEST['post_id'] ) ? intval( $_REQUEST['post_id'] ) : 0;
	echo '<script type="text/javascript">post_id = ' . $post_id . ";</script>\n";
	if ( empty( $_GET['chromeless'] ) ) {
		echo '<div id="media-upload-header">';
		the_media_upload_tabs();
		echo '</div>';
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.0
 *
 * @param array $errors
 */
function media_upload_form( $errors = null ) {
	global $type, $tab, $is_IE, $is_opera;

	if ( ! _device_can_upload() ) {
		echo '<p>' . sprintf( __('The web browser on your device cannot be used to upload files. You may be able to use the <a href="%s">native app for your device</a> instead.'), 'https://apps.wordpress.org/' ) . '</p>';
		return;
	}

	$upload_action_url = admin_url('async-upload.php');
	$post_id = isset($_REQUEST['post_id']) ? intval($_REQUEST['post_id']) : 0;
	$_type = isset($type) ? $type : '';
	$_tab = isset($tab) ? $tab : '';

	$max_upload_size = wp_max_upload_size();
	if ( ! $max_upload_size ) {
		$max_upload_size = 0;
	}
?>

<div id="media-upload-notice"><?php

	if (isset($errors['upload_notice']) )
		echo $errors['upload_notice'];

?></div>
<div id="media-upload-error"><?php

	if (isset($errors['upload_error']) && is_wp_error($errors['upload_error']))
		echo $errors['upload_error']->get_error_message();

?></div>
<?php
if ( is_multisite() && !is_upload_space_available() ) {
	/**
	 * Fires when an upload will exceed the defined upload space quota for a network site.
	 *
	 * @since 3.5.0
	 */
	do_action( 'upload_ui_over_quota' );
	return;
}

/**
 * Fires just before the legacy (pre-3.5.0) upload interface is loaded.
 *
 * @since 2.6.0
 */
do_action( 'pre-upload-ui' );

$post_params = array(
	"post_id" => $post_id,
	"_wpnonce" => wp_create_nonce('media-form'),
	"type" => $_type,
	"tab" => $_tab,
	"short" => "1",
);

/**
 * Filter the media upload post parameters.
 *
 * @since 3.1.0 As 'swfupload_post_params'
 * @since 3.3.0
 *
 * @param array $post_params An array of media upload parameters used by Plupload.
 */
$post_params = apply_filters( 'upload_post_params', $post_params );

$plupload_init = array(
	'runtimes'            => 'html5,flash,silverlight,html4',
	'browse_button'       => 'plupload-browse-button',
	'container'           => 'plupload-upload-ui',
	'drop_element'        => 'drag-drop-area',
	'file_data_name'      => 'async-upload',
	'url'                 => $upload_action_url,
	'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
	'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
	'filters' => array(
		'max_file_size'   => $max_upload_size . 'b',
	),
	'multipart_params'    => $post_params,
);

// Currently only iOS Safari supports multiple files uploading but iOS 7.x has a bug that prevents uploading of videos
// when enabled. See #29602.
if ( wp_is_mobile() && strpos( $_SERVER['HTTP_USER_AGENT'], 'OS 7_' ) !== false &&
	strpos( $_SERVER['HTTP_USER_AGENT'], 'like Mac OS X' ) !== false ) {

	$plupload_init['multi_selection'] = false;
}

/**
 * Filter the default Plupload settings.
 *
 * @since 3.3.0
 *
 * @param array $plupload_init An array of default settings used by Plupload.
 */
$plupload_init = apply_filters( 'plupload_init', $plupload_init );

?>

<script type="text/javascript">
<?php
// Verify size is an int. If not return default value.
$large_size_h = absint( get_option('large_size_h') );
if( !$large_size_h )
	$large_size_h = 1024;
$large_size_w = absint( get_option('large_size_w') );
if( !$large_size_w )
	$large_size_w = 1024;
?>
var resize_height = <?php echo $large_size_h; ?>, resize_width = <?php echo $large_size_w; ?>,
wpUploaderInit = <?php echo wp_json_encode( $plupload_init ); ?>;
</script>

<div id="plupload-upload-ui" class="hide-if-no-js">
<?php
/**
 * Fires before the upload interface loads.
 *
 * @since 2.6.0 As 'pre-flash-upload-ui'
 * @since 3.3.0
 */
do_action( 'pre-plupload-upload-ui' ); ?>
<div id="drag-drop-area">
	<div class="drag-drop-inside">
	<p class="drag-drop-info"><?php _e('Drop files here'); ?></p>
	<p><?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?></p>
	<p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php esc_attr_e('Select Files'); ?>" class="button" /></p>
	</div>
</div>
<?php
/**
 * Fires after the upload interface loads.
 *
 * @since 2.6.0 As 'post-flash-upload-ui'
 * @since 3.3.0
 */
do_action( 'post-plupload-upload-ui' ); ?>
</div>

<div id="html-upload-ui" class="hide-if-js">
	<?php
	/**
	 * Fires before the upload button in the media upload interface.
	 *
	 * @since 2.6.0
	 */
	do_action( 'pre-html-upload-ui' );
	?>
	<p id="async-upload-wrap">
		<label class="screen-reader-text" for="async-upload"><?php _e('Upload'); ?></label>
		<input type="file" name="async-upload" id="async-upload" />
		<?php submit_button( __( 'Upload' ), 'button', 'html-upload', false ); ?>
		<a href="#" onclick="try{top.tb_remove();}catch(e){}; return false;"><?php _e('Cancel'); ?></a>
	</p>
	<div class="clear"></div>
<?php
/**
 * Fires after the upload button in the media upload interface.
 *
 * @since 2.6.0
 */
do_action( 'post-html-upload-ui' );
?>
</div>

<p class="max-upload-size"><?php printf( __( 'Maximum upload file size: %s.' ), esc_html( size_format( $max_upload_size ) ) ); ?></p>
<?php

	/**
	 * Fires on the post upload UI screen.
	 *
	 * Legacy (pre-3.5.0) media workflow hook.
	 *
	 * @since 2.6.0
	 */
	do_action( 'post-upload-ui' );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.0
 *
 * @param string $type
 * @param object $errors
 * @param integer $id
 */
function media_upload_type_form($type = 'file', $errors = null, $id = null) {

	media_upload_header();

	$post_id = isset( $_REQUEST['post_id'] )? intval( $_REQUEST['post_id'] ) : 0;

	$form_action_url = admin_url("media-upload.php?type=$type&tab=type&post_id=$post_id");

	/**
	 * Filter the media upload form action URL.
	 *
	 * @since 2.6.0
	 *
	 * @param string $form_action_url The media upload form action URL.
	 * @param string $type            The type of media. Default 'file'.
	 */
	$form_action_url = apply_filters( 'media_upload_form_url', $form_action_url, $type );
	$form_class = 'media-upload-form type-form validate';

	if ( get_user_setting('uploader') )
		$form_class .= ' html-uploader';
?>

<form enctype="multipart/form-data" method="post" action="<?php echo esc_url( $form_action_url ); ?>" class="<?php echo $form_class; ?>" id="<?php echo $type; ?>-form">
<?php submit_button( '', 'hidden', 'save', false ); ?>
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
<div id="media-items"><?php

if ( $id ) {
	if ( !is_wp_error($id) ) {
		add_filter('attachment_fields_to_edit', 'media_post_single_attachment_fields_to_edit', 10, 2);
		echo get_media_items( $id, $errors );
	} else {
		echo '<div id="media-upload-error">'.esc_html($id->get_error_message()).'</div></div>';
		exit;
	}
}
?></div>

<p class="savebutton ml-submit">
<?php submit_button( __( 'Save all changes' ), 'button', 'save', false ); ?>
</p>
</form>
<?php
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.7.0
 *
 * @param string $type
 * @param object $errors
 * @param integer $id
 */
function media_upload_type_url_form($type = null, $errors = null, $id = null) {
	if ( null === $type )
		$type = 'image';

	media_upload_header();

	$post_id = isset( $_REQUEST['post_id'] ) ? intval( $_REQUEST['post_id'] ) : 0;

	$form_action_url = admin_url("media-upload.php?type=$type&tab=type&post_id=$post_id");
	/** This filter is documented in wp-admin/includes/media.php */
	$form_action_url = apply_filters( 'media_upload_form_url', $form_action_url, $type );
	$form_class = 'media-upload-form type-form validate';

	if ( get_user_setting('uploader') )
		$form_class .= ' html-uploader';
?>

<form enctype="multipart/form-data" method="post" action="<?php echo esc_url( $form_action_url ); ?>" class="<?php echo $form_class; ?>" id="<?php echo $type; ?>-form">
<input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
<?php wp_nonce_field('media-form'); ?>

<h3 class="media-title"><?php _e('Insert media from another website'); ?></h3>

<script type="text/javascript">
//<![CDATA[
var addExtImage = {

	width : '',
	height : '',
	align : 'alignnone',

	insert : function() {
		var t = this, html, f = document.forms[0], cls, title = '', alt = '', caption = '';

		if ( '' == f.src.value || '' == t.width )
			return false;

		if ( f.alt.value )
			alt = f.alt.value.replace(/'/g, '&#039;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

<?php
	/** This filter is documented in wp-admin/includes/media.php */
	if ( ! apply_filters( 'disable_captions', '' ) ) {
		?>
		if ( f.caption.value ) {
			caption = f.caption.value.replace(/\r\n|\r/g, '\n');
			caption = caption.replace(/<[a-zA-Z0-9]+( [^<>]+)?>/g, function(a){
				return a.replace(/[\r\n\t]+/, ' ');
			});

			caption = caption.replace(/\s*\n\s*/g, '<br />');
		}
<?php } ?>

		cls = caption ? '' : ' class="'+t.align+'"';

		html = '<img alt="'+alt+'" src="'+f.src.value+'"'+cls+' width="'+t.width+'" height="'+t.height+'" />';

		if ( f.url.value ) {
			url = f.url.value.replace(/'/g, '&#039;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
			html = '<a href="'+url+'">'+html+'</a>';
		}

		if ( caption )
			html = '[caption id="" align="'+t.align+'" width="'+t.width+'"]'+html+caption+'[/caption]';

		var win = window.dialogArguments || opener || parent || top;
		win.send_to_editor(html);
		return false;
	},

	resetImageData : function() {
		var t = addExtImage;

		t.width = t.height = '';
		document.getElementById('go_button').style.color = '#bbb';
		if ( ! document.forms[0].src.value )
			document.getElementById('status_img').innerHTML = '*';
		else document.getElementById('status_img').innerHTML = '<img src="<?php echo esc_url( admin_url( 'images/no.png' ) ); ?>" alt="" />';
	},

	updateImageData : function() {
		var t = addExtImage;

		t.width = t.preloadImg.width;
		t.height = t.preloadImg.height;
		document.getElementById('go_button').style.color = '#333';
		document.getElementById('status_img').innerHTML = '<img src="<?php echo esc_url( admin_url( 'images/yes.png' ) ); ?>" alt="" />';
	},

	getImageData : function() {
		if ( jQuery('table.describe').hasClass('not-image') )
			return;

		var t = addExtImage, src = document.forms[0].src.value;

		if ( ! src ) {
			t.resetImageData();
			return false;
		}

		document.getElementById('status_img').innerHTML = '<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" width="16" />';
		t.preloadImg = new Image();
		t.preloadImg.onload = t.updateImageData;
		t.preloadImg.onerror = t.resetImageData;
		t.preloadImg.src = src;
	}
}

jQuery(document).ready( function($) {
	$('.media-types input').click( function() {
		$('table.describe').toggleClass('not-image', $('#not-image').prop('checked') );
	});
});

//]]>
</script>

<div id="media-items">
<div class="media-item media-blank">
<?php
/**
 * Filter the insert media from URL form HTML.
 *
 * @since 3.3.0
 *
 * @param string $form_html The insert from URL form HTML.
 */
echo apply_filters( 'type_url_form_media', wp_media_insert_url_form( $type ) );
?>
</div>
</div>
</form>
<?php
}

/**
 * Adds gallery form to upload iframe
 *
 * @since 2.5.0
 *
 * @param array $errors
 */
function media_upload_gallery_form($errors) {
	global $redir_tab, $type;

	$redir_tab = 'gallery';
	media_upload_header();

	$post_id = intval($_REQUEST['post_id']);
	$form_action_url = admin_url("media-upload.php?type=$type&tab=gallery&post_id=$post_id");
	/** This filter is documented in wp-admin/includes/media.php */
	$form_action_url = apply_filters( 'media_upload_form_url', $form_action_url, $type );
	$form_class = 'media-upload-form validate';

	if ( get_user_setting('uploader') )
		$form_class .= ' html-uploader';
?>

<script type="text/javascript">
jQuery(function($){
	var preloaded = $(".media-item.preloaded");
	if ( preloaded.length > 0 ) {
		preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
		updateMediaForm();
	}
});
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
<a href="#" id="clear"><?php _ex('Clear', 'verb'); ?></a>
</div>
<form enctype="multipart/form-data" method="post" action="<?php echo esc_url( $form_action_url ); ?>" class="<?php echo $form_class; ?>" id="gallery-form">
<?php wp_nonce_field('media-form'); ?>
<?php //media_upload_form( $errors ); ?>
<table class="widefat">
<thead><tr>
<th><?php _e('Media'); ?></th>
<th class="order-head"><?php _e('Order'); ?></th>
<th class="actions-head"><?php _e('Actions'); ?></th>
</tr></thead>
</table>
<div id="media-items">
<?php add_filter('attachment_fields_to_edit', 'media_post_single_attachment_fields_to_edit', 10, 2); ?>
<?php echo get_media_items($post_id, $errors); ?>
</div>

<p class="ml-submit">
<?php submit_button( __( 'Save all changes' ), 'button savebutton', 'save', false, array( 'id' => 'save-all', 'style' => 'display: none;' ) ); ?>
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
			<option value="post_date"><?php _e('Date/Time'); ?></option>
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
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3" selected="selected">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9">9</option>
		</select>
	</td>
	</tr>
</tbody></table>

<p class="ml-submit">
<input type="button" class="button" style="display:none;" onMouseDown="wpgallery.update();" name="insert-gallery" id="insert-gallery" value="<?php esc_attr_e( 'Insert gallery' ); ?>" />
<input type="button" class="button" style="display:none;" onMouseDown="wpgallery.update();" name="update-gallery" id="update-gallery" value="<?php esc_attr_e( 'Update gallery settings' ); ?>" />
</p>
</div>
</form>
<?php
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.0
 *
 * @param array $errors
 */
function media_upload_library_form($errors) {
	global $wpdb, $wp_query, $wp_locale, $type, $tab, $post_mime_types;

	media_upload_header();

	$post_id = isset( $_REQUEST['post_id'] ) ? intval( $_REQUEST['post_id'] ) : 0;

	$form_action_url = admin_url("media-upload.php?type=$type&tab=library&post_id=$post_id");
	/** This filter is documented in wp-admin/includes/media.php */
	$form_action_url = apply_filters( 'media_upload_form_url', $form_action_url, $type );
	$form_class = 'media-upload-form validate';

	if ( get_user_setting('uploader') )
		$form_class .= ' html-uploader';

	$q = $_GET;
	$q['posts_per_page'] = 10;
	$q['paged'] = isset( $q['paged'] ) ? intval( $q['paged'] ) : 0;
	if ( $q['paged'] < 1 ) {
		$q['paged'] = 1;
	}
	$q['offset'] = ( $q['paged'] - 1 ) * 10;
	if ( $q['offset'] < 1 ) {
		$q['offset'] = 0;
	}

	list($post_mime_types, $avail_post_mime_types) = wp_edit_attachments_query( $q );

?>

<form id="filter" action="" method="get">
<input type="hidden" name="type" value="<?php echo esc_attr( $type ); ?>" />
<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>" />
<input type="hidden" name="post_id" value="<?php echo (int) $post_id; ?>" />
<input type="hidden" name="post_mime_type" value="<?php echo isset( $_GET['post_mime_type'] ) ? esc_attr( $_GET['post_mime_type'] ) : ''; ?>" />
<input type="hidden" name="context" value="<?php echo isset( $_GET['context'] ) ? esc_attr( $_GET['context'] ) : ''; ?>" />

<p id="media-search" class="search-box">
	<label class="screen-reader-text" for="media-search-input"><?php _e('Search Media');?>:</label>
	<input type="search" id="media-search-input" name="s" value="<?php the_search_query(); ?>" />
	<?php submit_button( __( 'Search Media' ), 'button', '', false ); ?>
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
$type_links[] = '<li><a href="' . esc_url(add_query_arg(array('post_mime_type'=>'all', 'paged'=>false, 'm'=>false))) . '"' . $class . '>' . __('All Types') . '</a>';
foreach ( $post_mime_types as $mime_type => $label ) {
	$class = '';

	if ( !wp_match_mime_types($mime_type, $avail_post_mime_types) )
		continue;

	if ( isset($_GET['post_mime_type']) && wp_match_mime_types($mime_type, $_GET['post_mime_type']) )
		$class = ' class="current"';

	$type_links[] = '<li><a href="' . esc_url(add_query_arg(array('post_mime_type'=>$mime_type, 'paged'=>false))) . '"' . $class . '>' . sprintf( translate_nooped_plural( $label[2], $num_posts[$mime_type] ), '<span id="' . $mime_type . '-counter">' . number_format_i18n( $num_posts[$mime_type] ) . '</span>') . '</a>';
}
/**
 * Filter the media upload mime type list items.
 *
 * Returned values should begin with an `<li>` tag.
 *
 * @since 3.1.0
 *
 * @param array $type_links An array of list items containing mime type link HTML.
 */
echo implode(' | </li>', apply_filters( 'media_upload_mime_type_links', $type_links ) ) . '</li>';
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
	'current' => $q['paged'],
));

if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft actions">
<?php

$arc_query = "SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts WHERE post_type = 'attachment' ORDER BY post_date DESC";

$arc_result = $wpdb->get_results( $arc_query );

$month_count = count($arc_result);
$selected_month = isset( $_GET['m'] ) ? $_GET['m'] : 0;

if ( $month_count && !( 1 == $month_count && 0 == $arc_result[0]->mmonth ) ) { ?>
<select name='m'>
<option<?php selected( $selected_month, 0 ); ?> value='0'><?php _e( 'All dates' ); ?></option>
<?php
foreach ($arc_result as $arc_row) {
	if ( $arc_row->yyear == 0 )
		continue;
	$arc_row->mmonth = zeroise( $arc_row->mmonth, 2 );

	if ( $arc_row->yyear . $arc_row->mmonth == $selected_month )
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

<?php submit_button( __( 'Filter &#187;' ), 'button', 'post-query-submit', false ); ?>

</div>

<br class="clear" />
</div>
</form>

<form enctype="multipart/form-data" method="post" action="<?php echo esc_url( $form_action_url ); ?>" class="<?php echo $form_class; ?>" id="library-form">

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
<?php submit_button( __( 'Save all changes' ), 'button savebutton', 'save', false ); ?>
<input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
</p>
</form>
<?php
}

/**
 * Creates the form for external url
 *
 * @since 2.7.0
 *
 * @param string $default_view
 * @return string the form html
 */
function wp_media_insert_url_form( $default_view = 'image' ) {
	/** This filter is documented in wp-admin/includes/media.php */
	if ( ! apply_filters( 'disable_captions', '' ) ) {
		$caption = '
		<tr class="image-only">
			<th scope="row" class="label">
				<label for="caption"><span class="alignleft">' . __('Image Caption') . '</span></label>
			</th>
			<td class="field"><textarea id="caption" name="caption"></textarea></td>
		</tr>
';
	} else {
		$caption = '';
	}

	$default_align = get_option('image_default_align');
	if ( empty($default_align) )
		$default_align = 'none';

	if ( 'image' == $default_view ) {
		$view = 'image-only';
		$table_class = '';
	} else {
		$view = $table_class = 'not-image';
	}

	return '
	<p class="media-types"><label><input type="radio" name="media_type" value="image" id="image-only"' . checked( 'image-only', $view, false ) . ' /> ' . __( 'Image' ) . '</label> &nbsp; &nbsp; <label><input type="radio" name="media_type" value="generic" id="not-image"' . checked( 'not-image', $view, false ) . ' /> ' . __( 'Audio, Video, or Other File' ) . '</label></p>
	<table class="describe ' . $table_class . '"><tbody>
		<tr>
			<th scope="row" class="label" style="width:130px;">
				<label for="src"><span class="alignleft">' . __('URL') . '</span></label>
				<span class="alignright"><abbr id="status_img" title="required" class="required">*</abbr></span>
			</th>
			<td class="field"><input id="src" name="src" value="" type="text" aria-required="true" onblur="addExtImage.getImageData()" /></td>
		</tr>

		<tr>
			<th scope="row" class="label">
				<label for="title"><span class="alignleft">' . __('Title') . '</span></label>
				<span class="alignright"><abbr title="required" class="required">*</abbr></span>
			</th>
			<td class="field"><input id="title" name="title" value="" type="text" aria-required="true" /></td>
		</tr>

		<tr class="not-image"><td></td><td><p class="help">' . __('Link text, e.g. &#8220;Ransom Demands (PDF)&#8221;') . '</p></td></tr>

		<tr class="image-only">
			<th scope="row" class="label">
				<label for="alt"><span class="alignleft">' . __('Alternative Text') . '</span></label>
			</th>
			<td class="field"><input id="alt" name="alt" value="" type="text" aria-required="true" />
			<p class="help">' . __('Alt text for the image, e.g. &#8220;The Mona Lisa&#8221;') . '</p></td>
		</tr>
		' . $caption . '
		<tr class="align image-only">
			<th scope="row" class="label"><p><label for="align">' . __('Alignment') . '</label></p></th>
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

		<tr class="image-only">
			<th scope="row" class="label">
				<label for="url"><span class="alignleft">' . __('Link Image To:') . '</span></label>
			</th>
			<td class="field"><input id="url" name="url" value="" type="text" /><br />

			<button type="button" class="button" value="" onclick="document.forms[0].url.value=null">' . __('None') . '</button>
			<button type="button" class="button" value="" onclick="document.forms[0].url.value=document.forms[0].src.value">' . __('Link to image') . '</button>
			<p class="help">' . __('Enter a link URL or click above for presets.') . '</p></td>
		</tr>
		<tr class="image-only">
			<td></td>
			<td>
				<input type="button" class="button" id="go_button" style="color:#bbb;" onclick="addExtImage.insert()" value="' . esc_attr__('Insert into Post') . '" />
			</td>
		</tr>
		<tr class="not-image">
			<td></td>
			<td>
				' . get_submit_button( __( 'Insert into Post' ), 'button', 'insertonlybutton', false ) . '
			</td>
		</tr>
	</tbody></table>
';

}

/**
 * Displays the multi-file uploader message.
 *
 * @since 2.6.0
 */
function media_upload_flash_bypass() {
	$browser_uploader = admin_url( 'media-new.php?browser-uploader' );

	if ( $post = get_post() )
		$browser_uploader .= '&amp;post_id=' . intval( $post->ID );
	elseif ( ! empty( $GLOBALS['post_ID'] ) )
		$browser_uploader .= '&amp;post_id=' . intval( $GLOBALS['post_ID'] );

	?>
	<p class="upload-flash-bypass">
	<?php printf( __( 'You are using the multi-file uploader. Problems? Try the <a href="%1$s" target="%2$s">browser uploader</a> instead.' ), $browser_uploader, '_blank' ); ?>
	</p>
	<?php
}
add_action('post-plupload-upload-ui', 'media_upload_flash_bypass');

/**
 * Displays the browser's built-in uploader message.
 *
 * @since 2.6.0
 */
function media_upload_html_bypass() {
	?>
	<p class="upload-html-bypass hide-if-no-js">
	   <?php _e('You are using the browser&#8217;s built-in file uploader. The WordPress uploader includes multiple file selection and drag and drop capability. <a href="#">Switch to the multi-file uploader</a>.'); ?>
	</p>
	<?php
}
add_action('post-html-upload-ui', 'media_upload_html_bypass');

/**
 * Used to display a "After a file has been uploaded..." help message.
 *
 * @since 3.3.0
 */
function media_upload_text_after() {}

/**
 * Displays the checkbox to scale images.
 *
 * @since 3.3.0
 */
function media_upload_max_image_resize() {
	$checked = get_user_setting('upload_resize') ? ' checked="true"' : '';
	$a = $end = '';

	if ( current_user_can( 'manage_options' ) ) {
		$a = '<a href="' . esc_url( admin_url( 'options-media.php' ) ) . '" target="_blank">';
		$end = '</a>';
	}
?>
<p class="hide-if-no-js"><label>
<input name="image_resize" type="checkbox" id="image_resize" value="true"<?php echo $checked; ?> />
<?php
	/* translators: %1$s is link start tag, %2$s is link end tag, %3$d is width, %4$d is height*/
	printf( __( 'Scale images to match the large size selected in %1$simage options%2$s (%3$d &times; %4$d).' ), $a, $end, (int) get_option( 'large_size_w', '1024' ), (int) get_option( 'large_size_h', '1024' ) );
?>
</label></p>
<?php
}

/**
 * Displays the out of storage quota message in Multisite.
 *
 * @since 3.5.0
 */
function multisite_over_quota_message() {
	echo '<p>' . sprintf( __( 'Sorry, you have used all of your storage quota of %s MB.' ), get_space_allowed() ) . '</p>';
}

/**
 * Displays the image and editor in the post editor
 *
 * @since 3.5.0
 */
function edit_form_image_editor( $post ) {
	$open = isset( $_GET['image-editor'] );
	if ( $open )
		require_once ABSPATH . 'wp-admin/includes/image-edit.php';

	$thumb_url = false;
	if ( $attachment_id = intval( $post->ID ) )
		$thumb_url = wp_get_attachment_image_src( $attachment_id, array( 900, 450 ), true );

	$alt_text = get_post_meta( $post->ID, '_wp_attachment_image_alt', true );

	$att_url = wp_get_attachment_url( $post->ID ); ?>
	<div class="wp_attachment_holder">
	<?php
	if ( wp_attachment_is_image( $post->ID ) ) :
		$image_edit_button = '';
		if ( wp_image_editor_supports( array( 'mime_type' => $post->post_mime_type ) ) ) {
			$nonce = wp_create_nonce( "image_editor-$post->ID" );
			$image_edit_button = "<input type='button' id='imgedit-open-btn-$post->ID' onclick='imageEdit.open( $post->ID, \"$nonce\" )' class='button' value='" . esc_attr__( 'Edit Image' ) . "' /> <span class='spinner'></span>";
		}
	?>

		<div class="imgedit-response" id="imgedit-response-<?php echo $attachment_id; ?>"></div>

		<div<?php if ( $open ) echo ' style="display:none"'; ?> class="wp_attachment_image" id="media-head-<?php echo $attachment_id; ?>">
			<p id="thumbnail-head-<?php echo $attachment_id; ?>"><img class="thumbnail" src="<?php echo set_url_scheme( $thumb_url[0] ); ?>" style="max-width:100%" alt="" /></p>
			<p><?php echo $image_edit_button; ?></p>
		</div>
		<div<?php if ( ! $open ) echo ' style="display:none"'; ?> class="image-editor" id="image-editor-<?php echo $attachment_id; ?>">
			<?php if ( $open ) wp_image_editor( $attachment_id ); ?>
		</div>
	<?php
	elseif ( $attachment_id && 0 === strpos( $post->post_mime_type, 'audio/' ) ):

		wp_maybe_generate_attachment_metadata( $post );

		echo wp_audio_shortcode( array( 'src' => $att_url ) );

	elseif ( $attachment_id && 0 === strpos( $post->post_mime_type, 'video/' ) ):

		wp_maybe_generate_attachment_metadata( $post );

		$meta = wp_get_attachment_metadata( $attachment_id );
		$w = ! empty( $meta['width'] ) ? min( $meta['width'], 640 ) : 0;
		$h = ! empty( $meta['height'] ) ? $meta['height'] : 0;
		if ( $h && $w < $meta['width'] ) {
			$h = round( ( $meta['height'] * $w ) / $meta['width'] );
		}

		$attr = array( 'src' => $att_url );
		if ( ! empty( $w ) && ! empty( $h ) ) {
			$attr['width'] = $w;
			$attr['height'] = $h;
		}

		$thumb_id = get_post_thumbnail_id( $attachment_id );
		if ( ! empty( $thumb_id ) ) {
			$attr['poster'] = wp_get_attachment_url( $thumb_id );
		}

		echo wp_video_shortcode( $attr );

	endif; ?>
	</div>
	<div class="wp_attachment_details edit-form-section">
		<p>
			<label for="attachment_caption"><strong><?php _e( 'Caption' ); ?></strong></label><br />
			<textarea class="widefat" name="excerpt" id="attachment_caption"><?php echo $post->post_excerpt; ?></textarea>
		</p>


	<?php if ( 'image' === substr( $post->post_mime_type, 0, 5 ) ) : ?>
		<p>
			<label for="attachment_alt"><strong><?php _e( 'Alternative Text' ); ?></strong></label><br />
			<input type="text" class="widefat" name="_wp_attachment_image_alt" id="attachment_alt" value="<?php echo esc_attr( $alt_text ); ?>" />
		</p>
	<?php endif; ?>

	<?php
		$quicktags_settings = array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' );
		$editor_args = array(
			'textarea_name' => 'content',
			'textarea_rows' => 5,
			'media_buttons' => false,
			'tinymce' => false,
			'quicktags' => $quicktags_settings,
		);
	?>

	<label for="content"><strong><?php _e( 'Description' ); ?></strong><?php
	if ( preg_match( '#^(audio|video)/#', $post->post_mime_type ) ) {
		echo ': ' . __( 'Displayed on attachment pages.' );
	} ?></label>
	<?php wp_editor( $post->post_content, 'attachment_content', $editor_args ); ?>

	</div>
	<?php
	$extras = get_compat_media_markup( $post->ID );
	echo $extras['item'];
	echo '<input type="hidden" id="image-edit-context" value="edit-attachment" />' . "\n";
}

/**
 * Displays non-editable attachment metadata in the publish metabox
 *
 * @since 3.5.0
 */
function attachment_submitbox_metadata() {
	$post = get_post();

	$filename = esc_html( wp_basename( $post->guid ) );

	$media_dims = '';
	$meta = wp_get_attachment_metadata( $post->ID );
	if ( isset( $meta['width'], $meta['height'] ) )
		$media_dims .= "<span id='media-dims-$post->ID'>{$meta['width']}&nbsp;&times;&nbsp;{$meta['height']}</span> ";
	/** This filter is documented in wp-admin/includes/media.php */
	$media_dims = apply_filters( 'media_meta', $media_dims, $post );

	$att_url = wp_get_attachment_url( $post->ID );
?>
	<div class="misc-pub-section misc-pub-attachment">
			<label for="attachment_url"><?php _e( 'File URL:' ); ?></label>
			<input type="text" class="widefat urlfield" readonly="readonly" name="attachment_url" value="<?php echo esc_attr($att_url); ?>" />
	</div>
	<div class="misc-pub-section misc-pub-filename">
		<?php _e( 'File name:' ); ?> <strong><?php echo $filename; ?></strong>
	</div>
	<div class="misc-pub-section misc-pub-filetype">
		<?php _e( 'File type:' ); ?> <strong><?php
			if ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $post->ID ), $matches ) ) {
				echo esc_html( strtoupper( $matches[1] ) );
				list( $mime_type ) = explode( '/', $post->post_mime_type );
				if ( $mime_type !== 'image' && ! empty( $meta['mime_type'] ) ) {
					if ( $meta['mime_type'] !== "$mime_type/" . strtolower( $matches[1] ) ) {
						echo ' (' . $meta['mime_type'] . ')';
					}
				}
			} else {
				echo strtoupper( str_replace( 'image/', '', $post->post_mime_type ) );
			}
		?></strong>
	</div>

	<?php
		$file  = get_attached_file( $post->ID );
		$file_size = false;

		if ( isset( $meta['filesize'] ) )
			$file_size = $meta['filesize'];
		elseif ( file_exists( $file ) )
			$file_size = filesize( $file );

		if ( ! empty( $file_size ) ) : ?>
			<div class="misc-pub-section misc-pub-filesize">
				<?php _e( 'File size:' ); ?> <strong><?php echo size_format( $file_size ); ?></strong>
			</div>
			<?php
		endif;

	if ( preg_match( '#^(audio|video)/#', $post->post_mime_type ) ) {

		/**
		 * Filter the audio and video metadata fields to be shown in the publish meta box.
		 *
		 * The key for each item in the array should correspond to an attachment
		 * metadata key, and the value should be the desired label.
		 *
		 * @since 3.7.0
		 *
		 * @param array $fields An array of the attachment metadata keys and labels.
		 */
		$fields = apply_filters( 'media_submitbox_misc_sections', array(
			'length_formatted' => __( 'Length:' ),
			'bitrate'          => __( 'Bitrate:' ),
		) );

		foreach ( $fields as $key => $label ) {
			if ( empty( $meta[ $key ] ) ) {
				continue;
			}
	?>
		<div class="misc-pub-section misc-pub-mime-meta misc-pub-<?php echo sanitize_html_class( $key ); ?>">
			<?php echo $label ?> <strong><?php
				switch ( $key ) {
					case 'bitrate' :
						echo round( $meta['bitrate'] / 1000 ) . 'kb/s';
						if ( ! empty( $meta['bitrate_mode'] ) ) {
							echo ' ' . strtoupper( esc_html( $meta['bitrate_mode'] ) );
						}
						break;
					default:
						echo esc_html( $meta[ $key ] );
						break;
				}
			?></strong>
		</div>
	<?php
		}

		/**
		 * Filter the audio attachment metadata fields to be shown in the publish meta box.
		 *
		 * The key for each item in the array should correspond to an attachment
		 * metadata key, and the value should be the desired label.
		 *
		 * @since 3.7.0
		 *
		 * @param array $fields An array of the attachment metadata keys and labels.
		 */
		$audio_fields = apply_filters( 'audio_submitbox_misc_sections', array(
			'dataformat' => __( 'Audio Format:' ),
			'codec'      => __( 'Audio Codec:' )
		) );

		foreach ( $audio_fields as $key => $label ) {
			if ( empty( $meta['audio'][ $key ] ) ) {
				continue;
			}
	?>
		<div class="misc-pub-section misc-pub-audio misc-pub-<?php echo sanitize_html_class( $key ); ?>">
			<?php echo $label; ?> <strong><?php echo esc_html( $meta['audio'][$key] ); ?></strong>
		</div>
	<?php
		}

	}

	if ( $media_dims ) : ?>
	<div class="misc-pub-section misc-pub-dimensions">
		<?php _e( 'Dimensions:' ); ?> <strong><?php echo $media_dims; ?></strong>
	</div>
<?php
	endif;
}

add_filter( 'async_upload_image', 'get_media_item', 10, 2 );
add_filter( 'async_upload_audio', 'get_media_item', 10, 2 );
add_filter( 'async_upload_video', 'get_media_item', 10, 2 );
add_filter( 'async_upload_file',  'get_media_item', 10, 2 );

add_action( 'media_upload_image', 'wp_media_upload_handler' );
add_action( 'media_upload_audio', 'wp_media_upload_handler' );
add_action( 'media_upload_video', 'wp_media_upload_handler' );
add_action( 'media_upload_file',  'wp_media_upload_handler' );

add_filter( 'media_upload_gallery', 'media_upload_gallery' );
add_filter( 'media_upload_library', 'media_upload_library' );

add_action( 'attachment_submitbox_misc_actions', 'attachment_submitbox_metadata' );

/**
 * Parse ID3v2, ID3v1, and getID3 comments to extract usable data
 *
 * @since 3.6.0
 *
 * @param array $metadata An existing array with data
 * @param array $data Data supplied by ID3 tags
 */
function wp_add_id3_tag_data( &$metadata, $data ) {
	foreach ( array( 'id3v2', 'id3v1' ) as $version ) {
		if ( ! empty( $data[$version]['comments'] ) ) {
			foreach ( $data[$version]['comments'] as $key => $list ) {
				if ( 'length' !== $key && ! empty( $list ) ) {
					$metadata[$key] = reset( $list );
					// Fix bug in byte stream analysis.
					if ( 'terms_of_use' === $key && 0 === strpos( $metadata[$key], 'yright notice.' ) )
						$metadata[$key] = 'Cop' . $metadata[$key];
				}
			}
			break;
		}
	}

	if ( ! empty( $data['id3v2']['APIC'] ) ) {
		$image = reset( $data['id3v2']['APIC']);
		if ( ! empty( $image['data'] ) ) {
			$metadata['image'] = array(
				'data' => $image['data'],
				'mime' => $image['image_mime'],
				'width' => $image['image_width'],
				'height' => $image['image_height']
			);
		}
	} elseif ( ! empty( $data['comments']['picture'] ) ) {
		$image = reset( $data['comments']['picture'] );
		if ( ! empty( $image['data'] ) ) {
			$metadata['image'] = array(
				'data' => $image['data'],
				'mime' => $image['image_mime']
			);
		}
	}
}

/**
 * Retrieve metadata from a video file's ID3 tags
 *
 * @since 3.6.0
 *
 * @param string $file Path to file.
 * @return array|bool Returns array of metadata, if found.
 */
function wp_read_video_metadata( $file ) {
	if ( ! file_exists( $file ) )
		return false;

	$metadata = array();

	if ( ! class_exists( 'getID3' ) )
		require( ABSPATH . WPINC . '/ID3/getid3.php' );
	$id3 = new getID3();
	$data = $id3->analyze( $file );

	if ( isset( $data['video']['lossless'] ) )
		$metadata['lossless'] = $data['video']['lossless'];
	if ( ! empty( $data['video']['bitrate'] ) )
		$metadata['bitrate'] = (int) $data['video']['bitrate'];
	if ( ! empty( $data['video']['bitrate_mode'] ) )
		$metadata['bitrate_mode'] = $data['video']['bitrate_mode'];
	if ( ! empty( $data['filesize'] ) )
		$metadata['filesize'] = (int) $data['filesize'];
	if ( ! empty( $data['mime_type'] ) )
		$metadata['mime_type'] = $data['mime_type'];
	if ( ! empty( $data['playtime_seconds'] ) )
		$metadata['length'] = (int) round( $data['playtime_seconds'] );
	if ( ! empty( $data['playtime_string'] ) )
		$metadata['length_formatted'] = $data['playtime_string'];
	if ( ! empty( $data['video']['resolution_x'] ) )
		$metadata['width'] = (int) $data['video']['resolution_x'];
	if ( ! empty( $data['video']['resolution_y'] ) )
		$metadata['height'] = (int) $data['video']['resolution_y'];
	if ( ! empty( $data['fileformat'] ) )
		$metadata['fileformat'] = $data['fileformat'];
	if ( ! empty( $data['video']['dataformat'] ) )
		$metadata['dataformat'] = $data['video']['dataformat'];
	if ( ! empty( $data['video']['encoder'] ) )
		$metadata['encoder'] = $data['video']['encoder'];
	if ( ! empty( $data['video']['codec'] ) )
		$metadata['codec'] = $data['video']['codec'];

	if ( ! empty( $data['audio'] ) ) {
		unset( $data['audio']['streams'] );
		$metadata['audio'] = $data['audio'];
	}

	wp_add_id3_tag_data( $metadata, $data );

	return $metadata;
}

/**
 * Retrieve metadata from a audio file's ID3 tags
 *
 * @since 3.6.0
 *
 * @param string $file Path to file.
 * @return array|boolean Returns array of metadata, if found.
 */
function wp_read_audio_metadata( $file ) {
	if ( ! file_exists( $file ) )
		return false;
	$metadata = array();

	if ( ! class_exists( 'getID3' ) )
		require( ABSPATH . WPINC . '/ID3/getid3.php' );
	$id3 = new getID3();
	$data = $id3->analyze( $file );

	if ( ! empty( $data['audio'] ) ) {
		unset( $data['audio']['streams'] );
		$metadata = $data['audio'];
	}

	if ( ! empty( $data['fileformat'] ) )
		$metadata['fileformat'] = $data['fileformat'];
	if ( ! empty( $data['filesize'] ) )
		$metadata['filesize'] = (int) $data['filesize'];
	if ( ! empty( $data['mime_type'] ) )
		$metadata['mime_type'] = $data['mime_type'];
	if ( ! empty( $data['playtime_seconds'] ) )
		$metadata['length'] = (int) round( $data['playtime_seconds'] );
	if ( ! empty( $data['playtime_string'] ) )
		$metadata['length_formatted'] = $data['playtime_string'];

	wp_add_id3_tag_data( $metadata, $data );

	return $metadata;
}
