<?php
require_once('admin.php');

$title = __('Settings');
$this_file = 'options.php';
$parent_file = 'options-general.php';

wp_reset_vars(array('action'));

if ( !current_user_can('manage_options') )
	wp_die(__('Cheatin&#8217; uh?'));

switch($action) {

case 'update':
	$any_changed = 0;

	check_admin_referer('update-options');

	if ( !$_POST['page_options'] ) {
		foreach ( (array) $_POST as $key => $value) {
			if ( !in_array($key, array('_wpnonce', '_wp_http_referer')) )
				$options[] = $key;
		}
	} else {
		$options = explode(',', stripslashes($_POST['page_options']));
	}

	if ($options) {
		foreach ($options as $option) {
			$option = trim($option);
			$value = $_POST[$option];
			if(!is_array($value))	$value = trim($value);
			$value = stripslashes_deep($value);
			update_option($option, $value);
		}
	}

	$goback = add_query_arg('updated', 'true', wp_get_referer());
	wp_redirect($goback);
    break;

default:
	include('admin-header.php'); ?>

<div class="wrap">
  <h2><?php _e('All Settings'); ?></h2>
  <form name="form" action="options.php" method="post" id="all-options">
  <?php wp_nonce_field('update-options') ?>
  <input type="hidden" name="action" value="update" />
  <table class="form-table">
<?php
$options = $wpdb->get_results("SELECT * FROM $wpdb->options ORDER BY option_name");

foreach ( (array) $options as $option) :
	$disabled = '';
	$option->option_name = attribute_escape($option->option_name);
	if ( is_serialized($option->option_value) ) {
		if ( is_serialized_string($option->option_value) ) {
			// this is a serialized string, so we should display it
			$value = maybe_unserialize($option->option_value);
			$options_to_update[] = $option->option_name;
			$class = 'all-options';
		} else {
			$value = 'SERIALIZED DATA';
			$disabled = ' disabled="disabled"';
			$class = 'all-options disabled';
		}
	} else {
		$value = $option->option_value;
		$options_to_update[] = $option->option_name;
		$class = 'all-options';
	}
	echo "
<tr>
	<th scope='row'>$option->option_name</th>
<td>";

	if (strpos($value, "\n") !== false) echo "<textarea class='$class' name='$option->option_name' id='$option->option_name' cols='30' rows='5'>" . wp_specialchars($value) . "</textarea>";
	else echo "<input class='$class' type='text' name='$option->option_name' id='$option->option_name' size='30' value='" . attribute_escape($value) . "'$disabled />";

	echo "</td>
</tr>";
endforeach;
?>
  </table>
<?php $options_to_update = implode(',', $options_to_update); ?>
<p class="submit"><input type="hidden" name="page_options" value="<?php echo $options_to_update; ?>" /><input type="submit" name="Update" value="<?php _e('Save Changes') ?>" /></p>
  </form>
</div>


<?php
break;
} // end switch

include('admin-footer.php');
?>
