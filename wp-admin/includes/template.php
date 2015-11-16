<?php
/**
 * Template WordPress Administration API.
 *
 * A Big Mess. Also some neat functions that are nicely written.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Walker_Category_Checklist class */
require_once( ABSPATH . 'wp-admin/includes/class-walker-category-checklist.php' );

<<<<<<< HEAD
/** WP_Internal_Pointers class */
require_once( ABSPATH . 'wp-admin/includes/class-wp-internal-pointers.php' );
=======
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
 * @global WP_List_Table $wp_list_table
 *
 * @param int    $position
 * @param bool   $checkbox
 * @param string $mode
 * @param bool   $table_row
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
<table style="display:none;"><tbody id="com-reply"><tr id="replyrow" class="inline-edit-row" style="display:none;"><td colspan="<?php echo $wp_list_table->get_column_count(); ?>" class="colspanchange">
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
 * @staticvar string $update_nonce
 *
 * @param array $entry
 * @param int   $count
 * @return string
 */
function _list_meta_row( $entry, &$count ) {
	static $update_nonce = '';

	if ( is_protected_meta( $entry['meta_key'], 'post' ) )
		return '';

	if ( ! $update_nonce )
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
 * @global wpdb $wpdb
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
	$sql = "SELECT DISTINCT meta_key
		FROM $wpdb->postmeta
		WHERE meta_key NOT BETWEEN '_' AND '_z'
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
 * @global WP_Locale $wp_locale
 * @global object    $comment
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

	$month = '<label><span class="screen-reader-text">' . __( 'Month' ) . '</span><select ' . ( $multi ? '' : 'id="mm" ' ) . 'name="mm"' . $tab_index_attribute . ">\n";
	for ( $i = 1; $i < 13; $i = $i +1 ) {
		$monthnum = zeroise($i, 2);
		$monthtext = $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) );
		$month .= "\t\t\t" . '<option value="' . $monthnum . '" data-text="' . $monthtext . '" ' . selected( $monthnum, $mm, false ) . '>';
		/* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
		$month .= sprintf( __( '%1$s-%2$s' ), $monthnum, $monthtext ) . "</option>\n";
	}
	$month .= '</select></label>';

	$day = '<label><span class="screen-reader-text">' . __( 'Day' ) . '</span><input type="text" ' . ( $multi ? '' : 'id="jj" ' ) . 'name="jj" value="' . $jj . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" /></label>';
	$year = '<label><span class="screen-reader-text">' . __( 'Year' ) . '</span><input type="text" ' . ( $multi ? '' : 'id="aa" ' ) . 'name="aa" value="' . $aa . '" size="4" maxlength="4"' . $tab_index_attribute . ' autocomplete="off" /></label>';
	$hour = '<label><span class="screen-reader-text">' . __( 'Hour' ) . '</span><input type="text" ' . ( $multi ? '' : 'id="hh" ' ) . 'name="hh" value="' . $hh . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" /></label>';
	$minute = '<label><span class="screen-reader-text">' . __( 'Minute' ) . '</span><input type="text" ' . ( $multi ? '' : 'id="mn" ' ) . 'name="mn" value="' . $mn . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" /></label>';

	echo '<div class="timestamp-wrap">';
	/* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
	printf( __( '%1$s %2$s, %3$s @ %4$s:%5$s' ), $month, $day, $year, $hour, $minute );

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
		echo "\n\t<option value='" . $templates[ $template ] . "' $selected>$template</option>";
	}
}

/**
 * Print out option HTML elements for the page parents drop-down.
 *
 * @since 1.5.0
 *
 * @global wpdb $wpdb
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
 * @global array $wp_meta_boxes
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
 * @global array $wp_meta_boxes
 *
 * @staticvar bool $already_sorted
 * @param string|WP_Screen $screen  Screen identifier
 * @param string           $context box context
 * @param mixed            $object  gets passed to the box callback function as first parameter
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
 * @global array $wp_meta_boxes
 *
 * @param string        $id      String for use in the 'id' attribute of tags.
 * @param string|object $screen  The screen on which to show the box (post, page, link).
 * @param string        $context The context within the page where the boxes should show ('normal', 'advanced').
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
 * @param string|object $screen  The screen identifier.
 * @param string        $context The meta box context.
 * @param mixed         $object  gets passed to the section callback function as first parameter.
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
>>>>>>> refs/remotes/origin/4.3-branch

/** Admin template functionality */
require_once( ABSPATH . 'wp-admin/includes/template-functions.php' );
