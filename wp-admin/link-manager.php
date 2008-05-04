<?php

require_once ('admin.php');

// Handle bulk deletes
if ( isset($_GET['deleteit']) && isset($_GET['linkcheck']) ) {
	check_admin_referer('bulk-bookmarks');

	if ( ! current_user_can('manage_links') )
		wp_die( __('You do not have sufficient permissions to edit the links for this blog.') );

	foreach ( (array) $_GET['linkcheck'] as $link_id) {
		$link_id = (int) $link_id;

		wp_delete_link($link_id);
	}

	$sendback = wp_get_referer();
	$sendback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $sendback);
	wp_redirect($sendback);
	exit;
} elseif ( !empty($_GET['_wp_http_referer']) ) {
	 wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI'])));
	 exit;
}

wp_enqueue_script('admin-forms');

wp_reset_vars(array('action', 'cat_id', 'linkurl', 'name', 'image', 'description', 'visible', 'target', 'category', 'link_id', 'submit', 'order_by', 'links_show_cat_id', 'rating', 'rel', 'notes', 'linkcheck[]'));

if (empty ($cat_id))
	$cat_id = 'all';

if (empty ($order_by))
	$order_by = 'order_name';

$title = __('Manage Links');
$this_file = $parent_file = 'edit.php';
include_once ("./admin-header.php");

if (!current_user_can('manage_links'))
	wp_die(__("You do not have sufficient permissions to edit the links for this blog."));

switch ($order_by) {
	case 'order_id' :
		$sqlorderby = 'id';
		break;
	case 'order_url' :
		$sqlorderby = 'url';
		break;
	case 'order_desc' :
		$sqlorderby = 'description';
		break;
	case 'order_owner' :
		$sqlorderby = 'owner';
		break;
	case 'order_rating' :
		$sqlorderby = 'rating';
		break;
	case 'order_name' :
	default :
		$sqlorderby = 'name';
		break;
}

if ( isset($_GET['deleted']) ) {
	echo '<div style="background-color: rgb(207, 235, 247);" id="message" class="updated fade"><p>';
	$deleted = (int) $_GET['deleted'];
	printf(__ngettext('%s link deleted.', '%s links deleted', $deleted), $deleted);
	echo '</p></div>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('deleted'), $_SERVER['REQUEST_URI']);
}
?>

<div class="wrap">

<form id="posts-filter" action="" method="get">
<h2><?php printf( __( 'Manage Links (<a href="%s">add new</a>)' ), 'link-add.php' ); ?></h2>

<p id="post-search">
	<label class="hidden" for="post-search-input"><?php _e( 'Search Links' ); ?>:</label>
	<input type="text" id="post-search-input" name="s" value="<?php echo attribute_escape(stripslashes($_GET['s'])); ?>" />
	<input type="submit" value="<?php _e( 'Search Links' ); ?>" class="button" />
</p>

<br class="clear" />

<div class="tablenav">

<div class="alignleft">
<input type="submit" value="<?php _e('Delete'); ?>" name="deleteit" class="button-secondary delete" />
<?php
$categories = get_terms('link_category', "hide_empty=1");
$select_cat = "<select name=\"cat_id\">\n";
$select_cat .= '<option value="all"'  . (($cat_id == 'all') ? " selected='selected'" : '') . '>' . __('View all Categories') . "</option>\n";
foreach ((array) $categories as $cat)
	$select_cat .= '<option value="' . $cat->term_id . '"' . (($cat->term_id == $cat_id) ? " selected='selected'" : '') . '>' . sanitize_term_field('name', $cat->name, $cat->term_id, 'link_category', 'display') . "</option>\n";
$select_cat .= "</select>\n";

$select_order = "<select name=\"order_by\">\n";
$select_order .= '<option value="order_id"' . (($order_by == 'order_id') ? " selected='selected'" : '') . '>' .  __('Order by Link ID') . "</option>\n";
$select_order .= '<option value="order_name"' . (($order_by == 'order_name') ? " selected='selected'" : '') . '>' .  __('Order by Name') . "</option>\n";
$select_order .= '<option value="order_url"' . (($order_by == 'order_url') ? " selected='selected'" : '') . '>' .  __('Order by Address') . "</option>\n";
$select_order .= '<option value="order_rating"' . (($order_by == 'order_rating') ? " selected='selected'" : '') . '>' .  __('Order by Rating') . "</option>\n";
$select_order .= "</select>\n";

echo $select_cat;
echo $select_order;

?>
<input type="submit" id="post-query-submit" value="<?php _e('Filter'); ?>" class="button-secondary" />

</div>

<br class="clear" />
</div>

<br class="clear" />

<?php
$link_columns = array(
	'name'       => '<th style="width: 15%;">' . __('Name') . '</th>',
	'url'       => '<th>' . __('URL') . '</th>',
	'categories' => '<th>' . __('Categories') . '</th>',
	'rel'      => '<th style="text-align: center">' . __('rel') . '</th>',
	'visible'   => '<th style="text-align: center">' . __('Visible') . '</th>',
);
$link_columns = apply_filters('manage_link_columns', $link_columns);
?>

<?php
if ( 'all' == $cat_id )
	$cat_id = '';
$args = array('category' => $cat_id, 'hide_invisible' => 0, 'orderby' => $sqlorderby, 'hide_empty' => 0);
if ( !empty($_GET['s']) )
	$args['search'] = $_GET['s'];
$links = get_bookmarks( $args );
if ( $links ) {
?>

<?php wp_nonce_field('bulk-bookmarks') ?>
<table class="widefat">
	<thead>
	<tr>
	<th scope="col" class="check-column"><input type="checkbox" /></th>
<?php foreach($link_columns as $column_display_name) {
	echo $column_display_name;
} ?>
	</tr>
	</thead>
	<tbody>
<?php
	foreach ($links as $link) {
		$link = sanitize_bookmark($link);
		$link->link_name = attribute_escape($link->link_name);
		$link->link_category = wp_get_link_cats($link->link_id);
		$short_url = str_replace('http://', '', $link->link_url);
		$short_url = str_replace('www.', '', $short_url);
		if ('/' == substr($short_url, -1))
			$short_url = substr($short_url, 0, -1);
		if (strlen($short_url) > 35)
			$short_url = substr($short_url, 0, 32).'...';

		$visible = ($link->link_visible == 'Y') ? __('Yes') : __('No');
		++ $i;
		$style = ($i % 2) ? '' : ' class="alternate"';
		?><tr id="link-<?php echo $link->link_id; ?>" valign="middle" <?php echo $style; ?>><?php
		echo '<th scope="row" class="check-column"><input type="checkbox" name="linkcheck[]" value="'.$link->link_id.'" /></th>';
		foreach($link_columns as $column_name=>$column_display_name) {
			switch($column_name) {
				case 'name':

					echo "<td><strong><a class='row-title' href='link.php?link_id=$link->link_id&amp;action=edit' title='" . attribute_escape(sprintf(__('Edit "%s"'), $link->link_name)) . "' class='edit'>$link->link_name</a></strong><br />";
					echo $link->link_description . "</td>";
					break;
				case 'url':
					echo "<td><a href='$link->link_url' title='".sprintf(__('Visit %s'), $link->link_name)."'>$short_url</a></td>";
					break;
				case 'categories':
					?><td><?php
					$cat_names = array();
					foreach ($link->link_category as $category) {
						$cat = get_term($category, 'link_category', OBJECT, 'display');
						if ( is_wp_error( $cat ) )
							echo $cat->get_error_message();
						$cat_name = $cat->name;
						if ( $cat_id != $category )
							$cat_name = "<a href='link-manager.php?cat_id=$category'>$cat_name</a>";
						$cat_names[] = $cat_name;
					}
					echo implode(', ', $cat_names);
					?> </td><?php
					break;
				case 'rel':
					?><td><?php echo $link->link_rel; ?></td><?php
					break;
				case 'visible':
					?><td style='text-align: center;'><?php echo $visible; ?></td><?php
					break;
				default:
					?>
					<td><?php do_action('manage_link_custom_column', $column_name, $link->link_id); ?></td>
					<?php
					break;

			}
		}
		echo "\n    </tr>\n";
	}
?>
	</tbody>
</table>

<?php } else { ?>
<p><?php _e('No links found.') ?></p>
<?php } ?>
</form>

<div id="ajax-response"></div>

<div class="tablenav">
<br class="clear" />
</div>


</div>

<?php include('admin-footer.php'); ?>
