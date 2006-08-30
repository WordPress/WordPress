<?php
require_once('admin.php');

$title = __('Discussion Options');
$parent_file = 'options-general.php';

include('admin-header.php');

if ($action == 'retrospam') {
	if ( $_GET['move'] == 'true' ) {
		retrospam_mgr::move_spam( $_GET[ids] );
	}
	$retrospaminator = new retrospam_mgr();
	$result = $retrospaminator->find_spam();
	echo $retrospaminator->display_edit_form( $result );
	include('./admin-footer.php');
	exit;
}
?>

<div class="wrap"> 
<h2><?php _e('Discussion Options') ?></h2> 
<form method="post" action="options.php"> 
<?php wp_nonce_field('update-options') ?>
<fieldset class="options">
<legend><?php _e('Usual settings for an article:<br /><small><em>(These settings may be overridden for individual articles.)</em></small>') ?></legend> 
<ul> 
<li> 
<label for="default_pingback_flag"> 
<input name="default_pingback_flag" type="checkbox" id="default_pingback_flag" value="1" <?php checked('1', get_settings('default_pingback_flag')); ?> /> 
<?php _e('Attempt to notify any Weblogs linked to from the article (slows down posting.)') ?></label> 
</li> 
<li> 
<label for="default_ping_status"> 
<input name="default_ping_status" type="checkbox" id="default_ping_status" value="open" <?php checked('open', get_settings('default_ping_status')); ?> /> 
<?php _e('Allow link notifications from other Weblogs (pingbacks and trackbacks.)') ?></label> 
</li> 
<li> 
<label for="default_comment_status"> 
<input name="default_comment_status" type="checkbox" id="default_comment_status" value="open" <?php checked('open', get_settings('default_comment_status')); ?> /> 
<?php _e('Allow people to post comments on the article') ?></label> 
</li> 
</ul> 
</fieldset>
<fieldset class="options">
<legend><?php _e('E-mail me whenever:') ?></legend> 
<ul> 
<li> 
<label for="comments_notify"> 
<input name="comments_notify" type="checkbox" id="comments_notify" value="1" <?php checked('1', get_settings('comments_notify')); ?> /> 
<?php _e('Anyone posts a comment') ?> </label> 
</li> 
<li> 
<label for="moderation_notify"> 
<input name="moderation_notify" type="checkbox" id="moderation_notify" value="1" <?php checked('1', get_settings('moderation_notify')); ?> /> 
<?php _e('A comment is held for moderation') ?> </label> 
</li> 
</ul> 
</fieldset>
<fieldset class="options">
<legend><?php _e('Before a comment appears:') ?></legend> 
<ul>
<li>
<label for="comment_moderation"> 
<input name="comment_moderation" type="checkbox" id="comment_moderation" value="1" <?php checked('1', get_settings('comment_moderation')); ?> /> 
<?php _e('An administrator must approve the comment (regardless of any matches below)') ?> </label> 
</li> 
<li><label for="require_name_email"><input type="checkbox" name="require_name_email" id="require_name_email" value="1" <?php checked('1', get_settings('require_name_email')); ?> /> <?php _e('Comment author must fill out name and e-mail') ?></label></li> 
<li><label for="comment_whitelist"><input type="checkbox" name="comment_whitelist" id="comment_whitelist" value="1" <?php checked('1', get_settings('comment_whitelist')); ?> /> <?php _e('Comment author must have a previously approved comment') ?></label></li> 
</ul> 
</fieldset>
<fieldset class="options">
<legend><?php _e('Comment Moderation') ?></legend>
<p><?php printf(__('Hold a comment in the queue if it contains more than %s links. (A common characteristic of comment spam is a large number of hyperlinks.)'), '<input name="comment_max_links" type="text" id="comment_max_links" size="3" value="' . get_settings('comment_max_links'). '" />' ) ?></p>

<p><?php _e('When a comment contains any of these words in its content, name, URL, e-mail, or IP, hold it in the moderation queue: (Separate multiple words with new lines.) <a href="http://codex.wordpress.org/Spam_Words">Common spam words</a>.') ?></p>
<p> 
<textarea name="moderation_keys" cols="60" rows="4" id="moderation_keys" style="width: 98%; font-size: 12px;" class="code"><?php form_option('moderation_keys'); ?></textarea> 
</p> 
<p>
<a id="retrospambutton" href="options-discussion.php?action=retrospam"><?php _e('Check past comments against moderation list'); ?></a>
</p> 
</fieldset>
<fieldset class="options">
<legend><?php _e('Comment Blacklist') ?></legend>
<p><?php _e('This is a list of words that you want completely blacklisted from your blog. Be very careful what you add here, because if a comment matches something here it will be completely nuked and there will be no notification. Remember that partial words can match, so if there is any chance something here might match it would be better to put it in the moderation box above.') ?></p>
<p> 
<textarea name="blacklist_keys" cols="60" rows="4" id="blacklist_keys" style="width: 98%; font-size: 12px;" class="code"><?php form_option('blacklist_keys'); ?></textarea> 
</p>
<p><label for="open_proxy_check"> 
<input name="open_proxy_check" type="checkbox" id="open_proxy_check" value="1" <?php checked('1', get_settings('open_proxy_check')); ?> /> 
<?php _e('Blacklist comments from open and insecure proxies.') ?></label></p>
</fieldset>
<p class="submit">
<input type="hidden" name="action" value="update" /> 
<input type="hidden" name="page_options" value="default_pingback_flag,default_ping_status,default_comment_status,comments_notify,moderation_notify,comment_moderation,require_name_email,comment_whitelist,comment_max_links,moderation_keys,blacklist_keys,open_proxy_check" /> 
<input type="submit" name="Submit" value="<?php _e('Update Options') ?>" /> 
</p> 
</form> 
</div>
<?php include('./admin-footer.php'); ?>
