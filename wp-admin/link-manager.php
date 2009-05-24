<?php
/**
 * Link Management Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Administration Bootstrap */
require_once ('admin.php');

// Handle bulk deletes
if ( isset($_GET['action']) && isset($_GET['linkcheck']) ) {
	check_admin_referer('bulk-bookmarks');
	$doaction = $_GET['action'] ? $_GET['action'] : $_GET['action2'];

	if ( ! current_user_can('manage_links') )
		wp_die( __('You do not have sufficient permissions to edit the links for this blog.') );

	if ( 'delete' == $doaction ) {
		foreach ( (array) $_GET['linkcheck'] as $link_id ) {
			$link_id = (int) $link_id;

			wp_delete_link($link_id);
		}

		wp_redirect( wp_get_referer() );
		exit;
	}
} elseif ( isset($_GET['_wp_http_referer']) && ! empty($_GET['_wp_http_referer']) ) {
	 wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI']) ) );
	 exit;
}

wp_reset_vars(array('action', 'cat_id', 'linkurl', 'name', 'image', 'description', 'visible', 'target', 'category', 'link_id', 'submit', 'order_by', 'links_show_cat_id', 'rating', 'rel', 'notes', 'linkcheck[]'));

if ( empty($cat_id) )
	$cat_id = 'all';

if ( empty($order_by) )
	$order_by = 'order_name';

$title = __('Edit Links');
$this_file = $parent_file = 'link-manager.php';
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
} ?>

<div class="wrap nosubsub">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title );
if ( isset($_GET['s']) && $_GET['s'] )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', esc_html( stripslashes($_GET['s']) ) ); ?>
</h2>

<?php
if ( isset($_GET['deleted']) ) {
	echo '<div id="message" class="updated fade"><p>';
	$deleted = (int) $_GET['deleted'];
	printf(_n('%s link deleted.', '%s links deleted', $deleted), $deleted);
	echo '</p></div>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('deleted'), $_SERVER['REQUEST_URI']);
}
?>

<form class="search-form" action="" method="get">
<p class="search-box">
	<label class="screen-reader-text" for="link-search-input"><?php _e( 'Search Links' ); ?>:</label>
	<input type="text" id="link-search-input" name="s" value="<?php _admin_search_query(); ?>" />
	<input type="submit" value="<?php esc_attr_e( 'Search Links' ); ?>" class="button" />
</p>
</form>
<br class="clear" />

<form id="posts-filter" action="" method="get">
<div class="tablenav">

<div class="alignleft actions">
<select name="action">
<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
<option value="delete"><?php _e('Delete'); ?></option>
</select>
<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />

<?php
$categories = get_terms('link_category', "hide_empty=1");
$select_cat = "<select name=\"cat_id\">\n";
$select_cat .= '<option value="all"'  . (($cat_id == 'all') ? " selected='selected'" : '') . '>' . __('View all Categories') . "</option>\n";
foreach ((array) $categories as $cat)
	$select_cat .= '<option value="' . esc_attr($cat->term_id) . '"' . (($cat->term_id == $cat_id) ? " selected='selected'" : '') . '>' . sanitize_term_field('name', $cat->name, $cat->term_id, 'link_category', 'display') . "</option>\n";
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
<input type="submit" id="post-query-submit" value="<?php esc_attr_e('Filter'); ?>" class="button-secondary" />

</div>

<br class="clear" />
</div>

<div class="clear"></div>

<?php
if ( 'all' == $cat_id )
	$cat_id = '';
$args = array('category' => $cat_id, 'hide_invisible' => 0, 'orderby' => $sqlorderby, 'hide_empty' => 0);
if ( !empty($_GET['s']) )
	$args['search'] = $_GET['s'];
$links = get_bookmarks( $args );
if ( $links ) {
	$link_columns = get_column_headers('link-manager');
	$hidden = get_hidden_columns('link-manager');
?>

<?php wp_nonce_field('bulk-bookmarks') ?>
<table class="widefat fixed" cellspacing="0">
	<thead>
	<tr>
<?php print_column_headers('link-manager'); ?>
	</tr>
	</thead>

	<tfoot>
	<tr>
<?php print_column_headers('link-manager', false); ?>
	</tr>
	</tfoot>

	<tbody>
<?php
	$alt = 0;

	foreach ($links as $link) {
		$link = sanitize_bookmark($link);
		$link->link_name = esc_attr($link->link_name);
		$link->link_category = wp_get_link_cats($link->link_id);
		$short_url = str_replace('http://', '', $link->link_url);
		$short_url = preg_replace('/^www\./i', '', $short_url);
		if ('/' == substr($short_url, -1))
			$short_url = substr($short_url, 0, -1);
		if (strlen($short_url) > 35)
			$short_url = substr($short_url, 0, 32).'...';
		$visible = ($link->link_visible == 'Y') ? __('Yes') : __('No');
		$rating  = $link->link_rating;
		$style = ($alt % 2) ? '' : ' class="alternate"';
		++ $alt;
		$edit_link = get_edit_bookmark_link();
		?><tr id="link-<?php echo $link->link_id; ?>" valign="middle" <?php echo $style; ?>><?php
		foreach($link_columns as $column_name=>$column_display_name) {
			$class = "class=\"column-$column_name\"";

			$style = '';
			if ( in_array($column_name, $hidden) )
				$style = ' style="display:none;"';

			$attributes = "$class$style";

			switch($column_name) {
				case 'cb':
					echo '<th scope="row" class="check-column"><input type="checkbox" name="linkcheck[]" value="'. esc_attr($link->link_id) .'" /></th>';
					break;
				case 'name':

					echo "<td $attributes><strong><a class='row-title' href='$edit_link' title='" . esc_attr(sprintf(__('Edit &#8220;%s&#8221;'), $link->link_name)) . "'>$link->link_name</a></strong><br />";
					$actions = array();
					$actions['edit'] = '<a href="' . $edit_link . '">' . __('Edit') . '</a>';
					$actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url("link.php?action=delete&amp;link_id=$link->link_id", 'delete-bookmark_' . $link->link_id) . "' onclick=\"if ( confirm('" . esc_js(sprintf( __("You are about to delete this link '%s'\n  'Cancel' to stop, 'OK' to delete."), $link->link_name )) . "') ) { return true;}return false;\">" . __('Delete') . "</a>";
					$action_count = count($actions);
					$i = 0;
					echo '<div class="row-actions">';
					foreach ( $actions as $action => $linkaction ) {
						++$i;
						( $i == $action_count ) ? $sep = '' : $sep = ' | ';
						echo "<span class='$action'>$linkaction$sep</span>";
					}
					echo '</div>';
					echo '</td>';
					break;
				case 'url':
					echo "<td $attributes><a href='$link->link_url' title='".sprintf(__('Visit %s'), $link->link_name)."'>$short_url</a></td>";
					break;
				case 'categories':
					?><td <?php echo $attributes ?>><?php
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
					?></td><?php
					break;
				case 'rel':
					?><td <?php echo $attributes ?>><?php echo empty($link->link_rel) ? '<br />' : $link->link_rel; ?></td><?php
					break;
				case 'visible':
					?><td <?php echo $attributes ?>><?php echo $visible; ?></td><?php
					break;
				case 'rating':
 					?><td <?php echo $attributes ?>><?php echo $rating; ?></td><?php
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

<div class="tablenav">

<div class="alignleft actions">
<select name="action2">
<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
<option value="delete"><?php _e('Delete'); ?></option>
</select>
<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
</div>

<br class="clear" />
</div>

</form>

<div id="ajax-response"></div>

</div>

<?php
include('admin-footer.php');
