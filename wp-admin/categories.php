<?php
/**
 * Categories Management Panel
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Bootstrap */
require_once('admin.php');

$title = __('Categories');

wp_reset_vars(array('action', 'cat'));

if ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' && isset($_GET['delete']) )
	$action = 'bulk-delete';

switch($action) {

case 'addcat':

	check_admin_referer('add-category');

	if ( !current_user_can('manage_categories') )
		wp_die(__('Cheatin&#8217; uh?'));

	if( wp_insert_category($_POST ) ) {
		wp_redirect('categories.php?message=1#addcat');
	} else {
		wp_redirect('categories.php?message=4#addcat');
	}
	exit;
break;

case 'delete':
	$cat_ID = (int) $_GET['cat_ID'];
	check_admin_referer('delete-category_' .  $cat_ID);

	if ( !current_user_can('manage_categories') )
		wp_die(__('Cheatin&#8217; uh?'));

	$cat_name = get_catname($cat_ID);

	// Don't delete the default cats.
    if ( $cat_ID == get_option('default_category') )
		wp_die(sprintf(__("Can&#8217;t delete the <strong>%s</strong> category: this is the default one"), $cat_name));

	wp_delete_category($cat_ID);

	wp_redirect('categories.php?message=2');
	exit;

break;

case 'bulk-delete':
	check_admin_referer('bulk-categories');

	if ( !current_user_can('manage_categories') )
		wp_die( __('You are not allowed to delete categories.') );

	foreach ( (array) $_GET['delete'] as $cat_ID ) {
		$cat_name = get_catname($cat_ID);

		// Don't delete the default cats.
		if ( $cat_ID == get_option('default_category') )
			wp_die(sprintf(__("Can&#8217;t delete the <strong>%s</strong> category: this is the default one"), $cat_name));

		wp_delete_category($cat_ID);
	}

	$sendback = wp_get_referer();
	$sendback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $sendback);

	wp_redirect($sendback);
	exit();

break;
case 'edit':

	require_once ('admin-header.php');
	$cat_ID = (int) $_GET['cat_ID'];
	$category = get_category_to_edit($cat_ID);
	include('edit-category-form.php');

break;

case 'editedcat':
	$cat_ID = (int) $_POST['cat_ID'];
	check_admin_referer('update-category_' . $cat_ID);

	if ( !current_user_can('manage_categories') )
		wp_die(__('Cheatin&#8217; uh?'));

	if ( wp_update_category($_POST) )
		wp_redirect('categories.php?message=3');
	else
		wp_redirect('categories.php?message=5');

	exit;
break;

default:

if ( !empty($_GET['_wp_http_referer']) ) {
	 wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI'])));
	 exit;
}

wp_enqueue_script( 'admin-categories' );
wp_enqueue_script('admin-forms');

require_once ('admin-header.php');

$messages[1] = __('Category added.');
$messages[2] = __('Category deleted.');
$messages[3] = __('Category updated.');
$messages[4] = __('Category not added.');
$messages[5] = __('Category not updated.');

if ( isset($_GET['message']) && ( $msg = (int) $_GET['message'] ) ) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$msg]; ?></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
endif; ?>

<div class="wrap">

<form id="adv-settings" action="" method="get">
<div id="show-settings"><a href="#edit_settings" id="show-settings-link" class="hide-if-no-js"><?php _e('Advanced Options') ?></a>
<a href="#edit_settings" id="hide-settings-link" class="hide-if-js hide-if-no-js"><?php _e('Hide Options') ?></a></div>

<div id="edit-settings" class="hide-if-js hide-if-no-js">
<div id="edit-settings-wrap">
<h5><?php _e('Show on screen') ?></h5>
<div class="metabox-prefs">
<?php manage_columns_prefs('category') ?>
<br class="clear" />
</div></div>
<?php wp_nonce_field( 'hiddencolumns', 'hiddencolumnsnonce', false ); ?>
</div></form>

	<h2><?php printf( current_user_can('manage_categories') ? __('Categories (<a href="%s">Add New</a>)') : __('Manage Tags'), '#addcat' ); ?></h2>

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
	'total' => ceil(wp_count_terms('category') / $catsperpage),
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
<?php wp_nonce_field('bulk-categories'); ?>
</div>

<br class="clear" />
</div>

<br class="clear" />

<table class="widefat">
	<thead>
	<tr>
<?php print_column_headers('category'); ?>
	</tr>
	</thead>
	<tbody id="the-list" class="list:cat">
<?php
cat_rows(0, 0, 0, $pagenum, $catsperpage);
?>
	</tbody>
</table>

</form>

<div class="tablenav">

<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>
<br class="clear" />
</div>
<br class="clear" />

</div>

<?php if ( current_user_can('manage_categories') ) : ?>
<div class="wrap">
<p><?php printf(__('<strong>Note:</strong><br />Deleting a category does not delete the posts in that category. Instead, posts that were only assigned to the deleted category are set to the category <strong>%s</strong>.'), apply_filters('the_category', get_catname(get_option('default_category')))) ?></p>
<p><?php printf(__('Categories can be selectively converted to tags using the <a href="%s">category to tag converter</a>.'), 'admin.php?import=wp-cat2tag') ?></p>
</div>

<?php include('edit-category-form.php'); ?>

<?php endif; ?>

<?php
break;
}

include('admin-footer.php');

?>
