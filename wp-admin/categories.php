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

wp_reset_vars( array('action', 'cat') );

if ( isset( $_GET['action'] ) && isset($_GET['delete']) && ( 'delete' == $_GET['action'] || 'delete' == $_GET['action2'] ) )
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

	wp_redirect($sendback);
	exit();

break;
case 'edit':

	$title = __('Edit Category');

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

	$location = 'categories.php';
	if ( $referer = wp_get_original_referer() ) {
		if ( false !== strpos($referer, 'categories.php') )
			$location = $referer;
	}

	if ( wp_update_category($_POST) )
		$location = add_query_arg('message', 3, $location);
	else
		$location = add_query_arg('message', 5, $location);

	wp_redirect($location);

	exit;
break;

default:

if ( isset($_GET['_wp_http_referer']) && ! empty($_GET['_wp_http_referer']) ) {
	 wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI']) ) );
	 exit;
}

wp_enqueue_script('admin-categories');
if ( current_user_can('manage_categories') )
	wp_enqueue_script('inline-edit-tax');

require_once ('admin-header.php');

$messages[1] = __('Category added.');
$messages[2] = __('Category deleted.');
$messages[3] = __('Category updated.');
$messages[4] = __('Category not added.');
$messages[5] = __('Category not updated.');
?>

<div class="wrap nosubsub">
<?php screen_icon(); ?>
<h2><?php echo wp_specialchars( $title );
if ( isset($_GET['s']) && $_GET['s'] )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', wp_specialchars( stripslashes($_GET['s']) ) ); ?>
</h2>

<?php
if ( isset($_GET['message']) && ( $msg = (int) $_GET['message'] ) ) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$msg]; ?></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
endif; ?>

<form class="search-form topmargin" action="" method="get">
<p class="search-box">
	<label class="hidden" for="category-search-input"><?php _e('Search Categories'); ?>:</label>
	<input type="text" class="search-input" id="category-search-input" name="s" value="<?php _admin_search_query(); ?>" />
	<input type="submit" value="<?php _e( 'Search Categories' ); ?>" class="button" />
</p>
</form>
<br class="clear" />

<div id="col-container">

<div id="col-right">
<div class="col-wrap">
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
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => ceil(wp_count_terms('category') / $catsperpage),
	'current' => $pagenum
));

if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft actions">
<select name="action">
<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
<option value="delete"><?php _e('Delete'); ?></option>
</select>
<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
<?php wp_nonce_field('bulk-categories'); ?>
</div>

<br class="clear" />
</div>

<div class="clear"></div>

<table class="widefat fixed" cellspacing="0">
	<thead>
	<tr>
<?php print_column_headers('categories'); ?>
	</tr>
	</thead>

	<tfoot>
	<tr>
<?php print_column_headers('categories', false); ?>
	</tr>
	</tfoot>

	<tbody id="the-list" class="list:cat">
<?php
cat_rows(0, 0, 0, $pagenum, $catsperpage);
?>
	</tbody>
</table>

<div class="tablenav">
<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft actions">
<select name="action2">
<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
<option value="delete"><?php _e('Delete'); ?></option>
</select>
<input type="submit" value="<?php _e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
<?php wp_nonce_field('bulk-categories'); ?>
</div>

<br class="clear" />
</div>

</form>

<div class="form-wrap">
<p><?php printf(__('<strong>Note:</strong><br />Deleting a category does not delete the posts in that category. Instead, posts that were only assigned to the deleted category are set to the category <strong>%s</strong>.'), apply_filters('the_category', get_catname(get_option('default_category')))) ?></p>
<p><?php printf(__('Categories can be selectively converted to tags using the <a href="%s">category to tag converter</a>.'), 'admin.php?import=wp-cat2tag') ?></p>
</div>

</div>
</div><!-- /col-right -->

<div id="col-left">
<div class="col-wrap">

<?php if ( current_user_can('manage_categories') ) { ?>
<?php $category = (object) array(); $category->parent = 0; do_action('add_category_form_pre', $category); ?>

<div class="form-wrap">
<h3><?php _e('Add Category'); ?></h3>
<div id="ajax-response"></div>
<form name="addcat" id="addcat" method="post" action="categories.php" class="add:the-list: validate">
<input type="hidden" name="action" value="addcat" />
<?php wp_original_referer_field(true, 'previous'); wp_nonce_field('add-category'); ?>

<div class="form-field form-required">
	<label for="cat_name"><?php _e('Category Name') ?></label>
	<input name="cat_name" id="cat_name" type="text" value="" size="40" aria-required="true" />
    <p><?php _e('The name is used to identify the category almost everywhere, for example under the post or in the category widget.'); ?></p>
</div>

<div class="form-field">
	<label for="category_nicename"><?php _e('Category Slug') ?></label>
	<input name="category_nicename" id="category_nicename" type="text" value="" size="40" />
    <p><?php _e('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.'); ?></p>
</div>

<div class="form-field">
	<label for="category_parent"><?php _e('Category Parent') ?></label>
	<?php wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'category_parent', 'orderby' => 'name', 'selected' => $category->parent, 'hierarchical' => true, 'show_option_none' => __('None'))); ?>
    <p><?php _e('Categories, unlike tags, can have a hierarchy. You might have a Jazz category, and under that have children categories for Bebop and Big Band. Totally optional.'); ?></p>
</div>

<div class="form-field">
	<label for="category_description"><?php _e('Description') ?></label>
	<textarea name="category_description" id="category_description" rows="5" cols="40"></textarea>
    <p><?php _e('The description is not prominent by default, however some themes may show it.'); ?></p>
</div>

<p class="submit"><input type="submit" class="button" name="submit" value="<?php _e('Add Category'); ?>" /></p>
<?php do_action('edit_category_form', $category); ?>
</form></div>

<?php } ?>

</div>
</div><!-- /col-left -->

</div><!-- /col-container -->
</div><!-- /wrap -->

<script type="text/javascript">
/* <![CDATA[ */
(function($){
	$(document).ready(function(){
		$('#doaction, #doaction2').click(function(){
			if ( $('select[name^="action"]').val() == 'delete' ) {
				var m = '<?php echo js_escape(__("You are about to delete the selected categories.\n  'Cancel' to stop, 'OK' to delete.")); ?>';
				return showNotice.warn(m);
			}
		});
	});
})(jQuery);
/* ]]> */
</script>

<?php
inline_edit_term_row('categories');

break;
}

include('admin-footer.php');

?>
