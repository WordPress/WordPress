<?php
$title = 'Options';
$this_file = 'options.php';

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

require_once("optionhandler.php");
$non_was_selected = 0;
if ($option_group_id == '') {
    $option_group_id = 1;
    $non_was_selected = 1;
}

switch($action) {

case "update":
	$standalone = 0;
	include_once("./admin-header.php");
    $any_changed = 0;
    
    // iterate through the list of options in this group
    // pull the vars from the post
    // validate ranges etc.
    // update the values
    $options = $wpdb->get_results("SELECT $tableoptions.option_id, option_name, option_type, option_value, option_admin_level "
                                  . "FROM $tableoptions "
                                  . "LEFT JOIN $tableoptiongroup_options ON $tableoptions.option_id = $tableoptiongroup_options.option_id "
                                  . "WHERE group_id = $option_group_id "
                                  . "ORDER BY seq");
    if ($options) {
        foreach ($options as $option) {
            // should we even bother checking?
            if ($user_level >= $option->option_admin_level) {
                $this_name = $option->option_name;
                $old_val = stripslashes($option->option_value);
                $new_val = $HTTP_POST_VARS[$this_name];

                if ($new_val != $old_val) {
                    // get type and validate
                    $msg = validate_option($option, $this_name, $new_val);
                    if ($msg == '') {
                        //no error message
                        $result = $wpdb->query("UPDATE $tableoptions SET option_value = '$new_val' WHERE option_id = $option->option_id");
                        if (!$result) {
                            $db_errors .= " SQL error while saving $this_name. ";
                        } else {
                            ++$any_changed;
                        }
                    } else {
                        $validation_message .= $msg;
                    }
                }
            }
        } // end foreach
        unset($cache_settings); // so they will be re-read
        get_settings('siteurl'); // make it happen now
    } // end if options
    
    if ($any_changed) {
        $message = $any_changed . ' setting(s) saved... ';
    }
    
    if (($dB_errors != '') || ($validation_message != '')) {
        if ($message != '') {
            $message .= '<br />and ';
        }
        $message .= $dB_errors . '<br />' . $validation_message;
    }
        
    //break; //fall through

default:
	$standalone = 0;
	include_once("./admin-header.php");
	if ($user_level <= 3) {
		die("You have no right to edit the options for this blog.<br>Ask for a promotion from your <a href=\"mailto:$admin_email\">blog admin</a> :)");
	}
?>

<?php
if ($non_was_selected) { // no group pre-selected, display opening page
?>
<div class="wrap">
<dl>
<?php
    //iterate through the available option groups. output them as a definition list.
    $option_groups = $wpdb->get_results("SELECT group_id, group_name, group_desc, group_longdesc FROM $tableoptiongroups ORDER BY group_id");
    foreach ($option_groups as $option_group) {
        echo("  <dt><a href=\"$this_file?option_group_id={$option_group->group_id}\" title=\"{$option_group->group_desc}\">{$option_group->group_name}</a></dt>\n");
        $current_long_desc = $option_group->group_longdesc;
        if ($current_long_desc == '') {
            $current_long_desc = 'No help for this group of options.';
        }
        echo("  <dd>{$option_group->group_desc}: $current_long_desc</dd>\n");
    } // end for each group
?>
  <dt><a href="options-permalink.php">Permalinks</a></dt>
  <dd>Permanent link configuration</dd>
</dl>
</div>
<?php    

} else { //there was a group selected.

?>
<ul id="adminmenu2">
<?php
    //Iterate through the available option groups.
    $option_groups = $wpdb->get_results("SELECT group_id, group_name, group_desc, group_longdesc FROM $tableoptiongroups ORDER BY group_id");
    foreach ($option_groups as $option_group) {
        if ($option_group->group_id == $option_group_id) {
            $current_desc=$option_group->group_desc;
            $current_long_desc = $option_group->group_longdesc;
            echo("  <li><a class=\"current\" href=\"$this_file?option_group_id={$option_group->group_id}\" title=\"{$option_group->group_desc}\">{$option_group->group_name}</a></li>\n");
        } else {
            echo("  <li><a href=\"$this_file?option_group_id={$option_group->group_id}\" title=\"{$option_group->group_desc}\">{$option_group->group_name}</a></li>\n");
        }
    } // end for each group
?>
  <li class="last"><a href="options-permalink.php">Permalinks</a></li>
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
    if ($options) {
        foreach ($options as $option) {
            echo('    <tr><td width="10%" valign="top">'. get_option_widget($option, ($user_level >= $option->option_admin_level), '</td><td width="15%" valign="top" style="border: 1px solid #ccc">'));
            echo("    </td><td  valign='top' class='helptext'>$option->option_description</td></tr>\n");
        }
    }
?>
    <tr><td colspan="3">&nbsp;</td></tr>
    <tr><td align="center" colspan="3"><input type="submit" name="Update" value="Update Settings" /></td></tr>
    <tr><td colspan="3"><?php echo $message; ?></td></tr>
  </table>
  </form>
</div>

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
} // end else a group was selected
break;
} // end switch

include("admin-footer.php") ?>