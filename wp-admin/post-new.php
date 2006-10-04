<?php
require_once('admin.php');
$title = __('Create New Post');
$parent_file = 'post-new.php';
$editing = true;
wp_enqueue_script('prototype');
wp_enqueue_script('autosave');
require_once ('./admin-header.php');

if ( ! current_user_can('edit_posts') ) { ?>
<div class="wrap">
<p><?php printf(__('Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to raise your level to 1, in order to be authorized to post.<br />
You can also <a href="mailto:%s?subject=Promotion?">e-mail the admin</a> to ask for a promotion.<br />
When you&#8217;re promoted, just reload this page and you&#8217;ll be able to blog. :)'), get_option('admin_email')); ?>
</p>
</div>
<?php
	include('admin-footer.php');
	exit();
}

if ( isset($_GET['posted']) ) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Post saved.'); ?></strong> <a href="<?php echo get_permalink( $_GET['posted'] ); ?>"><?php _e('View post'); ?> &raquo;</a></p></div>
<?php
endif;

if ( $drafts = get_users_drafts( $user_ID ) ) { ?>
<div class="wrap">
<p><strong><?php _e('Your Drafts:') ?></strong>
<?php
// Show drafts.
	$num_drafts = count($drafts);
	if ( $num_drafts > 15 ) $num_drafts = 15;
	for ( $i = 0; $i < $num_drafts; $i++ ) {
		$draft = $drafts[$i];
		if ( 0 != $i )
			echo ', ';
		if ( empty($draft->post_title) )
			$draft->post_title = sprintf(__('Post # %s'), $draft->ID);
		echo "<a href='post.php?action=edit&amp;post=$draft->ID' title='" . __('Edit this draft') . "'>$draft->post_title</a>";
	}

	if ( 15 < count($drafts) ) { ?>
		, <a href="edit.php"><?php echo sprintf(__('and %s more'), (count($drafts) - 15) ); ?> &raquo;</a>
	<?php } ?>
.</p>
</div>
<?php
}

// Show post form.
$post = get_default_post_to_edit();
include('edit-form-advanced.php');
?>

<?php if ( $is_NS4 || $is_gecko || $is_winIE ) { ?>
<div id="wp-bookmarklet" class="wrap">
<h3><?php _e('WordPress bookmarklet'); ?></h3>
<p><?php _e('Right click on the following link and choose "Add to favorites" to create a posting shortcut.'); ?></p>
<p>

<?php
if ($is_NS4 || $is_gecko) {
?>
<a href="javascript:if(navigator.userAgent.indexOf('Safari') >= 0){Q=getSelection();}else{Q=document.selection?document.selection.createRange().text:document.getSelection();}location.href='<?php echo get_option('siteurl') ?>/wp-admin/post-new.php?text='+encodeURIComponent(Q)+'&amp;popupurl='+encodeURIComponent(location.href)+'&amp;popuptitle='+encodeURIComponent(document.title);"><?php printf(__('Press It - %s'), wp_specialchars(get_option('blogname'))); ?></a> 
<?php
} else if ($is_winIE) {
?>
<a href="javascript:Q='';if(top.frames.length==0)Q=document.selection.createRange().text;location.href='<?php echo get_option('siteurl') ?>/wp-admin/post-new.php?text='+encodeURIComponent(Q)+'&amp;popupurl='+encodeURIComponent(location.href)+'&amp;popuptitle='+encodeURIComponent(document.title);"><?php printf(__('Press it - %s'), get_option('blogname')); ?></a>
<script type="text/javascript">
<!--
function oneclickbookmarklet(blah) {
window.open ("profile.php?action=IErightclick", "oneclickbookmarklet", "width=500, height=450, location=0, menubar=0, resizable=0, scrollbars=1, status=1, titlebar=0, toolbar=0, screenX=120, left=120, screenY=120, top=120");
}
// -->
</script>
<br />
<br />
<?php _e('One-click bookmarklet:') ?><br />
<a href="javascript:oneclickbookmarklet(0);"><?php _e('click here') ?></a> 
<?php
} else if ($is_opera) {
?>
<a href="javascript:location.href='<?php echo get_option('siteurl'); ?>/wp-admin/post-new.php?popupurl='+escape(location.href)+'&popuptitle='+escape(document.title);"><?php printf(__('Press it - %s'), get_option('blogname')); ?></a> 
<?php
} else if ($is_macIE) {
?>
<a href="javascript:Q='';location.href='<?php echo get_option('siteurl'); ?>/wp-admin/bookmarklet.php?text='+escape(document.getSelection())+'&popupurl='+escape(location.href)+'&popuptitle='+escape(document.title);"><?php printf(__('Press it - %s'), get_option('blogname')); ?></a> 
<?php
}
?>
</p>
</div>
<?php } ?>

<?php include('admin-footer.php'); ?>
