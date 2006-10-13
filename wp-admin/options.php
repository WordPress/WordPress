<?php
require_once('admin.php');

$title = __('Options');
$this_file = 'options.php';
$parent_file = 'options-general.php';

wp_reset_vars(array('action'));

if ( !current_user_can('manage_options') )
	wp_die(__('Cheatin&#8217; uh?'));

function sanitize_option($option, $value) { // Remember to call stripslashes!

	switch ($option) {
		case 'admin_email':
			$value = stripslashes($value);
			$value = sanitize_email($value);
			break;

		case 'default_post_edit_rows':
		case 'mailserver_port':
		case 'comment_max_links':
			$value = stripslashes($value);
			$value = abs((int) $value);
			break;

		case 'posts_per_page':
		case 'posts_per_rss':
			$value = stripslashes($value);
			$value = (int) $value;
			if ( empty($value) ) $value = 1;
			if ( $value < -1 ) $value = abs($value);
			break;

		case 'default_ping_status':
		case 'default_comment_status':
			$value = stripslashes($value);
			// Options that if not there have 0 value but need to be something like "closed"
			if ( $value == '0' || $value == '')
				$value = 'closed';
			break;

		case 'blogdescription':
		case 'blogname':
			if (current_user_can('unfiltered_html') == false)
				$value = wp_filter_post_kses( $value ); // calls stripslashes then addslashes
			$value = stripslashes($value);
			break;

		case 'blog_charset':
			$value = preg_replace('/[^a-zA-Z0-9_-]/', '', $value); // strips slashes
			break;

		case 'date_format':
		case 'time_format':
		case 'mailserver_url':
		case 'mailserver_login':
		case 'mailserver_pass':
		case 'ping_sites':
		case 'upload_path':
			$value = strip_tags($value);
			$value = wp_filter_kses($value); // calls stripslashes then addslashes
			$value = stripslashes($value);
			break;

		case 'gmt_offset':
			$value = preg_replace('/[^0-9:.-]/', '', $value); // strips slashes
			break;

		case 'siteurl':
		case 'home':
			$value = stripslashes($value);
			$value = clean_url($value);
			break;
		default :
			$value = stripslashes($value);
			break;
	}

	return $value;	
}

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
			$value = trim($_POST[$option]);
			$value = sanitize_option($option, $value); // This does stripslashes on those that need it
			update_option($option, $value);
		}
	}
    
	$referred = remove_query_arg('updated' , wp_get_referer());
	$goback = add_query_arg('updated', 'true', wp_get_referer());
	$goback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $goback);
	wp_redirect($goback);
    break;

default:
	include('admin-header.php'); ?>

<div class="wrap">
  <h2><?php _e('All Options'); ?></h2>
  <form name="form" action="options.php" method="post" id="all-options">
  <?php wp_nonce_field('update-options') ?>
  <input type="hidden" name="action" value="update" />
	<p class="submit"><input type="submit" name="Update" value="<?php _e('Update Options &raquo;') ?>" /></p>
  <table width="98%">
<?php
$options = $wpdb->get_results("SELECT * FROM $wpdb->options ORDER BY option_name");

foreach ( (array) $options as $option) :
	$disabled = '';
	if ( is_serialized($option->option_value) ) {
		if ( is_serialized_string($option->option_value) ) {
			// this is a serialized string, so we should display it
			$value = wp_specialchars(maybe_unserialize($option->option_value), 'single');
			$options_to_update[] = $option->option_name;
			$class = 'all-options';
		} else {
			$value = 'SERIALIZED DATA';
			$disabled = ' disabled="disabled"';
			$class = 'all-options disabled';
		}
	} else {
		$value = wp_specialchars($option->option_value, 'single');
		$options_to_update[] = $option->option_name;
		$class = 'all-options';
	}
	echo "
<tr>
	<th scope='row'><label for='$option->option_name'>$option->option_name</label></th>
<td>";

	if (stristr($value, "\n")) echo "<textarea class='$class' name='$option->option_name' id='$option->option_name' cols='30' rows='5'>$value</textarea>";
	else echo "<input class='$class' type='text' name='$option->option_name' id='$option->option_name' size='30' value='" . $value . "'$disabled />";
	
	echo "</td>
	<td>$option->option_description</td>
</tr>";
endforeach;
?>
  </table>
<?php $options_to_update = implode(',', $options_to_update); ?>
<p class="submit"><input type="hidden" name="page_options" value="<?php echo wp_specialchars($options_to_update, true); ?>" /><input type="submit" name="Update" value="<?php _e('Update Options &raquo;') ?>" /></p>
  </form>
</div>


<?php
break;
} // end switch

include('admin-footer.php');
?>
