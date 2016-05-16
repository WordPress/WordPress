<?php
/**
 * Edit Tags Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! $taxnow )
	wp_die( __( 'Invalid taxonomy' ) );

$tax = get_taxonomy( $taxnow );

if ( ! $tax )
	wp_die( __( 'Invalid taxonomy' ) );

if ( ! in_array( $tax->name, get_taxonomies( array( 'show_ui' => true ) ) ) ) {
   wp_die( __( 'You are not allowed to manage these items.' ) );
}

if ( ! current_user_can( $tax->cap->manage_terms ) ) {
	wp_die(
		'<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
		'<p>' . __( 'You are not allowed to manage these items.' ) . '</p>',
		403
	);
}

/**
 * $post_type is set when the WP_Terms_List_Table instance is created
 *
 * @global string $post_type
 */
global $post_type;

$wp_list_table = _get_list_table('WP_Terms_List_Table');
$pagenum = $wp_list_table->get_pagenum();

$title = $tax->labels->name;

if ( 'post' != $post_type ) {
	$parent_file = ( 'attachment' == $post_type ) ? 'upload.php' : "edit.php?post_type=$post_type";
	$submenu_file = "edit-tags.php?taxonomy=$taxonomy&amp;post_type=$post_type";
} elseif ( 'link_category' == $tax->name ) {
	$parent_file = 'link-manager.php';
	$submenu_file = 'edit-tags.php?taxonomy=link_category';
} else {
	$parent_file = 'edit.php';
	$submenu_file = "edit-tags.php?taxonomy=$taxonomy";
}

add_screen_option( 'per_page', array( 'default' => 20, 'option' => 'edit_' . $tax->name . '_per_page' ) );

get_current_screen()->set_screen_reader_content( array(
	'heading_pagination' => $tax->labels->items_list_navigation,
	'heading_list'       => $tax->labels->items_list,
) );

$location = false;
$referer = wp_get_referer();

switch ( $wp_list_table->current_action() ) {

case 'add-tag':

	check_admin_referer( 'add-tag', '_wpnonce_add-tag' );

	if ( ! current_user_can( $tax->cap->edit_terms ) ) {
		wp_die(
			'<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
			'<p>' . __( 'You are not allowed to add this item.' ) . '</p>',
			403
		);
	}

	$ret = wp_insert_term( $_POST['tag-name'], $taxonomy, $_POST );
	$location = 'edit-tags.php?taxonomy=' . $taxonomy;
	if ( 'post' != $post_type )
		$location .= '&post_type=' . $post_type;

	if ( $referer && false !== strpos( $referer, 'edit-tags.php' ) ) {
		$location = $referer;
	}

	if ( $ret && !is_wp_error( $ret ) )
		$location = add_query_arg( 'message', 1, $location );
	else
		$location = add_query_arg( array( 'error' => true, 'message' => 4 ), $location );

	break;

case 'delete':
	$location = 'edit-tags.php?taxonomy=' . $taxonomy;
	if ( 'post' != $post_type )
		$location .= '&post_type=' . $post_type;

	if ( $referer && false !== strpos( $referer, 'edit-tags.php' ) ) {
		$location = $referer;
	}

	if ( ! isset( $_REQUEST['tag_ID'] ) ) {
		break;
	}

	$tag_ID = (int) $_REQUEST['tag_ID'];
	check_admin_referer( 'delete-tag_' . $tag_ID );

	if ( ! current_user_can( $tax->cap->delete_terms ) ) {
		wp_die(
			'<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
			'<p>' . __( 'You are not allowed to delete this item.' ) . '</p>',
			403
		);
	}

	wp_delete_term( $tag_ID, $taxonomy );

	$location = add_query_arg( 'message', 2, $location );

	break;

case 'bulk-delete':
	check_admin_referer( 'bulk-tags' );

	if ( ! current_user_can( $tax->cap->delete_terms ) ) {
		wp_die(
			'<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
			'<p>' . __( 'You are not allowed to delete these items.' ) . '</p>',
			403
		);
	}

	$tags = (array) $_REQUEST['delete_tags'];
	foreach ( $tags as $tag_ID ) {
		wp_delete_term( $tag_ID, $taxonomy );
	}

	$location = 'edit-tags.php?taxonomy=' . $taxonomy;
	if ( 'post' != $post_type )
		$location .= '&post_type=' . $post_type;
	if ( $referer && false !== strpos( $referer, 'edit-tags.php' ) ) {
		$location = $referer;
	}

	$location = add_query_arg( 'message', 6, $location );

	break;

case 'edit':
	if ( ! isset( $_REQUEST['tag_ID'] ) ) {
		break;
	}

	$term_id = (int) $_REQUEST['tag_ID'];
	$term    = get_term( $term_id );

	if ( ! $term instanceof WP_Term ) {
		wp_die( __( 'You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?' ) );
	}

	wp_redirect( esc_url_raw( get_edit_term_link( $term_id, $taxonomy, $post_type ) ) );
	exit;

case 'editedtag':
	$tag_ID = (int) $_POST['tag_ID'];
	check_admin_referer( 'update-tag_' . $tag_ID );

	if ( ! current_user_can( $tax->cap->edit_terms ) ) {
		wp_die(
			'<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
			'<p>' . __( 'You are not allowed to edit this item.' ) . '</p>',
			403
		);
	}

	$tag = get_term( $tag_ID, $taxonomy );
	if ( ! $tag )
		wp_die( __( 'You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?' ) );

	$ret = wp_update_term( $tag_ID, $taxonomy, $_POST );

	$location = 'edit-tags.php?taxonomy=' . $taxonomy;
	if ( 'post' != $post_type )
		$location .= '&post_type=' . $post_type;

	if ( $referer && false !== strpos( $referer, 'edit-tags.php' ) ) {
		$location = $referer;
	}

	if ( $ret && !is_wp_error( $ret ) )
		$location = add_query_arg( 'message', 3, $location );
	else
		$location = add_query_arg( array( 'error' => true, 'message' => 5 ), $location );
	break;
}

if ( ! $location && ! empty( $_REQUEST['_wp_http_referer'] ) ) {
	$location = remove_query_arg( array('_wp_http_referer', '_wpnonce'), wp_unslash($_SERVER['REQUEST_URI']) );
}

if ( $location ) {
	if ( ! empty( $_REQUEST['paged'] ) ) {
		$location = add_query_arg( 'paged', (int) $_REQUEST['paged'], $location );
	}
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
		$help = '<p>' . __( 'You can create groups of links by using Link Categories. Link Category names must be unique and Link Categories are separate from the categories you use for posts.' ) . '</p>';
	else
		$help = '<p>' . __( 'You can assign keywords to your posts using <strong>tags</strong>. Unlike categories, tags have no hierarchy, meaning there&#8217;s no relationship from one tag to another.' ) . '</p>';

	if ( 'link_category' == $taxonomy )
		$help .= '<p>' . __( 'You can delete Link Categories in the Bulk Action pull-down, but that action does not delete the links within the category. Instead, it moves them to the default Link Category.' ) . '</p>';
	else
		$help .='<p>' . __( 'What&#8217;s the difference between categories and tags? Normally, tags are ad-hoc keywords that identify important information in your post (names, subjects, etc) that may or may not recur in other posts, while categories are pre-determined sections. If you think of your site like a book, the categories are like the Table of Contents and the tags are like the terms in the index.' ) . '</p>';

	get_current_screen()->add_help_tab( array(
		'id'      => 'overview',
		'title'   => __('Overview'),
		'content' => $help,
	) );

	if ( 'category' == $taxonomy || 'post_tag' == $taxonomy ) {
		if ( 'category' == $taxonomy )
			$help = '<p>' . __( 'When adding a new category on this screen, you&#8217;ll fill in the following fields:' ) . '</p>';
		else
			$help = '<p>' . __( 'When adding a new tag on this screen, you&#8217;ll fill in the following fields:' ) . '</p>';

		$help .= '<ul>' .
		'<li>' . __( '<strong>Name</strong> &mdash; The name is how it appears on your site.' ) . '</li>';

		if ( ! global_terms_enabled() )
			$help .= '<li>' . __( '<strong>Slug</strong> &mdash; The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.' ) . '</li>';

		if ( 'category' == $taxonomy )
			$help .= '<li>' . __( '<strong>Parent</strong> &mdash; Categories, unlike tags, can have a hierarchy. You might have a Jazz category, and under that have child categories for Bebop and Big Band. Totally optional. To create a subcategory, just choose another category from the Parent dropdown.' ) . '</li>';

		$help .= '<li>' . __( '<strong>Description</strong> &mdash; The description is not prominent by default; however, some themes may display it.' ) . '</li>' .
		'</ul>' .
		'<p>' . __( 'You can change the display of this screen using the Screen Options tab to set how many items are displayed per screen and to display/hide columns in the table.' ) . '</p>';

		get_current_screen()->add_help_tab( array(
			'id'      => 'adding-terms',
			'title'   => 'category' == $taxonomy ? __( 'Adding Categories' ) : __( 'Adding Tags' ),
			'content' => $help,
		) );
	}

	$help = '<p><strong>' . __( 'For more information:' ) . '</strong></p>';

	if ( 'category' == $taxonomy )
		$help .= '<p>' . __( '<a href="https://codex.wordpress.org/Posts_Categories_Screen" target="_blank">Documentation on Categories</a>' ) . '</p>';
	elseif ( 'link_category' == $taxonomy )
		$help .= '<p>' . __( '<a href="https://codex.wordpress.org/Links_Link_Categories_Screen" target="_blank">Documentation on Link Categories</a>' ) . '</p>';
	else
		$help .= '<p>' . __( '<a href="https://codex.wordpress.org/Posts_Tags_Screen" target="_blank">Documentation on Tags</a>' ) . '</p>';

	$help .= '<p>' . __('<a href="https://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>';

	get_current_screen()->set_help_sidebar( $help );

	unset( $help );
}

require_once( ABSPATH . 'wp-admin/admin-header.php' );

if ( ! current_user_can( $tax->cap->edit_terms ) ) {
	wp_die(
		'<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
		'<p>' . __( 'You are not allowed to edit this item.' ) . '</p>',
		403
	);
}

/** Also used by the Edit Tag  form */
require_once( ABSPATH . 'wp-admin/includes/edit-tag-messages.php' );

$class = ( isset( $_REQUEST['error'] ) ) ? 'error' : 'updated';

if ( is_plugin_active( 'wpcat2tag-importer/wpcat2tag-importer.php' ) ) {
	$import_link = admin_url( 'admin.php?import=wpcat2tag' );
} else {
	$import_link = admin_url( 'import.php' );
}

?>

<div class="wrap nosubsub">
<h1><?php echo esc_html( $title );
if ( isset( $_REQUEST['s'] ) && strlen( $_REQUEST['s'] ) ) {
	/* translators: %s: search keywords */
	printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', esc_html( wp_unslash( $_REQUEST['s'] ) ) );
}
?>
</h1>

<?php if ( $message ) : ?>
<div id="message" class="<?php echo $class; ?> notice is-dismissible"><p><?php echo $message; ?></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg( array( 'message', 'error' ), $_SERVER['REQUEST_URI'] );
endif; ?>
<div id="ajax-response"></div>

<form class="search-form wp-clearfix" method="get">
<input type="hidden" name="taxonomy" value="<?php echo esc_attr($taxonomy); ?>" />
<input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>" />

<?php $wp_list_table->search_box( $tax->labels->search_items, 'tag' ); ?>

</form>

<div id="col-container" class="wp-clearfix">

<div id="col-left">
<div class="col-wrap">

<?php

if ( current_user_can($tax->cap->edit_terms) ) {
	if ( 'category' == $taxonomy ) {
		/**
 		 * Fires before the Add Category form.
		 *
		 * @since 2.1.0
		 * @deprecated 3.0.0 Use {$taxonomy}_pre_add_form instead.
		 *
		 * @param object $arg Optional arguments cast to an object.
		 */
		do_action( 'add_category_form_pre', (object) array( 'parent' => 0 ) );
	} elseif ( 'link_category' == $taxonomy ) {
		/**
		 * Fires before the link category form.
		 *
		 * @since 2.3.0
		 * @deprecated 3.0.0 Use {$taxonomy}_pre_add_form instead.
		 *
		 * @param object $arg Optional arguments cast to an object.
		 */
		do_action( 'add_link_category_form_pre', (object) array( 'parent' => 0 ) );
	} else {
		/**
		 * Fires before the Add Tag form.
		 *
		 * @since 2.5.0
		 * @deprecated 3.0.0 Use {$taxonomy}_pre_add_form instead.
		 *
		 * @param string $taxonomy The taxonomy slug.
		 */
		do_action( 'add_tag_form_pre', $taxonomy );
	}

	/**
	 * Fires before the Add Term form for all taxonomies.
	 *
	 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $taxonomy The taxonomy slug.
	 */
	do_action( "{$taxonomy}_pre_add_form", $taxonomy );
?>

<div class="form-wrap">
<h2><?php echo $tax->labels->add_new_item; ?></h2>
<form id="addtag" method="post" action="edit-tags.php" class="validate"
<?php
/**
 * Fires at the beginning of the Add Tag form.
 *
 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
 *
 * @since 3.7.0
 */
do_action( "{$taxonomy}_term_new_form_tag" );
?>>
<input type="hidden" name="action" value="add-tag" />
<input type="hidden" name="screen" value="<?php echo esc_attr($current_screen->id); ?>" />
<input type="hidden" name="taxonomy" value="<?php echo esc_attr($taxonomy); ?>" />
<input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>" />
<?php wp_nonce_field('add-tag', '_wpnonce_add-tag'); ?>

<div class="form-field form-required term-name-wrap">
	<label for="tag-name"><?php _ex( 'Name', 'term name' ); ?></label>
	<input name="tag-name" id="tag-name" type="text" value="" size="40" aria-required="true" />
	<p><?php _e('The name is how it appears on your site.'); ?></p>
</div>
<?php if ( ! global_terms_enabled() ) : ?>
<div class="form-field term-slug-wrap">
	<label for="tag-slug"><?php _e( 'Slug' ); ?></label>
	<input name="slug" id="tag-slug" type="text" value="" size="40" />
	<p><?php _e('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.'); ?></p>
</div>
<?php endif; // global_terms_enabled() ?>
<?php if ( is_taxonomy_hierarchical($taxonomy) ) : ?>
<div class="form-field term-parent-wrap">
	<label for="parent"><?php _ex( 'Parent', 'term parent' ); ?></label>
	<?php
	$dropdown_args = array(
		'hide_empty'       => 0,
		'hide_if_empty'    => false,
		'taxonomy'         => $taxonomy,
		'name'             => 'parent',
		'orderby'          => 'name',
		'hierarchical'     => true,
		'show_option_none' => __( 'None' ),
	);

	/**
	 * Filter the taxonomy parent drop-down on the Edit Term page.
	 *
	 * @since 3.7.0
	 * @since 4.2.0 Added `$context` parameter.
	 *
	 * @param array  $dropdown_args {
	 *     An array of taxonomy parent drop-down arguments.
	 *
	 *     @type int|bool $hide_empty       Whether to hide terms not attached to any posts. Default 0|false.
	 *     @type bool     $hide_if_empty    Whether to hide the drop-down if no terms exist. Default false.
	 *     @type string   $taxonomy         The taxonomy slug.
	 *     @type string   $name             Value of the name attribute to use for the drop-down select element.
	 *                                      Default 'parent'.
	 *     @type string   $orderby          The field to order by. Default 'name'.
	 *     @type bool     $hierarchical     Whether the taxonomy is hierarchical. Default true.
	 *     @type string   $show_option_none Label to display if there are no terms. Default 'None'.
	 * }
	 * @param string $taxonomy The taxonomy slug.
	 * @param string $context  Filter context. Accepts 'new' or 'edit'.
	 */
	$dropdown_args = apply_filters( 'taxonomy_parent_dropdown_args', $dropdown_args, $taxonomy, 'new' );

	wp_dropdown_categories( $dropdown_args );
	?>
	<?php if ( 'category' == $taxonomy ) : // @todo: Generic text for hierarchical taxonomies ?>
		<p><?php _e('Categories, unlike tags, can have a hierarchy. You might have a Jazz category, and under that have children categories for Bebop and Big Band. Totally optional.'); ?></p>
	<?php endif; ?>
</div>
<?php endif; // is_taxonomy_hierarchical() ?>
<div class="form-field term-description-wrap">
	<label for="tag-description"><?php _e( 'Description' ); ?></label>
	<textarea name="description" id="tag-description" rows="5" cols="40"></textarea>
	<p><?php _e('The description is not prominent by default; however, some themes may show it.'); ?></p>
</div>

<?php
if ( ! is_taxonomy_hierarchical( $taxonomy ) ) {
	/**
	 * Fires after the Add Tag form fields for non-hierarchical taxonomies.
	 *
	 * @since 3.0.0
	 *
	 * @param string $taxonomy The taxonomy slug.
	 */
	do_action( 'add_tag_form_fields', $taxonomy );
}

/**
 * Fires after the Add Term form fields.
 *
 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
 *
 * @since 3.0.0
 *
 * @param string $taxonomy The taxonomy slug.
 */
do_action( "{$taxonomy}_add_form_fields", $taxonomy );

submit_button( $tax->labels->add_new_item );

if ( 'category' == $taxonomy ) {
	/**
	 * Fires at the end of the Edit Category form.
	 *
	 * @since 2.1.0
	 * @deprecated 3.0.0 Use {$taxonomy}_add_form instead.
	 *
	 * @param object $arg Optional arguments cast to an object.
	 */
	do_action( 'edit_category_form', (object) array( 'parent' => 0 ) );
} elseif ( 'link_category' == $taxonomy ) {
	/**
	 * Fires at the end of the Edit Link form.
	 *
	 * @since 2.3.0
	 * @deprecated 3.0.0 Use {$taxonomy}_add_form instead.
	 *
	 * @param object $arg Optional arguments cast to an object.
	 */
	do_action( 'edit_link_category_form', (object) array( 'parent' => 0 ) );
} else {
	/**
	 * Fires at the end of the Add Tag form.
	 *
	 * @since 2.7.0
	 * @deprecated 3.0.0 Use {$taxonomy}_add_form instead.
	 *
	 * @param string $taxonomy The taxonomy slug.
	 */
	do_action( 'add_tag_form', $taxonomy );
}

/**
 * Fires at the end of the Add Term form for all taxonomies.
 *
 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
 *
 * @since 3.0.0
 *
 * @param string $taxonomy The taxonomy slug.
 */
do_action( "{$taxonomy}_add_form", $taxonomy );
?>
</form></div>
<?php }

if ( ! is_null( $tax->labels->popular_items ) ) {
	if ( current_user_can( $tax->cap->edit_terms ) ) {
		$tag_cloud = wp_tag_cloud( array( 'taxonomy' => $taxonomy, 'post_type' => $post_type, 'echo' => false, 'link' => 'edit' ) );
	} else {
		$tag_cloud = wp_tag_cloud( array( 'taxonomy' => $taxonomy, 'echo' => false ) );
	}

	if ( $tag_cloud ) :
	?>
<div class="tagcloud">
<h2><?php echo $tax->labels->popular_items; ?></h2>
<?php echo $tag_cloud; unset( $tag_cloud ); ?>
</div>
<?php
	endif;
}

?>

</div>
</div><!-- /col-left -->

<div id="col-right">
<div class="col-wrap">
<form id="posts-filter" method="post">
<input type="hidden" name="taxonomy" value="<?php echo esc_attr( $taxonomy ); ?>" />
<input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>" />

<?php $wp_list_table->display(); ?>

</form>

<?php if ( 'category' == $taxonomy ) : ?>
<div class="form-wrap edit-term-notes">
<p>
	<?php
	echo '<strong>' . __( 'Note:' ) . '</strong><br />';
	printf(
		/* translators: %s: default category */
		__( 'Deleting a category does not delete the posts in that category. Instead, posts that were only assigned to the deleted category are set to the category %s.' ),
		/** This filter is documented in wp-includes/category-template.php */
		'<strong>' . apply_filters( 'the_category', get_cat_name( get_option( 'default_category') ) ) . '</strong>'
	);
	?>
</p>
<?php if ( current_user_can( 'import' ) ) : ?>
<p><?php printf( __( 'Categories can be selectively converted to tags using the <a href="%s">category to tag converter</a>.' ), esc_url( $import_link ) ) ?></p>
<?php endif; ?>
</div>
<?php elseif ( 'post_tag' == $taxonomy && current_user_can( 'import' ) ) : ?>
<div class="form-wrap edit-term-notes">
<p><?php printf( __( 'Tags can be selectively converted to categories using the <a href="%s">tag to category converter</a>.' ), esc_url( $import_link ) ) ;?></p>
</div>
<?php endif;

/**
 * Fires after the taxonomy list table.
 *
 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
 *
 * @since 3.0.0
 *
 * @param string $taxonomy The taxonomy name.
 */
do_action( "after-{$taxonomy}-table", $taxonomy );
?>

</div>
</div><!-- /col-right -->

</div><!-- /col-container -->
</div><!-- /wrap -->

<?php if ( ! wp_is_mobile() ) : ?>
<script type="text/javascript">
try{document.forms.addtag['tag-name'].focus();}catch(e){}
</script>
<?php
endif;

$wp_list_table->inline_edit();

include( ABSPATH . 'wp-admin/admin-footer.php' );
