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

if ( ! isset( $_GET['inline'] ) )
	define( 'IFRAME_REQUEST' , true );

/** Load WordPress Administration Bootstrap */
require_once('./admin.php');

if (!current_user_can('upload_files'))
	wp_die(__('You do not have permission to upload files.'));

wp_enqueue_script('plupload-full');
wp_enqueue_script('plupload-handlers');
wp_enqueue_script('image-edit');
wp_enqueue_script('set-post-thumbnail' );
wp_enqueue_style('imgareaselect');

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
		check_admin_referer('media-form');
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
		exit;
	}

	$title = __('Upload New Media');
	$parent_file = 'upload.php';

	add_contextual_help( $current_screen,
'<p>' . __('You can upload media files here without creating a post first. This allows you to upload files to use with posts and pages later and/or to get a web link for a particular file that you can share.') . '</p>' .
		'<p>' . __('There are two options for uploading files: <em>Select Files</em> will open the Flash-based uploader (multiple file upload allowed), or you can use the <em>Browser Uploader</em>. Clicking <em>Select Files</em> opens a navigation window showing you files in your operating system. Selecting <em>Open</em> after clicking on the file you want activates a progress bar on the uploader screen. Basic image editing is available after upload is complete. Make sure you click <em>Save</em> before leaving this screen.') . '</p>'
	);

	get_current_screen()->set_help_sidebar(
		'<p><strong>' . __('For more information:') . '</strong></p>' .
		'<p>' . __('<a href="http://codex.wordpress.org/Media_Add_New_Screen" target="_blank">Documentation on Uploading Media Files</a>') . '</p>' .
		'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
	);

	require_once('./admin-header.php'); ?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( $title ); ?></h2>

	<form enctype="multipart/form-data" method="post" action="<?php echo admin_url('media-upload.php?inline=&amp;upload-page-form='); ?>" class="media-upload-form type-form validate" id="file-form">

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
	<div id="media-items" class="hide-if-no-js"></div>
	<?php submit_button( __( 'Save all changes' ), 'button savebutton hide-if-no-js', 'save' ); ?>
	</form>
	</div>

<?php
	include('./admin-footer.php');

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
	if ( $tab == 'type' || $tab == 'type_url' || ! array_key_exists( $tab , media_upload_tabs() ) )
		do_action("media_upload_$type");
	else
		do_action("media_upload_$tab");
}
?>
