<?php
$title = 'Writing Options';

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
	die("You have no right to edit the options for this blog.<br />Ask for a promotion to your <a href=\"mailto:$admin_email\">blog admin</a> :)");
}
?>
 <ul id="adminmenu2"> 
 	<li><a href="options-general.php">General</a></li>
	<li><a class="current">Writing</a></li>
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
  <h2>Writing Options</h2> 
  <form name="form1" method="post" action="options.php"> 
    <input type="hidden" name="action" value="update" /> 
    <table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
      <tr valign="top"> 
        <th width="33%" scope="row"> Size of the writing box, in lines:</th> 
        <td><input name="default_post_edit_rows" type="text" id="default_post_edit_rows" value="<?php echo get_settings('default_post_edit_rows'); ?>" size="3" /></td> 
      </tr>
      <tr valign="top">
        <th scope="row">Character Setting: </th>
        <td><input name="blog_charset" type="text" id="blog_charset" value="<?php echo get_settings('blog_charset'); ?>" size="20" class="code" />
            <br />
    The charset you write your blog in (UTF-8 recommended<a href="http://developer.apple.com/documentation/macos8/TextIntlSvcs/TextEncodingConversionManager/TEC1.5/TEC.b0.html"></a>)</td>
      </tr>
    </table> 
	<p><label for="use_smilies"><input name="use_smilies" type="checkbox" id="use_smilies" value="1" <?php checked('1', get_settings('use_smilies')); ?> /> 
 Convert emoticons like <code>:-)</code> and <code>:-P</code> to graphics</label> 
          <label for="new_users_can_blog"></label></p>
	<p>
	  <label for="use_balanceTags">
      <input name="use_balanceTags" type="checkbox" id="use_balanceTags" value="1" <?php checked('1', get_settings('use_balanceTags')); ?> /> 
      WordPress should correct invalidly nested XHTML automatically </label>
    </p>
	<p style="text-align: right;">
      <input type="submit" name="Submit" value="Update Options" />
    </p>
  </form> 
</div> 
<?php include("admin-footer.php") ?>