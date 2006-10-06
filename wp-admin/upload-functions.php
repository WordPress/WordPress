<?php
function wp_upload_display( $dims = false, $href = '' ) {
	global $post;
	$id = get_the_ID();
	$attachment_data = get_post_meta( $id, '_wp_attachment_metadata', true );
	if ( isset($attachment_data['width']) )
		list($width,$height) = wp_shrink_dimensions($attachment_data['width'], $attachment_data['height'], 171, 128);
	ob_start();
		the_title();
		$post_title = wp_specialchars( ob_get_contents(), 1 );
	ob_end_clean();
	$post_content = apply_filters( 'content_edit_pre', $post->post_content );
	
	$class = 'text';
	$innerHTML = get_attachment_innerHTML( $id, false, $dims );
	if ( $image_src = strstr($innerHTML, 'src="') ) {
		$image_src = explode('"', $image_src);
		$image_src = $image_src[1];
		$class = 'image';
		$innerHTML = '&nbsp;' . $innerHTML;
	}

	$r = '';

	if ( $href )
		$r .= "<a id='file-link-$id' href='$href' title='$post_title' class='file-link $class'>\n";
	if ( $href || $image_src )
		$r .= "\t\t\t$innerHTML";
	if ( $href )
		$r .= "</a>\n";
	$r .= "\n\t\t<div class='upload-file-data'>\n\t\t\t<p>\n";
	$r .= "\t\t\t\t<input type='hidden' name='attachment-url-$id' id='attachment-url-$id' value='" . get_the_guid() . "' />\n";

	if ( $image_src )
		$r .= "\t\t\t\t<input type='hidden' name='attachment-thumb-url-$id' id='attachment-thumb-url-$id' value='$image_src' />\n";
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
	global $style, $post_id;
	$id = get_the_ID();
	$attachment_data = get_post_meta( $id, '_wp_attachment_metadata', true );
?>
	<div id="upload-file">
		<div id="file-title">
			<h2><?php if ( !isset($attachment_data['width']) )
					echo "<a href='" . get_the_guid() . "' title='" . __('Direct link to file') . "'>";
				the_title();
				if ( !isset($attachment_data['width']) )
					echo '</a>';
			?></h2>
			<span><?php
				echo '[&nbsp;';
				echo '<a href="' . get_permalink() . '">' . __('view') . '</a>';
				echo '&nbsp;|&nbsp;';
					echo '<a href="' . wp_specialchars( add_query_arg( 'action', 'edit' ), 1 ) . '" title="' . __('Edit this file') . '">' . __('edit') . '</a>';
				echo '&nbsp;|&nbsp;';
				echo '<a href="' . wp_specialchars( remove_query_arg( array('action', 'ID') ), 1 ) . '" title="' . __('Browse your files') . '">' . __('cancel') . '</a>';
				echo '&nbsp;]'; ?></span>
		</div>

		<div id="upload-file-view" class="alignleft">
<?php		if ( isset($attachment_data['width']) )
			echo "<a href='" . get_the_guid() . "' title='" . __('Direct link to file') . "'>";
		echo wp_upload_display( array(171, 128) );
		if ( isset($attachment_data['width']) )
			echo '</a>'; ?>
		</div>
		<?php the_attachment_links( $id ); ?>
	</div>
<?php
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
		$attachment_data = get_post_meta( $id, '_wp_attachment_metadata', true );
?>
		<div id="file-title">
			<h2><?php if ( !isset($attachment_data['width']) )
					echo "<a href='" . get_the_guid() . "' title='" . __('Direct link to file') . "'>";
				the_title();
				if ( !isset($attachment_data['width']) )
					echo '</a>';
			?></h2>
			<span><?php
				echo '[&nbsp;';
				echo '<a href="' . get_permalink() . '">' . __('view') . '</a>';
				echo '&nbsp;|&nbsp;';
					echo '<a href="' . wp_specialchars( add_query_arg( 'action', 'view' ), 1 ) . '">' . __('links') . '</a>';
				echo '&nbsp;|&nbsp;';
				echo '<a href="' . wp_specialchars( remove_query_arg( array('action','ID') ), 1 ) . '" title="' . __('Browse your files') . '">' . __('cancel') . '</a>';
				echo '&nbsp;]'; ?></span>
		</div>

	<div id="upload-file-view" class="alignleft">
<?php		if ( isset($attachment_data['width']) )
			echo "<a href='" . get_the_guid() . "' title='" . __('Direct link to file') . "'>";
		echo wp_upload_display( array(171, 128) );
		if ( isset($attachment_data['width']) )
			echo '</a>'; ?>
	</div>
<?php	endif; ?>
		<table>
<?php	if ( !$id ): ?>
			<tr>
				<th scope="row"><label for="upload"><?php _e('File:'); ?></label></th>
				<td><input type="file" id="upload" name="image" /></td>
			</tr>
<?php	endif; ?>
			<tr>
				<th scope="row"><label for="post_title"><?php _e('Title:'); ?></label></th>
				<td><input type="text" id="post_title" name="post_title" value="<?php echo $attachment->post_title; ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="post_content"><?php _e('Description:'); ?></label></th>
				<td><textarea name="post_content" id="post_content"><?php echo $attachment->post_content; ?></textarea></td>
			</tr>
			<tr id="buttons">
				<th></th>
				<td>
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
<?php	if ( $id ) : ?>
						<input type="submit" name="delete" class="delete" value="<?php _e('Delete'); ?>" />
<?php	endif; ?>
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
			. "/wp-admin/upload.php?style=$style&amp;tab=$from_tab&amp;post_id=$post_id'>'" . __('Back to Image Uploading') . '</a>'
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

		if ( preg_match('!^image/!', $attachment['post_mime_type']) ) {
			// Generate the attachment's postmeta.
			$imagesize = getimagesize($file);
			$imagedata['width'] = $imagesize['0'];
			$imagedata['height'] = $imagesize['1'];
			list($uwidth, $uheight) = get_udims($imagedata['width'], $imagedata['height']);
			$imagedata['hwstring_small'] = "height='$uheight' width='$uwidth'";
			$imagedata['file'] = $file;

			add_post_meta($id, '_wp_attachment_metadata', $imagedata);

			if ( $imagedata['width'] * $imagedata['height'] < 3 * 1024 * 1024 ) {
				if ( $imagedata['width'] > 128 && $imagedata['width'] >= $imagedata['height'] * 4 / 3 )
					$thumb = wp_create_thumbnail($file, 128);
				elseif ( $imagedata['height'] > 96 )
					$thumb = wp_create_thumbnail($file, 96);

				if ( @file_exists($thumb) ) {
					$newdata = $imagedata;
					$newdata['thumb'] = basename($thumb);
					update_post_meta($id, '_wp_attachment_metadata', $newdata, $imagedata);
				} else {
					$error = $thumb;
				}
			}
		} else {
			add_post_meta($id, '_wp_attachment_metadata', array());
		}

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
	echo "<link rel='stylesheet' href='" . get_option('siteurl') . '/wp-admin/upload.css?version=' . get_bloginfo('version') . "' type='text/css' />\n";
	if ( 'rtl' == $wp_locale->text_direction )
		echo "<link rel='stylesheet' href='" . get_option('siteurl') . '/wp-admin/upload-rtl.css?version=' . get_bloginfo('version') . "' type='text/css' />\n";
	if ( 'inline' == @$_GET['style'] ) {
		echo "<style type='text/css'>\n";
		echo "\tbody { height: 14em; overflow: hidden; }\n";
		echo "\t#upload-content { overflow-y: auto; }\n";
		echo "\t#upload-file { position: absolute; }\n";
		echo "</style>";
	}
}

