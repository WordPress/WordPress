<?php
function wp_upload_display( $dims = false, $href = '' ) {
	global $post;
	$id = get_the_ID();
	$attachment_data = wp_get_attachment_metadata( $id );
	$is_image = (int) wp_attachment_is_image();
	if ( !isset($attachment_data['width']) && $is_image ) {
		if ( $image_data = getimagesize( get_attached_file( $id ) ) ) {
			$attachment_data['width'] = $image_data[0];
			$attachment_data['height'] = $image_data[1];
			wp_update_attachment_metadata( $id, $attachment_data );
		}
	}
	if ( isset($attachment_data['width']) )
		list($width,$height) = wp_shrink_dimensions($attachment_data['width'], $attachment_data['height'], 171, 128);
		
	ob_start();
		the_title();
		$post_title = attribute_escape(ob_get_contents());
	ob_end_clean();
	$post_content = apply_filters( 'content_edit_pre', $post->post_content );
	
	$class = 'text';
	$innerHTML = get_attachment_innerHTML( $id, false, $dims );
	if ( $image_src = get_attachment_icon_src() ) {
		$image_rel = wp_make_link_relative($image_src);
		$innerHTML = '&nbsp;' . str_replace($image_src, $image_rel, $innerHTML);
		$class = 'image';
	}

	$src_base = wp_get_attachment_url();
	$src = wp_make_link_relative( $src_base );
	$src_base = str_replace($src, '', $src_base);

	$r = '';

	if ( $href )
		$r .= "<a id='file-link-$id' href='$href' title='$post_title' class='file-link $class'>\n";
	if ( $href || $image_src )
		$r .= "\t\t\t$innerHTML";
	if ( $href )
		$r .= "</a>\n";
	$r .= "\n\t\t<div class='upload-file-data'>\n\t\t\t<p>\n";
	$r .= "\t\t\t\t<input type='hidden' name='attachment-url-$id' id='attachment-url-$id' value='$src' />\n";
	$r .= "\t\t\t\t<input type='hidden' name='attachment-url-base-$id' id='attachment-url-base-$id' value='$src_base' />\n";

	if ( !$thumb_base = wp_get_attachment_thumb_url() )
		$thumb_base = wp_mime_type_icon();
	if ( $thumb_base ) {
		$thumb_rel = wp_make_link_relative( $thumb_base );
		$thumb_base = str_replace( $thumb_rel, '', $thumb_base );
		$r .= "\t\t\t\t<input type='hidden' name='attachment-thumb-url-$id' id='attachment-thumb-url-$id' value='$thumb_rel' />\n";
		$r .= "\t\t\t\t<input type='hidden' name='attachment-thumb-url-base-$id' id='attachment-thumb-url-base-$id' value='$thumb_base' />\n";
	}

	$r .= "\t\t\t\t<input type='hidden' name='attachment-is-image-$id' id='attachment-is-image-$id' value='$is_image' />\n";

	if ( isset($width) ) {
		$r .= "\t\t\t\t<input type='hidden' name='attachment-width-$id' id='attachment-width-$id' value='$width' />\n";
		$r .= "\t\t\t\t<input type='hidden' name='attachment-height-$id' id='attachment-height-$id' value='$height' />\n";
	}
	$r .= "\t\t\t\t<input type='hidden' name='attachment-page-url-$id' id='attachment-page-url-$id' value='" . get_attachment_link( $id ) . "' />\n";
	$r .= "\t\t\t\t<input type='hidden' name='attachment-title-$id' id='attachment-title-$id' value='$post_title' />\n";
	$r .= "\t\t\t\t<input type='hidden' name='attachment-description-$id' id='attachment-description-$id' value='$post_content' />\n";
	$r .= "\t\t\t</p>\n\t\t</div>\n";
	return $r;
}

function wp_upload_view() {
	global $style, $post_id, $style;
	$id = get_the_ID();
	$attachment_data = wp_get_attachment_metadata( $id );
?>
	<div id="upload-file">
		<div id="file-title">
			<h2><?php if ( !isset($attachment_data['width']) && 'inline' != $style )
					echo "<a href='" . wp_get_attachment_url() . "' title='" . __('Direct link to file') . "'>";
				the_title();
				if ( !isset($attachment_data['width']) && 'inline' != $style )
					echo '</a>';
			?></h2>
			<span><?php
				echo '[&nbsp;';
				echo '<a href="' . get_permalink() . '">' . __('view') . '</a>';
				echo '&nbsp;|&nbsp;';
					echo '<a href="' . attribute_escape(add_query_arg('action', 'edit')) . '" title="' . __('Edit this file') . '">' . __('edit') . '</a>';
				echo '&nbsp;|&nbsp;';
				echo '<a href="' . attribute_escape(remove_query_arg(array('action', 'ID'))) . '" title="' . __('Browse your files') . '">' . __('cancel') . '</a>';
				echo '&nbsp;]'; ?></span>
		</div>

		<div id="upload-file-view" class="alignleft">
<?php		if ( isset($attachment_data['width']) && 'inline' != $style )
			echo "<a href='" . wp_get_attachment_url() . "' title='" . __('Direct link to file') . "'>";
		echo wp_upload_display( array(171, 128) );
		if ( isset($attachment_data['width']) && 'inline' != $style )
			echo '</a>'; ?>
		</div>
		<?php the_attachment_links( $id ); ?>
	</div>
<?php	echo "<form action='' id='browse-form'><input type='hidden' id='nonce-value' value='" . wp_create_nonce( 'inlineuploading' )  . "' /></form>\n";
}

function wp_upload_form() {
	$id = get_the_ID();
	global $post_id, $tab, $style;
	$enctype = $id ? '' : ' enctype="multipart/form-data"';
?>
	<form<?php echo $enctype; ?> id="upload-file" method="post" action="<?php echo get_option('siteurl') . "/wp-admin/upload.php?style=$style&amp;tab=upload&amp;post_id=$post_id"; ?>">
<?php
	if ( $id ) :
		$attachment = get_post_to_edit( $id );
		$attachment_data = wp_get_attachment_metadata( $id );
?>
		<div id="file-title">
			<h2><?php if ( !isset($attachment_data['width']) && 'inline' != $style )
					echo "<a href='" . wp_get_attachment_url() . "' title='" . __('Direct link to file') . "'>";
				the_title();
				if ( !isset($attachment_data['width']) && 'inline' != $style )
					echo '</a>';
			?></h2>
			<span><?php
				echo '[&nbsp;';
				echo '<a href="' . get_permalink() . '">' . __('view') . '</a>';
				echo '&nbsp;|&nbsp;';
					echo '<a href="' . attribute_escape(add_query_arg('action', 'view')) . '">' . __('links') . '</a>';
				echo '&nbsp;|&nbsp;';
				echo '<a href="' . attribute_escape(remove_query_arg(array('action','ID'))) . '" title="' . __('Browse your files') . '">' . __('cancel') . '</a>';
				echo '&nbsp;]'; ?></span>
		</div>

	<div id="upload-file-view" class="alignleft">
<?php		if ( isset($attachment_data['width']) && 'inline' != $style )
			echo "<a href='" . wp_get_attachment_url() . "' title='" . __('Direct link to file') . "'>";
		echo wp_upload_display( array(171, 128) );
		if ( isset($attachment_data['width']) && 'inline' != $style )
			echo '</a>'; ?>
	</div>
<?php	endif; ?>
		<table><col /><col class="widefat" />
<?php	if ( $id ): ?>
			<tr>
				<th scope="row"><label for="url"><?php _e('URL'); ?></label></th>
				<td><input type="text" id="url" class="readonly" value="<?php echo wp_get_attachment_url(); ?>" readonly="readonly" /></td>
			</tr>
<?php	else : ?>
			<tr>
				<th scope="row"><label for="upload"><?php _e('File'); ?></label></th>
				<td><input type="file" id="upload" name="image" /></td>
			</tr>
<?php	endif; ?>
			<tr>
				<th scope="row"><label for="post_title"><?php _e('Title'); ?></label></th>
				<td><input type="text" id="post_title" name="post_title" value="<?php echo $attachment->post_title; ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="post_content"><?php _e('Description'); ?></label></th>
				<td><textarea name="post_content" id="post_content"><?php echo $attachment->post_content; ?></textarea></td>
			</tr>
			<tr id="buttons" class="submit">
				<td colspan='2'>
<?php	if ( $id ) : ?>
					<input type="submit" name="delete" id="delete" class="delete alignleft" value="<?php _e('Delete File'); ?>" />
<?php	endif; ?>
					<input type="hidden" name="from_tab" value="<?php echo $tab; ?>" />
					<input type="hidden" name="action" value="<?php echo $id ? 'save' : 'upload'; ?>" />
<?php	if ( $post_id ) : ?>
					<input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
<?php	endif; if ( $id ) : ?>
					<input type="hidden" name="ID" value="<?php echo $id; ?>" />
<?php	endif; ?>
					<?php wp_nonce_field( 'inlineuploading' ); ?>
					<div class="submit">
						<input type="submit" value="<?php $id ? _e('Save') : _e('Upload'); ?> &raquo;" />
					</div>
				</td>
			</tr>
		</table>
	</form>
<?php
}

function wp_upload_tab_upload() {
	wp_upload_form();
}

function wp_upload_tab_upload_action() {
	global $action;
	if ( isset($_POST['delete']) )
		$action = 'delete';

	switch ( $action ) :
	case 'upload' :
		global $from_tab, $post_id, $style;
		if ( !$from_tab )
			$from_tab = 'upload';

		check_admin_referer( 'inlineuploading' );

		global $post_id, $post_title, $post_content;

		if ( !current_user_can( 'upload_files' ) )
			wp_die( __('You are not allowed to upload files.')
				. " <a href='" . get_option('siteurl') . "/wp-admin/upload.php?style=$style&amp;tab=browse-all&amp;post_id=$post_id'>"
				. __('Browse Files') . '</a>'
			);

		$overrides = array('action'=>'upload');

		$file = wp_handle_upload($_FILES['image'], $overrides);

		if ( isset($file['error']) )
			wp_die($file['error'] . "<br /><a href='" . get_option('siteurl')
			. "/wp-admin/upload.php?style=$style&amp;tab=$from_tab&amp;post_id=$post_id'>" . __('Back to Image Uploading') . '</a>'
		);

		$url = $file['url'];
		$type = $file['type'];
		$file = $file['file'];
		$filename = basename($file);

		// Construct the attachment array
		$attachment = array(
			'post_title' => $post_title ? $post_title : $filename,
			'post_content' => $post_content,
			'post_type' => 'attachment',
			'post_parent' => $post_id,
			'post_mime_type' => $type,
			'guid' => $url
		);

		// Save the data
		$id = wp_insert_attachment($attachment, $file, $post_id);

		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );

		wp_redirect( get_option('siteurl') . "/wp-admin/upload.php?style=$style&tab=browse&action=view&ID=$id&post_id=$post_id");
		die;
		break;

	case 'save' :
		global $from_tab, $post_id, $style;
		if ( !$from_tab )
			$from_tab = 'upload';
		check_admin_referer( 'inlineuploading' );

		wp_update_post($_POST);
		wp_redirect( get_option('siteurl') . "/wp-admin/upload.php?style=$style&tab=$from_tab&post_id=$post_id");
		die;
		break;

	case 'delete' :
		global $ID, $post_id, $from_tab, $style;
		if ( !$from_tab )
			$from_tab = 'upload';

		check_admin_referer( 'inlineuploading' );

		if ( !current_user_can('edit_post', (int) $ID) )
			wp_die( __('You are not allowed to delete this attachment.')
				. " <a href='" . get_option('siteurl') . "/wp-admin/upload.php?style=$style&amp;tab=$from_tab&amp;post_id=$post_id'>"
				. __('Go back') . '</a>'
			);

		wp_delete_attachment($ID);

		wp_redirect( get_option('siteurl') . "/wp-admin/upload.php?style=$style&tab=$from_tab&post_id=$post_id" );
		die;
		break;

	endswitch;
}

add_action( 'upload_files_upload', 'wp_upload_tab_upload_action' );

function wp_upload_grab_attachments( $obj ) {
	$obj->is_attachment = true;
}

function wp_upload_posts_where( $where ) {
	global $post_id;
	return $where . " AND post_parent = '" . (int) $post_id . "'";
}

function wp_upload_tab_browse() {
	global $wpdb, $action, $paged;
	$old_vars = compact( 'paged' );
	
	switch ( $action ) :
	case 'edit' :
	case 'view' :
		global $ID;
		$attachments = query_posts("attachment_id=$ID");
		if ( have_posts() ) : while ( have_posts() ) : the_post();
			'edit' == $action ? wp_upload_form() : wp_upload_view();
		endwhile; endif;
		break;
	default :
		global $tab, $post_id, $style;
		add_action( 'pre_get_posts', 'wp_upload_grab_attachments' );
		if ( 'browse' == $tab && $post_id )
			add_filter( 'posts_where', 'wp_upload_posts_where' );
		$attachments = query_posts("what_to_show=posts&posts_per_page=10&paged=$paged");
		$count_query = "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'attachment'";
		if ( $post_id )
			$count_query .= " AND post_parent = '$post_id'";
        	$total =  $wpdb->get_var($count_query);

		echo "<ul id='upload-files'>\n";
		if ( have_posts() ) : while ( have_posts() ) : the_post();
			$href = wp_specialchars( add_query_arg( array(
				'action' => 'inline' == $style ? 'view' : 'edit',
				'ID' => get_the_ID())
			 ), 1 );

			echo "\t<li id='file-";
			the_ID();
			echo "' class='alignleft'>\n";
			echo wp_upload_display( array(128,128), $href );
			echo "\t</li>\n";
		endwhile;
		else :
			echo "\t<li>" . __('There are no attachments to show.') . "</li>\n";
		endif;
		echo "</ul>\n\n";

		echo "<form action='' id='browse-form'><input type='hidden' id='nonce-value' value='" . wp_create_nonce( 'inlineuploading' )  . "' /></form>\n";
		break;
	endswitch;

	extract($old_vars);
}


function wp_upload_tab_browse_action() {
	global $style;
	if ( 'inline' == $style )
		wp_enqueue_script('upload');
}

add_action( 'upload_files_browse', 'wp_upload_tab_browse_action' );
add_action( 'upload_files_browse-all', 'wp_upload_tab_browse_action' );

function wp_upload_admin_head() {
	global $wp_locale;
	echo "<link rel='stylesheet' href='" . get_option('siteurl') . '/wp-admin/upload.css?version=' . get_bloginfo('version') . "a' type='text/css' />\n";
	if ( 'rtl' == $wp_locale->text_direction )
		echo "<link rel='stylesheet' href='" . get_option('siteurl') . '/wp-admin/upload-rtl.css?version=' . get_bloginfo('version') . "a' type='text/css' />\n";
	if ( 'inline' == @$_GET['style'] ) {
		echo "<style type='text/css' media='screen'>\n";
		echo "\t#upload-menu { position: absolute; z-index: 2; }\n";
		echo "\tbody > #upload-menu { position: fixed; }\n";
		echo "\t#upload-content { top: 2em; }\n";
		echo "\t#upload-file { position: absolute; top: 15px; }\n";
		echo "</style>";
	}
}
