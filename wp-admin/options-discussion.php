<?php
/**
 * Discussion settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( ! current_user_can('manage_options') )
	wp_die(__('You do not have sufficient permissions to manage options for this blog.'));

$title = __('Discussion Settings');
$parent_file = 'options-general.php';

include('admin-header.php');
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<form method="post" action="options.php">
<?php settings_fields('discussion'); ?>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Default article settings') ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Default article settings') ?></span></legend>
<label for="default_pingback_flag">
<input name="default_pingback_flag" type="checkbox" id="default_pingback_flag" value="1" <?php checked('1', get_option('default_pingback_flag')); ?> />
<?php _e('Attempt to notify any blogs linked to from the article (slows down posting.)') ?></label>
<br />
<label for="default_ping_status">
<input name="default_ping_status" type="checkbox" id="default_ping_status" value="open" <?php checked('open', get_option('default_ping_status')); ?> />
<?php _e('Allow link notifications from other blogs (pingbacks and trackbacks.)') ?></label>
<br />
<label for="default_comment_status">
<input name="default_comment_status" type="checkbox" id="default_comment_status" value="open" <?php checked('open', get_option('default_comment_status')); ?> />
<?php _e('Allow people to post comments on new articles') ?></label>
<br />
<small><em><?php echo '(' . __('These settings may be overridden for individual articles.') . ')'; ?></em></small>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Other comment settings') ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Other comment settings') ?></span></legend>
<label for="require_name_email"><input type="checkbox" name="require_name_email" id="require_name_email" value="1" <?php checked('1', get_option('require_name_email')); ?> /> <?php _e('Comment author must fill out name and e-mail') ?></label>
<br />
<label for="comment_registration">
<input name="comment_registration" type="checkbox" id="comment_registration" value="1" <?php checked('1', get_option('comment_registration')); ?> />
<?php _e('Users must be registered and logged in to comment') ?>
</label>
<br />

<label for="close_comments_for_old_posts">
<input name="close_comments_for_old_posts" type="checkbox" id="close_comments_for_old_posts" value="1" <?php checked('1', get_option('close_comments_for_old_posts')); ?> />
<?php printf( __('Automatically close comments on articles older than %s days'), '</label><input name="close_comments_days_old" type="text" id="close_comments_days_old" value="' . esc_attr(get_option('close_comments_days_old')) . '" class="small-text" />') ?>
<br />
<label for="thread_comments">
<input name="thread_comments" type="checkbox" id="thread_comments" value="1" <?php checked('1', get_option('thread_comments')); ?> />
<?php

$maxdeep = (int) apply_filters( 'thread_comments_depth_max', 10 );

$thread_comments_depth = '</label><select name="thread_comments_depth" id="thread_comments_depth">';
for ( $i = 1; $i <= $maxdeep; $i++ ) {
	$thread_comments_depth .= "<option value='" . esc_attr($i) . "'";
	if ( get_option('thread_comments_depth') == $i ) $thread_comments_depth .= " selected='selected'";
	$thread_comments_depth .= ">$i</option>";
}
$thread_comments_depth .= '</select>';

printf( __('Enable threaded (nested) comments %s levels deep'), $thread_comments_depth );

?><br />
<label for="page_comments">
<input name="page_comments" type="checkbox" id="page_comments" value="1" <?php checked('1', get_option('page_comments')); ?> />
<?php

$default_comments_page = '</label><label for="default_comments_page"><select name="default_comments_page" id="default_comments_page"><option value="newest"';
if ( 'newest' == get_option('default_comments_page') ) $default_comments_page .= ' selected="selected"';
$default_comments_page .= '>' . __('last') . '</option><option value="oldest"';
if ( 'oldest' == get_option('default_comments_page') ) $default_comments_page .= ' selected="selected"';
$default_comments_page .= '>' . __('first') . '</option></select>';

printf( __('Break comments into pages with %1$s comments per page and the %2$s page displayed by default'), '</label><label for="comments_per_page"><input name="comments_per_page" type="text" id="comments_per_page" value="' . esc_attr(get_option('comments_per_page')) . '" class="small-text" />', $default_comments_page );

?></label>
<br />
<label for="comment_order"><?php

$comment_order = '<select name="comment_order" id="comment_order"><option value="asc"';
if ( 'asc' == get_option('comment_order') ) $comment_order .= ' selected="selected"';
$comment_order .= '>' . __('older') . '</option><option value="desc"';
if ( 'desc' == get_option('comment_order') ) $comment_order .= ' selected="selected"';
$comment_order .= '>' . __('newer') . '</option></select>';

printf( __('Comments should be displayed with the %s comments at the top of each page'), $comment_order );

?></label>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('E-mail me whenever') ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('E-mail me whenever') ?></span></legend>
<label for="comments_notify">
<input name="comments_notify" type="checkbox" id="comments_notify" value="1" <?php checked('1', get_option('comments_notify')); ?> />
<?php _e('Anyone posts a comment') ?> </label>
<br />
<label for="moderation_notify">
<input name="moderation_notify" type="checkbox" id="moderation_notify" value="1" <?php checked('1', get_option('moderation_notify')); ?> />
<?php _e('A comment is held for moderation') ?> </label>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Before a comment appears') ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Before a comment appears') ?></span></legend>
<label for="comment_moderation">
<input name="comment_moderation" type="checkbox" id="comment_moderation" value="1" <?php checked('1', get_option('comment_moderation')); ?> />
<?php _e('An administrator must always approve the comment') ?> </label>
<br />
<label for="comment_whitelist"><input type="checkbox" name="comment_whitelist" id="comment_whitelist" value="1" <?php checked('1', get_option('comment_whitelist')); ?> /> <?php _e('Comment author must have a previously approved comment') ?></label>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Comment Moderation') ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Comment Moderation') ?></span></legend>
<p><label for="comment_max_links"><?php printf(__('Hold a comment in the queue if it contains %s or more links. (A common characteristic of comment spam is a large number of hyperlinks.)'), '<input name="comment_max_links" type="text" id="comment_max_links" value="' . esc_attr(get_option('comment_max_links')) . '" class="small-text" />' ) ?></label></p>

<p><label for="moderation_keys"><?php _e('When a comment contains any of these words in its content, name, URL, e-mail, or IP, it will be held in the <a href="edit-comments.php?comment_status=moderated">moderation queue</a>. One word or IP per line. It will match inside words, so &#8220;press&#8221; will match &#8220;WordPress&#8221;.') ?></label></p>
<p>
<textarea name="moderation_keys" rows="10" cols="50" id="moderation_keys" class="large-text code"><?php form_option('moderation_keys'); ?></textarea>
</p>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Comment Blacklist') ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Comment Blacklist') ?></span></legend>
<p><label for="blacklist_keys"><?php _e('When a comment contains any of these words in its content, name, URL, e-mail, or IP, it will be marked as spam. One word or IP per line. It will match inside words, so &#8220;press&#8221; will match &#8220;WordPress&#8221;.') ?></label></p>
<p>
<textarea name="blacklist_keys" rows="10" cols="50" id="blacklist_keys" class="large-text code"><?php form_option('blacklist_keys'); ?></textarea>
</p>
</fieldset></td>
</tr>
<?php do_settings_fields('discussion', 'default'); ?>
</table>

<h3><?php _e('Avatars') ?></h3>

<p><?php _e('An avatar is an image that follows you from weblog to weblog appearing beside your name when you comment on avatar enabled sites.  Here you can enable the display of avatars for people who comment on your blog.'); ?></p>

<?php // the above would be a good place to link to codex documentation on the gravatar functions, for putting it in themes. anything like that? ?>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Avatar Display') ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Avatar display') ?></span></legend>
<?php
	$yesorno = array(0 => __("Don&#8217;t show Avatars"), 1 => __('Show Avatars'));
	foreach ( $yesorno as $key => $value) {
		$selected = (get_option('show_avatars') == $key) ? 'checked="checked"' : '';
		echo "\n\t<label><input type='radio' name='show_avatars' value='" . esc_attr($key) . "' $selected/> $value</label><br />";
	}
?>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Maximum Rating') ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Maximum Rating') ?></span></legend>

<?php
$ratings = array( 'G' => __('G &#8212; Suitable for all audiences'), 'PG' => __('PG &#8212; Possibly offensive, usually for audiences 13 and above'), 'R' => __('R &#8212; Intended for adult audiences above 17'), 'X' => __('X &#8212; Even more mature than above'));
foreach ($ratings as $key => $rating) :
	$selected = (get_option('avatar_rating') == $key) ? 'checked="checked"' : '';
	echo "\n\t<label><input type='radio' name='avatar_rating' value='" . esc_attr($key) . "' $selected/> $rating</label><br />";
endforeach;
?>

</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Default Avatar') ?></th>
<td class="defaultavatarpicker"><fieldset><legend class="screen-reader-text"><span><?php _e('Default Avatar') ?></span></legend>

<?php _e('For users without a custom avatar of their own, you can either display a generic logo or a generated one based on their e-mail address.'); ?><br />

<?php
$avatar_defaults = array(
	'mystery' => __('Mystery Man'),
	'blank' => __('Blank'),
	'gravatar_default' => __('Gravatar Logo'),
	'identicon' => __('Identicon (Generated)'),
	'wavatar' => __('Wavatar (Generated)'),
	'monsterid' => __('MonsterID (Generated)')
);
$avatar_defaults = apply_filters('avatar_defaults', $avatar_defaults);
$default = get_option('avatar_default');
if ( empty($default) )
	$default = 'mystery';
$size = 32;
$avatar_list = '';
foreach ( $avatar_defaults as $default_key => $default_name ) {
	$selected = ($default == $default_key) ? 'checked="checked" ' : '';
	$avatar_list .= "\n\t<label><input type='radio' name='avatar_default' id='avatar_{$default_key}' value='" . esc_attr($default_key)  . "' {$selected}/> ";

	$avatar = get_avatar( $user_email, $size, $default_key );
	$avatar_list .= preg_replace("/src='(.+?)'/", "src='\$1&amp;forcedefault=1'", $avatar);

	$avatar_list .= ' ' . $default_name . '</label>';
	$avatar_list .= '<br />';
}
echo apply_filters('default_avatar_select', $avatar_list);
?>

</fieldset></td>
</tr>
<?php do_settings_fields('discussion', 'avatars'); ?>
</table>

<?php do_settings_sections('discussion'); ?>

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>
</form>
</div>

<?php include('./admin-footer.php'); ?>
