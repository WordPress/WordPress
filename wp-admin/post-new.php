<?php
/**
 * New Post Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Administration Bootstrap */
require_once('./admin.php');

if ( !isset($_GET['post_type']) )
	$post_type = 'post';
elseif ( in_array( $_GET['post_type'], get_post_types( array('public' => true ) ) ) )
	$post_type = $_GET['post_type'];
else
	wp_die( __('Invalid post type') );

if ( 'post' != $post_type ) {
	$parent_file = "edit.php?post_type=$post_type";
	$submenu_file = "post-new.php?post_type=$post_type";
} else {
	$parent_file = 'edit.php';
	$submenu_file = 'post-new.php';
}

$post_type_object = get_post_type_object($post_type);

$title = $post_type_object->labels->add_new_item;

$editing = true;

if ( 'post' == $post_type && !current_user_can('edit_posts') ) {
	include('./admin-header.php'); ?>
<div class="wrap">
<p><?php printf(__('Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to add the <code>edit_posts</code> capability to your user, in order to be authorized to post.<br />
You can also <a href="mailto:%s?subject=Promotion?">e-mail the admin</a> to ask for a promotion.<br />
When you&#8217;re promoted, just reload this page and you&#8217;ll be able to blog. :)'), get_option('admin_email')); ?>
</p>
</div>
<?php
	include('./admin-footer.php');
	exit();
}

wp_enqueue_script('autosave');

// Show post form.
if ( current_user_can($post_type_object->edit_type_cap) ) {
	$post = get_default_post_to_edit( $post_type, true );
	$post_ID = $post->ID;
	include('edit-form-advanced.php');
}

include('./admin-footer.php');
?>