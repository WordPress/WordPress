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
require_once ABSPATH . 'wp-admin/includes/class-walker-category-checklist.php';

/** WP_Internal_Pointers class */
require_once ABSPATH . 'wp-admin/includes/class-wp-internal-pointers.php';

//
// Category Checklists.
//

/**
 * Outputs an unordered list of checkbox input elements labeled with category names.
 *
 * @since 2.5.1
 *
 * @see wp_terms_checklist()
 *
 * @param int         $post_id              Optional. Post to generate a categories checklist for. Default 0.
 *                                          $selected_cats must not be an array. Default 0.
 * @param int         $descendants_and_self Optional. ID of the category to output along with its descendants.
 *                                          Default 0.
 * @param int[]|false $selected_cats        Optional. Array of category IDs to mark as checked. Default false.
 * @param int[]|false $popular_cats         Optional. Array of category IDs to receive the "popular-category" class.
 *                                          Default false.
 * @param Walker      $walker               Optional. Walker object to use to build the output.
 *                                          Default is a Walker_Category_Checklist instance.
 * @param bool        $checked_ontop        Optional. Whether to move checked items out of the hierarchy and to
 *                                          the top of the list. Default true.
 */
function wp_category_checklist( $post_id = 0, $descendants_and_self = 0, $selected_cats = false, $popular_cats = false, $walker = null, $checked_ontop = true ) {
	wp_terms_checklist(
		$post_id,
		array(
			'taxonomy'             => 'category',
			'descendants_and_self' => $descendants_and_self,
			'selected_cats'        => $selected_cats,
			'popular_cats'         => $popular_cats,
			'walker'               => $walker,
			'checked_ontop'        => $checked_ontop,
		)
	);
}

/**
 * Outputs an unordered list of checkbox input elements labelled with term names.
 *
 * Taxonomy-independent version of wp_category_checklist().
 *
 * @since 3.0.0
 * @since 4.4.0 Introduced the `$echo` argument.
 *
 * @param int          $post_id Optional. Post ID. Default 0.
 * @param array|string $args {
 *     Optional. Array or string of arguments for generating a terms checklist. Default empty array.
 *
 *     @type int    $descendants_and_self ID of the category to output along with its descendants.
 *                                        Default 0.
 *     @type int[]  $selected_cats        Array of category IDs to mark as checked. Default false.
 *     @type int[]  $popular_cats         Array of category IDs to receive the "popular-category" class.
 *                                        Default false.
 *     @type Walker $walker               Walker object to use to build the output. Default empty which
 *                                        results in a Walker_Category_Checklist instance being used.
 *     @type string $taxonomy             Taxonomy to generate the checklist for. Default 'category'.
 *     @type bool   $checked_ontop        Whether to move checked items out of the hierarchy and to
 *                                        the top of the list. Default true.
 *     @type bool   $echo                 Whether to echo the generated markup. False to return the markup instead
 *                                        of echoing it. Default true.
 * }
 * @return string HTML list of input elements.
 */
function wp_terms_checklist( $post_id = 0, $args = array() ) {
	$defaults = array(
		'descendants_and_self' => 0,
		'selected_cats'        => false,
		'popular_cats'         => false,
		'walker'               => null,
		'taxonomy'             => 'category',
		'checked_ontop'        => true,
		'echo'                 => true,
	);

	/**
	 * Filters the taxonomy terms checklist arguments.
	 *
	 * @since 3.4.0
	 *
	 * @see wp_terms_checklist()
	 *
	 * @param array|string $args    An array or string of arguments.
	 * @param int          $post_id The post ID.
	 */
	$params = apply_filters( 'wp_terms_checklist_args', $args, $post_id );

	$parsed_args = wp_parse_args( $params, $defaults );

	if ( empty( $parsed_args['walker'] ) || ! ( $parsed_args['walker'] instanceof Walker ) ) {
		$walker = new Walker_Category_Checklist();
	} else {
		$walker = $parsed_args['walker'];
	}

	$taxonomy             = $parsed_args['taxonomy'];
	$descendants_and_self = (int) $parsed_args['descendants_and_self'];

	$args = array( 'taxonomy' => $taxonomy );

	$tax              = get_taxonomy( $taxonomy );
	$args['disabled'] = ! current_user_can( $tax->cap->assign_terms );

	$args['list_only'] = ! empty( $parsed_args['list_only'] );

	if ( is_array( $parsed_args['selected_cats'] ) ) {
		$args['selected_cats'] = array_map( 'intval', $parsed_args['selected_cats'] );
	} elseif ( $post_id ) {
		$args['selected_cats'] = wp_get_object_terms( $post_id, $taxonomy, array_merge( $args, array( 'fields' => 'ids' ) ) );
	} else {
		$args['selected_cats'] = array();
	}

	if ( is_array( $parsed_args['popular_cats'] ) ) {
		$args['popular_cats'] = array_map( 'intval', $parsed_args['popular_cats'] );
	} else {
		$args['popular_cats'] = get_terms(
			array(
				'taxonomy'     => $taxonomy,
				'fields'       => 'ids',
				'orderby'      => 'count',
				'order'        => 'DESC',
				'number'       => 10,
				'hierarchical' => false,
			)
		);
	}

	if ( $descendants_and_self ) {
		$categories = (array) get_terms(
			array(
				'taxonomy'     => $taxonomy,
				'child_of'     => $descendants_and_self,
				'hierarchical' => 0,
				'hide_empty'   => 0,
			)
		);
		$self       = get_term( $descendants_and_self, $taxonomy );
		array_unshift( $categories, $self );
	} else {
		$categories = (array) get_terms(
			array(
				'taxonomy' => $taxonomy,
				'get'      => 'all',
			)
		);
	}

	$output = '';

	if ( $parsed_args['checked_ontop'] ) {
		/*
		 * Post-process $categories rather than adding an exclude to the get_terms() query
		 * to keep the query the same across all posts (for any query cache).
		 */
		$checked_categories = array();
		$keys               = array_keys( $categories );

		foreach ( $keys as $k ) {
			if ( in_array( $categories[ $k ]->term_id, $args['selected_cats'], true ) ) {
				$checked_categories[] = $categories[ $k ];
				unset( $categories[ $k ] );
			}
		}

		// Put checked categories on top.
		$output .= $walker->walk( $checked_categories, 0, $args );
	}
	// Then the rest of them.
	$output .= $walker->walk( $categories, 0, $args );

	if ( $parsed_args['echo'] ) {
		echo $output;
	}

	return $output;
}

/**
 * Retrieves a list of the most popular terms from the specified taxonomy.
 *
 * If the `$display` argument is true then the elements for a list of checkbox
 * `<input>` elements labelled with the names of the selected terms is output.
 * If the `$post_ID` global is not empty then the terms associated with that
 * post will be marked as checked.
 *
 * @since 2.5.0
 *
 * @param string $taxonomy     Taxonomy to retrieve terms from.
 * @param int    $default_term Optional. Not used.
 * @param int    $number       Optional. Number of terms to retrieve. Default 10.
 * @param bool   $display      Optional. Whether to display the list as well. Default true.
 * @return int[] Array of popular term IDs.
 */
function wp_popular_terms_checklist( $taxonomy, $default_term = 0, $number = 10, $display = true ) {
	$post = get_post();

	if ( $post && $post->ID ) {
		$checked_terms = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );
	} else {
		$checked_terms = array();
	}

	$terms = get_terms(
		array(
			'taxonomy'     => $taxonomy,
			'orderby'      => 'count',
			'order'        => 'DESC',
			'number'       => $number,
			'hierarchical' => false,
		)
	);

	$tax = get_taxonomy( $taxonomy );

	$popular_ids = array();

	foreach ( (array) $terms as $term ) {
		$popular_ids[] = $term->term_id;

		if ( ! $display ) { // Hack for Ajax use.
			continue;
		}

		$id      = "popular-$taxonomy-$term->term_id";
		$checked = in_array( $term->term_id, $checked_terms, true ) ? 'checked="checked"' : '';
		?>

		<li id="<?php echo $id; ?>" class="popular-category">
			<label class="selectit">
				<input id="in-<?php echo $id; ?>" type="checkbox" <?php echo $checked; ?> value="<?php echo (int) $term->term_id; ?>" <?php disabled( ! current_user_can( $tax->cap->assign_terms ) ); ?> />
				<?php
				/** This filter is documented in wp-includes/category-template.php */
				echo esc_html( apply_filters( 'the_category', $term->name, '', '' ) );
				?>
			</label>
		</li>

		<?php
	}
	return $popular_ids;
}

/**
 * Outputs a link category checklist element.
 *
 * @since 2.5.1
 *
 * @param int $link_id Optional. The link ID. Default 0.
 */
function wp_link_category_checklist( $link_id = 0 ) {
	$default = 1;

	$checked_categories = array();

	if ( $link_id ) {
		$checked_categories = wp_get_link_cats( $link_id );
		// No selected categories, strange.
		if ( ! count( $checked_categories ) ) {
			$checked_categories[] = $default;
		}
	} else {
		$checked_categories[] = $default;
	}

	$categories = get_terms(
		array(
			'taxonomy'   => 'link_category',
			'orderby'    => 'name',
			'hide_empty' => 0,
		)
	);

	if ( empty( $categories ) ) {
		return;
	}

	foreach ( $categories as $category ) {
		$cat_id = $category->term_id;

		/** This filter is documented in wp-includes/category-template.php */
		$name    = esc_html( apply_filters( 'the_category', $category->name, '', '' ) );
		$checked = in_array( $cat_id, $checked_categories, true ) ? ' checked="checked"' : '';
		echo '<li id="link-category-', $cat_id, '"><label for="in-link-category-', $cat_id, '" class="selectit"><input value="', $cat_id, '" type="checkbox" name="link_category[]" id="in-link-category-', $cat_id, '"', $checked, '/> ', $name, '</label></li>';
	}
}

/**
 * Adds hidden fields with the data for use in the inline editor for posts and pages.
 *
 * @since 2.7.0
 *
 * @param WP_Post $post Post object.
 */
function get_inline_data( $post ) {
	$post_type_object = get_post_type_object( $post->post_type );
	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return;
	}

	$title = esc_textarea( trim( $post->post_title ) );

	echo '
<div class="hidden" id="inline_' . $post->ID . '">
	<div class="post_title">' . $title . '</div>' .
	/** This filter is documented in wp-admin/edit-tag-form.php */
	'<div class="post_name">' . apply_filters( 'editable_slug', $post->post_name, $post ) . '</div>
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

	if ( $post_type_object->hierarchical ) {
		echo '<div class="post_parent">' . $post->post_parent . '</div>';
	}

	echo '<div class="page_template">' . ( $post->page_template ? esc_html( $post->page_template ) : 'default' ) . '</div>';

	if ( post_type_supports( $post->post_type, 'page-attributes' ) ) {
		echo '<div class="menu_order">' . $post->menu_order . '</div>';
	}

	$taxonomy_names = get_object_taxonomies( $post->post_type );

	foreach ( $taxonomy_names as $taxonomy_name ) {
		$taxonomy = get_taxonomy( $taxonomy_name );

		if ( ! $taxonomy->show_in_quick_edit ) {
			continue;
		}

		if ( $taxonomy->hierarchical ) {

			$terms = get_object_term_cache( $post->ID, $taxonomy_name );
			if ( false === $terms ) {
				$terms = wp_get_object_terms( $post->ID, $taxonomy_name );
				wp_cache_add( $post->ID, wp_list_pluck( $terms, 'term_id' ), $taxonomy_name . '_relationships' );
			}
			$term_ids = empty( $terms ) ? array() : wp_list_pluck( $terms, 'term_id' );

			echo '<div class="post_category" id="' . $taxonomy_name . '_' . $post->ID . '">' . implode( ',', $term_ids ) . '</div>';

		} else {

			$terms_to_edit = get_terms_to_edit( $post->ID, $taxonomy_name );
			if ( ! is_string( $terms_to_edit ) ) {
				$terms_to_edit = '';
			}

			echo '<div class="tags_input" id="' . $taxonomy_name . '_' . $post->ID . '">'
				. esc_html( str_replace( ',', ', ', $terms_to_edit ) ) . '</div>';

		}
	}

	if ( ! $post_type_object->hierarchical ) {
		echo '<div class="sticky">' . ( is_sticky( $post->ID ) ? 'sticky' : '' ) . '</div>';
	}

	if ( post_type_supports( $post->post_type, 'post-formats' ) ) {
		echo '<div class="post_format">' . esc_html( get_post_format( $post->ID ) ) . '</div>';
	}

	/**
	 * Fires after outputting the fields for the inline editor for posts and pages.
	 *
	 * @since 4.9.8
	 *
	 * @param WP_Post      $post             The current post object.
	 * @param WP_Post_Type $post_type_object The current post's post type object.
	 */
	do_action( 'add_inline_data', $post, $post_type_object );

	echo '</div>';
}

/**
 * Outputs the in-line comment reply-to form in the Comments list table.
 *
 * @since 2.7.0
 *
 * @global WP_List_Table $wp_list_table
 *
 * @param int    $position  Optional. The value of the 'position' input field. Default 1.
 * @param bool   $checkbox  Optional. The value of the 'checkbox' input field. Default false.
 * @param string $mode      Optional. If set to 'single', will use WP_Post_Comments_List_Table,
 *                          otherwise WP_Comments_List_Table. Default 'single'.
 * @param bool   $table_row Optional. Whether to use a table instead of a div element. Default true.
 */
function wp_comment_reply( $position = 1, $checkbox = false, $mode = 'single', $table_row = true ) {
	global $wp_list_table;
	/**
	 * Filters the in-line comment reply-to form output in the Comments
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
	$content = apply_filters(
		'wp_comment_reply',
		'',
		array(
			'position' => $position,
			'checkbox' => $checkbox,
			'mode'     => $mode,
		)
	);

	if ( ! empty( $content ) ) {
		echo $content;
		return;
	}

	if ( ! $wp_list_table ) {
		if ( 'single' === $mode ) {
			$wp_list_table = _get_list_table( 'WP_Post_Comments_List_Table' );
		} else {
			$wp_list_table = _get_list_table( 'WP_Comments_List_Table' );
		}
	}

	?>
<form method="get">
	<?php if ( $table_row ) : ?>
<table style="display:none;"><tbody id="com-reply"><tr id="replyrow" class="inline-edit-row" style="display:none;"><td colspan="<?php echo $wp_list_table->get_column_count(); ?>" class="colspanchange">
<?php else : ?>
<div id="com-reply" style="display:none;"><div id="replyrow" style="display:none;">
<?php endif; ?>
	<fieldset class="comment-reply">
	<legend>
		<span class="hidden" id="editlegend"><?php _e( 'Edit Comment' ); ?></span>
		<span class="hidden" id="replyhead"><?php _e( 'Reply to Comment' ); ?></span>
		<span class="hidden" id="addhead"><?php _e( 'Add New Comment' ); ?></span>
	</legend>

	<div id="replycontainer">
	<label for="replycontent" class="screen-reader-text">
		<?php
		/* translators: Hidden accessibility text. */
		_e( 'Comment' );
		?>
	</label>
	<?php
	$quicktags_settings = array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' );
	wp_editor(
		'',
		'replycontent',
		array(
			'media_buttons' => false,
			'tinymce'       => false,
			'quicktags'     => $quicktags_settings,
		)
	);
	?>
	</div>

	<div id="edithead" style="display:none;">
		<div class="inside">
		<label for="author-name"><?php _e( 'Name' ); ?></label>
		<input type="text" name="newcomment_author" size="50" value="" id="author-name" />
		</div>

		<div class="inside">
		<label for="author-email"><?php _e( 'Email' ); ?></label>
		<input type="text" name="newcomment_author_email" size="50" value="" id="author-email" />
		</div>

		<div class="inside">
		<label for="author-url"><?php _e( 'URL' ); ?></label>
		<input type="text" id="author-url" name="newcomment_author_url" class="code" size="103" value="" />
		</div>
	</div>

	<div id="replysubmit" class="submit">
		<p class="reply-submit-buttons">
			<button type="button" class="save button button-primary">
				<span id="addbtn" style="display: none;"><?php _e( 'Add Comment' ); ?></span>
				<span id="savebtn" style="display: none;"><?php _e( 'Update Comment' ); ?></span>
				<span id="replybtn" style="display: none;"><?php _e( 'Submit Reply' ); ?></span>
			</button>
			<button type="button" class="cancel button"><?php _e( 'Cancel' ); ?></button>
			<span class="waiting spinner"></span>
		</p>
		<?php
		wp_admin_notice(
			'<p class="error"></p>',
			array(
				'type'               => 'error',
				'additional_classes' => array( 'notice-alt', 'inline', 'hidden' ),
				'paragraph_wrap'     => false,
			)
		);
		?>
	</div>

	<input type="hidden" name="action" id="action" value="" />
	<input type="hidden" name="comment_ID" id="comment_ID" value="" />
	<input type="hidden" name="comment_post_ID" id="comment_post_ID" value="" />
	<input type="hidden" name="status" id="status" value="" />
	<input type="hidden" name="position" id="position" value="<?php echo $position; ?>" />
	<input type="hidden" name="checkbox" id="checkbox" value="<?php echo $checkbox ? 1 : 0; ?>" />
	<input type="hidden" name="mode" id="mode" value="<?php echo esc_attr( $mode ); ?>" />
	<?php
		wp_nonce_field( 'replyto-comment', '_ajax_nonce-replyto-comment', false );
	if ( current_user_can( 'unfiltered_html' ) ) {
		wp_nonce_field( 'unfiltered-html-comment', '_wp_unfiltered_html_comment', false );
	}
	?>
	</fieldset>
	<?php if ( $table_row ) : ?>
</td></tr></tbody></table>
	<?php else : ?>
</div></div>
	<?php endif; ?>
</form>
	<?php
}

/**
 * Outputs 'undo move to Trash' text for comments.
 *
 * @since 2.9.0
 */
function wp_comment_trashnotice() {
	?>
<div class="hidden" id="trash-undo-holder">
	<div class="trash-undo-inside">
		<?php
		/* translators: %s: Comment author, filled by Ajax. */
		printf( __( 'Comment by %s moved to the Trash.' ), '<strong></strong>' );
		?>
		<span class="undo untrash"><a href="#"><?php _e( 'Undo' ); ?></a></span>
	</div>
</div>
<div class="hidden" id="spam-undo-holder">
	<div class="spam-undo-inside">
		<?php
		/* translators: %s: Comment author, filled by Ajax. */
		printf( __( 'Comment by %s marked as spam.' ), '<strong></strong>' );
		?>
		<span class="undo unspam"><a href="#"><?php _e( 'Undo' ); ?></a></span>
	</div>
</div>
	<?php
}

/**
 * Outputs a post's public meta data in the Custom Fields meta box.
 *
 * @since 1.2.0
 *
 * @param array[] $meta An array of meta data arrays keyed on 'meta_key' and 'meta_value'.
 */
function list_meta( $meta ) {
	// Exit if no meta.
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
</table>'; // TBODY needed for list-manipulation JS.
		return;
	}
	$count = 0;
	?>
<table id="list-table">
	<thead>
	<tr>
		<th class="left"><?php _ex( 'Name', 'meta name' ); ?></th>
		<th><?php _e( 'Value' ); ?></th>
	</tr>
	</thead>
	<tbody id='the-list' data-wp-lists='list:meta'>
	<?php
	foreach ( $meta as $entry ) {
		echo _list_meta_row( $entry, $count );
	}
	?>
	</tbody>
</table>
	<?php
}

/**
 * Outputs a single row of public meta data in the Custom Fields meta box.
 *
 * @since 2.5.0
 *
 * @param array $entry An array of meta data keyed on 'meta_key' and 'meta_value'.
 * @param int   $count Reference to the row number.
 * @return string A single row of public meta data.
 */
function _list_meta_row( $entry, &$count ) {
	static $update_nonce = '';

	if ( is_protected_meta( $entry['meta_key'], 'post' ) ) {
		return '';
	}

	if ( ! $update_nonce ) {
		$update_nonce = wp_create_nonce( 'add-meta' );
	}

	$r = '';
	++$count;

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

	$entry['meta_key']   = esc_attr( $entry['meta_key'] );
	$entry['meta_value'] = esc_textarea( $entry['meta_value'] ); // Using a <textarea />.
	$entry['meta_id']    = (int) $entry['meta_id'];

	$delete_nonce = wp_create_nonce( 'delete-meta_' . $entry['meta_id'] );

	$r .= "\n\t<tr id='meta-{$entry['meta_id']}'>";
	$r .= "\n\t\t<td class='left'><label class='screen-reader-text' for='meta-{$entry['meta_id']}-key'>" .
		/* translators: Hidden accessibility text. */
		__( 'Key' ) .
	"</label><input name='meta[{$entry['meta_id']}][key]' id='meta-{$entry['meta_id']}-key' type='text' size='20' value='{$entry['meta_key']}' />";

	$r .= "\n\t\t<div class='submit'>";
	$r .= get_submit_button( __( 'Delete' ), 'deletemeta small', "deletemeta[{$entry['meta_id']}]", false, array( 'data-wp-lists' => "delete:the-list:meta-{$entry['meta_id']}::_ajax_nonce=$delete_nonce" ) );
	$r .= "\n\t\t";
	$r .= get_submit_button( __( 'Update' ), 'updatemeta small', "meta-{$entry['meta_id']}-submit", false, array( 'data-wp-lists' => "add:the-list:meta-{$entry['meta_id']}::_ajax_nonce-add-meta=$update_nonce" ) );
	$r .= '</div>';
	$r .= wp_nonce_field( 'change-meta', '_ajax_nonce', false, false );
	$r .= '</td>';

	$r .= "\n\t\t<td><label class='screen-reader-text' for='meta-{$entry['meta_id']}-value'>" .
		/* translators: Hidden accessibility text. */
		__( 'Value' ) .
	"</label><textarea name='meta[{$entry['meta_id']}][value]' id='meta-{$entry['meta_id']}-value' rows='2' cols='30'>{$entry['meta_value']}</textarea></td>\n\t</tr>";
	return $r;
}

/**
 * Prints the form in the Custom Fields meta box.
 *
 * @since 1.2.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param WP_Post $post Optional. The post being edited.
 */
function meta_form( $post = null ) {
	global $wpdb;
	$post = get_post( $post );

	/**
	 * Filters values for the meta key dropdown in the Custom Fields meta box.
	 *
	 * Returning a non-null value will effectively short-circuit and avoid a
	 * potentially expensive query against postmeta.
	 *
	 * @since 4.4.0
	 *
	 * @param array|null $keys Pre-defined meta keys to be used in place of a postmeta query. Default null.
	 * @param WP_Post    $post The current post object.
	 */
	$keys = apply_filters( 'postmeta_form_keys', null, $post );

	if ( null === $keys ) {
		/**
		 * Filters the number of custom fields to retrieve for the drop-down
		 * in the Custom Fields meta box.
		 *
		 * @since 2.1.0
		 *
		 * @param int $limit Number of custom fields to retrieve. Default 30.
		 */
		$limit = apply_filters( 'postmeta_form_limit', 30 );

		$keys = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT meta_key
				FROM $wpdb->postmeta
				WHERE meta_key NOT BETWEEN '_' AND '_z'
				HAVING meta_key NOT LIKE %s
				ORDER BY meta_key
				LIMIT %d",
				$wpdb->esc_like( '_' ) . '%',
				$limit
			)
		);
	}

	if ( $keys ) {
		natcasesort( $keys );
	}
	?>
<p><strong><?php _e( 'Add New Custom Field:' ); ?></strong></p>
<table id="newmeta">
<thead>
<tr>
<th class="left"><label for="metakeyselect"><?php _ex( 'Name', 'meta name' ); ?></label></th>
<th><label for="metavalue"><?php _e( 'Value' ); ?></label></th>
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
			if ( is_protected_meta( $key, 'post' ) || ! current_user_can( 'add_post_meta', $post->ID, $key ) ) {
				continue;
			}
			echo "\n<option value='" . esc_attr( $key ) . "'>" . esc_html( $key ) . '</option>';
		}
		?>
</select>
<input class="hidden" type="text" id="metakeyinput" name="metakeyinput" value="" aria-label="<?php _e( 'New custom field name' ); ?>" />
<button type="button" id="newmeta-button" class="button button-small hide-if-no-js" onclick="jQuery('#metakeyinput, #metakeyselect, #enternew, #cancelnew').toggleClass('hidden');jQuery('#metakeyinput, #metakeyselect').filter(':visible').trigger('focus');">
<span id="enternew"><?php _e( 'Enter new' ); ?></span>
<span id="cancelnew" class="hidden"><?php _e( 'Cancel' ); ?></span></button>
<?php } else { ?>
<input type="text" id="metakeyinput" name="metakeyinput" value="" />
<?php } ?>
</td>
<td><textarea id="metavalue" name="metavalue" rows="2" cols="25"></textarea>
	<?php wp_nonce_field( 'add-meta', '_ajax_nonce-add-meta', false ); ?>
</td>
</tr>
</tbody>
</table>
<div class="submit add-custom-field">
	<?php
	submit_button(
		__( 'Add Custom Field' ),
		'',
		'addmeta',
		false,
		array(
			'id'            => 'newmeta-submit',
			'data-wp-lists' => 'add:the-list:newmeta',
		)
	);
	?>
</div>
	<?php
}

/**
 * Prints out HTML form date elements for editing post or comment publish date.
 *
 * @since 0.71
 * @since 4.4.0 Converted to use get_comment() instead of the global `$comment`.
 *
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 *
 * @param int|bool $edit      Accepts 1|true for editing the date, 0|false for adding the date.
 * @param int|bool $for_post  Accepts 1|true for applying the date to a post, 0|false for a comment.
 * @param int      $tab_index The tabindex attribute to add. Default 0.
 * @param int|bool $multi     Optional. Whether the additional fields and buttons should be added.
 *                            Default 0|false.
 */
function touch_time( $edit = 1, $for_post = 1, $tab_index = 0, $multi = 0 ) {
	global $wp_locale;
	$post = get_post();

	if ( $for_post ) {
		$edit = ! ( in_array( $post->post_status, array( 'draft', 'pending' ), true ) && ( ! $post->post_date_gmt || '0000-00-00 00:00:00' === $post->post_date_gmt ) );
	}

	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 ) {
		$tab_index_attribute = " tabindex=\"$tab_index\"";
	}

	// @todo Remove this?
	// echo '<label for="timestamp" style="display: block;"><input type="checkbox" class="checkbox" name="edit_date" value="1" id="timestamp"'.$tab_index_attribute.' /> '.__( 'Edit timestamp' ).'</label><br />';

	$post_date = ( $for_post ) ? $post->post_date : get_comment()->comment_date;
	$jj        = ( $edit ) ? mysql2date( 'd', $post_date, false ) : current_time( 'd' );
	$mm        = ( $edit ) ? mysql2date( 'm', $post_date, false ) : current_time( 'm' );
	$aa        = ( $edit ) ? mysql2date( 'Y', $post_date, false ) : current_time( 'Y' );
	$hh        = ( $edit ) ? mysql2date( 'H', $post_date, false ) : current_time( 'H' );
	$mn        = ( $edit ) ? mysql2date( 'i', $post_date, false ) : current_time( 'i' );
	$ss        = ( $edit ) ? mysql2date( 's', $post_date, false ) : current_time( 's' );

	$cur_jj = current_time( 'd' );
	$cur_mm = current_time( 'm' );
	$cur_aa = current_time( 'Y' );
	$cur_hh = current_time( 'H' );
	$cur_mn = current_time( 'i' );

	$month = '<label><span class="screen-reader-text">' .
		/* translators: Hidden accessibility text. */
		__( 'Month' ) .
	'</span><select class="form-required" ' . ( $multi ? '' : 'id="mm" ' ) . 'name="mm"' . $tab_index_attribute . ">\n";
	for ( $i = 1; $i < 13; $i = $i + 1 ) {
		$monthnum  = zeroise( $i, 2 );
		$monthtext = $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) );
		$month    .= "\t\t\t" . '<option value="' . $monthnum . '" data-text="' . $monthtext . '" ' . selected( $monthnum, $mm, false ) . '>';
		/* translators: 1: Month number (01, 02, etc.), 2: Month abbreviation. */
		$month .= sprintf( __( '%1$s-%2$s' ), $monthnum, $monthtext ) . "</option>\n";
	}
	$month .= '</select></label>';

	$day = '<label><span class="screen-reader-text">' .
		/* translators: Hidden accessibility text. */
		__( 'Day' ) .
	'</span><input type="text" ' . ( $multi ? '' : 'id="jj" ' ) . 'name="jj" value="' . $jj . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" class="form-required" /></label>';
	$year = '<label><span class="screen-reader-text">' .
		/* translators: Hidden accessibility text. */
		__( 'Year' ) .
	'</span><input type="text" ' . ( $multi ? '' : 'id="aa" ' ) . 'name="aa" value="' . $aa . '" size="4" maxlength="4"' . $tab_index_attribute . ' autocomplete="off" class="form-required" /></label>';
	$hour = '<label><span class="screen-reader-text">' .
		/* translators: Hidden accessibility text. */
		__( 'Hour' ) .
	'</span><input type="text" ' . ( $multi ? '' : 'id="hh" ' ) . 'name="hh" value="' . $hh . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" class="form-required" /></label>';
	$minute = '<label><span class="screen-reader-text">' .
		/* translators: Hidden accessibility text. */
		__( 'Minute' ) .
	'</span><input type="text" ' . ( $multi ? '' : 'id="mn" ' ) . 'name="mn" value="' . $mn . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" class="form-required" /></label>';

	echo '<div class="timestamp-wrap">';
	/* translators: 1: Month, 2: Day, 3: Year, 4: Hour, 5: Minute. */
	printf( __( '%1$s %2$s, %3$s at %4$s:%5$s' ), $month, $day, $year, $hour, $minute );

	echo '</div><input type="hidden" id="ss" name="ss" value="' . $ss . '" />';

	if ( $multi ) {
		return;
	}

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
<a href="#edit_timestamp" class="save-timestamp hide-if-no-js button"><?php _e( 'OK' ); ?></a>
<a href="#edit_timestamp" class="cancel-timestamp hide-if-no-js button-cancel"><?php _e( 'Cancel' ); ?></a>
</p>
	<?php
}

/**
 * Prints out option HTML elements for the page templates drop-down.
 *
 * @since 1.5.0
 * @since 4.7.0 Added the `$post_type` parameter.
 *
 * @param string $default_template Optional. The template file name. Default empty.
 * @param string $post_type        Optional. Post type to get templates for. Default 'page'.
 */
function page_template_dropdown( $default_template = '', $post_type = 'page' ) {
	$templates = get_page_templates( null, $post_type );

	ksort( $templates );

	foreach ( array_keys( $templates ) as $template ) {
		$selected = selected( $default_template, $templates[ $template ], false );
		echo "\n\t<option value='" . esc_attr( $templates[ $template ] ) . "' $selected>" . esc_html( $template ) . '</option>';
	}
}

/**
 * Prints out option HTML elements for the page parents drop-down.
 *
 * @since 1.5.0
 * @since 4.4.0 `$post` argument was added.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int         $default_page Optional. The default page ID to be pre-selected. Default 0.
 * @param int         $parent_page  Optional. The parent page ID. Default 0.
 * @param int         $level        Optional. Page depth level. Default 0.
 * @param int|WP_Post $post         Post ID or WP_Post object.
 * @return void|false Void on success, false if the page has no children.
 */
function parent_dropdown( $default_page = 0, $parent_page = 0, $level = 0, $post = null ) {
	global $wpdb;

	$post  = get_post( $post );
	$items = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_parent, post_title
			FROM $wpdb->posts
			WHERE post_parent = %d AND post_type = 'page'
			ORDER BY menu_order",
			$parent_page
		)
	);

	if ( $items ) {
		foreach ( $items as $item ) {
			// A page cannot be its own parent.
			if ( $post && $post->ID && (int) $item->ID === $post->ID ) {
				continue;
			}

			$pad      = str_repeat( '&nbsp;', $level * 3 );
			$selected = selected( $default_page, $item->ID, false );

			echo "\n\t<option class='level-$level' value='$item->ID' $selected>$pad " . esc_html( $item->post_title ) . '</option>';
			parent_dropdown( $default_page, $item->ID, $level + 1 );
		}
	} else {
		return false;
	}
}

/**
 * Prints out option HTML elements for role selectors.
 *
 * @since 2.1.0
 *
 * @param string $selected Slug for the role that should be already selected.
 */
function wp_dropdown_roles( $selected = '' ) {
	$r = '';

	$editable_roles = array_reverse( get_editable_roles() );

	foreach ( $editable_roles as $role => $details ) {
		$name = translate_user_role( $details['name'] );
		// Preselect specified role.
		if ( $selected === $role ) {
			$r .= "\n\t<option selected='selected' value='" . esc_attr( $role ) . "'>$name</option>";
		} else {
			$r .= "\n\t<option value='" . esc_attr( $role ) . "'>$name</option>";
		}
	}

	echo $r;
}

/**
 * Outputs the form used by the importers to accept the data to be imported.
 *
 * @since 2.0.0
 *
 * @param string $action The action attribute for the form.
 */
function wp_import_upload_form( $action ) {

	/**
	 * Filters the maximum allowed upload size for import files.
	 *
	 * @since 2.3.0
	 *
	 * @see wp_max_upload_size()
	 *
	 * @param int $max_upload_size Allowed upload size. Default 1 MB.
	 */
	$bytes      = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
	$size       = size_format( $bytes );
	$upload_dir = wp_upload_dir();
	if ( ! empty( $upload_dir['error'] ) ) :
		$upload_directory_error  = '<p>' . __( 'Before you can upload your import file, you will need to fix the following error:' ) . '</p>';
		$upload_directory_error .= '<p><strong>' . $upload_dir['error'] . '</strong></p>';
		wp_admin_notice(
			$upload_directory_error,
			array(
				'additional_classes' => array( 'error' ),
				'paragraph_wrap'     => false,
			)
		);
	else :
		?>
<form enctype="multipart/form-data" id="import-upload-form" method="post" class="wp-upload-form" action="<?php echo esc_url( wp_nonce_url( $action, 'import-upload' ) ); ?>">
<p>
		<?php
		printf(
			'<label for="upload">%s</label> (%s)',
			__( 'Choose a file from your computer:' ),
			/* translators: %s: Maximum allowed file size. */
			sprintf( __( 'Maximum size: %s' ), $size )
		);
		?>
<input type="file" id="upload" name="import" size="25" />
<input type="hidden" name="action" value="save" />
<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
</p>
		<?php submit_button( __( 'Upload file and import' ), 'primary' ); ?>
</form>
		<?php
	endif;
}

/**
 * Adds a meta box to one or more screens.
 *
 * @since 2.5.0
 * @since 4.4.0 The `$screen` parameter now accepts an array of screen IDs.
 *
 * @global array $wp_meta_boxes
 *
 * @param string                 $id            Meta box ID (used in the 'id' attribute for the meta box).
 * @param string                 $title         Title of the meta box.
 * @param callable               $callback      Function that fills the box with the desired content.
 *                                              The function should echo its output.
 * @param string|array|WP_Screen $screen        Optional. The screen or screens on which to show the box
 *                                              (such as a post type, 'link', or 'comment'). Accepts a single
 *                                              screen ID, WP_Screen object, or array of screen IDs. Default
 *                                              is the current screen.  If you have used add_menu_page() or
 *                                              add_submenu_page() to create a new screen (and hence screen_id),
 *                                              make sure your menu slug conforms to the limits of sanitize_key()
 *                                              otherwise the 'screen' menu may not correctly render on your page.
 * @param string                 $context       Optional. The context within the screen where the box
 *                                              should display. Available contexts vary from screen to
 *                                              screen. Post edit screen contexts include 'normal', 'side',
 *                                              and 'advanced'. Comments screen contexts include 'normal'
 *                                              and 'side'. Menus meta boxes (accordion sections) all use
 *                                              the 'side' context. Global default is 'advanced'.
 * @param string                 $priority      Optional. The priority within the context where the box should show.
 *                                              Accepts 'high', 'core', 'default', or 'low'. Default 'default'.
 * @param array                  $callback_args Optional. Data that should be set as the $args property
 *                                              of the box array (which is the second parameter passed
 *                                              to your callback). Default null.
 */
function add_meta_box( $id, $title, $callback, $screen = null, $context = 'advanced', $priority = 'default', $callback_args = null ) {
	global $wp_meta_boxes;

	if ( empty( $screen ) ) {
		$screen = get_current_screen();
	} elseif ( is_string( $screen ) ) {
		$screen = convert_to_screen( $screen );
	} elseif ( is_array( $screen ) ) {
		foreach ( $screen as $single_screen ) {
			add_meta_box( $id, $title, $callback, $single_screen, $context, $priority, $callback_args );
		}
	}

	if ( ! isset( $screen->id ) ) {
		return;
	}

	$page = $screen->id;

	if ( ! isset( $wp_meta_boxes ) ) {
		$wp_meta_boxes = array();
	}
	if ( ! isset( $wp_meta_boxes[ $page ] ) ) {
		$wp_meta_boxes[ $page ] = array();
	}
	if ( ! isset( $wp_meta_boxes[ $page ][ $context ] ) ) {
		$wp_meta_boxes[ $page ][ $context ] = array();
	}

	foreach ( array_keys( $wp_meta_boxes[ $page ] ) as $a_context ) {
		foreach ( array( 'high', 'core', 'default', 'low' ) as $a_priority ) {
			if ( ! isset( $wp_meta_boxes[ $page ][ $a_context ][ $a_priority ][ $id ] ) ) {
				continue;
			}

			// If a core box was previously removed, don't add.
			if ( ( 'core' === $priority || 'sorted' === $priority )
				&& false === $wp_meta_boxes[ $page ][ $a_context ][ $a_priority ][ $id ]
			) {
				return;
			}

			// If a core box was previously added by a plugin, don't add.
			if ( 'core' === $priority ) {
				/*
				 * If the box was added with default priority, give it core priority
				 * to maintain sort order.
				 */
				if ( 'default' === $a_priority ) {
					$wp_meta_boxes[ $page ][ $a_context ]['core'][ $id ] = $wp_meta_boxes[ $page ][ $a_context ]['default'][ $id ];
					unset( $wp_meta_boxes[ $page ][ $a_context ]['default'][ $id ] );
				}
				return;
			}

			// If no priority given and ID already present, use existing priority.
			if ( empty( $priority ) ) {
				$priority = $a_priority;
				/*
				 * Else, if we're adding to the sorted priority, we don't know the title
				 * or callback. Grab them from the previously added context/priority.
				 */
			} elseif ( 'sorted' === $priority ) {
				$title         = $wp_meta_boxes[ $page ][ $a_context ][ $a_priority ][ $id ]['title'];
				$callback      = $wp_meta_boxes[ $page ][ $a_context ][ $a_priority ][ $id ]['callback'];
				$callback_args = $wp_meta_boxes[ $page ][ $a_context ][ $a_priority ][ $id ]['args'];
			}

			// An ID can be in only one priority and one context.
			if ( $priority !== $a_priority || $context !== $a_context ) {
				unset( $wp_meta_boxes[ $page ][ $a_context ][ $a_priority ][ $id ] );
			}
		}
	}

	if ( empty( $priority ) ) {
		$priority = 'low';
	}

	if ( ! isset( $wp_meta_boxes[ $page ][ $context ][ $priority ] ) ) {
		$wp_meta_boxes[ $page ][ $context ][ $priority ] = array();
	}

	$wp_meta_boxes[ $page ][ $context ][ $priority ][ $id ] = array(
		'id'       => $id,
		'title'    => $title,
		'callback' => $callback,
		'args'     => $callback_args,
	);
}


/**
 * Renders a "fake" meta box with an information message,
 * shown on the block editor, when an incompatible meta box is found.
 *
 * @since 5.0.0
 *
 * @param mixed $data_object The data object being rendered on this screen.
 * @param array $box         {
 *     Custom formats meta box arguments.
 *
 *     @type string   $id           Meta box 'id' attribute.
 *     @type string   $title        Meta box title.
 *     @type callable $old_callback The original callback for this meta box.
 *     @type array    $args         Extra meta box arguments.
 * }
 */
function do_block_editor_incompatible_meta_box( $data_object, $box ) {
	$plugin  = _get_plugin_from_callback( $box['old_callback'] );
	$plugins = get_plugins();
	echo '<p>';
	if ( $plugin ) {
		/* translators: %s: The name of the plugin that generated this meta box. */
		printf( __( 'This meta box, from the %s plugin, is not compatible with the block editor.' ), "<strong>{$plugin['Name']}</strong>" );
	} else {
		_e( 'This meta box is not compatible with the block editor.' );
	}
	echo '</p>';

	if ( empty( $plugins['classic-editor/classic-editor.php'] ) ) {
		if ( current_user_can( 'install_plugins' ) ) {
			$install_url = wp_nonce_url(
				self_admin_url( 'plugin-install.php?tab=favorites&user=wordpressdotorg&save=0' ),
				'save_wporg_username_' . get_current_user_id()
			);

			echo '<p>';
			/* translators: %s: A link to install the Classic Editor plugin. */
			printf( __( 'Please install the <a href="%s">Classic Editor plugin</a> to use this meta box.' ), esc_url( $install_url ) );
			echo '</p>';
		}
	} elseif ( is_plugin_inactive( 'classic-editor/classic-editor.php' ) ) {
		if ( current_user_can( 'activate_plugins' ) ) {
			$activate_url = wp_nonce_url(
				self_admin_url( 'plugins.php?action=activate&plugin=classic-editor/classic-editor.php' ),
				'activate-plugin_classic-editor/classic-editor.php'
			);

			echo '<p>';
			/* translators: %s: A link to activate the Classic Editor plugin. */
			printf( __( 'Please activate the <a href="%s">Classic Editor plugin</a> to use this meta box.' ), esc_url( $activate_url ) );
			echo '</p>';
		}
	} elseif ( $data_object instanceof WP_Post ) {
		$edit_url = add_query_arg(
			array(
				'classic-editor'         => '',
				'classic-editor__forget' => '',
			),
			get_edit_post_link( $data_object )
		);
		echo '<p>';
		/* translators: %s: A link to use the Classic Editor plugin. */
		printf( __( 'Please open the <a href="%s">classic editor</a> to use this meta box.' ), esc_url( $edit_url ) );
		echo '</p>';
	}
}

/**
 * Internal helper function to find the plugin from a meta box callback.
 *
 * @since 5.0.0
 *
 * @access private
 *
 * @param callable $callback The callback function to check.
 * @return array|null The plugin that the callback belongs to, or null if it doesn't belong to a plugin.
 */
function _get_plugin_from_callback( $callback ) {
	try {
		if ( is_array( $callback ) ) {
			$reflection = new ReflectionMethod( $callback[0], $callback[1] );
		} elseif ( is_string( $callback ) && str_contains( $callback, '::' ) ) {
			$reflection = new ReflectionMethod( $callback );
		} else {
			$reflection = new ReflectionFunction( $callback );
		}
	} catch ( ReflectionException $exception ) {
		// We could not properly reflect on the callable, so we abort here.
		return null;
	}

	// Don't show an error if it's an internal PHP function.
	if ( ! $reflection->isInternal() ) {

		// Only show errors if the meta box was registered by a plugin.
		$filename   = wp_normalize_path( $reflection->getFileName() );
		$plugin_dir = wp_normalize_path( WP_PLUGIN_DIR );

		if ( str_starts_with( $filename, $plugin_dir ) ) {
			$filename = str_replace( $plugin_dir, '', $filename );
			$filename = preg_replace( '|^/([^/]*/).*$|', '\\1', $filename );

			$plugins = get_plugins();

			foreach ( $plugins as $name => $plugin ) {
				if ( str_starts_with( $name, $filename ) ) {
					return $plugin;
				}
			}
		}
	}

	return null;
}

/**
 * Meta-Box template function.
 *
 * @since 2.5.0
 *
 * @global array $wp_meta_boxes
 *
 * @param string|WP_Screen $screen      The screen identifier. If you have used add_menu_page() or
 *                                      add_submenu_page() to create a new screen (and hence screen_id)
 *                                      make sure your menu slug conforms to the limits of sanitize_key()
 *                                      otherwise the 'screen' menu may not correctly render on your page.
 * @param string           $context     The screen context for which to display meta boxes.
 * @param mixed            $data_object Gets passed to the meta box callback function as the first parameter.
 *                                      Often this is the object that's the focus of the current screen,
 *                                      for example a `WP_Post` or `WP_Comment` object.
 * @return int Number of meta_boxes.
 */
function do_meta_boxes( $screen, $context, $data_object ) {
	global $wp_meta_boxes;
	static $already_sorted = false;

	if ( empty( $screen ) ) {
		$screen = get_current_screen();
	} elseif ( is_string( $screen ) ) {
		$screen = convert_to_screen( $screen );
	}

	$page = $screen->id;

	$hidden = get_hidden_meta_boxes( $screen );

	printf( '<div id="%s-sortables" class="meta-box-sortables">', esc_attr( $context ) );

	/*
	 * Grab the ones the user has manually sorted.
	 * Pull them out of their previous context/priority and into the one the user chose.
	 */
	$sorted = get_user_option( "meta-box-order_$page" );

	if ( ! $already_sorted && $sorted ) {
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
			if ( isset( $wp_meta_boxes[ $page ][ $context ][ $priority ] ) ) {
				foreach ( (array) $wp_meta_boxes[ $page ][ $context ][ $priority ] as $box ) {
					if ( false === $box || ! $box['title'] ) {
						continue;
					}

					$block_compatible = true;
					if ( is_array( $box['args'] ) ) {
						// If a meta box is just here for back compat, don't show it in the block editor.
						if ( $screen->is_block_editor() && isset( $box['args']['__back_compat_meta_box'] ) && $box['args']['__back_compat_meta_box'] ) {
							continue;
						}

						if ( isset( $box['args']['__block_editor_compatible_meta_box'] ) ) {
							$block_compatible = (bool) $box['args']['__block_editor_compatible_meta_box'];
							unset( $box['args']['__block_editor_compatible_meta_box'] );
						}

						// If the meta box is declared as incompatible with the block editor, override the callback function.
						if ( ! $block_compatible && $screen->is_block_editor() ) {
							$box['old_callback'] = $box['callback'];
							$box['callback']     = 'do_block_editor_incompatible_meta_box';
						}

						if ( isset( $box['args']['__back_compat_meta_box'] ) ) {
							$block_compatible = $block_compatible || (bool) $box['args']['__back_compat_meta_box'];
							unset( $box['args']['__back_compat_meta_box'] );
						}
					}

					++$i;
					// get_hidden_meta_boxes() doesn't apply in the block editor.
					$hidden_class = ( ! $screen->is_block_editor() && in_array( $box['id'], $hidden, true ) ) ? ' hide-if-js' : '';
					echo '<div id="' . $box['id'] . '" class="postbox ' . postbox_classes( $box['id'], $page ) . $hidden_class . '" ' . '>' . "\n";

					echo '<div class="postbox-header">';
					echo '<h2 class="hndle">';
					if ( 'dashboard_php_nag' === $box['id'] ) {
						echo '<span aria-hidden="true" class="dashicons dashicons-warning"></span>';
						echo '<span class="screen-reader-text">' .
							/* translators: Hidden accessibility text. */
							__( 'Warning:' ) .
						' </span>';
					}
					echo $box['title'];
					echo "</h2>\n";

					if ( 'dashboard_browser_nag' !== $box['id'] ) {
						$widget_title = $box['title'];

						if ( is_array( $box['args'] ) && isset( $box['args']['__widget_basename'] ) ) {
							$widget_title = $box['args']['__widget_basename'];
							// Do not pass this parameter to the user callback function.
							unset( $box['args']['__widget_basename'] );
						}

						echo '<div class="handle-actions hide-if-no-js">';

						echo '<button type="button" class="handle-order-higher" aria-disabled="false" aria-describedby="' . $box['id'] . '-handle-order-higher-description">';
						echo '<span class="screen-reader-text">' .
							/* translators: Hidden accessibility text. */
							__( 'Move up' ) .
						'</span>';
						echo '<span class="order-higher-indicator" aria-hidden="true"></span>';
						echo '</button>';
						echo '<span class="hidden" id="' . $box['id'] . '-handle-order-higher-description">' . sprintf(
							/* translators: %s: Meta box title. */
							__( 'Move %s box up' ),
							$widget_title
						) . '</span>';

						echo '<button type="button" class="handle-order-lower" aria-disabled="false" aria-describedby="' . $box['id'] . '-handle-order-lower-description">';
						echo '<span class="screen-reader-text">' .
							/* translators: Hidden accessibility text. */
							__( 'Move down' ) .
						'</span>';
						echo '<span class="order-lower-indicator" aria-hidden="true"></span>';
						echo '</button>';
						echo '<span class="hidden" id="' . $box['id'] . '-handle-order-lower-description">' . sprintf(
							/* translators: %s: Meta box title. */
							__( 'Move %s box down' ),
							$widget_title
						) . '</span>';

						echo '<button type="button" class="handlediv" aria-expanded="true">';
						echo '<span class="screen-reader-text">' . sprintf(
							/* translators: %s: Hidden accessibility text. Meta box title. */
							__( 'Toggle panel: %s' ),
							$widget_title
						) . '</span>';
						echo '<span class="toggle-indicator" aria-hidden="true"></span>';
						echo '</button>';

						echo '</div>';
					}
					echo '</div>';

					echo '<div class="inside">' . "\n";

					if ( WP_DEBUG && ! $block_compatible && 'edit' === $screen->parent_base && ! $screen->is_block_editor() && ! isset( $_GET['meta-box-loader'] ) ) {
						$plugin = _get_plugin_from_callback( $box['callback'] );
						if ( $plugin ) {
							$meta_box_not_compatible_message = sprintf(
								/* translators: %s: The name of the plugin that generated this meta box. */
								__( 'This meta box, from the %s plugin, is not compatible with the block editor.' ),
								"<strong>{$plugin['Name']}</strong>"
							);
							wp_admin_notice(
								$meta_box_not_compatible_message,
								array(
									'additional_classes' => array( 'error', 'inline' ),
								)
							);
						}
					}

					call_user_func( $box['callback'], $data_object, $box );
					echo "</div>\n";
					echo "</div>\n";
				}
			}
		}
	}

	echo '</div>';

	return $i;
}

/**
 * Removes a meta box from one or more screens.
 *
 * @since 2.6.0
 * @since 4.4.0 The `$screen` parameter now accepts an array of screen IDs.
 *
 * @global array $wp_meta_boxes
 *
 * @param string                 $id      Meta box ID (used in the 'id' attribute for the meta box).
 * @param string|array|WP_Screen $screen  The screen or screens on which the meta box is shown (such as a
 *                                        post type, 'link', or 'comment'). Accepts a single screen ID,
 *                                        WP_Screen object, or array of screen IDs.
 * @param string                 $context The context within the screen where the box is set to display.
 *                                        Contexts vary from screen to screen. Post edit screen contexts
 *                                        include 'normal', 'side', and 'advanced'. Comments screen contexts
 *                                        include 'normal' and 'side'. Menus meta boxes (accordion sections)
 *                                        all use the 'side' context.
 */
function remove_meta_box( $id, $screen, $context ) {
	global $wp_meta_boxes;

	if ( empty( $screen ) ) {
		$screen = get_current_screen();
	} elseif ( is_string( $screen ) ) {
		$screen = convert_to_screen( $screen );
	} elseif ( is_array( $screen ) ) {
		foreach ( $screen as $single_screen ) {
			remove_meta_box( $id, $single_screen, $context );
		}
	}

	if ( ! isset( $screen->id ) ) {
		return;
	}

	$page = $screen->id;

	if ( ! isset( $wp_meta_boxes ) ) {
		$wp_meta_boxes = array();
	}
	if ( ! isset( $wp_meta_boxes[ $page ] ) ) {
		$wp_meta_boxes[ $page ] = array();
	}
	if ( ! isset( $wp_meta_boxes[ $page ][ $context ] ) ) {
		$wp_meta_boxes[ $page ][ $context ] = array();
	}

	foreach ( array( 'high', 'core', 'default', 'low' ) as $priority ) {
		$wp_meta_boxes[ $page ][ $context ][ $priority ][ $id ] = false;
	}
}

/**
 * Meta Box Accordion Template Function.
 *
 * Largely made up of abstracted code from do_meta_boxes(), this
 * function serves to build meta boxes as list items for display as
 * a collapsible accordion.
 *
 * @since 3.6.0
 *
 * @uses global $wp_meta_boxes Used to retrieve registered meta boxes.
 *
 * @param string|object $screen      The screen identifier.
 * @param string        $context     The screen context for which to display accordion sections.
 * @param mixed         $data_object Gets passed to the section callback function as the first parameter.
 * @return int Number of meta boxes as accordion sections.
 */
function do_accordion_sections( $screen, $context, $data_object ) {
	global $wp_meta_boxes;

	wp_enqueue_script( 'accordion' );

	if ( empty( $screen ) ) {
		$screen = get_current_screen();
	} elseif ( is_string( $screen ) ) {
		$screen = convert_to_screen( $screen );
	}

	$page = $screen->id;

	$hidden = get_hidden_meta_boxes( $screen );
	?>
	<div id="side-sortables" class="accordion-container">
		<ul class="outer-border">
	<?php
	$i          = 0;
	$first_open = false;

	if ( isset( $wp_meta_boxes[ $page ][ $context ] ) ) {
		foreach ( array( 'high', 'core', 'default', 'low' ) as $priority ) {
			if ( isset( $wp_meta_boxes[ $page ][ $context ][ $priority ] ) ) {
				foreach ( $wp_meta_boxes[ $page ][ $context ][ $priority ] as $box ) {
					if ( false === $box || ! $box['title'] ) {
						continue;
					}

					++$i;
					$hidden_class = in_array( $box['id'], $hidden, true ) ? 'hide-if-js' : '';

					$open_class = '';
					if ( ! $first_open && empty( $hidden_class ) ) {
						$first_open = true;
						$open_class = 'open';
					}
					?>
					<li class="control-section accordion-section <?php echo $hidden_class; ?> <?php echo $open_class; ?> <?php echo esc_attr( $box['id'] ); ?>" id="<?php echo esc_attr( $box['id'] ); ?>">
						<h3 class="accordion-section-title hndle" tabindex="0">
							<?php echo esc_html( $box['title'] ); ?>
							<span class="screen-reader-text">
								<?php
								/* translators: Hidden accessibility text. */
								_e( 'Press return or enter to open this section' );
								?>
							</span>
						</h3>
						<div class="accordion-section-content <?php postbox_classes( $box['id'], $page ); ?>">
							<div class="inside">
								<?php call_user_func( $box['callback'], $data_object, $box ); ?>
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
 * Adds a new section to a settings page.
 *
 * Part of the Settings API. Use this to define new settings sections for an admin page.
 * Show settings sections in your admin page callback function with do_settings_sections().
 * Add settings fields to your section with add_settings_field().
 *
 * The $callback argument should be the name of a function that echoes out any
 * content you want to show at the top of the settings section before the actual
 * fields. It can output nothing if you want.
 *
 * @since 2.7.0
 * @since 6.1.0 Added an `$args` parameter for the section's HTML wrapper and class name.
 *
 * @global array $wp_settings_sections Storage array of all settings sections added to admin pages.
 *
 * @param string   $id       Slug-name to identify the section. Used in the 'id' attribute of tags.
 * @param string   $title    Formatted title of the section. Shown as the heading for the section.
 * @param callable $callback Function that echos out any content at the top of the section (between heading and fields).
 * @param string   $page     The slug-name of the settings page on which to show the section. Built-in pages include
 *                           'general', 'reading', 'writing', 'discussion', 'media', etc. Create your own using
 *                           add_options_page();
 * @param array    $args     {
 *     Arguments used to create the settings section.
 *
 *     @type string $before_section HTML content to prepend to the section's HTML output.
 *                                  Receives the section's class name as `%s`. Default empty.
 *     @type string $after_section  HTML content to append to the section's HTML output. Default empty.
 *     @type string $section_class  The class name to use for the section. Default empty.
 * }
 */
function add_settings_section( $id, $title, $callback, $page, $args = array() ) {
	global $wp_settings_sections;

	$defaults = array(
		'id'             => $id,
		'title'          => $title,
		'callback'       => $callback,
		'before_section' => '',
		'after_section'  => '',
		'section_class'  => '',
	);

	$section = wp_parse_args( $args, $defaults );

	if ( 'misc' === $page ) {
		_deprecated_argument(
			__FUNCTION__,
			'3.0.0',
			sprintf(
				/* translators: %s: misc */
				__( 'The "%s" options group has been removed. Use another settings group.' ),
				'misc'
			)
		);
		$page = 'general';
	}

	if ( 'privacy' === $page ) {
		_deprecated_argument(
			__FUNCTION__,
			'3.5.0',
			sprintf(
				/* translators: %s: privacy */
				__( 'The "%s" options group has been removed. Use another settings group.' ),
				'privacy'
			)
		);
		$page = 'reading';
	}

	$wp_settings_sections[ $page ][ $id ] = $section;
}

/**
 * Adds a new field to a section of a settings page.
 *
 * Part of the Settings API. Use this to define a settings field that will show
 * as part of a settings section inside a settings page. The fields are shown using
 * do_settings_fields() in do_settings_sections().
 *
 * The $callback argument should be the name of a function that echoes out the
 * HTML input tags for this setting field. Use get_option() to retrieve existing
 * values to show.
 *
 * @since 2.7.0
 * @since 4.2.0 The `$class` argument was added.
 *
 * @global array $wp_settings_fields Storage array of settings fields and info about their pages/sections.
 *
 * @param string   $id       Slug-name to identify the field. Used in the 'id' attribute of tags.
 * @param string   $title    Formatted title of the field. Shown as the label for the field
 *                           during output.
 * @param callable $callback Function that fills the field with the desired form inputs. The
 *                           function should echo its output.
 * @param string   $page     The slug-name of the settings page on which to show the section
 *                           (general, reading, writing, ...).
 * @param string   $section  Optional. The slug-name of the section of the settings page
 *                           in which to show the box. Default 'default'.
 * @param array    $args {
 *     Optional. Extra arguments that get passed to the callback function.
 *
 *     @type string $label_for When supplied, the setting title will be wrapped
 *                             in a `<label>` element, its `for` attribute populated
 *                             with this value.
 *     @type string $class     CSS Class to be added to the `<tr>` element when the
 *                             field is output.
 * }
 */
function add_settings_field( $id, $title, $callback, $page, $section = 'default', $args = array() ) {
	global $wp_settings_fields;

	if ( 'misc' === $page ) {
		_deprecated_argument(
			__FUNCTION__,
			'3.0.0',
			sprintf(
				/* translators: %s: misc */
				__( 'The "%s" options group has been removed. Use another settings group.' ),
				'misc'
			)
		);
		$page = 'general';
	}

	if ( 'privacy' === $page ) {
		_deprecated_argument(
			__FUNCTION__,
			'3.5.0',
			sprintf(
				/* translators: %s: privacy */
				__( 'The "%s" options group has been removed. Use another settings group.' ),
				'privacy'
			)
		);
		$page = 'reading';
	}

	$wp_settings_fields[ $page ][ $section ][ $id ] = array(
		'id'       => $id,
		'title'    => $title,
		'callback' => $callback,
		'args'     => $args,
	);
}

/**
 * Prints out all settings sections added to a particular settings page.
 *
 * Part of the Settings API. Use this in a settings page callback function
 * to output all the sections and fields that were added to that $page with
 * add_settings_section() and add_settings_field()
 *
 * @global array $wp_settings_sections Storage array of all settings sections added to admin pages.
 * @global array $wp_settings_fields Storage array of settings fields and info about their pages/sections.
 * @since 2.7.0
 *
 * @param string $page The slug name of the page whose settings sections you want to output.
 */
function do_settings_sections( $page ) {
	global $wp_settings_sections, $wp_settings_fields;

	if ( ! isset( $wp_settings_sections[ $page ] ) ) {
		return;
	}

	foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
		if ( '' !== $section['before_section'] ) {
			if ( '' !== $section['section_class'] ) {
				echo wp_kses_post( sprintf( $section['before_section'], esc_attr( $section['section_class'] ) ) );
			} else {
				echo wp_kses_post( $section['before_section'] );
			}
		}

		if ( $section['title'] ) {
			echo "<h2>{$section['title']}</h2>\n";
		}

		if ( $section['callback'] ) {
			call_user_func( $section['callback'], $section );
		}

		if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
			continue;
		}
		echo '<table class="form-table" role="presentation">';
		do_settings_fields( $page, $section['id'] );
		echo '</table>';

		if ( '' !== $section['after_section'] ) {
			echo wp_kses_post( $section['after_section'] );
		}
	}
}

/**
 * Prints out the settings fields for a particular settings section.
 *
 * Part of the Settings API. Use this in a settings page to output
 * a specific section. Should normally be called by do_settings_sections()
 * rather than directly.
 *
 * @global array $wp_settings_fields Storage array of settings fields and their pages/sections.
 *
 * @since 2.7.0
 *
 * @param string $page Slug title of the admin page whose settings fields you want to show.
 * @param string $section Slug title of the settings section whose fields you want to show.
 */
function do_settings_fields( $page, $section ) {
	global $wp_settings_fields;

	if ( ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
		return;
	}

	foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {
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
		call_user_func( $field['callback'], $field['args'] );
		echo '</td>';
		echo '</tr>';
	}
}

/**
 * Registers a settings error to be displayed to the user.
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
 * @since 5.3.0 Added `warning` and `info` as possible values for `$type`.
 *
 * @global array[] $wp_settings_errors Storage array of errors registered during this pageload
 *
 * @param string $setting Slug title of the setting to which this error applies.
 * @param string $code    Slug-name to identify the error. Used as part of 'id' attribute in HTML output.
 * @param string $message The formatted message text to display to the user (will be shown inside styled
 *                        `<div>` and `<p>` tags).
 * @param string $type    Optional. Message type, controls HTML class. Possible values include 'error',
 *                        'success', 'warning', 'info'. Default 'error'.
 */
function add_settings_error( $setting, $code, $message, $type = 'error' ) {
	global $wp_settings_errors;

	$wp_settings_errors[] = array(
		'setting' => $setting,
		'code'    => $code,
		'message' => $message,
		'type'    => $type,
	);
}

/**
 * Fetches settings errors registered by add_settings_error().
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
 * hasn't submitted data (i.e. when they first load an options page, or in the {@see 'admin_notices'}
 * action hook).
 *
 * @since 3.0.0
 *
 * @global array[] $wp_settings_errors Storage array of errors registered during this pageload
 *
 * @param string $setting  Optional. Slug title of a specific setting whose errors you want.
 * @param bool   $sanitize Optional. Whether to re-sanitize the setting value before returning errors.
 * @return array[] {
 *     Array of settings error arrays.
 *
 *     @type array ...$0 {
 *         Associative array of setting error data.
 *
 *         @type string $setting Slug title of the setting to which this error applies.
 *         @type string $code    Slug-name to identify the error. Used as part of 'id' attribute in HTML output.
 *         @type string $message The formatted message text to display to the user (will be shown inside styled
 *                               `<div>` and `<p>` tags).
 *         @type string $type    Optional. Message type, controls HTML class. Possible values include 'error',
 *                               'success', 'warning', 'info'. Default 'error'.
 *     }
 * }
 */
function get_settings_errors( $setting = '', $sanitize = false ) {
	global $wp_settings_errors;

	/*
	 * If $sanitize is true, manually re-run the sanitization for this option
	 * This allows the $sanitize_callback from register_setting() to run, adding
	 * any settings errors you want to show by default.
	 */
	if ( $sanitize ) {
		sanitize_option( $setting, get_option( $setting ) );
	}

	// If settings were passed back from options.php then use them.
	if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] && get_transient( 'settings_errors' ) ) {
		$wp_settings_errors = array_merge( (array) $wp_settings_errors, get_transient( 'settings_errors' ) );
		delete_transient( 'settings_errors' );
	}

	// Check global in case errors have been added on this pageload.
	if ( empty( $wp_settings_errors ) ) {
		return array();
	}

	// Filter the results to those of a specific setting if one was set.
	if ( $setting ) {
		$setting_errors = array();

		foreach ( (array) $wp_settings_errors as $key => $details ) {
			if ( $setting === $details['setting'] ) {
				$setting_errors[] = $wp_settings_errors[ $key ];
			}
		}

		return $setting_errors;
	}

	return $wp_settings_errors;
}

/**
 * Displays settings errors registered by add_settings_error().
 *
 * Part of the Settings API. Outputs a div for each error retrieved by
 * get_settings_errors().
 *
 * This is called automatically after a settings page based on the
 * Settings API is submitted. Errors should be added during the validation
 * callback function for a setting defined in register_setting().
 *
 * The $sanitize option is passed into get_settings_errors() and will
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
 * @since 5.3.0 Legacy `error` and `updated` CSS classes are mapped to
 *              `notice-error` and `notice-success`.
 *
 * @param string $setting        Optional slug title of a specific setting whose errors you want.
 * @param bool   $sanitize       Whether to re-sanitize the setting value before returning errors.
 * @param bool   $hide_on_update If set to true errors will not be shown if the settings page has
 *                               already been submitted.
 */
function settings_errors( $setting = '', $sanitize = false, $hide_on_update = false ) {

	if ( $hide_on_update && ! empty( $_GET['settings-updated'] ) ) {
		return;
	}

	$settings_errors = get_settings_errors( $setting, $sanitize );

	if ( empty( $settings_errors ) ) {
		return;
	}

	$output = '';

	foreach ( $settings_errors as $key => $details ) {
		if ( 'updated' === $details['type'] ) {
			$details['type'] = 'success';
		}

		if ( in_array( $details['type'], array( 'error', 'success', 'warning', 'info' ), true ) ) {
			$details['type'] = 'notice-' . $details['type'];
		}

		$css_id    = sprintf(
			'setting-error-%s',
			esc_attr( $details['code'] )
		);
		$css_class = sprintf(
			'notice %s settings-error is-dismissible',
			esc_attr( $details['type'] )
		);

		$output .= "<div id='$css_id' class='$css_class'> \n";
		$output .= "<p><strong>{$details['message']}</strong></p>";
		$output .= "</div> \n";
	}

	echo $output;
}

/**
 * Outputs the modal window used for attaching media to posts or pages in the media-listing screen.
 *
 * @since 2.7.0
 *
 * @param string $found_action Optional. The value of the 'found_action' input field. Default empty string.
 */
function find_posts_div( $found_action = '' ) {
	?>
	<div id="find-posts" class="find-box" style="display: none;">
		<div id="find-posts-head" class="find-box-head">
			<?php _e( 'Attach to existing content' ); ?>
			<button type="button" id="find-posts-close"><span class="screen-reader-text">
				<?php
				/* translators: Hidden accessibility text. */
				_e( 'Close media attachment panel' );
				?>
			</span></button>
		</div>
		<div class="find-box-inside">
			<div class="find-box-search">
				<?php if ( $found_action ) { ?>
					<input type="hidden" name="found_action" value="<?php echo esc_attr( $found_action ); ?>" />
				<?php } ?>
				<input type="hidden" name="affected" id="affected" value="" />
				<?php wp_nonce_field( 'find-posts', '_ajax_nonce', false ); ?>
				<label class="screen-reader-text" for="find-posts-input">
					<?php
					/* translators: Hidden accessibility text. */
					_e( 'Search' );
					?>
				</label>
				<input type="text" id="find-posts-input" name="ps" value="" />
				<span class="spinner"></span>
				<input type="button" id="find-posts-search" value="<?php esc_attr_e( 'Search' ); ?>" class="button" />
				<div class="clear"></div>
			</div>
			<div id="find-posts-response"></div>
		</div>
		<div class="find-box-buttons">
			<?php submit_button( __( 'Select' ), 'primary alignright', 'find-posts-submit', false ); ?>
			<div class="clear"></div>
		</div>
	</div>
	<?php
}

/**
 * Displays the post password.
 *
 * The password is passed through esc_attr() to ensure that it is safe for placing in an HTML attribute.
 *
 * @since 2.7.0
 */
function the_post_password() {
	$post = get_post();
	if ( isset( $post->post_password ) ) {
		echo esc_attr( $post->post_password );
	}
}

/**
 * Gets the post title.
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
	if ( empty( $title ) ) {
		$title = __( '(no title)' );
	}
	return esc_html( $title );
}

/**
 * Displays the search query.
 *
 * A simple wrapper to display the "s" parameter in a `GET` URI. This function
 * should only be used when the_search_query() cannot.
 *
 * @since 2.7.0
 */
function _admin_search_query() {
	echo isset( $_REQUEST['s'] ) ? esc_attr( wp_unslash( $_REQUEST['s'] ) ) : '';
}

/**
 * Generic Iframe header for use with Thickbox.
 *
 * @since 2.7.0
 *
 * @global string    $hook_suffix
 * @global string    $admin_body_class
 * @global string    $body_id
 * @global WP_Locale $wp_locale        WordPress date and time locale object.
 *
 * @param string $title      Optional. Title of the Iframe page. Default empty.
 * @param bool   $deprecated Not used.
 */
function iframe_header( $title = '', $deprecated = false ) {
	global $hook_suffix, $admin_body_class, $body_id, $wp_locale;

	show_admin_bar( false );

	$admin_body_class = preg_replace( '/[^a-z0-9_-]+/i', '-', $hook_suffix );

	$current_screen = get_current_screen();

	header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );
	_wp_admin_html_begin();
	?>
<title><?php bloginfo( 'name' ); ?> &rsaquo; <?php echo $title; ?> &#8212; <?php _e( 'WordPress' ); ?></title>
	<?php
	wp_enqueue_style( 'colors' );
	?>
<script type="text/javascript">
addLoadEvent = function(func){if(typeof jQuery!=='undefined')jQuery(function(){func();});else if(typeof wpOnload!=='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
function tb_close(){var win=window.dialogArguments||opener||parent||top;win.tb_remove();}
var ajaxurl = '<?php echo esc_js( admin_url( 'admin-ajax.php', 'relative' ) ); ?>',
	pagenow = '<?php echo esc_js( $current_screen->id ); ?>',
	typenow = '<?php echo esc_js( $current_screen->post_type ); ?>',
	adminpage = '<?php echo esc_js( $admin_body_class ); ?>',
	thousandsSeparator = '<?php echo esc_js( $wp_locale->number_format['thousands_sep'] ); ?>',
	decimalPoint = '<?php echo esc_js( $wp_locale->number_format['decimal_point'] ); ?>',
	isRtl = <?php echo (int) is_rtl(); ?>;
</script>
	<?php
	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_enqueue_scripts', $hook_suffix );

	/** This action is documented in wp-admin/admin-header.php */
	do_action( "admin_print_styles-{$hook_suffix}" );  // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_print_styles' );

	/** This action is documented in wp-admin/admin-header.php */
	do_action( "admin_print_scripts-{$hook_suffix}" ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_print_scripts' );

	/** This action is documented in wp-admin/admin-header.php */
	do_action( "admin_head-{$hook_suffix}" ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_head' );

	$admin_body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_user_locale() ) ) );

	if ( is_rtl() ) {
		$admin_body_class .= ' rtl';
	}

	?>
</head>
	<?php
	$admin_body_id = isset( $body_id ) ? 'id="' . $body_id . '" ' : '';

	/** This filter is documented in wp-admin/admin-header.php */
	$admin_body_classes = apply_filters( 'admin_body_class', '' );
	$admin_body_classes = ltrim( $admin_body_classes . ' ' . $admin_body_class );
	?>
<body <?php echo $admin_body_id; ?>class="wp-admin wp-core-ui no-js iframe <?php echo esc_attr( $admin_body_classes ); ?>">
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
 * Generic Iframe footer for use with Thickbox.
 *
 * @since 2.7.0
 */
function iframe_footer() {
	/*
	 * We're going to hide any footer output on iFrame pages,
	 * but run the hooks anyway since they output JavaScript
	 * or other needed content.
	 */

	/**
	 * @global string $hook_suffix
	 */
	global $hook_suffix;
	?>
	<div class="hidden">
	<?php
	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_footer', $hook_suffix );

	/** This action is documented in wp-admin/admin-footer.php */
	do_action( "admin_print_footer_scripts-{$hook_suffix}" ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_print_footer_scripts' );
	?>
	</div>
<script type="text/javascript">if(typeof wpOnload==='function')wpOnload();</script>
</body>
</html>
	<?php
}

/**
 * Echoes or returns the post states as HTML.
 *
 * @since 2.7.0
 * @since 5.3.0 Added the `$display` parameter and a return value.
 *
 * @see get_post_states()
 *
 * @param WP_Post $post    The post to retrieve states for.
 * @param bool    $display Optional. Whether to display the post states as an HTML string.
 *                         Default true.
 * @return string Post states string.
 */
function _post_states( $post, $display = true ) {
	$post_states        = get_post_states( $post );
	$post_states_string = '';

	if ( ! empty( $post_states ) ) {
		$state_count = count( $post_states );

		$i = 0;

		$post_states_string .= ' &mdash; ';

		foreach ( $post_states as $state ) {
			++$i;

			$separator = ( $i < $state_count ) ? ', ' : '';

			$post_states_string .= "<span class='post-state'>{$state}{$separator}</span>";
		}
	}

	if ( $display ) {
		echo $post_states_string;
	}

	return $post_states_string;
}

/**
 * Retrieves an array of post states from a post.
 *
 * @since 5.3.0
 *
 * @param WP_Post $post The post to retrieve states for.
 * @return string[] Array of post state labels keyed by their state.
 */
function get_post_states( $post ) {
	$post_states = array();

	if ( isset( $_REQUEST['post_status'] ) ) {
		$post_status = $_REQUEST['post_status'];
	} else {
		$post_status = '';
	}

	if ( ! empty( $post->post_password ) ) {
		$post_states['protected'] = _x( 'Password protected', 'post status' );
	}

	if ( 'private' === $post->post_status && 'private' !== $post_status ) {
		$post_states['private'] = _x( 'Private', 'post status' );
	}

	if ( 'draft' === $post->post_status ) {
		if ( get_post_meta( $post->ID, '_customize_changeset_uuid', true ) ) {
			$post_states[] = __( 'Customization Draft' );
		} elseif ( 'draft' !== $post_status ) {
			$post_states['draft'] = _x( 'Draft', 'post status' );
		}
	} elseif ( 'trash' === $post->post_status && get_post_meta( $post->ID, '_customize_changeset_uuid', true ) ) {
		$post_states[] = _x( 'Customization Draft', 'post status' );
	}

	if ( 'pending' === $post->post_status && 'pending' !== $post_status ) {
		$post_states['pending'] = _x( 'Pending', 'post status' );
	}

	if ( is_sticky( $post->ID ) ) {
		$post_states['sticky'] = _x( 'Sticky', 'post status' );
	}

	if ( 'future' === $post->post_status ) {
		$post_states['scheduled'] = _x( 'Scheduled', 'post status' );
	}

	if ( 'page' === get_option( 'show_on_front' ) ) {
		if ( (int) get_option( 'page_on_front' ) === $post->ID ) {
			$post_states['page_on_front'] = _x( 'Front Page', 'page label' );
		}

		if ( (int) get_option( 'page_for_posts' ) === $post->ID ) {
			$post_states['page_for_posts'] = _x( 'Posts Page', 'page label' );
		}
	}

	if ( (int) get_option( 'wp_page_for_privacy_policy' ) === $post->ID ) {
		$post_states['page_for_privacy_policy'] = _x( 'Privacy Policy Page', 'page label' );
	}

	/**
	 * Filters the default post display states used in the posts list table.
	 *
	 * @since 2.8.0
	 * @since 3.6.0 Added the `$post` parameter.
	 * @since 5.5.0 Also applied in the Customizer context. If any admin functions
	 *              are used within the filter, their existence should be checked
	 *              with `function_exists()` before being used.
	 *
	 * @param string[] $post_states An array of post display states.
	 * @param WP_Post  $post        The current post object.
	 */
	return apply_filters( 'display_post_states', $post_states, $post );
}

/**
 * Outputs the attachment media states as HTML.
 *
 * @since 3.2.0
 * @since 5.6.0 Added the `$display` parameter and a return value.
 *
 * @param WP_Post $post    The attachment post to retrieve states for.
 * @param bool    $display Optional. Whether to display the post states as an HTML string.
 *                         Default true.
 * @return string Media states string.
 */
function _media_states( $post, $display = true ) {
	$media_states        = get_media_states( $post );
	$media_states_string = '';

	if ( ! empty( $media_states ) ) {
		$state_count = count( $media_states );

		$i = 0;

		$media_states_string .= ' &mdash; ';

		foreach ( $media_states as $state ) {
			++$i;

			$separator = ( $i < $state_count ) ? ', ' : '';

			$media_states_string .= "<span class='post-state'>{$state}{$separator}</span>";
		}
	}

	if ( $display ) {
		echo $media_states_string;
	}

	return $media_states_string;
}

/**
 * Retrieves an array of media states from an attachment.
 *
 * @since 5.6.0
 *
 * @param WP_Post $post The attachment to retrieve states for.
 * @return string[] Array of media state labels keyed by their state.
 */
function get_media_states( $post ) {
	static $header_images;

	$media_states = array();
	$stylesheet   = get_option( 'stylesheet' );

	if ( current_theme_supports( 'custom-header' ) ) {
		$meta_header = get_post_meta( $post->ID, '_wp_attachment_is_custom_header', true );

		if ( is_random_header_image() ) {
			if ( ! isset( $header_images ) ) {
				$header_images = wp_list_pluck( get_uploaded_header_images(), 'attachment_id' );
			}

			if ( $meta_header === $stylesheet && in_array( $post->ID, $header_images, true ) ) {
				$media_states[] = __( 'Header Image' );
			}
		} else {
			$header_image = get_header_image();

			// Display "Header Image" if the image was ever used as a header image.
			if ( ! empty( $meta_header ) && $meta_header === $stylesheet && wp_get_attachment_url( $post->ID ) !== $header_image ) {
				$media_states[] = __( 'Header Image' );
			}

			// Display "Current Header Image" if the image is currently the header image.
			if ( $header_image && wp_get_attachment_url( $post->ID ) === $header_image ) {
				$media_states[] = __( 'Current Header Image' );
			}
		}

		if ( get_theme_support( 'custom-header', 'video' ) && has_header_video() ) {
			$mods = get_theme_mods();
			if ( isset( $mods['header_video'] ) && $post->ID === $mods['header_video'] ) {
				$media_states[] = __( 'Current Header Video' );
			}
		}
	}

	if ( current_theme_supports( 'custom-background' ) ) {
		$meta_background = get_post_meta( $post->ID, '_wp_attachment_is_custom_background', true );

		if ( ! empty( $meta_background ) && $meta_background === $stylesheet ) {
			$media_states[] = __( 'Background Image' );

			$background_image = get_background_image();
			if ( $background_image && wp_get_attachment_url( $post->ID ) === $background_image ) {
				$media_states[] = __( 'Current Background Image' );
			}
		}
	}

	if ( (int) get_option( 'site_icon' ) === $post->ID ) {
		$media_states[] = __( 'Site Icon' );
	}

	if ( (int) get_theme_mod( 'custom_logo' ) === $post->ID ) {
		$media_states[] = __( 'Logo' );
	}

	/**
	 * Filters the default media display states for items in the Media list table.
	 *
	 * @since 3.2.0
	 * @since 4.8.0 Added the `$post` parameter.
	 *
	 * @param string[] $media_states An array of media states. Default 'Header Image',
	 *                               'Background Image', 'Site Icon', 'Logo'.
	 * @param WP_Post  $post         The current attachment object.
	 */
	return apply_filters( 'display_media_states', $media_states, $post );
}

/**
 * Tests support for compressing JavaScript from PHP.
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
	var compressionNonce = <?php echo wp_json_encode( wp_create_nonce( 'update_can_compress_scripts' ) ); ?>;
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

				x.open('GET', ajaxurl + '?action=wp-compression-test&test='+test+'&_ajax_nonce='+compressionNonce+'&'+(new Date()).getTime(), true);
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
				if ( '"wpCompressionTest' === r )
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
 * @param string       $text             Optional. The text of the button. Defaults to 'Save Changes'.
 * @param string       $type             Optional. The type and CSS class(es) of the button. Core values
 *                                       include 'primary', 'small', and 'large'. Default 'primary'.
 * @param string       $name             Optional. The HTML name of the submit button. If no `id` attribute
 *                                       is given in the `$other_attributes` parameter, `$name` will be used
 *                                       as the button's `id`. Default 'submit'.
 * @param bool         $wrap             Optional. True if the output button should be wrapped in a paragraph tag,
 *                                       false otherwise. Default true.
 * @param array|string $other_attributes Optional. Other attributes that should be output with the button,
 *                                       mapping attributes to their values, e.g. `array( 'id' => 'search-submit' )`.
 *                                       These key/value attribute pairs will be output as `attribute="value"`,
 *                                       where attribute is the key. Attributes can also be provided as a string,
 *                                       e.g. `id="search-submit"`, though the array format is generally preferred.
 *                                       Default empty string.
 */
function submit_button( $text = '', $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = '' ) {
	echo get_submit_button( $text, $type, $name, $wrap, $other_attributes );
}

/**
 * Returns a submit button, with provided text and appropriate class.
 *
 * @since 3.1.0
 *
 * @param string       $text             Optional. The text of the button. Defaults to 'Save Changes'.
 * @param string       $type             Optional. The type and CSS class(es) of the button. Core values
 *                                       include 'primary', 'small', and 'large'. Default 'primary large'.
 * @param string       $name             Optional. The HTML name of the submit button. If no `id` attribute
 *                                       is given in the `$other_attributes` parameter, `$name` will be used
 *                                       as the button's `id`. Default 'submit'.
 * @param bool         $wrap             Optional. True if the output button should be wrapped in a paragraph tag,
 *                                       false otherwise. Default true.
 * @param array|string $other_attributes Optional. Other attributes that should be output with the button,
 *                                       mapping attributes to their values, e.g. `array( 'id' => 'search-submit' )`.
 *                                       These key/value attribute pairs will be output as `attribute="value"`,
 *                                       where attribute is the key. Attributes can also be provided as a string,
 *                                       e.g. `id="search-submit"`, though the array format is generally preferred.
 *                                       Default empty string.
 * @return string Submit button HTML.
 */
function get_submit_button( $text = '', $type = 'primary large', $name = 'submit', $wrap = true, $other_attributes = '' ) {
	if ( ! is_array( $type ) ) {
		$type = explode( ' ', $type );
	}

	$button_shorthand = array( 'primary', 'small', 'large' );
	$classes          = array( 'button' );

	foreach ( $type as $t ) {
		if ( 'secondary' === $t || 'button-secondary' === $t ) {
			continue;
		}

		$classes[] = in_array( $t, $button_shorthand, true ) ? 'button-' . $t : $t;
	}

	// Remove empty items, remove duplicate items, and finally build a string.
	$class = implode( ' ', array_unique( array_filter( $classes ) ) );

	$text = $text ? $text : __( 'Save Changes' );

	// Default the id attribute to $name unless an id was specifically provided in $other_attributes.
	$id = $name;
	if ( is_array( $other_attributes ) && isset( $other_attributes['id'] ) ) {
		$id = $other_attributes['id'];
		unset( $other_attributes['id'] );
	}

	$attributes = '';
	if ( is_array( $other_attributes ) ) {
		foreach ( $other_attributes as $attribute => $value ) {
			$attributes .= $attribute . '="' . esc_attr( $value ) . '" '; // Trailing space is important.
		}
	} elseif ( ! empty( $other_attributes ) ) { // Attributes provided as a string.
		$attributes = $other_attributes;
	}

	// Don't output empty name and id attributes.
	$name_attr = $name ? ' name="' . esc_attr( $name ) . '"' : '';
	$id_attr   = $id ? ' id="' . esc_attr( $id ) . '"' : '';

	$button  = '<input type="submit"' . $name_attr . $id_attr . ' class="' . esc_attr( $class );
	$button .= '" value="' . esc_attr( $text ) . '" ' . $attributes . ' />';

	if ( $wrap ) {
		$button = '<p class="submit">' . $button . '</p>';
	}

	return $button;
}

/**
 * Prints out the beginning of the admin HTML header.
 *
 * @global bool $is_IE
 */
function _wp_admin_html_begin() {
	global $is_IE;

	$admin_html_class = ( is_admin_bar_showing() ) ? 'wp-toolbar' : '';

	if ( $is_IE ) {
		header( 'X-UA-Compatible: IE=edge' );
	}

	?>
<!DOCTYPE html>
<html class="<?php echo $admin_html_class; ?>"
	<?php
	/**
	 * Fires inside the HTML tag in the admin header.
	 *
	 * @since 2.2.0
	 */
	do_action( 'admin_xml_ns' );

	language_attributes();
	?>
>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php echo get_option( 'blog_charset' ); ?>" />
	<?php
}

/**
 * Converts a screen string to a screen object.
 *
 * @since 3.0.0
 *
 * @param string $hook_name The hook name (also known as the hook suffix) used to determine the screen.
 * @return WP_Screen Screen object.
 */
function convert_to_screen( $hook_name ) {
	if ( ! class_exists( 'WP_Screen' ) ) {
		_doing_it_wrong(
			'convert_to_screen(), add_meta_box()',
			sprintf(
				/* translators: 1: wp-admin/includes/template.php, 2: add_meta_box(), 3: add_meta_boxes */
				__( 'Likely direct inclusion of %1$s in order to use %2$s. This is very wrong. Hook the %2$s call into the %3$s action instead.' ),
				'<code>wp-admin/includes/template.php</code>',
				'<code>add_meta_box()</code>',
				'<code>add_meta_boxes</code>'
			),
			'3.3.0'
		);
		return (object) array(
			'id'   => '_invalid',
			'base' => '_are_belong_to_us',
		);
	}

	return WP_Screen::get( $hook_name );
}

/**
 * Outputs the HTML for restoring the post data from DOM storage
 *
 * @since 3.6.0
 * @access private
 */
function _local_storage_notice() {
	$local_storage_message  = '<p class="local-restore">';
	$local_storage_message .= __( 'The backup of this post in your browser is different from the version below.' );
	$local_storage_message .= '<button type="button" class="button restore-backup">' . __( 'Restore the backup' ) . '</button></p>';
	$local_storage_message .= '<p class="help">';
	$local_storage_message .= __( 'This will replace the current editor content with the last backup version. You can use undo and redo in the editor to get the old content back or to return to the restored version.' );
	$local_storage_message .= '</p>';

	wp_admin_notice(
		$local_storage_message,
		array(
			'id'                 => 'local-storage-notice',
			'additional_classes' => array( 'hidden' ),
			'dismissible'        => true,
			'paragraph_wrap'     => false,
		)
	);
}

/**
 * Outputs a HTML element with a star rating for a given rating.
 *
 * Outputs a HTML element with the star rating exposed on a 0..5 scale in
 * half star increments (ie. 1, 1.5, 2 stars). Optionally, if specified, the
 * number of ratings may also be displayed by passing the $number parameter.
 *
 * @since 3.8.0
 * @since 4.4.0 Introduced the `echo` parameter.
 *
 * @param array $args {
 *     Optional. Array of star ratings arguments.
 *
 *     @type int|float $rating The rating to display, expressed in either a 0.5 rating increment,
 *                             or percentage. Default 0.
 *     @type string    $type   Format that the $rating is in. Valid values are 'rating' (default),
 *                             or, 'percent'. Default 'rating'.
 *     @type int       $number The number of ratings that makes up this rating. Default 0.
 *     @type bool      $echo   Whether to echo the generated markup. False to return the markup instead
 *                             of echoing it. Default true.
 * }
 * @return string Star rating HTML.
 */
function wp_star_rating( $args = array() ) {
	$defaults    = array(
		'rating' => 0,
		'type'   => 'rating',
		'number' => 0,
		'echo'   => true,
	);
	$parsed_args = wp_parse_args( $args, $defaults );

	// Non-English decimal places when the $rating is coming from a string.
	$rating = (float) str_replace( ',', '.', $parsed_args['rating'] );

	// Convert percentage to star rating, 0..5 in .5 increments.
	if ( 'percent' === $parsed_args['type'] ) {
		$rating = round( $rating / 10, 0 ) / 2;
	}

	// Calculate the number of each type of star needed.
	$full_stars  = floor( $rating );
	$half_stars  = ceil( $rating - $full_stars );
	$empty_stars = 5 - $full_stars - $half_stars;

	if ( $parsed_args['number'] ) {
		/* translators: Hidden accessibility text. 1: The rating, 2: The number of ratings. */
		$format = _n( '%1$s rating based on %2$s rating', '%1$s rating based on %2$s ratings', $parsed_args['number'] );
		$title  = sprintf( $format, number_format_i18n( $rating, 1 ), number_format_i18n( $parsed_args['number'] ) );
	} else {
		/* translators: Hidden accessibility text. %s: The rating. */
		$title = sprintf( __( '%s rating' ), number_format_i18n( $rating, 1 ) );
	}

	$output  = '<div class="star-rating">';
	$output .= '<span class="screen-reader-text">' . $title . '</span>';
	$output .= str_repeat( '<div class="star star-full" aria-hidden="true"></div>', $full_stars );
	$output .= str_repeat( '<div class="star star-half" aria-hidden="true"></div>', $half_stars );
	$output .= str_repeat( '<div class="star star-empty" aria-hidden="true"></div>', $empty_stars );
	$output .= '</div>';

	if ( $parsed_args['echo'] ) {
		echo $output;
	}

	return $output;
}

/**
 * Outputs a notice when editing the page for posts (internal use only).
 *
 * @ignore
 * @since 4.2.0
 */
function _wp_posts_page_notice() {
	wp_admin_notice(
		__( 'You are currently editing the page that shows your latest posts.' ),
		array(
			'type'               => 'warning',
			'additional_classes' => array( 'inline' ),
		)
	);
}

/**
 * Outputs a notice when editing the page for posts in the block editor (internal use only).
 *
 * @ignore
 * @since 5.8.0
 */
function _wp_block_editor_posts_page_notice() {
	wp_add_inline_script(
		'wp-notices',
		sprintf(
			'wp.data.dispatch( "core/notices" ).createWarningNotice( "%s", { isDismissible: false } )',
			__( 'You are currently editing the page that shows your latest posts.' )
		),
		'after'
	);
}
