<?php
/**
 * Manage media uploaded file.
 *
 * There are many filters in here for media. Plugins can extend functionality
 * by hooking into the filters.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Administration Bootstrap */
require_once('admin.php');

if (!current_user_can('upload_files'))
	wp_die(__('You do not have permission to upload files.'));

wp_enqueue_script('swfupload-all');
wp_enqueue_script('swfupload-handlers');

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

// IDs should be integers
$ID = isset($ID) ? (int) $ID : 0;
$post_id = isset($post_id)? (int) $post_id : 0;

// Require an ID for the edit screen
if ( isset($action) && $action == 'edit' && !$ID )
	wp_die(__("You are not allowed to be here"));

if ( isset($_GET['inline']) ) {
	$errors = array();

	if ( isset($_POST['html-upload']) && !empty($_FILES) ) {
		// Upload File button was clicked
		$id = media_handle_upload('async-upload', $_REQUEST['post_id']);
		unset($_FILES);
		if ( is_wp_error($id) ) {
			$errors['upload_error'] = $id;
			$id = false;
		}
	}

	if ( isset($_GET['upload-page-form']) ) {
		$errors = array_merge($errors, (array) media_upload_form_handler());

		$location = 'upload.php';
		if ( $errors )
			$location .= '?message=3';

		wp_redirect( admin_url($location) );
	}

	$title = __('Upload New Media');
	$parent_file = 'upload.php';
	require_once('admin-header.php'); ?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( $title ); ?></h2>

	<form enctype="multipart/form-data" method="post" action="media-upload.php?inline=&amp;upload-page-form=" class="media-upload-form type-form validate" id="file-form">

	<?php media_upload_form(); ?>

	<script type="text/javascript">
	jQuery(function($){
		var preloaded = $(".media-item.preloaded");
		if ( preloaded.length > 0 ) {
			preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
		}
		updateMediaForm();
		post_id = 0;
		shortform = 1;
	});
	</script>
	<input type="hidden" name="post_id" id="post_id" value="0" />
	<?php wp_nonce_field('media-form'); ?>
	<div id="media-items"> </div>
	<p>
	<input type="submit" class="button savebutton" name="save" value="<?php esc_attr_e( 'Save all changes' ); ?>" />
	</p>
	</form>
	</div>

<?php
	include('admin-footer.php');

} else {

	// upload type: image, video, file, ..?
	if ( isset($_GET['type']) )
		$type = strval($_GET['type']);
	else
		$type = apply_filters('media_upload_default_type', 'file');

	// tab: gallery, library, or type-specific
	if ( isset($_GET['tab']) )
		$tab = strval($_GET['tab']);
	else
		$tab = apply_filters('media_upload_default_tab', 'type');

	$body_id = 'media-upload';

	// let the action code decide how to handle the request
	if ( $tab == 'type' || $tab == 'type_url' )
		do_action("media_upload_$type");
	else
		do_action("media_upload_$tab");
}
?>
