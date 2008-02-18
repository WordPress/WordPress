<?php
require_once('admin.php');
$title = __('Create New Post');
$parent_file = 'post-new.php';
$editing = true;
wp_enqueue_script('autosave');
wp_enqueue_script('post');
if ( user_can_richedit() )
	wp_enqueue_script('editor');
wp_enqueue_script('thickbox');
wp_enqueue_script('media-upload');

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
$my_drafts = get_users_drafts($user_ID);
$pending = get_others_pending($user_ID);
$others_drafts = get_others_drafts($user_ID);

$nag_posts_limit = (int) apply_filters('nag_posts_limit', 3);

$nag_posts = array(
	array(
		'my_drafts',
		__('Your Drafts:'),
		'edit.php?post_status=draft&amp;author=' . $user_ID,
		count($my_drafts)),
	array(
		'pending',
		__('Pending Review:'),
		'edit.php?post_status=pending',
		count($pending)),
	array(
		'others_drafts',
		__('Others&#8217; Drafts:'),
		'edit.php?post_status=draft&author=-' . $user_ID,
		count($others_drafts))
	);

if ( !empty($my_drafts) || !empty($pending) || !empty($others_drafts) ) {
	echo '<div class="wrap" id="draft-nag">';

	foreach ( $nag_posts as $nag ) {
		if ( ${$nag[0]} ) {
			echo '<p><strong>' . wp_specialchars($nag[1]) . '</strong> ';
			$i = 0;
			foreach ( ${$nag[0]} as $post ) {
				$i++;
				if ( $i > $nag_posts_limit )
					break;
				echo '<a href="post.php?action=edit&amp;post=' . $post->ID . '">';
				( '' == the_title('', '', FALSE) ) ? printf( __('Post #%s'), $post->ID ) : the_title();
				echo '</a>';
				if ( $i < min($nag[3], $nag_posts_limit) )
					echo ', ';
			}
			if ( $nag[3] > $nag_posts_limit )
				printf(__(', and <a href="%s">%d more</a>'), $nag[2], $nag[3] - $nag_posts_limit);
			echo '.</p>';
		}
	}
	echo "</div>\n";
}

// Show post form.
$post = get_default_post_to_edit();
include('edit-form-advanced.php');

include('admin-footer.php');
?>
