<?php
require_once('admin.php');

$title = __('Options');
$this_file = 'options.php';
$parent_file = 'options-general.php';

wp_reset_vars(array('action'));

if ( !current_user_can('manage_options') )
	wp_die(__('Cheatin&#8217; uh?'));

function sanitize_option($option, $value) {

	switch ($option) {
		case 'admin_email':
			$value = sanitize_email($value);
			break;

		case 'default_post_edit_rows':
		case 'mailserver_port':
		case 'comment_max_links':
			$value = abs((int) $value);
			break;

		case 'posts_per_page':
		case 'posts_per_rss':
			$value = (int) $value;
			if ( empty($value) ) $value = 1;
			if ( $value < -1 ) $value = abs($value);
			break;

		case 'default_ping_status':
		case 'default_comment_status':
			// Options that if not there have 0 value but need to be something like "closed"
			if ( $value == '0' || $value == '')
				$value = 'closed';
			break;

		case 'blogdescription':
		case 'blogname':
			if (current_user_can('unfiltered_html') == false)
				$value = wp_filter_post_kses( $value );
			break;

		case 'blog_charset':
			$value = preg_replace('/[^a-zA-Z0-9_-]/', '', $value);
			break;

		case 'date_format':
		case 'time_format':
		case 'mailserver_url':
		case 'mailserver_login':
		case 'mailserver_pass':
		case 'ping_sites':
		case 'upload_path':
			$value = strip_tags($value);
			$value = wp_filter_kses($value);
			break;

		case 'gmt_offset':
			$value = preg_replace('/[^0-9:.-]/', '', $value);
			break;

		case 'siteurl':
		case 'home':
			$value = clean_url($value);
			break;
	}

	return $value;	
}

switch($action) {

case 'update':
	$any_changed = 0;

	check_admin_referer('update-options');

	if (!$_POST['page_options']) {
		foreach ($_POST as $key => $value) {
			$options[] = $key;
		}
	} else {
		$options = explode(',', stripslashes($_POST['page_options']));
	}

	// Save for later.
	$old_siteurl = get_option('siteurl');
	$old_home = get_option('home');

	if ($options) {
		foreach ($options as $option) {
			$option = trim($option);
			$value = trim(stripslashes($_POST[$option]));
			$value = sanitize_option($option, $value);

			if (update_option($option, $value) ) {
				$any_changed++;
			}
		}
	}
    
	if ($any_changed) {
			// If siteurl or home changed, reset cookies.
			if ( get_option('siteurl') != $old_siteurl || get_option('home') != $old_home ) {
				// If home changed, write rewrite rules to new location.
				$wp_rewrite->flush_rules();
				// Clear cookies for old paths.
				wp_clearcookie();
				// Set cookies for new paths.
				wp_setcookie($user_login, $user_pass_md5, true, get_option('home'), get_option('siteurl'));
			}

			//$message = sprintf(__('%d setting(s) saved... '), $any_changed);
    }
    
	$referred = remove_query_arg('updated' , wp_get_referer());
	$goback = add_query_arg('updated', 'true', wp_get_referer());
	$goback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $goback);
	wp_redirect($goback);
    break;

default:
	include('admin-header.php'); ?>

<div class="wrap">
  <h2><?php _e('All options'); ?></h2>
  <form name="form" action="options.php" method="post">
  <?php wp_nonce_field('update-options') ?>
  <input type="hidden" name="action" value="update" />
  <table width="98%">
<?php
$options = $wpdb->get_results("SELECT * FROM $wpdb->options ORDER BY option_name");

foreach ($options as $option) :
	$value = wp_specialchars($option->option_value, 'single');
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
