<?php
require_once('admin.php');
$title = __('Create New Post');
$parent_file = 'post-new.php';
$editing = true;
wp_enqueue_script('autosave');
wp_enqueue_script('post');
if ( user_can_richedit() )
	wp_enqueue_script('editor');
add_thickbox();
wp_enqueue_script('media-upload');
wp_enqueue_script('word-count');

require_once ('./admin-header.php');

if ( ! current_user_can('edit_posts') ) { ?>
<div class="wrap">
<p><?php printf(__('Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to add the <code>edit_posts</code> capability to your user, in order to be authorized to post.<br />
You can also <a href="mailto:%s?subject=Promotion?">e-mail the admin</a> to ask for a promotion.<br />
When you&#8217;re promoted, just reload this page and you&#8217;ll be able to blog. :)'), get_option('admin_email')); ?>
</p>
</div>
<?php
	include('admin-footer.php');
	exit();
}

if ( isset($_GET['posted']) && $_GET['posted'] ) : $_GET['posted'] = (int) $_GET['posted']; ?>
<div id="message" class="updated fade"><p><strong><?php _e('Your post has been saved.'); ?></strong> <a href="<?php echo get_permalink( $_GET['posted'] ); ?>"><?php _e('View post'); ?></a> | <a href="post.php?action=edit&amp;post=<?php echo $_GET['posted']; ?>"><?php _e('Edit post'); ?></a></p></div>
<?php
endif;
?>


<?php

// Show post form.
$post = get_default_post_to_edit();
include('edit-form-advanced.php');

include('admin-footer.php');
?>
