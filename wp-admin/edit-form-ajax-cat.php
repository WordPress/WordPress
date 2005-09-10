<?php
require_once('../wp-config.php');
require_once('admin-functions.php');

get_currentuserinfo();

if ( !current_user_can('manage_categories') )
	die('-1');

function get_out_now() { exit; }

add_action('shutdown', 'get_out_now', -1);

$cat_name = rawurldecode($_GET['ajaxnewcat']);

if ( !$category_nicename = sanitize_title($cat_name) )
	die('0');
if ( $already = category_exists($cat_name) )
	die((string) $already);

$cat_name = $wpdb->escape($cat_name);
$new_cat_id = wp_create_category($cat_name);
die((string) $new_cat_id);
?>
