<?php
require_once('admin.php');

wp_reset_vars(array('action', 'cat'));

switch($action) {

case 'addcat':

	check_admin_referer('add-link-category');

	if ( !current_user_can('manage_categories') )
		wp_die(__('Cheatin&#8217; uh?'));

	if ( wp_insert_term($_POST['name'], 'link_category', $_POST ) ) {
		wp_redirect('edit-link-categories.php?message=1#addcat');
	} else {
		wp_redirect('edit-link-categories.php?message=4#addcat');
	}
	exit;
break;

case 'delete':
	$cat_ID = (int) $_GET['cat_ID'];
	check_admin_referer('delete-link-category_' .  $cat_ID);

	if ( !current_user_can('manage_categories') )
		wp_die(__('Cheatin&#8217; uh?'));

	$cat_name = get_term_field('name', $cat_ID, 'link_category');

	// Don't delete the default cats.
    if ( $cat_ID == get_option('default_link_category') )
		wp_die(sprintf(__("Can&#8217;t delete the <strong>%s</strong> category: this is the default one"), $cat_name));

	wp_delete_term($cat_ID, 'link_category');

	$location = 'edit-link-categories.php';
	if ( $referer = wp_get_original_referer() ) {
		if ( false !== strpos($referer, 'edit-link-categories.php') )
			$location = $referer;
	}

	$location = add_query_arg('message', 2, $location);

	wp_redirect($location);
	exit;

break;

case 'edit':
	$title = __('Categories');
	$parent_file = 'edit.php';
	$submenu_file = 'edit-link-categories.php';
	require_once ('admin-header.php');
	$cat_ID = (int) $_GET['cat_ID'];
	$category = get_term_to_edit($cat_ID, 'link_category');
	include('edit-link-category-form.php');
	include('admin-footer.php');
	exit;
break;

case 'editedcat':
	$cat_ID = (int) $_POST['cat_ID'];
	check_admin_referer('update-link-category_' . $cat_ID);

	if ( !current_user_can('manage_categories') )
		wp_die(__('Cheatin&#8217; uh?'));

	$location = 'edit-link-categories.php';
	if ( $referer = wp_get_original_referer() ) {
		if ( false !== strpos($referer, 'edit-link-categories.php') )
			$location = $referer;
	}

	if ( wp_update_term($cat_ID, 'link_category', $_POST) )
		$location = add_query_arg('message', 3, $location);
	else
		$location = add_query_arg('message', 5, $location);

	wp_redirect($location);
	exit;
break;
}

?>
