<?php
$title = 'Options';
$this_file = 'options.php';
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
$option_group_id = (int) $_GET['option_group_id'];
require_once('./optionhandler.php');
$non_was_selected = 0;
if ($option_group_id == '') {
    $option_group_id = 1;
    $non_was_selected = 1;
}

switch($action) {

case 'update':
	$standalone = 1;
	include_once('./admin-header.php');
    $any_changed = 0;
    
    // iterate through the list of options in this group
    // pull the vars from the post
    // validate ranges etc.
    // update the values
	if (!$_POST['page_options']) {
		foreach ($_POST as $key => $value) {
			$option_names[] = "'$key'";
		}
		$option_names = implode(',', $option_names);
	} else {
		$option_names = stripslashes($_POST['page_options']);
	}

    $options = $wpdb->get_results("SELECT $tableoptions.option_id, option_name, option_type, option_value, option_admin_level FROM $tableoptions WHERE option_name IN ($option_names)");
//	die(var_dump($options));

// HACK
// Options that if not there have 0 value but need to be something like "closed"
$nonbools = array('default_ping_status', 'default_comment_status');
    if ($options) {
        foreach ($options as $option) {
            // should we even bother checking?
            if ($user_level >= $option->option_admin_level) {
                $old_val = stripslashes($option->option_value);
                $new_val = $_POST[$option->option_name];
				if (!$new_val) {
					if (3 == $option->option_type)
						$new_val = '';
					else
						$new_val = 0;
				}
				if( in_array($option->option_name, $nonbools) && $new_val == 0 ) $new_value = 'closed';
                if ($new_val !== $old_val) {
					$query = "UPDATE $tableoptions SET option_value = '$new_val' WHERE option_id = $option->option_id";
					$result = $wpdb->query($query);
					//if( in_array($option->option_name, $nonbools)) die('boo'.$query);
					if (!$result) {
						$db_errors .= " SQL error while saving $this_name. ";
					} else {
						++$any_changed;
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

	$goback = str_replace('?updated=true', '', $_SERVER['HTTP_REFERER']) . '?updated=true';
    header('Location: ' . $goback);
    break;

default:
	$standalone = 0;
	include_once("./admin-header.php");
	if ($user_level <= 6) {
		die("You have do not have sufficient permissions to edit the options for this blog.");
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
include('options-head.php');
?>

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
	echo $current_long_desc;
}
?>
</div>
<?php
} // end else a group was selected
break;
} // end switch

include('admin-footer.php');
?>