<?php
require_once('admin.php');

$title = __('Discussion Settings');
$parent_file = 'options-general.php';

include('admin-header.php');
?>

<div class="wrap">
<h2><?php _e('Discussion Settings') ?></h2>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options') ?>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Default article settings') ?></th>
<td><fieldset><legend class="hidden"><?php _e('Default article settings') ?></legend>
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
<?php _e('Allow people to post comments on the article') ?></label>
<br />
<small><em><?php echo '(' . __('These settings may be overridden for individual articles.') . ')'; ?></em></small>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('E-mail me whenever') ?></th>
<td><fieldset><legend class="hidden"><?php _e('E-mail me whenever') ?></legend>
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
<td><fieldset><legend class="hidden"><?php _e('Before a comment appears') ?></legend>
<label for="comment_moderation">
<input name="comment_moderation" type="checkbox" id="comment_moderation" value="1" <?php checked('1', get_option('comment_moderation')); ?> />
<?php _e('An administrator must always approve the comment') ?> </label>
<br />
<label for="require_name_email"><input type="checkbox" name="require_name_email" id="require_name_email" value="1" <?php checked('1', get_option('require_name_email')); ?> /> <?php _e('Comment author must fill out name and e-mail') ?></label>
<br />
<label for="comment_whitelist"><input type="checkbox" name="comment_whitelist" id="comment_whitelist" value="1" <?php checked('1', get_option('comment_whitelist')); ?> /> <?php _e('Comment author must have a previously approved comment') ?></label>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Comment Moderation') ?></th>
<td><fieldset><legend class="hidden"><?php _e('Comment Moderation') ?></legend>
<p><label for="comment_max_links"><?php printf(__('Hold a comment in the queue if it contains %s or more links. (A common characteristic of comment spam is a large number of hyperlinks.)'), '<input name="comment_max_links" type="text" id="comment_max_links" size="3" value="' . get_option('comment_max_links'). '" />' ) ?></label></p>

<p><label for="moderation_keys"><?php _e('When a comment contains any of these words in its content, name, URL, e-mail, or IP, it will be held in the <a href="edit-comments.php?comment_status=moderated">moderation queue</a>. One word or IP per line. It will match inside words, so "press" will match "WordPress".') ?></label></p>
<p>
<textarea name="moderation_keys" cols="60" rows="10" id="moderation_keys" style="width: 98%; font-size: 12px;" class="code"><?php form_option('moderation_keys'); ?></textarea>
</p>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Comment Blacklist') ?></th>
<td><fieldset><legend class="hidden"><?php _e('Comment Blacklist') ?></legend>
<p><label for="blacklist_keys"><?php _e('When a comment contains any of these words in its content, name, URL, e-mail, or IP, it will be marked as spam. One word or IP per line. It will match inside words, so "press" will match "WordPress".') ?></label></p>
<p>
<textarea name="blacklist_keys" cols="60" rows="10" id="blacklist_keys" style="width: 98%; font-size: 12px;" class="code"><?php form_option('blacklist_keys'); ?></textarea>
</p>
</fieldset></td>
</tr>
</table>

<h3><?php _e('Avatars') ?></h3>

<p><?php _e('An avatar is an image that follows you from weblog to weblog appearing beside your name when you comment on avatar enabled sites.  Here you can enable the display of avatars for people who comment on your blog.'); ?></p>

<?php // the above would be a good place to link to codex documentation on the gravatar functions, for putting it in themes. anything like that? ?>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Avatar Display') ?></th>
<td><fieldset><legend class="hidden"><?php _e('Avatar display') ?></legend>
<?php
	$yesorno = array(0 => __("Don&#8217;t show Avatars"), 1 => __('Show Avatars'));
	foreach ( $yesorno as $key => $value) {
		$selected = (get_option('show_avatars') == $key) ? 'checked="checked"' : '';
		echo "\n\t<label><input type='radio' name='show_avatars' value='$key' $selected/> $value</label><br />";
	}
?>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Maximum Rating') ?></th>
<td><fieldset><legend class="hidden"><?php _e('Maximum Rating') ?></legend>

<?php
$ratings = array( 'G' => __('G &#8212; Suitable for all audiences'), 'PG' => __('PG &#8212; Possibly offensive, usually for audiences 13 and above'), 'R' => __('R &#8212; Intended for adult audiences above 17'), 'X' => __('X &#8212; Even more mature than above'));
foreach ($ratings as $key => $rating) :
	$selected = (get_option('avatar_rating') == $key) ? 'checked="checked"' : '';
	echo "\n\t<label><input type='radio' name='avatar_rating' value='$key' $selected/> $rating</label><br />";
endforeach;
?>

</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Default Avatar') ?></th>
<td class="defaultavatarpicker"><fieldset><legend class="hidden"><?php _e('Default Avatar') ?></legend>

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
	$avatar_list .= "\n\t<label><input type='radio' name='avatar_default' id='avatar_{$default_key}' value='{$default_key}' {$selected}/> ";

	$avatar = get_avatar( $user_email, $size, $default_key );
	$avatar_list .= preg_replace("/src='(.+?)'/", "src='\$1&amp;forcedefault=1'", $avatar);

	$avatar_list .= ' ' . $default_name . '</label>';
	$avatar_list .= '<br />';
}
echo apply_filters('default_avatar_select', $avatar_list);
?>

</fieldset></td>
</tr>

</table>


<p class="submit">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="default_pingback_flag,default_ping_status,default_comment_status,comments_notify,moderation_notify,comment_moderation,require_name_email,comment_whitelist,comment_max_links,moderation_keys,blacklist_keys,show_avatars,avatar_rating,avatar_default" />
<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
</p>
</form>
</div>

<?php include('./admin-footer.php'); ?>
