<?php
/**
 * Edit Link Categories Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

// Handle bulk actions
if ( isset($_GET['action']) && isset($_GET['delete']) ) {
	check_admin_referer('bulk-link-categories');
	$doaction = $_GET['action'] ? $_GET['action'] : $_GET['action2'];

	if ( !current_user_can('manage_categories') )
		wp_die(__('Cheatin&#8217; uh?'));

	if ( 'delete' == $doaction ) {
		foreach( (array) $_GET['delete'] as $cat_ID ) {
			$cat_name = get_term_field('name', $cat_ID, 'link_category');
			$default_cat_id = get_option('default_link_category');

			// Don't delete the default cats.
			if ( $cat_ID == $default_cat_id )
				wp_die(sprintf(__("Can&#8217;t delete the <strong>%s</strong> category: this is the default one"), $cat_name));

			wp_delete_term($cat_ID, 'link_category', array('default' => $default_cat_id));
		}

		$location = 'edit-link-categories.php';
		if ( $referer = wp_get_referer() ) {
			if ( false !== strpos($referer, 'edit-link-categories.php') )
				$location = $referer;
		}

		$location = add_query_arg('message', 6, $location);
		wp_redirect($location);
		exit();
	}
} elseif ( isset($_GET['_wp_http_referer']) && ! empty($_GET['_wp_http_referer']) ) {
	 wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI']) ) );
	 exit;
}

$title = __('Link Categories');

wp_enqueue_script( 'admin-categories' );
wp_enqueue_script('admin-forms');
if ( current_user_can('manage_categories') )
	wp_enqueue_script('inline-edit-tax');

require_once ('admin-header.php');

$messages[1] = __('Category added.');
$messages[2] = __('Category deleted.');
$messages[3] = __('Category updated.');
$messages[4] = __('Category not added.');
$messages[5] = __('Category not updated.');
$messages[6] = __('Categories deleted.'); ?>

<div id="edit-settings-wrap" class="hidden">
<h5><?php _e('Show on screen') ?></h5>
<form id="adv-settings" action="" method="get">
<div class="metabox-prefs">
<?php manage_columns_prefs('link-category') ?>
<?php wp_nonce_field( 'hiddencolumns', 'hiddencolumnsnonce', false ); ?>
<br class="clear" />
</div></form>
</div>

<?php if ( isset($_GET['message']) && ( $msg = (int) $_GET['message'] ) ) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$msg]; ?></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
endif; ?>

<div class="wrap">

<ul class="subsubsub"><li><a class="current"><br /></a></li></ul>
<form class="search-form" action="" method="get">
<p class="search-box">
	<label class="hidden" for="post-search-input"><?php _e( 'Search Categories' ); ?>:</label>
	<input type="text" class="search-input" id="post-search-input" name="s" value="<?php _admin_search_query(); ?>" />
	<input type="submit" value="<?php _e( 'Search Categories' ); ?>" class="button" />
</p>
</form>
<br class="clear" />

<form id="posts-filter" action="" method="get">
<div class="tablenav">

<?php
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 0;
if ( empty($pagenum) )
	$pagenum = 1;
if( ! isset( $catsperpage ) || $catsperpage < 0 )
	$catsperpage = 20;

$page_links = paginate_links( array(
	'base' => add_query_arg( 'pagenum', '%#%' ),
	'format' => '',
	'total' => ceil(wp_count_terms('link_category') / $catsperpage),
	'current' => $pagenum
));

if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft">
<select name="action">
<option value="" selected><?php _e('Actions'); ?></option>
<option value="delete"><?php _e('Delete'); ?></option>
</select>
<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
<?php wp_nonce_field('bulk-link-categories'); ?>
</div>

<br class="clear" />
</div>

<div class="clear"></div>

<table class="widefat">
	<thead>
	<tr>
<?php print_column_headers('link-category'); ?>
	</tr>
	</thead>

	<tfoot>
	<tr>
<?php print_column_headers('link-category', false); ?>
	</tr>
	</tfoot>

	<tbody id="the-list" class="list:link-cat">
<?php
$start = ($pagenum - 1) * $catsperpage;
$args = array('offset' => $start, 'number' => $catsperpage, 'hide_empty' => 0);
if ( !empty( $_GET['s'] ) )
	$args['search'] = $_GET['s'];

$categories = get_terms( 'link_category', $args );
if ( $categories ) {
	$output = '';
	foreach ( $categories as $category ) {
		$category = sanitize_term($category, 'link_category', 'display');
		$output .= link_cat_row($category);
	}
	$output = apply_filters('cat_rows', $output);
	echo $output;
	unset($category);
}

?>
	</tbody>
</table>

<div class="tablenav">
<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft">
<select name="action2">
<option value="" selected><?php _e('Actions'); ?></option>
<option value="delete"><?php _e('Delete'); ?></option>
</select>
<input type="submit" value="<?php _e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
</div>

<br class="clear" />
</div>
<br class="clear" />
</form>

</div>

<?php if ( current_user_can('manage_categories') ) : ?>
<div class="wrap">
<p><?php printf(__('<strong>Note:</strong><br />Deleting a category does not delete the links in that category. Instead, links that were only assigned to the deleted category are set to the category <strong>%s</strong>.'), get_term_field('name', get_option('default_link_category'), 'link_category')) ?></p>
</div>

<?php include('edit-link-category-form.php'); ?>
<?php inline_edit_term_row('link-category'); ?>

<?php endif; ?>

<?php include('admin-footer.php'); ?>
