<?php
/**
 * Template WordPress Administration API.
 *
 * A Big Mess. Also some neat functions that are nicely written.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.7
 *
 * Outputs the HTML for the hidden table rows used in Categories, Link Caregories and Tags quick edit.
 *
 * @param string $type "edit-tags", "categoried" or "edit-link-categories"
 * @param string $taxonomy The taxonomy of the row.
 * @return
 */
function inline_edit_term_row($type, $taxonomy) {

	$tax = get_taxonomy($taxonomy);
	if ( ! current_user_can( $tax->cap->edit_terms ) )
		return;

	$columns = get_column_headers($type);
	$hidden = array_intersect( array_keys( $columns ), array_filter( get_hidden_columns($type) ) );
	$col_count = count($columns) - count($hidden);
	?>

<form method="get" action=""><table style="display: none"><tbody id="inlineedit">
	<tr id="inline-edit" class="inline-edit-row" style="display: none"><td colspan="<?php echo $col_count; ?>">

		<fieldset><div class="inline-edit-col">
			<h4><?php _e( 'Quick Edit' ); ?></h4>

			<label>
				<span class="title"><?php _e( 'Name' ); ?></span>
				<span class="input-text-wrap"><input type="text" name="name" class="ptitle" value="" /></span>
			</label>
<?php if ( !is_multisite() ) { ?>
			<label>
				<span class="title"><?php _e( 'Slug' ); ?></span>
				<span class="input-text-wrap"><input type="text" name="slug" class="ptitle" value="" /></span>
			</label>
<?php } ?>

		</div></fieldset>

<?php

	$core_columns = array( 'cb' => true, 'description' => true, 'name' => true, 'slug' => true, 'posts' => true );

	foreach ( $columns as $column_name => $column_display_name ) {
		if ( isset( $core_columns[$column_name] ) )
			continue;
		do_action( 'quick_edit_custom_box', $column_name, $type, $taxonomy );
	}

?>

	<p class="inline-edit-save submit">
		<a accesskey="c" href="#inline-edit" title="<?php _e('Cancel'); ?>" class="cancel button-secondary alignleft"><?php _e('Cancel'); ?></a>
		<?php $update_text = sprintf( __('Update %s'), $tax->singular_label ); ?>
		<a accesskey="s" href="#inline-edit" title="<?php echo esc_attr( $update_text ); ?>" class="save button-primary alignright"><?php echo $update_text; ?></a>
		<img class="waiting" style="display:none;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
		<span class="error" style="display:none;"></span>
		<?php wp_nonce_field( 'taxinlineeditnonce', '_inline_edit', false ); ?>
		<input type="hidden" name="taxonomy" value="<?php echo esc_attr( $tax->name ); ?>" />
		<br class="clear" />
	</p>
	</td></tr>
	</tbody></table></form>
<?php
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $category
 * @param unknown_type $name_override
 * @return unknown
 */
function link_cat_row( $category, $name_override = false ) {
	static $row_class = '';

	if ( !$category = get_term( $category, 'link_category', OBJECT, 'display' ) )
		return false;
	if ( is_wp_error( $category ) )
		return $category;

	$default_cat_id = (int) get_option( 'default_link_category' );
	$name = ( $name_override ? $name_override : $category->name );
	$edit_link = "link-category.php?action=edit&amp;cat_ID=$category->term_id";
	if ( current_user_can( 'manage_categories' ) ) {
		$edit = "<a class='row-title' href='$edit_link' title='" . esc_attr(sprintf(__('Edit &#8220;%s&#8221;'), $category->name)) . "'>$name</a><br />";
		$actions = array();
		$actions['edit'] = '<a href="' . $edit_link . '">' . __('Edit') . '</a>';
		$actions['inline hide-if-no-js'] = '<a href="#" class="editinline">' . __('Quick&nbsp;Edit') . '</a>';
		if ( $default_cat_id != $category->term_id )
			$actions['delete'] = "<a class='delete:the-list:link-cat-$category->term_id submitdelete' href='" . wp_nonce_url("link-category.php?action=delete&amp;cat_ID=$category->term_id", 'delete-link-category_' . $category->term_id) . "'>" . __('Delete') . "</a>";
		$actions = apply_filters('link_cat_row_actions', $actions, $category);
		$action_count = count($actions);
		$i = 0;
		$edit .= '<div class="row-actions">';
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			$edit .= "<span class='$action'>$link$sep</span>";
		}
		$edit .= '</div>';
	} else {
		$edit = $name;
	}

	$row_class = 'alternate' == $row_class ? '' : 'alternate';
	$qe_data = get_term_to_edit($category->term_id, 'link_category');

	$category->count = number_format_i18n( $category->count );
	$count = ( $category->count > 0 ) ? "<a href='link-manager.php?cat_id=$category->term_id'>$category->count</a>" : $category->count;
	$output = "<tr id='link-cat-$category->term_id' class='iedit $row_class'>";
	$columns = get_column_headers('edit-link-categories');
	$hidden = get_hidden_columns('edit-link-categories');
	foreach ( $columns as $column_name => $column_display_name ) {
		$class = "class=\"$column_name column-$column_name\"";

		$style = '';
		if ( in_array($column_name, $hidden) )
			$style = ' style="display:none;"';

		$attributes = "$class$style";

		switch ($column_name) {
			case 'cb':
				$output .= "<th scope='row' class='check-column'>";
				if ( absint( get_option( 'default_link_category' ) ) != $category->term_id ) {
					$output .= "<input type='checkbox' name='delete[]' value='$category->term_id' />";
				} else {
					$output .= "&nbsp;";
				}
				$output .= "</th>";
				break;
			case 'name':
				$output .= "<td $attributes>$edit";
				$output .= '<div class="hidden" id="inline_' . $qe_data->term_id . '">';
				$output .= '<div class="name">' . $qe_data->name . '</div>';
				$output .= '<div class="slug">' . apply_filters('editable_slug', $qe_data->slug) . '</div>';
				$output .= '<div class="cat_parent">' . $qe_data->parent . '</div></div></td>';
				break;
			case 'description':
				$output .= "<td $attributes>$category->description</td>";
				break;
			case 'slug':
				$output .= "<td $attributes>" . apply_filters('editable_slug', $category->slug) . "</td>";
				break;
			case 'links':
				$attributes = 'class="links column-links num"' . $style;
				$output .= "<td $attributes>$count</td>";
				break;
			default:
				$output .= "<td $attributes>";
				$output .= apply_filters('manage_link_categories_custom_column', '', $column_name, $category->term_id);
				$output .= "</td>";
		}
	}
	$output .= '</tr>';

	return $output;
}

//
// Category Checklists
//

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 */
class Walker_Category_Checklist extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

	function start_lvl(&$output, $depth, $args) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	function end_lvl(&$output, $depth, $args) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function start_el(&$output, $category, $depth, $args) {
		extract($args);
		if ( empty($taxonomy) )
			$taxonomy = 'category';

		if ( $taxonomy == 'category' )
			$name = 'post_category';
		else
			$name = 'tax_input['.$taxonomy.']';

		$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
		$output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" . '<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . esc_html( apply_filters('the_category', $category->name )) . '</label>';
	}

	function end_el(&$output, $category, $depth, $args) {
		$output .= "</li>\n";
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $post_id
 * @param unknown_type $descendants_and_self
 * @param unknown_type $selected_cats
 * @param unknown_type $popular_cats
 */
function wp_category_checklist( $post_id = 0, $descendants_and_self = 0, $selected_cats = false, $popular_cats = false, $walker = null, $checked_ontop = true ) {
	wp_terms_checklist($post_id,
	 	array(
			'taxonomy' => 'category',
			'descendants_and_self' => $descendants_and_self,
			'selected_cats' => $selected_cats,
			'popular_cats' => $popular_cats,
			'walker' => $walker,
			'checked_ontop' => $checked_ontop
  ));
}

/**
 * Taxonomy independent version of wp_category_checklist
 *
 * @param int $post_id
 * @param array $args
 */
function wp_terms_checklist($post_id = 0, $args = array()) {
 	$defaults = array(
		'descendants_and_self' => 0,
		'selected_cats' => false,
		'popular_cats' => false,
		'walker' => null,
		'taxonomy' => 'category',
		'checked_ontop' => true
	);
	extract( wp_parse_args($args, $defaults), EXTR_SKIP );

	if ( empty($walker) || !is_a($walker, 'Walker') )
		$walker = new Walker_Category_Checklist;

	$descendants_and_self = (int) $descendants_and_self;

	$args = array('taxonomy' => $taxonomy);

	$tax = get_taxonomy($taxonomy);
	$args['disabled'] = !current_user_can($tax->cap->assign_terms);

	if ( is_array( $selected_cats ) )
		$args['selected_cats'] = $selected_cats;
	elseif ( $post_id )
		$args['selected_cats'] = wp_get_object_terms($post_id, $taxonomy, array_merge($args, array('fields' => 'ids')));
	else
		$args['selected_cats'] = array();

	if ( is_array( $popular_cats ) )
		$args['popular_cats'] = $popular_cats;
	else
		$args['popular_cats'] = get_terms( $taxonomy, array( 'fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );

	if ( $descendants_and_self ) {
		$categories = (array) get_terms($taxonomy, array( 'child_of' => $descendants_and_self, 'hierarchical' => 0, 'hide_empty' => 0 ) );
		$self = get_term( $descendants_and_self, $taxonomy );
		array_unshift( $categories, $self );
	} else {
		$categories = (array) get_terms($taxonomy, array('get' => 'all'));
	}

	if ( $checked_ontop ) {
		// Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
		$checked_categories = array();
		$keys = array_keys( $categories );

		foreach( $keys as $k ) {
			if ( in_array( $categories[$k]->term_id, $args['selected_cats'] ) ) {
				$checked_categories[] = $categories[$k];
				unset( $categories[$k] );
			}
		}

		// Put checked cats on top
		echo call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args));
	}
	// Then the rest of them
	echo call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $taxonomy
 * @param unknown_type $default
 * @param unknown_type $number
 * @param unknown_type $echo
 * @return unknown
 */
function wp_popular_terms_checklist( $taxonomy, $default = 0, $number = 10, $echo = true ) {
	global $post_ID;

	if ( $post_ID )
		$checked_terms = wp_get_object_terms($post_ID, $taxonomy, array('fields'=>'ids'));
	else
		$checked_terms = array();

	$terms = get_terms( $taxonomy, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => $number, 'hierarchical' => false ) );

	$tax = get_taxonomy($taxonomy);
	if ( ! current_user_can($tax->cap->assign_terms) )
		$disabled = 'disabled="disabled"';
	else
		$disabled = '';

	$popular_ids = array();
	foreach ( (array) $terms as $term ) {
		$popular_ids[] = $term->term_id;
		if ( !$echo ) // hack for AJAX use
			continue;
		$id = "popular-$taxonomy-$term->term_id";
		$checked = in_array( $term->term_id, $checked_terms ) ? 'checked="checked"' : '';
		?>

		<li id="<?php echo $id; ?>" class="popular-category">
			<label class="selectit">
			<input id="in-<?php echo $id; ?>" type="checkbox" <?php echo $checked; ?> value="<?php echo (int) $term->term_id; ?>" <?php echo $disabled ?>/>
				<?php echo esc_html( apply_filters( 'the_category', $term->name ) ); ?>
			</label>
		</li>

		<?php
	}
	return $popular_ids;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $link_id
 */
function wp_link_category_checklist( $link_id = 0 ) {
	$default = 1;

	if ( $link_id ) {
		$checked_categories = wp_get_link_cats( $link_id );
		// No selected categories, strange
		if ( ! count( $checked_categories ) )
			$checked_categories[] = $default;
	} else {
		$checked_categories[] = $default;
	}

	$categories = get_terms( 'link_category', array( 'orderby' => 'name', 'hide_empty' => 0 ) );

	if ( empty( $categories ) )
		return;

	foreach ( $categories as $category ) {
		$cat_id = $category->term_id;
		$name = esc_html( apply_filters( 'the_category', $category->name ) );
		$checked = in_array( $cat_id, $checked_categories ) ? ' checked="checked"' : '';
		echo '<li id="link-category-', $cat_id, '"><label for="in-link-category-', $cat_id, '" class="selectit"><input value="', $cat_id, '" type="checkbox" name="link_category[]" id="in-link-category-', $cat_id, '"', $checked, '/> ', $name, "</label></li>";
	}
}

// Tag stuff

// Returns a single tag row (see tag_rows below)
// Note: this is also used in admin-ajax.php!
/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $tag
 * @param unknown_type $class
 * @return unknown
 */
function _tag_row( $tag, $level, $taxonomy = 'post_tag' ) {
		global $post_type;
		static $row_class = '';
		$row_class = ($row_class == '' ? ' class="alternate"' : '');

		$count = number_format_i18n( $tag->count );
		$tax = get_taxonomy($taxonomy);

		if ( 'post_tag' == $taxonomy ) {
			$tagsel = 'tag';
		} elseif ( 'category' == $taxonomy ) {
			$tagsel = 'category_name';
		} elseif ( ! empty($tax->query_var) ) {
			$tagsel = $tax->query_var;
		} else {
			$tagsel = $taxonomy;
		}

		$pad = str_repeat( '&#8212; ', max(0, $level) );
		$name = apply_filters( 'term_name', $pad . ' ' . $tag->name, $tag );
		$qe_data = get_term($tag->term_id, $taxonomy, object, 'edit');
		$edit_link = "edit-tags.php?action=edit&amp;taxonomy=$taxonomy&amp;post_type=$post_type&amp;tag_ID=$tag->term_id";

		$out = '';
		$out .= '<tr id="tag-' . $tag->term_id . '"' . $row_class . '>';


		$columns = get_column_headers('edit-tags');
		$hidden = get_hidden_columns('edit-tags');
		$default_term = get_option('default_' . $taxonomy);
		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class=\"$column_name column-$column_name\"";

			$style = '';
			if ( in_array($column_name, $hidden) )
				$style = ' style="display:none;"';

			$attributes = "$class$style";

			switch ($column_name) {
				case 'cb':
					if ( current_user_can($tax->cap->delete_terms) && $tag->term_id != $default_term )
						$out .= '<th scope="row" class="check-column"> <input type="checkbox" name="delete_tags[]" value="' . $tag->term_id . '" /></th>';
					else
						$out .= '<th scope="row" class="check-column">&nbsp;</th>';
					break;
				case 'name':
					$out .= '<td ' . $attributes . '><strong><a class="row-title" href="' . $edit_link . '" title="' . esc_attr(sprintf(__('Edit &#8220;%s&#8221;'), $name)) . '">' . $name . '</a></strong><br />';
					$actions = array();
					if ( current_user_can($tax->cap->edit_terms) ) {
						$actions['edit'] = '<a href="' . $edit_link . '">' . __('Edit') . '</a>';
						$actions['inline hide-if-no-js'] = '<a href="#" class="editinline">' . __('Quick&nbsp;Edit') . '</a>';
					}
					if ( current_user_can($tax->cap->delete_terms) && $tag->term_id != $default_term )
						$actions['delete'] = "<a class='delete-tag' href='" . wp_nonce_url("edit-tags.php?action=delete&amp;taxonomy=$taxonomy&amp;tag_ID=$tag->term_id", 'delete-tag_' . $tag->term_id) . "'>" . __('Delete') . "</a>";

					$actions = apply_filters('tag_row_actions', $actions, $tag);
					$actions = apply_filters("${taxonomy}_row_actions", $actions, $tag);

					$action_count = count($actions);
					$i = 0;
					$out .= '<div class="row-actions">';
					foreach ( $actions as $action => $link ) {
						++$i;
						( $i == $action_count ) ? $sep = '' : $sep = ' | ';
						$out .= "<span class='$action'>$link$sep</span>";
					}
					$out .= '</div>';
					$out .= '<div class="hidden" id="inline_' . $qe_data->term_id . '">';
					$out .= '<div class="name">' . $qe_data->name . '</div>';
					$out .= '<div class="slug">' . apply_filters('editable_slug', $qe_data->slug) . '</div>';
					$out .= '<div class="parent">' . $qe_data->parent . '</div></div></td>';
					break;
				case 'description':
					$out .= "<td $attributes>$tag->description</td>";
					break;
				case 'slug':
					$out .= "<td $attributes>" . apply_filters('editable_slug', $tag->slug) . "</td>";
					break;
				case 'posts':
					$attributes = 'class="posts column-posts num"' . $style;
					$out .= "<td $attributes><a href='edit.php?$tagsel=$tag->slug&amp;post_type=$post_type'>$count</a></td>";
					break;
				default:
					$out .= "<td $attributes>";
					$out .= apply_filters("manage_${taxonomy}_custom_column", '', $column_name, $tag->term_id);
					$out .= "</td>";
			}
		}

		$out .= "</tr>\n";

		return $out;
}

// Outputs appropriate rows for the Nth page of the Tag Management screen,
// assuming M tags displayed at a time on the page
// Returns the number of tags displayed
/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $page
 * @param unknown_type $pagesize
 * @param unknown_type $searchterms
 * @return unknown
 */
function tag_rows( $page = 1, $pagesize = 20, $searchterms = '', $taxonomy = 'post_tag' ) {

	// Get a page worth of tags
	$start = ($page - 1) * $pagesize;

	$args = array('offset' => $start, 'number' => $pagesize, 'hide_empty' => 0);

	if ( !empty( $searchterms ) )
		$args['search'] = $searchterms;

	// convert it to table rows
	$out = '';
	$count = 0;
	if ( is_taxonomy_hierarchical($taxonomy) ) {
		// We'll need the full set of terms then.
		$args['number'] = $args['offset'] = 0;

		$terms = get_terms( $taxonomy, $args );
		if ( !empty( $searchterms ) ) // Ignore children on searches.
			$children = array();
		else
			$children = _get_term_hierarchy($taxonomy);

		// Some funky recursion to get the job done(Paging & parents mainly) is contained within, Skip it for non-hierarchical taxonomies for performance sake
		$out .= _term_rows($taxonomy, $terms, $children, $page, $pagesize, $count);
	} else {
		$terms = get_terms( $taxonomy, $args );
		foreach( $terms as $term )
			$out .= _tag_row( $term, 0, $taxonomy );
		$count = $pagesize; // Only displaying a single page.
	}

	echo $out;
	return $count;
}

function _term_rows( $taxonomy, $terms, &$children, $page = 1, $per_page = 20, &$count, $parent = 0, $level = 0 ) {

	$start = ($page - 1) * $per_page;
	$end = $start + $per_page;

	$output = '';
	foreach ( $terms as $key => $term ) {

		if ( $count >= $end )
			break;

		if ( $term->parent != $parent && empty($_GET['s']) )
			continue;

		// If the page starts in a subtree, print the parents.
		if ( $count == $start && $term->parent > 0 && empty($_GET['s']) ) {
			$my_parents = $parent_ids = array();
			$p = $term->parent;
			while ( $p ) {
				$my_parent = get_term( $p, $taxonomy );
				$my_parents[] = $my_parent;
				$p = $my_parent->parent;
				if ( in_array($p, $parent_ids) ) // Prevent parent loops.
					break;
				$parent_ids[] = $p;
			}
			unset($parent_ids);

			$num_parents = count($my_parents);
			while ( $my_parent = array_pop($my_parents) ) {
				$output .=  "\t" . _tag_row( $my_parent, $level - $num_parents, $taxonomy );
				$num_parents--;
			}
		}

		if ( $count >= $start )
			$output .= "\t" . _tag_row( $term, $level, $taxonomy );

		++$count;

		unset($terms[$key]);

		if ( isset($children[$term->term_id]) && empty($_GET['s']) )
			$output .= _term_rows( $taxonomy, $terms, $children, $page, $per_page, $count, $term->term_id, $level + 1 );
	}

	return $output;
}

// define the columns to display, the syntax is 'internal name' => 'display name'
/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function wp_manage_posts_columns( $screen = '') {
	if ( empty($screen) )
		$post_type = 'post';
	else
		$post_type = $screen->post_type;

	$posts_columns = array();
	$posts_columns['cb'] = '<input type="checkbox" />';
	/* translators: manage posts column name */
	$posts_columns['title'] = _x('Title', 'column name');
	$posts_columns['author'] = __('Author');
	if ( empty($post_type) || is_object_in_taxonomy($post_type, 'category') )
		$posts_columns['categories'] = __('Categories');
	if ( empty($post_type) || is_object_in_taxonomy($post_type, 'post_tag') )
		$posts_columns['tags'] = __('Tags');
	$post_status = !empty($_REQUEST['post_status']) ? $_REQUEST['post_status'] : 'all';
	if ( !in_array( $post_status, array('pending', 'draft', 'future') ) && ( empty($post_type) || post_type_supports($post_type, 'comments') ) )
		$posts_columns['comments'] = '<div class="vers"><img alt="Comments" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>';
	$posts_columns['date'] = __('Date');

	if ( 'page' == $post_type )
		$posts_columns = apply_filters( 'manage_pages_columns', $posts_columns );
	else
		$posts_columns = apply_filters( 'manage_posts_columns', $posts_columns, $post_type );
	$posts_columns = apply_filters( "manage_{$post_type}_posts_columns", $posts_columns );

	return $posts_columns;
}

// define the columns to display, the syntax is 'internal name' => 'display name'
/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function wp_manage_media_columns() {
	$posts_columns = array();
	$posts_columns['cb'] = '<input type="checkbox" />';
	$posts_columns['icon'] = '';
	/* translators: column name */
	$posts_columns['media'] = _x('File', 'column name');
	$posts_columns['author'] = __('Author');
	//$posts_columns['tags'] = _x('Tags', 'column name');
	/* translators: column name */
	$posts_columns['parent'] = _x('Attached to', 'column name');
	$posts_columns['comments'] = '<div class="vers"><img alt="Comments" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>';
	//$posts_columns['comments'] = __('Comments');
	/* translators: column name */
	$posts_columns['date'] = _x('Date', 'column name');
	$posts_columns = apply_filters('manage_media_columns', $posts_columns);

	return $posts_columns;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function wp_manage_pages_columns() {
	return wp_manage_posts_columns();
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $screen
 * @return unknown
 */
function get_column_headers($screen) {
	global $_wp_column_headers;

	if ( !isset($_wp_column_headers) )
		$_wp_column_headers = array();

	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	// Store in static to avoid running filters on each call
	if ( isset($_wp_column_headers[$screen->id]) )
		return $_wp_column_headers[$screen->id];

	switch ($screen->base) {
		case 'edit':
			 $_wp_column_headers[$screen->id] = wp_manage_posts_columns( $screen );
			 break;
		case 'edit-comments':
			$_wp_column_headers[$screen->id] = array(
				'cb' => '<input type="checkbox" />',
				'author' => __('Author'),
				/* translators: column name */
				'comment' => _x('Comment', 'column name'),
				//'date' => __('Submitted'),
				'response' => __('In Response To')
			);

			break;
		case 'link-manager':
			$_wp_column_headers[$screen->id] = array(
				'cb' => '<input type="checkbox" />',
				'name' => __('Name'),
				'url' => __('URL'),
				'categories' => __('Categories'),
				'rel' => __('Relationship'),
				'visible' => __('Visible'),
				'rating' => __('Rating')
			);

			break;
		case 'upload':
			$_wp_column_headers[$screen->id] = wp_manage_media_columns();
			break;
		case 'categories':
			$_wp_column_headers[$screen->id] = array(
				'cb' => '<input type="checkbox" />',
				'name' => __('Name'),
				'description' => __('Description'),
				'slug' => __('Slug'),
				'posts' => __('Posts')
			);

			break;
		case 'edit-link-categories':
			$_wp_column_headers[$screen->id] = array(
				'cb' => '<input type="checkbox" />',
				'name' => __('Name'),
				'description' => __('Description'),
				'slug' => __('Slug'),
				'links' => __('Links')
			);

			break;
		case 'edit-tags':
			$_wp_column_headers[$screen->id] = array(
				'cb' => '<input type="checkbox" />',
				'name' => __('Name'),
				'description' => __('Description'),
				'slug' => __('Slug'),
				'posts' => __('Posts')
			);

			break;
		case 'users':
			$_wp_column_headers[$screen->id] = array(
				'cb' => '<input type="checkbox" />',
				'username' => __('Username'),
				'name' => __('Name'),
				'email' => __('E-mail'),
				'role' => __('Role'),
				'posts' => __('Posts')
			);
			break;
		default :
			$_wp_column_headers[$screen->id] = array();
	}

	$_wp_column_headers[$screen->id] = apply_filters('manage_' . $screen->id . '_columns', $_wp_column_headers[$screen->id]);
	return $_wp_column_headers[$screen->id];
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $screen
 * @param unknown_type $id
 */
function print_column_headers( $screen, $id = true ) {
	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	$columns = get_column_headers( $screen );
	$hidden = get_hidden_columns($screen);
	$styles = array();

	foreach ( $columns as $column_key => $column_display_name ) {
		$class = ' class="manage-column';

		$class .= " column-$column_key";

		if ( 'cb' == $column_key )
			$class .= ' check-column';
		elseif ( in_array($column_key, array('posts', 'comments', 'links')) )
			$class .= ' num';

		$class .= '"';

		$style = '';
		if ( in_array($column_key, $hidden) )
			$style = 'display:none;';

		if ( isset($styles[$screen->id]) && isset($styles[$screen->id][$column_key]) )
			$style .= ' ' . $styles[$screen>id][$column_key];
		$style = ' style="' . $style . '"';
?>
	<th scope="col" <?php echo $id ? "id=\"$column_key\"" : ""; echo $class; echo $style; ?>><?php echo $column_display_name; ?></th>
<?php }
}

/**
 * Register column headers for a particular screen.  The header names will be listed in the Screen Options.
 *
 * @since 2.7.0
 *
 * @param string $screen The handle for the screen to add help to.  This is usually the hook name returned by the add_*_page() functions.
 * @param array $columns An array of columns with column IDs as the keys and translated column names as the values
 * @see get_column_headers(), print_column_headers(), get_hidden_columns()
 */
function register_column_headers($screen, $columns) {
	global $_wp_column_headers;

	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	if ( !isset($_wp_column_headers) )
		$_wp_column_headers = array();

	$_wp_column_headers[$screen->id] = $columns;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $screen
 */
function get_hidden_columns($screen) {
	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	return (array) get_user_option( 'manage' . $screen->id. 'columnshidden' );
}

/**
 * {@internal Missing Short Description}}
 *
 * Outputs the quick edit and bulk edit table rows for posts and pages
 *
 * @since 2.7
 *
 * @param string $screen
 */
function inline_edit_row( $screen ) {
	global $current_user, $mode;

	if ( is_string($screen) ) {
		$screen = array('id' => 'edit-' . $screen, 'base' => 'edit', 'post_type' => $screen );
		$screen = (object) $screen;
	}

	$post = get_default_post_to_edit( $screen->post_type );
	$post_type_object = get_post_type_object( $screen->post_type );

	$taxonomy_names = get_object_taxonomies( $screen->post_type );
	$hierarchical_taxonomies = array();
	$flat_taxonomies = array();
	foreach ( $taxonomy_names as $taxonomy_name ) {
		$taxonomy = get_taxonomy( $taxonomy_name);

		if ( !$taxonomy->show_ui )
			continue;

		if ( $taxonomy->hierarchical )
			$hierarchical_taxonomies[] = $taxonomy;
		else
			$flat_taxonomies[] = $taxonomy;
	}

	$columns = wp_manage_posts_columns($screen);
	$hidden = array_intersect( array_keys( $columns ), array_filter( get_hidden_columns($screen) ) );
	$col_count = count($columns) - count($hidden);
	$m = ( isset($mode) && 'excerpt' == $mode ) ? 'excerpt' : 'list';
	$can_publish = current_user_can($post_type_object->cap->publish_posts);
	$core_columns = array( 'cb' => true, 'date' => true, 'title' => true, 'categories' => true, 'tags' => true, 'comments' => true, 'author' => true );

?>

<form method="get" action=""><table style="display: none"><tbody id="inlineedit">
	<?php
	$hclass = count( $hierarchical_taxonomies ) ? 'post' : 'page';
	$bulk = 0;
	while ( $bulk < 2 ) { ?>

	<tr id="<?php echo $bulk ? 'bulk-edit' : 'inline-edit'; ?>" class="inline-edit-row inline-edit-row-<?php echo "$hclass inline-edit-$screen->post_type ";
		echo $bulk ? "bulk-edit-row bulk-edit-row-$hclass bulk-edit-$screen->post_type" : "quick-edit-row quick-edit-row-$hclass inline-edit-$screen->post_type";
	?>" style="display: none"><td colspan="<?php echo $col_count; ?>">

	<fieldset class="inline-edit-col-left"><div class="inline-edit-col">
		<h4><?php echo $bulk ? __( 'Bulk Edit' ) : __( 'Quick Edit' ); ?></h4>


<?php

if ( post_type_supports( $screen->post_type, 'title' ) ) :
	if ( $bulk ) : ?>
		<div id="bulk-title-div">
			<div id="bulk-titles"></div>
		</div>

<?php else : // $bulk ?>

		<label>
			<span class="title"><?php _e( 'Title' ); ?></span>
			<span class="input-text-wrap"><input type="text" name="post_title" class="ptitle" value="" /></span>
		</label>

		<label>
			<span class="title"><?php _e( 'Slug' ); ?></span>
			<span class="input-text-wrap"><input type="text" name="post_name" value="" /></span>
		</label>

<?php endif; // $bulk
endif; // post_type_supports title ?>

<?php if ( !$bulk ) : ?>
		<label><span class="title"><?php _e( 'Date' ); ?></span></label>
		<div class="inline-edit-date">
			<?php touch_time(1, 1, 4, 1); ?>
		</div>
		<br class="clear" />

<?php endif; // $bulk

	if ( post_type_supports( $screen->post_type, 'author' ) ) :
		$authors = get_editable_user_ids( $current_user->id, true, $screen->post_type ); // TODO: ROLE SYSTEM
		$authors_dropdown = '';
		if ( $authors && count( $authors ) > 1 ) :
			$users_opt = array('include' => $authors, 'name' => 'post_author', 'class'=> 'authors', 'multi' => 1, 'echo' => 0);
			if ( $bulk )
				$users_opt['show_option_none'] = __('&mdash; No Change &mdash;');
			$authors_dropdown  = '<label>';
			$authors_dropdown .= '<span class="title">' . __( 'Author' ) . '</span>';
			$authors_dropdown .= wp_dropdown_users( $users_opt );
			$authors_dropdown .= '</label>';

		endif; // authors
?>

<?php if ( !$bulk ) echo $authors_dropdown;
endif; // post_type_supports author

if ( !$bulk ) :
?>

		<div class="inline-edit-group">
			<label class="alignleft">
				<span class="title"><?php _e( 'Password' ); ?></span>
				<span class="input-text-wrap"><input type="text" name="post_password" class="inline-edit-password-input" value="" /></span>
			</label>

			<em style="margin:5px 10px 0 0" class="alignleft">
				<?php
				/* translators: Between password field and private checkbox on post quick edit interface */
				echo __( '&ndash;OR&ndash;' );
				?>
			</em>
			<label class="alignleft inline-edit-private">
				<input type="checkbox" name="keep_private" value="private" />
				<span class="checkbox-title"><?php echo __('Private'); ?></span>
			</label>
		</div>

<?php endif; ?>

	</div></fieldset>

<?php if ( count($hierarchical_taxonomies) && !$bulk ) : ?>

	<fieldset class="inline-edit-col-center inline-edit-categories"><div class="inline-edit-col">

<?php foreach ( $hierarchical_taxonomies as $taxonomy ) : ?>

		<span class="title inline-edit-categories-label"><?php echo esc_html($taxonomy->label) ?>
			<span class="catshow"><?php _e('[more]'); ?></span>
			<span class="cathide" style="display:none;"><?php _e('[less]'); ?></span>
		</span>
		<ul class="cat-checklist <?php echo esc_attr($taxonomy->name)?>-checklist">
			<?php wp_terms_checklist(null, array('taxonomy' => $taxonomy->name)) ?>
		</ul>

<?php endforeach; //$hierarchical_taxonomies as $taxonomy ?>

	</div></fieldset>

<?php endif; // count($hierarchical_taxonomies) && !$bulk ?>

	<fieldset class="inline-edit-col-right"><div class="inline-edit-col">

<?php
	if ( post_type_supports( $screen->post_type, 'author' ) && $bulk )
		echo $authors_dropdown;
?>

<?php if ( $post_type_object->hierarchical ) : ?>

		<label>
			<span class="title"><?php _e( 'Parent' ); ?></span>
<?php
	$dropdown_args = array('post_type' => $post_type_object->name, 'selected' => $post->post_parent, 'name' => 'post_parent', 'show_option_none' => __('Main Page (no parent)'), 'option_none_value' => 0, 'sort_column'=> 'menu_order, post_title');
	if ( $bulk )
		$dropdown_args['show_option_no_change'] =  __('&mdash; No Change &mdash;');
	$dropdown_args = apply_filters('quick_edit_dropdown_pages_args', $dropdown_args);
	wp_dropdown_pages($dropdown_args);
?>
		</label>

<?php if ( post_type_supports( $screen->post_type, 'page-attributes' ) ) :
		if ( !$bulk ) : ?>

		<label>
			<span class="title"><?php _e( 'Order' ); ?></span>
			<span class="input-text-wrap"><input type="text" name="menu_order" class="inline-edit-menu-order-input" value="<?php echo $post->menu_order ?>" /></span>
		</label>

<?php	endif; // !$bulk ?>

		<label>
			<span class="title"><?php _e( 'Template' ); ?></span>
			<select name="page_template">
<?php	if ( $bulk ) : ?>
				<option value="-1"><?php _e('&mdash; No Change &mdash;'); ?></option>
<?php	endif; // $bulk ?>
				<option value="default"><?php _e( 'Default Template' ); ?></option>
				<?php page_template_dropdown() ?>
			</select>
		</label>

<?php
	endif; // post_type_supports page-attributes
endif; // $post_type_object->hierarchical ?>

<?php if ( count($flat_taxonomies) && !$bulk ) : ?>

<?php foreach ( $flat_taxonomies as $taxonomy ) : ?>

		<label class="inline-edit-tags">
			<span class="title"><?php echo esc_html($taxonomy->label) ?></span>
			<textarea cols="22" rows="1" name="tax_input[<?php echo esc_attr($taxonomy->name)?>]" class="tax_input_<?php echo esc_attr($taxonomy->name)?>"></textarea>
		</label>

<?php endforeach; //$flat_taxonomies as $taxonomy ?>

<?php endif; // count($flat_taxonomies) && !$bulk  ?>

<?php if ( post_type_supports( $screen->post_type, 'comments' ) || post_type_supports( $screen->post_type, 'trackbacks' ) ) :
	if ( $bulk ) : ?>

		<div class="inline-edit-group">
	<?php if ( post_type_supports( $screen->post_type, 'comments' ) ) : ?>
		<label class="alignleft">
			<span class="title"><?php _e( 'Comments' ); ?></span>
			<select name="comment_status">
				<option value=""><?php _e('&mdash; No Change &mdash;'); ?></option>
				<option value="open"><?php _e('Allow'); ?></option>
				<option value="closed"><?php _e('Do not allow'); ?></option>
			</select>
		</label>
	<?php endif; if ( post_type_supports( $screen->post_type, 'trackbacks' ) ) : ?>
		<label class="alignright">
			<span class="title"><?php _e( 'Pings' ); ?></span>
			<select name="ping_status">
				<option value=""><?php _e('&mdash; No Change &mdash;'); ?></option>
				<option value="open"><?php _e('Allow'); ?></option>
				<option value="closed"><?php _e('Do not allow'); ?></option>
			</select>
		</label>
	<?php endif; ?>
		</div>

<?php else : // $bulk ?>

		<div class="inline-edit-group">
		<?php if ( post_type_supports( $screen->post_type, 'comments' ) ) : ?>
			<label class="alignleft">
				<input type="checkbox" name="comment_status" value="open" />
				<span class="checkbox-title"><?php _e( 'Allow Comments' ); ?></span>
			</label>
		<?php endif; if ( post_type_supports( $screen->post_type, 'trackbacks' ) ) : ?>
			<label class="alignleft">
				<input type="checkbox" name="ping_status" value="open" />
				<span class="checkbox-title"><?php _e( 'Allow Pings' ); ?></span>
			</label>
		<?php endif; ?>
		</div>

<?php endif; // $bulk
endif; // post_type_supports comments or pings ?>

		<div class="inline-edit-group">
			<label class="inline-edit-status alignleft">
				<span class="title"><?php _e( 'Status' ); ?></span>
				<select name="_status">
<?php if ( $bulk ) : ?>
					<option value="-1"><?php _e('&mdash; No Change &mdash;'); ?></option>
<?php endif; // $bulk ?>
				<?php if ( $can_publish ) : // Contributors only get "Unpublished" and "Pending Review" ?>
					<option value="publish"><?php _e( 'Published' ); ?></option>
					<option value="future"><?php _e( 'Scheduled' ); ?></option>
<?php if ( $bulk ) : ?>
					<option value="private"><?php _e('Private') ?></option>
<?php endif; // $bulk ?>
				<?php endif; ?>
					<option value="pending"><?php _e( 'Pending Review' ); ?></option>
					<option value="draft"><?php _e( 'Draft' ); ?></option>
				</select>
			</label>

<?php if ( 'post' == $screen->post_type && $can_publish && current_user_can( $post_type_object->cap->edit_others_posts ) ) : ?>

<?php	if ( $bulk ) : ?>

			<label class="alignright">
				<span class="title"><?php _e( 'Sticky' ); ?></span>
				<select name="sticky">
					<option value="-1"><?php _e( '&mdash; No Change &mdash;' ); ?></option>
					<option value="sticky"><?php _e( 'Sticky' ); ?></option>
					<option value="unsticky"><?php _e( 'Not Sticky' ); ?></option>
				</select>
			</label>

<?php	else : // $bulk ?>

			<label class="alignleft">
				<input type="checkbox" name="sticky" value="sticky" />
				<span class="checkbox-title"><?php _e( 'Make this post sticky' ); ?></span>
			</label>

<?php	endif; // $bulk ?>

<?php endif; // !$is_page && $can_publish && current_user_can( 'edit_others_cap' ) ?>

		</div>

	</div></fieldset>

<?php
	foreach ( $columns as $column_name => $column_display_name ) {
		if ( isset( $core_columns[$column_name] ) )
			continue;
		do_action( $bulk ? 'bulk_edit_custom_box' : 'quick_edit_custom_box', $column_name, $screen->post_type );
	}
?>
	<p class="submit inline-edit-save">
		<a accesskey="c" href="#inline-edit" title="<?php _e('Cancel'); ?>" class="button-secondary cancel alignleft"><?php _e('Cancel'); ?></a>
		<?php if ( ! $bulk ) {
			wp_nonce_field( 'inlineeditnonce', '_inline_edit', false );
			$update_text = __( 'Update' );
			?>
			<a accesskey="s" href="#inline-edit" title="<?php _e('Update'); ?>" class="button-primary save alignright"><?php echo esc_attr( $update_text ); ?></a>
			<img class="waiting" style="display:none;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
		<?php } else {
			$update_text = __( 'Update' );
		?>
			<input accesskey="s" class="button-primary alignright" type="submit" name="bulk_edit" value="<?php echo esc_attr( $update_text ); ?>" />
		<?php } ?>
		<input type="hidden" name="post_view" value="<?php echo esc_attr($m); ?>" />
		<input type="hidden" name="screen" value="<?php echo esc_attr($screen->id); ?>" />
		<br class="clear" />
	</p>
	</td></tr>
<?php
	$bulk++;
	} ?>
	</tbody></table></form>
<?php
}

// adds hidden fields with the data for use in the inline editor for posts and pages
/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $post
 */
function get_inline_data($post) {
	$post_type_object = get_post_type_object($post->post_type);
	if ( ! current_user_can($post_type_object->cap->edit_post, $post->ID) )
		return;

	$title = esc_attr( get_the_title( $post->ID ) );

	echo '
<div class="hidden" id="inline_' . $post->ID . '">
	<div class="post_title">' . $title . '</div>
	<div class="post_name">' . apply_filters('editable_slug', $post->post_name) . '</div>
	<div class="post_author">' . $post->post_author . '</div>
	<div class="comment_status">' . $post->comment_status . '</div>
	<div class="ping_status">' . $post->ping_status . '</div>
	<div class="_status">' . $post->post_status . '</div>
	<div class="jj">' . mysql2date( 'd', $post->post_date, false ) . '</div>
	<div class="mm">' . mysql2date( 'm', $post->post_date, false ) . '</div>
	<div class="aa">' . mysql2date( 'Y', $post->post_date, false ) . '</div>
	<div class="hh">' . mysql2date( 'H', $post->post_date, false ) . '</div>
	<div class="mn">' . mysql2date( 'i', $post->post_date, false ) . '</div>
	<div class="ss">' . mysql2date( 's', $post->post_date, false ) . '</div>
	<div class="post_password">' . esc_html( $post->post_password ) . '</div>';

	if ( $post_type_object->hierarchical )
		echo '<div class="post_parent">' . $post->post_parent . '</div>';

	if ( $post->post_type == 'page' )
		echo '<div class="page_template">' . esc_html( get_post_meta( $post->ID, '_wp_page_template', true ) ) . '</div>';

	if ( $post_type_object->hierarchical )
		echo '<div class="menu_order">' . $post->menu_order . '</div>';

	$taxonomy_names = get_object_taxonomies( $post->post_type );
	foreach ( $taxonomy_names as $taxonomy_name) {
		$taxonomy = get_taxonomy( $taxonomy_name );

		if ( $taxonomy->hierarchical && $taxonomy->show_ui )
				echo '<div class="post_category" id="'.$taxonomy_name.'_'.$post->ID.'">' . implode( ',', wp_get_object_terms( $post->ID, $taxonomy_name, array('fields'=>'ids')) ) . '</div>';
		elseif ( $taxonomy->show_ui )
			echo '<div class="tags_input" id="'.$taxonomy_name.'_'.$post->ID.'">' . esc_html( str_replace( ',', ', ', get_terms_to_edit($post->ID, $taxonomy_name) ) ) . '</div>';
	}

	if ( !$post_type_object->hierarchical )
		echo '<div class="sticky">' . (is_sticky($post->ID) ? 'sticky' : '') . '</div>';

	echo '</div>';
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $posts
 */
function post_rows( $posts = array() ) {
	global $wp_query, $post, $mode;

	add_filter('the_title','esc_html');

	// Create array of post IDs.
	$post_ids = array();

	if ( empty($posts) )
		$posts = &$wp_query->posts;

	foreach ( $posts as $a_post )
		$post_ids[] = $a_post->ID;

	$comment_pending_count = get_pending_comments_num($post_ids);

	foreach ( $posts as $post ) {
		if ( empty($comment_pending_count[$post->ID]) )
			$comment_pending_count[$post->ID] = 0;

		_post_row($post, $comment_pending_count[$post->ID], $mode);
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $a_post
 * @param unknown_type $pending_comments
 * @param unknown_type $mode
 */
function _post_row($a_post, $pending_comments, $mode) {
	global $post, $current_user, $current_screen;
	static $rowclass;

	$global_post = $post;
	$post = $a_post;
	setup_postdata($post);

	$rowclass = 'alternate' == $rowclass ? '' : 'alternate';
	$post_owner = ( $current_user->ID == $post->post_author ? 'self' : 'other' );
	$edit_link = get_edit_post_link( $post->ID );
	$title = _draft_or_post_title();
	$post_type_object = get_post_type_object($post->post_type);
?>
	<tr id='post-<?php echo $post->ID; ?>' class='<?php echo trim( $rowclass . ' author-' . $post_owner . ' status-' . $post->post_status ); ?> iedit' valign="top">
<?php
	$posts_columns = get_column_headers( $current_screen );
	$hidden = get_hidden_columns( $current_screen );
	foreach ( $posts_columns as $column_name=>$column_display_name ) {
		$class = "class=\"$column_name column-$column_name\"";

		$style = '';
		if ( in_array($column_name, $hidden) )
			$style = ' style="display:none;"';

		$attributes = "$class$style";

		switch ($column_name) {

		case 'cb':
		?>
		<th scope="row" class="check-column"><?php if ( current_user_can( $post_type_object->cap->edit_post, $post->ID ) ) { ?><input type="checkbox" name="post[]" value="<?php the_ID(); ?>" /><?php } ?></th>
		<?php
		break;

		case 'date':
			if ( '0000-00-00 00:00:00' == $post->post_date && 'date' == $column_name ) {
				$t_time = $h_time = __('Unpublished');
				$time_diff = 0;
			} else {
				$t_time = get_the_time(__('Y/m/d g:i:s A'));
				$m_time = $post->post_date;
				$time = get_post_time('G', true, $post);

				$time_diff = time() - $time;

				if ( $time_diff > 0 && $time_diff < 24*60*60 )
					$h_time = sprintf( __('%s ago'), human_time_diff( $time ) );
				else
					$h_time = mysql2date(__('Y/m/d'), $m_time);
			}

			echo '<td ' . $attributes . '>';
			if ( 'excerpt' == $mode )
				echo apply_filters('post_date_column_time', $t_time, $post, $column_name, $mode);
			else
				echo '<abbr title="' . $t_time . '">' . apply_filters('post_date_column_time', $h_time, $post, $column_name, $mode) . '</abbr>';
			echo '<br />';
			if ( 'publish' == $post->post_status ) {
				_e('Published');
			} elseif ( 'future' == $post->post_status ) {
				if ( $time_diff > 0 )
					echo '<strong class="attention">' . __('Missed schedule') . '</strong>';
				else
					_e('Scheduled');
			} else {
				_e('Last Modified');
			}
			echo '</td>';
		break;

		case 'title':
			$attributes = 'class="post-title column-title"' . $style;
		?>
		<td <?php echo $attributes ?>><strong><?php if ( current_user_can($post_type_object->cap->edit_post, $post->ID) && $post->post_status != 'trash' ) { ?><a class="row-title" href="<?php echo $edit_link; ?>" title="<?php echo esc_attr(sprintf(__('Edit &#8220;%s&#8221;'), $title)); ?>"><?php echo $title ?></a><?php } else { echo $title; }; _post_states($post); ?></strong>
		<?php
			if ( 'excerpt' == $mode )
				the_excerpt();

			$actions = array();
			if ( current_user_can($post_type_object->cap->edit_post, $post->ID) && 'trash' != $post->post_status ) {
				$actions['edit'] = '<a href="' . get_edit_post_link($post->ID, true) . '" title="' . esc_attr(__('Edit this post')) . '">' . __('Edit') . '</a>';
				$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="' . esc_attr(__('Edit this post inline')) . '">' . __('Quick&nbsp;Edit') . '</a>';
			}
			if ( current_user_can($post_type_object->cap->delete_post, $post->ID) ) {
				if ( 'trash' == $post->post_status )
					$actions['untrash'] = "<a title='" . esc_attr(__('Restore this post from the Trash')) . "' href='" . wp_nonce_url( admin_url( sprintf($post_type_object->_edit_link . '&amp;action=untrash', $post->ID) ), 'untrash-' . $post->post_type . '_' . $post->ID ) . "'>" . __('Restore') . "</a>";
				elseif ( EMPTY_TRASH_DAYS )
					$actions['trash'] = "<a class='submitdelete' title='" . esc_attr(__('Move this post to the Trash')) . "' href='" . get_delete_post_link($post->ID) . "'>" . __('Trash') . "</a>";
				if ( 'trash' == $post->post_status || !EMPTY_TRASH_DAYS )
					$actions['delete'] = "<a class='submitdelete' title='" . esc_attr(__('Delete this post permanently')) . "' href='" . get_delete_post_link($post->ID, '', true) . "'>" . __('Delete Permanently') . "</a>";
			}
			if ( in_array($post->post_status, array('pending', 'draft')) ) {
				if ( current_user_can($post_type_object->cap->edit_post, $post->ID) )
					$actions['view'] = '<a href="' . add_query_arg( 'preview', 'true', get_permalink($post->ID) ) . '" title="' . esc_attr(sprintf(__('Preview &#8220;%s&#8221;'), $title)) . '" rel="permalink">' . __('Preview') . '</a>';
			} elseif ( 'trash' != $post->post_status ) {
				$actions['view'] = '<a href="' . get_permalink($post->ID) . '" title="' . esc_attr(sprintf(__('View &#8220;%s&#8221;'), $title)) . '" rel="permalink">' . __('View') . '</a>';
			}
			$actions = apply_filters('post_row_actions', $actions, $post);
			$action_count = count($actions);
			$i = 0;
			echo '<div class="row-actions">';
			foreach ( $actions as $action => $link ) {
				++$i;
				( $i == $action_count ) ? $sep = '' : $sep = ' | ';
				echo "<span class='$action'>$link$sep</span>";
			}
			echo '</div>';

			get_inline_data($post);
		?>
		</td>
		<?php
		break;

		case 'categories':
		?>
		<td <?php echo $attributes ?>><?php
			$categories = get_the_category();
			if ( !empty( $categories ) ) {
				$out = array();
				foreach ( $categories as $c )
					$out[] = "<a href='edit.php?category_name=$c->slug'> " . esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'category', 'display')) . "</a>";
					echo join( ', ', $out );
			} else {
				_e('Uncategorized');
			}
		?></td>
		<?php
		break;

		case 'tags':
		?>
		<td <?php echo $attributes ?>><?php
			$tags = get_the_tags($post->ID);
			if ( !empty( $tags ) ) {
				$out = array();
				foreach ( $tags as $c )
					$out[] = "<a href='edit.php?tag=$c->slug'> " . esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'post_tag', 'display')) . "</a>";
				echo join( ', ', $out );
			} else {
				_e('No Tags');
			}
		?></td>
		<?php
		break;

		case 'comments':
		?>
		<td <?php echo $attributes ?>><div class="post-com-count-wrapper">
		<?php
			$pending_phrase = sprintf( __('%s pending'), number_format( $pending_comments ) );
			if ( $pending_comments )
				echo '<strong>';
				comments_number("<a href='edit-comments.php?p=$post->ID' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . /* translators: comment count link */ _x('0', 'comment count') . '</span></a>', "<a href='edit-comments.php?p=$post->ID' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . /* translators: comment count link */ _x('1', 'comment count') . '</span></a>', "<a href='edit-comments.php?p=$post->ID' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . /* translators: comment count link: % will be substituted by comment count */ _x('%', 'comment count') . '</span></a>');
				if ( $pending_comments )
				echo '</strong>';
		?>
		</div></td>
		<?php
		break;

		case 'author':
		?>
		<td <?php echo $attributes ?>><a href="edit.php?author=<?php the_author_meta('ID'); ?>"><?php the_author() ?></a></td>
		<?php
		break;

		case 'control_view':
		?>
		<td><a href="<?php the_permalink(); ?>" rel="permalink" class="view"><?php _e('View'); ?></a></td>
		<?php
		break;

		case 'control_edit':
		?>
		<td><?php if ( current_user_can($post_type_object->cap->edit_post, $post->ID) ) { echo "<a href='$edit_link' class='edit'>" . __('Edit') . "</a>"; } ?></td>
		<?php
		break;

		case 'control_delete':
		?>
		<td><?php if ( current_user_can($post_type_object->cap->delete_post, $post->ID) ) { echo "<a href='" . wp_nonce_url("post.php?action=delete&amp;post=$id", 'delete-post_' . $post->ID) . "' class='delete'>" . __('Delete') . "</a>"; } ?></td>
		<?php
		break;

		default:
		?>
		<td <?php echo $attributes ?>><?php do_action('manage_posts_custom_column', $column_name, $post->ID); ?></td>
		<?php
		break;
	}
}
?>
	</tr>
<?php
	$post = $global_post;
}

/*
 * display one row if the page doesn't have any children
 * otherwise, display the row and its children in subsequent rows
 */
/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $page
 * @param unknown_type $level
 */
function display_page_row( $page, $level = 0 ) {
	global $post, $current_screen;
	static $rowclass;

	$post = $page;
	setup_postdata($page);

	if ( 0 == $level && (int)$page->post_parent > 0 ) {
		//sent level 0 by accident, by default, or because we don't know the actual level
		$find_main_page = (int)$page->post_parent;
		while ( $find_main_page > 0 ) {
			$parent = get_page($find_main_page);

			if ( is_null($parent) )
				break;

			$level++;
			$find_main_page = (int)$parent->post_parent;

			if ( !isset($parent_name) )
				$parent_name = $parent->post_title;
		}
	}

	$page->post_title = esc_html( $page->post_title );
	$pad = str_repeat( '&#8212; ', $level );
	$id = (int) $page->ID;
	$rowclass = 'alternate' == $rowclass ? '' : 'alternate';
	$posts_columns = get_column_headers( $current_screen );
	$hidden = get_hidden_columns(  $current_screen );
	$title = _draft_or_post_title();
	$post_type = $page->post_type;
	$post_type_object = get_post_type_object($post_type);
?>
<tr id="page-<?php echo $id; ?>" class="<?php echo $rowclass; ?> iedit">
<?php

foreach ( $posts_columns as $column_name => $column_display_name ) {
	$class = "class=\"$column_name column-$column_name\"";

	$style = '';
	if ( in_array($column_name, $hidden) )
		$style = ' style="display:none;"';

	$attributes = "$class$style";

	switch ($column_name) {

	case 'cb':
		?>
		<th scope="row" class="check-column"><input type="checkbox" name="post[]" value="<?php the_ID(); ?>" /></th>
		<?php
		break;
	case 'date':
		if ( '0000-00-00 00:00:00' == $page->post_date && 'date' == $column_name ) {
			$t_time = $h_time = __('Unpublished');
			$time_diff = 0;
		} else {
			$t_time = get_the_time(__('Y/m/d g:i:s A'));
			$m_time = $page->post_date;
			$time = get_post_time('G', true);

			$time_diff = time() - $time;

			if ( $time_diff > 0 && $time_diff < 24*60*60 )
				$h_time = sprintf( __('%s ago'), human_time_diff( $time ) );
			else
				$h_time = mysql2date(__('Y/m/d'), $m_time);
		}
		echo '<td ' . $attributes . '>';
		echo '<abbr title="' . $t_time . '">' . apply_filters('post_date_column_time', $h_time, $page, $column_name, '') . '</abbr>';
		echo '<br />';
		if ( 'publish' == $page->post_status ) {
			_e('Published');
		} elseif ( 'future' == $page->post_status ) {
			if ( $time_diff > 0 )
				echo '<strong class="attention">' . __('Missed schedule') . '</strong>';
			else
				_e('Scheduled');
		} else {
			_e('Last Modified');
		}
		echo '</td>';
		break;
	case 'title':
		$attributes = 'class="post-title page-title column-title"' . $style;
		$edit_link = get_edit_post_link( $page->ID );
		?>
		<td <?php echo $attributes ?>><strong><?php if ( current_user_can($post_type_object->cap->edit_post, $page->ID) && $post->post_status != 'trash' ) { ?><a class="row-title" href="<?php echo $edit_link; ?>" title="<?php echo esc_attr(sprintf(__('Edit &#8220;%s&#8221;'), $title)); ?>"><?php echo $pad; echo $title ?></a><?php } else { echo $pad; echo $title; }; _post_states($page); echo isset($parent_name) ? ' | ' . $post_type_object->labels->parent . ' ' . esc_html($parent_name) : ''; ?></strong>
		<?php
		$actions = array();
		if ( current_user_can($post_type_object->cap->edit_post, $page->ID) && $post->post_status != 'trash' ) {
			$actions['edit'] = '<a href="' . $edit_link . '" title="' . esc_attr(__('Edit this page')) . '">' . __('Edit') . '</a>';
			$actions['inline'] = '<a href="#" class="editinline">' . __('Quick&nbsp;Edit') . '</a>';
		}
		if ( current_user_can($post_type_object->cap->delete_post, $page->ID) ) {
			if ( $post->post_status == 'trash' )
				$actions['untrash'] = "<a title='" . esc_attr(__('Remove this page from the Trash')) . "' href='" . wp_nonce_url("post.php?post_type=$post_type&amp;action=untrash&amp;post=$page->ID", 'untrash-' . $post->post_type . '_' . $page->ID) . "'>" . __('Restore') . "</a>";
			elseif ( EMPTY_TRASH_DAYS )
				$actions['trash'] = "<a class='submitdelete' title='" . esc_attr(__('Move this page to the Trash')) . "' href='" . get_delete_post_link($page->ID) . "'>" . __('Trash') . "</a>";
			if ( $post->post_status == 'trash' || !EMPTY_TRASH_DAYS )
				$actions['delete'] = "<a class='submitdelete' title='" . esc_attr(__('Delete this page permanently')) . "' href='" . wp_nonce_url("post.php?post_type=$post_type&amp;action=delete&amp;post=$page->ID", 'delete-' . $post->post_type . '_' . $page->ID) . "'>" . __('Delete Permanently') . "</a>";
		}
		if ( in_array($post->post_status, array('pending', 'draft')) ) {
			if ( current_user_can($post_type_object->cap->edit_post, $page->ID) )
				$actions['view'] = '<a href="' . add_query_arg( 'preview', 'true', get_permalink($page->ID) ) . '" title="' . esc_attr(sprintf(__('Preview &#8220;%s&#8221;'), $title)) . '" rel="permalink">' . __('Preview') . '</a>';
		} elseif ( $post->post_status != 'trash' ) {
			$actions['view'] = '<a href="' . get_permalink($page->ID) . '" title="' . esc_attr(sprintf(__('View &#8220;%s&#8221;'), $title)) . '" rel="permalink">' . __('View') . '</a>';
		}
		$actions = apply_filters('page_row_actions', $actions, $page);
		$action_count = count($actions);

		$i = 0;
		echo '<div class="row-actions">';
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			echo "<span class='$action'>$link$sep</span>";
		}
		echo '</div>';

		get_inline_data($post);
		echo '</td>';
		break;

	case 'comments':
		?>
		<td <?php echo $attributes ?>><div class="post-com-count-wrapper">
		<?php
		$left = get_pending_comments_num( $page->ID );
		$pending_phrase = sprintf( __('%s pending'), number_format( $left ) );
		if ( $left )
			echo '<strong>';
		comments_number("<a href='edit-comments.php?p=$id' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . /* translators: comment count link */ _x('0', 'comment count') . '</span></a>', "<a href='edit-comments.php?p=$id' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . /* translators: comment count link */ _x('1', 'comment count') . '</span></a>', "<a href='edit-comments.php?p=$id' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . /* translators: comment count link: % will be substituted by comment count */ _x('%', 'comment count') . '</span></a>');
		if ( $left )
			echo '</strong>';
		?>
		</div></td>
		<?php
		break;

	case 'author':
		?>
		<td <?php echo $attributes ?>><a href="edit-pages.php?author=<?php the_author_meta('ID'); ?>"><?php the_author() ?></a></td>
		<?php
		break;

	default:
		?>
		<td <?php echo $attributes ?>><?php do_action('manage_pages_custom_column', $column_name, $id); ?></td>
		<?php
		break;
	}
}
?>

</tr>

<?php
}

/*
 * displays pages in hierarchical order with paging support
 */
/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $pages
 * @param unknown_type $pagenum
 * @param unknown_type $per_page
 * @return unknown
 */
function page_rows($pages, $pagenum = 1, $per_page = 20) {
	global $wpdb;

	$level = 0;

	if ( ! $pages ) {
		$pages = get_pages( array('sort_column' => 'menu_order') );

		if ( ! $pages )
			return false;
	}

	/*
	 * arrange pages into two parts: top level pages and children_pages
	 * children_pages is two dimensional array, eg.
	 * children_pages[10][] contains all sub-pages whose parent is 10.
	 * It only takes O(N) to arrange this and it takes O(1) for subsequent lookup operations
	 * If searching, ignore hierarchy and treat everything as top level
	 */
	if ( empty($_GET['s']) ) {

		$top_level_pages = array();
		$children_pages = array();

		foreach ( $pages as $page ) {

			// catch and repair bad pages
			if ( $page->post_parent == $page->ID ) {
				$page->post_parent = 0;
				$wpdb->update($wpdb->posts, array('post_parent' => 0), array('ID' => $page->ID));
				clean_page_cache( $page->ID );
			}

			if ( 0 == $page->post_parent )
				$top_level_pages[] = $page;
			else
				$children_pages[ $page->post_parent ][] = $page;
		}

		$pages = &$top_level_pages;
	}

	$count = 0;
	$start = ($pagenum - 1) * $per_page;
	$end = $start + $per_page;

	foreach ( $pages as $page ) {
		if ( $count >= $end )
			break;

		if ( $count >= $start )
			echo "\t" . display_page_row( $page, $level );

		$count++;

		if ( isset($children_pages) )
			_page_rows( $children_pages, $count, $page->ID, $level + 1, $pagenum, $per_page );
	}

	// if it is the last pagenum and there are orphaned pages, display them with paging as well
	if ( isset($children_pages) && $count < $end ){
		foreach( $children_pages as $orphans ){
			foreach ( $orphans as $op ) {
				if ( $count >= $end )
					break;
				if ( $count >= $start )
					echo "\t" . display_page_row( $op, 0 );
				$count++;
			}
		}
	}
}

/*
 * Given a top level page ID, display the nested hierarchy of sub-pages
 * together with paging support
 */
/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $children_pages
 * @param unknown_type $count
 * @param unknown_type $parent
 * @param unknown_type $level
 * @param unknown_type $pagenum
 * @param unknown_type $per_page
 */
function _page_rows( &$children_pages, &$count, $parent, $level, $pagenum, $per_page ) {

	if ( ! isset( $children_pages[$parent] ) )
		return;

	$start = ($pagenum - 1) * $per_page;
	$end = $start + $per_page;

	foreach ( $children_pages[$parent] as $page ) {

		if ( $count >= $end )
			break;

		// If the page starts in a subtree, print the parents.
		if ( $count == $start && $page->post_parent > 0 ) {
			$my_parents = array();
			$my_parent = $page->post_parent;
			while ( $my_parent) {
				$my_parent = get_post($my_parent);
				$my_parents[] = $my_parent;
				if ( !$my_parent->post_parent )
					break;
				$my_parent = $my_parent->post_parent;
			}
			$num_parents = count($my_parents);
			while( $my_parent = array_pop($my_parents) ) {
				echo "\t" . display_page_row( $my_parent, $level - $num_parents );
				$num_parents--;
			}
		}

		if ( $count >= $start )
			echo "\t" . display_page_row( $page, $level );

		$count++;

		_page_rows( $children_pages, $count, $page->ID, $level + 1, $pagenum, $per_page );
	}

	unset( $children_pages[$parent] ); //required in order to keep track of orphans
}

/**
 * Generate HTML for a single row on the users.php admin panel.
 *
 * @since 2.1.0
 *
 * @param object $user_object
 * @param string $style Optional. Attributes added to the TR element.  Must be sanitized.
 * @param string $role Key for the $wp_roles array.
 * @param int $numposts Optional. Post count to display for this user.  Defaults to zero, as in, a new user has made zero posts.
 * @return string
 */
function user_row( $user_object, $style = '', $role = '', $numposts = 0 ) {
	global $wp_roles;

	$current_user = wp_get_current_user();

	if ( !( is_object( $user_object) && is_a( $user_object, 'WP_User' ) ) )
		$user_object = new WP_User( (int) $user_object );
	$user_object = sanitize_user_object($user_object, 'display');
	$email = $user_object->user_email;
	$url = $user_object->user_url;
	$short_url = str_replace( 'http://', '', $url );
	$short_url = str_replace( 'www.', '', $short_url );
	if ('/' == substr( $short_url, -1 ))
		$short_url = substr( $short_url, 0, -1 );
	if ( strlen( $short_url ) > 35 )
		$short_url = substr( $short_url, 0, 32 ).'...';
	$checkbox = '';
	// Check if the user for this row is editable
	if ( current_user_can( 'list_users' ) ) {
		// Set up the user editing link
		// TODO: make profile/user-edit determination a separate function
		if ($current_user->ID == $user_object->ID) {
			$edit_link = 'profile.php';
		} else {
			$edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( esc_url( stripslashes( $_SERVER['REQUEST_URI'] ) ) ), "user-edit.php?user_id=$user_object->ID" ) );
		}
		$edit = "<strong><a href=\"$edit_link\">$user_object->user_login</a></strong><br />";

		// Set up the hover actions for this user
		$actions = array();

		if ( current_user_can('edit_user',  $user_object->ID) ) {
			$edit = "<strong><a href=\"$edit_link\">$user_object->user_login</a></strong><br />";
			$actions['edit'] = '<a href="' . $edit_link . '">' . __('Edit') . '</a>';
		} else {
			$edit = "<strong>$user_object->user_login</strong><br />";
		}

		if ( !is_multisite() && $current_user->ID != $user_object->ID && current_user_can('delete_user', $user_object->ID) )
			$actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url("users.php?action=delete&amp;user=$user_object->ID", 'bulk-users') . "'>" . __('Delete') . "</a>";
		if ( is_multisite() && $current_user->ID != $user_object->ID && current_user_can('remove_user', $user_object->ID) )
			$actions['remove'] = "<a class='submitdelete' href='" . wp_nonce_url("users.php?action=remove&amp;user=$user_object->ID", 'bulk-users') . "'>" . __('Remove') . "</a>";
		$actions = apply_filters('user_row_actions', $actions, $user_object);
		$action_count = count($actions);
		$i = 0;
		$edit .= '<div class="row-actions">';
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			$edit .= "<span class='$action'>$link$sep</span>";
		}
		$edit .= '</div>';

		// Set up the checkbox (because the user is editable, otherwise its empty)
		$checkbox = "<input type='checkbox' name='users[]' id='user_{$user_object->ID}' class='$role' value='{$user_object->ID}' />";

	} else {
		$edit = '<strong>' . $user_object->user_login . '</strong>';
	}
	$role_name = isset($wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : __('None');
	$r = "<tr id='user-$user_object->ID'$style>";
	$columns = get_column_headers('users');
	$hidden = get_hidden_columns('users');
	$avatar = get_avatar( $user_object->ID, 32 );
	foreach ( $columns as $column_name => $column_display_name ) {
		$class = "class=\"$column_name column-$column_name\"";

		$style = '';
		if ( in_array($column_name, $hidden) )
			$style = ' style="display:none;"';

		$attributes = "$class$style";

		switch ($column_name) {
			case 'cb':
				$r .= "<th scope='row' class='check-column'>$checkbox</th>";
				break;
			case 'username':
				$r .= "<td $attributes>$avatar $edit</td>";
				break;
			case 'name':
				$r .= "<td $attributes>$user_object->first_name $user_object->last_name</td>";
				break;
			case 'email':
				$r .= "<td $attributes><a href='mailto:$email' title='" . sprintf( __('E-mail: %s' ), $email ) . "'>$email</a></td>";
				break;
			case 'role':
				$r .= "<td $attributes>$role_name</td>";
				break;
			case 'posts':
				$attributes = 'class="posts column-posts num"' . $style;
				$r .= "<td $attributes>";
				if ( $numposts > 0 ) {
					$r .= "<a href='edit.php?author=$user_object->ID' title='" . __( 'View posts by this author' ) . "' class='edit'>";
					$r .= $numposts;
					$r .= '</a>';
				} else {
					$r .= 0;
				}
				$r .= "</td>";
				break;
			default:
				$r .= "<td $attributes>";
				$r .= apply_filters('manage_users_custom_column', '', $column_name, $user_object->ID);
				$r .= "</td>";
		}
	}
	$r .= '</tr>';

	return $r;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param string $status Comment status (approved, spam, trash, etc)
 * @param string $s Term to search for
 * @param int $start Offset to start at for pagination
 * @param int $num Maximum number of comments to return
 * @param int $post Post ID or 0 to return all comments
 * @param string $type Comment type (comment, trackback, pingback, etc)
 * @return array [0] contains the comments and [1] contains the total number of comments that match (ignoring $start and $num)
 */
function _wp_get_comment_list( $status = '', $s = false, $start, $num, $post = 0, $type = '' ) {
	global $wpdb;

	$start = abs( (int) $start );
	$num = (int) $num;
	$post = (int) $post;
	$count = wp_count_comments();
	$index = '';

	if ( 'moderated' == $status ) {
		$approved = "c.comment_approved = '0'";
		$total = $count->moderated;
	} elseif ( 'approved' == $status ) {
		$approved = "c.comment_approved = '1'";
		$total = $count->approved;
	} elseif ( 'spam' == $status ) {
		$approved = "c.comment_approved = 'spam'";
		$total = $count->spam;
	} elseif ( 'trash' == $status ) {
		$approved = "c.comment_approved = 'trash'";
		$total = $count->trash;
	} else {
		$approved = "( c.comment_approved = '0' OR c.comment_approved = '1' )";
		$total = $count->moderated + $count->approved;
		$index = 'USE INDEX (c.comment_date_gmt)';
	}

	if ( $post ) {
		$total = '';
		$post = " AND c.comment_post_ID = '$post'";
	} else {
		$post = '';
	}

	$orderby = "ORDER BY c.comment_date_gmt DESC LIMIT $start, $num";

	if ( 'comment' == $type )
		$typesql = "AND c.comment_type = ''";
	elseif ( 'pings' == $type )
		$typesql = "AND ( c.comment_type = 'pingback' OR c.comment_type = 'trackback' )";
	elseif ( 'all' == $type )
		$typesql = '';
	elseif ( !empty($type) )
		$typesql = $wpdb->prepare("AND c.comment_type = %s", $type);
	else
		$typesql = '';

	if ( !empty($type) )
		$total = '';

	$query = "FROM $wpdb->comments c LEFT JOIN $wpdb->posts p ON c.comment_post_ID = p.ID WHERE p.post_status != 'trash' ";
	if ( $s ) {
		$total = '';
		$s = $wpdb->escape($s);
		$query .= "AND
			(c.comment_author LIKE '%$s%' OR
			c.comment_author_email LIKE '%$s%' OR
			c.comment_author_url LIKE ('%$s%') OR
			c.comment_author_IP LIKE ('%$s%') OR
			c.comment_content LIKE ('%$s%') ) AND
			$approved
			$typesql";
	} else {
		$query .= "AND $approved $post $typesql";
	}

	$comments = $wpdb->get_results("SELECT * $query $orderby");
	if ( '' === $total )
		$total = $wpdb->get_var("SELECT COUNT(c.comment_ID) $query");

	update_comment_cache($comments);

	return array($comments, $total);
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $comment_id
 * @param unknown_type $mode
 * @param unknown_type $comment_status
 * @param unknown_type $checkbox
 */
function _wp_comment_row( $comment_id, $mode, $comment_status, $checkbox = true, $from_ajax = false ) {
	global $comment, $post, $_comment_pending_count;
	$comment = get_comment( $comment_id );
	$post = get_post($comment->comment_post_ID);
	$the_comment_status = wp_get_comment_status($comment->comment_ID);
	$post_type_object = get_post_type_object($post->post_type);
	$user_can = current_user_can($post_type_object->cap->edit_post, $post->ID);

	$comment_url = esc_url(get_comment_link($comment->comment_ID));
	$author_url = get_comment_author_url();
	if ( 'http://' == $author_url )
		$author_url = '';
	$author_url_display = preg_replace('|http://(www\.)?|i', '', $author_url);
	if ( strlen($author_url_display) > 50 )
		$author_url_display = substr($author_url_display, 0, 49) . '...';

	$ptime = date('G', strtotime( $comment->comment_date ) );
	if ( ( abs(time() - $ptime) ) < 86400 )
		$ptime = sprintf( __('%s ago'), human_time_diff( $ptime ) );
	else
		$ptime = mysql2date(__('Y/m/d \a\t g:i A'), $comment->comment_date );

	if ( $user_can ) {
		$del_nonce = esc_html( '_wpnonce=' . wp_create_nonce( "delete-comment_$comment->comment_ID" ) );
		$approve_nonce = esc_html( '_wpnonce=' . wp_create_nonce( "approve-comment_$comment->comment_ID" ) );

		$approve_url = esc_url( "comment.php?action=approvecomment&p=$post->ID&c=$comment->comment_ID&$approve_nonce" );
		$unapprove_url = esc_url( "comment.php?action=unapprovecomment&p=$post->ID&c=$comment->comment_ID&$approve_nonce" );
		$spam_url = esc_url( "comment.php?action=spamcomment&p=$post->ID&c=$comment->comment_ID&$del_nonce" );
		$unspam_url = esc_url( "comment.php?action=unspamcomment&p=$post->ID&c=$comment->comment_ID&$del_nonce" );
		$trash_url = esc_url( "comment.php?action=trashcomment&p=$post->ID&c=$comment->comment_ID&$del_nonce" );
		$untrash_url = esc_url( "comment.php?action=untrashcomment&p=$post->ID&c=$comment->comment_ID&$del_nonce" );
		$delete_url = esc_url( "comment.php?action=deletecomment&p=$post->ID&c=$comment->comment_ID&$del_nonce" );
	}

	echo "<tr id='comment-$comment->comment_ID' class='$the_comment_status'>";
	$columns = get_column_headers('edit-comments');
	$hidden = get_hidden_columns('edit-comments');
	foreach ( $columns as $column_name => $column_display_name ) {
		$class = "class=\"$column_name column-$column_name\"";

		$style = '';
		if ( in_array($column_name, $hidden) )
			$style = ' style="display:none;"';

		$attributes = "$class$style";

		switch ($column_name) {
			case 'cb':
				if ( !$checkbox ) break;
				echo '<th scope="row" class="check-column">';
				if ( $user_can ) echo "<input type='checkbox' name='delete_comments[]' value='$comment->comment_ID' />";
				echo '</th>';
				break;
			case 'comment':
				echo "<td $attributes>";
				echo '<div id="submitted-on">';
				/* translators: 2: comment date, 3: comment time */
				printf( __( '<a href="%1$s">%2$s at %3$s</a>' ), $comment_url,
					/* translators: comment date format. See http://php.net/date */ get_comment_date( __('Y/m/d') ),
					/* translators: comment time format. See http://php.net/date */ get_comment_date( get_option( 'time_format' ) ) );

				if ( $comment->comment_parent ) {
					$parent = get_comment( $comment->comment_parent );
					$parent_link = esc_url( get_comment_link( $comment->comment_parent ) );
					$name = apply_filters( 'get_comment_author', $parent->comment_author ); // there's no API function for this
					printf( ' | '.__( 'In reply to <a href="%1$s">%2$s</a>.' ), $parent_link, $name );
				}

				echo '</div>';
				comment_text();
				if ( $user_can ) { ?>
				<div id="inline-<?php echo $comment->comment_ID; ?>" class="hidden">
				<textarea class="comment" rows="1" cols="1"><?php echo htmlspecialchars( apply_filters('comment_edit_pre', $comment->comment_content), ENT_QUOTES ); ?></textarea>
				<div class="author-email"><?php echo esc_attr( $comment->comment_author_email ); ?></div>
				<div class="author"><?php echo esc_attr( $comment->comment_author ); ?></div>
				<div class="author-url"><?php echo esc_attr( $comment->comment_author_url ); ?></div>
				<div class="comment_status"><?php echo $comment->comment_approved; ?></div>
				</div>
				<?php
				}

				if ( $user_can ) {
					// preorder it: Approve | Reply | Quick Edit | Edit | Spam | Trash
					$actions = array(
						'approve' => '', 'unapprove' => '',
						'reply' => '',
						'quickedit' => '',
						'edit' => '',
						'spam' => '', 'unspam' => '',
						'trash' => '', 'untrash' => '', 'delete' => ''
					);

					if ( $comment_status && 'all' != $comment_status ) { // not looking at all comments
						if ( 'approved' == $the_comment_status )
							$actions['unapprove'] = "<a href='$unapprove_url' class='delete:the-comment-list:comment-$comment->comment_ID:e7e7d3:action=dim-comment&amp;new=unapproved vim-u vim-destructive' title='" . esc_attr__( 'Unapprove this comment' ) . "'>" . __( 'Unapprove' ) . '</a>';
						else if ( 'unapproved' == $the_comment_status )
							$actions['approve'] = "<a href='$approve_url' class='delete:the-comment-list:comment-$comment->comment_ID:e7e7d3:action=dim-comment&amp;new=approved vim-a vim-destructive' title='" . esc_attr__( 'Approve this comment' ) . "'>" . __( 'Approve' ) . '</a>';
					} else {
						$actions['approve'] = "<a href='$approve_url' class='dim:the-comment-list:comment-$comment->comment_ID:unapproved:e7e7d3:e7e7d3:new=approved vim-a' title='" . esc_attr__( 'Approve this comment' ) . "'>" . __( 'Approve' ) . '</a>';
						$actions['unapprove'] = "<a href='$unapprove_url' class='dim:the-comment-list:comment-$comment->comment_ID:unapproved:e7e7d3:e7e7d3:new=unapproved vim-u' title='" . esc_attr__( 'Unapprove this comment' ) . "'>" . __( 'Unapprove' ) . '</a>';
					}

					if ( 'spam' != $the_comment_status && 'trash' != $the_comment_status ) {
						$actions['spam'] = "<a href='$spam_url' class='delete:the-comment-list:comment-$comment->comment_ID::spam=1 vim-s vim-destructive' title='" . esc_attr__( 'Mark this comment as spam' ) . "'>" . /* translators: mark as spam link */ _x( 'Spam', 'verb' ) . '</a>';
					} elseif ( 'spam' == $the_comment_status ) {
						$actions['unspam'] = "<a href='$unspam_url' class='delete:the-comment-list:comment-$comment->comment_ID:66cc66:unspam=1 vim-z vim-destructive'>" . _x( 'Not Spam', 'comment' ) . '</a>';
					} elseif ( 'trash' == $the_comment_status ) {
						$actions['untrash'] = "<a href='$untrash_url' class='delete:the-comment-list:comment-$comment->comment_ID:66cc66:untrash=1 vim-z vim-destructive'>" . __( 'Restore' ) . '</a>';
					}

					if ( 'spam' == $the_comment_status || 'trash' == $the_comment_status || !EMPTY_TRASH_DAYS ) {
						$actions['delete'] = "<a href='$delete_url' class='delete:the-comment-list:comment-$comment->comment_ID::delete=1 delete vim-d vim-destructive'>" . __('Delete Permanently') . '</a>';
					} else {
						$actions['trash'] = "<a href='$trash_url' class='delete:the-comment-list:comment-$comment->comment_ID::trash=1 delete vim-d vim-destructive' title='" . esc_attr__( 'Move this comment to the trash' ) . "'>" . _x('Trash', 'verb') . '</a>';
					}

					if ( 'trash' != $the_comment_status ) {
						$actions['edit'] = "<a href='comment.php?action=editcomment&amp;c={$comment->comment_ID}' title='" . esc_attr__('Edit comment') . "'>". __('Edit') . '</a>';
						$actions['quickedit'] = '<a onclick="commentReply.open(\''.$comment->comment_ID.'\',\''.$post->ID.'\',\'edit\');return false;" class="vim-q" title="'.esc_attr__('Quick Edit').'" href="#">' . __('Quick&nbsp;Edit') . '</a>';
						if ( 'spam' != $the_comment_status )
							$actions['reply'] = '<a onclick="commentReply.open(\''.$comment->comment_ID.'\',\''.$post->ID.'\');return false;" class="vim-r" title="'.esc_attr__('Reply to this comment').'" href="#">' . __('Reply') . '</a>';
					}

					$actions = apply_filters( 'comment_row_actions', array_filter($actions), $comment );

					$i = 0;
					echo '<div class="row-actions">';
					foreach ( $actions as $action => $link ) {
						++$i;
						( ( ('approve' == $action || 'unapprove' == $action) && 2 === $i ) || 1 === $i ) ? $sep = '' : $sep = ' | ';

						// Reply and quickedit need a hide-if-no-js span when not added with ajax
						if ( ('reply' == $action || 'quickedit' == $action) && ! $from_ajax )
							$action .= ' hide-if-no-js';
						elseif ( ($action == 'untrash' && $the_comment_status == 'trash') || ($action == 'unspam' && $the_comment_status == 'spam') ) {
							if ('1' == get_comment_meta($comment_id, '_wp_trash_meta_status', true))
								$action .= ' approve';
							else
								$action .= ' unapprove';
						}

						echo "<span class='$action'>$sep$link</span>";
					}
					echo '</div>';
				}

				echo '</td>';
				break;
			case 'author':
				echo "<td $attributes><strong>"; comment_author(); echo '</strong><br />';
				if ( !empty($author_url) )
					echo "<a title='$author_url' href='$author_url'>$author_url_display</a><br />";
				if ( $user_can ) {
					if ( !empty($comment->comment_author_email) ) {
						comment_author_email_link();
						echo '<br />';
					}
					echo '<a href="edit-comments.php?s=';
					comment_author_IP();
					echo '&amp;mode=detail';
					if ( 'spam' == $comment_status )
						echo '&amp;comment_status=spam';
					echo '">';
					comment_author_IP();
					echo '</a>';
				} //current_user_can
				echo '</td>';
				break;
			case 'date':
				echo "<td $attributes>" . get_comment_date(__('Y/m/d \a\t g:ia')) . '</td>';
				break;
			case 'response':
				if ( 'single' !== $mode ) {
					if ( isset( $_comment_pending_count[$post->ID] ) ) {
						$pending_comments = $_comment_pending_count[$post->ID];
					} else {
						$_comment_pending_count_temp = get_pending_comments_num( array( $post->ID ) );
						$pending_comments = $_comment_pending_count[$post->ID] = $_comment_pending_count_temp[$post->ID];
					}
					if ( $user_can ) {
						$post_link = "<a href='" . get_edit_post_link($post->ID) . "'>";
						$post_link .= get_the_title($post->ID) . '</a>';
					} else {
						$post_link = get_the_title($post->ID);
					}
					echo "<td $attributes>\n";
					echo '<div class="response-links"><span class="post-com-count-wrapper">';
					echo $post_link . '<br />';
					$pending_phrase = esc_attr(sprintf( __('%s pending'), number_format( $pending_comments ) ));
					if ( $pending_comments )
						echo '<strong>';
					comments_number("<a href='edit-comments.php?p=$post->ID' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . /* translators: comment count link */ _x('0', 'comment count') . '</span></a>', "<a href='edit-comments.php?p=$post->ID' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . /* translators: comment count link */ _x('1', 'comment count') . '</span></a>', "<a href='edit-comments.php?p=$post->ID' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . /* translators: comment count link: % will be substituted by comment count */ _x('%', 'comment count') . '</span></a>');
					if ( $pending_comments )
						echo '</strong>';
					echo '</span> ';
					echo "<a href='" . get_permalink( $post->ID ) . "'>#</a>";
					echo '</div>';
					if ( 'attachment' == $post->post_type && ( $thumb = wp_get_attachment_image( $post->ID, array(80, 60), true ) ) )
						echo $thumb;
					echo '</td>';
				}
				break;
			default:
				echo "<td $attributes>\n";
				do_action( 'manage_comments_custom_column', $column_name, $comment->comment_ID );
				echo "</td>\n";
				break;
		}
	}
	echo "</tr>\n";
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $position
 * @param unknown_type $checkbox
 * @param unknown_type $mode
 */
function wp_comment_reply($position = '1', $checkbox = false, $mode = 'single', $table_row = true) {
	global $current_user;

	// allow plugin to replace the popup content
	$content = apply_filters( 'wp_comment_reply', '', array('position' => $position, 'checkbox' => $checkbox, 'mode' => $mode) );

	if ( ! empty($content) ) {
		echo $content;
		return;
	}

	$columns = get_column_headers('edit-comments');
	$hidden = array_intersect( array_keys( $columns ), array_filter( get_hidden_columns('edit-comments') ) );
	$col_count = count($columns) - count($hidden);

?>
<form method="get" action="">
<?php if ( $table_row ) : ?>
<table style="display:none;"><tbody id="com-reply"><tr id="replyrow" style="display:none;"><td colspan="<?php echo $col_count; ?>">
<?php else : ?>
<div id="com-reply" style="display:none;"><div id="replyrow" style="display:none;">
<?php endif; ?>
	<div id="replyhead" style="display:none;"><?php _e('Reply to Comment'); ?></div>

	<div id="edithead" style="display:none;">
		<div class="inside">
		<label for="author"><?php _e('Name') ?></label>
		<input type="text" name="newcomment_author" size="50" value="" tabindex="101" id="author" />
		</div>

		<div class="inside">
		<label for="author-email"><?php _e('E-mail') ?></label>
		<input type="text" name="newcomment_author_email" size="50" value="" tabindex="102" id="author-email" />
		</div>

		<div class="inside">
		<label for="author-url"><?php _e('URL') ?></label>
		<input type="text" id="author-url" name="newcomment_author_url" size="103" value="" tabindex="103" />
		</div>
		<div style="clear:both;"></div>
	</div>

	<div id="replycontainer"><textarea rows="8" cols="40" name="replycontent" tabindex="104" id="replycontent"></textarea></div>

	<p id="replysubmit" class="submit">
	<a href="#comments-form" class="cancel button-secondary alignleft" tabindex="106"><?php _e('Cancel'); ?></a>
	<a href="#comments-form" class="save button-primary alignright" tabindex="104">
	<span id="savebtn" style="display:none;"><?php _e('Update Comment'); ?></span>
	<span id="replybtn" style="display:none;"><?php _e('Submit Reply'); ?></span></a>
	<img class="waiting" style="display:none;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
	<span class="error" style="display:none;"></span>
	<br class="clear" />
	</p>

	<input type="hidden" name="user_ID" id="user_ID" value="<?php echo $current_user->ID; ?>" />
	<input type="hidden" name="action" id="action" value="" />
	<input type="hidden" name="comment_ID" id="comment_ID" value="" />
	<input type="hidden" name="comment_post_ID" id="comment_post_ID" value="" />
	<input type="hidden" name="status" id="status" value="" />
	<input type="hidden" name="position" id="position" value="<?php echo $position; ?>" />
	<input type="hidden" name="checkbox" id="checkbox" value="<?php echo $checkbox ? 1 : 0; ?>" />
	<input type="hidden" name="mode" id="mode" value="<?php echo esc_attr($mode); ?>" />
	<?php wp_nonce_field( 'replyto-comment', '_ajax_nonce', false ); ?>
	<?php wp_comment_form_unfiltered_html_nonce(); ?>
<?php if ( $table_row ) : ?>
</td></tr></tbody></table>
<?php else : ?>
</div></div>
<?php endif; ?>
</form>
<?php
}

/**
 * Output 'undo move to trash' text for comments
 *
 * @since 2.9.0
 */
function wp_comment_trashnotice() {
?>
<div class="hidden" id="trash-undo-holder">
	<div class="trash-undo-inside"><?php printf(__('Comment by %s moved to the trash.'), '<strong></strong>'); ?> <span class="undo untrash"><a href="#"><?php _e('Undo'); ?></a></span></div>
</div>
<div class="hidden" id="spam-undo-holder">
	<div class="spam-undo-inside"><?php printf(__('Comment by %s marked as spam.'), '<strong></strong>'); ?> <span class="undo unspam"><a href="#"><?php _e('Undo'); ?></a></span></div>
</div>
<?php
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $meta
 */
function list_meta( $meta ) {
	// Exit if no meta
	if ( ! $meta ) {
		echo '
<table id="list-table" style="display: none;">
	<thead>
	<tr>
		<th class="left">' . __( 'Name' ) . '</th>
		<th>' . __( 'Value' ) . '</th>
	</tr>
	</thead>
	<tbody id="the-list" class="list:meta">
	<tr><td></td></tr>
	</tbody>
</table>'; //TBODY needed for list-manipulation JS
		return;
	}
	$count = 0;
?>
<table id="list-table">
	<thead>
	<tr>
		<th class="left"><?php _e( 'Name' ) ?></th>
		<th><?php _e( 'Value' ) ?></th>
	</tr>
	</thead>
	<tbody id='the-list' class='list:meta'>
<?php
	foreach ( $meta as $entry )
		echo _list_meta_row( $entry, $count );
?>
	</tbody>
</table>
<?php
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $entry
 * @param unknown_type $count
 * @return unknown
 */
function _list_meta_row( $entry, &$count ) {
	static $update_nonce = false;
	if ( !$update_nonce )
		$update_nonce = wp_create_nonce( 'add-meta' );

	$r = '';
	++ $count;
	if ( $count % 2 )
		$style = 'alternate';
	else
		$style = '';
	if ('_' == $entry['meta_key'] { 0 } )
		$style .= ' hidden';

	if ( is_serialized( $entry['meta_value'] ) ) {
		if ( is_serialized_string( $entry['meta_value'] ) ) {
			// this is a serialized string, so we should display it
			$entry['meta_value'] = maybe_unserialize( $entry['meta_value'] );
		} else {
			// this is a serialized array/object so we should NOT display it
			--$count;
			return;
		}
	}

	$entry['meta_key'] = esc_attr($entry['meta_key']);
	$entry['meta_value'] = htmlspecialchars($entry['meta_value']); // using a <textarea />
	$entry['meta_id'] = (int) $entry['meta_id'];

	$delete_nonce = wp_create_nonce( 'delete-meta_' . $entry['meta_id'] );

	$r .= "\n\t<tr id='meta-{$entry['meta_id']}' class='$style'>";
	$r .= "\n\t\t<td class='left'><label class='screen-reader-text' for='meta[{$entry['meta_id']}][key]'>" . __( 'Key' ) . "</label><input name='meta[{$entry['meta_id']}][key]' id='meta[{$entry['meta_id']}][key]' tabindex='6' type='text' size='20' value='{$entry['meta_key']}' />";

	$r .= "\n\t\t<div class='submit'><input name='deletemeta[{$entry['meta_id']}]' type='submit' ";
	$r .= "class='delete:the-list:meta-{$entry['meta_id']}::_ajax_nonce=$delete_nonce deletemeta' tabindex='6' value='". esc_attr__( 'Delete' ) ."' />";
	$r .= "\n\t\t<input name='updatemeta' type='submit' tabindex='6' value='". esc_attr__( 'Update' ) ."' class='add:the-list:meta-{$entry['meta_id']}::_ajax_nonce=$update_nonce updatemeta' /></div>";
	$r .= wp_nonce_field( 'change-meta', '_ajax_nonce', false, false );
	$r .= "</td>";

	$r .= "\n\t\t<td><label class='screen-reader-text' for='meta[{$entry['meta_id']}][value]'>" . __( 'Value' ) . "</label><textarea name='meta[{$entry['meta_id']}][value]' id='meta[{$entry['meta_id']}][value]' tabindex='6' rows='2' cols='30'>{$entry['meta_value']}</textarea></td>\n\t</tr>";
	return $r;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 */
function meta_form() {
	global $wpdb;
	$limit = (int) apply_filters( 'postmeta_form_limit', 30 );
	$keys = $wpdb->get_col( "
		SELECT meta_key
		FROM $wpdb->postmeta
		GROUP BY meta_key
		HAVING meta_key NOT LIKE '\_%'
		ORDER BY meta_key
		LIMIT $limit" );
	if ( $keys )
		natcasesort($keys);
?>
<p><strong><?php _e( 'Add New Custom Field:' ) ?></strong></p>
<table id="newmeta">
<thead>
<tr>
<th class="left"><label for="metakeyselect"><?php _e( 'Name' ) ?></label></th>
<th><label for="metavalue"><?php _e( 'Value' ) ?></label></th>
</tr>
</thead>

<tbody>
<tr>
<td id="newmetaleft" class="left">
<?php if ( $keys ) { ?>
<select id="metakeyselect" name="metakeyselect" tabindex="7">
<option value="#NONE#"><?php _e( '&mdash; Select &mdash;' ); ?></option>
<?php

	foreach ( $keys as $key ) {
		echo "\n<option value='" . esc_attr($key) . "'>" . esc_html($key) . "</option>";
	}
?>
</select>
<input class="hide-if-js" type="text" id="metakeyinput" name="metakeyinput" tabindex="7" value="" />
<a href="#postcustomstuff" class="hide-if-no-js" onclick="jQuery('#metakeyinput, #metakeyselect, #enternew, #cancelnew').toggle();return false;">
<span id="enternew"><?php _e('Enter new'); ?></span>
<span id="cancelnew" class="hidden"><?php _e('Cancel'); ?></span></a>
<?php } else { ?>
<input type="text" id="metakeyinput" name="metakeyinput" tabindex="7" value="" />
<?php } ?>
</td>
<td><textarea id="metavalue" name="metavalue" rows="2" cols="25" tabindex="8"></textarea></td>
</tr>

<tr><td colspan="2" class="submit">
<input type="submit" id="addmetasub" name="addmeta" class="add:the-list:newmeta" tabindex="9" value="<?php esc_attr_e( 'Add Custom Field' ) ?>" />
<?php wp_nonce_field( 'add-meta', '_ajax_nonce', false ); ?>
</td></tr>
</tbody>
</table>
<?php

}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $edit
 * @param unknown_type $for_post
 * @param unknown_type $tab_index
 * @param unknown_type $multi
 */
function touch_time( $edit = 1, $for_post = 1, $tab_index = 0, $multi = 0 ) {
	global $wp_locale, $post, $comment;

	if ( $for_post )
		$edit = ! ( in_array($post->post_status, array('draft', 'pending') ) && (!$post->post_date_gmt || '0000-00-00 00:00:00' == $post->post_date_gmt ) );

	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 )
		$tab_index_attribute = " tabindex=\"$tab_index\"";

	// echo '<label for="timestamp" style="display: block;"><input type="checkbox" class="checkbox" name="edit_date" value="1" id="timestamp"'.$tab_index_attribute.' /> '.__( 'Edit timestamp' ).'</label><br />';

	$time_adj = current_time('timestamp');
	$post_date = ($for_post) ? $post->post_date : $comment->comment_date;
	$jj = ($edit) ? mysql2date( 'd', $post_date, false ) : gmdate( 'd', $time_adj );
	$mm = ($edit) ? mysql2date( 'm', $post_date, false ) : gmdate( 'm', $time_adj );
	$aa = ($edit) ? mysql2date( 'Y', $post_date, false ) : gmdate( 'Y', $time_adj );
	$hh = ($edit) ? mysql2date( 'H', $post_date, false ) : gmdate( 'H', $time_adj );
	$mn = ($edit) ? mysql2date( 'i', $post_date, false ) : gmdate( 'i', $time_adj );
	$ss = ($edit) ? mysql2date( 's', $post_date, false ) : gmdate( 's', $time_adj );

	$cur_jj = gmdate( 'd', $time_adj );
	$cur_mm = gmdate( 'm', $time_adj );
	$cur_aa = gmdate( 'Y', $time_adj );
	$cur_hh = gmdate( 'H', $time_adj );
	$cur_mn = gmdate( 'i', $time_adj );

	$month = "<select " . ( $multi ? '' : 'id="mm" ' ) . "name=\"mm\"$tab_index_attribute>\n";
	for ( $i = 1; $i < 13; $i = $i +1 ) {
		$month .= "\t\t\t" . '<option value="' . zeroise($i, 2) . '"';
		if ( $i == $mm )
			$month .= ' selected="selected"';
		$month .= '>' . $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) . "</option>\n";
	}
	$month .= '</select>';

	$day = '<input type="text" ' . ( $multi ? '' : 'id="jj" ' ) . 'name="jj" value="' . $jj . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';
	$year = '<input type="text" ' . ( $multi ? '' : 'id="aa" ' ) . 'name="aa" value="' . $aa . '" size="4" maxlength="4"' . $tab_index_attribute . ' autocomplete="off" />';
	$hour = '<input type="text" ' . ( $multi ? '' : 'id="hh" ' ) . 'name="hh" value="' . $hh . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';
	$minute = '<input type="text" ' . ( $multi ? '' : 'id="mn" ' ) . 'name="mn" value="' . $mn . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';

	echo '<div class="timestamp-wrap">';
	/* translators: 1: month input, 2: day input, 3: year input, 4: hour input, 5: minute input */
	printf(__('%1$s%2$s, %3$s @ %4$s : %5$s'), $month, $day, $year, $hour, $minute);

	echo '</div><input type="hidden" id="ss" name="ss" value="' . $ss . '" />';

	if ( $multi ) return;

	echo "\n\n";
	foreach ( array('mm', 'jj', 'aa', 'hh', 'mn') as $timeunit ) {
		echo '<input type="hidden" id="hidden_' . $timeunit . '" name="hidden_' . $timeunit . '" value="' . $$timeunit . '" />' . "\n";
		$cur_timeunit = 'cur_' . $timeunit;
		echo '<input type="hidden" id="'. $cur_timeunit . '" name="'. $cur_timeunit . '" value="' . $$cur_timeunit . '" />' . "\n";
	}
?>

<p>
<a href="#edit_timestamp" class="save-timestamp hide-if-no-js button"><?php _e('OK'); ?></a>
<a href="#edit_timestamp" class="cancel-timestamp hide-if-no-js"><?php _e('Cancel'); ?></a>
</p>
<?php
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $default
 */
function page_template_dropdown( $default = '' ) {
	$templates = get_page_templates();
	ksort( $templates );
	foreach (array_keys( $templates ) as $template )
		: if ( $default == $templates[$template] )
			$selected = " selected='selected'";
		else
			$selected = '';
	echo "\n\t<option value='".$templates[$template]."' $selected>$template</option>";
	endforeach;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $default
 * @param unknown_type $parent
 * @param unknown_type $level
 * @return unknown
 */
function parent_dropdown( $default = 0, $parent = 0, $level = 0 ) {
	global $wpdb, $post_ID;
	$items = $wpdb->get_results( $wpdb->prepare("SELECT ID, post_parent, post_title FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'page' ORDER BY menu_order", $parent) );

	if ( $items ) {
		foreach ( $items as $item ) {
			// A page cannot be its own parent.
			if (!empty ( $post_ID ) ) {
				if ( $item->ID == $post_ID ) {
					continue;
				}
			}
			$pad = str_repeat( '&nbsp;', $level * 3 );
			if ( $item->ID == $default)
				$current = ' selected="selected"';
			else
				$current = '';

			echo "\n\t<option class='level-$level' value='$item->ID'$current>$pad " . esc_html($item->post_title) . "</option>";
			parent_dropdown( $default, $item->ID, $level +1 );
		}
	} else {
		return false;
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $id
 * @return unknown
 */
function the_attachment_links( $id = false ) {
	$id = (int) $id;
	$post = & get_post( $id );

	if ( $post->post_type != 'attachment' )
		return false;

	$icon = wp_get_attachment_image( $post->ID, 'thumbnail', true );
	$attachment_data = wp_get_attachment_metadata( $id );
	$thumb = isset( $attachment_data['thumb'] );
?>
<form id="the-attachment-links">
<table>
	<col />
	<col class="widefat" />
	<tr>
		<th scope="row"><?php _e( 'URL' ) ?></th>
		<td><textarea rows="1" cols="40" type="text" class="attachmentlinks" readonly="readonly"><?php echo wp_get_attachment_url(); ?></textarea></td>
	</tr>
<?php if ( $icon ) : ?>
	<tr>
		<th scope="row"><?php $thumb ? _e( 'Thumbnail linked to file' ) : _e( 'Image linked to file' ); ?></th>
		<td><textarea rows="1" cols="40" type="text" class="attachmentlinks" readonly="readonly"><a href="<?php echo wp_get_attachment_url(); ?>"><?php echo $icon ?></a></textarea></td>
	</tr>
	<tr>
		<th scope="row"><?php $thumb ? _e( 'Thumbnail linked to page' ) : _e( 'Image linked to page' ); ?></th>
		<td><textarea rows="1" cols="40" type="text" class="attachmentlinks" readonly="readonly"><a href="<?php echo get_attachment_link( $post->ID ) ?>" rel="attachment wp-att-<?php echo $post->ID; ?>"><?php echo $icon ?></a></textarea></td>
	</tr>
<?php else : ?>
	<tr>
		<th scope="row"><?php _e( 'Link to file' ) ?></th>
		<td><textarea rows="1" cols="40" type="text" class="attachmentlinks" readonly="readonly"><a href="<?php echo wp_get_attachment_url(); ?>" class="attachmentlink"><?php echo basename( wp_get_attachment_url() ); ?></a></textarea></td>
	</tr>
	<tr>
		<th scope="row"><?php _e( 'Link to page' ) ?></th>
		<td><textarea rows="1" cols="40" type="text" class="attachmentlinks" readonly="readonly"><a href="<?php echo get_attachment_link( $post->ID ) ?>" rel="attachment wp-att-<?php echo $post->ID ?>"><?php the_title(); ?></a></textarea></td>
	</tr>
<?php endif; ?>
</table>
</form>
<?php
}


/**
 * Print out <option> html elements for role selectors based on $wp_roles
 *
 * @package WordPress
 * @subpackage Administration
 * @since 2.1
 *
 * @uses $wp_roles
 * @param string $default slug for the role that should be already selected
 */
function wp_dropdown_roles( $selected = false ) {
	$p = '';
	$r = '';

	$editable_roles = get_editable_roles();

	foreach ( $editable_roles as $role => $details ) {
		$name = translate_user_role($details['name'] );
		if ( $selected == $role ) // Make default first in list
			$p = "\n\t<option selected='selected' value='" . esc_attr($role) . "'>$name</option>";
		else
			$r .= "\n\t<option value='" . esc_attr($role) . "'>$name</option>";
	}
	echo $p . $r;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $size
 * @return unknown
 */
function wp_convert_hr_to_bytes( $size ) {
	$size = strtolower($size);
	$bytes = (int) $size;
	if ( strpos($size, 'k') !== false )
		$bytes = intval($size) * 1024;
	elseif ( strpos($size, 'm') !== false )
		$bytes = intval($size) * 1024 * 1024;
	elseif ( strpos($size, 'g') !== false )
		$bytes = intval($size) * 1024 * 1024 * 1024;
	return $bytes;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $bytes
 * @return unknown
 */
function wp_convert_bytes_to_hr( $bytes ) {
	$units = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
	$log = log( $bytes, 1024 );
	$power = (int) $log;
	$size = pow(1024, $log - $power);
	return $size . $units[$power];
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function wp_max_upload_size() {
	$u_bytes = wp_convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) );
	$p_bytes = wp_convert_hr_to_bytes( ini_get( 'post_max_size' ) );
	$bytes = apply_filters( 'upload_size_limit', min($u_bytes, $p_bytes), $u_bytes, $p_bytes );
	return $bytes;
}

/**
 * Outputs the form used by the importers to accept the data to be imported
 *
 * @since 2.0
 *
 * @param string $action The action attribute for the form.
 */
function wp_import_upload_form( $action ) {
	$bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
	$size = wp_convert_bytes_to_hr( $bytes );
	$upload_dir = wp_upload_dir();
	if ( ! empty( $upload_dir['error'] ) ) :
		?><div class="error"><p><?php _e('Before you can upload your import file, you will need to fix the following error:'); ?></p>
		<p><strong><?php echo $upload_dir['error']; ?></strong></p></div><?php
	else :
?>
<form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo esc_attr(wp_nonce_url($action, 'import-upload')); ?>">
<p>
<label for="upload"><?php _e( 'Choose a file from your computer:' ); ?></label> (<?php printf( __('Maximum size: %s' ), $size ); ?>)
<input type="file" id="upload" name="import" size="25" />
<input type="hidden" name="action" value="save" />
<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
</p>
<p class="submit">
<input type="submit" class="button" value="<?php esc_attr_e( 'Upload file and import' ); ?>" />
</p>
</form>
<?php
	endif;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 */
function wp_remember_old_slug() {
	global $post;
	$name = esc_attr($post->post_name); // just in case
	if ( strlen($name) )
		echo '<input type="hidden" id="wp-old-slug" name="wp-old-slug" value="' . $name . '" />';
}

/**
 * Add a meta box to an edit form.
 *
 * @since 2.5.0
 *
 * @param string $id String for use in the 'id' attribute of tags.
 * @param string $title Title of the meta box.
 * @param string $callback Function that fills the box with the desired content. The function should echo its output.
 * @param string $page The type of edit page on which to show the box (post, page, link).
 * @param string $context The context within the page where the boxes should show ('normal', 'advanced').
 * @param string $priority The priority within the context where the boxes should show ('high', 'low').
 */
function add_meta_box($id, $title, $callback, $page, $context = 'advanced', $priority = 'default', $callback_args=null) {
	global $wp_meta_boxes;

	if ( !isset($wp_meta_boxes) )
		$wp_meta_boxes = array();
	if ( !isset($wp_meta_boxes[$page]) )
		$wp_meta_boxes[$page] = array();
	if ( !isset($wp_meta_boxes[$page][$context]) )
		$wp_meta_boxes[$page][$context] = array();

	foreach ( array_keys($wp_meta_boxes[$page]) as $a_context ) {
	foreach ( array('high', 'core', 'default', 'low') as $a_priority ) {
		if ( !isset($wp_meta_boxes[$page][$a_context][$a_priority][$id]) )
			continue;

		// If a core box was previously added or removed by a plugin, don't add.
		if ( 'core' == $priority ) {
			// If core box previously deleted, don't add
			if ( false === $wp_meta_boxes[$page][$a_context][$a_priority][$id] )
				return;
			// If box was added with default priority, give it core priority to maintain sort order
			if ( 'default' == $a_priority ) {
				$wp_meta_boxes[$page][$a_context]['core'][$id] = $wp_meta_boxes[$page][$a_context]['default'][$id];
				unset($wp_meta_boxes[$page][$a_context]['default'][$id]);
			}
			return;
		}
		// If no priority given and id already present, use existing priority
		if ( empty($priority) ) {
			$priority = $a_priority;
		// else if we're adding to the sorted priortiy, we don't know the title or callback. Glab them from the previously added context/priority.
		} elseif ( 'sorted' == $priority ) {
			$title = $wp_meta_boxes[$page][$a_context][$a_priority][$id]['title'];
			$callback = $wp_meta_boxes[$page][$a_context][$a_priority][$id]['callback'];
			$callback_args = $wp_meta_boxes[$page][$a_context][$a_priority][$id]['args'];
		}
		// An id can be in only one priority and one context
		if ( $priority != $a_priority || $context != $a_context )
			unset($wp_meta_boxes[$page][$a_context][$a_priority][$id]);
	}
	}

	if ( empty($priority) )
		$priority = 'low';

	if ( !isset($wp_meta_boxes[$page][$context][$priority]) )
		$wp_meta_boxes[$page][$context][$priority] = array();

	$wp_meta_boxes[$page][$context][$priority][$id] = array('id' => $id, 'title' => $title, 'callback' => $callback, 'args' => $callback_args);
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $page
 * @param unknown_type $context
 * @param unknown_type $object
 * @return int number of meta_boxes
 */
function do_meta_boxes($page, $context, $object) {
	global $wp_meta_boxes;
	static $already_sorted = false;

	//do_action('do_meta_boxes', $page, $context, $object);

	$hidden = get_hidden_meta_boxes($page);

	echo "<div id='$context-sortables' class='meta-box-sortables'>\n";

	$i = 0;
	do {
		// Grab the ones the user has manually sorted. Pull them out of their previous context/priority and into the one the user chose
		if ( !$already_sorted && $sorted = get_user_option( "meta-box-order_$page" ) ) {
			foreach ( $sorted as $box_context => $ids )
				foreach ( explode(',', $ids) as $id )
					if ( $id )
						add_meta_box( $id, null, null, $page, $box_context, 'sorted' );
		}
		$already_sorted = true;

		if ( !isset($wp_meta_boxes) || !isset($wp_meta_boxes[$page]) || !isset($wp_meta_boxes[$page][$context]) )
			break;

		foreach ( array('high', 'sorted', 'core', 'default', 'low') as $priority ) {
			if ( isset($wp_meta_boxes[$page][$context][$priority]) ) {
				foreach ( (array) $wp_meta_boxes[$page][$context][$priority] as $box ) {
					if ( false == $box || ! $box['title'] )
						continue;
					$i++;
					$style = '';
					$hidden_class = in_array($box['id'], $hidden) ? ' hide-if-js' : '';
					echo '<div id="' . $box['id'] . '" class="postbox ' . postbox_classes($box['id'], $page) . $hidden_class . '" ' . '>' . "\n";
					echo '<div class="handlediv" title="' . __('Click to toggle') . '"><br /></div>';
					echo "<h3 class='hndle'><span>{$box['title']}</span></h3>\n";
					echo '<div class="inside">' . "\n";
					call_user_func($box['callback'], $object, $box);
					echo "</div>\n";
					echo "</div>\n";
				}
			}
		}
	} while(0);

	echo "</div>";

	return $i;

}

/**
 * Remove a meta box from an edit form.
 *
 * @since 2.6.0
 *
 * @param string $id String for use in the 'id' attribute of tags.
 * @param string $page The type of edit page on which to show the box (post, page, link).
 * @param string $context The context within the page where the boxes should show ('normal', 'advanced').
 */
function remove_meta_box($id, $page, $context) {
	global $wp_meta_boxes;

	if ( !isset($wp_meta_boxes) )
		$wp_meta_boxes = array();
	if ( !isset($wp_meta_boxes[$page]) )
		$wp_meta_boxes[$page] = array();
	if ( !isset($wp_meta_boxes[$page][$context]) )
		$wp_meta_boxes[$page][$context] = array();

	foreach ( array('high', 'core', 'default', 'low') as $priority )
		$wp_meta_boxes[$page][$context][$priority][$id] = false;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $screen
 */
function meta_box_prefs($screen) {
	global $wp_meta_boxes;

	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	if ( empty($wp_meta_boxes[$screen->id]) )
		return;

	$hidden = get_hidden_meta_boxes($screen);

	foreach ( array_keys($wp_meta_boxes[$screen->id]) as $context ) {
		foreach ( array_keys($wp_meta_boxes[$screen->id][$context]) as $priority ) {
			foreach ( $wp_meta_boxes[$screen->id][$context][$priority] as $box ) {
				if ( false == $box || ! $box['title'] )
					continue;
				// Submit box cannot be hidden
				if ( 'submitdiv' == $box['id'] || 'linksubmitdiv' == $box['id'] )
					continue;
				$box_id = $box['id'];
				echo '<label for="' . $box_id . '-hide">';
				echo '<input class="hide-postbox-tog" name="' . $box_id . '-hide" type="checkbox" id="' . $box_id . '-hide" value="' . $box_id . '"' . (! in_array($box_id, $hidden) ? ' checked="checked"' : '') . ' />';
				echo "{$box['title']}</label>\n";
			}
		}
	}
}

function get_hidden_meta_boxes($screen) {
	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	$hidden = get_user_option( "metaboxhidden_$screen->id" );

	// Hide slug boxes by default
	if ( !is_array($hidden) )
		$hidden = array('slugdiv');

	return $hidden;
}

/**
 * Add a new section to a settings page.
 *
 * Part of the Settings API. Use this to define new settings sections for an admin page.
 * Show settings sections in your admin page callback function with do_settings_sections().
 * Add settings fields to your section with add_settings_field()
 *
 * The $callback argument should be the name of a function that echos out any
 * content you want to show at the top of the settings section before the actual
 * fields. It can output nothing if you want.
 *
 * @since 2.7.0
 *
 * @global $wp_settings_sections Storage array of all settings sections added to admin pages

 * @param string $id Slug-name to identify the section. Used in the 'id' attribute of tags.
 * @param string $title Formatted title of the section. Shown as the heading for the section.
 * @param string $callback Function that echo's out content for the section heading.
 * @param string $page The slug-name of the settings page on which to show the section (general, reading, writing, ...).
 */
function add_settings_section($id, $title, $callback, $page) {
	global $wp_settings_sections;

	if ( 'misc' == $page ) {
		_deprecated_argument( __FUNCTION__, '3.0', __( 'The miscellaneous options group has been removed. Use another settings group.' ) );
		$page = 'general';
	}

	if ( !isset($wp_settings_sections) )
		$wp_settings_sections = array();
	if ( !isset($wp_settings_sections[$page]) )
		$wp_settings_sections[$page] = array();
	if ( !isset($wp_settings_sections[$page][$id]) )
		$wp_settings_sections[$page][$id] = array();

	$wp_settings_sections[$page][$id] = array('id' => $id, 'title' => $title, 'callback' => $callback);
}

/**
 * Add a new field to a section of a settings page
 *
 * Part of the Settings API. Use this to define a settings field that will show
 * as part of a settings section inside a settings page. The fields are shown using
 * do_settings_fields() in do_settings-sections()
 *
 * The $callback argument should be the name of a function that echoes out the
 * html input tags for this setting field. Use get_option() to retrive existing
 * values to show.
 *
 * @since 2.7.0
 *
 * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections
 *
 * @param string $id Slug-name to identify the field. Used in the 'id' attribute of tags.
 * @param string $title Formatted title of the field. Shown as the label for the field during output.
 * @param string $callback Function that fills the field with the desired form inputs. The function should echo its output.
 * @param string $page The slug-name of the settings page on which to show the section (general, reading, writing, ...).
 * @param string $section The slug-name of the section of the settingss page in which to show the box (default, ...).
 * @param array $args Additional arguments
 */
function add_settings_field($id, $title, $callback, $page, $section = 'default', $args = array()) {
	global $wp_settings_fields;

	if ( 'misc' == $page ) {
		_deprecated_argument( __FUNCTION__, '3.0', __( 'The miscellaneous options group has been removed. Use another settings group.' ) );
		$page = 'general';
	}

	if ( !isset($wp_settings_fields) )
		$wp_settings_fields = array();
	if ( !isset($wp_settings_fields[$page]) )
		$wp_settings_fields[$page] = array();
	if ( !isset($wp_settings_fields[$page][$section]) )
		$wp_settings_fields[$page][$section] = array();

	$wp_settings_fields[$page][$section][$id] = array('id' => $id, 'title' => $title, 'callback' => $callback, 'args' => $args);
}

/**
 * Prints out all settings sections added to a particular settings page
 *
 * Part of the Settings API. Use this in a settings page callback function
 * to output all the sections and fields that were added to that $page with
 * add_settings_section() and add_settings_field()
 *
 * @global $wp_settings_sections Storage array of all settings sections added to admin pages
 * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections
 * @since unknown
 *
 * @param string $page The slug name of the page whos settings sections you want to output
 */
function do_settings_sections($page) {
	global $wp_settings_sections, $wp_settings_fields;

	if ( !isset($wp_settings_sections) || !isset($wp_settings_sections[$page]) )
		return;

	foreach ( (array) $wp_settings_sections[$page] as $section ) {
		echo "<h3>{$section['title']}</h3>\n";
		call_user_func($section['callback'], $section);
		if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']]) )
			continue;
		echo '<table class="form-table">';
		do_settings_fields($page, $section['id']);
		echo '</table>';
	}
}

/**
 * Print out the settings fields for a particular settings section
 *
 * Part of the Settings API. Use this in a settings page to output
 * a specific section. Should normally be called by do_settings_sections()
 * rather than directly.
 *
 * @global $wp_settings_fields Storage array of settings fields and their pages/sections
 *
 * @since unknown
 *
 * @param string $page Slug title of the admin page who's settings fields you want to show.
 * @param section $section Slug title of the settings section who's fields you want to show.
 */
function do_settings_fields($page, $section) {
	global $wp_settings_fields;

	if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section]) )
		return;

	foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
		echo '<tr valign="top">';
		if ( !empty($field['args']['label_for']) )
			echo '<th scope="row"><label for="' . $field['args']['label_for'] . '">' . $field['title'] . '</label></th>';
		else
			echo '<th scope="row">' . $field['title'] . '</th>';
		echo '<td>';
		call_user_func($field['callback'], $field['args']);
		echo '</td>';
		echo '</tr>';
	}
}

/**
 * Register a settings error to be displayed to the user
 *
 * Part of the Settings API. Use this to show messages to users about settings validation
 * problems, missing settings or anything else.
 *
 * Settings errors should be added inside the $sanitize_callback function defined in
 * register_setting() for a given setting to give feedback about the submission.
 *
 * By default messages will show immediately after the submission that generated the error.
 * Additional calls to settings_errors() can be used to show errors even when the settings
 * page is first accessed.
 *
 * @global array $wp_settings_errors Storage array of errors registered during this pageload
 *
 * @param string $setting Slug title of the setting to which this error applies
 * @param string $code Slug-name to identify the error. Used as part of 'id' attribute in HTML output.
 * @param string $message The formatted message text to display to the user (will be shown inside styled <div> and <p>)
 * @param string $type The type of message it is, controls HTML class. Use 'error' or 'updated'.
 */
function add_settings_error( $setting, $code, $message, $type = 'error' ) {
	global $wp_settings_errors;

	if ( !isset($wp_settings_errors) )
		$wp_settings_errors = array();

	$new_error = array(
		'setting' => $setting,
		'code' => $code,
		'message' => $message,
		'type' => $type
	);
	$wp_settings_errors[] = $new_error;
}

/**
 * Fetch settings errors registered by add_settings_error()
 *
 * Checks the $wp_settings_errors array for any errors declared during the current
 * pageload and returns them.
 *
 * If changes were just submitted ($_GET['updated']) and settings errors were saved
 * to the 'settings_errors' transient then those errors will be returned instead. This
 * is used to pass errors back across pageloads.
 *
 * Use the $sanitize argument to manually re-sanitize the option before returning errors.
 * This is useful if you have errors or notices you want to show even when the user
 * hasn't submitted data (i.e. when they first load an options page, or in admin_notices action hook)
 *
 * @global array $wp_settings_errors Storage array of errors registered during this pageload
 *
 * @param string $setting Optional slug title of a specific setting who's errors you want.
 * @param boolean $sanitize Whether to re-sanitize the setting value before returning errors.
 * @return array Array of settings errors
 */
function get_settings_errors( $setting = '', $sanitize = FALSE ) {
	global $wp_settings_errors;

	// If $sanitize is true, manually re-run the sanitizisation for this option
	// This allows the $sanitize_callback from register_setting() to run, adding
	// any settings errors you want to show by default.
	if ( $sanitize )
		sanitize_option( $setting, get_option($setting));

	// If settings were passed back from options.php then use them
	// Ignore transients if $sanitize is true, we dont' want the old values anyway
	if ( isset($_GET['updated']) && $_GET['updated'] && get_transient('settings_errors') ) {
		$settings_errors = get_transient('settings_errors');
		delete_transient('settings_errors');
	// Otherwise check global in case validation has been run on this pageload
	} elseif ( count( $wp_settings_errors ) ) {
		$settings_errors = $wp_settings_errors;
	} else {
		return;
	}

	// Filter the results to those of a specific setting if one was set
	if ( $setting ) {
		foreach ( (array) $settings_errors as $key => $details )
			if ( $setting != $details['setting'] )
				unset( $settings_errors[$key] );
	}
	return $settings_errors;
}

/**
 * Display settings errors registered by add_settings_error()
 *
 * Part of the Settings API. Outputs a <div> for each error retrieved by get_settings_errors().
 *
 * This is called automatically after a settings page based on the Settings API is submitted.
 * Errors should be added during the validation callback function for a setting defined in register_setting()
 *
 * The $sanitize option is passed into get_settings_errors() and will re-run the setting sanitization
 * on its current value.
 *
 * The $hide_on_update option will cause errors to only show when the settings page is first loaded.
 * if the user has already saved new values it will be hidden to avoid repeating messages already
 * shown in the default error reporting after submission. This is useful to show general errors like missing
 * settings when the user arrives at the settings page.
 *
 * @param string $setting Optional slug title of a specific setting who's errors you want.
 * @param boolean $sanitize Whether to re-sanitize the setting value before returning errors.
 * @param boolean $hide_on_update If set to true errors will not be shown if the settings page has already been submitted.
 * @return <type>
 */
function settings_errors ( $setting = '', $sanitize = FALSE, $hide_on_update = FALSE ) {

	if ($hide_on_update AND $_GET['updated']) return;

	$settings_errors = get_settings_errors( $setting, $sanitize );

	if ( !is_array($settings_errors) ) return;

	$output = '';
	foreach ( $settings_errors as $key => $details ) {
		$css_id = 'setting-error-' . $details['code'];
		$css_class = $details['type'] . ' settings-error';
		$output .= "<div id='$css_id' class='$css_class'> \n";
		$output .= "<p><strong>{$details['message']}</strong></p>";
		$output .= "</div> \n";
	}
	echo $output;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $page
 */
function manage_columns_prefs($page) {
	$columns = get_column_headers($page);

	$hidden = get_hidden_columns($page);

	foreach ( $columns as $column => $title ) {
		// Can't hide these
		if ( 'cb' == $column || 'title' == $column || 'name' == $column || 'username' == $column || 'media' == $column || 'comment' == $column )
			continue;
		if ( empty($title) )
			continue;

		if ( 'comments' == $column )
			$title = __('Comments');
		$id = "$column-hide";
		echo '<label for="' . $id . '">';
		echo '<input class="hide-column-tog" name="' . $id . '" type="checkbox" id="' . $id . '" value="' . $column . '"' . (! in_array($column, $hidden) ? ' checked="checked"' : '') . ' />';
		echo "$title</label>\n";
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $found_action
 */
function find_posts_div($found_action = '') {
?>
	<div id="find-posts" class="find-box" style="display:none;">
		<div id="find-posts-head" class="find-box-head"><?php _e('Find Posts or Pages'); ?></div>
		<div class="find-box-inside">
			<div class="find-box-search">
				<?php if ( $found_action ) { ?>
					<input type="hidden" name="found_action" value="<?php echo esc_attr($found_action); ?>" />
				<?php } ?>

				<input type="hidden" name="affected" id="affected" value="" />
				<?php wp_nonce_field( 'find-posts', '_ajax_nonce', false ); ?>
				<label class="screen-reader-text" for="find-posts-input"><?php _e( 'Search' ); ?></label>
				<input type="text" id="find-posts-input" name="ps" value="" />
				<input type="button" onclick="findPosts.send();" value="<?php esc_attr_e( 'Search' ); ?>" class="button" /><br />

				<input type="radio" name="find-posts-what" id="find-posts-posts" checked="checked" value="posts" />
				<label for="find-posts-posts"><?php _e( 'Posts' ); ?></label>
				<input type="radio" name="find-posts-what" id="find-posts-pages" value="pages" />
				<label for="find-posts-pages"><?php _e( 'Pages' ); ?></label>
			</div>
			<div id="find-posts-response"></div>
		</div>
		<div class="find-box-buttons">
			<input type="button" class="button alignleft" onclick="findPosts.close();" value="<?php esc_attr_e('Close'); ?>" />
			<input id="find-posts-submit" type="submit" class="button-primary alignright" value="<?php esc_attr_e('Select'); ?>" />
		</div>
	</div>
<?php
}

/**
 * Display the post password.
 *
 * The password is passed through {@link esc_attr()} to ensure that it
 * is safe for placing in an html attribute.
 *
 * @uses attr
 * @since 2.7.0
 */
function the_post_password() {
	global $post;
	if ( isset( $post->post_password ) ) echo esc_attr( $post->post_password );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 */
function favorite_actions( $screen = null ) {
	global $post_type_object;

	$default_action = false;

	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	if ( isset($post_type_object) ) {
		switch ( $screen->id ) {
			case $post_type_object->name:
				$default_action = array('edit.php?post_type=' . $post_type_object->name => array($post_type_object->labels->edit_item, $post_type_object->cap->edit_posts));
				break;
			case "edit-{$post_type_object->name}":
				$default_action = array('post-new.php?post_type=' . $post_type_object->name => array($post_type_object->labels->new_item, $post_type_object->cap->edit_posts));
				break;
		}
	}

	if ( !$default_action ) {
		switch ( $screen->id ) {
			case 'upload':
				$default_action = array('media-new.php' => array(__('New Media'), 'upload_files'));
				break;
			case 'media':
				$default_action = array('upload.php' => array(__('Edit Media'), 'upload_files'));
				break;
			case 'link-manager':
				$default_action = array('link-add.php' => array(__('New Link'), 'manage_links'));
				break;
			case 'link-add':
				$default_action = array('link-manager.php' => array(__('Edit Links'), 'manage_links'));
				break;
			case 'users':
				$default_action = array('user-new.php' => array(__('New User'), 'create_users'));
				break;
			case 'user':
				$default_action = array('users.php' => array(__('Edit Users'), 'edit_users'));
				break;
			case 'plugins':
				$default_action = array('plugin-install.php' => array(__('Install Plugins'), 'install_plugins'));
				break;
			case 'plugin-install':
				$default_action = array('plugins.php' => array(__('Manage Plugins'), 'activate_plugins'));
				break;
			case 'themes':
				$default_action = array('theme-install.php' => array(__('Install Themes'), 'install_themes'));
				break;
			case 'theme-install':
				$default_action = array('themes.php' => array(__('Manage Themes'), 'switch_themes'));
				break;
			default:
				$default_action = array('post-new.php' => array(__('New Post'), 'edit_posts'));
				break;
		}
	}

	$actions = array(
		'post-new.php' => array(__('New Post'), 'edit_posts'),
		'edit.php?post_status=draft' => array(__('Drafts'), 'edit_posts'),
		'post-new.php?post_type=page' => array(__('New Page'), 'edit_pages'),
		'media-new.php' => array(__('Upload'), 'upload_files'),
		'edit-comments.php' => array(__('Comments'), 'moderate_comments')
		);

	$default_key = array_keys($default_action);
	$default_key = $default_key[0];
	if ( isset($actions[$default_key]) )
		unset($actions[$default_key]);
	$actions = array_merge($default_action, $actions);
	$actions = apply_filters('favorite_actions', $actions);

	$allowed_actions = array();
	foreach ( $actions as $action => $data ) {
		if ( current_user_can($data[1]) )
			$allowed_actions[$action] = $data[0];
	}

	if ( empty($allowed_actions) )
		return;

	$first = array_keys($allowed_actions);
	$first = $first[0];
	echo '<div id="favorite-actions">';
	echo '<div id="favorite-first"><a href="' . $first . '">' . $allowed_actions[$first] . '</a></div><div id="favorite-toggle"><br /></div>';
	echo '<div id="favorite-inside">';

	array_shift($allowed_actions);

	foreach ( $allowed_actions as $action => $label) {
		echo "<div class='favorite-action'><a href='$action'>";
		echo $label;
		echo "</a></div>\n";
	}
	echo "</div></div>\n";
}

/**
 * Get the post title.
 *
 * The post title is fetched and if it is blank then a default string is
 * returned.
 *
 * @since 2.7.0
 * @param int $id The post id. If not supplied the global $post is used.
 *
 */
function _draft_or_post_title($post_id = 0) {
	$title = get_the_title($post_id);
	if ( empty($title) )
		$title = __('(no title)');
	return $title;
}

/**
 * Display the search query.
 *
 * A simple wrapper to display the "s" parameter in a GET URI. This function
 * should only be used when {@link the_search_query()} cannot.
 *
 * @uses attr
 * @since 2.7.0
 *
 */
function _admin_search_query() {
	echo isset($_GET['s']) ? esc_attr( stripslashes( $_GET['s'] ) ) : '';
}

/**
 * Generic Iframe header for use with Thickbox
 *
 * @since 2.7.0
 * @param string $title Title of the Iframe page.
 * @param bool $limit_styles Limit styles to colour-related styles only (unless others are enqueued).
 *
 */
function iframe_header( $title = '', $limit_styles = false ) {
global $hook_suffix;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title ?> &#8212; <?php _e('WordPress'); ?></title>
<?php
wp_enqueue_style( 'global' );
if ( ! $limit_styles )
	wp_enqueue_style( 'wp-admin' );
wp_enqueue_style( 'colors' );
?>
<script type="text/javascript">
//<![CDATA[
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
function tb_close(){var win=window.dialogArguments||opener||parent||top;win.tb_remove();}
//]]>
</script>
<?php
do_action('admin_print_styles');
do_action('admin_print_scripts');
do_action('admin_head');

$admin_body_class = preg_replace('/[^a-z0-9_-]+/i', '-', $hook_suffix);
?>
</head>
<body<?php if ( isset($GLOBALS['body_id']) ) echo ' id="' . $GLOBALS['body_id'] . '"'; ?>  class="no-js <?php echo $admin_body_class; ?>">
<script type="text/javascript">
//<![CDATA[
(function(){
var c = document.body.className;
c = c.replace(/no-js/, 'js');
document.body.className = c;
})();
//]]>
</script>
<?php
}

/**
 * Generic Iframe footer for use with Thickbox
 *
 * @since 2.7.0
 *
 */
function iframe_footer() {
	//We're going to hide any footer output on iframe pages, but run the hooks anyway since they output Javascript or other needed content. ?>
	<div class="hidden">
<?php
	do_action('admin_footer', '');
	do_action('admin_print_footer_scripts'); ?>
	</div>
<script type="text/javascript">if(typeof wpOnload=="function")wpOnload();</script>
</body>
</html>
<?php
}

function _post_states($post) {
	$post_states = array();
	if ( isset($_GET['post_status']) )
		$post_status = $_GET['post_status'];
	else
		$post_status = '';

	if ( !empty($post->post_password) )
		$post_states[] = __('Password protected');
	if ( 'private' == $post->post_status && 'private' != $post_status )
		$post_states[] = __('Private');
	if ( 'draft' == $post->post_status && 'draft' != $post_status )
		$post_states[] = __('Draft');
	if ( 'pending' == $post->post_status && 'pending' != $post_status )
		/* translators: post state */
		$post_states[] = _x('Pending', 'post state');
	if ( is_sticky($post->ID) )
		$post_states[] = __('Sticky');

	$post_states = apply_filters( 'display_post_states', $post_states );

	if ( ! empty($post_states) ) {
		$state_count = count($post_states);
		$i = 0;
		echo ' - ';
		foreach ( $post_states as $state ) {
			++$i;
			( $i == $state_count ) ? $sep = '' : $sep = ', ';
			echo "<span class='post-state'>$state$sep</span>";
		}
	}
}

// Convert a screen string to a screen object
function convert_to_screen( $screen ) {
	$screen = str_replace('.php', '', $screen);
	$screen = str_replace('-new', '', $screen);
	$screen = str_replace('-add', '', $screen);
	$screen = apply_filters('screen_meta_screen', $screen);

	$screen = array('id' => $screen, 'base' => $screen);
	return (object) $screen;
}

function screen_meta($screen) {
	global $wp_meta_boxes, $_wp_contextual_help, $title;

	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	$column_screens = get_column_headers($screen);
	$meta_screens = array('index' => 'dashboard');

	if ( isset($meta_screens[$screen->id]) ) {
		$screen->id = $meta_screens[$screen->id];
		$screen->base = $screen->id;
	}

	$show_screen = false;
	if ( !empty($wp_meta_boxes[$screen->id]) || !empty($column_screens) )
		$show_screen = true;

	$screen_options = screen_options($screen);
	if ( $screen_options )
		$show_screen = true;

	if ( !isset($_wp_contextual_help) )
		$_wp_contextual_help = array();

	$settings = apply_filters('screen_settings', '', $screen);

	switch ( $screen->id ) {
		case 'widgets':
			$settings = '<p><a id="access-on" href="widgets.php?widgets-access=on">' . __('Enable accessibility mode') . '</a><a id="access-off" href="widgets.php?widgets-access=off">' . __('Disable accessibility mode') . "</a></p>\n";
			break;
	}
	if( $settings )
		$show_screen = true;
?>
<div id="screen-meta">
<?php
	if ( $show_screen ) :
		$default_text = __('Show on screen');
?>
<div id="screen-options-wrap" class="hidden">
	<form id="adv-settings" action="" method="post">
	<?php if ( isset($wp_meta_boxes[$screen->id]) ) : ?>
		<h5><?php echo apply_filters('meta_box_prefs_header', $default_text); ?></h5>
		<div class="metabox-prefs">
			<?php meta_box_prefs($screen); ?>
			<br class="clear" />
		</div>
		<?php endif;
		if ( isset($column_screens) ) : ?>
		<h5><?php echo apply_filters('columns_prefs_header', $default_text); ?></h5>
		<div class="metabox-prefs">
			<?php manage_columns_prefs($screen); ?>
			<br class="clear" />
		</div>
	<?php endif;
	echo screen_layout($screen);
	echo $screen_options;
	echo $settings; ?>
<div><?php wp_nonce_field( 'screen-options-nonce', 'screenoptionnonce', false ); ?></div>
</form>
</div>

<?php
	endif;

	$_wp_contextual_help = apply_filters('contextual_help_list', $_wp_contextual_help, $screen);
	?>
	<div id="contextual-help-wrap" class="hidden">
	<?php
	$contextual_help = '';
	if ( isset($_wp_contextual_help[$screen->id]) ) {
		if ( !empty($title) )
			$contextual_help .= '<h5>' . sprintf(__('Get help with &#8220;%s&#8221;'), $title) . '</h5>';
		else
			$contextual_help .= '<h5>' . __('Get help with this page') . '</h5>';
		$contextual_help .= '<div class="metabox-prefs">' . $_wp_contextual_help[$screen->id] . "</div>\n";

		$contextual_help .= '<h5>' . __('Other Help') . '</h5>';
	} else {
		$contextual_help .= '<h5>' . __('Help') . '</h5>';
	}

	$contextual_help .= '<div class="metabox-prefs">';
	$default_help = __('<a href="http://codex.wordpress.org/" target="_blank">Documentation</a>');
	$default_help .= '<br />';
	$default_help .= __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>');
	$contextual_help .= apply_filters('default_contextual_help', $default_help);
	$contextual_help .= "</div>\n";
	echo apply_filters('contextual_help', $contextual_help, $screen->id, $screen);
	?>
	</div>

<div id="screen-meta-links">
<div id="contextual-help-link-wrap" class="hide-if-no-js screen-meta-toggle">
<a href="#contextual-help" id="contextual-help-link" class="show-settings"><?php _e('Help') ?></a>
</div>
<?php if ( $show_screen ) { ?>
<div id="screen-options-link-wrap" class="hide-if-no-js screen-meta-toggle">
<a href="#screen-options" id="show-settings-link" class="show-settings"><?php _e('Screen Options') ?></a>
</div>
<?php } ?>
</div>
</div>
<?php
}

/**
 * Add contextual help text for a page
 *
 * @since 2.7.0
 *
 * @param string $screen The handle for the screen to add help to.  This is usually the hook name returned by the add_*_page() functions.
 * @param string $help Arbitrary help text
 */
function add_contextual_help($screen, $help) {
	global $_wp_contextual_help;

	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	if ( !isset($_wp_contextual_help) )
		$_wp_contextual_help = array();

	$_wp_contextual_help[$screen->id] = $help;
}

function drag_drop_help() {
	return '
	<p>' .	__('Most of the modules on this screen can be moved. If you hover your mouse over the title bar of a module you&rsquo;ll notice the 4 arrow cursor appears to let you know it is movable. Click on it, hold down the mouse button and start dragging the module to a new location. As you drag the module, notice the dotted gray box that also moves. This box indicates where the module will be placed when you release the mouse button.') . '</p>
	<p>' . __('The same modules can be expanded and collapsed by clicking once on their title bar and also completely hidden from the Screen Options tab.') . '</p>
';
}

function plugins_search_help() {
	return '
	<p><strong>' . __('Search help') . '</strong></p>' .
	'<p>' . __('You may search based on 3 criteria:') . '<br />' .
	__('<strong>Term:</strong> Searches theme names and descriptions for the specified term.') . '<br />' .
	__('<strong>Tag:</strong> Searches for themes tagged as such.') . '<br />' .
	__('<strong>Author:</strong> Searches for themes created by the Author, or which the Author contributed to.') . '</p>
';
}

function screen_layout($screen) {
	global $screen_layout_columns;

	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	$columns = array('dashboard' => 4, 'post' => 2, 'page' => 2, 'link' => 2);

	// Add custom post types
	foreach ( get_post_types( array('show_ui' => true) ) as $post_type )
		$columns[$post_type] = 2;

	$columns = apply_filters('screen_layout_columns', $columns, $screen->id, $screen);

	if ( !isset($columns[$screen->id]) ) {
		$screen_layout_columns = 0;
		return '';
 	}

	$screen_layout_columns = get_user_option("screen_layout_$screen->id");
	$num = $columns[$screen->id];

	if ( ! $screen_layout_columns )
			$screen_layout_columns = 2;

	$i = 1;
	$return = '<h5>' . __('Screen Layout') . "</h5>\n<div class='columns-prefs'>" . __('Number of Columns:') . "\n";
	while ( $i <= $num ) {
		$return .= "<label><input type='radio' name='screen_columns' value='$i'" . ( ($screen_layout_columns == $i) ? " checked='checked'" : "" ) . " /> $i</label>\n";
		++$i;
	}
	$return .= "</div>\n";
	return $return;
}

function screen_options($screen) {
	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	switch ( $screen->base ) {
		case 'edit':
		case 'edit-pages':
			$post_type = 'post';
			if ( isset($_GET['post_type']) && in_array( $_GET['post_type'], get_post_types( array('public' => true ) ) ) )
				$post_type = $_GET['post_type'];
			$post_type_object = get_post_type_object($post_type);
			$per_page_label = $post_type_object->labels->name;
			break;
		case 'ms-sites':
			$per_page_label = __('Sites');
			break;
		case 'ms-users':
			$per_page_label = __('Users');
			break;
		case 'edit-comments':
			$per_page_label = __('Comments');
			break;
		case 'upload':
			$per_page_label = __('Media items');
			break;
		case 'edit-tags':
			global $tax;
			$per_page_label = $tax->label;
			break;
		case 'plugins':
			$per_page_label = __('Plugins');
			break;
		default:
			return '';
	}

	$option = str_replace( '-', '_', "{$screen->id}_per_page" );
	if ( 'edit_tags_per_page' == $option ) {
		if ( 'category' == $tax->name )
			$option = 'categories_per_page';
		elseif ( 'post_tag' != $tax->name )
			$option = 'edit_' . $tax->name . '_per_page';
	}

	$per_page = (int) get_user_option( $option );
	if ( empty( $per_page ) || $per_page < 1 ) {
		if ( 'plugins' == $screen->id )
			$per_page = 999;
		else
			$per_page = 20;
	}

	if ( 'edit_comments_per_page' == $option )
		$per_page = apply_filters( 'comments_per_page', $per_page, isset($_REQUEST['comment_status']) ? $_REQUEST['comment_status'] : 'all' );
	elseif ( 'categories_per_page' == $option )
		$per_page = apply_filters( 'edit_categories_per_page', $per_page );
	else
		$per_page = apply_filters( $option, $per_page );

	$return = "<div class='screen-options'>\n";
	if ( !empty($per_page_label) )
		$return .= "<input type='text' class='screen-per-page' name='wp_screen_options[value]' id='$option' maxlength='3' value='$per_page' /> <label for='$option'>$per_page_label</label>\n";
	$return .= "<input type='submit' class='button' value='" . esc_attr__('Apply') . "' />";
	$return .= "<input type='hidden' name='wp_screen_options[option]' value='" . esc_attr($option) . "' />";
	$return .= "</div>\n";
	return $return;
}

function screen_icon($screen = '') {
	global $current_screen;

	if ( empty($screen) )
		$screen = $current_screen;
	elseif ( is_string($screen) )
		$name = $screen;

	if ( empty($name) ) {
		if ( !empty($screen->parent_base) )
			$name = $screen->parent_base;
		else
			$name = $screen->base;

		if ( 'edit' == $name && isset($screen->post_type) && 'page' == $screen->post_type )
			$name = 'edit-pages';
	}

?>
	<div id="icon-<?php echo $name; ?>" class="icon32"><br /></div>
<?php
}

/**
 * Test support for compressing JavaScript from PHP
 *
 * Outputs JavaScript that tests if compression from PHP works as expected
 * and sets an option with the result. Has no effect when the current user
 * is not an administrator. To run the test again the option 'can_compress_scripts'
 * has to be deleted.
 *
 * @since 2.8.0
 */
function compression_test() {
?>
	<script type="text/javascript">
	/* <![CDATA[ */
	var testCompression = {
		get : function(test) {
			var x;
			if ( window.XMLHttpRequest ) {
				x = new XMLHttpRequest();
			} else {
				try{x=new ActiveXObject('Msxml2.XMLHTTP');}catch(e){try{x=new ActiveXObject('Microsoft.XMLHTTP');}catch(e){};}
			}

			if (x) {
				x.onreadystatechange = function() {
					var r, h;
					if ( x.readyState == 4 ) {
						r = x.responseText.substr(0, 18);
						h = x.getResponseHeader('Content-Encoding');
						testCompression.check(r, h, test);
					}
				}

				x.open('GET', 'admin-ajax.php?action=wp-compression-test&test='+test+'&'+(new Date()).getTime(), true);
				x.send('');
			}
		},

		check : function(r, h, test) {
			if ( ! r && ! test )
				this.get(1);

			if ( 1 == test ) {
				if ( h && ( h.match(/deflate/i) || h.match(/gzip/i) ) )
					this.get('no');
				else
					this.get(2);

				return;
			}

			if ( 2 == test ) {
				if ( '"wpCompressionTest' == r )
					this.get('yes');
				else
					this.get('no');
			}
		}
	};
	testCompression.check();
	/* ]]> */
	</script>
<?php
}

/**
 * Set the current screen object
 *
 * @since 3.0.0
 *
 * @uses $current_screen
 *
 * @param string $id Screen id, optional.
 */
function set_current_screen( $id =  '' ) {
	global $current_screen, $hook_suffix, $typenow;

	if ( empty($id) ) {
		$current_screen = $hook_suffix;
		$current_screen = str_replace('.php', '', $current_screen);
		$current_screen = str_replace('-new', '', $current_screen);
		$current_screen = str_replace('-add', '', $current_screen);
		$current_screen = array('id' => $current_screen, 'base' => $current_screen);
	} else {
		if ( false !== strpos($id, '-') )
			list( $id, $typenow ) = explode('-', $id, 2);
		$current_screen = array('id' => $id, 'base' => $id);
	}

	$current_screen = (object) $current_screen;

	// Map index to dashboard
	if ( 'index' == $current_screen->base )
		$current_screen->base = 'dashboard';
	if ( 'index' == $current_screen->id )
		$current_screen->id = 'dashboard';

	if ( 'edit' == $current_screen->id ) {
		if ( empty($typenow) )
			$typenow = 'post';
		$current_screen->id .= '-' . $typenow;
		$current_screen->post_type = $typenow;
	} elseif ( 'post' == $current_screen->id ) {
		if ( empty($typenow) )
			$typenow = 'post';
		$current_screen->id = $typenow;
		$current_screen->post_type = $typenow;
	}

	$current_screen = apply_filters('current_screen', $current_screen);
}

?>
