<?php

//
// Big Mess
//

// Dandy new recursive multiple category stuff.
function cat_rows( $parent = 0, $level = 0, $categories = 0 ) {
	if ( !$categories )
		$categories = get_categories( 'hide_empty=0' );

	$children = _get_term_hierarchy('category');

	if ( $categories ) {
		ob_start();
		foreach ( $categories as $category ) {
			if ( $category->parent == $parent) {
				echo "\t" . _cat_row( $category, $level );
				if ( isset($children[$category->term_id]) )
					cat_rows( $category->term_id, $level +1, $categories );
			}
		}
		$output = ob_get_contents();
		ob_end_clean();

		$output = apply_filters('cat_rows', $output);

		echo $output;
	} else {
		return false;
	}
}

function _cat_row( $category, $level, $name_override = false ) {
	global $class;

	$pad = str_repeat( '&#8212; ', $level );
	if ( current_user_can( 'manage_categories' ) ) {
		$edit = "<a href='categories.php?action=edit&amp;cat_ID=$category->term_id' class='edit'>".__( 'Edit' )."</a></td>";
		$default_cat_id = (int) get_option( 'default_category' );
		$default_link_cat_id = (int) get_option( 'default_link_category' );

		if ( $category->term_id != $default_cat_id )
			$edit .= "<td><a href='" . wp_nonce_url( "categories.php?action=delete&amp;cat_ID=$category->term_id", 'delete-category_' . $category->term_id ) . "' onclick=\"return deleteSomething( 'cat', $category->term_id, '" . js_escape(sprintf( __("You are about to delete the category '%s'.\nAll posts that were only assigned to this category will be assigned to the '%s' category.\nAll links that were only assigned to this category will be assigned to the '%s' category.\n'OK' to delete, 'Cancel' to stop." ), $category->name, get_catname( $default_cat_id ), get_catname( $default_link_cat_id ) )) . "' );\" class='delete'>".__( 'Delete' )."</a>";
		else
			$edit .= "<td style='text-align:center'>".__( "Default" );
	} else
		$edit = '';

	$class = ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || " class='alternate'" == $class ) ? '' : " class='alternate'";

	$category->count = number_format_i18n( $category->count );
	$posts_count = ( $category->count > 0 ) ? "<a href='edit.php?cat=$category->term_id'>$category->count</a>" : $category->count;
	$output = "<tr id='cat-$category->term_id'$class>
		<th scope='row' style='text-align: center'>$category->term_id</th>
		<td>" . ( $name_override ? $name_override : $pad . ' ' . $category->name ) . "</td>
		<td>$category->description</td>
		<td align='center'>$posts_count</td>
		<td>$edit</td>\n\t</tr>\n";

	return apply_filters('cat_row', $output);
}

function checked( $checked, $current) {
	if ( $checked == $current)
		echo ' checked="checked"';
}

// TODO: Remove?
function documentation_link( $for ) {
	return;
}

function selected( $selected, $current) {
	if ( $selected == $current)
		echo ' selected="selected"';
}

//
// Nasty Category Stuff
//

function sort_cats( $cat1, $cat2 ) {
	if ( $cat1['checked'] || $cat2['checked'] )
		return ( $cat1['checked'] && !$cat2['checked'] ) ? -1 : 1;
	else
		return strcasecmp( $cat1['cat_name'], $cat2['cat_name'] );
}

function get_nested_categories( $default = 0, $parent = 0 ) {
	global $post_ID, $mode, $wpdb, $checked_categories;

	if ( empty($checked_categories) ) {
		if ( $post_ID ) {
			$checked_categories = wp_get_post_categories($post_ID);

			if ( count( $checked_categories ) == 0 ) {
				// No selected categories, strange
			$checked_categories[] = $default;
			}
		} else {
			$checked_categories[] = $default;
		}
	}

	$cats = get_categories("parent=$parent&hide_empty=0&fields=ids");

	$result = array ();
	if ( is_array( $cats ) ) {
		foreach ( $cats as $cat) {
			$result[$cat]['children'] = get_nested_categories( $default, $cat);
			$result[$cat]['cat_ID'] = $cat;
			$result[$cat]['checked'] = in_array( $cat, $checked_categories );
			$result[$cat]['cat_name'] = get_the_category_by_ID( $cat);
		}
	}

	$result = apply_filters('get_nested_categories', $result);
	usort( $result, 'sort_cats' );

	return $result;
}

function write_nested_categories( $categories ) {
	foreach ( $categories as $category ) {
		echo '<li id="category-', $category['cat_ID'], '"><label for="in-category-', $category['cat_ID'], '" class="selectit"><input value="', $category['cat_ID'], '" type="checkbox" name="post_category[]" id="in-category-', $category['cat_ID'], '"', ($category['checked'] ? ' checked="checked"' : "" ), '/> ', wp_specialchars( apply_filters('the_category', $category['cat_name'] )), "</label></li>";

		if ( $category['children'] ) {
			echo "<ul>\n";
			write_nested_categories( $category['children'] );
			echo "</ul>\n";
		}
	}
}

function dropdown_categories( $default = 0 ) {
	write_nested_categories( get_nested_categories( $default) );
}

function dropdown_link_categories( $default = 0 ) {
	global $link_id;

	if ( $link_id ) {
		$checked_categories = wp_get_link_cats($link_id);

		if ( count( $checked_categories ) == 0 ) {
			// No selected categories, strange
			$checked_categories[] = $default;
		}
	} else {
		$checked_categories[] = $default;
	}

	$categories = get_terms('link_category', 'orderby=count&hide_empty=0');

	if ( empty($categories) )
		return;

	foreach ( $categories as $category ) {
		$cat_id = $category->term_id;
		$name = wp_specialchars( apply_filters('the_category', $category->name));
		$checked = in_array( $cat_id, $checked_categories );
		echo '<li id="link-category-', $cat_id, '"><label for="in-link-category-', $cat_id, '" class="selectit"><input value="', $cat_id, '" type="checkbox" name="link_category[]" id="in-link-category-', $cat_id, '"', ($checked ? ' checked="checked"' : "" ), '/> ', $name, "</label></li>";
	}
}

function page_rows( $parent = 0, $level = 0, $pages = 0, $hierarchy = true ) {
	global $wpdb, $class, $post;

	if (!$pages )
		$pages = get_pages( 'sort_column=menu_order' );

	if (! $pages )
		return false;

	foreach ( $pages as $post) {
		setup_postdata( $post);
		if ( $hierarchy && ($post->post_parent != $parent) )
			continue;

		$post->post_title = wp_specialchars( $post->post_title );
		$pad = str_repeat( '&#8212; ', $level );
		$id = (int) $post->ID;
		$class = ('alternate' == $class ) ? '' : 'alternate';
?>
  <tr id='page-<?php echo $id; ?>' class='<?php echo $class; ?>'>
    <th scope="row" style="text-align: center"><?php echo $post->ID; ?></th>
    <td>
      <?php echo $pad; ?><?php the_title() ?>
    </td>
    <td><?php the_author() ?></td>
    <td><?php if ( '0000-00-00 00:00:00' ==$post->post_modified ) _e('Unpublished'); else echo mysql2date( __('Y-m-d g:i a'), $post->post_modified ); ?></td>
	<td><a href="<?php the_permalink(); ?>" rel="permalink" class="view"><?php _e( 'View' ); ?></a></td>
    <td><?php if ( current_user_can( 'edit_page', $id ) ) { echo "<a href='page.php?action=edit&amp;post=$id' class='edit'>" . __( 'Edit' ) . "</a>"; } ?></td>
    <td><?php if ( current_user_can( 'delete_page', $id ) ) { echo "<a href='" . wp_nonce_url( "page.php?action=delete&amp;post=$id", 'delete-page_' . $id ) .  "' class='delete' onclick=\"return deleteSomething( 'page', " . $id . ", '" . js_escape(sprintf( __("You are about to delete the '%s' page.\n'OK' to delete, 'Cancel' to stop." ), get_the_title() ) ) . "' );\">" . __( 'Delete' ) . "</a>"; } ?></td>
  </tr>

<?php
		if ( $hierarchy ) page_rows( $id, $level + 1, $pages );
	}
}

function user_row( $user_object, $style = '' ) {
	if ( !(is_object( $user_object) && is_a( $user_object, 'WP_User' ) ) )
		$user_object = new WP_User( (int) $user_object );
	$email = $user_object->user_email;
	$url = $user_object->user_url;
	$short_url = str_replace( 'http://', '', $url );
	$short_url = str_replace( 'www.', '', $short_url );
	if ('/' == substr( $short_url, -1 ))
		$short_url = substr( $short_url, 0, -1 );
	if ( strlen( $short_url ) > 35 )
		$short_url =  substr( $short_url, 0, 32 ).'...';
	$numposts = get_usernumposts( $user_object->ID );
	$r = "<tr id='user-$user_object->ID'$style>
		<td><input type='checkbox' name='users[]' id='user_{$user_object->ID}' value='{$user_object->ID}' /> <label for='user_{$user_object->ID}'>{$user_object->ID}</label></td>
		<td><label for='user_{$user_object->ID}'><strong>$user_object->user_login</strong></label></td>
		<td><label for='user_{$user_object->ID}'>$user_object->first_name $user_object->last_name</label></td>
		<td><a href='mailto:$email' title='" . sprintf( __('e-mail: %s' ), $email ) . "'>$email</a></td>
		<td><a href='$url' title='website: $url'>$short_url</a></td>";
	$r .= "\n\t\t<td align='center'>";
	if ( $numposts > 0 ) {
		$r .= "<a href='edit.php?author=$user_object->ID' title='" . __( 'View posts by this author' ) . "' class='edit'>";
		$r .= sprintf(__ngettext( 'View %s post', 'View %s posts', $numposts ), $numposts);
		$r .= '</a>';
	}
	$r .= "</td>\n\t\t<td>";
	if ( current_user_can( 'edit_user', $user_object->ID ) ) {
		$edit_link = add_query_arg( 'wp_http_referer', urlencode( clean_url( stripslashes( $_SERVER['REQUEST_URI'] ) ) ), "user-edit.php?user_id=$user_object->ID" );
		$r .= "<a href='$edit_link' class='edit'>".__( 'Edit' )."</a>";
	}
	$r .= "</td>\n\t</tr>";
	return $r;
}

function _wp_get_comment_list( $s = false, $start, $num ) {
	global $wpdb;

	$start = abs( (int) $start );
	$num = (int) $num;

	if ( $s ) {
		$s = $wpdb->escape($s);
		$comments = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->comments WHERE
			(comment_author LIKE '%$s%' OR
			comment_author_email LIKE '%$s%' OR
			comment_author_url LIKE ('%$s%') OR
			comment_author_IP LIKE ('%$s%') OR
			comment_content LIKE ('%$s%') ) AND
			comment_approved != 'spam'
			ORDER BY comment_date DESC LIMIT $start, $num");
	} else {
		$comments = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->comments WHERE comment_approved = '0' OR comment_approved = '1' ORDER BY comment_date DESC LIMIT $start, $num" );
	}

	update_comment_cache($comments);

	$total = $wpdb->get_var( "SELECT FOUND_ROWS()" );

	return array($comments, $total);
}

function _wp_comment_list_item( $id, $alt = 0 ) {
	global $authordata, $comment, $wpdb;
	$id = (int) $id;
	$comment =& get_comment( $id );
	$class = '';
	$post = get_post($comment->comment_post_ID);
	$authordata = get_userdata($post->post_author);
	$comment_status = wp_get_comment_status($comment->comment_ID);
	if ( 'unapproved' == $comment_status )
		$class .= ' unapproved';
	if ( $alt % 2 )
		$class .= ' alternate';
	echo "<li id='comment-$comment->comment_ID' class='$class'>";
?>
<p><strong><?php comment_author(); ?></strong> <?php if ($comment->comment_author_email) { ?>| <?php comment_author_email_link() ?> <?php } if ($comment->comment_author_url && 'http://' != $comment->comment_author_url) { ?> | <?php comment_author_url_link() ?> <?php } ?>| <?php _e('IP:') ?> <a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php comment_author_IP() ?>"><?php comment_author_IP() ?></a></p>

<?php comment_text() ?>

<p><?php comment_date(__('M j, g:i A'));  ?> &#8212; [
<?php
if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
	echo " <a href='comment.php?action=editcomment&amp;c=".$comment->comment_ID."'>" .  __('Edit') . '</a>';
	echo ' | <a href="' . wp_nonce_url('comment.php?action=deletecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID) . '" onclick="return deleteSomething( \'comment\', ' . $comment->comment_ID . ', \'' . js_escape(sprintf(__("You are about to delete this comment by '%s'.\n'Cancel' to stop, 'OK' to delete."), $comment->comment_author)) . "', theCommentList );\">" . __('Delete') . '</a> ';
	if ( ('none' != $comment_status) && ( current_user_can('moderate_comments') ) ) {
		echo '<span class="unapprove"> | <a href="' . wp_nonce_url('comment.php?action=unapprovecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'unapprove-comment_' . $comment->comment_ID) . '" onclick="return dimSomething( \'comment\', ' . $comment->comment_ID . ', \'unapproved\', theCommentList );">' . __('Unapprove') . '</a> </span>';
		echo '<span class="approve"> | <a href="' . wp_nonce_url('comment.php?action=approvecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'approve-comment_' . $comment->comment_ID) . '" onclick="return dimSomething( \'comment\', ' . $comment->comment_ID . ', \'unapproved\', theCommentList );">' . __('Approve') . '</a> </span>';
	}
	echo " | <a href=\"" . wp_nonce_url("comment.php?action=deletecomment&amp;dt=spam&amp;p=" . $comment->comment_post_ID . "&amp;c=" . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID) . "\" onclick=\"return deleteSomething( 'comment-as-spam', $comment->comment_ID, '" . js_escape(sprintf(__("You are about to mark as spam this comment by '%s'.\n'Cancel' to stop, 'OK' to mark as spam."), $comment->comment_author))  . "', theCommentList );\">" . __('Spam') . "</a> ";
}
$post = get_post($comment->comment_post_ID, OBJECT, 'display');
$post_title = wp_specialchars( $post->post_title, 'double' );
$post_title = ('' == $post_title) ? "# $comment->comment_post_ID" : $post_title;
?>
 ] &#8212; <a href="<?php echo get_permalink($comment->comment_post_ID); ?>"><?php echo $post_title; ?></a></p>
		</li>
<?php
}

function wp_dropdown_cats( $currentcat = 0, $currentparent = 0, $parent = 0, $level = 0, $categories = 0 ) {
	global $wpdb;
	if (!$categories )
		$categories = get_categories( 'hide_empty=0' );

	if ( $categories ) {
		foreach ( $categories as $category ) {
			if ( $currentcat != $category->term_id && $parent == $category->parent) {
				$pad = str_repeat( '&#8211; ', $level );
				$category->name = wp_specialchars( $category->name );
				echo "\n\t<option value='$category->term_id'";
				if ( $currentparent == $category->term_id )
					echo " selected='selected'";
				echo ">$pad$category->name</option>";
				wp_dropdown_cats( $currentcat, $currentparent, $category->term_id, $level +1, $categories );
			}
		}
	} else {
		return false;
	}
}

function list_meta( $meta ) {
	global $post_ID;
	// Exit if no meta
	if (!$meta ) {
		echo '<tbody id="the-list"><tr style="display: none;"><td>&nbsp;</td></tr></tbody>'; //TBODY needed for list-manipulation JS
		return;
	}
	$count = 0;
?>
	<thead>
	<tr>
		<th><?php _e( 'Key' ) ?></th>
		<th><?php _e( 'Value' ) ?></th>
		<th colspan='2'><?php _e( 'Action' ) ?></th>
	</tr>
	</thead>
<?php
	$r ="\n\t<tbody id='the-list'>";
	foreach ( $meta as $entry ) {
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
				continue;
			}
		}

		$key_js = js_escape( $entry['meta_key'] );
		$entry['meta_key']   = attribute_escape($entry['meta_key']);
		$entry['meta_value'] = attribute_escape($entry['meta_value']);
		$entry['meta_id'] = (int) $entry['meta_id'];
		$r .= "\n\t<tr id='meta-{$entry['meta_id']}' class='$style'>";
		$r .= "\n\t\t<td valign='top'><input name='meta[{$entry['meta_id']}][key]' tabindex='6' type='text' size='20' value='{$entry['meta_key']}' /></td>";
		$r .= "\n\t\t<td><textarea name='meta[{$entry['meta_id']}][value]' tabindex='6' rows='2' cols='30'>{$entry['meta_value']}</textarea></td>";
		$r .= "\n\t\t<td align='center'><input name='updatemeta' type='submit' class='updatemeta' tabindex='6' value='".attribute_escape(__( 'Update' ))."' /><br />";
		$r .= "\n\t\t<input name='deletemeta[{$entry['meta_id']}]' type='submit' onclick=\"return deleteSomething( 'meta', {$entry['meta_id']}, '";
		$r .= js_escape(sprintf( __("You are about to delete the '%s' custom field on this post.\n'OK' to delete, 'Cancel' to stop." ), $key_js ) );
		$r .= "' );\" class='deletemeta' tabindex='6' value='".attribute_escape(__( 'Delete' ))."' /></td>";
		$r .= "\n\t</tr>";
	}
	echo $r;
	echo "\n\t</tbody>";
}

function meta_form() {
	global $wpdb;
	$limit = (int) apply_filters( 'postmeta_form_limit', 30 );
	$keys = $wpdb->get_col( "
		SELECT meta_key
		FROM $wpdb->postmeta
		WHERE meta_key NOT LIKE '\_%'
		GROUP BY meta_key
		ORDER BY meta_id DESC
		LIMIT $limit" );
	if ( $keys )
		natcasesort($keys);
?>
<h3><?php _e( 'Add a new custom field:' ) ?></h3>
<table id="newmeta" cellspacing="3" cellpadding="3">
	<tr>
<th colspan="2"><?php _e( 'Key' ) ?></th>
<th><?php _e( 'Value' ) ?></th>
</tr>
	<tr valign="top">
		<td align="right" width="18%">
<?php if ( $keys ) : ?>
<select id="metakeyselect" name="metakeyselect" tabindex="7">
<option value="#NONE#"><?php _e( '- Select -' ); ?></option>
<?php

	foreach ( $keys as $key ) {
		$key = attribute_escape( $key );
		echo "\n\t<option value='$key'>$key</option>";
	}
?>
</select> <?php _e( 'or' ); ?>
<?php endif; ?>
</td>
<td><input type="text" id="metakeyinput" name="metakeyinput" tabindex="7" /></td>
		<td><textarea id="metavalue" name="metavalue" rows="3" cols="25" tabindex="8"></textarea></td>
	</tr>

</table>
<p class="submit"><input type="submit" id="updatemetasub" name="updatemeta" tabindex="9" value="<?php _e( 'Add Custom Field &raquo;' ) ?>" /></p>
<?php

}

function touch_time( $edit = 1, $for_post = 1, $tab_index = 0 ) {
	global $wp_locale, $post, $comment;

	if ( $for_post )
		$edit = ( in_array($post->post_status, array('draft', 'pending') ) && (!$post->post_date || '0000-00-00 00:00:00' == $post->post_date ) ) ? false : true;
	
	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 )
		$tab_index_attribute = " tabindex=\"$tab_index\"";

	echo '<fieldset><legend><input type="checkbox" class="checkbox" name="edit_date" value="1" id="timestamp"'.$tab_index_attribute.' /> <label for="timestamp">'.__( 'Edit timestamp' ).'</label></legend>';

	$time_adj = time() + (get_option( 'gmt_offset' ) * 3600 );
	$post_date = ($for_post) ? $post->post_date : $comment->comment_date;
	$jj = ($edit) ? mysql2date( 'd', $post_date ) : gmdate( 'd', $time_adj );
	$mm = ($edit) ? mysql2date( 'm', $post_date ) : gmdate( 'm', $time_adj );
	$aa = ($edit) ? mysql2date( 'Y', $post_date ) : gmdate( 'Y', $time_adj );
	$hh = ($edit) ? mysql2date( 'H', $post_date ) : gmdate( 'H', $time_adj );
	$mn = ($edit) ? mysql2date( 'i', $post_date ) : gmdate( 'i', $time_adj );
	$ss = ($edit) ? mysql2date( 's', $post_date ) : gmdate( 's', $time_adj );

	echo "<select name=\"mm\" onchange=\"edit_date.checked=true\"$tab_index_attribute>\n";
	for ( $i = 1; $i < 13; $i = $i +1 ) {
		echo "\t\t\t<option value=\"$i\"";
		if ( $i == $mm )
			echo ' selected="selected"';
		echo '>' . $wp_locale->get_month( $i ) . "</option>\n";
	}
?>
</select>
<input type="text" id="jj" name="jj" value="<?php echo $jj; ?>" size="2" maxlength="2" onchange="edit_date.checked=true"<?php echo $tab_index_attribute ?> />
<input type="text" id="aa" name="aa" value="<?php echo $aa ?>" size="4" maxlength="5" onchange="edit_date.checked=true"<?php echo $tab_index_attribute ?> /> @
<input type="text" id="hh" name="hh" value="<?php echo $hh ?>" size="2" maxlength="2" onchange="edit_date.checked=true"<?php echo $tab_index_attribute ?> /> :
<input type="text" id="mn" name="mn" value="<?php echo $mn ?>" size="2" maxlength="2" onchange="edit_date.checked=true"<?php echo $tab_index_attribute ?> />
<input type="hidden" id="ss" name="ss" value="<?php echo $ss ?>" size="2" maxlength="2" onchange="edit_date.checked=true" />
<?php
	if ( $edit ) {
		printf( _c( 'Existing timestamp: %1$s %2$s, %3$s @ %4$s:%5$s|1: month, 2: month string, 3: full year, 4: hours, 5: minutes' ), $wp_locale->get_month( $mm ), $jj, $aa, $hh, $mn );
	}
?>
</fieldset>
	<?php

}

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

function parent_dropdown( $default = 0, $parent = 0, $level = 0 ) {
	global $wpdb, $post_ID;
	$items = $wpdb->get_results( "SELECT ID, post_parent, post_title FROM $wpdb->posts WHERE post_parent = $parent AND post_type = 'page' ORDER BY menu_order" );

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

			echo "\n\t<option value='$item->ID'$current>$pad " . wp_specialchars($item->post_title) . "</option>";
			parent_dropdown( $default, $item->ID, $level +1 );
		}
	} else {
		return false;
	}
}

function browse_happy() {
	$getit = __( 'WordPress recommends a better browser' );
	echo '
		<p id="bh" style="text-align: center;"><a href="http://browsehappy.com/" title="'.$getit.'"><img src="images/browse-happy.gif" alt="Browse Happy" /></a></p>
		';
}

if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
	add_action( 'admin_footer', 'browse_happy' );

function the_attachment_links( $id = false ) {
	$id = (int) $id;
	$post = & get_post( $id );

	if ( $post->post_type != 'attachment' )
		return false;

	$icon = get_attachment_icon( $post->ID );
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
		<td><textarea rows="1" cols="40" type="text" class="attachmentlinks" readonly="readonly"><a href="<?php echo wp_get_attachment_url(); ?>" class="attachmentlink"><?php echo basename( wp_get_attachment_url() );  ?></a></textarea></td>
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

function wp_dropdown_roles( $default = false ) {
	global $wp_roles;
	$r = '';
	foreach( $wp_roles->role_names as $role => $name )
		if ( $default == $role ) // Make default first in list
			$p = "\n\t<option selected='selected' value='$role'>$name</option>";
		else
			$r .= "\n\t<option value='$role'>$name</option>";
	echo $p . $r;
}

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

function wp_convert_bytes_to_hr( $bytes ) {
	$units = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
	$log = log( $bytes, 1024 );
	$power = (int) $log;
	$size = pow(1024, $log - $power);
	return $size . $units[$power];
}

function wp_import_upload_form( $action ) {
	$u_bytes = wp_convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) );
	$p_bytes = wp_convert_hr_to_bytes( ini_get( 'post_max_size' ) );
	$bytes = apply_filters( 'import_upload_size_limit', min($u_bytes, $p_bytes), $u_bytes, $p_bytes );
	$size = wp_convert_bytes_to_hr( $bytes );
?>
<form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo attribute_escape($action) ?>">
<p>
<?php wp_nonce_field('import-upload'); ?>
<label for="upload"><?php _e( 'Choose a file from your computer:' ); ?></label> (<?php printf( __('Maximum size: %s' ), $size ); ?>)
<input type="file" id="upload" name="import" size="25" />
<input type="hidden" name="action" value="save" />
<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
</p>
<p class="submit">
<input type="submit" value="<?php _e( 'Upload file and import &raquo;' ); ?>" />
</p>
</form>
<?php
}

function wp_remember_old_slug() {
	global $post;
	$name = attribute_escape($post->post_name); // just in case
	if ( strlen($name) )
		echo '<input type="hidden" id="wp-old-slug" name="wp-old-slug" value="' . $name . '" />';
}

?>
