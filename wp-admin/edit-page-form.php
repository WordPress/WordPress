<?php
if ( isset($_GET['message']) )
	$_GET['message'] = absint( $_GET['message'] );
$messages[1] = sprintf( __( 'Page updated. Continue editing below or <a href="%s">go back</a>.' ), attribute_escape( stripslashes( $_GET['_wp_original_http_referer'] ) ) );
$messages[2] = __('Custom field updated.');
$messages[3] = __('Custom field deleted.');
$messages[4] = __('Page updated.');
?>
<?php if (isset($_GET['message'])) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php endif;

if (!isset($post_ID) || 0 == $post_ID) {
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
<h2><?php _e('Write Page') ?></h2>

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
<input type="hidden" id="original_post_status" name="original_post_status" value="<?php echo $post->post_status ?>" />
<input name="referredby" type="hidden" id="referredby" value="<?php
if ( strpos( wp_get_referer(), '/wp-admin/' ) === false && $post_ID && url_to_postid(wp_get_referer()) === $post_ID )
	echo 'redo';
else
	echo clean_url(stripslashes(wp_get_referer()));
?>" />
<?php if ( 'draft' != $post->post_status ) wp_original_referer_field(true, 'previous'); ?>

<div id="poststuff">

<div class="submitbox" id="submitpage">

<div id="previewview">
<?php if ( 'publish' == $post->post_status ) { ?>
<a href="<?php echo clean_url(get_permalink($post->ID)); ?>" target="_blank"  tabindex="4"><?php _e('View this Page'); ?></a>
<?php } elseif ( 'edit' == $action ) { ?>
<a href="<?php echo clean_url(apply_filters('preview_post_link', add_query_arg('preview', 'true', get_permalink($post->ID)))); ?>" target="_blank" tabindex="4"><?php _e('Preview this Page'); ?></a>
<?php } ?>
</div>

<div class="inside">

<p><strong><?php _e('Publish Status') ?></strong></p>
<p>
<select name='post_status' tabindex='4'>
<?php if ( current_user_can('publish_pages') ) : ?>
<option<?php selected( $post->post_status, 'publish' ); selected( $post->post_status, 'private' );?> value='publish'><?php _e('Published') ?></option>
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

<p><label for="post_status_private" class="selectit"><input id="post_status_private" name="post_status" type="checkbox" value="private" <?php checked($post->post_status, 'private'); ?> tabindex='4' /> <?php _e('Keep this page private') ?></label></p>
<?php
if ($post_ID) {
	if ( 'future' == $post->post_status ) { // scheduled for publishing at a future date
		$stamp = __('Scheduled for:<br />%1$s at %2$s');
	} else if ( 'publish' == $post->post_status ) { // already published
		$stamp = __('Published on:<br />%1$s at %2$s');
	} else if ( '0000-00-00 00:00:00' == $post->post_date ) { // draft, 1 or more saves, no date specified
		$stamp = __('Publish immediately');
	} else { // draft, 1 or more saves, date specified
		$stamp = __('Publish on:<br />%1$s at %2$s');
	}
	$date = mysql2date(get_option('date_format'), $post->post_date);
	$time = mysql2date(get_option('time_format'), $post->post_date);
} else { // draft (no saves, and thus no date specified)
	$stamp = __('Publish immediately');
	$date = mysql2date(get_option('date_format'), current_time('mysql'));
	$time = mysql2date(get_option('time_format'), current_time('mysql'));
}
?>
<p class="curtime"><?php printf($stamp, $date, $time); ?>
&nbsp;<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" tabindex='4'><?php _e('Edit') ?></a></p>

<div id='timestampdiv' class='hide-if-js'><?php touch_time(($action == 'edit'),1,4); ?></div>

</div>

<p class="submit">
<input type="submit" name="save" class="button button-highlighted" value="<?php _e('Save'); ?>" tabindex="4" />
<?php
if ( !in_array( $post->post_status, array('publish', 'future') ) || 0 == $post_ID ) {
?>
<?php if ( current_user_can('publish_pages') ) : ?>
	<input name="publish" type="submit" class="button" id="publish" tabindex="5" accesskey="p" value="<?php _e('Publish') ?>" />
<?php else : ?>
	<input name="publish" type="submit" class="button" id="publish" tabindex="5" accesskey="p" value="<?php _e('Submit for Review') ?>" />
<?php endif; ?>
<?php
}

if ( ('edit' == $action) && current_user_can('delete_page', $post_ID) )
	echo "<a class='submitdelete' href='" . wp_nonce_url("page.php?action=delete&amp;post=$post_ID", 'delete-page_' . $post_ID) . "' onclick=\"if ( confirm('" . js_escape(sprintf( ('draft' == $post->post_status) ? __("You are about to delete this draft '%s'\n  'Cancel' to stop, 'OK' to delete.") : __("You are about to delete this page '%s'\n  'Cancel' to stop, 'OK' to delete."), $post->post_title )) . "') ) { return true;}return false;\">" . __('Delete&nbsp;page') . "</a>";
?>
<br class="clear" />
<?php if ($post_ID): ?>
<?php if ( $last_id = get_post_meta($post_ID, '_edit_last', true) ) {
	$last_user = get_userdata($last_id);
	printf(__('Last edited by %1$s on %2$s at %3$s'), wp_specialchars( $last_user->display_name ), mysql2date(get_option('date_format'), $post->post_modified), mysql2date(get_option('time_format'), $post->post_modified));
} else {
	printf(__('Last edited on %1$s at %2$s'), mysql2date(get_option('date_format'), $post->post_modified), mysql2date(get_option('time_format'), $post->post_modified));
}
?>
<br class="clear" />
<?php endif; ?>
<span id="autosave"></span>
</p>

<div class="side-info">
<h5><?php _e('Related') ?></h5>

<ul>
<?php if ($post_ID): ?>
<li><a href="edit-pages.php?page_id=<?php echo $post_ID ?>"><?php _e('See Comments on this Page') ?></a></li>
<?php endif; ?>
<li><a href="edit-comments.php"><?php _e('Manage All Comments') ?></a></li>
<li><a href="edit-pages.php"><?php _e('Manage All Pages') ?></a></li>
<?php do_action('page_relatedlinks_list'); ?>
</ul>
</div>
<?php do_action('submitpage_box'); ?>
</div>

<div id="post-body">
<div id="titlediv">
<h3><?php _e('Title') ?></h3>
<div id="titlewrap">
  <input type="text" name="post_title" size="30" tabindex="1" value="<?php echo attribute_escape( $post->post_title ); ?>" id="title" autocomplete="off" />
</div>
<div class="inside">
<?php $sample_permalink_html = get_sample_permalink_html($post->ID); ?>
	<div id="edit-slug-box">
<?php if ( ! empty($post->ID) && ! empty($sample_permalink_html) ) :
	echo $sample_permalink_html;
endif; ?>
	</div>
</div>
</div>

<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">
<h3><?php _e('Page') ?></h3>
<?php the_editor($post->post_content); ?>
<?php wp_nonce_field( 'autosave', 'autosavenonce', false ); ?>
<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
<?php wp_nonce_field( 'getpermalink', 'getpermalinknonce', false ); ?>
<?php wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false ); ?>
</div>

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
<p><?php _e('Custom fields can be used to add extra metadata to a post that you can <a href="http://codex.wordpress.org/Using_Custom_Fields" target="_blank">use in your theme</a>.'); ?></p>
</div>
</div>

<div id="pagecommentstatusdiv" class="postbox <?php echo postbox_classes('pagecommentstatusdiv', 'page'); ?>">
<h3><?php _e('Comments &amp; Pings') ?></h3>
<div class="inside">
<input name="advanced_view" type="hidden" value="1" />
<p><label for="comment_status" class="selectit">
<input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($post->comment_status, 'open'); ?> />
<?php _e('Allow Comments') ?></label></p>
<p><label for="ping_status" class="selectit"><input name="ping_status" type="checkbox" id="ping_status" value="open" <?php checked($post->ping_status, 'open'); ?> /> <?php _e('Allow Pings') ?></label></p>
<p><?php _e('These settings apply to this page only. &#8220;Pings&#8221; are <a href="http://codex.wordpress.org/Introduction_to_Blogging#Managing_Comments" target="_blank">trackbacks and pingbacks</a>.'); ?></p>
</div>
</div>

<div id="pagepassworddiv" class="postbox <?php echo postbox_classes('pagepassworddiv', 'page'); ?>">
<h3><?php _e('Password Protect This Page') ?></h3>
<div class="inside">
<p><input name="post_password" type="text" size="25" id="post_password" value="<?php echo attribute_escape( $post->post_password ); ?>" /></p>
<p><?php _e('Setting a password will require people who visit your blog to enter the above password to view this page and its comments.'); ?></p>
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
<p><?php _e('You can arrange your pages in hierarchies, for example you could have an &#8220;About&#8221; page that has &#8220;Life Story&#8221; and &#8220;My Dog&#8221; pages under it. There are no limits to how deeply nested you can make pages.'); ?></p>
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
<p><?php _e('Some themes have custom templates you can use for certain pages that might have additional features or custom layouts. If so, you&#8217;ll see them above.'); ?></p>
</div>
</div>
<?php } ?>

<div id="pageorderdiv" class="postbox <?php echo postbox_classes('pageorderdiv', 'page'); ?>">
<h3><?php _e('Page Order') ?></h3>
<div class="inside">
<p><input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo $post->menu_order ?>" /></p>
<p><?php _e('Pages are usually ordered alphabetically, but you can put a number above to change the order pages appear in. (We know this is a little janky, it&#8217;ll be better in future releases.)'); ?></p>
</div>
</div>

<?php
$authors = get_editable_user_ids( $current_user->id ); // TODO: ROLE SYSTEM
if ( $post->post_author && !in_array($post->post_author, $authors) )
	$authors[] = $post->post_author;
if ( $authors && count( $authors ) > 1 ) :
?>
<div id="pageauthordiv" class="postbox <?php echo postbox_classes('pageauthordiv', 'page'); ?>">
<h3><?php _e('Page Author'); ?></h3>
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

<script type="text/javascript">
try{document.post.title.focus();}catch(e){}
</script>
