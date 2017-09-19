<?php
/**
 * Template WordPress Administration API.
 *
 * A Big Mess. Also some neat functions that are nicely written.
 *
 * @package WordPress
 * @subpackage Administration
 */

//
// Category Checklists
//

/**
 * Walker to output an unordered list of category checkbox input elements.
 *
 * @since 2.5.1
 *
 * @see Walker
 * @see wp_category_checklist()
 * @see wp_terms_checklist()
 */
class Walker_Category_Checklist extends Walker {
	public $tree_type = 'category';
	public $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker:start_lvl()
	 *
	 * @since 2.5.1
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since 2.5.1
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 2.5.1
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 * @param int    $id       ID of the current term.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		if ( empty( $args['taxonomy'] ) ) {
			$taxonomy = 'category';
		} else {
			$taxonomy = $args['taxonomy'];
		}

		if ( $taxonomy == 'category' ) {
			$name = 'post_category';
		} else {
			$name = 'tax_input[' . $taxonomy . ']';
		}

		$args['popular_cats'] = empty( $args['popular_cats'] ) ? array() : $args['popular_cats'];
		$class = in_array( $category->term_id, $args['popular_cats'] ) ? ' class="popular-category"' : '';

		$args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];

		/** This filter is documented in wp-includes/category-template.php */
		if ( ! empty( $args['list_only'] ) ) {
			$aria_cheched = 'false';
			$inner_class = 'category';

			if ( in_array( $category->term_id, $args['selected_cats'] ) ) {
				$inner_class .= ' selected';
				$aria_cheched = 'true';
			}

			$output .= "\n" . '<li' . $class . '>' .
				'<div class="' . $inner_class . '" data-term-id=' . $category->term_id .
				' tabindex="0" role="checkbox" aria-checked="' . $aria_cheched . '">' .
				esc_html( apply_filters( 'the_category', $category->name ) ) . '</div>';
		} else {
			$output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
				'<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' .
				checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) .
				disabled( empty( $args['disabled'] ), false, false ) . ' /> ' .
				esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
		}
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @since 2.5.1
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 */
	public function end_el( &$output, $category, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}

/**
 * Output an unordered list of checkbox input elements labeled with category names.
 *
 * @since 2.5.1
 *
 * @see wp_terms_checklist()
 *
 * @param int    $post_id              Optional. Post to generate a categories checklist for. Default 0.
 *                                     $selected_cats must not be an array. Default 0.
 * @param int    $descendants_and_self Optional. ID of the category to output along with its descendants.
 *                                     Default 0.
 * @param array  $selected_cats        Optional. List of categories to mark as checked. Default false.
 * @param array  $popular_cats         Optional. List of categories to receive the "popular-category" class.
 *                                     Default false.
 * @param object $walker               Optional. Walker object to use to build the output.
 *                                     Default is a Walker_Category_Checklist instance.
 * @param bool   $checked_ontop        Optional. Whether to move checked items out of the hierarchy and to
 *                                     the top of the list. Default true.
 */
function wp_category_checklist( $post_id = 0, $descendants_and_self = 0, $selected_cats = false, $popular_cats = false, $walker = null, $checked_ontop = true ) {
	wp_terms_checklist( $post_id, array(
		'taxonomy' => 'category',
		'descendants_and_self' => $descendants_and_self,
		'selected_cats' => $selected_cats,
		'popular_cats' => $popular_cats,
		'walker' => $walker,
		'checked_ontop' => $checked_ontop
	) );
}

/**
 * Output an unordered list of checkbox input elements labelled with term names.
 *
 * Taxonomy-independent version of wp_category_checklist().
 *
 * @since 3.0.0
 *
 * @param int          $post_id Optional. Post ID. Default 0.
 * @param array|string $args {
 *     Optional. Array or string of arguments for generating a terms checklist. Default empty array.
 *
 *     @type int    $descendants_and_self ID of the category to output along with its descendants.
 *                                        Default 0.
 *     @type array  $selected_cats        List of categories to mark as checked. Default false.
 *     @type array  $popular_cats         List of categories to receive the "popular-category" class.
 *                                        Default false.
 *     @type object $walker               Walker object to use to build the output.
 *                                        Default is a Walker_Category_Checklist instance.
 *     @type string $taxonomy             Taxonomy to generate the checklist for. Default 'category'.
 *     @type bool   $checked_ontop        Whether to move checked items out of the hierarchy and to
 *                                        the top of the list. Default true.
 * }
 */
function wp_terms_checklist( $post_id = 0, $args = array() ) {
 	$defaults = array(
		'descendants_and_self' => 0,
		'selected_cats' => false,
		'popular_cats' => false,
		'walker' => null,
		'taxonomy' => 'category',
		'checked_ontop' => true
	);

	/**
	 * Filter the taxonomy terms checklist arguments.
	 *
	 * @since 3.4.0
	 *
	 * @see wp_terms_checklist()
	 *
	 * @param array $args    An array of arguments.
	 * @param int   $post_id The post ID.
	 */
	$params = apply_filters( 'wp_terms_checklist_args', $args, $post_id );

	$r = wp_parse_args( $params, $defaults );

	if ( empty( $r['walker'] ) || ! ( $r['walker'] instanceof Walker ) ) {
		$walker = new Walker_Category_Checklist;
	} else {
		$walker = $r['walker'];
	}

	$taxonomy = $r['taxonomy'];
	$descendants_and_self = (int) $r['descendants_and_self'];

	$args = array( 'taxonomy' => $taxonomy );

	$tax = get_taxonomy( $taxonomy );
	$args['disabled'] = ! current_user_can( $tax->cap->assign_terms );

	$args['list_only'] = ! empty( $r['list_only'] );

	if ( is_array( $r['selected_cats'] ) ) {
		$args['selected_cats'] = $r['selected_cats'];
	} elseif ( $post_id ) {
		$args['selected_cats'] = wp_get_object_terms( $post_id, $taxonomy, array_merge( $args, array( 'fields' => 'ids' ) ) );
	} else {
		$args['selected_cats'] = array();
	}
	if ( is_array( $r['popular_cats'] ) ) {
		$args['popular_cats'] = $r['popular_cats'];
	} else {
		$args['popular_cats'] = get_terms( $taxonomy, array(
			'fields' => 'ids',
			'orderby' => 'count',
			'order' => 'DESC',
			'number' => 10,
			'hierarchical' => false
		) );
	}
	if ( $descendants_and_self ) {
		$categories = (array) get_terms( $taxonomy, array(
			'child_of' => $descendants_and_self,
			'hierarchical' => 0,
			'hide_empty' => 0
		) );
		$self = get_term( $descendants_and_self, $taxonomy );
		array_unshift( $categories, $self );
	} else {
		$categories = (array) get_terms( $taxonomy, array( 'get' => 'all' ) );
	}

	if ( $r['checked_ontop'] ) {
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
		echo call_user_func_array( array( $walker, 'walk' ), array( $checked_categories, 0, $args ) );
	}
	// Then the rest of them
	echo call_user_func_array( array( $walker, 'walk' ), array( $categories, 0, $args ) );
}

/**
 * Retrieve a list of the most popular terms from the specified taxonomy.
 *
 * If the $echo argument is true then the elements for a list of checkbox
 * `<input>` elements labelled with the names of the selected terms is output.
 * If the $post_ID global isn't empty then the terms associated with that
 * post will be marked as checked.
 *
 * @since 2.5.0
 *
 * @param string $taxonomy Taxonomy to retrieve terms from.
 * @param int $default Not used.
 * @param int $number Number of terms to retrieve. Defaults to 10.
 * @param bool $echo Optionally output the list as well. Defaults to true.
 * @return array List of popular term IDs.
 */
function wp_popular_terms_checklist( $taxonomy, $default = 0, $number = 10, $echo = true ) {
	$post = get_post();

	if ( $post && $post->ID )
		$checked_terms = wp_get_object_terms($post->ID, $taxonomy, array('fields'=>'ids'));
	else
		$checked_terms = array();

	$terms = get_terms( $taxonomy, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => $number, 'hierarchical' => false ) );

	$tax = get_taxonomy($taxonomy);

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
				<input id="in-<?php echo $id; ?>" type="checkbox" <?php echo $checked; ?> value="<?php echo (int) $term->term_id; ?>" <?php disabled( ! current_user_can( $tax->cap->assign_terms ) ); ?> />
				<?php
				/** This filter is documented in wp-includes/category-template.php */
				echo esc_html( apply_filters( 'the_category', $term->name ) );
				?>
			</label>
		</li>

		<?php
	}
	return $popular_ids;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.5.1
 *
 * @param int $link_id
 */
function wp_link_category_checklist( $link_id = 0 ) {
	$default = 1;

	$checked_categories = array();

	if ( $link_id ) {
		$checked_categories = wp_get_link_cats( $link_id );
		// No selected categories, strange
		if ( ! count( $checked_categories ) ) {
			$checked_categories[] = $default;
		}
	} else {
		$checked_categories[] = $default;
	}

	$categories = get_terms( 'link_category', array( 'orderby' => 'name', 'hide_empty' => 0 ) );

	if ( empty( $categories ) )
		return;

	foreach ( $categories as $category ) {
		$cat_id = $category->term_id;

		/** This filter is documented in wp-includes/category-template.php */
		$name = esc_html( apply_filters( 'the_category', $category->name ) );
		$checked = in_array( $cat_id, $checked_categories ) ? ' checked="checked"' : '';
		echo '<li id="link-category-', $cat_id, '"><label for="in-link-category-', $cat_id, '" class="selectit"><input value="', $cat_id, '" type="checkbox" name="link_category[]" id="in-link-category-', $cat_id, '"', $checked, '/> ', $name, "</label></li>";
	}
}

/**
 * Adds hidden fields with the data for use in the inline editor for posts and pages.
 *
 * @since 2.7.0
 *
 * @param WP_Post $post Post object.
 */
function get_inline_data($post) {
	$post_type_object = get_post_type_object($post->post_type);
	if ( ! current_user_can( 'edit_post', $post->ID ) )
		return;

	$title = esc_textarea( trim( $post->post_title ) );

	/** This filter is documented in wp-admin/edit-tag-form.php */
	echo '
<div class="hidden" id="inline_' . $post->ID . '">
	<div class="post_title">' . $title . '</div>
	<div class="post_name">' . apply_filters( 'editable_slug', $post->post_name ) . '</div>
	<div class="post_author">' . $post->post_author . '</div>
	<div class="comment_status">' . esc_html( $post->comment_status ) . '</div>
	<div class="ping_status">' . esc_html( $post->ping_status ) . '</div>
	<div class="_status">' . esc_html( $post->post_status ) . '</div>
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

	if ( post_type_supports( $post->post_type, 'page-attributes' ) )
		echo '<div class="menu_order">' . $post->menu_order . '</div>';

	$taxonomy_names = get_object_taxonomies( $post->post_type );
	foreach ( $taxonomy_names as $taxonomy_name) {
		$taxonomy = get_taxonomy( $taxonomy_name );

		if ( $taxonomy->hierarchical && $taxonomy->show_ui ) {

			$terms = get_object_term_cache( $post->ID, $taxonomy_name );
			if ( false === $terms ) {
				$terms = wp_get_object_terms( $post->ID, $taxonomy_name );
				wp_cache_add( $post->ID, $terms, $taxonomy_name . '_relationships' );
			}
			$term_ids = empty( $terms ) ? array() : wp_list_pluck( $terms, 'term_id' );

			echo '<div class="post_category" id="' . $taxonomy_name . '_' . $post->ID . '">' . implode( ',', $term_ids ) . '</div>';

		} elseif ( $taxonomy->show_ui ) {

			echo '<div class="tags_input" id="'.$taxonomy_name.'_'.$post->ID.'">'
				. esc_html( str_replace( ',', ', ', get_terms_to_edit( $post->ID, $taxonomy_name ) ) ) . '</div>';

		}
	}

	if ( !$post_type_object->hierarchical )
		echo '<div class="sticky">' . (is_sticky($post->ID) ? 'sticky' : '') . '</div>';

	if ( post_type_supports( $post->post_type, 'post-formats' ) )
		echo '<div class="post_format">' . esc_html( get_post_format( $post->ID ) ) . '</div>';

	echo '</div>';
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.7.0
 *
 * @param int $position
 * @param bool $checkbox
 * @param string $mode
 * @param bool $table_row
 */
function wp_comment_reply( $position = 1, $checkbox = false, $mode = 'single', $table_row = true ) {
	global $wp_list_table;
	/**
	 * Filter the in-line comment reply-to form output in the Comments
	 * list table.
	 *
	 * Returning a non-empty value here will short-circuit display
	 * of the in-line comment-reply form in the Comments list table,
	 * echoing the returned value instead.
	 *
	 * @since 2.7.0
	 *
	 * @see wp_comment_reply()
	 *
	 * @param string $content The reply-to form content.
	 * @param array  $args    An array of default args.
	 */
	$content = apply_filters( 'wp_comment_reply', '', array( 'position' => $position, 'checkbox' => $checkbox, 'mode' => $mode ) );

	if ( ! empty($content) ) {
		echo $content;
		return;
	}

	if ( ! $wp_list_table ) {
		if ( $mode == 'single' ) {
			$wp_list_table = _get_list_table('WP_Post_Comments_List_Table');
		} else {
			$wp_list_table = _get_list_table('WP_Comments_List_Table');
		}
	}

?>
<form method="get">
<?php if ( $table_row ) : ?>
<table style="display:none;"><tbody id="com-reply"><tr id="replyrow" style="display:none;"><td colspan="<?php echo $wp_list_table->get_column_count(); ?>" class="colspanchange">
<?php else : ?>
<div id="com-reply" style="display:none;"><div id="replyrow" style="display:none;">
<?php endif; ?>
	<div id="replyhead" style="display:none;"><h5><?php _e( 'Reply to Comment' ); ?></h5></div>
	<div id="addhead" style="display:none;"><h5><?php _e('Add new Comment'); ?></h5></div>
	<div id="edithead" style="display:none;">
		<div class="inside">
		<label for="author"><?php _e('Name') ?></label>
		<input type="text" name="newcomment_author" size="50" value="" id="author" />
		</div>

		<div class="inside">
		<label for="author-email"><?php _e('E-mail') ?></label>
		<input type="text" name="newcomment_author_email" size="50" value="" id="author-email" />
		</div>

		<div class="inside">
		<label for="author-url"><?php _e('URL') ?></label>
		<input type="text" id="author-url" name="newcomment_author_url" class="code" size="103" value="" />
		</div>
		<div style="clear:both;"></div>
	</div>

	<div id="replycontainer">
	<?php
	$quicktags_settings = array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' );
	wp_editor( '', 'replycontent', array( 'media_buttons' => false, 'tinymce' => false, 'quicktags' => $quicktags_settings ) );
	?>
	</div>

	<p id="replysubmit" class="submit">
	<a href="#comments-form" class="save button-primary alignright">
	<span id="addbtn" style="display:none;"><?php _e('Add Comment'); ?></span>
	<span id="savebtn" style="display:none;"><?php _e('Update Comment'); ?></span>
	<span id="replybtn" style="display:none;"><?php _e('Submit Reply'); ?></span></a>
	<a href="#comments-form" class="cancel button-secondary alignleft"><?php _e('Cancel'); ?></a>
	<span class="waiting spinner"></span>
	<span class="error" style="display:none;"></span>
	<br class="clear" />
	</p>

	<input type="hidden" name="action" id="action" value="" />
	<input type="hidden" name="comment_ID" id="comment_ID" value="" />
	<input type="hidden" name="comment_post_ID" id="comment_post_ID" value="" />
	<input type="hidden" name="status" id="status" value="" />
	<input type="hidden" name="position" id="position" value="<?php echo $position; ?>" />
	<input type="hidden" name="checkbox" id="checkbox" value="<?php echo $checkbox ? 1 : 0; ?>" />
	<input type="hidden" name="mode" id="mode" value="<?php echo esc_attr($mode); ?>" />
	<?php
		wp_nonce_field( 'replyto-comment', '_ajax_nonce-replyto-comment', false );
		if ( current_user_can( 'unfiltered_html' ) )
			wp_nonce_field( 'unfiltered-html-comment', '_wp_unfiltered_html_comment', false );
	?>
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
 * @since 1.2.0
 *
 * @param array $meta
 */
function list_meta( $meta ) {
	// Exit if no meta
	if ( ! $meta ) {
		echo '
<table id="list-table" style="display: none;">
	<thead>
	<tr>
		<th class="left">' . _x( 'Name', 'meta name' ) . '</th>
		<th>' . __( 'Value' ) . '</th>
	</tr>
	</thead>
	<tbody id="the-list" data-wp-lists="list:meta">
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
		<th class="left"><?php _ex( 'Name', 'meta name' ) ?></th>
		<th><?php _e( 'Value' ) ?></th>
	</tr>
	</thead>
	<tbody id='the-list' data-wp-lists='list:meta'>
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
 * @since 2.5.0
 *
 * @param array $entry
 * @param int   $count
 * @return string
 */
function _list_meta_row( $entry, &$count ) {
	static $update_nonce = false;

	if ( is_protected_meta( $entry['meta_key'], 'post' ) )
		return '';

	if ( !$update_nonce )
		$update_nonce = wp_create_nonce( 'add-meta' );

	$r = '';
	++ $count;

	if ( is_serialized( $entry['meta_value'] ) ) {
		if ( is_serialized_string( $entry['meta_value'] ) ) {
			// This is a serialized string, so we should display it.
			$entry['meta_value'] = maybe_unserialize( $entry['meta_value'] );
		} else {
			// This is a serialized array/object so we should NOT display it.
			--$count;
			return '';
		}
	}

	$entry['meta_key'] = esc_attr($entry['meta_key']);
	$entry['meta_value'] = esc_textarea( $entry['meta_value'] ); // using a <textarea />
	$entry['meta_id'] = (int) $entry['meta_id'];

	$delete_nonce = wp_create_nonce( 'delete-meta_' . $entry['meta_id'] );

	$r .= "\n\t<tr id='meta-{$entry['meta_id']}'>";
	$r .= "\n\t\t<td class='left'><label class='screen-reader-text' for='meta-{$entry['meta_id']}-key'>" . __( 'Key' ) . "</label><input name='meta[{$entry['meta_id']}][key]' id='meta-{$entry['meta_id']}-key' type='text' size='20' value='{$entry['meta_key']}' />";

	$r .= "\n\t\t<div class='submit'>";
	$r .= get_submit_button( __( 'Delete' ), 'deletemeta small', "deletemeta[{$entry['meta_id']}]", false, array( 'data-wp-lists' => "delete:the-list:meta-{$entry['meta_id']}::_ajax_nonce=$delete_nonce" ) );
	$r .= "\n\t\t";
	$r .= get_submit_button( __( 'Update' ), 'updatemeta small', "meta-{$entry['meta_id']}-submit", false, array( 'data-wp-lists' => "add:the-list:meta-{$entry['meta_id']}::_ajax_nonce-add-meta=$update_nonce" ) );
	$r .= "</div>";
	$r .= wp_nonce_field( 'change-meta', '_ajax_nonce', false, false );
	$r .= "</td>";

	$r .= "\n\t\t<td><label class='screen-reader-text' for='meta-{$entry['meta_id']}-value'>" . __( 'Value' ) . "</label><textarea name='meta[{$entry['meta_id']}][value]' id='meta-{$entry['meta_id']}-value' rows='2' cols='30'>{$entry['meta_value']}</textarea></td>\n\t</tr>";
	return $r;
}

/**
 * Prints the form in the Custom Fields meta box.
 *
 * @since 1.2.0
 *
 * @param WP_Post $post Optional. The post being edited.
 */
function meta_form( $post = null ) {
	global $wpdb;
	$post = get_post( $post );

	/**
	 * Filter the number of custom fields to retrieve for the drop-down
	 * in the Custom Fields meta box.
	 *
	 * @since 2.1.0
	 *
	 * @param int $limit Number of custom fields to retrieve. Default 30.
	 */
	$limit = apply_filters( 'postmeta_form_limit', 30 );
	$sql = "SELECT meta_key
		FROM $wpdb->postmeta
		GROUP BY meta_key
		HAVING meta_key NOT LIKE %s
		ORDER BY meta_key
		LIMIT %d";
	$keys = $wpdb->get_col( $wpdb->prepare( $sql, $wpdb->esc_like( '_' ) . '%', $limit ) );
	if ( $keys ) {
		natcasesort( $keys );
		$meta_key_input_id = 'metakeyselect';
	} else {
		$meta_key_input_id = 'metakeyinput';
	}
?>
<p><strong><?php _e( 'Add New Custom Field:' ) ?></strong></p>
<table id="newmeta">
<thead>
<tr>
<th class="left"><label for="<?php echo $meta_key_input_id; ?>"><?php _ex( 'Name', 'meta name' ) ?></label></th>
<th><label for="metavalue"><?php _e( 'Value' ) ?></label></th>
</tr>
</thead>

<tbody>
<tr>
<td id="newmetaleft" class="left">
<?php if ( $keys ) { ?>
<select id="metakeyselect" name="metakeyselect">
<option value="#NONE#"><?php _e( '&mdash; Select &mdash;' ); ?></option>
<?php

	foreach ( $keys as $key ) {
		if ( is_protected_meta( $key, 'post' ) || ! current_user_can( 'add_post_meta', $post->ID, $key ) )
			continue;
		echo "\n<option value='" . esc_attr($key) . "'>" . esc_html($key) . "</option>";
	}
?>
</select>
<input class="hide-if-js" type="text" id="metakeyinput" name="metakeyinput" value="" />
<a href="#postcustomstuff" class="hide-if-no-js" onclick="jQuery('#metakeyinput, #metakeyselect, #enternew, #cancelnew').toggle();return false;">
<span id="enternew"><?php _e('Enter new'); ?></span>
<span id="cancelnew" class="hidden"><?php _e('Cancel'); ?></span></a>
<?php } else { ?>
<input type="text" id="metakeyinput" name="metakeyinput" value="" />
<?php } ?>
</td>
<td><textarea id="metavalue" name="metavalue" rows="2" cols="25"></textarea></td>
</tr>

<tr><td colspan="2">
<div class="submit">
<?php submit_button( __( 'Add Custom Field' ), 'secondary', 'addmeta', false, array( 'id' => 'newmeta-submit', 'data-wp-lists' => 'add:the-list:newmeta' ) ); ?>
</div>
<?php wp_nonce_field( 'add-meta', '_ajax_nonce-add-meta', false ); ?>
</td></tr>
</tbody>
</table>
<?php

}

/**
 * Print out HTML form date elements for editing post or comment publish date.
 *
 * @since 0.71
 *
 * @param int|bool $edit      Accepts 1|true for editing the date, 0|false for adding the date.
 * @param int|bool $for_post  Accepts 1|true for applying the date to a post, 0|false for a comment.
 * @param int      $tab_index The tabindex attribute to add. Default 0.
 * @param int|bool $multi     Optional. Whether the additional fields and buttons should be added.
 *                            Default 0|false.
 */
function touch_time( $edit = 1, $for_post = 1, $tab_index = 0, $multi = 0 ) {
	global $wp_locale, $comment;
	$post = get_post();

	if ( $for_post )
		$edit = ! ( in_array($post->post_status, array('draft', 'pending') ) && (!$post->post_date_gmt || '0000-00-00 00:00:00' == $post->post_date_gmt ) );

	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 )
		$tab_index_attribute = " tabindex=\"$tab_index\"";

	// todo: Remove this?
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

	$month = '<label for="mm" class="screen-reader-text">' . __( 'Month' ) . '</label><select ' . ( $multi ? '' : 'id="mm" ' ) . 'name="mm"' . $tab_index_attribute . ">\n";
	for ( $i = 1; $i < 13; $i = $i +1 ) {
		$monthnum = zeroise($i, 2);
		$month .= "\t\t\t" . '<option value="' . $monthnum . '" ' . selected( $monthnum, $mm, false ) . '>';
		/* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
		$month .= sprintf( __( '%1$s-%2$s' ), $monthnum, $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) ) . "</option>\n";
	}
	$month .= '</select>';

	$day = '<label for="jj" class="screen-reader-text">' . __( 'Day' ) . '</label><input type="text" ' . ( $multi ? '' : 'id="jj" ' ) . 'name="jj" value="' . $jj . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';
	$year = '<label for="aa" class="screen-reader-text">' . __( 'Year' ) . '</label><input type="text" ' . ( $multi ? '' : 'id="aa" ' ) . 'name="aa" value="' . $aa . '" size="4" maxlength="4"' . $tab_index_attribute . ' autocomplete="off" />';
	$hour = '<label for="hh" class="screen-reader-text">' . __( 'Hour' ) . '</label><input type="text" ' . ( $multi ? '' : 'id="hh" ' ) . 'name="hh" value="' . $hh . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';
	$minute = '<label for="mn" class="screen-reader-text">' . __( 'Minute' ) . '</label><input type="text" ' . ( $multi ? '' : 'id="mn" ' ) . 'name="mn" value="' . $mn . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';

	echo '<div class="timestamp-wrap">';
	/* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
	printf( __( '%1$s %2$s, %3$s @ %4$s : %5$s' ), $month, $day, $year, $hour, $minute );

	echo '</div><input type="hidden" id="ss" name="ss" value="' . $ss . '" />';

	if ( $multi ) return;

	echo "\n\n";
	$map = array(
		'mm' => array( $mm, $cur_mm ),
		'jj' => array( $jj, $cur_jj ),
		'aa' => array( $aa, $cur_aa ),
		'hh' => array( $hh, $cur_hh ),
		'mn' => array( $mn, $cur_mn ),
	);
	foreach ( $map as $timeunit => $value ) {
		list( $unit, $curr ) = $value;

		echo '<input type="hidden" id="hidden_' . $timeunit . '" name="hidden_' . $timeunit . '" value="' . $unit . '" />' . "\n";
		$cur_timeunit = 'cur_' . $timeunit;
		echo '<input type="hidden" id="' . $cur_timeunit . '" name="' . $cur_timeunit . '" value="' . $curr . '" />' . "\n";
	}
?>

<p>
<a href="#edit_timestamp" class="save-timestamp hide-if-no-js button"><?php _e('OK'); ?></a>
<a href="#edit_timestamp" class="cancel-timestamp hide-if-no-js button-cancel"><?php _e('Cancel'); ?></a>
</p>
<?php
}

/**
 * Print out option HTML elements for the page templates drop-down.
 *
 * @since 1.5.0
 *
 * @param string $default Optional. The template file name. Default empty.
 */
function page_template_dropdown( $default = '' ) {
	$templates = get_page_templates( get_post() );
	ksort( $templates );
	foreach ( array_keys( $templates ) as $template ) {
		$selected = selected( $default, $templates[ $template ], false );
		echo "\n\t<option value='" . esc_attr( $templates[ $template ] ) . "' $selected>" . esc_html( $template ) . "</option>";
	}
}

/**
 * Print out option HTML elements for the page parents drop-down.
 *
 * @since 1.5.0
 *
 * @param int $default Optional. The default page ID to be pre-selected. Default 0.
 * @param int $parent  Optional. The parent page ID. Default 0.
 * @param int $level   Optional. Page depth level. Default 0.
 *
 * @return null|false Boolean False if page has no children, otherwise print out html elements
 */
function parent_dropdown( $default = 0, $parent = 0, $level = 0 ) {
	global $wpdb;
	$post = get_post();
	$items = $wpdb->get_results( $wpdb->prepare("SELECT ID, post_parent, post_title FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'page' ORDER BY menu_order", $parent) );

	if ( $items ) {
		foreach ( $items as $item ) {
			// A page cannot be its own parent.
			if ( $post && $post->ID && $item->ID == $post->ID )
				continue;

			$pad = str_repeat( '&nbsp;', $level * 3 );
			$selected = selected( $default, $item->ID, false );

			echo "\n\t<option class='level-$level' value='$item->ID' $selected>$pad " . esc_html($item->post_title) . "</option>";
			parent_dropdown( $default, $item->ID, $level +1 );
		}
	} else {
		return false;
	}
}

/**
 * Print out option html elements for role selectors.
 *
 * @since 2.1.0
 *
 * @param string $selected Slug for the role that should be already selected.
 */
function wp_dropdown_roles( $selected = '' ) {
	$p = '';
	$r = '';

	$editable_roles = array_reverse( get_editable_roles() );

	foreach ( $editable_roles as $role => $details ) {
		$name = translate_user_role($details['name'] );
		if ( $selected == $role ) // preselect specified role
			$p = "\n\t<option selected='selected' value='" . esc_attr($role) . "'>$name</option>";
		else
			$r .= "\n\t<option value='" . esc_attr($role) . "'>$name</option>";
	}
	echo $p . $r;
}

/**
 * Outputs the form used by the importers to accept the data to be imported
 *
 * @since 2.0.0
 *
 * @param string $action The action attribute for the form.
 */
function wp_import_upload_form( $action ) {

	/**
	 * Filter the maximum allowed upload size for import files.
	 *
	 * @since 2.3.0
	 *
	 * @see wp_max_upload_size()
	 *
	 * @param int $max_upload_size Allowed upload size. Default 1 MB.
	 */
	$bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
	$size = size_format( $bytes );
	$upload_dir = wp_upload_dir();
	if ( ! empty( $upload_dir['error'] ) ) :
		?><div class="error"><p><?php _e('Before you can upload your import file, you will need to fix the following error:'); ?></p>
		<p><strong><?php echo $upload_dir['error']; ?></strong></p></div><?php
	else :
?>
<form enctype="multipart/form-data" id="import-upload-form" method="post" class="wp-upload-form" action="<?php echo esc_url( wp_nonce_url( $action, 'import-upload' ) ); ?>">
<p>
<label for="upload"><?php _e( 'Choose a file from your computer:' ); ?></label> (<?php printf( __('Maximum size: %s' ), $size ); ?>)
<input type="file" id="upload" name="import" size="25" />
<input type="hidden" name="action" value="save" />
<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
</p>
<?php submit_button( __('Upload file and import'), 'button' ); ?>
</form>
<?php
	endif;
}

/**
 * Add a meta box to an edit form.
 *
 * @since 2.5.0
 *
 * @param string           $id            String for use in the 'id' attribute of tags.
 * @param string           $title         Title of the meta box.
 * @param callback         $callback      Function that fills the box with the desired content.
 *                                        The function should echo its output.
 * @param string|WP_Screen $screen        Optional. The screen on which to show the box (like a post
 *                                        type, 'link', or 'comment'). Default is the current screen.
 * @param string           $context       Optional. The context within the screen where the boxes
 *                                        should display. Available contexts vary from screen to
 *                                        screen. Post edit screen contexts include 'normal', 'side',
 *                                        and 'advanced'. Comments screen contexts include 'normal'
 *                                        and 'side'. Menus meta boxes (accordion sections) all use
 *                                        the 'side' context. Global default is 'advanced'.
 * @param string           $priority      Optional. The priority within the context where the boxes
 *                                        should show ('high', 'low'). Default 'default'.
 * @param array            $callback_args Optional. Data that should be set as the $args property
 *                                        of the box array (which is the second parameter passed
 *                                        to your callback). Default null.
 */
function add_meta_box( $id, $title, $callback, $screen = null, $context = 'advanced', $priority = 'default', $callback_args = null ) {
	global $wp_meta_boxes;

	if ( empty( $screen ) )
		$screen = get_current_screen();
	elseif ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	$page = $screen->id;

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

				/*
				 * If box was added with default priority, give it core priority to
				 * maintain sort order.
				 */
				if ( 'default' == $a_priority ) {
					$wp_meta_boxes[$page][$a_context]['core'][$id] = $wp_meta_boxes[$page][$a_context]['default'][$id];
					unset($wp_meta_boxes[$page][$a_context]['default'][$id]);
				}
				return;
			}
			// If no priority given and id already present, use existing priority.
			if ( empty($priority) ) {
				$priority = $a_priority;
			/*
			 * Else, if we're adding to the sorted priority, we don't know the title
			 * or callback. Grab them from the previously added context/priority.
			 */
			} elseif ( 'sorted' == $priority ) {
				$title = $wp_meta_boxes[$page][$a_context][$a_priority][$id]['title'];
				$callback = $wp_meta_boxes[$page][$a_context][$a_priority][$id]['callback'];
				$callback_args = $wp_meta_boxes[$page][$a_context][$a_priority][$id]['args'];
			}
			// An id can be in only one priority and one context.
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
 * Meta-Box template function
 *
 * @since 2.5.0
 *
 * @staticvar bool $already_sorted
 * @param string|WP_Screen $screen Screen identifier
 * @param string $context box context
 * @param mixed $object gets passed to the box callback function as first parameter
 * @return int number of meta_boxes
 */
function do_meta_boxes( $screen, $context, $object ) {
	global $wp_meta_boxes;
	static $already_sorted = false;

	if ( empty( $screen ) )
		$screen = get_current_screen();
	elseif ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	$page = $screen->id;

	$hidden = get_hidden_meta_boxes( $screen );

	printf('<div id="%s-sortables" class="meta-box-sortables">', htmlspecialchars($context));

	// Grab the ones the user has manually sorted. Pull them out of their previous context/priority and into the one the user chose
	if ( ! $already_sorted && $sorted = get_user_option( "meta-box-order_$page" ) ) {
		foreach ( $sorted as $box_context => $ids ) {
			foreach ( explode( ',', $ids ) as $id ) {
				if ( $id && 'dashboard_browser_nag' !== $id ) {
					add_meta_box( $id, null, null, $screen, $box_context, 'sorted' );
				}
			}
		}
	}

	$already_sorted = true;

	$i = 0;

	if ( isset( $wp_meta_boxes[ $page ][ $context ] ) ) {
		foreach ( array( 'high', 'sorted', 'core', 'default', 'low' ) as $priority ) {
			if ( isset( $wp_meta_boxes[ $page ][ $context ][ $priority ]) ) {
				foreach ( (array) $wp_meta_boxes[ $page ][ $context ][ $priority ] as $box ) {
					if ( false == $box || ! $box['title'] )
						continue;
					$i++;
					$hidden_class = in_array($box['id'], $hidden) ? ' hide-if-js' : '';
					echo '<div id="' . $box['id'] . '" class="postbox ' . postbox_classes($box['id'], $page) . $hidden_class . '" ' . '>' . "\n";
					if ( 'dashboard_browser_nag' != $box['id'] )
						echo '<div class="handlediv" title="' . esc_attr__('Click to toggle') . '"><br /></div>';
					echo "<h3 class='hndle'><span>{$box['title']}</span></h3>\n";
					echo '<div class="inside">' . "\n";
					call_user_func($box['callback'], $object, $box);
					echo "</div>\n";
					echo "</div>\n";
				}
			}
		}
	}

	echo "</div>";

	return $i;

}

/**
 * Remove a meta box from an edit form.
 *
 * @since 2.6.0
 *
 * @param string $id String for use in the 'id' attribute of tags.
 * @param string|object $screen The screen on which to show the box (post, page, link).
 * @param string $context The context within the page where the boxes should show ('normal', 'advanced').
 */
function remove_meta_box($id, $screen, $context) {
	global $wp_meta_boxes;

	if ( empty( $screen ) )
		$screen = get_current_screen();
	elseif ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	$page = $screen->id;

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
 * Meta Box Accordion Template Function
 *
 * Largely made up of abstracted code from {@link do_meta_boxes()}, this
 * function serves to build meta boxes as list items for display as
 * a collapsible accordion.
 *
 * @since 3.6.0
 *
 * @uses global $wp_meta_boxes Used to retrieve registered meta boxes.
 *
 * @param string|object $screen The screen identifier.
 * @param string $context The meta box context.
 * @param mixed $object gets passed to the section callback function as first parameter.
 * @return int number of meta boxes as accordion sections.
 */
function do_accordion_sections( $screen, $context, $object ) {
	global $wp_meta_boxes;

	wp_enqueue_script( 'accordion' );

	if ( empty( $screen ) )
		$screen = get_current_screen();
	elseif ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	$page = $screen->id;

	$hidden = get_hidden_meta_boxes( $screen );
	?>
	<div id="side-sortables" class="accordion-container">
		<ul class="outer-border">
	<?php
	$i = 0;
	$first_open = false;

	if ( isset( $wp_meta_boxes[ $page ][ $context ] ) ) {
		foreach ( array( 'high', 'core', 'default', 'low' ) as $priority ) {
			if ( isset( $wp_meta_boxes[ $page ][ $context ][ $priority ] ) ) {
				foreach ( $wp_meta_boxes[ $page ][ $context ][ $priority ] as $box ) {
					if ( false == $box || ! $box['title'] )
						continue;
					$i++;
					$hidden_class = in_array( $box['id'], $hidden ) ? 'hide-if-js' : '';

					$open_class = '';
					if ( ! $first_open && empty( $hidden_class ) ) {
						$first_open = true;
						$open_class = 'open';
					}
					?>
					<li class="control-section accordion-section <?php echo $hidden_class; ?> <?php echo $open_class; ?> <?php echo esc_attr( $box['id'] ); ?>" id="<?php echo esc_attr( $box['id'] ); ?>">
						<h3 class="accordion-section-title hndle" tabindex="0">
							<?php echo esc_html( $box['title'] ); ?>
							<span class="screen-reader-text"><?php _e( 'Press return or enter to expand' ); ?></span>
						</h3>
						<div class="accordion-section-content <?php postbox_classes( $box['id'], $page ); ?>">
							<div class="inside">
								<?php call_user_func( $box['callback'], $object, $box ); ?>
							</div><!-- .inside -->
						</div><!-- .accordion-section-content -->
					</li><!-- .accordion-section -->
					<?php
				}
			}
		}
	}
	?>
		</ul><!-- .outer-border -->
	</div><!-- .accordion-container -->
	<?php
	return $i;
}

/**
 * Add a new section to a settings page.
 *
 * Part of the Settings API. Use this to define new settings sections for an admin page.
 * Show settings sections in your admin page callback function with do_settings_sections().
 * Add settings fields to your section with add_settings_field()
 *
 * The $callback argument should be the name of a function that echoes out any
 * content you want to show at the top of the settings section before the actual
 * fields. It can output nothing if you want.
 *
 * @since 2.7.0
 *
 * @global $wp_settings_sections Storage array of all settings sections added to admin pages
 *
 * @param string $id Slug-name to identify the section. Used in the 'id' attribute of tags.
 * @param string $title Formatted title of the section. Shown as the heading for the section.
 * @param string $callback Function that echos out any content at the top of the section (between heading and fields).
 * @param string $page The slug-name of the settings page on which to show the section. Built-in pages include 'general', 'reading', 'writing', 'discussion', 'media', etc. Create your own using add_options_page();
 */
function add_settings_section($id, $title, $callback, $page) {
	global $wp_settings_sections;

	if ( 'misc' == $page ) {
		_deprecated_argument( __FUNCTION__, '3.0', sprintf( __( 'The "%s" options group has been removed. Use another settings group.' ), 'misc' ) );
		$page = 'general';
	}

	if ( 'privacy' == $page ) {
		_deprecated_argument( __FUNCTION__, '3.5', sprintf( __( 'The "%s" options group has been removed. Use another settings group.' ), 'privacy' ) );
		$page = 'reading';
	}

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
 * html input tags for this setting field. Use get_option() to retrieve existing
 * values to show.
 *
 * @since 2.7.0
 * @since 4.2.0 The `$class` argument was added.
 *
 * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections
 *
 * @param string $id       Slug-name to identify the field. Used in the 'id' attribute of tags.
 * @param string $title    Formatted title of the field. Shown as the label for the field
 *                         during output.
 * @param string $callback Function that fills the field with the desired form inputs. The
 *                         function should echo its output.
 * @param string $page     The slug-name of the settings page on which to show the section
 *                         (general, reading, writing, ...).
 * @param string $section  Optional. The slug-name of the section of the settings page
 *                         in which to show the box. Default 'default'.
 * @param array  $args {
 *     Optional. Extra arguments used when outputting the field.
 *
 *     @type string $label_for When supplied, the setting title will be wrapped
 *                             in a `<label>` element, its `for` attribute populated
 *                             with this value.
 *     @type string $class     CSS Class to be added to the `<tr>` element when the
 *                             field is output.
 * }
 */
function add_settings_field($id, $title, $callback, $page, $section = 'default', $args = array()) {
	global $wp_settings_fields;

	if ( 'misc' == $page ) {
		_deprecated_argument( __FUNCTION__, '3.0', __( 'The miscellaneous options group has been removed. Use another settings group.' ) );
		$page = 'general';
	}

	if ( 'privacy' == $page ) {
		_deprecated_argument( __FUNCTION__, '3.5', __( 'The privacy options group has been removed. Use another settings group.' ) );
		$page = 'reading';
	}

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
 * @since 2.7.0
 *
 * @param string $page The slug name of the page whos settings sections you want to output
 */
function do_settings_sections( $page ) {
	global $wp_settings_sections, $wp_settings_fields;

	if ( ! isset( $wp_settings_sections[$page] ) )
		return;

	foreach ( (array) $wp_settings_sections[$page] as $section ) {
		if ( $section['title'] )
			echo "<h3>{$section['title']}</h3>\n";

		if ( $section['callback'] )
			call_user_func( $section['callback'], $section );

		if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )
			continue;
		echo '<table class="form-table">';
		do_settings_fields( $page, $section['id'] );
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
 * @since 2.7.0
 *
 * @param string $page Slug title of the admin page who's settings fields you want to show.
 * @param string $section Slug title of the settings section who's fields you want to show.
 */
function do_settings_fields($page, $section) {
	global $wp_settings_fields;

	if ( ! isset( $wp_settings_fields[$page][$section] ) )
		return;

	foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
		$class = '';

		if ( ! empty( $field['args']['class'] ) ) {
			$class = ' class="' . esc_attr( $field['args']['class'] ) . '"';
		}

		echo "<tr{$class}>";

		if ( ! empty( $field['args']['label_for'] ) ) {
			echo '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label></th>';
		} else {
			echo '<th scope="row">' . $field['title'] . '</th>';
		}

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
 * @since 3.0.0
 *
 * @global array $wp_settings_errors Storage array of errors registered during this pageload
 *
 * @param string $setting Slug title of the setting to which this error applies
 * @param string $code    Slug-name to identify the error. Used as part of 'id' attribute in HTML output.
 * @param string $message The formatted message text to display to the user (will be shown inside styled
 *                        `<div>` and `<p>` tags).
 * @param string $type    Optional. Message type, controls HTML class. Accepts 'error' or 'updated'.
 *                        Default 'error'.
 */
function add_settings_error( $setting, $code, $message, $type = 'error' ) {
	global $wp_settings_errors;

	$wp_settings_errors[] = array(
		'setting' => $setting,
		'code'    => $code,
		'message' => $message,
		'type'    => $type
	);
}

/**
 * Fetch settings errors registered by add_settings_error()
 *
 * Checks the $wp_settings_errors array for any errors declared during the current
 * pageload and returns them.
 *
 * If changes were just submitted ($_GET['settings-updated']) and settings errors were saved
 * to the 'settings_errors' transient then those errors will be returned instead. This
 * is used to pass errors back across pageloads.
 *
 * Use the $sanitize argument to manually re-sanitize the option before returning errors.
 * This is useful if you have errors or notices you want to show even when the user
 * hasn't submitted data (i.e. when they first load an options page, or in admin_notices action hook)
 *
 * @since 3.0.0
 *
 * @global array $wp_settings_errors Storage array of errors registered during this pageload
 *
 * @param string $setting Optional slug title of a specific setting who's errors you want.
 * @param boolean $sanitize Whether to re-sanitize the setting value before returning errors.
 * @return array Array of settings errors
 */
function get_settings_errors( $setting = '', $sanitize = false ) {
	global $wp_settings_errors;

	/*
	 * If $sanitize is true, manually re-run the sanitization for this option
	 * This allows the $sanitize_callback from register_setting() to run, adding
	 * any settings errors you want to show by default.
	 */
	if ( $sanitize )
		sanitize_option( $setting, get_option( $setting ) );

	// If settings were passed back from options.php then use them.
	if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] && get_transient( 'settings_errors' ) ) {
		$wp_settings_errors = array_merge( (array) $wp_settings_errors, get_transient( 'settings_errors' ) );
		delete_transient( 'settings_errors' );
	}

	// Check global in case errors have been added on this pageload.
	if ( ! count( $wp_settings_errors ) )
		return array();

	// Filter the results to those of a specific setting if one was set.
	if ( $setting ) {
		$setting_errors = array();
		foreach ( (array) $wp_settings_errors as $key => $details ) {
			if ( $setting == $details['setting'] )
				$setting_errors[] = $wp_settings_errors[$key];
		}
		return $setting_errors;
	}

	return $wp_settings_errors;
}

/**
 * Display settings errors registered by {@see add_settings_error()}.
 *
 * Part of the Settings API. Outputs a div for each error retrieved by
 * {@see get_settings_errors()}.
 *
 * This is called automatically after a settings page based on the
 * Settings API is submitted. Errors should be added during the validation
 * callback function for a setting defined in {@see register_setting()}
 *
 * The $sanitize option is passed into {@see get_settings_errors()} and will
 * re-run the setting sanitization
 * on its current value.
 *
 * The $hide_on_update option will cause errors to only show when the settings
 * page is first loaded. if the user has already saved new values it will be
 * hidden to avoid repeating messages already shown in the default error
 * reporting after submission. This is useful to show general errors like
 * missing settings when the user arrives at the settings page.
 *
 * @since 3.0.0
 *
 * @param string $setting Optional slug title of a specific setting who's errors you want.
 * @param boolean $sanitize Whether to re-sanitize the setting value before returning errors.
 * @param boolean $hide_on_update If set to true errors will not be shown if the settings page has already been submitted.
 */
function settings_errors( $setting = '', $sanitize = false, $hide_on_update = false ) {

	if ( $hide_on_update && ! empty( $_GET['settings-updated'] ) )
		return;

	$settings_errors = get_settings_errors( $setting, $sanitize );

	if ( empty( $settings_errors ) )
		return;

	$output = '';
	foreach ( $settings_errors as $key => $details ) {
		$css_id = 'setting-error-' . $details['code'];
		$css_class = $details['type'] . ' settings-error notice is-dismissible';
		$output .= "<div id='$css_id' class='$css_class'> \n";
		$output .= "<p><strong>{$details['message']}</strong></p>";
		$output .= "</div> \n";
	}
	echo $output;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.7.0
 *
 * @param string $found_action
 */
function find_posts_div($found_action = '') {
?>
	<div id="find-posts" class="find-box" style="display: none;">
		<div id="find-posts-head" class="find-box-head">
			<?php _e( 'Find Posts or Pages' ); ?>
			<div id="find-posts-close"></div>
		</div>
		<div class="find-box-inside">
			<div class="find-box-search">
				<?php if ( $found_action ) { ?>
					<input type="hidden" name="found_action" value="<?php echo esc_attr($found_action); ?>" />
				<?php } ?>
				<input type="hidden" name="affected" id="affected" value="" />
				<?php wp_nonce_field( 'find-posts', '_ajax_nonce', false ); ?>
				<label class="screen-reader-text" for="find-posts-input"><?php _e( 'Search' ); ?></label>
				<input type="text" id="find-posts-input" name="ps" value="" />
				<span class="spinner"></span>
				<input type="button" id="find-posts-search" value="<?php esc_attr_e( 'Search' ); ?>" class="button" />
				<div class="clear"></div>
			</div>
			<div id="find-posts-response"></div>
		</div>
		<div class="find-box-buttons">
			<?php submit_button( __( 'Select' ), 'button-primary alignright', 'find-posts-submit', false ); ?>
			<div class="clear"></div>
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
 * @since 2.7.0
 */
function the_post_password() {
	$post = get_post();
	if ( isset( $post->post_password ) )
		echo esc_attr( $post->post_password );
}

/**
 * Get the post title.
 *
 * The post title is fetched and if it is blank then a default string is
 * returned.
 *
 * @since 2.7.0
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return string The post title if set.
 */
function _draft_or_post_title( $post = 0 ) {
	$title = get_the_title( $post );
	if ( empty( $title ) )
		$title = __( '(no title)' );
	return esc_html( $title );
}

/**
 * Display the search query.
 *
 * A simple wrapper to display the "s" parameter in a GET URI. This function
 * should only be used when {@link the_search_query()} cannot.
 *
 * @since 2.7.0
 */
function _admin_search_query() {
	echo isset($_REQUEST['s']) ? esc_attr( wp_unslash( $_REQUEST['s'] ) ) : '';
}

/**
 * Generic Iframe header for use with Thickbox
 *
 * @since 2.7.0
 *
 * @param string $title      Optional. Title of the Iframe page. Default empty.
 * @param bool   $deprecated Not used.
 */
function iframe_header( $title = '', $deprecated = false ) {
	show_admin_bar( false );
	global $hook_suffix, $admin_body_class, $wp_locale;
	$admin_body_class = preg_replace('/[^a-z0-9_-]+/i', '-', $hook_suffix);

	$current_screen = get_current_screen();

	@header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );
	_wp_admin_html_begin();
?>
<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title ?> &#8212; <?php _e('WordPress'); ?></title>
<?php
wp_enqueue_style( 'colors' );
?>
<script type="text/javascript">
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
function tb_close(){var win=window.dialogArguments||opener||parent||top;win.tb_remove();}
var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>',
	pagenow = '<?php echo $current_screen->id; ?>',
	typenow = '<?php echo $current_screen->post_type; ?>',
	adminpage = '<?php echo $admin_body_class; ?>',
	thousandsSeparator = '<?php echo addslashes( $wp_locale->number_format['thousands_sep'] ); ?>',
	decimalPoint = '<?php echo addslashes( $wp_locale->number_format['decimal_point'] ); ?>',
	isRtl = <?php echo (int) is_rtl(); ?>;
</script>
<?php
/** This action is documented in wp-admin/admin-header.php */
do_action( 'admin_enqueue_scripts', $hook_suffix );

/** This action is documented in wp-admin/admin-header.php */
do_action( "admin_print_styles-$hook_suffix" );

/** This action is documented in wp-admin/admin-header.php */
do_action( 'admin_print_styles' );

/** This action is documented in wp-admin/admin-header.php */
do_action( "admin_print_scripts-$hook_suffix" );

/** This action is documented in wp-admin/admin-header.php */
do_action( 'admin_print_scripts' );

/** This action is documented in wp-admin/admin-header.php */
do_action( "admin_head-$hook_suffix" );

/** This action is documented in wp-admin/admin-header.php */
do_action( 'admin_head' );

$admin_body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) );

if ( is_rtl() )
	$admin_body_class .= ' rtl';

?>
</head>
<?php
/** This filter is documented in wp-admin/admin-header.php */
$admin_body_classes = apply_filters( 'admin_body_class', '' );
?>
<body<?php if ( isset($GLOBALS['body_id']) ) echo ' id="' . $GLOBALS['body_id'] . '"'; ?> class="wp-admin wp-core-ui no-js iframe <?php echo $admin_body_classes . ' ' . $admin_body_class; ?>">
<script type="text/javascript">
(function(){
var c = document.body.className;
c = c.replace(/no-js/, 'js');
document.body.className = c;
})();
</script>
<?php
}

/**
 * Generic Iframe footer for use with Thickbox
 *
 * @since 2.7.0
 */
function iframe_footer() {
	/*
	 * We're going to hide any footer output on iFrame pages,
	 * but run the hooks anyway since they output JavaScript
	 * or other needed content.
	 */
	 ?>
	<div class="hidden">
<?php
	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_footer', '' );

	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_print_footer_scripts' );
?>
	</div>
<script type="text/javascript">if(typeof wpOnload=="function")wpOnload();</script>
</body>
</html>
<?php
}

function _post_states($post) {
	$post_states = array();
	if ( isset( $_REQUEST['post_status'] ) )
		$post_status = $_REQUEST['post_status'];
	else
		$post_status = '';

	if ( !empty($post->post_password) )
		$post_states['protected'] = __('Password protected');
	if ( 'private' == $post->post_status && 'private' != $post_status )
		$post_states['private'] = __('Private');
	if ( 'draft' == $post->post_status && 'draft' != $post_status )
		$post_states['draft'] = __('Draft');
	if ( 'pending' == $post->post_status && 'pending' != $post_status )
		/* translators: post state */
		$post_states['pending'] = _x('Pending', 'post state');
	if ( is_sticky($post->ID) )
		$post_states['sticky'] = __('Sticky');

	if ( get_option( 'page_on_front' ) == $post->ID ) {
		$post_states['page_on_front'] = __( 'Front Page' );
	}

	if ( get_option( 'page_for_posts' ) == $post->ID ) {
		$post_states['page_for_posts'] = __( 'Posts Page' );
	}

	/**
	 * Filter the default post display states used in the posts list table.
	 *
	 * @since 2.8.0
	 *
	 * @param array $post_states An array of post display states.
	 * @param int   $post        The post ID.
	 */
	$post_states = apply_filters( 'display_post_states', $post_states, $post );

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

function _media_states( $post ) {
	$media_states = array();
	$stylesheet = get_option('stylesheet');

	if ( current_theme_supports( 'custom-header') ) {
		$meta_header = get_post_meta($post->ID, '_wp_attachment_is_custom_header', true );
		if ( ! empty( $meta_header ) && $meta_header == $stylesheet )
			$media_states[] = __( 'Header Image' );
	}

	if ( current_theme_supports( 'custom-background') ) {
		$meta_background = get_post_meta($post->ID, '_wp_attachment_is_custom_background', true );
		if ( ! empty( $meta_background ) && $meta_background == $stylesheet )
			$media_states[] = __( 'Background Image' );
	}

	/**
	 * Filter the default media display states for items in the Media list table.
	 *
	 * @since 3.2.0
	 *
	 * @param array $media_states An array of media states. Default 'Header Image',
	 *                            'Background Image'.
	 */
	$media_states = apply_filters( 'display_media_states', $media_states );

	if ( ! empty( $media_states ) ) {
		$state_count = count( $media_states );
		$i = 0;
		echo ' - ';
		foreach ( $media_states as $state ) {
			++$i;
			( $i == $state_count ) ? $sep = '' : $sep = ', ';
			echo "<span class='post-state'>$state$sep</span>";
		}
	}
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
				};

				x.open('GET', ajaxurl + '?action=wp-compression-test&test='+test+'&'+(new Date()).getTime(), true);
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
	</script>
<?php
}

/**
 * Echoes a submit button, with provided text and appropriate class(es).
 *
 * @since 3.1.0
 *
 * @see get_submit_button()
 *
 * @param string       $text             The text of the button (defaults to 'Save Changes')
 * @param string       $type             Optional. The type and CSS class(es) of the button. Core values
 *                                       include 'primary', 'secondary', 'delete'. Default 'primary'
 * @param string       $name             The HTML name of the submit button. Defaults to "submit". If no
 *                                       id attribute is given in $other_attributes below, $name will be
 *                                       used as the button's id.
 * @param bool         $wrap             True if the output button should be wrapped in a paragraph tag,
 *                                       false otherwise. Defaults to true
 * @param array|string $other_attributes Other attributes that should be output with the button, mapping
 *                                       attributes to their values, such as setting tabindex to 1, etc.
 *                                       These key/value attribute pairs will be output as attribute="value",
 *                                       where attribute is the key. Other attributes can also be provided
 *                                       as a string such as 'tabindex="1"', though the array format is
 *                                       preferred. Default null.
 */
function submit_button( $text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null ) {
	echo get_submit_button( $text, $type, $name, $wrap, $other_attributes );
}

/**
 * Returns a submit button, with provided text and appropriate class
 *
 * @since 3.1.0
 *
 * @param string       $text             Optional. The text of the button. Default 'Save Changes'.
 * @param string       $type             Optional. The type of button. Accepts 'primary', 'secondary',
 *                                       or 'delete'. Default 'primary large'.
 * @param string       $name             Optional. The HTML name of the submit button. Defaults to "submit".
 *                                       If no id attribute is given in $other_attributes below, `$name` will
 *                                       be used as the button's id. Default 'submit'.
 * @param bool         $wrap             Optional. True if the output button should be wrapped in a paragraph
 *                                       tag, false otherwise. Default true.
 * @param array|string $other_attributes Optional. Other attributes that should be output with the button,
 *                                       mapping attributes to their values, such as `array( 'tabindex' => '1' )`.
 *                                       These attributes will be output as `attribute="value"`, such as
 *                                       `tabindex="1"`. Other attributes can also be provided as a string such
 *                                       as `tabindex="1"`, though the array format is typically cleaner.
 *                                       Default empty.
 * @return string Submit button HTML.
 */
function get_submit_button( $text = '', $type = 'primary large', $name = 'submit', $wrap = true, $other_attributes = '' ) {
	if ( ! is_array( $type ) )
		$type = explode( ' ', $type );

	$button_shorthand = array( 'primary', 'small', 'large' );
	$classes = array( 'button' );
	foreach ( $type as $t ) {
		if ( 'secondary' === $t || 'button-secondary' === $t )
			continue;
		$classes[] = in_array( $t, $button_shorthand ) ? 'button-' . $t : $t;
	}
	$class = implode( ' ', array_unique( $classes ) );

	if ( 'delete' === $type )
		$class = 'button-secondary delete';

	$text = $text ? $text : __( 'Save Changes' );

	// Default the id attribute to $name unless an id was specifically provided in $other_attributes
	$id = $name;
	if ( is_array( $other_attributes ) && isset( $other_attributes['id'] ) ) {
		$id = $other_attributes['id'];
		unset( $other_attributes['id'] );
	}

	$attributes = '';
	if ( is_array( $other_attributes ) ) {
		foreach ( $other_attributes as $attribute => $value ) {
			$attributes .= $attribute . '="' . esc_attr( $value ) . '" '; // Trailing space is important
		}
	} elseif ( ! empty( $other_attributes ) ) { // Attributes provided as a string
		$attributes = $other_attributes;
	}

	// Don't output empty name and id attributes.
	$name_attr = $name ? ' name="' . esc_attr( $name ) . '"' : '';
	$id_attr = $id ? ' id="' . esc_attr( $id ) . '"' : '';

	$button = '<input type="submit"' . $name_attr . $id_attr . ' class="' . esc_attr( $class );
	$button	.= '" value="' . esc_attr( $text ) . '" ' . $attributes . ' />';

	if ( $wrap ) {
		$button = '<p class="submit">' . $button . '</p>';
	}

	return $button;
}

function _wp_admin_html_begin() {
	global $is_IE;

	$admin_html_class = ( is_admin_bar_showing() ) ? 'wp-toolbar' : '';

	if ( $is_IE )
		@header('X-UA-Compatible: IE=edge');

?>
<!DOCTYPE html>
<!--[if IE 8]>
<html xmlns="http://www.w3.org/1999/xhtml" class="ie8 <?php echo $admin_html_class; ?>" <?php
	/**
	 * Fires inside the HTML tag in the admin header.
	 *
	 * @since 2.2.0
	 */
	do_action( 'admin_xml_ns' );
?> <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 8) ]><!-->
<html xmlns="http://www.w3.org/1999/xhtml" class="<?php echo $admin_html_class; ?>" <?php
	/** This action is documented in wp-admin/includes/template.php */
	do_action( 'admin_xml_ns' );
?> <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<?php
}

final class WP_Internal_Pointers {
	/**
	 * Initializes the new feature pointers.
	 *
	 * @since 3.3.0
	 *
	 * All pointers can be disabled using the following:
	 *     remove_action( 'admin_enqueue_scripts', array( 'WP_Internal_Pointers', 'enqueue_scripts' ) );
	 *
	 * Individual pointers (e.g. wp390_widgets) can be disabled using the following:
	 *     remove_action( 'admin_print_footer_scripts', array( 'WP_Internal_Pointers', 'pointer_wp390_widgets' ) );
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public static function enqueue_scripts( $hook_suffix ) {
		/*
		 * Register feature pointers
		 * Format: array( hook_suffix => pointer_id )
		 */

		$registered_pointers = array(
			'post-new.php' => 'wp410_dfw',
			'post.php'     => 'wp410_dfw',
			'edit.php'     => 'wp360_locks',
			'widgets.php'  => 'wp390_widgets',
			'themes.php'   => 'wp390_widgets',
		);

		// Check if screen related pointer is registered
		if ( empty( $registered_pointers[ $hook_suffix ] ) )
			return;

		$pointers = (array) $registered_pointers[ $hook_suffix ];

		$caps_required = array(
			'wp390_widgets' => array( 'edit_theme_options' ),
		);

		// Get dismissed pointers
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

		$got_pointers = false;
		foreach ( array_diff( $pointers, $dismissed ) as $pointer ) {
			if ( isset( $caps_required[ $pointer ] ) ) {
				foreach ( $caps_required[ $pointer ] as $cap ) {
					if ( ! current_user_can( $cap ) )
						continue 2;
				}
			}

			// Bind pointer print function
			add_action( 'admin_print_footer_scripts', array( 'WP_Internal_Pointers', 'pointer_' . $pointer ) );
			$got_pointers = true;
		}

		if ( ! $got_pointers )
			return;

		// Add pointers script and style to queue
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}

	/**
	 * Print the pointer JavaScript data.
	 *
	 * @since 3.3.0
	 *
	 * @param string $pointer_id The pointer ID.
	 * @param string $selector The HTML elements, on which the pointer should be attached.
	 * @param array  $args Arguments to be passed to the pointer JS (see wp-pointer.js).
	 */
	private static function print_js( $pointer_id, $selector, $args ) {
		if ( empty( $pointer_id ) || empty( $selector ) || empty( $args ) || empty( $args['content'] ) )
			return;

		?>
		<script type="text/javascript">
		(function($){
			var options = <?php echo wp_json_encode( $args ); ?>, setup;

			if ( ! options )
				return;

			options = $.extend( options, {
				close: function() {
					$.post( ajaxurl, {
						pointer: '<?php echo $pointer_id; ?>',
						action: 'dismiss-wp-pointer'
					});
				}
			});

			setup = function() {
				$('<?php echo $selector; ?>').first().pointer( options ).pointer('open');
			};

			if ( options.position && options.position.defer_loading )
				$(window).bind( 'load.wp-pointers', setup );
			else
				$(document).ready( setup );

		})( jQuery );
		</script>
		<?php
	}

	public static function pointer_wp330_toolbar() {}
	public static function pointer_wp330_media_uploader() {}
	public static function pointer_wp330_saving_widgets() {}
	public static function pointer_wp340_customize_current_theme_link() {}
	public static function pointer_wp340_choose_image_from_library() {}
	public static function pointer_wp350_media() {}
	public static function pointer_wp360_revisions() {}

	public static function pointer_wp360_locks() {
		if ( ! is_multi_author() ) {
			return;
		}

		$content  = '<h3>' . __( 'Edit Lock' ) . '</h3>';
		$content .= '<p>' . __( 'Someone else is editing this. No need to refresh; the lock will disappear when they&#8217;re done.' ) . '</p>';

		self::print_js( 'wp360_locks', 'tr.wp-locked .locked-indicator', array(
			'content' => $content,
			'position' => array( 'edge' => 'left', 'align' => 'left' ),
		) );
	}

	public static function pointer_wp390_widgets() {
		if ( ! current_theme_supports( 'widgets' ) ) {
			return;
		}

		$content  = '<h3>' . __( 'New Feature: Live Widget Previews' ) . '</h3>';
		$content .= '<p>' . __( 'Add, edit, and play around with your widgets from the Customizer.' ) . ' ' . __( 'Preview your changes in real-time and only save them when you&#8217;re ready.' ) . '</p>';

		if ( 'themes' === get_current_screen()->id ) {
			$selector = '.theme.active .customize';
			$position = array( 'edge' => is_rtl() ? 'right' : 'left', 'align' => 'center' );
		} else {
			$selector = 'a[href^="customize.php"]';
			if ( is_rtl() ) {
				$position = array( 'edge' => 'right', 'align' => 'center', 'my' => 'right-5px' );
			} else {
				$position = array( 'edge' => 'left', 'align' => 'center', 'my' => 'left-5px' );
			}
		}

		self::print_js( 'wp390_widgets', $selector, array(
			'content' => $content,
			'position' => $position,
		) );
	}

	public static function pointer_wp410_dfw() {
		// Don't show when editor-scrolling is not used.
		if ( empty( $GLOBALS['_wp_editor_expand'] ) ) {
			return;
		}

		$content  = '<h3>' . __( 'Distraction-Free Writing' ) . '</h3>';
		$content .= '<p>' . __( 'Enable distraction-free writing mode, and everything surrounding the editor will fade away when you start typing. Move your mouse out of the editor to reveal everything again.' ) . '</p>';

		if ( is_rtl() ) {
			$position = array( 'edge' => 'left', 'align' => 'center', 'my' => 'left+40 top-11', 'at' => 'left top' );
		} else {
			$position = array( 'edge' => 'right', 'align' => 'center', 'my' => 'right-40 top-11', 'at' => 'right top' );
		}

		self::print_js( 'wp410_dfw', '#wp-content-wrap', array(
			'content' => $content,
			'position' => $position,
		) );
	}

	/**
	 * Prevents new users from seeing existing 'new feature' pointers.
	 *
	 * @since 3.3.0
	 *
	 * @param int $user_id User ID.
	 */
	public static function dismiss_pointers_for_new_users( $user_id ) {
		add_user_meta( $user_id, 'dismissed_wp_pointers', 'wp360_locks,wp390_widgets' );
	}
}

add_action( 'admin_enqueue_scripts', array( 'WP_Internal_Pointers', 'enqueue_scripts'                ) );
add_action( 'user_register',         array( 'WP_Internal_Pointers', 'dismiss_pointers_for_new_users' ) );

/**
 * Convert a screen string to a screen object
 *
 * @since 3.0.0
 *
 * @param string $hook_name The hook name (also known as the hook suffix) used to determine the screen.
 * @return WP_Screen Screen object.
 */
function convert_to_screen( $hook_name ) {
	if ( ! class_exists( 'WP_Screen' ) ) {
		_doing_it_wrong( 'convert_to_screen(), add_meta_box()', __( "Likely direct inclusion of wp-admin/includes/template.php in order to use add_meta_box(). This is very wrong. Hook the add_meta_box() call into the add_meta_boxes action instead." ), '3.3' );
		return (object) array( 'id' => '_invalid', 'base' => '_are_belong_to_us' );
	}

	return WP_Screen::get( $hook_name );
}

/**
 * Output the HTML for restoring the post data from DOM storage
 *
 * @since 3.6.0
 * @access private
 */
function _local_storage_notice() {
	?>
	<div id="local-storage-notice" class="hidden notice">
	<p class="local-restore">
		<?php _e('The backup of this post in your browser is different from the version below.'); ?>
		<a class="restore-backup" href="#"><?php _e('Restore the backup.'); ?></a>
	</p>
	<p class="undo-restore hidden">
		<?php _e('Post restored successfully.'); ?>
		<a class="undo-restore-backup" href="#"><?php _e('Undo.'); ?></a>
	</p>
	</div>
	<?php
}

/**
 * Output a HTML element with a star rating for a given rating.
 *
 * Outputs a HTML element with the star rating exposed on a 0..5 scale in
 * half star increments (ie. 1, 1.5, 2 stars). Optionally, if specified, the
 * number of ratings may also be displayed by passing the $number parameter.
 *
 * @since 3.8.0
 * @param array $args {
 *     Optional. Array of star ratings arguments.
 *
 *     @type int    $rating The rating to display, expressed in either a 0.5 rating increment,
 *                          or percentage. Default 0.
 *     @type string $type   Format that the $rating is in. Valid values are 'rating' (default),
 *                          or, 'percent'. Default 'rating'.
 *     @type int    $number The number of ratings that makes up this rating. Default 0.
 * }
 */
function wp_star_rating( $args = array() ) {
	$defaults = array(
		'rating' => 0,
		'type' => 'rating',
		'number' => 0,
	);
	$r = wp_parse_args( $args, $defaults );

	// Non-english decimal places when the $rating is coming from a string
	$rating = str_replace( ',', '.', $r['rating'] );

	// Convert Percentage to star rating, 0..5 in .5 increments
	if ( 'percent' == $r['type'] ) {
		$rating = round( $rating / 10, 0 ) / 2;
	}

	// Calculate the number of each type of star needed
	$full_stars = floor( $rating );
	$half_stars = ceil( $rating - $full_stars );
	$empty_stars = 5 - $full_stars - $half_stars;

	if ( $r['number'] ) {
		/* translators: 1: The rating, 2: The number of ratings */
		$format = _n( '%1$s rating based on %2$s rating', '%1$s rating based on %2$s ratings', $r['number'] );
		$title = sprintf( $format, number_format_i18n( $rating, 1 ), number_format_i18n( $r['number'] ) );
	} else {
		/* translators: 1: The rating */
		$title = sprintf( __( '%s rating' ), number_format_i18n( $rating, 1 ) );
	}

	echo '<div class="star-rating" title="' . esc_attr( $title ) . '">';
	echo '<span class="screen-reader-text">' . $title . '</span>';
	echo str_repeat( '<div class="star star-full"></div>', $full_stars );
	echo str_repeat( '<div class="star star-half"></div>', $half_stars );
	echo str_repeat( '<div class="star star-empty"></div>', $empty_stars);
	echo '</div>';
}

/**
 * Output a notice when editing the page for posts (internal use only).
 *
 * @ignore
 * @since 4.2.0
 */
function _wp_posts_page_notice() {
	echo '<div class="notice notice-warning inline"><p>' . __( 'You are currently editing the page that shows your latest posts.' ) . '</p></div>';
}
