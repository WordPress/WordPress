<?php
$title = 'Options';
$this_file = 'wp-options.php';

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

$b2varstoreset = array('action','standalone', 'option_group_id');
for ($i=0; $i<count($b2varstoreset); $i += 1) {
	$b2var = $b2varstoreset[$i];
	if (!isset($$b2var)) {
		if (empty($HTTP_POST_VARS["$b2var"])) {
			if (empty($HTTP_GET_VARS["$b2var"])) {
				$$b2var = '';
			} else {
				$$b2var = $HTTP_GET_VARS["$b2var"];
			}
		} else {
			$$b2var = $HTTP_POST_VARS["$b2var"];
		}
	}
}

require_once("optionhandler.php");
if ($option_group_id == '') {
    $option_group_id = 1;
}
switch($action) {

case "update":
	$standalone = 1;
	include("./b2header.php");
    //do something
    $message = "Settings saved...";
    //error_log("got action=update and option_group_id=$option_group_id");

    // iterate through the list of options in this group
    // pull the vars from the post
    // validate ranges etc.?
    // update the values
    $options = $wpdb->get_results("SELECT $tableoptions.option_id, option_name, option_type, option_value, option_admin_level "
                                  . "FROM $tableoptions "
                                  . "LEFT JOIN $tableoptiongroup_options ON $tableoptions.option_id = $tableoptiongroup_options.option_id "
                                  . "WHERE group_id = $option_group_id "
                                  . "ORDER BY seq");
    if ($options)
    {
        foreach ($options as $option)
        {
            $this_name = $option->option_name;
            $old_val = stripslashes($option->option_value);
            $new_val = $HTTP_POST_VARS[$this_name];
            // get type and validate

            //error_log("update checking $this_name: $old_val and $new_val");
            if ($new_val != $old_val)
            {
                //error_log("updating $this_name from $old_val to $new_val");
                $result = $wpdb->query("UPDATE $tableoptions SET option_value='".addslashes($new_val)."' WHERE option_id=$option->option_id");
                if (!$result)
                {
                    $message .= "Error while saving $this_name. ";
                }
            }
        } // end foreach
    }
    //header("Location: $this_file?option_group_id=$option_group_id");
    //break;
    //fall through

default:
	$standalone=0;
	include ("./b2header.php");
	if ($user_level <= 3) {
		die("You have no right to edit the options for this blog.<br>Ask for a promotion to your <a href=\"mailto:$admin_email\">blog admin</a> :)");
	}
?>
<ul id="adminmenu2">
<?php
    //we need to iterate through the available option groups.
    $option_groups = $wpdb->get_results("SELECT group_id, group_name, group_desc, group_longdesc FROM $tableoptiongroups ORDER BY group_id");
    foreach ($option_groups as $option_group)
    {
        if ($option_group->group_id == $option_group_id)
        {
            $current_desc=$option_group->group_desc;
            $current_long_desc = $option_group->group_longdesc;
            echo("  <li id=\"current2\"><a href=\"$this_file?option_group_id={$option_group->group_id}\" title=\"{$option_group->group_desc}\">{$option_group->group_name}</a></li>\n");
        }
        else
        {
            echo("  <li><a href=\"$this_file?option_group_id={$option_group->group_id}\" title=\"{$option_group->group_desc}\">{$option_group->group_name}</a></li>\n");
        }
    } // end for each group
?>
</ul>
    <br clear="all" />
<div class="wrap">
    <h2><?php echo $current_desc; ?></h2>
    <form name="form" action="<?php echo $this_file; ?>" method="post">
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="option_group_id" value="<?php echo $option_group_id; ?>" />
			
  <table width="90%" cellpadding="2" cellspacing="2" border="0">
<?php
    //Now display all the options for the selected group.
    $options = $wpdb->get_results("SELECT $tableoptions.option_id, option_name, option_type, option_value, option_width, option_height, option_description, option_admin_level "
                                  . "FROM $tableoptions "
                                  . "LEFT JOIN $tableoptiongroup_options ON $tableoptions.option_id = $tableoptiongroup_options.option_id "
                                  . "WHERE group_id = $option_group_id "
                                  . "ORDER BY seq");
    if ($options)
    {
        foreach ($options as $option)
        {
            echo('<tr><td width="10%" valign="top">'. get_option_widget($option, ($user_level >= $option->option_admin_level), '</td><td width="15%" valign="top" style="border: 1px solid #ccc">'));
            echo("</td><td  valign='top' class='helptext'>$option->option_description</td></tr>\n");
        }
    }
?>
    <tr><td colspan="3">&nbsp;</td></tr>
    <tr><td align="center" colspan="3"><input type="submit" name="Update" value="Update Settings" /></td></tr>
    <tr><td colspan="3"><?php echo $message; ?></td></tr>
  </table>

</div>

   </form>

<div class="wrap">
<?php
if ($current_long_desc != '') {
    echo($current_long_desc);
} else {
?>
    <p> No help for this group of options.</p>
<?php
}
?>
</div>
<?php

break;
}

include("b2footer.php") ?>