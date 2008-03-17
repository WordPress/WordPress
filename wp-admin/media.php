<?php

require_once('admin.php');

$parent_file = 'edit.php';
$submenu_file = 'upload.php';

wp_reset_vars(array('action'));

switch( $action ) :
case 'editattachment' :
	$errors = media_upload_form_handler();
	if ( empty($errors) ) {
		wp_redirect( add_query_arg( 'message', 'updated' ) );
		exit;
		break;
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
	$att = get_post($att_id);

	add_filter('attachment_fields_to_edit', 'media_single_attachment_fields_to_edit', 10, 2);

	wp_enqueue_script( 'wp-ajax-response' );
	add_action('admin_head', 'media_admin_css');

	require( 'admin-header.php' );

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

<h2><?php _e( 'Edit Media' ); ?></h2>

<form method="post" action="<?php echo clean_url( remove_query_arg( 'message' ) ); ?>" class="media-upload-form" id="media-single-form">
<div id="media-items" class="media-single">
<div id='media-item-<?php echo $att_id; ?>' class='media-item'>
<?php echo get_media_item( $att_id, array( 'toggle' => false, 'send' => false, 'delete' => false, 'errors' => $errors ) ); ?>
</div>
</div>

<p class="submit">
<input type="submit" class="button" name="save" value="<?php _e('Save Changes'); ?>" />
<input type="hidden" name="post_id" id="post_id" value="<?php echo $post_id; ?>" />
<input type="hidden" name="action" value="editattachment" />
<?php wp_nonce_field('media-form'); ?>
</p>


</div>

<?php

	require( 'admin-footer.php' );

	exit;

default:
	wp_redirect( 'upload.php' );
	exit;

endswitch;


?>
