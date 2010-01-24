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
		$cats = (array) $_GET['delete'];
		$default_cat_id = get_option('default_link_category');

		foreach( $cats as $cat_ID ) {
			$cat_ID = (int) $cat_ID;
			// Don't delete the default cats.
			if ( $cat_ID == $default_cat_id )
				wp_die( sprintf( __("Can&#8217;t delete the <strong>%s</strong> category: this is the default one"), get_term_field('name', $cat_ID, 'link_category') ) );

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

wp_enqueue_script('admin-categories');
if ( current_user_can('manage_categories') )
	wp_enqueue_script('inline-edit-tax');

require_once ('admin-header.php');

$messages[1] = __('Category added.');
$messages[2] = __('Category deleted.');
$messages[3] = __('Category updated.');
$messages[4] = __('Category not added.');
$messages[5] = __('Category not updated.');
$messages[6] = __('Categories deleted.'); ?>

<div class="wrap nosubsub">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title );
if ( isset($_GET['s']) && $_GET['s'] )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', esc_html( stripslashes($_GET['s']) ) ); ?>
</h2>

<?php if ( isset($_GET['message']) && ( $msg = (int) $_GET['message'] ) ) : ?>
<div id="message" class="updated"><p><?php echo $messages[$msg]; ?></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
endif; ?>

<form class="search-form" action="" method="get">
<p class="search-box">
	<label class="screen-reader-text" for="link-category-search-input"><?php _e( 'Search Categories' ); ?>:</label>
	<input type="text" id="link-category-search-input" name="s" value="<?php _admin_search_query(); ?>" />
	<input type="submit" value="<?php esc_attr_e( 'Search Categories' ); ?>" class="button" />
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
if ( ! isset( $catsperpage ) || $catsperpage < 0 )
	$catsperpage = 20;

$page_links = paginate_links( array(
	'base' => add_query_arg( 'pagenum', '%#%' ),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => ceil(wp_count_terms('link_category') / $catsperpage),
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
<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
<?php wp_nonce_field('bulk-link-categories'); ?>
</div>

<br class="clear" />
</div>

<div class="clear"></div>

<table class="widefat fixed" cellspacing="0">
	<thead>
	<tr>
<?php print_column_headers('edit-link-categories'); ?>
	</tr>
	</thead>

	<tfoot>
	<tr>
<?php print_column_headers('edit-link-categories', false); ?>
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
		$output .= link_cat_row($category);
	}
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

<div class="alignleft actions">
<select name="action2">
<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
<option value="delete"><?php _e('Delete'); ?></option>
</select>
<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
</div>

<br class="clear" />
</div>
<br class="clear" />
</form>

<div class="form-wrap">
<p><?php printf(__('<strong>Note:</strong><br />Deleting a category does not delete the links in that category. Instead, links that were only assigned to the deleted category are set to the category <strong>%s</strong>.'), get_term_field('name', get_option('default_link_category'), 'link_category')) ?></p>
</div>


</div>
</div><!-- /col-right -->

<div id="col-left">
<div class="col-wrap">

<?php if ( current_user_can('manage_categories') ) {
	$category = (object) array(); $category->parent = 0; do_action('add_link_category_form_pre', $category); ?>

<div class="form-wrap">
<h3><?php _e('Add Link Category'); ?></h3>
<div id="ajax-response"></div>
<form name="addcat" id="addcat" class="add:the-list: validate" method="post" action="link-category.php">
<input type="hidden" name="action" value="addcat" />
<?php wp_original_referer_field(true, 'previous'); wp_nonce_field('add-link-category'); ?>

<div class="form-field form-required">
	<label for="name"><?php _e('Link Category name') ?></label>
	<input name="name" id="name" type="text" value="" size="40" aria-required="true" />
</div>
<?php if ( !is_multisite() ) { ?>
<div class="form-field">
	<label for="slug"><?php _e('Link Category slug') ?></label>
	<input name="slug" id="slug" type="text" value="" size="40" />
	<p><?php _e('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.'); ?></p>
</div>
<?php } ?>
<div class="form-field">
	<label for="description"><?php _e('Description (optional)') ?></label>
	<textarea name="description" id="description" rows="5" cols="40"></textarea>
	<p><?php _e('The description is not prominent by default; however, some themes may show it.'); ?></p>
</div>

<p class="submit"><input type="submit" class="button" name="submit" value="<?php esc_attr_e('Add Category'); ?>" /></p>
<?php do_action('edit_link_category_form', $category); ?>
</form>
</div>

<?php } ?>

</div>
</div><!-- /col-left -->

</div><!-- /col-container -->
</div><!-- /wrap -->

<?php inline_edit_term_row('edit-link-categories', 'link_category'); ?>
<?php include('admin-footer.php'); ?>
