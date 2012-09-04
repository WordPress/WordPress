<?php

// -- Post related Meta Boxes

/**
 * Display post submit form fields.
 *
 * @since 2.7.0
 *
 * @param object $post
 */
function post_submit_meta_box($post) {
	global $action;

	$post_type = $post->post_type;
	$post_type_object = get_post_type_object($post_type);
	$can_publish = current_user_can($post_type_object->cap->publish_posts);
?>
<div class="submitbox" id="submitpost">

<div id="minor-publishing">

<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
<div style="display:none;">
<?php submit_button( __( 'Save' ), 'button', 'save' ); ?>
</div>

<div id="minor-publishing-actions">
<div id="save-action">
<?php if ( 'publish' != $post->post_status && 'future' != $post->post_status && 'pending' != $post->post_status ) { ?>
<input <?php if ( 'private' == $post->post_status ) { ?>style="display:none"<?php } ?> type="submit" name="save" id="save-post" value="<?php esc_attr_e('Save Draft'); ?>" tabindex="4" class="button button-highlighted" />
<?php } elseif ( 'pending' == $post->post_status && $can_publish ) { ?>
<input type="submit" name="save" id="save-post" value="<?php esc_attr_e('Save as Pending'); ?>" tabindex="4" class="button button-highlighted" />
<?php } ?>
<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-loading" id="draft-ajax-loading" alt="" />
</div>
<?php if ( $post_type_object->public ) : ?>
<div id="preview-action">
<?php
if ( 'publish' == $post->post_status ) {
	$preview_link = esc_url( get_permalink( $post->ID ) );
	$preview_button = __( 'Preview Changes' );
} else {
	$preview_link = get_permalink( $post->ID );
	if ( is_ssl() )
		$preview_link = str_replace( 'http://', 'https://', $preview_link );
	$preview_link = esc_url( apply_filters( 'preview_post_link', add_query_arg( 'preview', 'true', $preview_link ) ) );
	$preview_button = __( 'Preview' );
}
?>
<a class="preview button" href="<?php echo $preview_link; ?>" target="wp-preview" id="post-preview" tabindex="4"><?php echo $preview_button; ?></a>
<input type="hidden" name="wp-preview" id="wp-preview" value="" />
</div>
<?php endif; // public post type ?>
<div class="clear"></div>
</div><?php // /minor-publishing-actions ?>

<div id="misc-publishing-actions">

<div class="misc-pub-section"><label for="post_status"><?php _e('Status:') ?></label>
<span id="post-status-display">
<?php
switch ( $post->post_status ) {
	case 'private':
		_e('Privately Published');
		break;
	case 'publish':
		_e('Published');
		break;
	case 'future':
		_e('Scheduled');
		break;
	case 'pending':
		_e('Pending Review');
		break;
	case 'draft':
	case 'auto-draft':
		_e('Draft');
		break;
}
?>
</span>
<?php if ( 'publish' == $post->post_status || 'private' == $post->post_status || $can_publish ) { ?>
<a href="#post_status" <?php if ( 'private' == $post->post_status ) { ?>style="display:none;" <?php } ?>class="edit-post-status hide-if-no-js" tabindex='4'><?php _e('Edit') ?></a>

<div id="post-status-select" class="hide-if-js">
<input type="hidden" name="hidden_post_status" id="hidden_post_status" value="<?php echo esc_attr( ('auto-draft' == $post->post_status ) ? 'draft' : $post->post_status); ?>" />
<select name='post_status' id='post_status' tabindex='4'>
<?php if ( 'publish' == $post->post_status ) : ?>
<option<?php selected( $post->post_status, 'publish' ); ?> value='publish'><?php _e('Published') ?></option>
<?php elseif ( 'private' == $post->post_status ) : ?>
<option<?php selected( $post->post_status, 'private' ); ?> value='publish'><?php _e('Privately Published') ?></option>
<?php elseif ( 'future' == $post->post_status ) : ?>
<option<?php selected( $post->post_status, 'future' ); ?> value='future'><?php _e('Scheduled') ?></option>
<?php endif; ?>
<option<?php selected( $post->post_status, 'pending' ); ?> value='pending'><?php _e('Pending Review') ?></option>
<?php if ( 'auto-draft' == $post->post_status ) : ?>
<option<?php selected( $post->post_status, 'auto-draft' ); ?> value='draft'><?php _e('Draft') ?></option>
<?php else : ?>
<option<?php selected( $post->post_status, 'draft' ); ?> value='draft'><?php _e('Draft') ?></option>
<?php endif; ?>
</select>
 <a href="#post_status" class="save-post-status hide-if-no-js button"><?php _e('OK'); ?></a>
 <a href="#post_status" class="cancel-post-status hide-if-no-js"><?php _e('Cancel'); ?></a>
</div>

<?php } ?>
</div><?php // /misc-pub-section ?>

<div class="misc-pub-section" id="visibility">
<?php _e('Visibility:'); ?> <span id="post-visibility-display"><?php

if ( 'private' == $post->post_status ) {
	$post->post_password = '';
	$visibility = 'private';
	$visibility_trans = __('Private');
} elseif ( !empty( $post->post_password ) ) {
	$visibility = 'password';
	$visibility_trans = __('Password protected');
} elseif ( $post_type == 'post' && is_sticky( $post->ID ) ) {
	$visibility = 'public';
	$visibility_trans = __('Public, Sticky');
} else {
	$visibility = 'public';
	$visibility_trans = __('Public');
}

echo esc_html( $visibility_trans ); ?></span>
<?php if ( $can_publish ) { ?>
<a href="#visibility" class="edit-visibility hide-if-no-js"><?php _e('Edit'); ?></a>

<div id="post-visibility-select" class="hide-if-js">
<input type="hidden" name="hidden_post_password" id="hidden-post-password" value="<?php echo esc_attr($post->post_password); ?>" />
<?php if ($post_type == 'post'): ?>
<input type="checkbox" style="display:none" name="hidden_post_sticky" id="hidden-post-sticky" value="sticky" <?php checked(is_sticky($post->ID)); ?> />
<?php endif; ?>
<input type="hidden" name="hidden_post_visibility" id="hidden-post-visibility" value="<?php echo esc_attr( $visibility ); ?>" />
<input type="radio" name="visibility" id="visibility-radio-public" value="public" <?php checked( $visibility, 'public' ); ?> /> <label for="visibility-radio-public" class="selectit"><?php _e('Public'); ?></label><br />
<?php if ( $post_type == 'post' && current_user_can( 'edit_others_posts' ) ) : ?>
<span id="sticky-span"><input id="sticky" name="sticky" type="checkbox" value="sticky" <?php checked( is_sticky( $post->ID ) ); ?> tabindex="4" /> <label for="sticky" class="selectit"><?php _e( 'Stick this post to the front page' ); ?></label><br /></span>
<?php endif; ?>
<input type="radio" name="visibility" id="visibility-radio-password" value="password" <?php checked( $visibility, 'password' ); ?> /> <label for="visibility-radio-password" class="selectit"><?php _e('Password protected'); ?></label><br />
<span id="password-span"><label for="post_password"><?php _e('Password:'); ?></label> <input type="text" name="post_password" id="post_password" value="<?php echo esc_attr($post->post_password); ?>" /><br /></span>
<input type="radio" name="visibility" id="visibility-radio-private" value="private" <?php checked( $visibility, 'private' ); ?> /> <label for="visibility-radio-private" class="selectit"><?php _e('Private'); ?></label><br />

<p>
 <a href="#visibility" class="save-post-visibility hide-if-no-js button"><?php _e('OK'); ?></a>
 <a href="#visibility" class="cancel-post-visibility hide-if-no-js"><?php _e('Cancel'); ?></a>
</p>
</div>
<?php } ?>

</div><?php // /misc-pub-section ?>

<?php
// translators: Publish box date format, see http://php.net/date
$datef = __( 'M j, Y @ G:i' );
if ( 0 != $post->ID ) {
	if ( 'future' == $post->post_status ) { // scheduled for publishing at a future date
		$stamp = __('Scheduled for: <b>%1$s</b>');
	} else if ( 'publish' == $post->post_status || 'private' == $post->post_status ) { // already published
		$stamp = __('Published on: <b>%1$s</b>');
	} else if ( '0000-00-00 00:00:00' == $post->post_date_gmt ) { // draft, 1 or more saves, no date specified
		$stamp = __('Publish <b>immediately</b>');
	} else if ( time() < strtotime( $post->post_date_gmt . ' +0000' ) ) { // draft, 1 or more saves, future date specified
		$stamp = __('Schedule for: <b>%1$s</b>');
	} else { // draft, 1 or more saves, date specified
		$stamp = __('Publish on: <b>%1$s</b>');
	}
	$date = date_i18n( $datef, strtotime( $post->post_date ) );
} else { // draft (no saves, and thus no date specified)
	$stamp = __('Publish <b>immediately</b>');
	$date = date_i18n( $datef, strtotime( current_time('mysql') ) );
}

if ( $can_publish ) : // Contributors don't get to choose the date of publish ?>
<div class="misc-pub-section curtime">
	<span id="timestamp">
	<?php printf($stamp, $date); ?></span>
	<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" tabindex='4'><?php _e('Edit') ?></a>
	<div id="timestampdiv" class="hide-if-js"><?php touch_time(($action == 'edit'),1,4); ?></div>
</div><?php // /misc-pub-section ?>
<?php endif; ?>

<?php do_action('post_submitbox_misc_actions'); ?>
</div>
<div class="clear"></div>
</div>

<div id="major-publishing-actions">
<?php do_action('post_submitbox_start'); ?>
<div id="delete-action">
<?php
if ( current_user_can( "delete_post", $post->ID ) ) {
	if ( !EMPTY_TRASH_DAYS )
		$delete_text = __('Delete Permanently');
	else
		$delete_text = __('Move to Trash');
	?>
<a class="submitdelete deletion" href="<?php echo get_delete_post_link($post->ID); ?>"><?php echo $delete_text; ?></a><?php
} ?>
</div>

<div id="publishing-action">
<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-loading" id="ajax-loading" alt="" />
<?php
if ( !in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ) {
	if ( $can_publish ) :
		if ( !empty($post->post_date_gmt) && time() < strtotime( $post->post_date_gmt . ' +0000' ) ) : ?>
		<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Schedule') ?>" />
		<?php submit_button( __( 'Schedule' ), 'primary', 'publish', false, array( 'tabindex' => '5', 'accesskey' => 'p' ) ); ?>
<?php	else : ?>
		<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Publish') ?>" />
		<?php submit_button( __( 'Publish' ), 'primary', 'publish', false, array( 'tabindex' => '5', 'accesskey' => 'p' ) ); ?>
<?php	endif;
	else : ?>
		<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Submit for Review') ?>" />
		<?php submit_button( __( 'Submit for Review' ), 'primary', 'publish', false, array( 'tabindex' => '5', 'accesskey' => 'p' ) ); ?>
<?php
	endif;
} else { ?>
		<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Update') ?>" />
		<input name="save" type="submit" class="button-primary" id="publish" tabindex="5" accesskey="p" value="<?php esc_attr_e('Update') ?>" />
<?php
} ?>
</div>
<div class="clear"></div>
</div>
</div>

<?php
}

/**
 * Display post format form elements.
 *
 * @since 3.1.0
 *
 * @param object $post
 */
function post_format_meta_box( $post, $box ) {
	if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post->post_type, 'post-formats' ) ) :
	$post_formats = get_theme_support( 'post-formats' );

	if ( is_array( $post_formats[0] ) ) :
		$post_format = get_post_format( $post->ID );
		if ( !$post_format )
			$post_format = '0';
		// Add in the current one if it isn't there yet, in case the current theme doesn't support it
		if ( $post_format && !in_array( $post_format, $post_formats[0] ) )
			$post_formats[0][] = $post_format;
	?>
	<div id="post-formats-select">
		<input type="radio" name="post_format" class="post-format" id="post-format-0" value="0" <?php checked( $post_format, '0' ); ?> /> <label for="post-format-0"><?php _e('Standard'); ?></label>
		<?php foreach ( $post_formats[0] as $format ) : ?>
		<br /><input type="radio" name="post_format" class="post-format" id="post-format-<?php echo esc_attr( $format ); ?>" value="<?php echo esc_attr( $format ); ?>" <?php checked( $post_format, $format ); ?> /> <label for="post-format-<?php echo esc_attr( $format ); ?>"><?php echo esc_html( get_post_format_string( $format ) ); ?></label>
		<?php endforeach; ?><br />
	</div>
	<?php endif; endif;
}

/**
 * Display post tags form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function post_tags_meta_box($post, $box) {
	$defaults = array('taxonomy' => 'post_tag');
	if ( !isset($box['args']) || !is_array($box['args']) )
		$args = array();
	else
		$args = $box['args'];
	extract( wp_parse_args($args, $defaults), EXTR_SKIP );
	$tax_name = esc_attr($taxonomy);
	$taxonomy = get_taxonomy($taxonomy);
	$disabled = !current_user_can($taxonomy->cap->assign_terms) ? 'disabled="disabled"' : '';
	$comma = _x( ',', 'tag delimiter' );
?>
<div class="tagsdiv" id="<?php echo $tax_name; ?>">
	<div class="jaxtag">
	<div class="nojs-tags hide-if-js">
	<p><?php echo $taxonomy->labels->add_or_remove_items; ?></p>
	<textarea name="<?php echo "tax_input[$tax_name]"; ?>" rows="3" cols="20" class="the-tags" id="tax-input-<?php echo $tax_name; ?>" <?php echo $disabled; ?>><?php echo str_replace( ',', $comma . ' ', get_terms_to_edit( $post->ID, $tax_name ) ); // textarea_escaped by esc_attr() ?></textarea></div>
 	<?php if ( current_user_can($taxonomy->cap->assign_terms) ) : ?>
	<div class="ajaxtag hide-if-no-js">
		<label class="screen-reader-text" for="new-tag-<?php echo $tax_name; ?>"><?php echo $box['title']; ?></label>
		<div class="taghint"><?php echo $taxonomy->labels->add_new_item; ?></div>
		<p><input type="text" id="new-tag-<?php echo $tax_name; ?>" name="newtag[<?php echo $tax_name; ?>]" class="newtag form-input-tip" size="16" autocomplete="off" value="" />
		<input type="button" class="button tagadd" value="<?php esc_attr_e('Add'); ?>" tabindex="3" /></p>
	</div>
	<p class="howto"><?php echo esc_attr( $taxonomy->labels->separate_items_with_commas ); ?></p>
	<?php endif; ?>
	</div>
	<div class="tagchecklist"></div>
</div>
<?php if ( current_user_can($taxonomy->cap->assign_terms) ) : ?>
<p class="hide-if-no-js"><a href="#titlediv" class="tagcloud-link" id="link-<?php echo $tax_name; ?>"><?php echo $taxonomy->labels->choose_from_most_used; ?></a></p>
<?php endif; ?>
<?php
}

/**
 * Display post categories form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function post_categories_meta_box( $post, $box ) {
	$defaults = array('taxonomy' => 'category');
	if ( !isset($box['args']) || !is_array($box['args']) )
		$args = array();
	else
		$args = $box['args'];
	extract( wp_parse_args($args, $defaults), EXTR_SKIP );
	$tax = get_taxonomy($taxonomy);

	?>
	<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
		<ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
			<li class="tabs"><a href="#<?php echo $taxonomy; ?>-all" tabindex="3"><?php echo $tax->labels->all_items; ?></a></li>
			<li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-pop" tabindex="3"><?php _e( 'Most Used' ); ?></a></li>
		</ul>

		<div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
			<ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
				<?php $popular_ids = wp_popular_terms_checklist($taxonomy); ?>
			</ul>
		</div>

		<div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
			<?php
            $name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
            echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
            ?>
			<ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
				<?php wp_terms_checklist($post->ID, array( 'taxonomy' => $taxonomy, 'popular_cats' => $popular_ids ) ) ?>
			</ul>
		</div>
	<?php if ( current_user_can($tax->cap->edit_terms) ) : ?>
			<div id="<?php echo $taxonomy; ?>-adder" class="wp-hidden-children">
				<h4>
					<a id="<?php echo $taxonomy; ?>-add-toggle" href="#<?php echo $taxonomy; ?>-add" class="hide-if-no-js" tabindex="3">
						<?php
							/* translators: %s: add new taxonomy label */
							printf( __( '+ %s' ), $tax->labels->add_new_item );
						?>
					</a>
				</h4>
				<p id="<?php echo $taxonomy; ?>-add" class="category-add wp-hidden-child">
					<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>"><?php echo $tax->labels->add_new_item; ?></label>
					<input type="text" name="new<?php echo $taxonomy; ?>" id="new<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="<?php echo esc_attr( $tax->labels->new_item_name ); ?>" tabindex="3" aria-required="true"/>
					<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>_parent">
						<?php echo $tax->labels->parent_item_colon; ?>
					</label>
					<?php wp_dropdown_categories( array( 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'name' => 'new'.$taxonomy.'_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => '&mdash; ' . $tax->labels->parent_item . ' &mdash;', 'tab_index' => 3 ) ); ?>
					<input type="button" id="<?php echo $taxonomy; ?>-add-submit" class="add:<?php echo $taxonomy ?>checklist:<?php echo $taxonomy ?>-add button category-add-submit" value="<?php echo esc_attr( $tax->labels->add_new_item ); ?>" tabindex="3" />
					<?php wp_nonce_field( 'add-'.$taxonomy, '_ajax_nonce-add-'.$taxonomy, false ); ?>
					<span id="<?php echo $taxonomy; ?>-ajax-response"></span>
				</p>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Display post excerpt form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function post_excerpt_meta_box($post) {
?>
<label class="screen-reader-text" for="excerpt"><?php _e('Excerpt') ?></label><textarea rows="1" cols="40" name="excerpt" tabindex="6" id="excerpt"><?php echo $post->post_excerpt; // textarea_escaped ?></textarea>
<p><?php _e('Excerpts are optional hand-crafted summaries of your content that can be used in your theme. <a href="http://codex.wordpress.org/Excerpt" target="_blank">Learn more about manual excerpts.</a>'); ?></p>
<?php
}

/**
 * Display trackback links form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function post_trackback_meta_box($post) {
	$form_trackback = '<input type="text" name="trackback_url" id="trackback_url" class="code" tabindex="7" value="'. esc_attr( str_replace("\n", ' ', $post->to_ping) ) .'" />';
	if ('' != $post->pinged) {
		$pings = '<p>'. __('Already pinged:') . '</p><ul>';
		$already_pinged = explode("\n", trim($post->pinged));
		foreach ($already_pinged as $pinged_url) {
			$pings .= "\n\t<li>" . esc_html($pinged_url) . "</li>";
		}
		$pings .= '</ul>';
	}

?>
<p><label for="trackback_url"><?php _e('Send trackbacks to:'); ?></label> <?php echo $form_trackback; ?><br /> (<?php _e('Separate multiple URLs with spaces'); ?>)</p>
<p><?php _e('Trackbacks are a way to notify legacy blog systems that you&#8217;ve linked to them. If you link other WordPress sites they&#8217;ll be notified automatically using <a href="http://codex.wordpress.org/Introduction_to_Blogging#Managing_Comments" target="_blank">pingbacks</a>, no other action necessary.'); ?></p>
<?php
if ( ! empty($pings) )
	echo $pings;
}

/**
 * Display custom fields form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function post_custom_meta_box($post) {
?>
<div id="postcustomstuff">
<div id="ajax-response"></div>
<?php
$metadata = has_meta($post->ID);
foreach ( $metadata as $key => $value ) {
	if ( is_protected_meta( $metadata[ $key ][ 'meta_key' ], 'post' ) || ! current_user_can( 'edit_post_meta', $post->ID, $metadata[ $key ][ 'meta_key' ] ) )
		unset( $metadata[ $key ] );
}
list_meta( $metadata );
meta_form(); ?>
</div>
<p><?php _e('Custom fields can be used to add extra metadata to a post that you can <a href="http://codex.wordpress.org/Using_Custom_Fields" target="_blank">use in your theme</a>.'); ?></p>
<?php
}

/**
 * Display comments status form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function post_comment_status_meta_box($post) {
?>
<input name="advanced_view" type="hidden" value="1" />
<p class="meta-options">
	<label for="comment_status" class="selectit"><input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($post->comment_status, 'open'); ?> /> <?php _e( 'Allow comments.' ) ?></label><br />
	<label for="ping_status" class="selectit"><input name="ping_status" type="checkbox" id="ping_status" value="open" <?php checked($post->ping_status, 'open'); ?> /> <?php printf( __( 'Allow <a href="%s" target="_blank">trackbacks and pingbacks</a> on this page.' ), __( 'http://codex.wordpress.org/Introduction_to_Blogging#Managing_Comments' ) ); ?></label>
	<?php do_action('post_comment_status_meta_box-options', $post); ?>
</p>
<?php
}

/**
 * Display comments for post table header
 *
 * @since 3.0.0
 *
 * @param array $result table header rows
 * @return array
 */
function post_comment_meta_box_thead($result) {
	unset($result['cb'], $result['response']);
	return $result;
}

/**
 * Display comments for post.
 *
 * @since 2.8.0
 *
 * @param object $post
 */
function post_comment_meta_box($post) {
	global $wpdb, $post_ID;

	wp_nonce_field( 'get-comments', 'add_comment_nonce', false );
	?>
	<p class="hide-if-no-js" id="add-new-comment"><a href="#commentstatusdiv" onclick="commentReply.addcomment(<?php echo $post_ID; ?>);return false;"><?php _e('Add comment'); ?></a></p>
	<?php

	$total = $wpdb->get_var($wpdb->prepare("SELECT count(1) FROM $wpdb->comments WHERE comment_post_ID = '%d' AND ( comment_approved = '0' OR comment_approved = '1')", $post_ID));
	$wp_list_table = _get_list_table('WP_Post_Comments_List_Table');
	$wp_list_table->display( true );

	if ( 1 > $total ) {
		echo '<p id="no-comments">' . __('No comments yet.') . '</p>';
	} else {
		$hidden = get_hidden_meta_boxes( get_current_screen() );
		if ( ! in_array('commentsdiv', $hidden) ) {
			?>
			<script type="text/javascript">jQuery(document).ready(function(){commentsBox.get(<?php echo $total; ?>, 10);});</script>
			<?php
		}

		?>
		<p class="hide-if-no-js" id="show-comments"><a href="#commentstatusdiv" onclick="commentsBox.get(<?php echo $total; ?>);return false;"><?php _e('Show comments'); ?></a> <img class="waiting" style="display:none;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" /></p>
		<?php
	}

	wp_comment_trashnotice();
}

/**
 * Display slug form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function post_slug_meta_box($post) {
?>
<label class="screen-reader-text" for="post_name"><?php _e('Slug') ?></label><input name="post_name" type="text" size="13" id="post_name" value="<?php echo esc_attr( apply_filters('editable_slug', $post->post_name) ); ?>" />
<?php
}

/**
 * Display form field with list of authors.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function post_author_meta_box($post) {
	global $user_ID;
?>
<label class="screen-reader-text" for="post_author_override"><?php _e('Author'); ?></label>
<?php
	wp_dropdown_users( array(
		'who' => 'authors',
		'name' => 'post_author_override',
		'selected' => empty($post->ID) ? $user_ID : $post->post_author,
		'include_selected' => true
	) );
}

/**
 * Display list of revisions.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function post_revisions_meta_box($post) {
	wp_list_post_revisions();
}

// -- Page related Meta Boxes

/**
 * Display page attributes form fields.
 *
 * @since 2.7.0
 *
 * @param object $post
 */
function page_attributes_meta_box($post) {
	$post_type_object = get_post_type_object($post->post_type);
	if ( $post_type_object->hierarchical ) {
		$dropdown_args = array(
			'post_type'        => $post->post_type,
			'exclude_tree'     => $post->ID,
			'selected'         => $post->post_parent,
			'name'             => 'parent_id',
			'show_option_none' => __('(no parent)'),
			'sort_column'      => 'menu_order, post_title',
			'echo'             => 0,
		);

		$dropdown_args = apply_filters( 'page_attributes_dropdown_pages_args', $dropdown_args, $post );
		$pages = wp_dropdown_pages( $dropdown_args );
		if ( ! empty($pages) ) {
?>
<p><strong><?php _e('Parent') ?></strong></p>
<label class="screen-reader-text" for="parent_id"><?php _e('Parent') ?></label>
<?php echo $pages; ?>
<?php
		} // end empty pages check
	} // end hierarchical check.
	if ( 'page' == $post->post_type && 0 != count( get_page_templates() ) ) {
		$template = !empty($post->page_template) ? $post->page_template : false;
		?>
<p><strong><?php _e('Template') ?></strong></p>
<label class="screen-reader-text" for="page_template"><?php _e('Page Template') ?></label><select name="page_template" id="page_template">
<option value='default'><?php _e('Default Template'); ?></option>
<?php page_template_dropdown($template); ?>
</select>
<?php
	} ?>
<p><strong><?php _e('Order') ?></strong></p>
<p><label class="screen-reader-text" for="menu_order"><?php _e('Order') ?></label><input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo esc_attr($post->menu_order) ?>" /></p>
<p><?php if ( 'page' == $post->post_type ) _e( 'Need help? Use the Help tab in the upper right of your screen.' ); ?></p>
<?php
}

// -- Link related Meta Boxes

/**
 * Display link create form fields.
 *
 * @since 2.7.0
 *
 * @param object $link
 */
function link_submit_meta_box($link) {
?>
<div class="submitbox" id="submitlink">

<div id="minor-publishing">

<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
<div style="display:none;">
<?php submit_button( __( 'Save' ), 'button', 'save', false ); ?>
</div>

<div id="minor-publishing-actions">
<div id="preview-action">
<?php if ( !empty($link->link_id) ) { ?>
	<a class="preview button" href="<?php echo $link->link_url; ?>" target="_blank" tabindex="4"><?php _e('Visit Link'); ?></a>
<?php } ?>
</div>
<div class="clear"></div>
</div>

<div id="misc-publishing-actions">
<div class="misc-pub-section">
	<label for="link_private" class="selectit"><input id="link_private" name="link_visible" type="checkbox" value="N" <?php checked($link->link_visible, 'N'); ?> /> <?php _e('Keep this link private') ?></label>
</div>
</div>

</div>

<div id="major-publishing-actions">
<?php do_action('post_submitbox_start'); ?>
<div id="delete-action">
<?php
if ( !empty($_GET['action']) && 'edit' == $_GET['action'] && current_user_can('manage_links') ) { ?>
	<a class="submitdelete deletion" href="<?php echo wp_nonce_url("link.php?action=delete&amp;link_id=$link->link_id", 'delete-bookmark_' . $link->link_id); ?>" onclick="if ( confirm('<?php echo esc_js(sprintf(__("You are about to delete this link '%s'\n 'Cancel' to stop, 'OK' to delete."), $link->link_name )); ?>') ) {return true;}return false;"><?php _e('Delete'); ?></a>
<?php } ?>
</div>

<div id="publishing-action">
<?php if ( !empty($link->link_id) ) { ?>
	<input name="save" type="submit" class="button-primary" id="publish" tabindex="4" accesskey="p" value="<?php esc_attr_e('Update Link') ?>" />
<?php } else { ?>
	<input name="save" type="submit" class="button-primary" id="publish" tabindex="4" accesskey="p" value="<?php esc_attr_e('Add Link') ?>" />
<?php } ?>
</div>
<div class="clear"></div>
</div>
<?php do_action('submitlink_box'); ?>
<div class="clear"></div>
</div>
<?php
}

/**
 * Display link categories form fields.
 *
 * @since 2.6.0
 *
 * @param object $link
 */
function link_categories_meta_box($link) {
?>
<div id="taxonomy-linkcategory" class="categorydiv">
	<ul id="category-tabs" class="category-tabs">
		<li class="tabs"><a href="#categories-all"><?php _e( 'All Categories' ); ?></a></li>
		<li class="hide-if-no-js"><a href="#categories-pop"><?php _e( 'Most Used' ); ?></a></li>
	</ul>

	<div id="categories-all" class="tabs-panel">
		<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
			<?php
			if ( isset($link->link_id) )
				wp_link_category_checklist($link->link_id);
			else
				wp_link_category_checklist();
			?>
		</ul>
	</div>

	<div id="categories-pop" class="tabs-panel" style="display: none;">
		<ul id="categorychecklist-pop" class="categorychecklist form-no-clear">
			<?php wp_popular_terms_checklist('link_category'); ?>
		</ul>
	</div>

	<div id="category-adder" class="wp-hidden-children">
		<h4><a id="category-add-toggle" href="#category-add"><?php _e( '+ Add New Category' ); ?></a></h4>
		<p id="link-category-add" class="wp-hidden-child">
			<label class="screen-reader-text" for="newcat"><?php _e( '+ Add New Category' ); ?></label>
			<input type="text" name="newcat" id="newcat" class="form-required form-input-tip" value="<?php esc_attr_e( 'New category name' ); ?>" aria-required="true" />
			<input type="button" id="link-category-add-submit" class="add:categorychecklist:linkcategorydiv button" value="<?php esc_attr_e( 'Add' ); ?>" />
			<?php wp_nonce_field( 'add-link-category', '_ajax_nonce', false ); ?>
			<span id="category-ajax-response"></span>
		</p>
	</div>
</div>
<?php
}

/**
 * Display form fields for changing link target.
 *
 * @since 2.6.0
 *
 * @param object $link
 */
function link_target_meta_box($link) { ?>
<fieldset><legend class="screen-reader-text"><span><?php _e('Target') ?></span></legend>
<p><label for="link_target_blank" class="selectit">
<input id="link_target_blank" type="radio" name="link_target" value="_blank" <?php echo ( isset( $link->link_target ) && ($link->link_target == '_blank') ? 'checked="checked"' : ''); ?> />
<?php _e('<code>_blank</code> &mdash; new window or tab.'); ?></label></p>
<p><label for="link_target_top" class="selectit">
<input id="link_target_top" type="radio" name="link_target" value="_top" <?php echo ( isset( $link->link_target ) && ($link->link_target == '_top') ? 'checked="checked"' : ''); ?> />
<?php _e('<code>_top</code> &mdash; current window or tab, with no frames.'); ?></label></p>
<p><label for="link_target_none" class="selectit">
<input id="link_target_none" type="radio" name="link_target" value="" <?php echo ( isset( $link->link_target ) && ($link->link_target == '') ? 'checked="checked"' : ''); ?> />
<?php _e('<code>_none</code> &mdash; same window or tab.'); ?></label></p>
</fieldset>
<p><?php _e('Choose the target frame for your link.'); ?></p>
<?php
}

/**
 * Display checked checkboxes attribute for xfn microformat options.
 *
 * @since 1.0.1
 *
 * @param string $class
 * @param string $value
 * @param mixed $deprecated Never used.
 */
function xfn_check( $class, $value = '', $deprecated = '' ) {
	global $link;

	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '0.0' ); // Never implemented

	$link_rel = isset( $link->link_rel ) ? $link->link_rel : ''; // In PHP 5.3: $link_rel = $link->link_rel ?: '';
	$rels = preg_split('/\s+/', $link_rel);

	if ('' != $value && in_array($value, $rels) ) {
		echo ' checked="checked"';
	}

	if ('' == $value) {
		if ('family' == $class && strpos($link_rel, 'child') === false && strpos($link_rel, 'parent') === false && strpos($link_rel, 'sibling') === false && strpos($link_rel, 'spouse') === false && strpos($link_rel, 'kin') === false) echo ' checked="checked"';
		if ('friendship' == $class && strpos($link_rel, 'friend') === false && strpos($link_rel, 'acquaintance') === false && strpos($link_rel, 'contact') === false) echo ' checked="checked"';
		if ('geographical' == $class && strpos($link_rel, 'co-resident') === false && strpos($link_rel, 'neighbor') === false) echo ' checked="checked"';
		if ('identity' == $class && in_array('me', $rels) ) echo ' checked="checked"';
	}
}

/**
 * Display xfn form fields.
 *
 * @since 2.6.0
 *
 * @param object $link
 */
function link_xfn_meta_box($link) {
?>
<table class="links-table" cellspacing="0">
	<tr>
		<th scope="row"><label for="link_rel"><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('rel:') ?></label></th>
		<td><input type="text" name="link_rel" id="link_rel" value="<?php echo ( isset( $link->link_rel ) ? esc_attr($link->link_rel) : ''); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('identity') ?></th>
		<td><fieldset><legend class="screen-reader-text"><span><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('identity') ?></span></legend>
			<label for="me">
			<input type="checkbox" name="identity" value="me" id="me" <?php xfn_check('identity', 'me'); ?> />
			<?php _e('another web address of mine') ?></label>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('friendship') ?></th>
		<td><fieldset><legend class="screen-reader-text"><span><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('friendship') ?></span></legend>
			<label for="contact">
			<input class="valinp" type="radio" name="friendship" value="contact" id="contact" <?php xfn_check('friendship', 'contact'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('contact') ?>
			</label>
			<label for="acquaintance">
			<input class="valinp" type="radio" name="friendship" value="acquaintance" id="acquaintance" <?php xfn_check('friendship', 'acquaintance'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('acquaintance') ?>
			</label>
			<label for="friend">
			<input class="valinp" type="radio" name="friendship" value="friend" id="friend" <?php xfn_check('friendship', 'friend'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('friend') ?>
			</label>
			<label for="friendship">
			<input name="friendship" type="radio" class="valinp" value="" id="friendship" <?php xfn_check('friendship'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('none') ?>
			</label>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"> <?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('physical') ?> </th>
		<td><fieldset><legend class="screen-reader-text"><span><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('physical') ?></span></legend>
			<label for="met">
			<input class="valinp" type="checkbox" name="physical" value="met" id="met" <?php xfn_check('physical', 'met'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('met') ?>
			</label>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"> <?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('professional') ?> </th>
		<td><fieldset><legend class="screen-reader-text"><span><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('professional') ?></span></legend>
			<label for="co-worker">
			<input class="valinp" type="checkbox" name="professional" value="co-worker" id="co-worker" <?php xfn_check('professional', 'co-worker'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('co-worker') ?>
			</label>
			<label for="colleague">
			<input class="valinp" type="checkbox" name="professional" value="colleague" id="colleague" <?php xfn_check('professional', 'colleague'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('colleague') ?>
			</label>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('geographical') ?></th>
		<td><fieldset><legend class="screen-reader-text"><span> <?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('geographical') ?> </span></legend>
			<label for="co-resident">
			<input class="valinp" type="radio" name="geographical" value="co-resident" id="co-resident" <?php xfn_check('geographical', 'co-resident'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('co-resident') ?>
			</label>
			<label for="neighbor">
			<input class="valinp" type="radio" name="geographical" value="neighbor" id="neighbor" <?php xfn_check('geographical', 'neighbor'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('neighbor') ?>
			</label>
			<label for="geographical">
			<input class="valinp" type="radio" name="geographical" value="" id="geographical" <?php xfn_check('geographical'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('none') ?>
			</label>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('family') ?></th>
		<td><fieldset><legend class="screen-reader-text"><span> <?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('family') ?> </span></legend>
			<label for="child">
			<input class="valinp" type="radio" name="family" value="child" id="child" <?php xfn_check('family', 'child'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('child') ?>
			</label>
			<label for="kin">
			<input class="valinp" type="radio" name="family" value="kin" id="kin" <?php xfn_check('family', 'kin'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('kin') ?>
			</label>
			<label for="parent">
			<input class="valinp" type="radio" name="family" value="parent" id="parent" <?php xfn_check('family', 'parent'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('parent') ?>
			</label>
			<label for="sibling">
			<input class="valinp" type="radio" name="family" value="sibling" id="sibling" <?php xfn_check('family', 'sibling'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('sibling') ?>
			</label>
			<label for="spouse">
			<input class="valinp" type="radio" name="family" value="spouse" id="spouse" <?php xfn_check('family', 'spouse'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('spouse') ?>
			</label>
			<label for="family">
			<input class="valinp" type="radio" name="family" value="" id="family" <?php xfn_check('family'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('none') ?>
			</label>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('romantic') ?></th>
		<td><fieldset><legend class="screen-reader-text"><span> <?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('romantic') ?> </span></legend>
			<label for="muse">
			<input class="valinp" type="checkbox" name="romantic" value="muse" id="muse" <?php xfn_check('romantic', 'muse'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('muse') ?>
			</label>
			<label for="crush">
			<input class="valinp" type="checkbox" name="romantic" value="crush" id="crush" <?php xfn_check('romantic', 'crush'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('crush') ?>
			</label>
			<label for="date">
			<input class="valinp" type="checkbox" name="romantic" value="date" id="date" <?php xfn_check('romantic', 'date'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('date') ?>
			</label>
			<label for="romantic">
			<input class="valinp" type="checkbox" name="romantic" value="sweetheart" id="romantic" <?php xfn_check('romantic', 'sweetheart'); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e('sweetheart') ?>
			</label>
		</fieldset></td>
	</tr>

</table>
<p><?php _e('If the link is to a person, you can specify your relationship with them using the above form. If you would like to learn more about the idea check out <a href="http://gmpg.org/xfn/">XFN</a>.'); ?></p>
<?php
}

/**
 * Display advanced link options form fields.
 *
 * @since 2.6.0
 *
 * @param object $link
 */
function link_advanced_meta_box($link) {
?>
<table class="links-table" cellpadding="0">
	<tr>
		<th scope="row"><label for="link_image"><?php _e('Image Address') ?></label></th>
		<td><input type="text" name="link_image" class="code" id="link_image" value="<?php echo ( isset( $link->link_image ) ? esc_attr($link->link_image) : ''); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="rss_uri"><?php _e('RSS Address') ?></label></th>
		<td><input name="link_rss" class="code" type="text" id="rss_uri" value="<?php echo ( isset( $link->link_rss ) ? esc_attr($link->link_rss) : ''); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="link_notes"><?php _e('Notes') ?></label></th>
		<td><textarea name="link_notes" id="link_notes" rows="10"><?php echo ( isset( $link->link_notes ) ? $link->link_notes : ''); // textarea_escaped ?></textarea></td>
	</tr>
	<tr>
		<th scope="row"><label for="link_rating"><?php _e('Rating') ?></label></th>
		<td><select name="link_rating" id="link_rating" size="1">
		<?php
			for ( $r = 0; $r <= 10; $r++ ) {
				echo '<option value="' . $r . '"';
				if ( isset($link->link_rating) && $link->link_rating == $r )
					echo ' selected="selected"';
				echo('>' . $r . '</option>');
			}
		?></select>&nbsp;<?php _e('(Leave at 0 for no rating.)') ?>
		</td>
	</tr>
</table>
<?php
}

/**
 * Display post thumbnail meta box.
 *
 * @since 2.9.0
 */
function post_thumbnail_meta_box() {
	global $post;
	$thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );
	echo _wp_post_thumbnail_html( $thumbnail_id );
}
