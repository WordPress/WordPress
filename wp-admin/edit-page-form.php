<?php
/**
 * Edit page form for inclusion in the administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Post ID global.
 * @name $post_ID
 * @var int
 */
if ( ! isset( $post_ID ) )
	$post_ID = 0;
if ( ! isset( $temp_ID ) )
	$temp_ID = 0;

if ( isset($_GET['message']) )
	$_GET['message'] = absint( $_GET['message'] );
$messages[1] = sprintf(__('Page updated. <a href="%s">View page</a>'), get_permalink($post_ID));
$messages[2] = __('Custom field updated.');
$messages[3] = __('Custom field deleted.');
$messages[5] = sprintf(__('Page published. <a href="%s">View page</a>'), get_permalink($post_ID));
$messages[6] = sprintf(__('Page submitted. <a href="%s">Preview page</a>'), add_query_arg( 'preview', 'true', get_permalink($post_ID) ) );

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
	$autosave = wp_get_post_autosave( $post_ID );
	if ( $autosave && mysql2date( 'U', $autosave->post_modified_gmt, false ) > mysql2date( 'U', $post->post_modified_gmt, false ) )
		$notice = sprintf( $notices[1], get_edit_post_link( $autosave->ID ) );
}

$temp_ID = (int) $temp_ID;
$user_ID = (int) $user_ID;

/**
 * Display submit form fields.
 *
 * @since 2.7.0
 *
 * @param object $post
 */
function page_submit_meta_box($post) {
	global $action;

	$can_publish = current_user_can('publish_pages');
?>
<div class="submitbox" id="submitpage">

<div id="minor-publishing">

<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
<div style="display:none;">
<input type="submit" name="save" value="<?php esc_attr_e('Save'); ?>" />
</div>

<div id="minor-publishing-actions">
<div id="save-action">
<?php if ( 'publish' != $post->post_status && 'future' != $post->post_status && 'pending' != $post->post_status )  { ?>
<input <?php if ( 'private' == $post->post_status ) { ?>style="display:none"<?php } ?> type="submit" name="save" id="save-post" value="<?php esc_attr_e('Save Draft'); ?>" tabindex="4" class="button button-highlighted" />
<?php } elseif ( 'pending' == $post->post_status && $can_publish ) { ?>
<input type="submit" name="save" id="save-post" value="<?php esc_attr_e('Save as Pending'); ?>" tabindex="4" class="button button-highlighted" />
<?php } ?>
</div>

<div id="preview-action">
<?php
if ( 'publish' == $post->post_status ) {
	$preview_link = clean_url(get_permalink($post->ID));
	$preview_button = __('Preview Changes');
} else {
	$preview_link = clean_url(apply_filters('preview_post_link', add_query_arg('preview', 'true', get_permalink($post->ID))));
	$preview_button = __('Preview');
}
?>
<a class="preview button" href="<?php echo $preview_link; ?>" target="wp-preview" id="post-preview" tabindex="4"><?php echo $preview_button; ?></a>
<input type="hidden" name="wp-preview" id="wp-preview" value="" />
</div>

<div class="clear"></div>
</div><?php // /minor-publishing-actions ?>

<div id="misc-publishing-actions">

<div class="misc-pub-section<?php if ( !$can_publish ) { echo ' misc-pub-section-last'; } ?>"><label for="post_status"><?php _e('Status:') ?></label>
<span id="post-status-display">
<?php
switch ( $post->post_status ) {
	case 'private':
		_e('Privately Published');
		break;
	case 'publish':
		_e('Published');
		break;
	case 'future':
		_e('Scheduled');
		break;
	case 'pending':
		_e('Pending Review');
		break;
	case 'draft':
		_e('Draft');
		break;
}
?>
</span>
<?php if ( 'publish' == $post->post_status || 'private' == $post->post_status || $can_publish ) { ?>
<a href="#post_status" <?php if ( 'private' == $post->post_status ) { ?>style="display:none;" <?php } ?>class="edit-post-status hide-if-no-js" tabindex='4'><?php _e('Edit') ?></a>

<div id="post-status-select" class="hide-if-js">
<input type="hidden" name="hidden_post_status" id="hidden_post_status" value="<?php echo esc_attr($post->post_status); ?>" />
<select name='post_status' id='post_status' tabindex='4'>
<?php if ( 'publish' == $post->post_status ) : ?>
<option<?php selected( $post->post_status, 'publish' ); ?> value='publish'><?php _e('Published') ?></option>
<?php elseif ( 'private' == $post->post_status ) : ?>
<option<?php selected( $post->post_status, 'private' ); ?> value='publish'><?php _e('Privately Published') ?></option>
<?php elseif ( 'future' == $post->post_status ) : ?>
<option<?php selected( $post->post_status, 'future' ); ?> value='future'><?php _e('Scheduled') ?></option>
<?php endif; ?>
<option<?php selected( $post->post_status, 'pending' ); ?> value='pending'><?php _e('Pending Review') ?></option>
<option<?php selected( $post->post_status, 'draft' ); ?> value='draft'><?php _e('Draft') ?></option>
</select>

 <a href="#post_status" class="save-post-status hide-if-no-js button"><?php _e('OK'); ?></a>
 <a href="#post_status" class="cancel-post-status hide-if-no-js"><?php _e('Cancel'); ?></a>
</div>

<?php } ?>
</div><?php // /misc-pub-section ?>

<div class="misc-pub-section " id="visibility">
<?php _e('Visibility:'); ?> <span id="post-visibility-display"><?php

if ( 'private' == $post->post_status ) {
	$post->post_password = '';
	$visibility = 'private';
	$visibility_trans = __('Private');
} elseif ( !empty( $post->post_password ) ) {
	$visibility = 'password';
	$visibility_trans = __('Password protected');
} else {
	$visibility = 'public';
	$visibility_trans = __('Public');
}

echo esc_html( $visibility_trans ); ?></span>
<?php if ( $can_publish ) { ?>
<a href="#visibility" class="edit-visibility hide-if-no-js"><?php _e('Edit'); ?></a>

<div id="post-visibility-select" class="hide-if-js">
<input type="hidden" name="hidden_post_password" id="hidden-post-password" value="<?php echo esc_attr($post->post_password); ?>" />
<input type="hidden" name="hidden_post_visibility" id="hidden-post-visibility" value="<?php echo esc_attr( $visibility ); ?>" />

<input type="radio" name="visibility" id="visibility-radio-public" value="public" <?php checked( $visibility, 'public' ); ?> /> <label for="visibility-radio-public" class="selectit"><?php _e('Public'); ?></label><br />
<input type="radio" name="visibility" id="visibility-radio-password" value="password" <?php checked( $visibility, 'password' ); ?> /> <label for="visibility-radio-password" class="selectit"><?php _e('Password protected'); ?></label><br />
<span id="password-span"><label for="post_password"><?php _e('Password:'); ?></label> <input type="text" name="post_password" id="post_password" value="<?php echo esc_attr($post->post_password); ?>" /><br /></span>
<input type="radio" name="visibility" id="visibility-radio-private" value="private" <?php checked( $visibility, 'private' ); ?> /> <label for="visibility-radio-private" class="selectit"><?php _e('Private'); ?></label><br />

<p><a href="#visibility" class="save-post-visibility hide-if-no-js button"><?php _e('OK'); ?></a>
<a href="#visibility" class="cancel-post-visibility hide-if-no-js"><?php _e('Cancel'); ?></a></p>
</div>
<?php } ?>

</div><?php // /misc-pub-section ?>

<?php
// translators: Publish box date formt, see http://php.net/date
$datef = __( 'M j, Y @ G:i' );
if ( 0 != $post->ID ) {
	if ( 'future' == $post->post_status ) { // scheduled for publishing at a future date
		$stamp = __('Scheduled for: <b>%1$s</b>');
	} else if ( 'publish' == $post->post_status || 'private' == $post->post_status ) { // already published
		$stamp = __('Published on: <b>%1$s</b>');
	} else if ( '0000-00-00 00:00:00' == $post->post_date_gmt ) { // draft, 1 or more saves, no date specified
		$stamp = __('Publish <b>immediately</b>');
	} else if ( time() < strtotime( $post->post_date_gmt . ' +0000' ) ) { // draft, 1 or more saves, future date specified
		$stamp = __('Schedule for: <b>%1$s</b>');
	} else { // draft, 1 or more saves, date specified
		$stamp = __('Publish on: <b>%1$s</b>');
	}
	$date = date_i18n( $datef, strtotime( $post->post_date ) );
} else { // draft (no saves, and thus no date specified)
	$stamp = __('Publish <b>immediately</b>');
	$date = date_i18n( $datef, strtotime( current_time('mysql') ) );
}

if ( $can_publish ) : // Contributors don't get to choose the date of publish ?>
<div class="misc-pub-section curtime misc-pub-section-last">
	<span id="timestamp"><?php printf($stamp, $date); ?></span>
	<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" tabindex='4'><?php _e('Edit') ?></a>
	<div id="timestampdiv" class="hide-if-js"><?php touch_time(($action == 'edit'),1,4); ?></div>
</div><?php // /misc-pub-section
endif; ?>

</div>
<div class="clear"></div>
</div>

<div id="major-publishing-actions">
<?php do_action('post_submitbox_start'); ?>
<div id="delete-action">
<?php
if ( ( 'edit' == $action ) && current_user_can('delete_page', $post->ID) ) { ?>
<a class="submitdelete deletion" href="<?php echo wp_nonce_url("page.php?action=delete&amp;post=$post->ID", 'delete-page_' . $post->ID); ?>" onclick="if ( confirm('<?php echo esc_js(sprintf( ('draft' == $post->post_status) ? __("You are about to delete this draft '%s'\n  'Cancel' to stop, 'OK' to delete.") : __("You are about to delete this page '%s'\n  'Cancel' to stop, 'OK' to delete."), $post->post_title )); ?>') ) {return true;}return false;"><?php _e('Delete'); ?></a>
<?php } ?>
</div>

<div id="publishing-action">
<?php
if ( !in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ) { ?>
<?php
	if ( $can_publish ) :
		if ( !empty($post->post_date_gmt) && time() < strtotime( $post->post_date_gmt . ' +0000' ) ) : ?>
		<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Schedule') ?>" />
		<input name="publish" type="submit" class="button-primary" id="publish" tabindex="5" accesskey="p" value="<?php esc_attr_e('Schedule') ?>" />
<?php	else : ?>
		<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Publish') ?>" />
		<input name="publish" type="submit" class="button-primary" id="publish" tabindex="5" accesskey="p" value="<?php esc_attr_e('Publish') ?>" />
<?php	endif;
	else : ?>
	<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Submit for Review') ?>" />
	<input name="publish" type="submit" class="button-primary" id="publish" tabindex="5" accesskey="p" value="<?php esc_attr_e('Submit for Review') ?>" />
<?php
	endif;
} else { ?>
	<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Update Page') ?>" />
	<input name="save" type="submit" class="button-primary" id="publish" tabindex="5" accesskey="p" value="<?php esc_attr_e('Update Page') ?>" />
<?php
} ?>
</div>
<div class="clear"></div>
</div>
</div>
<?php
}
add_meta_box('pagesubmitdiv', __('Publish'), 'page_submit_meta_box', 'page', 'side', 'core');

/**
 * Display page password form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function page_password_meta_box($post){
?>
<p><label for="post_status_private" class="selectit"><input id="post_status_private" name="post_status" type="checkbox" value="private" <?php checked($post->post_status, 'private'); ?> tabindex='4' /> <?php _e('Keep this page private') ?></label></p>
<h4><?php _e( 'Page Password' ); ?></h4>
<p><label class="screen-reader-text" for="post_password"><?php _e('Password Protect This Page') ?></label><input name="post_password" type="text" size="25" id="post_password" value="<?php the_post_password(); ?>" /></p>
<p><?php _e('Setting a password will require people who visit your blog to enter the above password to view this page and its comments.'); ?></p>
<?php
}
// add_meta_box('pagepassworddiv', __('Privacy Options'), 'page_password_meta_box', 'page', 'side', 'core');

/**
 * Display page attributes form fields.
 *
 * @since 2.7.0
 *
 * @param object $post
 */
function page_attributes_meta_box($post){
?>
<h5><?php _e('Parent') ?></h5>
<label class="screen-reader-text" for="parent_id"><?php _e('Page Parent') ?></label>
<?php wp_dropdown_pages(array('exclude_tree' => $post->ID, 'selected' => $post->post_parent, 'name' => 'parent_id', 'show_option_none' => __('Main Page (no parent)'), 'sort_column'=> 'menu_order, post_title')); ?>
<p><?php _e('You can arrange your pages in hierarchies, for example you could have an &#8220;About&#8221; page that has &#8220;Life Story&#8221; and &#8220;My Dog&#8221; pages under it. There are no limits to how deeply nested you can make pages.'); ?></p>
<?php
	if ( 0 != count( get_page_templates() ) ) { ?>
<h5><?php _e('Template') ?></h5>
<label class="screen-reader-text" for="page_template"><?php _e('Page Template') ?></label><select name="page_template" id="page_template">
<option value='default'><?php _e('Default Template'); ?></option>
<?php page_template_dropdown($post->page_template); ?>
</select>
<p><?php _e('Some themes have custom templates you can use for certain pages that might have additional features or custom layouts. If so, you&#8217;ll see them above.'); ?></p>
<?php
	} ?>
<h5><?php _e('Order') ?></h5>
<p><label class="screen-reader-text" for="menu_order"><?php _e('Page Order') ?></label><input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo esc_attr($post->menu_order) ?>" /></p>
<p><?php _e('Pages are usually ordered alphabetically, but you can put a number above to change the order pages appear in. (We know this is a little janky, it&#8217;ll be better in future releases.)'); ?></p>
<?php
}
add_meta_box('pageparentdiv', __('Attributes'), 'page_attributes_meta_box', 'page', 'side', 'core');

/**
 * Display custom field for page form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function page_custom_meta_box($post){
?>
<div id="postcustomstuff">
<?php
	$metadata = has_meta($post->ID);
	list_meta($metadata);
	meta_form(); ?>
	<div id="ajax-response"></div>
</div>
<p><?php _e('Custom fields can be used to add extra metadata to a post that you can <a href="http://codex.wordpress.org/Using_Custom_Fields" target="_blank">use in your theme</a>.'); ?></p>
<?php
}
add_meta_box('pagecustomdiv', __('Custom Fields'), 'page_custom_meta_box', 'page', 'normal', 'core');

/**
 * Display comments status form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
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
add_meta_box('pagecommentstatusdiv', __('Discussion'), 'page_comments_status_meta_box', 'page', 'normal', 'core');

/**
 * Display page slug form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function page_slug_meta_box($post){
?>
<label class="screen-reader-text" for="post_name"><?php _e('Page Slug') ?></label><input name="post_name" type="text" size="13" id="post_name" value="<?php echo esc_attr( $post->post_name ); ?>" />
<?php
}
add_meta_box('pageslugdiv', __('Page Slug'), 'page_slug_meta_box', 'page', 'normal', 'core');

$authors = get_editable_user_ids( $current_user->id, true, 'page' ); // TODO: ROLE SYSTEM
if ( $post->post_author && !in_array($post->post_author, $authors) )
	$authors[] = $post->post_author;
if ( $authors && count( $authors ) > 1 ) {
	/**
	 * Display page author form fields, when more than one author exists.
	 *
	 * @since 2.6.0
	 *
	 * @param object $post
	 */
	function page_author_meta_box($post){
		global $current_user, $user_ID;
		$authors = get_editable_user_ids( $current_user->id, true, 'page' ); // TODO: ROLE SYSTEM
		if ( $post->post_author && !in_array($post->post_author, $authors) )
			$authors[] = $post->post_author;
?>
<label class="screen-reader-text" for="post_author_override"><?php _e('Page Author'); ?></label><?php wp_dropdown_users( array('include' => $authors, 'name' => 'post_author_override', 'selected' => empty($post->ID) ? $user_ID : $post->post_author) ); ?>
<?php
	}
	add_meta_box('pageauthordiv', __('Page Author'), 'page_author_meta_box', 'page', 'normal', 'core');
}

if ( 0 < $post_ID && wp_get_post_revisions( $post_ID ) ) :
/**
 * Display list of page revisions.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function page_revisions_meta_box($post) {
	wp_list_post_revisions();
}
add_meta_box('revisionsdiv', __('Page Revisions'), 'page_revisions_meta_box', 'page', 'normal', 'core');
endif;

do_action('do_meta_boxes', 'page', 'normal', $post);
do_action('do_meta_boxes', 'page', 'advanced', $post);
do_action('do_meta_boxes', 'page', 'side', $post);

require_once('admin-header.php');
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

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
<input type="hidden" id="hiddenaction" name="action" value='<?php echo esc_attr($form_action) ?>' />
<input type="hidden" id="originalaction" name="originalaction" value="<?php echo esc_attr($form_action) ?>" />
<input type="hidden" id="post_author" name="post_author" value="<?php echo esc_attr( $post->post_author ); ?>" />
<?php echo $form_extra ?>
<input type="hidden" id="post_type" name="post_type" value="<?php echo esc_attr($post->post_type) ?>" />
<input type="hidden" id="original_post_status" name="original_post_status" value="<?php echo esc_attr($post->post_status) ?>" />
<input name="referredby" type="hidden" id="referredby" value="<?php echo clean_url(stripslashes(wp_get_referer())); ?>" />
<?php if ( 'draft' != $post->post_status ) wp_original_referer_field(true, 'previous'); ?>

<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">

<div id="side-info-column" class="inner-sidebar">
<?php
do_action('submitpage_box');
$side_meta_boxes = do_meta_boxes('page', 'side', $post); ?>
</div>

<div id="post-body">
<div id="post-body-content">
<div id="titlediv">
<div id="titlewrap">
	<label class="screen-reader-text" for="title"><?php _e('Title') ?></label>
	<input type="text" name="post_title" size="30" tabindex="1" value="<?php echo esc_attr( htmlspecialchars( $post->post_title ) ); ?>" id="title" autocomplete="off" />
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

<?php the_editor($post->post_content); ?>
<table id="post-status-info" cellspacing="0"><tbody><tr>
	<td id="wp-word-count"></td>
	<td class="autosave-info">
	<span id="autosave">&nbsp;</span>

<?php
	if ($post_ID) {
		if ( $last_id = get_post_meta($post_ID, '_edit_last', true) ) {
			$last_user = get_userdata($last_id);
			printf(__('Last edited by %1$s on %2$s at %3$s'), esc_html( $last_user->display_name ), mysql2date(get_option('date_format'), $post->post_modified), mysql2date(get_option('time_format'), $post->post_modified));
		} else {
			printf(__('Last edited on %1$s at %2$s'), mysql2date(get_option('date_format'), $post->post_modified), mysql2date(get_option('time_format'), $post->post_modified));
		}
	}
?>
	</td>
</tr></tbody></table>

<?php
wp_nonce_field( 'autosave', 'autosavenonce', false );
wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
wp_nonce_field( 'getpermalink', 'getpermalinknonce', false );
wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false );
wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
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
