<?php
require_once('../wp-includes/wp-l10n.php');

$title = __('Discussion Options');
$parent_file = 'options-general.php';

function add_magic_quotes($array) {
	foreach ($array as $k => $v) {
		if (is_array($v)) {
			$array[$k] = add_magic_quotes($v);
		} else {
			$array[$k] = addslashes($v);
		}
	}
	return $array;
}

if (!get_magic_quotes_gpc()) {
	$_GET    = add_magic_quotes($_GET);
	$_POST   = add_magic_quotes($_POST);
	$_COOKIE = add_magic_quotes($_COOKIE);
}

$wpvarstoreset = array('action','standalone', 'option_group_id');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($_POST["$wpvar"])) {
			if (empty($_GET["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $_GET["$wpvar"];
			}
		} else {
			$$wpvar = $_POST["$wpvar"];
		}
	}
}

$standalone = 0;
include_once('admin-header.php');
include('options-head.php');

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
	<form name="form1" method="post" action="options.php"> 
		<input type="hidden" name="action" value="update" /> 
		<input type="hidden" name="page_options" value="'default_pingback_flag','default_ping_status','default_comment_status','comments_notify','moderation_notify','comment_moderation','require_name_email','comment_max_links','moderation_keys'" /> 
<fieldset class="options">
        <legend><?php _e('Usual settings for an article: <em>(These settings may be overridden for individual articles.)</em>') ?></legend> 
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
			<li> 
				<label for="require_name_email"> 
				<input type="checkbox" name="require_name_email" id="require_name_email" value="1" <?php checked('1', get_settings('require_name_email')); ?> /> 
				<?php _e('User must fill out name and e-mail') ?> </label> 
			</li> 
		</ul> 
</fieldset>
<fieldset class="options">
    <legend><?php _e('Comment Moderation') ?></legend>
    <p><?php printf(__('Hold a comment in the queue if it contains more than %s links. (A common characteristic of comment spam is a large number of hyperlinks.)'), '<input name="comment_max_links" type="text" id="comment_max_links" size="3" value="' . get_settings('comment_max_links'). '" />' ) ?></p>

    <p><?php _e('When a comment contains any of these words in its content, name, URI, e-mail, or IP, hold it in the moderation queue: (Separate multiple words with new lines.) <a href="http://codex.wordpress.org/Spam_Words">Common spam words</a>.') ?></p>
		<p> 
			<textarea name="moderation_keys" cols="60" rows="4" id="moderation_keys" style="width: 98%; font-size: 12px;" class="code"><?php form_option('moderation_keys'); ?></textarea> 
		</p> 
		<p>
			<a id="retrospambutton" href="options-discussion.php?action=retrospam" title="Click this link to check old comments for spam that your current filters would catch.">Check past comments against current word list</a>
 		</p> 
</fieldset>
		<p class="submit"> 
    	<input type="submit" name="Submit" value="<?php _e('Update Options') ?>" /> 
		</p> 
	</form> 
</div>
<?php include('./admin-footer.php'); ?>