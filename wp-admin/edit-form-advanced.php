<?php

$action = isset($action) ? $action : '';
if ( isset($_GET['message']) )
	$_GET['message'] = absint( $_GET['message'] );
$messages[1] = sprintf( __( 'Post updated. Continue editing below or <a href="%s">go back</a>.' ), attribute_escape( stripslashes( $_GET['_wp_original_http_referer'] ) ) );
$messages[2] = __('Custom field updated.');
$messages[3] = __('Custom field deleted.');
$messages[4] = __('Post updated.');
$messages[5] = sprintf( __('Post restored to revision from %s'), wp_post_revision_title( $_GET['revision'], false ) );

$notice = false;
$notices[1] = __( 'There is an autosave of this post that is more recent than the version below.  <a href="%s">View the autosave</a>.' );

if ( !isset($post_ID) || 0 == $post_ID ) {
	$form_action = 'post';
	$temp_ID = -1 * time(); // don't change this formula without looking at wp_write_post()
	$form_extra = "<input type='hidden' id='post_ID' name='temp_ID' value='$temp_ID' />";
	$autosave = false;
} else {
	$post_ID = (int) $post_ID;
	$form_action = 'editpost';
	$form_extra = "<input type='hidden' id='post_ID' name='post_ID' value='$post_ID' />";
	$autosave = wp_get_post_autosave( $post_id );

	// Detect if there exists an autosave newer than the post and if that autosave is different than the post
	if ( $autosave && mysql2date( 'U', $autosave->post_modified_gmt ) > mysql2date( 'U', $post->post_modified_gmt ) ) {
		foreach ( _wp_post_revision_fields() as $autosave_field => $_autosave_field ) {
			if ( wp_text_diff( $autosave->$autosave_field, $post->$autosave_field ) ) {
				$notice = sprintf( $notices[1], get_edit_post_link( $autosave->ID ) );
				break;
			}
		}
		unset($autosave_field, $_autosave_field);
	}
}

?>
<?php if ( $notice ) : ?>
<div id="notice" class="error"><p><?php echo $notice ?></p></div>
<?php endif; ?>
<?php if (isset($_GET['message'])) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php endif; ?>

<form name="post" action="post.php" method="post" id="post">
<?php if ( (isset($mode) && 'bookmarklet' == $mode) || isset($_GET['popupurl']) ): ?>
<input type="hidden" name="mode" value="bookmarklet" />
<?php endif; ?>

<div class="wrap">
<h2><?php _e('Write Post') ?></h2>
<?php

if ( !isset($post_ID) || 0 == $post_ID)
	wp_nonce_field('add-post');
else
	wp_nonce_field('update-post_' .  $post_ID);

$form_pingback = '<input type="hidden" name="post_pingback" value="' . (int) get_option('default_pingback_flag') . '" id="post_pingback" />';

$form_prevstatus = '<input type="hidden" name="prev_status" value="' . attribute_escape( $post->post_status ) . '" />';

$saveasdraft = '<input name="save" type="submit" id="save" class="button" tabindex="3" value="' . attribute_escape( __('Save and Continue Editing') ) . '" />';

?>

<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<input type="hidden" id="hiddenaction" name="action" value="<?php echo $form_action ?>" />
<input type="hidden" id="originalaction" name="originalaction" value="<?php echo $form_action ?>" />
<input type="hidden" id="post_author" name="post_author" value="<?php echo attribute_escape( $post->post_author ); ?>" />
<input type="hidden" id="post_type" name="post_type" value="<?php echo $post->post_type ?>" />
<input type="hidden" id="original_post_status" name="original_post_status" value="<?php echo $post->post_status ?>" />
<input name="referredby" type="hidden" id="referredby" value="<?php
if ( !empty($_REQUEST['popupurl']) )
	echo clean_url(stripslashes($_REQUEST['popupurl']));
else if ( strpos( wp_get_referer(), '/wp-admin/' ) === false && $post_ID && url_to_postid(wp_get_referer()) === $post_ID  )
	echo 'redo';
else
	echo clean_url(stripslashes(wp_get_referer()));
?>" />
<?php if ( 'draft' != $post->post_status ) wp_original_referer_field(true, 'previous'); ?>

<?php echo $form_extra ?>

<div id="poststuff">

<div class="submitbox" id="submitpost">

<div id="previewview">
<?php if ( 'publish' == $post->post_status ) { ?>
<a href="<?php echo clean_url(get_permalink($post->ID)); ?>" target="_blank" tabindex="4"><?php _e('View this Post'); ?></a>
<?php } elseif ( 'edit' == $action ) { ?>
<a href="<?php echo clean_url(apply_filters('preview_post_link', add_query_arg('preview', 'true', get_permalink($post->ID)))); ?>" target="_blank"  tabindex="4"><?php _e('Preview this Post'); ?></a>
<?php } ?>
</div>

<div class="inside">

<p><strong><label for='post_status'><?php _e('Publish Status') ?></label></strong></p>
<p>
<select name='post_status' id='post_status' tabindex='4'>
<?php 
// only show the publish menu item if they are allowed to publish posts or they are allowed to edit this post (accounts for 'edit_published_posts' capability) 
if ( current_user_can('publish_posts') OR ( $post->post_status == 'publish' AND current_user_can('edit_post', $post->ID) ) ) :
?>
<option<?php selected( $post->post_status, 'publish' ); selected( $post->post_status, 'private' );?> value='publish'><?php _e('Published') ?></option>
<?php if ( 'future' == $post->post_status ) : ?>
<option<?php selected( $post->post_status, 'future' ); ?> value='future'><?php _e('Scheduled') ?></option>
<?php endif; ?>
<?php endif; ?>
<option<?php selected( $post->post_status, 'pending' ); ?> value='pending'><?php _e('Pending Review') ?></option>
<option<?php selected( $post->post_status, 'draft' ); ?> value='draft'><?php _e('Unpublished') ?></option>
</select>
</p>

<?php if ( current_user_can( 'publish_posts' ) ) : ?>
<p><label for="post_status_private" class="selectit"><input id="post_status_private" name="post_status" type="checkbox" value="private" <?php checked($post->post_status, 'private'); ?> tabindex="4" /> <?php _e('Keep this post private') ?></label></p>
<?php endif; ?>
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
<?php if ( current_user_can( 'publish_posts' ) ) : // Contributors don't get to choose the date of publish ?>
<p class="curtime"><?php printf($stamp, $date, $time); ?>
&nbsp;<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" tabindex='4'><?php _e('Edit') ?></a></p>

<div id='timestampdiv' class='hide-if-js'><?php touch_time(($action == 'edit'),1,4); ?></div>
<?php endif; ?>

</div>

<p class="submit">
<input type="submit" name="save" id="save-post" value="<?php _e('Save'); ?>" tabindex="4" class="button button-highlighted" />
<?php
if ( !in_array( $post->post_status, array('publish', 'future') ) || 0 == $post_ID ) {
?>
<?php if ( current_user_can('publish_posts') ) : ?>
	<input name="publish" type="submit" class="button" id="publish" tabindex="5" accesskey="p" value="<?php _e('Publish') ?>" />
<?php else : ?>
	<input name="publish" type="submit" class="button" id="publish" tabindex="5" accesskey="p" value="<?php _e('Submit for Review') ?>" />
<?php endif; ?>
<?php
}

if ( ( 'edit' == $action) && current_user_can('delete_post', $post_ID) )
	echo "<a class='submitdelete' href='" . wp_nonce_url("post.php?action=delete&amp;post=$post_ID", 'delete-post_' . $post_ID) . "' onclick=\"if ( confirm('" . js_escape(sprintf( ('draft' == $post->post_status) ? __("You are about to delete this draft '%s'\n  'Cancel' to stop, 'OK' to delete.") : __("You are about to delete this post '%s'\n  'Cancel' to stop, 'OK' to delete."), $post->post_title )) . "') ) { return true;}return false;\">" . __('Delete&nbsp;post') . "</a>";
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
<span id="wp-word-count"></span>
</p>

<div class="side-info">
<h5><?php _e('Related') ?></h5>

<ul>
<?php if ($post_ID): ?>
<li><a href="edit.php?p=<?php echo $post_ID ?>"><?php _e('See Comments on this Post') ?></a></li>
<?php endif; ?>
<li><a href="edit-comments.php"><?php _e('Manage All Comments') ?></a></li>
<li><a href="edit.php"><?php _e('Manage All Posts') ?></a></li>
<li><a href="categories.php"><?php _e('Manage All Categories') ?></a></li>
<li><a href="edit-tags.php"><?php _e('Manage All Tags') ?></a></li>
<li><a href="edit.php?post_status=draft"><?php _e('View Drafts'); ?></a></li>
<?php do_action('post_relatedlinks_list'); ?>
</ul>

<h5><?php _e('Shortcuts') ?></h5>
<p><?php _e('Drag-and-drop the following link to your bookmarks bar or right click it and add it to your favorites for a posting shortcut.') ?>  <a href="<?php echo get_shortcut_link(); ?>" title="<?php echo attribute_escape(__('Press This')) ?>"><?php _e('Press This') ?></a></p>
</div>

<?php do_action('submitpost_box'); ?>
</div>

<div id="post-body">
<div id="titlediv">
<h3><label for="title"><?php _e('Title') ?></label></h3>
<div id="titlewrap">
	<input type="text" name="post_title" size="30" tabindex="1" value="<?php echo attribute_escape($post->post_title); ?>" id="title" autocomplete="off" />
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
<h3><label for="content"><?php _e('Post') ?></label></h3>
<?php the_editor($post->post_content); ?>
<?php wp_nonce_field( 'autosave', 'autosavenonce', false ); ?>
<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
<?php wp_nonce_field( 'getpermalink', 'getpermalinknonce', false ); ?>
<?php wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false ); ?>
</div>

<?php echo $form_pingback ?>
<?php echo $form_prevstatus ?>

<?php
function post_tags_meta_box($post) {
?>
<p id="jaxtag"><label class="hidden" for="newtag"><?php _e('Tags'); ?></label><input type="text" name="tags_input" class="tags-input" id="tags-input" size="40" tabindex="3" value="<?php echo get_tags_to_edit( $post->ID ); ?>" /></p>
<div id="tagchecklist"></div>
<?php
}
add_meta_box('tagsdiv', __('Tags'), 'post_tags_meta_box', 'post', 'normal', 'core');

function post_categories_meta_box($post) {
?>
<div id="category-adder" class="wp-hidden-children">
	<h4><a id="category-add-toggle" href="#category-add" class="hide-if-no-js" tabindex="3"><?php _e( '+ Add New Category' ); ?></a></h4>
	<p id="category-add" class="wp-hidden-child">
		<label class="hidden" for="newcat"><?php _e( 'Add New Category' ); ?></label><input type="text" name="newcat" id="newcat" class="form-required form-input-tip" value="<?php _e( 'New category name' ); ?>" tabindex="3" aria-required="true"/>
		<label class="hidden" for="newcat_parent"><?php _e('Parent category'); ?>:</label><?php wp_dropdown_categories( array( 'hide_empty' => 0, 'name' => 'newcat_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => __('Parent category'), 'tab_index' => 3 ) ); ?>
		<input type="button" id="category-add-sumbit" class="add:categorychecklist:category-add button" value="<?php _e( 'Add' ); ?>" tabindex="3" />
		<?php wp_nonce_field( 'add-category', '_ajax_nonce', false ); ?>
		<span id="category-ajax-response"></span>
	</p>
</div>

<ul id="category-tabs">
	<li class="ui-tabs-selected"><a href="#categories-all" tabindex="3"><?php _e( 'All Categories' ); ?></a></li>
	<li class="wp-no-js-hidden"><a href="#categories-pop" tabindex="3"><?php _e( 'Most Used' ); ?></a></li>
</ul>

<div id="categories-pop" class="ui-tabs-panel" style="display: none;">
	<ul id="categorychecklist-pop" class="categorychecklist form-no-clear" >
		<?php $popular_ids = wp_popular_terms_checklist('category'); ?>
	</ul>
</div>

<div id="categories-all" class="ui-tabs-panel">
	<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
		<?php wp_category_checklist($post->ID, false, false, $popular_ids) ?>
	</ul>
</div>
<?php
}
add_meta_box('categorydiv', __('Categories'), 'post_categories_meta_box', 'post', 'normal', 'core');
?>

<?php do_meta_boxes('post', 'normal', $post); ?>

<?php do_action('edit_form_advanced'); ?>

<h2><?php _e('Advanced Options'); ?></h2>

<?php
function post_excerpt_meta_box($post) {
?>
<label class="hidden" for="excerpt"><?php _e('Excerpt') ?></label><textarea rows="1" cols="40" name="excerpt" tabindex="6" id="excerpt"><?php echo $post->post_excerpt ?></textarea>
<p><?php _e('Excerpts are optional hand-crafted summaries of your content. You can <a href="http://codex.wordpress.org/Template_Tags/the_excerpt" target="_blank">use them in your template</a>'); ?></p>
<?php
}
add_meta_box('postexcerpt', __('Excerpt'), 'post_excerpt_meta_box', 'post', 'advanced', 'core');

function post_trackback_meta_box($post) {
	$form_trackback = '<input type="text" name="trackback_url" style="width: 415px" id="trackback" tabindex="7" value="'. attribute_escape( str_replace("\n", ' ', $post->to_ping) ) .'" />';
	if ('' != $post->pinged) {
		$pings = '<p>'. __('Already pinged:') . '</p><ul>';
		$already_pinged = explode("\n", trim($post->pinged));
		foreach ($already_pinged as $pinged_url) {
			$pings .= "\n\t<li>" . wp_specialchars($pinged_url) . "</li>";
		}
		$pings .= '</ul>';
	}

?>
<p><label for="trackback"><?php _e('Send trackbacks to:'); ?></label> <?php echo $form_trackback; ?><br /> (<?php _e('Separate multiple URLs with spaces'); ?>)</p>
<p><?php _e('Trackbacks are a way to notify legacy blog systems that you&#8217;ve linked to them. If you link other WordPress blogs they&#8217;ll be notified automatically using <a href="http://codex.wordpress.org/Introduction_to_Blogging#Managing_Comments" target="_blank">pingbacks</a>, no other action necessary.'); ?></p>
<?php
if ( ! empty($pings) )
	echo $pings;
}
add_meta_box('trackbacksdiv', __('Trackbacks'), 'post_trackback_meta_box', 'post', 'advanced', 'core');

function post_custom_meta_box($post) {
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
add_meta_box('postcustom', __('Custom Fields'), 'post_custom_meta_box', 'post', 'advanced', 'core');

do_action('dbx_post_advanced');

function post_comment_status_meta_box($post) {
?>
<input name="advanced_view" type="hidden" value="1" />
<p><label for="comment_status" class="selectit">
<input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($post->comment_status, 'open'); ?> />
<?php _e('Allow Comments') ?></label></p>
<p><label for="ping_status" class="selectit"><input name="ping_status" type="checkbox" id="ping_status" value="open" <?php checked($post->ping_status, 'open'); ?> /> <?php _e('Allow Pings') ?></label></p>
<p><?php _e('These settings apply to this post only. &#8220;Pings&#8221; are <a href="http://codex.wordpress.org/Introduction_to_Blogging#Managing_Comments" target="_blank">trackbacks and pingbacks</a>.'); ?></p>
<?php
}
add_meta_box('commentstatusdiv', __('Comments &amp; Pings'), 'post_comment_status_meta_box', 'post', 'advanced', 'core');

function post_password_meta_box($post) {
?>
<p><label class="hidden" for="post_password"><?php _e('Password Protect This Post') ?></label><input name="post_password" type="text" size="25" id="post_password" value="<?php echo attribute_escape( $post->post_password ); ?>" /></p>
<p><?php _e('Setting a password will require people who visit your blog to enter the above password to view this post and its comments.'); ?></p>
<?php
}
add_meta_box('passworddiv', __('Password Protect This Post'), 'post_password_meta_box', 'post', 'advanced', 'core');

function post_slug_meta_box($post) {
?>
<label class="hidden" for="post_name"><?php _e('Post Slug') ?></label><input name="post_name" type="text" size="13" id="post_name" value="<?php echo attribute_escape( $post->post_name ); ?>" />
<?php
}
add_meta_box('slugdiv', __('Post Slug'), 'post_slug_meta_box', 'post', 'advanced', 'core');

$authors = get_editable_user_ids( $current_user->id ); // TODO: ROLE SYSTEM
if ( $post->post_author && !in_array($post->post_author, $authors) )
	$authors[] = $post->post_author;
if ( $authors && count( $authors ) > 1 ) :
function post_author_meta_box($post) {
	global $current_user, $user_ID;
	$authors = get_editable_user_ids( $current_user->id ); // TODO: ROLE SYSTEM
	if ( $post->post_author && !in_array($post->post_author, $authors) )
		$authors[] = $post->post_author;
?>
<label class="hidden" for="post_author_override"><?php _e('Post Author'); ?></label><?php wp_dropdown_users( array('include' => $authors, 'name' => 'post_author_override', 'selected' => empty($post->ID) ? $user_ID : $post->post_author) ); ?>
<?php
}
add_meta_box('authordiv', __('Post Author'), 'post_author_meta_box', 'post', 'advanced', 'core');
endif;

if ( isset($post_ID) && 0 < $post_ID && wp_get_post_revisions( $post_ID ) ) :
function post_revisions_meta_box($post) {
	wp_list_post_revisions();
}
add_meta_box('revisionsdiv', __('Post Revisions'), 'post_revisions_meta_box', 'post', 'advanced', 'core');
endif;

do_meta_boxes('post', 'advanced', $post);

do_action('dbx_post_sidebar');
?>
</div>
</div>

</div>

</form>

<?php if ((isset($post->post_title) && '' == $post->post_title) || (isset($_GET['message']) && 2 > $_GET['message'])) : ?>
<script type="text/javascript">
try{document.post.title.focus();}catch(e){}
</script>
<?php endif; ?>
