<?php
require_once('admin.php');

$title = __('Link Categories');
$parent_file = 'edit.php';

wp_enqueue_script( 'admin-categories' );
require_once ('admin-header.php');

$messages[1] = __('Category added.');
$messages[2] = __('Category deleted.');
$messages[3] = __('Category updated.');
$messages[4] = __('Category not added.');
$messages[5] = __('Category not updated.');

if (isset($_GET['message'])) : ?>

<div id="message" class="updated fade"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php endif; ?>

<div class="wrap">

<form id="posts-filter" action="" method="get">
<?php if ( current_user_can('manage_categories') ) : ?>
	<h2><?php printf(__('Manage Link Categories (<a href="%s">add new</a>)'), '#addcat') ?> </h2>
<?php else : ?>
	<h2><?php _e('Manage Link Categories') ?> </h2>
<?php endif; ?>

<p id="post-search">
	<input type="text" id="post-search-input" name="s" value="<?php echo attribute_escape(stripslashes($_GET['s'])); ?>" />
	<input type="submit" value="<?php _e( 'Search Categories' ); ?>" />
</p>

<br style="clear:both;" />

<div class="tablenav">

<?php
$pagenum = absint( $_GET['pagenum'] );
if ( empty($pagenum) )
	$pagenum = 1;
if( !$catsperpage || $catsperpage < 0 )
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

<div style="float: left">
<input type="button" value="<?php _e('Delete'); ?>" name="deleteit" />
</div>

<br style="clear:both;" />
</div>
</form>

<br style="clear:both;" />

<table class="widefat">
	<thead>
	<tr>
        <th scope="col" style="text-align: center"><input type="checkbox" onclick="checkAll(document.getElementById('deletetags'));" /></th>
        <th scope="col"><?php _e('Name') ?></th>
        <th scope="col"><?php _e('Description') ?></th>
        <th scope="col" width="90" style="text-align: center"><?php _e('Links') ?></th>
	</tr>
	</thead>
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

<br style="clear:both;" />

<div class="tablenav">

<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>
<br style="clear:both;" />
</div>

</div>

<?php if ( current_user_can('manage_categories') ) : ?>
<div class="wrap">
<p><?php printf(__('<strong>Note:</strong><br />Deleting a category does not delete the links in that category. Instead, links that were only assigned to the deleted category are set to the category <strong>%s</strong>.'), get_term_field('name', get_option('default_link_category'), 'link_category')) ?></p>
</div>

<?php include('edit-link-category-form.php'); ?>

<?php endif; ?>

<?php include('admin-footer.php'); ?>
