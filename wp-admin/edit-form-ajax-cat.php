<?php
require_once('../wp-config.php');
require_once('admin-functions.php');

get_currentuserinfo();

if ( !current_user_can('manage_categories') )
	die('-1');

function grab_id() {
	global $new_cat_id;
	$new_cat_id = func_get_arg(0);
}

function get_out_now() { exit; }


add_action('edit_category', 'grab_id');
add_action('create_category', 'grab_id');
add_action('shutdown', 'get_out_now', -1);

$cat_name = rawurldecode($_GET['ajaxnewcat']);

if ( !$category_nicename = sanitize_title($cat_name) )
	die('0');
if ( $already = $wpdb->get_var("SELECT cat_ID FROM $wpdb->categories WHERE category_nicename = '$category_nicename'") )
	die($already);

$cat_name = $wpdb->escape($cat_name);
$cat_array = compact('cat_name', 'category_nicename');
wp_insert_category($cat_array);
die($new_cat_id);
?>