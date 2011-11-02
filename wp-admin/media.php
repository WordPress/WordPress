<?php
/**
 * Media management action handler.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Administration Bootstrap */
require_once('./admin.php');

$parent_file = 'upload.php';
$submenu_file = 'upload.php';

wp_reset_vars(array('action'));

switch( $action ) :
case 'editattachment' :
	$attachment_id = (int) $_POST['attachment_id'];
	check_admin_referer('media-form');

	if ( !current_user_can('edit_post', $attachment_id) )
		wp_die ( __('You are not allowed to edit this attachment.') );

	$errors = media_upload_form_handler();

	if ( empty($errors) ) {
		$location = 'media.php';
		if ( $referer = wp_get_original_referer() ) {
			if ( false !== strpos($referer, 'upload.php') || ( url_to_postid($referer) == $attachment_id )  )
				$location = $referer;
		}
		if ( false !== strpos($location, 'upload.php') ) {
			$location = remove_query_arg('message', $location);
			$location = add_query_arg('posted',	$attachment_id, $location);
		} elseif ( false !== strpos($location, 'media.php') ) {
			$location = add_query_arg('message', 'updated', $location);
		}
		wp_redirect($location);
		exit;
	}

	// no break
case 'edit' :
	$title = __('Edit Media');

	if ( empty($errors) )
		$errors = null;

	if ( empty( $_GET['attachment_id'] ) ) {
		wp_redirect( admin_url('upload.php') );
		exit();
	}
	$att_id = (int) $_GET['attachment_id'];

	if ( !current_user_can('edit_post', $att_id) )
		wp_die ( __('You are not allowed to edit this attachment.') );

	$att = get_post($att_id);

	if ( empty($att->ID) ) wp_die( __('You attempted to edit an attachment that doesn&#8217;t exist. Perhaps it was deleted?') );
	if ( $att->post_status == 'trash' ) wp_die( __('You can&#8217;t edit this attachment because it is in the Trash. Please move it out of the Trash and try again.') );

	add_filter('attachment_fields_to_edit', 'media_single_attachment_fields_to_edit', 10, 2);

	wp_enqueue_script( 'wp-ajax-response' );
	wp_enqueue_script('image-edit');
	wp_enqueue_style('imgareaselect');

	add_contextual_help( $current_screen,
	'<p>' . __('This screen allows you to edit five fields for metadata in a file within the media library.') . '</p>' .
	'<p>' . __('For images only, you can click on Edit Image under the thumbnail to expand out an inline image editor with icons for cropping, rotating, or flipping the image as well as for undoing and redoing. The boxes on the right give you more options for scaling the image, for cropping it, and for cropping the thumbnail in a different way than you crop the original image. You can click on Help in those boxes to get more information.') . '</p>' .
	'<p>' . __('Note that you crop the image by clicking on it (the Crop icon is already selected) and dragging the cropping frame to select the desired part. Then click Save to retain the cropping.') . '</p>' .
	'<p>' . __('Remember to click Update Media to save metadata entered or changed.') . '</p>'
	);

	get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Media_Add_New_Screen#Edit_Media" target="_blank">Documentation on Edit Media</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
	);

	require( './admin-header.php' );

	$parent_file = 'upload.php';
	$message = '';
	$class = '';
	if ( isset($_GET['message']) ) {
		switch ( $_GET['message'] ) :
		case 'updated' :
			$message = __('Media attachment updated.');
			$class = 'updated';
			break;
		endswitch;
	}
	if ( $message )
		echo "<div id='message' class='$class'><p>$message</p></div>\n";

?>

<div class="wrap">
<?php screen_icon(); ?>
<h2>
<?php
echo esc_html( $title );
if ( current_user_can( 'upload_files' ) ) { ?>
	<a href="media-new.php" class="add-new-h2"><?php echo esc_html_x('Add New', 'file'); ?></a>
<?php } ?>
</h2>

<form method="post" action="" class="media-upload-form" id="media-single-form">
<p class="submit" style="padding-bottom: 0;">
<?php submit_button( __( 'Update Media' ), 'primary', 'save', false ); ?>
</p>

<div class="media-single">
<div id='media-item-<?php echo $att_id; ?>' class='media-item'>
<?php echo get_media_item( $att_id, array( 'toggle' => false, 'send' => false, 'delete' => false, 'show_title' => false, 'errors' => !empty($errors[$att_id]) ? $errors[$att_id] : null ) ); ?>
</div>
</div>

<?php submit_button( __( 'Update Media' ), 'primary', 'save' ); ?>
<input type="hidden" name="post_id" id="post_id" value="<?php echo isset($post_id) ? esc_attr($post_id) : ''; ?>" />
<input type="hidden" name="attachment_id" id="attachment_id" value="<?php echo esc_attr($att_id); ?>" />
<input type="hidden" name="action" value="editattachment" />
<?php wp_original_referer_field(true, 'previous'); ?>
<?php wp_nonce_field('media-form'); ?>

</form>

</div>

<?php

	require( './admin-footer.php' );

	exit;

default:
	wp_redirect( admin_url('upload.php') );
	exit;

endswitch;


?>
