<?php
require_once('../wp-includes/wp-l10n.php');

$title = __('Options');
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

$wpvarstoreset = array('action','standalone');
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

switch($action) {

case 'update':
	$standalone = 1;
	include_once('./admin-header.php');
    $any_changed = 0;
    
	if (!$_POST['page_options']) {
		foreach ($_POST as $key => $value) {
			$option_names[] = "'$key'";
		}
		$option_names = implode(',', $option_names);
	} else {
		$option_names = stripslashes($_POST['page_options']);
	}

    $options = $wpdb->get_results("SELECT $wpdb->options.option_id, option_name, option_type, option_value, option_admin_level FROM $wpdb->options WHERE option_name IN ($option_names)");

// HACK
// Options that if not there have 0 value but need to be something like "closed"
    $nonbools = array('default_ping_status', 'default_comment_status');
    if ($options) {
        foreach ($options as $option) {
            // should we even bother checking?
            if ($user_level >= $option->option_admin_level) {
                $old_val = $option->option_value;
                $new_val = $_POST[$option->option_name];
                if (!$new_val) {
                    if (3 == $option->option_type)
                        $new_val = '';
                    else
                        $new_val = 0;
                }
                if( in_array($option->option_name, $nonbools) && $new_val == '0' ) $new_val = 'closed';
                if ($new_val !== $old_val)
                    $result = $wpdb->query("UPDATE $wpdb->options SET option_value = '$new_val' WHERE option_name = '$option->option_name'");
            }
        }
        unset($cache_settings); // so they will be re-read
        get_settings('siteurl'); // make it happen now
    } // end if options
    
    if ($any_changed) {
        $message = sprintf(__('%d setting(s) saved... '), $any_changed);
    }
    
	$referred = str_replace('?updated=true' , '', $_SERVER['HTTP_REFERER']);
	$goback = str_replace('?updated=true', '', $_SERVER['HTTP_REFERER']) . '?updated=true';
	$goback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $goback);
    header('Location: ' . $goback);
    break;

default:
	$standalone = 0;
	include_once('./admin-header.php');
	if ($user_level <= 6) {
		die(__("You have do not have sufficient permissions to edit the options for this blog."));
	}
?>

<?php include('options-head.php'); ?>

<div class="wrap">
  <h2>All options</h2>
  <form name="form" action="options.php" method="post">
  <input type="hidden" name="action" value="update" />
  <table width="98%">
<?php
$options = $wpdb->get_results("SELECT * FROM $wpdb->options ORDER BY option_name");

foreach ($options as $option) :
	$value = htmlspecialchars($option->option_value);
	echo "
<tr>
	<th scope='row'><label for='$option->option_name'>$option->option_name</label></th>
	<td><input type='text' name='$option->option_name' id='$option->option_name' size='30' value='$value' /></td>
	<td>$option->option_description</td>
</tr>";
endforeach;
?>
  </table>
<p class="submit"><input type="submit" name="Update" value="<?php _e('Update Settings &raquo;') ?>" /></p>
  </form>
</div>


<?php
break;
} // end switch

include('admin-footer.php');
?>
