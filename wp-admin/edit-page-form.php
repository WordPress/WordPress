<?php

if (0 == $post_ID) {
	$form_action = 'post';
	$nonce_action = 'add-page';
	$temp_ID = -1 * time(); // don't change this formula without looking at wp_write_post()
	$form_extra = "<input type='hidden' id='post_ID' name='temp_ID' value='$temp_ID' />";
} else {
	$post_ID = (int) $post_ID;
	$form_action = 'editpost';
	$nonce_action = 'update-page_' . $post_ID;
	$form_extra = "<input type='hidden' id='post_ID' name='post_ID' value='$post_ID' />";
}

$temp_ID = (int) $temp_ID;
$user_ID = (int) $user_ID;

$sendto = clean_url(stripslashes(wp_get_referer()));

if ( 0 != $post_ID && $sendto == get_permalink($post_ID) )
	$sendto = 'redo';
?>

<form name="post" action="page.php" method="post" id="post">
<div class="wrap">

<?php
wp_nonce_field($nonce_action);

if (isset($mode) && 'bookmarklet' == $mode)
	echo '<input type="hidden" name="mode" value="bookmarklet" />';
?>
<input type="hidden" id="user-id" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" id="hiddenaction" name="action" value='<?php echo $form_action ?>' />
<input type="hidden" id="originalaction" name="originalaction" value="<?php echo $form_action ?>" />
<?php echo $form_extra ?>
<input type="hidden" id="post_type" name="post_type" value="<?php echo $post->post_type ?>" />

<script type="text/javascript">
// <![CDATA[
function focusit() { // focus on first input field
	document.post.title.focus();
}
addLoadEvent(focusit);
// ]]>
</script>
<div id="poststuff">

<div id="titlediv">
<h3><?php _e('Title') ?></h3>
<div class="inside">
  <input type="text" name="post_title" size="30" tabindex="1" value="<?php echo attribute_escape( $post->post_title ); ?>" id="title" />
</div>
</div>

<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">
<h3><?php _e('Page') ?></h3>
<?php the_editor($post->post_content); ?>
<?php wp_nonce_field( 'autosave', 'autosavenonce', false ); ?>
<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
</div>

<div id="submitpost">

<div id="previewview">
<?php if ( 'publish' == $post->post_status ) { ?>
<a href="<?php echo clean_url(get_permalink($post->ID)); ?>" target="_blank"><?php _e('View this Page'); ?></a>
<?php } elseif ( 'edit' == $action ) { ?>
<a href="<?php echo clean_url(apply_filters('preview_post_link', add_query_arg('preview', 'true', get_permalink($post->ID)))); ?>" target="_blank"><?php _e('Preview this Page'); ?></a>
<?php } ?>
</div>

<div class="inside">

<p><strong><?php _e('Publish Status') ?></strong></p>
<p>
<select name='post_status'>
<?php if ( current_user_can('publish_posts') ) : ?>
<?php $pub_value = ( 'private' == $post->post_status ) ? 'private' : 'publish'; ?>
<option<?php selected( $post->post_status, 'publish' ); selected( $post->post_status, 'private' );?> value='<?php echo $pub_value ?>'><?php _e('Published') ?></option>
<?php else: ?>
<option<?php selected( $post->post_status, 'private' ); ?> value='private'><?php _e('Published') ?></option>
<?php endif; ?>
<?php if ( 'future' == $post->post_status ) : ?>
<option<?php selected( $post->post_status, 'future' ); ?> value='future'><?php _e('Pending') ?></option>
<?php endif; ?>
<option<?php selected( $post->post_status, 'pending' ); ?> value='pending'><?php _e('Pending Review') ?></option>
<option<?php selected( $post->post_status, 'draft' ); ?> value='draft'><?php _e('Unpublished') ?></option>
</select>
</p>

<p><label for="post_status_private" class="selectit"><input id="post_status_private" name="post_status" type="checkbox" value="private" <?php checked($post->post_status, 'private'); ?> /> <?php _e('Keep this post private') ?></label></p>
<?php
if ($post_ID):

if ( 'future' == $post->post_status ) {
	$time = __('Scheduled for:<br />%1$s at %2$s');
	$date = $post->post_date;
} else if ( 'publish' == $post->post_status ) {
	$time = __('Published on:<br />%1$s at %2$s');
	$date = $post->post_date;
} else {
	$time = __('Saved on:<br />%1$s at %2$s');
	$date = $post->post_modified;
}

?>
<p><?php printf($time, mysql2date(get_option('date_format'), $date), mysql2date(get_option('time_format'), $date)); ?>
<?php endif; ?>
</div>

<p class="submit">
<input type="submit" name="save" value="<?php _e('Save'); ?>" style="font-weight: bold;" tabindex="4" />
<?php
if ( !in_array( $post->post_status, array('publish', 'future') ) || 0 == $post_ID ) {
?>
<?php if ( current_user_can('publish_pages') ) : ?>
	<input name="publish" type="submit" id="publish" tabindex="5" accesskey="p" value="<?php _e('Publish') ?>" />
<?php else : ?>
	<input name="publish" type="submit" id="publish" tabindex="5" accesskey="p" value="<?php _e('Submit for Review') ?>" />
<?php endif; ?>
<?php
}

if ( ('edit' == $action) && current_user_can('delete_page', $post_ID) )
	echo "<a href='" . wp_nonce_url("page.php?action=delete&amp;post=$post_ID", 'delete-page_' . $post_ID) . "' onclick=\"if ( confirm('" . js_escape(sprintf( ('draft' == $post->post_status) ? __("You are about to delete this draft '%s'\n  'Cancel' to stop, 'OK' to delete.") : __("You are about to delete this page '%s'\n  'Cancel' to stop, 'OK' to delete."), $post->post_title )) . "') ) { return true;}return false;\">" . __('Delete page') . "</a>";
?>
<?php if ($post_ID): ?>
<br />
<?php printf(__('Last edited on %1$s at %2$s'), mysql2date(get_option('date_format'), $post->post_modified), mysql2date(get_option('time_format'), $post->post_modified)); ?>
<?php endif; ?>
</p>
</div>

<p class="submit">

<span id="autosave"></span>

</p>

<?php do_meta_boxes('page', 'normal', $post); ?>

<?php do_action('edit_page_form'); ?>

<h2><?php _e('Advanced Options'); ?></h2>

<div id="pagepostcustom" class="postbox <?php echo postbox_classes('pagepostcustom', 'page'); ?>">
<h3><?php _e('Custom Fields') ?></h3>
<div class="inside">
<div id="postcustomstuff">
<table cellpadding="3">
<?php
$metadata = has_meta($post_ID);
list_meta($metadata);
?>

</table>
<?php
	meta_form();
?>
<div id="ajax-response"></div>
</div>
</div>
</div>

<div id="pagecommentstatusdiv" class="postbox <?php echo postbox_classes('pagecommentstatusdiv', 'page'); ?>">
<h3><?php _e('Discussion') ?></h3>
<div class="inside">
<input name="advanced_view" type="hidden" value="1" />
<label for="comment_status" class="selectit">
<input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($post->comment_status, 'open'); ?> />
<?php _e('Allow Comments') ?></label>
<label for="ping_status" class="selectit"><input name="ping_status" type="checkbox" id="ping_status" value="open" <?php checked($post->ping_status, 'open'); ?> /> <?php _e('Allow Pings') ?></label>
</div>
</div>

<div id="pagepassworddiv" class="postbox <?php echo postbox_classes('pagepassworddiv', 'page'); ?>">
<h3><?php _e('Page Password') ?></h3>
<div class="inside">
<input name="post_password" type="text" size="13" id="post_password" value="<?php echo attribute_escape( $post->post_password ); ?>" />
</div>
</div>

<div id="pageslugdiv" class="postbox <?php echo postbox_classes('pageslugdiv', 'page'); ?>">
<h3><?php _e('Page Slug') ?></h3>
<div class="inside">
<input name="post_name" type="text" size="13" id="post_name" value="<?php echo attribute_escape( $post->post_name ); ?>" />
</div>
</div>

<div id="pageparentdiv" class="postbox <?php echo postbox_classes('pageparentdiv', 'page'); ?>">
<h3><?php _e('Page Parent') ?></h3>
<div class="inside">
<select name="parent_id">
<option value='0'><?php _e('Main Page (no parent)'); ?></option>
<?php parent_dropdown($post->post_parent); ?>
</select>
</div>
</div>

<?php if ( 0 != count( get_page_templates() ) ) { ?>
<div id="pagetemplatediv" class="postbox <?php echo postbox_classes('pagetemplatediv', 'page'); ?>">
<h3><?php _e('Page Template') ?></h3>
<div class="inside">
<select name="page_template">
<option value='default'><?php _e('Default Template'); ?></option>
<?php page_template_dropdown($post->page_template); ?>
</select>
</div>
</div>
<?php } ?>

<div id="pageorderdiv" class="postbox <?php echo postbox_classes('pageorderdiv', 'page'); ?>">
<h3><?php _e('Page Order') ?></h3>
<div class="inside">
<input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo $post->menu_order ?>" />
</div>
</div>

<?php
$authors = get_editable_user_ids( $current_user->id ); // TODO: ROLE SYSTEM
if ( $post->post_author && !in_array($post->post_author, $authors) )
	$authors[] = $post->post_author;
if ( $authors && count( $authors ) > 1 ) :
?>
<div id="pageauthordiv" class="postbox <?php echo postbox_classes('pageauthordiv', 'page'); ?>">
<h3><?php _e('Post Author'); ?></h3>
<div class="inside">
<?php wp_dropdown_users( array('include' => $authors, 'name' => 'post_author_override', 'selected' => empty($post_ID) ? $user_ID : $post->post_author) ); ?>
</div>
</div>
<?php endif; ?>

<?php do_meta_boxes('page', 'advanced', $post); ?>

</div>

</div>

</div>

</form>
