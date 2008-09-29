<?php
/**
 * Edit page form for inclusion in the administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Post ID global
 * @name $post_ID
 * @var int
 */
if ( ! isset( $post_ID ) )
	$post_ID = 0;

if ( isset($_GET['message']) )
	$_GET['message'] = absint( $_GET['message'] );
$messages[1] = sprintf( __( 'Page updated. Continue editing below or <a href="%s">go back</a>.' ), attribute_escape( stripslashes( ( isset( $_GET['_wp_original_http_referer'] ) ? $_GET['_wp_original_http_referer'] : '') ) ) );
$messages[2] = __('Custom field updated.');
$messages[3] = __('Custom field deleted.');
$messages[4] = __('Page updated.');

if ( isset($_GET['revision']) )
	$messages[5] = sprintf( __('Page restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) );

$notice = false;
$notices[1] = __( 'There is an autosave of this page that is more recent than the version below.  <a href="%s">View the autosave</a>.' );

if ( 0 == $post_ID) {
	$form_action = 'post';
	$nonce_action = 'add-page';
	$temp_ID = -1 * time(); // don't change this formula without looking at wp_write_post()
	$form_extra = "<input type='hidden' id='post_ID' name='temp_ID' value='$temp_ID' />";
} else {
	$post_ID = (int) $post_ID;
	$form_action = 'editpost';
	$nonce_action = 'update-page_' . $post_ID;
	$form_extra = "<input type='hidden' id='post_ID' name='post_ID' value='$post_ID' />";
	$autosave = wp_get_post_autosave( $post_id );
	if ( $autosave && mysql2date( 'U', $autosave->post_modified_gmt ) > mysql2date( 'U', $post->post_modified_gmt ) )
		$notice = sprintf( $notices[1], get_edit_post_link( $autosave->ID ) );
}

$temp_ID = (int) $temp_ID;
$user_ID = (int) $user_ID;
?>

<?php
function page_submit_meta_box($post) {
	global $action;

	$can_publish = current_user_can('publish_pages');
?>
<div class="submitbox" id="submitpage">

<div class="inside-submitbox">

<div class="insidebox"><label for='post_status'><?php _e('This page is') ?></label>
<strong><span id="post-status-display">
<?php
switch ( $post->post_status ) {
	case 'publish':
	case 'private':
		_e('Published');
		break;
	case 'future':
		_e('Scheduled');
		break;
	case 'pending':
		_e('Pending Review');
		break;
	case 'draft':
		_e('Unpublished');
		break;
}
?>
</span></strong>
<?php if ( 'publish' == $post->post_status || 'private' == $post->post_status ) { ?>
<a href="#edit_post_status" class="edit-post-status hide-if-no-js" tabindex='4'><?php _e('Edit') ?></a>

<div id="post-status-select" class="hide-if-js">
<input type="hidden" name="hidden_post_status" id="hidden_post_status" value="<?php echo $post->post_status; ?>" />
<?php _e('Change page status'); ?><br />
<select name='post_status' id='post_status' tabindex='4'>
<?php
// only show the publish menu item if they are allowed to publish posts or they are allowed to edit this post (accounts for 'edit_published_posts' capability)
if ( $can_publish OR ( $post->post_status == 'publish' AND current_user_can('edit_page', $post->ID) ) ) : ?>
<option<?php selected( $post->post_status, 'publish' ); selected( $post->post_status, 'private' );?> value='publish'><?php _e('Published') ?></option>
<?php if ( 'future' == $post->post_status ) : ?>
<option<?php selected( $post->post_status, 'future' ); ?> value='future'><?php _e('Scheduled') ?></option>
<?php endif; ?>
<?php endif; ?>
<option<?php selected( $post->post_status, 'pending' ); ?> value='pending'><?php _e('Pending Review') ?></option>
<option<?php selected( $post->post_status, 'draft' ); ?> value='draft'><?php _e('Unpublished') ?></option>
</select>
<a href="#post_status" class="save-post-status hide-if-no-js button"><?php _e('OK'); ?></a>
<a href="#post_status" class="cancel-post-status hide-if-no-js"><?php _e('Cancel'); ?></a>
</div>
</div>

<?php } else { ?>
</div>

<?php if ( $can_publish && 'pending' != $post->post_status ) { ?>
<div  class="insidebox"><input name="pending" type="submit" class="button" id="pending" tabindex="6" accesskey="r" value="<?php _e('Submit for Review') ?>" /></div>
<?php } ?>

<?php } ?>

<?php if ( ('edit' == $action) && current_user_can('delete_page', $post->ID) ) { ?>
	<div class="insidebox" id="deletebutton"><a class="submitdelete" href="<?php echo wp_nonce_url("page.php?action=delete&amp;post=$post->ID", 'delete-post_' . $post->ID); ?>" onclick="if ( confirm('<?php echo js_escape(sprintf( ('draft' == $post->post_status) ? __("You are about to delete this draft '%s'\n  'Cancel' to stop, 'OK' to delete.") : __("You are about to delete this page '%s'\n  'Cancel' to stop, 'OK' to delete."), $post->post_title )); ?>') ) {return true;}return false;"><?php _e('Delete&nbsp;page'); ?></a></div>
<?php } ?>

<?php
if ( 0 != $post->ID ) {
	if ( 'future' == $post->post_status ) { // scheduled for publishing at a future date
		$stamp = __('Scheduled for: %1$s at %2$s');
	} else if ( 'publish' == $post->post_status ) { // already published
		$stamp = __('Published on: %1$s at %2$s');
	} else if ( '0000-00-00 00:00:00' == $post->post_date ) { // draft, 1 or more saves, no date specified
		$stamp = __('Publish immediately');
	} else { // draft, 1 or more saves, date specified
		$stamp = __('Publish on: %1$s at %2$s');
	}
	$date = mysql2date(get_option('date_format'), $post->post_date);
	$time = mysql2date(get_option('time_format'), $post->post_date);
} else { // draft (no saves, and thus no date specified)
	$stamp = __('Publish immediately');
	$date = mysql2date(get_option('date_format'), current_time('mysql'));
	$time = mysql2date(get_option('time_format'), current_time('mysql'));
}
?>
<?php if ( $can_publish ) : // Contributors don't get to choose the date of publish ?>
<div class="insidebox curtime"><span id="timestamp"><?php printf($stamp, $date, $time); ?></span>
&nbsp;<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" tabindex='4'><?php _e('Edit') ?></a></p>

<div id="timestampdiv" class="hide-if-js"><?php touch_time(($action == 'edit'),1,4); ?></div></div>
<?php endif; ?>

</div>

<p class="submit">
<?php do_action('page_submitbox_start'); ?>
<?php if ( 'publish' == $post->post_status || 'private' == $post->post_status )
	$savebtn = attribute_escape( __('Save') );
else
	$savebtn = attribute_escape( __('Save Draft') );
?>
<input type="submit" name="save" id="save-post" value="<?php echo $savebtn; ?>" tabindex="4" class="button button-highlighted" />

<?php if ( 'publish' == $post->post_status ) { ?>
<a class="preview button" href="<?php echo clean_url(get_permalink($post->ID)); ?>" target="_blank" tabindex="4"><?php _e('View this Page'); ?></a>
<?php } else { ?>
<a class="preview button" href="<?php echo clean_url(apply_filters('preview_post_link', add_query_arg('preview', 'true', get_permalink($post->ID)))); ?>" target="_blank" tabindex="4"><?php _e('Preview'); ?></a>
<?php } ?>

<?php
if ( !in_array( $post->post_status, array('publish', 'future') ) || 0 == $post->ID ) { ?>
<?php if ( current_user_can('publish_pages') ) : ?>
	<input name="publish" type="submit" class="button" id="publish" tabindex="5" accesskey="p" value="<?php _e('Publish') ?>" />
<?php else : ?>
	<input name="publish" type="submit" class="button" id="publish" tabindex="5" accesskey="p" value="<?php _e('Submit for Review') ?>" />
<?php endif; ?>
<?php } ?>

</p>
<div class="clear"></div>
</div>

<?php
}
add_meta_box('pagesubmitdiv', __('Publish'), 'page_submit_meta_box', 'page', 'side', 'core');

function page_custom_meta_box($post){
?>
<div id="postcustomstuff">
<table cellpadding="3">
<?php
$metadata = has_meta($post->ID);
list_meta($metadata);
?>

</table>
<?php
	meta_form();
?>
<div id="ajax-response"></div>
</div>
<p><?php _e('Custom fields can be used to add extra metadata to a post that you can <a href="http://codex.wordpress.org/Using_Custom_Fields" target="_blank">use in your theme</a>.'); ?></p>
<?php
}
add_meta_box('pagecustomdiv', __('Custom Fields'), 'page_custom_meta_box', 'page', 'normal', 'core');

function page_comments_status_meta_box($post){
?>
<input name="advanced_view" type="hidden" value="1" />
<p><label for="comment_status" class="selectit">
<input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($post->comment_status, 'open'); ?> />
<?php _e('Allow Comments') ?></label></p>
<p><label for="ping_status" class="selectit"><input name="ping_status" type="checkbox" id="ping_status" value="open" <?php checked($post->ping_status, 'open'); ?> /> <?php _e('Allow Pings') ?></label></p>
<p><?php _e('These settings apply to this page only. &#8220;Pings&#8221; are <a href="http://codex.wordpress.org/Introduction_to_Blogging#Managing_Comments" target="_blank">trackbacks and pingbacks</a>.'); ?></p>
<?php
}
add_meta_box('pagecommentstatusdiv', __('Comments &amp; Pings'), 'page_comments_status_meta_box', 'page', 'normal', 'core');

function page_password_meta_box($post){
?>
<p><label for="post_status_private" class="selectit"><input id="post_status_private" name="post_status" type="checkbox" value="private" <?php checked($post->post_status, 'private'); ?> tabindex='4' /> <?php _e('Keep this page private') ?></label></p>
<h4><?php _e( 'Page Password' ); ?></h4>
<p><label class="hidden" for="post_password"><?php _e('Password Protect This Page') ?></label><input name="post_password" type="text" size="25" id="post_password" value="<?php the_post_password(); ?>" /></p>
<p><?php _e('Setting a password will require people who visit your blog to enter the above password to view this page and its comments.'); ?></p>
<?php
}
add_meta_box('pagepassworddiv', __('Privacy Options'), 'page_password_meta_box', 'page', 'normal', 'core');

function page_slug_meta_box($post){
?>
<label class="hidden" for="post_name"><?php _e('Page Slug') ?></label><input name="post_name" type="text" size="13" id="post_name" value="<?php echo attribute_escape( $post->post_name ); ?>" />
<?php
}
add_meta_box('pageslugdiv', __('Page Slug'), 'page_slug_meta_box', 'page', 'normal', 'core');

function page_parent_meta_box($post){
?>
<label class="hidden" for="parent_id"><?php _e('Page Parent') ?></label>
<?php wp_dropdown_pages(array('selected' => $post->post_parent, 'name' => 'parent_id', 'show_option_none' => __('Main Page (no parent)'))); ?>
<p><?php _e('You can arrange your pages in hierarchies, for example you could have an &#8220;About&#8221; page that has &#8220;Life Story&#8221; and &#8220;My Dog&#8221; pages under it. There are no limits to how deeply nested you can make pages.'); ?></p>
<?php
}
add_meta_box('pageparentdiv', __('Page Parent'), 'page_parent_meta_box', 'page', 'normal', 'core');

if ( 0 != count( get_page_templates() ) ) {
	function page_template_meta_box($post){
?>
<label class="hidden" for="page_template"><?php _e('Page Template') ?></label><select name="page_template" id="page_template">
<option value='default'><?php _e('Default Template'); ?></option>
<?php page_template_dropdown($post->page_template); ?>
</select>
<p><?php _e('Some themes have custom templates you can use for certain pages that might have additional features or custom layouts. If so, you&#8217;ll see them above.'); ?></p>
<?php
	}
	add_meta_box('pagetemplatediv', __('Page Template'), 'page_template_meta_box', 'page', 'normal', 'core');
}

function page_order_meta_box($post){
?>
<p><label class="hidden" for="menu_order"><?php _e('Page Order') ?></label><input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo $post->menu_order ?>" /></p>
<p><?php _e('Pages are usually ordered alphabetically, but you can put a number above to change the order pages appear in. (We know this is a little janky, it&#8217;ll be better in future releases.)'); ?></p>
<?php
}
add_meta_box('pageorderdiv', __('Page Order'), 'page_order_meta_box', 'page', 'normal', 'core');


$authors = get_editable_user_ids( $current_user->id ); // TODO: ROLE SYSTEM
if ( $post->post_author && !in_array($post->post_author, $authors) )
	$authors[] = $post->post_author;
if ( $authors && count( $authors ) > 1 ) {
	function page_author_meta_box($post){
		global $current_user, $user_ID;
		$authors = get_editable_user_ids( $current_user->id ); // TODO: ROLE SYSTEM
		if ( $post->post_author && !in_array($post->post_author, $authors) )
			$authors[] = $post->post_author;
?>
<label class="hidden" for="post_author_override"><?php _e('Page Author'); ?></label><?php wp_dropdown_users( array('include' => $authors, 'name' => 'post_author_override', 'selected' => empty($post->ID) ? $user_ID : $post->post_author) ); ?>
<?php
	}
	add_meta_box('pageauthordiv', __('Page Author'), 'page_author_meta_box', 'page', 'normal', 'core');
}


if ( 0 < $post_ID && wp_get_post_revisions( $post_ID ) ) :
function page_revisions_meta_box($post) {
	wp_list_post_revisions();
}
add_meta_box('revisionsdiv', __('Page Revisions'), 'page_revisions_meta_box', 'page', 'normal', 'core');
endif;
?>

<div id="edit-settings">
<a href="#edit_settings" id="show-settings-link" class="hide-if-no-js show-settings"><?php _e('Page Options') ?></a>
<div id="edit-settings-wrap" class="hidden">
<a href="#edit_settings" id="hide-settings-link" class="show-settings"><?php _e('Hide Options') ?></a>
<h5><?php _e('Show on screen') ?></h5>
<form id="adv-settings" action="" method="get">
<div class="metabox-prefs">
<?php meta_box_prefs('page') ?>
<?php wp_nonce_field( 'hiddencolumns', 'hiddencolumnsnonce', false ); ?>
<br class="clear" />
</div></form>
</div></div>

<div class="wrap">

<form name="post" action="page.php" method="post" id="post">
<?php if ( $notice ) : ?>
<div id="notice" class="error"><p><?php echo $notice ?></p></div>
<?php endif; ?>
<?php if (isset($_GET['message'])) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php endif; ?>

<?php
wp_nonce_field($nonce_action);

if (isset($mode) && 'bookmarklet' == $mode)
	echo '<input type="hidden" name="mode" value="bookmarklet" />';
?>
<input type="hidden" id="user-id" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" id="hiddenaction" name="action" value='<?php echo $form_action ?>' />
<input type="hidden" id="originalaction" name="originalaction" value="<?php echo $form_action ?>" />
<input type="hidden" id="post_author" name="post_author" value="<?php echo attribute_escape( $post->post_author ); ?>" />
<?php echo $form_extra ?>
<input type="hidden" id="post_type" name="post_type" value="<?php echo $post->post_type ?>" />
<input type="hidden" id="original_post_status" name="original_post_status" value="<?php echo $post->post_status ?>" />
<input name="referredby" type="hidden" id="referredby" value="<?php echo clean_url(stripslashes(wp_get_referer())); ?>" />
<?php if ( 'draft' != $post->post_status ) wp_original_referer_field(true, 'previous'); ?>

<!-- TODO
<div class="inside">
<p><strong><label for='post_status'><?php _e('Publish Status') ?></label></strong></p>
<p>
<select name='post_status' tabindex='4' id='post_status'>
<?php // Show publish in dropdown if user can publish or if they can re-publish this page ('edit_published_pages')
// 'publish' option will be selected for published AND private posts (checkbox overrides dropdown)
if ( current_user_can('publish_pages') OR ( $post->post_status == 'publish' AND current_user_can('edit_page', $post->ID) ) ) : 
?>
<option<?php selected( $post->post_status, 'publish' ); selected( $post->post_status, 'private' );?> value='publish'><?php _e('Published') ?></option>
<?php endif; ?>
<?php if ( 'future' == $post->post_status ) : ?>
<option<?php selected( $post->post_status, 'future' ); ?> value='future'><?php _e('Pending') ?></option>
<?php endif; ?>
<option<?php selected( $post->post_status, 'pending' ); ?> value='pending'><?php _e('Pending Review') ?></option>
<option<?php selected( $post->post_status, 'draft' ); ?> value='draft'><?php _e('Unpublished') ?></option>
</select>
</p>
<?php if ( current_user_can( 'publish_posts' ) ) : ?> 
<p><label for="post_status_private2" class="selectit"><input id="post_status_private2" name="post_status" type="checkbox" value="private" <?php checked($post->post_status, 'private'); ?> tabindex='4' /> <?php _e('Keep this page private') ?></label></p>
<?php endif; ?>

<h5><?php _e('Related') ?></h5>
<ul>
<?php if ($post_ID): ?>
<li><a href="edit-pages.php?page_id=<?php echo $post_ID ?>"><?php _e('See Comments on this Page') ?></a></li>
<?php endif; ?>
<li><a href="edit-comments.php"><?php _e('Manage All Comments') ?></a></li>
<li><a href="edit-pages.php"><?php _e('Manage All Pages') ?></a></li>
<?php do_action('page_relatedlinks_list'); ?>
</ul>

-->

<div id="poststuff">

<div id="side-info-column" class="inner-sidebar">

<?php

do_action('submitpage_box');
$side_meta_boxes = do_meta_boxes('page', 'side', $post);

?>
</div>

<div id="post-body" class="<?php echo $side_meta_boxes ? 'has-sidebar' : ''; ?>">
<div id="post-body-content" class="has-sidebar-content">

<div id="titlediv">
<h3><label for="title"><?php _e('Title') ?></label></h3>
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

<div id="add-media-button"><a id="add-media-link" href="<?php echo clean_url( admin_url( 'media-upload.php?post_id=' . ( $post_ID ? $post_ID : $temp_ID ) . '&amp;type=image&amp;TB_iframe=true' ) ); ?>" class="thickbox button"><?php _e( 'Insert Media' ); ?></a></div>

<h3><?php _e('Page') ?></h3>
<?php the_editor($post->post_content); ?>
<div id="post-status-info">
	<span id="wp-word-count" class="alignleft"></span>
	<span class="alignright">
	<span id="autosave">&nbsp;</span>

<?php
	if ($post_ID) {
		if ( $last_id = get_post_meta($post_ID, '_edit_last', true) ) {
			$last_user = get_userdata($last_id);
			printf(__('Last edited by %1$s on %2$s at %3$s'), wp_specialchars( $last_user->display_name ), mysql2date(get_option('date_format'), $post->post_modified), mysql2date(get_option('time_format'), $post->post_modified));
		} else {
			printf(__('Last edited on %1$s at %2$s'), mysql2date(get_option('date_format'), $post->post_modified), mysql2date(get_option('time_format'), $post->post_modified));
		}
	}
?>
	</span>
	<br class="clear" />
</div>


<?php wp_nonce_field( 'autosave', 'autosavenonce', false ); ?>
<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
<?php wp_nonce_field( 'getpermalink', 'getpermalinknonce', false ); ?>
<?php wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false ); ?>
<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
</div>

<?php

do_meta_boxes('page', 'normal', $post);
do_action('edit_page_form');
do_meta_boxes('page', 'advanced', $post);

?>

</div>
</div>
</div>

</form>
</div>

<script type="text/javascript">
try{document.post.title.focus();}catch(e){}
</script>
