<?php
require_once('admin.php');

$title = __('Options');
$this_file = 'options.php';
$parent_file = 'options-general.php';

$wpvarstoreset = array('action');
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

if ( !current_user_can('manage_options') )
	die ( __('Cheatin&#8217; uh?') );

switch($action) {

case 'update':
	$any_changed = 0;

	check_admin_referer();

	if (!$_POST['page_options']) {
		foreach ($_POST as $key => $value) {
			$options[] = $key;
		}
	} else {
		$options = explode(',', stripslashes($_POST['page_options']));
	}

	// Save for later.
	$old_siteurl = get_settings('siteurl');
	$old_home = get_settings('home');

	// HACK
	// Options that if not there have 0 value but need to be something like "closed"
	$nonbools = array('default_ping_status', 'default_comment_status');
	if ($options) {
		foreach ($options as $option) {
			$option = trim($option);
			$value = trim(stripslashes($_POST[$option]));
				if( in_array($option, $nonbools) && ( $value == '0' || $value == '') )
				$value = 'closed';

			if( $option == 'blogdescription' || $option == 'blogname' )
				if (current_user_can('unfiltered_html') == false)
					$value = wp_filter_post_kses( $value );

			if (update_option($option, $value) ) {
				$any_changed++;
			}
		}
	}
    
	if ($any_changed) {
			// If siteurl or home changed, reset cookies.
			if ( get_settings('siteurl') != $old_siteurl || get_settings('home') != $old_home ) {
				// If home changed, write rewrite rules to new location.
				$wp_rewrite->flush_rules();
				// Clear cookies for old paths.
				wp_clearcookie();
				// Set cookies for new paths.
				wp_setcookie($user_login, $user_pass_md5, true, get_settings('home'), get_settings('siteurl'));
			}

			//$message = sprintf(__('%d setting(s) saved... '), $any_changed);
    }
    
	$referred = remove_query_arg('updated' , $_SERVER['HTTP_REFERER']);
	$goback = add_query_arg('updated', 'true', $_SERVER['HTTP_REFERER']);
	$goback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $goback);
	wp_redirect($goback);
    break;

default:
	include('admin-header.php'); ?>

<div class="wrap">
  <h2><?php _e('All options'); ?></h2>
  <form name="form" action="options.php" method="post">
  <input type="hidden" name="action" value="update" />
  <table width="98%">
<?php
$options = $wpdb->get_results("SELECT * FROM $wpdb->options ORDER BY option_name");

foreach ($options as $option) :
	$value = wp_specialchars($option->option_value);
	echo "
<tr>
	<th scope='row'><label for='$option->option_name'>$option->option_name</label></th>
	<td><input type='text' name='$option->option_name' id='$option->option_name' size='30' value='" . $value . "' /></td>
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
