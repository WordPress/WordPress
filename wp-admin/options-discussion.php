<?php
$title = 'Discussion Options';

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
	$HTTP_GET_VARS    = add_magic_quotes($HTTP_GET_VARS);
	$HTTP_POST_VARS   = add_magic_quotes($HTTP_POST_VARS);
	$HTTP_COOKIE_VARS = add_magic_quotes($HTTP_COOKIE_VARS);
}

$wpvarstoreset = array('action','standalone', 'option_group_id');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($HTTP_POST_VARS["$wpvar"])) {
			if (empty($HTTP_GET_VARS["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $HTTP_GET_VARS["$wpvar"];
			}
		} else {
			$$wpvar = $HTTP_POST_VARS["$wpvar"];
		}
	}
}

require_once('optionhandler.php');


$standalone = 0;
include_once('admin-header.php');
if ($user_level <= 3) {
	die("You have do not have sufficient permissions to edit the options for this blog.");
}
?>
 <ul id="adminmenu2"> 
  <li><a href="options-general.php">General</a></li> 
  <li><a href="options-writing.php">Writing</a></li> 
  <li><a class="current">Discussion</a></li> 
  <?php
    //we need to iterate through the available option groups.
    $option_groups = $wpdb->get_results("SELECT group_id, group_name, group_desc, group_longdesc FROM $tableoptiongroups ORDER BY group_id");
    foreach ($option_groups as $option_group) {
        if ($option_group->group_id == $option_group_id) {
            $current_desc=$option_group->group_desc;
            $current_long_desc = $option_group->group_longdesc;
            echo("  <li><a id=\"current2\" href=\"options.php?option_group_id={$option_group->group_id}\" title=\"{$option_group->group_desc}\">{$option_group->group_name}</a></li>\n");
        } else {
            echo("  <li><a href=\"options.php?option_group_id={$option_group->group_id}\" title=\"{$option_group->group_desc}\">{$option_group->group_name}</a></li>\n");
        }
    } // end for each group
?> 
  <li class="last"><a href="options-permalink.php">Permalinks</a></li> 
</ul> 
<br clear="all" /> 
<div class="wrap"> 
  <h2>Discussion Options</h2> 
  <form name="form1" method="post" action="options.php"> 
    <input type="hidden" name="action" value="update" /> 
    <input type="hidden" name="page_options" value="'default_pingback_flag','default_ping_status','default_comment_status','use_smilies','comments_notify','moderation_notify','comment_moderation','moderation_keys'" /> 
    <p>Usual settings for an article: <em>(These settings may be overidden for individual articles.)</em></p> 
    <ul> 
      <li> 
        <label for="default_pingback_flag"> 
        <input name="default_pingback_flag" type="checkbox" id="default_pingback_flag" value="1" <?php checked('1', get_settings('default_pingback_flag')); ?> /> 
        Attempt to notify any Weblogs linked to from the article. (Slows down posting.)</label> 
      </li> 
      <li> 
        <label for="default_ping_status"> 
        <input name="default_ping_status" type="checkbox" id="default_ping_status" value="open" <?php checked('open', get_settings('default_ping_status')); ?> /> 
        Allow link notifications from other Weblogs. (Pingbacks and trackbacks.)</label> 
      </li> 
      <li> 
        <label for="default_comment_status"> 
        <input name="default_comment_status" type="checkbox" id="default_comment_status" value="1" <?php checked('1', get_settings('default_comment_status')); ?> /> 
        Allow people to post comments on the article</label> 
      </li> 
    </ul> 
    <p>Email me whenever:</p> 
    <ul> 
      <li> 
        <label for="comments_notify"> 
        <input name="comments_notify" type="checkbox" id="comments_notify" value="1" <?php checked('1', get_settings('comments_notify')); ?> /> 
        Anyone posts a comment </label> 
      </li> 
      <li> 
        <label for="moderation_notify"> 
        <input name="moderation_notify" type="checkbox" id="moderation_notify" value="1" <?php checked('1', get_settings('moderation_notify')); ?> /> 
        A comment is approved or declined </label> 
      </li> 
    </ul> 
    <p>Before a comment appears:</p> 
    <ul> 
      <li> 
        <label for="comment_moderation"> 
        <input name="comment_moderation" type="checkbox" id="comment_moderation" value="1" <?php checked('1', get_settings('comment_moderation')); ?> /> 
        An administrator must approve the comment (regardless of any matches below) </label> 
      </li> 
    </ul> 
    <p>When a comment contains any of these words in its content, name, URI, or email, hold it in the moderation queue: (Seperate multiple words with new lines.)</p> 
    <p> 
      <textarea name="moderation_keys" cols="60" rows="4" id="moderation_keys" style="width: 98%;"><?php echo get_settings('moderation_keys'); ?></textarea> 
    </p> 
    <p style="text-align: right;"> 
      <input type="submit" name="Submit" value="Update Options" /> 
    </p> 
  </form> 
</div> 
<?php include("admin-footer.php") ?>
