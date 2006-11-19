<?php
require_once ('admin.php');

wp_reset_vars(array('action', 'cat_id', 'linkurl', 'name', 'image', 'description', 'visible', 'target', 'category', 'link_id', 'submit', 'order_by', 'links_show_cat_id', 'rating', 'rel', 'notes', 'linkcheck[]'));

if ('' != $_POST['deletebookmarks'])
	$action = 'deletebookmarks';
if ('' != $_POST['move'])
	$action = 'move';
if ('' != $_POST['linkcheck'])
	$linkcheck = $_POST[linkcheck];

$this_file = 'link-manager.php';

switch ($action) {
		case 'deletebookmarks' :
		check_admin_referer('bulk-bookmarks');

		// check the current user's level first.
		if (!current_user_can('manage_links'))
			wp_die(__('Cheatin&#8217; uh?'));

		//for each link id (in $linkcheck[]) change category to selected value
		if (count($linkcheck) == 0) {
			wp_redirect($this_file);
			exit;
		}

		$deleted = 0;
		foreach ($linkcheck as $link_id) {
			$link_id = (int) $link_id;

			if ( wp_delete_link($link_id) )
				$deleted++;
		}

		wp_redirect("$this_file?deleted=$deleted");
		exit;
		break;

	case 'move' :
		check_admin_referer('bulk-bookmarks');

		// check the current user's level first.
		if (!current_user_can('manage_links'))
			wp_die(__('Cheatin&#8217; uh?'));

		//for each link id (in $linkcheck[]) change category to selected value
		if (count($linkcheck) == 0) {
			wp_redirect($this_file);
			exit;
		}
		$all_links = join(',', $linkcheck);
		// should now have an array of links we can change
		//$q = $wpdb->query("update $wpdb->links SET link_category='$category' WHERE link_id IN ($all_links)");

		wp_redirect($this_file);
		exit;
		break;

	case 'add' :
		check_admin_referer('add-bookmark');

		add_link();

		wp_redirect(wp_get_referer().'?added=true');
		exit;
		break;

	case 'save' :
		$link_id = (int) $_POST['link_id'];
		check_admin_referer('update-bookmark_' . $link_id);

		edit_link($link_id);

		wp_redirect($this_file);
		exit;
		break;

	case 'delete' :
		$link_id = (int) $_GET['link_id'];
		check_admin_referer('delete-bookmark_' . $link_id);

		if (!current_user_can('manage_links'))
			wp_die(__('Cheatin&#8217; uh?'));

		wp_delete_link($link_id);

		wp_redirect($this_file);
		exit;
		break;

	case 'edit' :
		wp_enqueue_script( array('xfn', 'dbx-admin-key?pagenow=link.php') );
		if ( current_user_can( 'manage_categories' ) )
			wp_enqueue_script( 'ajaxcat' );
		$parent_file = 'link-manager.php';
		$submenu_file = 'link-manager.php';
		$title = __('Edit Bookmark');
		include_once ('admin-header.php');
		if (!current_user_can('manage_links'))
			wp_die(__('You do not have sufficient permissions to edit the bookmarks for this blog.'));

		$link_id = (int) $_GET['link_id'];

		if (!$link = get_link_to_edit($link_id))
			wp_die(__('Link not found.'));

		include ('edit-link-form.php');
		break;

	default :
		break;
}

include ('admin-footer.php');
?>
