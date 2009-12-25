<?php
/**
 * Media management action handler.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Administration Bootstrap */
require_once('admin.php');

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
		wp_redirect('upload.php');
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

	require( 'admin-header.php' );

	$parent_file = 'upload.php';
	$message = '';
	$class = '';
	if ( isset($_GET['message']) ) {
		switch ( $_GET['message'] ) :
		case 'updated' :
			$message = __('Media attachment updated.');
			$class = 'updated fade';
			break;
		endswitch;
	}
	if ( $message )
		echo "<div id='message' class='$class'><p>$message</p></div>\n";

?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e( 'Edit Media' ); ?></h2>

<form method="post" action="<?php echo esc_url( remove_query_arg( 'message' ) ); ?>" class="media-upload-form" id="media-single-form">
<p class="submit" style="padding-bottom: 0;">
<input type="submit" class="button-primary" name="save" value="<?php esc_attr_e('Update Media'); ?>" />
</p>

<div class="media-single">
<div id='media-item-<?php echo $att_id; ?>' class='media-item'>
<?php echo get_media_item( $att_id, array( 'toggle' => false, 'send' => false, 'delete' => false, 'show_title' => false, 'errors' => $errors ) ); ?>
</div>
</div>

<p class="submit">
<input type="submit" class="button-primary" name="save" value="<?php esc_attr_e('Update Media'); ?>" />
<input type="hidden" name="post_id" id="post_id" value="<?php echo isset($post_id) ? esc_attr($post_id) : ''; ?>" />
<input type="hidden" name="attachment_id" id="attachment_id" value="<?php echo esc_attr($att_id); ?>" />
<input type="hidden" name="action" value="editattachment" />
<?php wp_original_referer_field(true, 'previous'); ?>
<?php wp_nonce_field('media-form'); ?>
</p>
</form>

</div>

<?php

	require( 'admin-footer.php' );

	exit;

default:
	wp_redirect( 'upload.php' );
	exit;

endswitch;


?>
