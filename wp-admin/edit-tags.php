<?php
/**
 * Edit Tags Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

if ( ! isset( $_GET['taxonomy'] ) )
	$taxonomy = 'post_tag';
elseif ( in_array( $_GET['taxonomy'], get_taxonomies() ) )
	$taxonomy = sanitize_key( $_GET['taxonomy'] );
else
	wp_die( __( 'Invalid taxonomy' ) );

$_GET['taxonomy'] = $taxonomy;

$tax = get_taxonomy( $taxonomy );

if ( ! $tax )
	wp_die( __( 'Invalid taxonomy' ) );

if ( ! current_user_can( $tax->cap->manage_terms ) )
	wp_die( __( 'Cheatin&#8217; uh?' ) );

$wp_list_table = _get_list_table('WP_Terms_List_Table');
$pagenum = $wp_list_table->get_pagenum();

$title = $tax->labels->name;

if ( 'post' != $post_type ) {
	$parent_file = "edit.php?post_type=$post_type";
	$submenu_file = "edit-tags.php?taxonomy=$taxonomy&amp;post_type=$post_type";
} else if ( 'link_category' == $tax->name ) {
	$parent_file = 'link-manager.php';
	$submenu_file = 'edit-tags.php?taxonomy=link_category';
} else {
	$parent_file = 'edit.php';
	$submenu_file = "edit-tags.php?taxonomy=$taxonomy";
}

add_screen_option( 'per_page', array('label' => $title, 'default' => 20, 'option' => 'edit_' . $tax->name . '_per_page') );

switch ( $wp_list_table->current_action() ) {

case 'add-tag':

	check_admin_referer( 'add-tag', '_wpnonce_add-tag' );

	if ( !current_user_can( $tax->cap->edit_terms ) )
		wp_die( __( 'Cheatin&#8217; uh?' ) );

	$ret = wp_insert_term( $_POST['tag-name'], $taxonomy, $_POST );
	$location = 'edit-tags.php?taxonomy=' . $taxonomy;
	if ( 'post' != $post_type )
		$location .= '&post_type=' . $post_type;

	if ( $referer = wp_get_original_referer() ) {
		if ( false !== strpos( $referer, 'edit-tags.php' ) )
			$location = $referer;
	}

	if ( $ret && !is_wp_error( $ret ) )
		$location = add_query_arg( 'message', 1, $location );
	else
		$location = add_query_arg( 'message', 4, $location );
	wp_redirect( $location );
	exit;
break;

case 'delete':
	$location = 'edit-tags.php?taxonomy=' . $taxonomy;
	if ( 'post' != $post_type )
		$location .= '&post_type=' . $post_type;
	if ( $referer = wp_get_referer() ) {
		if ( false !== strpos( $referer, 'edit-tags.php' ) )
			$location = $referer;
	}

	if ( !isset( $_REQUEST['tag_ID'] ) ) {
		wp_redirect( $location );
		exit;
	}

	$tag_ID = (int) $_REQUEST['tag_ID'];
	check_admin_referer( 'delete-tag_' .  $tag_ID );

	if ( !current_user_can( $tax->cap->delete_terms ) )
		wp_die( __( 'Cheatin&#8217; uh?' ) );

	wp_delete_term( $tag_ID, $taxonomy );

	$location = add_query_arg( 'message', 2, $location );
	wp_redirect( $location );
	exit;

break;

case 'bulk-delete':
	check_admin_referer( 'bulk-tags' );

	if ( !current_user_can( $tax->cap->delete_terms ) )
		wp_die( __( 'Cheatin&#8217; uh?' ) );

	$tags = (array) $_REQUEST['delete_tags'];
	foreach ( $tags as $tag_ID ) {
		wp_delete_term( $tag_ID, $taxonomy );
	}

	$location = 'edit-tags.php?taxonomy=' . $taxonomy;
	if ( 'post' != $post_type )
		$location .= '&post_type=' . $post_type;
	if ( $referer = wp_get_referer() ) {
		if ( false !== strpos( $referer, 'edit-tags.php' ) )
			$location = $referer;
	}

	$location = add_query_arg( 'message', 6, $location );
	wp_redirect( $location );
	exit;

break;

case 'edit':
	$title = $tax->labels->edit_item;

	$tag_ID = (int) $_REQUEST['tag_ID'];

	$tag = get_term( $tag_ID, $taxonomy, OBJECT, 'edit' );
	if ( ! $tag )
		wp_die( __( 'You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?' ) );
	require_once ( 'admin-header.php' );
	include( './edit-tag-form.php' );

break;

case 'editedtag':
	$tag_ID = (int) $_POST['tag_ID'];
	check_admin_referer( 'update-tag_' . $tag_ID );

	if ( !current_user_can( $tax->cap->edit_terms ) )
		wp_die( __( 'Cheatin&#8217; uh?' ) );

	$tag = get_term( $tag_ID, $taxonomy );
	if ( ! $tag )
		wp_die( __( 'You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?' ) );

	$ret = wp_update_term( $tag_ID, $taxonomy, $_POST );

	$location = 'edit-tags.php?taxonomy=' . $taxonomy;
	if ( 'post' != $post_type )
		$location .= '&post_type=' . $post_type;

	if ( $referer = wp_get_original_referer() ) {
		if ( false !== strpos( $referer, 'edit-tags.php' ) )
			$location = $referer;
	}

	if ( $ret && !is_wp_error( $ret ) )
		$location = add_query_arg( 'message', 3, $location );
	else
		$location = add_query_arg( 'message', 5, $location );

	wp_redirect( $location );
	exit;
break;

default:
if ( ! empty($_REQUEST['_wp_http_referer']) ) {
	$location = remove_query_arg( array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI']) );

	if ( ! empty( $_REQUEST['paged'] ) )
		$location = add_query_arg( 'paged', (int) $_REQUEST['paged'] );

	wp_redirect( $location );
	exit;
}

$wp_list_table->prepare_items();
$total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );

if ( $pagenum > $total_pages && $total_pages > 0 ) {
	wp_redirect( add_query_arg( 'paged', $total_pages ) );
	exit;
}

wp_enqueue_script('admin-tags');
if ( current_user_can($tax->cap->edit_terms) )
	wp_enqueue_script('inline-edit-tax');

if ( 'category' == $taxonomy || 'link_category' == $taxonomy || 'post_tag' == $taxonomy  ) {
	$help ='';
	if ( 'category' == $taxonomy )
		$help = '<p>' . sprintf(__( 'You can use categories to define sections of your site and group related posts. The default category is &#8220;Uncategorized&#8221; until you change it in your <a href="%s">writing settings</a>.' ) , 'options-writing.php' ) . '</p>';
	elseif ( 'link_category' == $taxonomy )
		$help = '<p>' . __( 'You can create groups of links by using link categories. Link category names must be unique and link categories are separate from the categories you use for posts.' ) . '</p>';
	else
		$help = '<p>' . __( 'You can assign keywords to your posts using Tags. Unlike categories, tags have no hierarchy, meaning there&#8217;s no relationship from one tag to another.' ) . '</p>';

	if ( 'link_category' == $taxonomy )
		$help .= '<p>' . __( 'You can delete link categories in the Bulk Action pulldown, but that action does not delete the links within the category. Instead, it moves them to the default link category.' ) . '</p>';
	else
		$help .='<p>' . __( 'What&#8217;s the difference between categories and tags? Normally, tags are ad-hoc keywords that identify important information in your post (names, subjects, etc) that may or may not recur in other posts, while categories are pre-determined sections. If you think of your site like a book, the categories are like the Table of Contents and the tags are like the terms in the index.' ) . '</p>';

	if ( 'category' == $taxonomy )
		$help .= '<p>' . __( 'When adding a new category on this screen, you&#8217;ll fill in the following fields:' ) . '</p>';
	elseif ( 'post_tag' == $taxonomy )
		$help .= '<p>' . __( 'When adding a new tag on this screen, you&#8217;ll fill in the following fields:' ) . '</p>';

	if ( 'category' == $taxonomy || 'post_tag' == $taxonomy  )

		$help .= '<ul>' .
		'<li>' . __( '<strong>Name</strong> - The name is how it appears on your site.' ) . '</li>';

	if ( ! global_terms_enabled()  )
	if ( 'category' == $taxonomy || 'post_tag' == $taxonomy  )
		$help .= '<li>' . __( '<strong>Slug</strong> - The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.' ) . '</li>';

	if ( 'category' == $taxonomy )
		$help .= '<li>' . __( '<strong>Parent</strong> - Categories, unlike tags, can have a hierarchy. You might have a Jazz category, and under that have children categories for Bebop and Big Band. Totally optional. To create a subcategory, just choose another category from the Parent dropdown.' ) . '</li>';

	if ( 'category' == $taxonomy || 'post_tag' == $taxonomy  )
		$help .= '<li>' . __( '<strong>Description</strong> - The description is not prominent by default; however, some themes may display it.' ) . '</li>' .
		'</ul>' .
		'<p>' . __( 'You can change the display of this screen using the Screen Options tab to set how many items are displayed per screen and to display/hide columns in the table.' ) . '</p>' .
		'<p><strong>' . __( 'For more information:' ) . '</strong></p>';

	if ( 'category' == $taxonomy )
		$help .= '<p>' . __( '<a href="http://codex.wordpress.org/Posts_Categories_Screen" target="_blank">Documentation on Categories</a>' ) . '</p>';
	elseif ( 'link_category' == $taxonomy )
		$help .= '<p>' . __( '<a href="http://codex.wordpress.org/Links_Link_Categories_Screen" target="_blank">Documentation on Link Categories</a>' ) . '</p>';
	else
		$help .= '<p>' . __( '<a href="http://codex.wordpress.org/Posts_Tags_Screen" target="_blank">Documentation on Tags</a>' ) . '</p>';

	$help .= '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>';

	add_contextual_help($current_screen, $help);
	unset($help);
}

require_once ('admin-header.php');

if ( !current_user_can($tax->cap->edit_terms) )
	wp_die( __('You are not allowed to edit this item.') );

$messages[1] = __('Item added.');
$messages[2] = __('Item deleted.');
$messages[3] = __('Item updated.');
$messages[4] = __('Item not added.');
$messages[5] = __('Item not updated.');
$messages[6] = __('Items deleted.');

?>

<div class="wrap nosubsub">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title );
if ( !empty($_REQUEST['s']) )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', esc_html( stripslashes($_REQUEST['s']) ) ); ?>
</h2>

<?php if ( isset($_REQUEST['message']) && ( $msg = (int) $_REQUEST['message'] ) ) : ?>
<div id="message" class="updated"><p><?php echo $messages[$msg]; ?></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
endif; ?>
<div id="ajax-response"></div>

<form class="search-form" action="" method="get">
<input type="hidden" name="taxonomy" value="<?php echo esc_attr($taxonomy); ?>" />
<input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>" />

<?php $wp_list_table->search_box( $tax->labels->search_items, 'tag' ); ?>

</form>
<br class="clear" />

<div id="col-container">

<div id="col-right">
<div class="col-wrap">
<form id="posts-filter" action="" method="post">
<input type="hidden" name="taxonomy" value="<?php echo esc_attr($taxonomy); ?>" />
<input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>" />

<?php $wp_list_table->display(); ?>

<br class="clear" />
</form>

<?php if ( 'category' == $taxonomy ) : ?>
<div class="form-wrap">
<p><?php printf(__('<strong>Note:</strong><br />Deleting a category does not delete the posts in that category. Instead, posts that were only assigned to the deleted category are set to the category <strong>%s</strong>.'), apply_filters('the_category', get_cat_name(get_option('default_category')))) ?></p>
<?php if ( current_user_can( 'import' ) ) : ?>
<p><?php printf(__('Categories can be selectively converted to tags using the <a href="%s">category to tag converter</a>.'), 'import.php') ?></p>
<?php endif; ?>
</div>
<?php elseif ( 'post_tag' == $taxonomy && current_user_can( 'import' ) ) : ?>
<div class="form-wrap">
<p><?php printf(__('Tags can be selectively converted to categories using the <a href="%s">tag to category converter</a>'), 'import.php') ;?>.</p>
</div>
<?php endif;
do_action('after-' . $taxonomy . '-table', $taxonomy);
?>

</div>
</div><!-- /col-right -->

<div id="col-left">
<div class="col-wrap">

<?php

if ( !is_null( $tax->labels->popular_items ) ) {
	if ( current_user_can( $tax->cap->edit_terms ) )
		$tag_cloud = wp_tag_cloud( array( 'taxonomy' => $taxonomy, 'echo' => false, 'link' => 'edit' ) );
	else
		$tag_cloud = wp_tag_cloud( array( 'taxonomy' => $taxonomy, 'echo' => false ) );

	if ( $tag_cloud ) :
	?>
<div class="tagcloud">
<h3><?php echo $tax->labels->popular_items; ?></h3>
<?php echo $tag_cloud; unset( $tag_cloud ); ?>
</div>
<?php
endif;
}

if ( current_user_can($tax->cap->edit_terms) ) {
	// Back compat hooks. Deprecated in preference to {$taxonomy}_pre_add_form
	if ( 'category' == $taxonomy )
		do_action('add_category_form_pre', (object)array('parent' => 0) );
	elseif ( 'link_category' == $taxonomy )
		do_action('add_link_category_form_pre', (object)array('parent' => 0) );
	else
		do_action('add_tag_form_pre', $taxonomy);

	do_action($taxonomy . '_pre_add_form', $taxonomy);
?>

<div class="form-wrap">
<h3><?php echo $tax->labels->add_new_item; ?></h3>
<form id="addtag" method="post" action="edit-tags.php" class="validate">
<input type="hidden" name="action" value="add-tag" />
<input type="hidden" name="screen" value="<?php echo esc_attr($current_screen->id); ?>" />
<input type="hidden" name="taxonomy" value="<?php echo esc_attr($taxonomy); ?>" />
<input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>" />
<?php wp_nonce_field('add-tag', '_wpnonce_add-tag'); ?>

<div class="form-field form-required">
	<label for="tag-name"><?php _ex('Name', 'Taxonomy Name'); ?></label>
	<input name="tag-name" id="tag-name" type="text" value="" size="40" aria-required="true" />
	<p><?php _e('The name is how it appears on your site.'); ?></p>
</div>
<?php if ( ! global_terms_enabled() ) : ?>
<div class="form-field">
	<label for="tag-slug"><?php _ex('Slug', 'Taxonomy Slug'); ?></label>
	<input name="slug" id="tag-slug" type="text" value="" size="40" />
	<p><?php _e('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.'); ?></p>
</div>
<?php endif; // global_terms_enabled() ?>
<?php if ( is_taxonomy_hierarchical($taxonomy) ) : ?>
<div class="form-field">
	<label for="parent"><?php _ex('Parent', 'Taxonomy Parent'); ?></label>
	<?php wp_dropdown_categories(array('hide_empty' => 0, 'hide_if_empty' => false, 'taxonomy' => $taxonomy, 'name' => 'parent', 'orderby' => 'name', 'hierarchical' => true, 'show_option_none' => __('None'))); ?>
	<?php if ( 'category' == $taxonomy ) : // @todo: Generic text for hierarchical taxonomies ?>
		<p><?php _e('Categories, unlike tags, can have a hierarchy. You might have a Jazz category, and under that have children categories for Bebop and Big Band. Totally optional.'); ?></p>
	<?php endif; ?>
</div>
<?php endif; // is_taxonomy_hierarchical() ?>
<div class="form-field">
	<label for="tag-description"><?php _ex('Description', 'Taxonomy Description'); ?></label>
	<textarea name="description" id="tag-description" rows="5" cols="40"></textarea>
	<p><?php _e('The description is not prominent by default; however, some themes may show it.'); ?></p>
</div>

<?php
if ( ! is_taxonomy_hierarchical($taxonomy) )
	do_action('add_tag_form_fields', $taxonomy);
do_action($taxonomy . '_add_form_fields', $taxonomy);

submit_button( $tax->labels->add_new_item, 'button' );

// Back compat hooks. Deprecated in preference to {$taxonomy}_add_form
if ( 'category' == $taxonomy )
	do_action('edit_category_form', (object)array('parent' => 0) );
elseif ( 'link_category' == $taxonomy )
	do_action('edit_link_category_form', (object)array('parent' => 0) );
else
	do_action('add_tag_form', $taxonomy);

do_action($taxonomy . '_add_form', $taxonomy);
?>
</form></div>
<?php } ?>

</div>
</div><!-- /col-left -->

</div><!-- /col-container -->
</div><!-- /wrap -->

<?php $wp_list_table->inline_edit(); ?>

<?php
break;
}

include('./admin-footer.php');

?>
