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
 * @param unknown_type $position
 * @param unknown_type $checkbox
 * @param unknown_type $mode
 */
function wp_comment_reply($position = '1', $checkbox = false, $mode = 'single', $table_row = true) {
	// allow plugin to replace the popup content
	$content = apply_filters( 'wp_comment_reply', '', array('position' => $position, 'checkbox' => $checkbox, 'mode' => $mode) );

	if ( ! empty($content) ) {
		echo $content;
		return;
	}

	$wp_list_table = get_list_table('comments');

	$columns = $wp_list_table->get_column_headers();
	$hidden = array_intersect( array_keys( $columns ), array_filter( $wp_list_table->get_hidden_columns() ) );
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

	<input type="hidden" name="user_ID" id="user_ID" value="<?php echo get_current_user_id(); ?>" />
	<input type="hidden" name="action" id="action" value="" />
	<input type="hidden" name="comment_ID" id="comment_ID" value="" />
	<input type="hidden" name="comment_post_ID" id="comment_post_ID" value="" />
	<input type="hidden" name="status" id="status" value="" />
	<input type="hidden" name="position" id="position" value="<?php echo $position; ?>" />
	<input type="hidden" name="checkbox" id="checkbox" value="<?php echo $checkbox ? 1 : 0; ?>" />
	<input type="hidden" name="mode" id="mode" value="<?php echo esc_attr($mode); ?>" />
	<?php wp_nonce_field( 'replyto-comment', '_ajax_nonce-replyto-comment', false ); ?>
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
	$r .= "\n\t\t<input name='updatemeta' type='submit' tabindex='6' value='". esc_attr__( 'Update' ) ."' class='add:the-list:meta-{$entry['meta_id']}::_ajax_nonce-add-meta=$update_nonce updatemeta' /></div>";
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
<?php wp_nonce_field( 'add-meta', '_ajax_nonce-add-meta', false ); ?>
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
 * Meta-Box template function
 *
 * @since 2.5.0
 *
 * @param string $page page identifier, also known as screen identifier
 * @param string $context box context
 * @param mixed $object gets passed to the box callback function as first parameter
 * @return int number of meta_boxes
 */
function do_meta_boxes($page, $context, $object) {
	global $wp_meta_boxes;
	static $already_sorted = false;

	$hidden = get_hidden_meta_boxes($page);

	printf('<div id="%s-sortables" class="meta-box-sortables">', htmlspecialchars($context));

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

/**
 * Get Hidden Meta Boxes
 *
 * @since 2.7
 *
 * @param string|object $screen Screen identifier
 * @return array Hidden Meta Boxes
 */
function get_hidden_meta_boxes( $screen ) {
	if ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	$hidden = get_user_option( "metaboxhidden_{$screen->id}" );

	// Hide slug boxes by default
	if ( !is_array( $hidden ) )
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
function manage_columns_prefs( $page ) {
	global $wp_list_table;

	list( $columns, $hidden ) = $wp_list_table->get_column_headers();

	$special = array('_title', 'cb', 'comment', 'media', 'name', 'title', 'username');

	foreach ( $columns as $column => $title ) {
		// Can't hide these for they are special
		if ( in_array( $column, $special ) )
			continue;
		if ( empty( $title ) )
			continue;

		if ( 'comments' == $column )
			$title = __( 'Comments' );
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

				<?php
				$post_types = get_post_types( array('public' => true), 'objects' );
				foreach ( $post_types as $post ) {
					if ( 'attachment' == $post->name )
						continue;
				?>
				<input type="radio" name="find-posts-what" id="find-posts-<?php echo esc_attr($post->name); ?>" value="<?php echo esc_attr($post->name); ?>" <?php checked($post->name,  'post'); ?> />
				<label for="find-posts-<?php echo esc_attr($post->name); ?>"><?php echo $post->label; ?></label>
				<?php
				} ?>
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
	$default_action = false;

	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	if ( isset($screen->post_type) ) {
		$post_type_object = get_post_type_object($screen->post_type);
		if ( 'add' != $screen->action )
			$default_action = array('post-new.php?post_type=' . $post_type_object->name => array($post_type_object->labels->new_item, $post_type_object->cap->edit_posts));
		else
			$default_action = array('edit.php?post_type=' . $post_type_object->name => array($post_type_object->labels->name, $post_type_object->cap->edit_posts));
	}

	if ( !$default_action ) {
		if ( $screen->is_network ) {
			$default_action = array('sites.php' => array( __('Sites'), 'manage_sites'));
		} else {
			switch ( $screen->id ) {
				case 'upload':
					$default_action = array('media-new.php' => array(__('New Media'), 'upload_files'));
					break;
				case 'media':
					$default_action = array('upload.php' => array(__('Edit Media'), 'upload_files'));
					break;
				case 'link-manager':
				case 'link':
					if ( 'add' != $screen->action )
						$default_action = array('link-add.php' => array(__('New Link'), 'manage_links'));
					else
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
	}

	if ( !$screen->is_network ) {
		$actions = array(
			'post-new.php' => array(__('New Post'), 'edit_posts'),
			'edit.php?post_status=draft' => array(__('Drafts'), 'edit_posts'),
			'post-new.php?post_type=page' => array(__('New Page'), 'edit_pages'),
			'media-new.php' => array(__('Upload'), 'upload_files'),
			'edit-comments.php' => array(__('Comments'), 'moderate_comments')
			);
	} else {
		$actions = array(
			'sites.php' => array( __('Sites'), 'manage_sites'),
			'users.php' => array( __('Users'), 'manage_network_users')
		);
	}

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

/**
 * Convert a screen string to a screen object
 *
 * @since 3.0.0
 *
 * @param string $screen The name of the screen
 * @return object An object containing the safe screen name and id
 */
function convert_to_screen( $screen ) {
	$screen = str_replace( array('.php', '-new', '-add' ), '', $screen);
	$screen = (string) apply_filters( 'screen_meta_screen', $screen );
	$screen = (object) array('id' => $screen, 'base' => $screen);
	return $screen;
}

function screen_meta($screen) {
	global $wp_meta_boxes, $_wp_contextual_help, $title, $wp_list_table;

	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	if ( is_a($wp_list_table, 'WP_List_Table') )
		list( $screen_columns ) = $wp_list_table->get_column_headers();
	$meta_screens = array('index' => 'dashboard');

	if ( isset($meta_screens[$screen->id]) ) {
		$screen->id = $meta_screens[$screen->id];
		$screen->base = $screen->id;
	}

	$show_screen = false;
	if ( !empty($wp_meta_boxes[$screen->id]) || !empty($screen_columns) )
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
			$show_screen = true;
			break;
	}
	if( ! empty( $settings ) )
		$show_screen = true;

?>
<div id="screen-meta">
<?php if ( $show_screen ) : ?>
<div id="screen-options-wrap" class="hidden">
	<form id="adv-settings" action="" method="post">
	<?php if ( isset($wp_meta_boxes[$screen->id]) ) : ?>
		<h5><?php _ex('Show on screen', 'Metaboxes') ?></h5>
		<div class="metabox-prefs">
			<?php meta_box_prefs($screen); ?>
			<br class="clear" />
		</div>
		<?php endif;
		if ( ! empty($screen_columns) ) : ?>
		<h5><?php echo ( isset( $screen_columns['_title'] ) ?  $screen_columns['_title'] :  _x('Show on screen', 'Columns') ) ?></h5>
		<div class="metabox-prefs">
			<?php manage_columns_prefs($screen); ?>
			<br class="clear" />
		</div>
	<?php endif;
	echo screen_layout($screen);

	if ( !empty( $screen_options ) ) {
		?>
		<h5><?php _ex('Show on screen', 'Screen Options') ?></h5>
		<?php
	}

	echo $screen_options;
	echo $settings; ?>
<div><?php wp_nonce_field( 'screen-options-nonce', 'screenoptionnonce', false ); ?></div>
</form>
</div>

<?php endif; // $show_screen

	$_wp_contextual_help = apply_filters('contextual_help_list', $_wp_contextual_help, $screen);
	?>
	<div id="contextual-help-wrap" class="hidden">
	<?php
	$contextual_help = '';
	if ( isset($_wp_contextual_help[$screen->id]) ) {
		$contextual_help .= '<div class="metabox-prefs">' . $_wp_contextual_help[$screen->id] . "</div>\n";
	} else {
		$contextual_help .= '<div class="metabox-prefs">';
		$default_help = __('<a href="http://codex.wordpress.org/" target="_blank">Documentation</a>');
		$default_help .= '<br />';
		$default_help .= __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>');
		$contextual_help .= apply_filters('default_contextual_help', $default_help);
		$contextual_help .= "</div>\n";
	}

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

	$columns = array('dashboard' => 4, 'link' => 2);

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
			if ( isset($_GET['post_type']) && in_array( $_GET['post_type'], get_post_types( array( 'show_ui' => true ) ) ) )
				$post_type = $_GET['post_type'];
			$post_type_object = get_post_type_object($post_type);
			$per_page_label = $post_type_object->labels->name;
			break;
		case 'ms-sites':
			$per_page_label = _x( 'Sites', 'sites per page (screen options)' );
			break;
		case 'users':
		case 'ms-users':
			$per_page_label = _x( 'Users', 'users per page (screen options)' );
			break;
		case 'edit-comments':
			$per_page_label = _x( 'Comments', 'comments per page (screen options)' );
			break;
		case 'upload':
			$per_page_label = _x( 'Media items', 'items per page (screen options)' );
			break;
		case 'edit-tags':
			global $tax;
			$per_page_label = $tax->labels->name;
			break;
		case 'plugins':
			$per_page_label = _x( 'Plugins', 'plugins per page (screen options)' );
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
	global $current_screen, $hook_suffix, $typenow, $taxnow;

	$action = '';

	if ( empty($id) ) {
		$current_screen = $hook_suffix;
		$current_screen = str_replace('.php', '', $current_screen);
		if ( preg_match('/-add|-new$/', $current_screen) )
			$action = 'add';
		$current_screen = str_replace('-new', '', $current_screen);
		$current_screen = str_replace('-add', '', $current_screen);
		$current_screen = array('id' => $current_screen, 'base' => $current_screen);
	} else {
		$id = sanitize_key($id);
		if ( false !== strpos($id, '-') ) {
			list( $id, $typenow ) = explode('-', $id, 2);
			if ( taxonomy_exists( $typenow ) ) {
				$id = 'edit-tags';
				$taxnow = $typenow;
				$typenow = '';
			}
		}
		$current_screen = array('id' => $id, 'base' => $id);
	}

	$current_screen = (object) $current_screen;

	$current_screen->action = $action;

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
	} elseif ( 'edit-tags' == $current_screen->id ) {
		if ( empty($taxnow) )
			$taxnow = 'post_tag';
		$current_screen->id = 'edit-' . $taxnow;
		$current_screen->taxonomy = $taxnow;
	}

	$current_screen->is_network = is_network_admin() ? true : false;

	$current_screen = apply_filters('current_screen', $current_screen);
}

