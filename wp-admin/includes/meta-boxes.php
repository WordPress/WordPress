<?php
/**
 * WordPress Administration Meta Boxes API.
 *
 * @package WordPress
 * @subpackage Administration
 */

//
// Post-related Meta Boxes.
//

/**
 * Displays post submit form fields.
 *
 * @since 2.7.0
 *
 * @global string $action
 *
 * @param WP_Post  $post Current post object.
 * @param array    $args {
 *     Array of arguments for building the post submit meta box.
 *
 *     @type string   $id       Meta box 'id' attribute.
 *     @type string   $title    Meta box title.
 *     @type callable $callback Meta box display callback.
 *     @type array    $args     Extra meta box arguments.
 * }
 */
function post_submit_meta_box( $post, $args = array() ) {
	global $action;

	$post_type        = $post->post_type;
	$post_type_object = get_post_type_object( $post_type );
	$can_publish      = current_user_can( $post_type_object->cap->publish_posts );
	?>
<div class="submitbox" id="submitpost">

<div id="minor-publishing">

	<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key. ?>
<div style="display:none;">
	<?php submit_button( __( 'Save' ), '', 'save' ); ?>
</div>

<div id="minor-publishing-actions">
<div id="save-action">
	<?php
	if ( 'publish' != $post->post_status && 'future' != $post->post_status && 'pending' != $post->post_status ) {
		$private_style = '';
		if ( 'private' == $post->post_status ) {
			$private_style = 'style="display:none"';
		}
		?>
<input <?php echo $private_style; ?> type="submit" name="save" id="save-post" value="<?php esc_attr_e( 'Save Draft' ); ?>" class="button" />
<span class="spinner"></span>
<?php } elseif ( 'pending' == $post->post_status && $can_publish ) { ?>
<input type="submit" name="save" id="save-post" value="<?php esc_attr_e( 'Save as Pending' ); ?>" class="button" />
<span class="spinner"></span>
<?php } ?>
</div>
	<?php if ( is_post_type_viewable( $post_type_object ) ) : ?>
<div id="preview-action">
		<?php
		$preview_link = esc_url( get_preview_post_link( $post ) );
		if ( 'publish' == $post->post_status ) {
			$preview_button_text = __( 'Preview Changes' );
		} else {
			$preview_button_text = __( 'Preview' );
		}

		$preview_button = sprintf(
			'%1$s<span class="screen-reader-text"> %2$s</span>',
			$preview_button_text,
			/* translators: Accessibility text. */
			__( '(opens in a new tab)' )
		);
		?>
<a class="preview button" href="<?php echo $preview_link; ?>" target="wp-preview-<?php echo (int) $post->ID; ?>" id="post-preview"><?php echo $preview_button; ?></a>
<input type="hidden" name="wp-preview" id="wp-preview" value="" />
</div>
<?php endif; // is_post_type_viewable() ?>
	<?php
	/**
	 * Fires before the post time/date setting in the Publish meta box.
	 *
	 * @since 4.4.0
	 *
	 * @param WP_Post $post WP_Post object for the current post.
	 */
	do_action( 'post_submitbox_minor_actions', $post );
	?>
<div class="clear"></div>
</div><!-- #minor-publishing-actions -->

<div id="misc-publishing-actions">

<div class="misc-pub-section misc-pub-post-status">
	<?php _e( 'Status:' ); ?> <span id="post-status-display">
			<?php

			switch ( $post->post_status ) {
				case 'private':
					_e( 'Privately Published' );
					break;
				case 'publish':
					_e( 'Published' );
					break;
				case 'future':
					_e( 'Scheduled' );
					break;
				case 'pending':
					_e( 'Pending Review' );
					break;
				case 'draft':
				case 'auto-draft':
					_e( 'Draft' );
					break;
			}
			?>
</span>
	<?php
	if ( 'publish' == $post->post_status || 'private' == $post->post_status || $can_publish ) {
		$private_style = '';
		if ( 'private' == $post->post_status ) {
			$private_style = 'style="display:none"';
		}
		?>
<a href="#post_status" <?php echo $private_style; ?> class="edit-post-status hide-if-no-js" role="button"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text"><?php _e( 'Edit status' ); ?></span></a>

<div id="post-status-select" class="hide-if-js">
<input type="hidden" name="hidden_post_status" id="hidden_post_status" value="<?php echo esc_attr( ( 'auto-draft' == $post->post_status ) ? 'draft' : $post->post_status ); ?>" />
<label for="post_status" class="screen-reader-text"><?php _e( 'Set status' ); ?></label>
<select name="post_status" id="post_status">
		<?php if ( 'publish' == $post->post_status ) : ?>
<option<?php selected( $post->post_status, 'publish' ); ?> value='publish'><?php _e( 'Published' ); ?></option>
<?php elseif ( 'private' == $post->post_status ) : ?>
<option<?php selected( $post->post_status, 'private' ); ?> value='publish'><?php _e( 'Privately Published' ); ?></option>
<?php elseif ( 'future' == $post->post_status ) : ?>
<option<?php selected( $post->post_status, 'future' ); ?> value='future'><?php _e( 'Scheduled' ); ?></option>
<?php endif; ?>
<option<?php selected( $post->post_status, 'pending' ); ?> value='pending'><?php _e( 'Pending Review' ); ?></option>
		<?php if ( 'auto-draft' == $post->post_status ) : ?>
<option<?php selected( $post->post_status, 'auto-draft' ); ?> value='draft'><?php _e( 'Draft' ); ?></option>
<?php else : ?>
<option<?php selected( $post->post_status, 'draft' ); ?> value='draft'><?php _e( 'Draft' ); ?></option>
<?php endif; ?>
</select>
<a href="#post_status" class="save-post-status hide-if-no-js button"><?php _e( 'OK' ); ?></a>
<a href="#post_status" class="cancel-post-status hide-if-no-js button-cancel"><?php _e( 'Cancel' ); ?></a>
</div>

<?php } ?>
</div><!-- .misc-pub-section -->

<div class="misc-pub-section misc-pub-visibility" id="visibility">
	<?php _e( 'Visibility:' ); ?> <span id="post-visibility-display">
							<?php

							if ( 'private' === $post->post_status ) {
								$post->post_password = '';
								$visibility          = 'private';
								$visibility_trans    = __( 'Private' );
							} elseif ( ! empty( $post->post_password ) ) {
								$visibility       = 'password';
								$visibility_trans = __( 'Password protected' );
							} elseif ( 'post' === $post_type && is_sticky( $post->ID ) ) {
								$visibility       = 'public';
								$visibility_trans = __( 'Public, Sticky' );
							} else {
								$visibility       = 'public';
								$visibility_trans = __( 'Public' );
							}

							echo esc_html( $visibility_trans );
							?>
</span>
	<?php if ( $can_publish ) { ?>
<a href="#visibility" class="edit-visibility hide-if-no-js" role="button"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text"><?php _e( 'Edit visibility' ); ?></span></a>

<div id="post-visibility-select" class="hide-if-js">
<input type="hidden" name="hidden_post_password" id="hidden-post-password" value="<?php echo esc_attr( $post->post_password ); ?>" />
		<?php if ( 'post' === $post_type ) : ?>
<input type="checkbox" style="display:none" name="hidden_post_sticky" id="hidden-post-sticky" value="sticky" <?php checked( is_sticky( $post->ID ) ); ?> />
<?php endif; ?>
<input type="hidden" name="hidden_post_visibility" id="hidden-post-visibility" value="<?php echo esc_attr( $visibility ); ?>" />
<input type="radio" name="visibility" id="visibility-radio-public" value="public" <?php checked( $visibility, 'public' ); ?> /> <label for="visibility-radio-public" class="selectit"><?php _e( 'Public' ); ?></label><br />
		<?php if ( 'post' === $post_type && current_user_can( 'edit_others_posts' ) ) : ?>
<span id="sticky-span"><input id="sticky" name="sticky" type="checkbox" value="sticky" <?php checked( is_sticky( $post->ID ) ); ?> /> <label for="sticky" class="selectit"><?php _e( 'Stick this post to the front page' ); ?></label><br /></span>
<?php endif; ?>
<input type="radio" name="visibility" id="visibility-radio-password" value="password" <?php checked( $visibility, 'password' ); ?> /> <label for="visibility-radio-password" class="selectit"><?php _e( 'Password protected' ); ?></label><br />
<span id="password-span"><label for="post_password"><?php _e( 'Password:' ); ?></label> <input type="text" name="post_password" id="post_password" value="<?php echo esc_attr( $post->post_password ); ?>"  maxlength="255" /><br /></span>
<input type="radio" name="visibility" id="visibility-radio-private" value="private" <?php checked( $visibility, 'private' ); ?> /> <label for="visibility-radio-private" class="selectit"><?php _e( 'Private' ); ?></label><br />

<p>
	<a href="#visibility" class="save-post-visibility hide-if-no-js button"><?php _e( 'OK' ); ?></a>
	<a href="#visibility" class="cancel-post-visibility hide-if-no-js button-cancel"><?php _e( 'Cancel' ); ?></a>
</p>
</div>
<?php } ?>

</div><!-- .misc-pub-section -->

	<?php
	/* translators: Publish box date string. 1: Date, 2: Time. See https://www.php.net/date */
	$date_string = __( '%1$s at %2$s' );
	/* translators: Publish box date format, see https://www.php.net/date */
	$date_format = _x( 'M j, Y', 'publish box date format' );
	/* translators: Publish box time format, see https://www.php.net/date */
	$time_format = _x( 'H:i', 'publish box time format' );

	if ( 0 != $post->ID ) {
		if ( 'future' == $post->post_status ) { // Scheduled for publishing at a future date.
			/* translators: Post date information. %s: Date on which the post is currently scheduled to be published. */
			$stamp = __( 'Scheduled for: %s' );
		} elseif ( 'publish' == $post->post_status || 'private' == $post->post_status ) { // Already published.
			/* translators: Post date information. %s: Date on which the post was published. */
			$stamp = __( 'Published on: %s' );
		} elseif ( '0000-00-00 00:00:00' == $post->post_date_gmt ) { // Draft, 1 or more saves, no date specified.
			$stamp = __( 'Publish <b>immediately</b>' );
		} elseif ( time() < strtotime( $post->post_date_gmt . ' +0000' ) ) { // Draft, 1 or more saves, future date specified.
			/* translators: Post date information. %s: Date on which the post is to be published. */
			$stamp = __( 'Schedule for: %s' );
		} else { // Draft, 1 or more saves, date specified.
			/* translators: Post date information. %s: Date on which the post is to be published. */
			$stamp = __( 'Publish on: %s' );
		}
		$date = sprintf(
			$date_string,
			date_i18n( $date_format, strtotime( $post->post_date ) ),
			date_i18n( $time_format, strtotime( $post->post_date ) )
		);
	} else { // Draft (no saves, and thus no date specified).
		$stamp = __( 'Publish <b>immediately</b>' );
		$date  = sprintf(
			$date_string,
			date_i18n( $date_format, strtotime( current_time( 'mysql' ) ) ),
			date_i18n( $time_format, strtotime( current_time( 'mysql' ) ) )
		);
	}

	if ( ! empty( $args['args']['revisions_count'] ) ) :
		?>
<div class="misc-pub-section misc-pub-revisions">
		<?php
		/* translators: Post revisions heading. %s: The number of available revisions. */
		printf( __( 'Revisions: %s' ), '<b>' . number_format_i18n( $args['args']['revisions_count'] ) . '</b>' );
		?>
	<a class="hide-if-no-js" href="<?php echo esc_url( get_edit_post_link( $args['args']['revision_id'] ) ); ?>"><span aria-hidden="true"><?php _ex( 'Browse', 'revisions' ); ?></span> <span class="screen-reader-text"><?php _e( 'Browse revisions' ); ?></span></a>
</div>
		<?php
endif;

	if ( $can_publish ) : // Contributors don't get to choose the date of publish.
		?>
<div class="misc-pub-section curtime misc-pub-curtime">
	<span id="timestamp">
		<?php printf( $stamp, '<b>' . $date . '</b>' ); ?>
	</span>
	<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" role="button">
		<span aria-hidden="true"><?php _e( 'Edit' ); ?></span>
		<span class="screen-reader-text"><?php _e( 'Edit date and time' ); ?></span>
	</a>
	<fieldset id="timestampdiv" class="hide-if-js">
		<legend class="screen-reader-text"><?php _e( 'Date and time' ); ?></legend>
		<?php touch_time( ( 'edit' === $action ), 1 ); ?>
	</fieldset>
</div><?php // /misc-pub-section ?>
<?php endif; ?>

	<?php if ( 'draft' === $post->post_status && get_post_meta( $post->ID, '_customize_changeset_uuid', true ) ) : ?>
	<div class="notice notice-info notice-alt inline">
		<p>
			<?php
			echo sprintf(
				/* translators: %s: URL to the Customizer. */
				__( 'This draft comes from your <a href="%s">unpublished customization changes</a>. You can edit, but there&#8217;s no need to publish now. It will be published automatically with those changes.' ),
				esc_url(
					add_query_arg(
						'changeset_uuid',
						rawurlencode( get_post_meta( $post->ID, '_customize_changeset_uuid', true ) ),
						admin_url( 'customize.php' )
					)
				)
			);
			?>
		</p>
	</div>
	<?php endif; ?>

	<?php
	/**
	 * Fires after the post time/date setting in the Publish meta box.
	 *
	 * @since 2.9.0
	 * @since 4.4.0 Added the `$post` parameter.
	 *
	 * @param WP_Post $post WP_Post object for the current post.
	 */
	do_action( 'post_submitbox_misc_actions', $post );
	?>
</div>
<div class="clear"></div>
</div>

<div id="major-publishing-actions">
	<?php
	/**
	 * Fires at the beginning of the publishing actions section of the Publish meta box.
	 *
	 * @since 2.7.0
	 * @since 4.9.0 Added the `$post` parameter.
	 *
	 * @param WP_Post|null $post WP_Post object for the current post on Edit Post screen,
	 *                           null on Edit Link screen.
	 */
	do_action( 'post_submitbox_start', $post );
	?>
<div id="delete-action">
	<?php
	if ( current_user_can( 'delete_post', $post->ID ) ) {
		if ( ! EMPTY_TRASH_DAYS ) {
			$delete_text = __( 'Delete Permanently' );
		} else {
			$delete_text = __( 'Move to Trash' );
		}
		?>
<a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post->ID ); ?>"><?php echo $delete_text; ?></a>
													<?php
	}
	?>
</div>

<div id="publishing-action">
<span class="spinner"></span>
	<?php
	if ( ! in_array( $post->post_status, array( 'publish', 'future', 'private' ) ) || 0 == $post->ID ) {
		if ( $can_publish ) :
			if ( ! empty( $post->post_date_gmt ) && time() < strtotime( $post->post_date_gmt . ' +0000' ) ) :
				?>
		<input name="original_publish" type="hidden" id="original_publish" value="<?php echo esc_attr_x( 'Schedule', 'post action/button label' ); ?>" />
				<?php submit_button( _x( 'Schedule', 'post action/button label' ), 'primary large', 'publish', false ); ?>
	<?php	else : ?>
		<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Publish' ); ?>" />
		<?php submit_button( __( 'Publish' ), 'primary large', 'publish', false ); ?>
		<?php
	endif;
	else :
		?>
		<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Submit for Review' ); ?>" />
		<?php submit_button( __( 'Submit for Review' ), 'primary large', 'publish', false ); ?>
		<?php
		endif;
	} else {
		?>
		<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update' ); ?>" />
		<input name="save" type="submit" class="button button-primary button-large" id="publish" value="<?php esc_attr_e( 'Update' ); ?>" />
		<?php
	}
	?>
</div>
<div class="clear"></div>
</div>
</div>

	<?php
}

/**
 * Display attachment submit form fields.
 *
 * @since 3.5.0
 *
 * @param object $post
 */
function attachment_submit_meta_box( $post ) {
	?>
<div class="submitbox" id="submitpost">

<div id="minor-publishing">

	<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key. ?>
<div style="display:none;">
	<?php submit_button( __( 'Save' ), '', 'save' ); ?>
</div>


<div id="misc-publishing-actions">
	<div class="misc-pub-section curtime misc-pub-curtime">
		<span id="timestamp">
			<?php
			$uploaded_on = sprintf(
				/* translators: Publish box date string. 1: Date, 2: Time. See https://www.php.net/date */
				__( '%1$s at %2$s' ),
				/* translators: Publish box date format, see https://www.php.net/date */
				date_i18n( _x( 'M j, Y', 'publish box date format' ), strtotime( $post->post_date ) ),
				/* translators: Publish box time format, see https://www.php.net/date */
				date_i18n( _x( 'H:i', 'publish box time format' ), strtotime( $post->post_date ) )
			);
			/* translators: Attachment information. %s: Date the attachment was uploaded. */
			printf( __( 'Uploaded on: %s' ), '<b>' . $uploaded_on . '</b>' );
			?>
		</span>
	</div><!-- .misc-pub-section -->

	<?php
	/**
	 * Fires after the 'Uploaded on' section of the Save meta box
	 * in the attachment editing screen.
	 *
	 * @since 3.5.0
	 * @since 4.9.0 Added the `$post` parameter.
	 *
	 * @param WP_Post $post WP_Post object for the current attachment.
	 */
	do_action( 'attachment_submitbox_misc_actions', $post );
	?>
</div><!-- #misc-publishing-actions -->
<div class="clear"></div>
</div><!-- #minor-publishing -->

<div id="major-publishing-actions">
	<div id="delete-action">
	<?php
	if ( current_user_can( 'delete_post', $post->ID ) ) {
		if ( EMPTY_TRASH_DAYS && MEDIA_TRASH ) {
			echo "<a class='submitdelete deletion' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Move to Trash' ) . '</a>';
		} else {
			$delete_ays = ! MEDIA_TRASH ? " onclick='return showNotice.warn();'" : '';
			echo "<a class='submitdelete deletion'$delete_ays href='" . get_delete_post_link( $post->ID, null, true ) . "'>" . __( 'Delete Permanently' ) . '</a>';
		}
	}
	?>
	</div>

	<div id="publishing-action">
		<span class="spinner"></span>
		<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update' ); ?>" />
		<input name="save" type="submit" class="button button-primary button-large" id="publish" value="<?php esc_attr_e( 'Update' ); ?>" />
	</div>
	<div class="clear"></div>
</div><!-- #major-publishing-actions -->

</div>

	<?php
}

/**
 * Display post format form elements.
 *
 * @since 3.1.0
 *
 * @param WP_Post $post Post object.
 * @param array   $box {
 *     Post formats meta box arguments.
 *
 *     @type string   $id       Meta box 'id' attribute.
 *     @type string   $title    Meta box title.
 *     @type callable $callback Meta box display callback.
 *     @type array    $args     Extra meta box arguments.
 * }
 */
function post_format_meta_box( $post, $box ) {
	if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post->post_type, 'post-formats' ) ) :
		$post_formats = get_theme_support( 'post-formats' );

		if ( is_array( $post_formats[0] ) ) :
			$post_format = get_post_format( $post->ID );
			if ( ! $post_format ) {
				$post_format = '0';
			}
			// Add in the current one if it isn't there yet, in case the current theme doesn't support it.
			if ( $post_format && ! in_array( $post_format, $post_formats[0] ) ) {
				$post_formats[0][] = $post_format;
			}
			?>
		<div id="post-formats-select">
		<fieldset>
			<legend class="screen-reader-text"><?php _e( 'Post Formats' ); ?></legend>
			<input type="radio" name="post_format" class="post-format" id="post-format-0" value="0" <?php checked( $post_format, '0' ); ?> /> <label for="post-format-0" class="post-format-icon post-format-standard"><?php echo get_post_format_string( 'standard' ); ?></label>
			<?php foreach ( $post_formats[0] as $format ) : ?>
			<br /><input type="radio" name="post_format" class="post-format" id="post-format-<?php echo esc_attr( $format ); ?>" value="<?php echo esc_attr( $format ); ?>" <?php checked( $post_format, $format ); ?> /> <label for="post-format-<?php echo esc_attr( $format ); ?>" class="post-format-icon post-format-<?php echo esc_attr( $format ); ?>"><?php echo esc_html( get_post_format_string( $format ) ); ?></label>
			<?php endforeach; ?>
		</fieldset>
	</div>
			<?php
	endif;
endif;
}

/**
 * Display post tags form fields.
 *
 * @since 2.6.0
 *
 * @todo Create taxonomy-agnostic wrapper for this.
 *
 * @param WP_Post $post Post object.
 * @param array   $box {
 *     Tags meta box arguments.
 *
 *     @type string   $id       Meta box 'id' attribute.
 *     @type string   $title    Meta box title.
 *     @type callable $callback Meta box display callback.
 *     @type array    $args {
 *         Extra meta box arguments.
 *
 *         @type string $taxonomy Taxonomy. Default 'post_tag'.
 *     }
 * }
 */
function post_tags_meta_box( $post, $box ) {
	$defaults = array( 'taxonomy' => 'post_tag' );
	if ( ! isset( $box['args'] ) || ! is_array( $box['args'] ) ) {
		$args = array();
	} else {
		$args = $box['args'];
	}
	$parsed_args           = wp_parse_args( $args, $defaults );
	$tax_name              = esc_attr( $parsed_args['taxonomy'] );
	$taxonomy              = get_taxonomy( $parsed_args['taxonomy'] );
	$user_can_assign_terms = current_user_can( $taxonomy->cap->assign_terms );
	$comma                 = _x( ',', 'tag delimiter' );
	$terms_to_edit         = get_terms_to_edit( $post->ID, $tax_name );
	if ( ! is_string( $terms_to_edit ) ) {
		$terms_to_edit = '';
	}
	?>
<div class="tagsdiv" id="<?php echo $tax_name; ?>">
	<div class="jaxtag">
	<div class="nojs-tags hide-if-js">
		<label for="tax-input-<?php echo $tax_name; ?>"><?php echo $taxonomy->labels->add_or_remove_items; ?></label>
		<p><textarea name="<?php echo "tax_input[$tax_name]"; ?>" rows="3" cols="20" class="the-tags" id="tax-input-<?php echo $tax_name; ?>" <?php disabled( ! $user_can_assign_terms ); ?> aria-describedby="new-tag-<?php echo $tax_name; ?>-desc"><?php echo str_replace( ',', $comma . ' ', $terms_to_edit ); // textarea_escaped by esc_attr() ?></textarea></p>
	</div>
	<?php if ( $user_can_assign_terms ) : ?>
	<div class="ajaxtag hide-if-no-js">
		<label class="screen-reader-text" for="new-tag-<?php echo $tax_name; ?>"><?php echo $taxonomy->labels->add_new_item; ?></label>
		<input data-wp-taxonomy="<?php echo $tax_name; ?>" type="text" id="new-tag-<?php echo $tax_name; ?>" name="newtag[<?php echo $tax_name; ?>]" class="newtag form-input-tip" size="16" autocomplete="off" aria-describedby="new-tag-<?php echo $tax_name; ?>-desc" value="" />
		<input type="button" class="button tagadd" value="<?php esc_attr_e( 'Add' ); ?>" />
	</div>
	<p class="howto" id="new-tag-<?php echo $tax_name; ?>-desc"><?php echo $taxonomy->labels->separate_items_with_commas; ?></p>
	<?php elseif ( empty( $terms_to_edit ) ) : ?>
		<p><?php echo $taxonomy->labels->no_terms; ?></p>
	<?php endif; ?>
	</div>
	<ul class="tagchecklist" role="list"></ul>
</div>
	<?php if ( $user_can_assign_terms ) : ?>
<p class="hide-if-no-js"><button type="button" class="button-link tagcloud-link" id="link-<?php echo $tax_name; ?>" aria-expanded="false"><?php echo $taxonomy->labels->choose_from_most_used; ?></button></p>
<?php endif; ?>
	<?php
}

/**
 * Display post categories form fields.
 *
 * @since 2.6.0
 *
 * @todo Create taxonomy-agnostic wrapper for this.
 *
 * @param WP_Post $post Post object.
 * @param array   $box {
 *     Categories meta box arguments.
 *
 *     @type string   $id       Meta box 'id' attribute.
 *     @type string   $title    Meta box title.
 *     @type callable $callback Meta box display callback.
 *     @type array    $args {
 *         Extra meta box arguments.
 *
 *         @type string $taxonomy Taxonomy. Default 'category'.
 *     }
 * }
 */
function post_categories_meta_box( $post, $box ) {
	$defaults = array( 'taxonomy' => 'category' );
	if ( ! isset( $box['args'] ) || ! is_array( $box['args'] ) ) {
		$args = array();
	} else {
		$args = $box['args'];
	}
	$parsed_args = wp_parse_args( $args, $defaults );
	$tax_name    = esc_attr( $parsed_args['taxonomy'] );
	$taxonomy    = get_taxonomy( $parsed_args['taxonomy'] );
	?>
	<div id="taxonomy-<?php echo $tax_name; ?>" class="categorydiv">
		<ul id="<?php echo $tax_name; ?>-tabs" class="category-tabs">
			<li class="tabs"><a href="#<?php echo $tax_name; ?>-all"><?php echo $taxonomy->labels->all_items; ?></a></li>
			<li class="hide-if-no-js"><a href="#<?php echo $tax_name; ?>-pop"><?php echo esc_html( $taxonomy->labels->most_used ); ?></a></li>
		</ul>

		<div id="<?php echo $tax_name; ?>-pop" class="tabs-panel" style="display: none;">
			<ul id="<?php echo $tax_name; ?>checklist-pop" class="categorychecklist form-no-clear" >
				<?php $popular_ids = wp_popular_terms_checklist( $tax_name ); ?>
			</ul>
		</div>

		<div id="<?php echo $tax_name; ?>-all" class="tabs-panel">
			<?php
			$name = ( 'category' === $tax_name ) ? 'post_category' : 'tax_input[' . $tax_name . ']';
			// Allows for an empty term set to be sent. 0 is an invalid term ID and will be ignored by empty() checks.
			echo "<input type='hidden' name='{$name}[]' value='0' />";
			?>
			<ul id="<?php echo $tax_name; ?>checklist" data-wp-lists="list:<?php echo $tax_name; ?>" class="categorychecklist form-no-clear">
				<?php
				wp_terms_checklist(
					$post->ID,
					array(
						'taxonomy'     => $tax_name,
						'popular_cats' => $popular_ids,
					)
				);
				?>
			</ul>
		</div>
	<?php if ( current_user_can( $taxonomy->cap->edit_terms ) ) : ?>
			<div id="<?php echo $tax_name; ?>-adder" class="wp-hidden-children">
				<a id="<?php echo $tax_name; ?>-add-toggle" href="#<?php echo $tax_name; ?>-add" class="hide-if-no-js taxonomy-add-new">
					<?php
						/* translators: %s: Add New taxonomy label. */
						printf( __( '+ %s' ), $taxonomy->labels->add_new_item );
					?>
				</a>
				<p id="<?php echo $tax_name; ?>-add" class="category-add wp-hidden-child">
					<label class="screen-reader-text" for="new<?php echo $tax_name; ?>"><?php echo $taxonomy->labels->add_new_item; ?></label>
					<input type="text" name="new<?php echo $tax_name; ?>" id="new<?php echo $tax_name; ?>" class="form-required form-input-tip" value="<?php echo esc_attr( $taxonomy->labels->new_item_name ); ?>" aria-required="true"/>
					<label class="screen-reader-text" for="new<?php echo $tax_name; ?>_parent">
						<?php echo $taxonomy->labels->parent_item_colon; ?>
					</label>
					<?php
					$parent_dropdown_args = array(
						'taxonomy'         => $tax_name,
						'hide_empty'       => 0,
						'name'             => 'new' . $tax_name . '_parent',
						'orderby'          => 'name',
						'hierarchical'     => 1,
						'show_option_none' => '&mdash; ' . $taxonomy->labels->parent_item . ' &mdash;',
					);

					/**
					 * Filters the arguments for the taxonomy parent dropdown on the Post Edit page.
					 *
					 * @since 4.4.0
					 *
					 * @param array $parent_dropdown_args {
					 *     Optional. Array of arguments to generate parent dropdown.
					 *
					 *     @type string   $taxonomy         Name of the taxonomy to retrieve.
					 *     @type bool     $hide_if_empty    True to skip generating markup if no
					 *                                      categories are found. Default 0.
					 *     @type string   $name             Value for the 'name' attribute
					 *                                      of the select element.
					 *                                      Default "new{$tax_name}_parent".
					 *     @type string   $orderby          Which column to use for ordering
					 *                                      terms. Default 'name'.
					 *     @type bool|int $hierarchical     Whether to traverse the taxonomy
					 *                                      hierarchy. Default 1.
					 *     @type string   $show_option_none Text to display for the "none" option.
					 *                                      Default "&mdash; {$parent} &mdash;",
					 *                                      where `$parent` is 'parent_item'
					 *                                      taxonomy label.
					 * }
					 */
					$parent_dropdown_args = apply_filters( 'post_edit_category_parent_dropdown_args', $parent_dropdown_args );

					wp_dropdown_categories( $parent_dropdown_args );
					?>
					<input type="button" id="<?php echo $tax_name; ?>-add-submit" data-wp-lists="add:<?php echo $tax_name; ?>checklist:<?php echo $tax_name; ?>-add" class="button category-add-submit" value="<?php echo esc_attr( $taxonomy->labels->add_new_item ); ?>" />
					<?php wp_nonce_field( 'add-' . $tax_name, '_ajax_nonce-add-' . $tax_name, false ); ?>
					<span id="<?php echo $tax_name; ?>-ajax-response"></span>
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
function post_excerpt_meta_box( $post ) {
	?>
<label class="screen-reader-text" for="excerpt"><?php _e( 'Excerpt' ); ?></label><textarea rows="1" cols="40" name="excerpt" id="excerpt"><?php echo $post->post_excerpt; // textarea_escaped ?></textarea>
<p>
	<?php
	printf(
		/* translators: %s: Documentation URL. */
		__( 'Excerpts are optional hand-crafted summaries of your content that can be used in your theme. <a href="%s">Learn more about manual excerpts</a>.' ),
		__( 'https://wordpress.org/support/article/excerpt/' )
	);
	?>
</p>
	<?php
}

/**
 * Display trackback links form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function post_trackback_meta_box( $post ) {
	$form_trackback = '<input type="text" name="trackback_url" id="trackback_url" class="code" value="' .
		esc_attr( str_replace( "\n", ' ', $post->to_ping ) ) . '" aria-describedby="trackback-url-desc" />';
	if ( '' != $post->pinged ) {
		$pings          = '<p>' . __( 'Already pinged:' ) . '</p><ul>';
		$already_pinged = explode( "\n", trim( $post->pinged ) );
		foreach ( $already_pinged as $pinged_url ) {
			$pings .= "\n\t<li>" . esc_html( $pinged_url ) . '</li>';
		}
		$pings .= '</ul>';
	}

	?>
<p>
	<label for="trackback_url"><?php _e( 'Send trackbacks to:' ); ?></label>
	<?php echo $form_trackback; ?>
</p>
<p id="trackback-url-desc" class="howto"><?php _e( 'Separate multiple URLs with spaces' ); ?></p>
<p>
	<?php
	printf(
		/* translators: %s: Documentation URL. */
		__( 'Trackbacks are a way to notify legacy blog systems that you&#8217;ve linked to them. If you link other WordPress sites, they&#8217;ll be notified automatically using <a href="%s">pingbacks</a>, no other action necessary.' ),
		__( 'https://wordpress.org/support/article/introduction-to-blogging/#comments' )
	);
	?>
</p>
	<?php
	if ( ! empty( $pings ) ) {
		echo $pings;
	}
}

/**
 * Display custom fields form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function post_custom_meta_box( $post ) {
	?>
<div id="postcustomstuff">
<div id="ajax-response"></div>
	<?php
	$metadata = has_meta( $post->ID );
	foreach ( $metadata as $key => $value ) {
		if ( is_protected_meta( $metadata[ $key ]['meta_key'], 'post' ) || ! current_user_can( 'edit_post_meta', $post->ID, $metadata[ $key ]['meta_key'] ) ) {
			unset( $metadata[ $key ] );
		}
	}
	list_meta( $metadata );
	meta_form( $post );
	?>
</div>
<p>
	<?php
	printf(
		/* translators: %s: Documentation URL. */
		__( 'Custom fields can be used to add extra metadata to a post that you can <a href="%s">use in your theme</a>.' ),
		__( 'https://wordpress.org/support/article/custom-fields/' )
	);
	?>
</p>
	<?php
}

/**
 * Display comments status form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function post_comment_status_meta_box( $post ) {
	?>
<input name="advanced_view" type="hidden" value="1" />
<p class="meta-options">
	<label for="comment_status" class="selectit"><input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked( $post->comment_status, 'open' ); ?> /> <?php _e( 'Allow comments' ); ?></label><br />
	<label for="ping_status" class="selectit"><input name="ping_status" type="checkbox" id="ping_status" value="open" <?php checked( $post->ping_status, 'open' ); ?> />
		<?php
		printf(
			/* translators: %s: Documentation URL. */
			__( 'Allow <a href="%s">trackbacks and pingbacks</a> on this page' ),
			__( 'https://wordpress.org/support/article/introduction-to-blogging/#managing-comments' )
		);
		?>
	</label>
	<?php
	/**
	 * Fires at the end of the Discussion meta box on the post editing screen.
	 *
	 * @since 3.1.0
	 *
	 * @param WP_Post $post WP_Post object of the current post.
	 */
	do_action( 'post_comment_status_meta_box-options', $post ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	?>
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
function post_comment_meta_box_thead( $result ) {
	unset( $result['cb'], $result['response'] );
	return $result;
}

/**
 * Display comments for post.
 *
 * @since 2.8.0
 *
 * @param object $post
 */
function post_comment_meta_box( $post ) {
	wp_nonce_field( 'get-comments', 'add_comment_nonce', false );
	?>
	<p class="hide-if-no-js" id="add-new-comment"><button type="button" class="button" onclick="window.commentReply && commentReply.addcomment(<?php echo $post->ID; ?>);"><?php _e( 'Add Comment' ); ?></button></p>
	<?php

	$total         = get_comments(
		array(
			'post_id' => $post->ID,
			'number'  => 1,
			'count'   => true,
		)
	);
	$wp_list_table = _get_list_table( 'WP_Post_Comments_List_Table' );
	$wp_list_table->display( true );

	if ( 1 > $total ) {
		echo '<p id="no-comments">' . __( 'No comments yet.' ) . '</p>';
	} else {
		$hidden = get_hidden_meta_boxes( get_current_screen() );
		if ( ! in_array( 'commentsdiv', $hidden ) ) {
			?>
			<script type="text/javascript">jQuery(document).ready(function(){commentsBox.get(<?php echo $total; ?>, 10);});</script>
			<?php
		}

		?>
		<p class="hide-if-no-js" id="show-comments"><a href="#commentstatusdiv" onclick="commentsBox.load(<?php echo $total; ?>);return false;"><?php _e( 'Show comments' ); ?></a> <span class="spinner"></span></p>
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
function post_slug_meta_box( $post ) {
	/** This filter is documented in wp-admin/edit-tag-form.php */
	$editable_slug = apply_filters( 'editable_slug', $post->post_name, $post );
	?>
<label class="screen-reader-text" for="post_name"><?php _e( 'Slug' ); ?></label><input name="post_name" type="text" size="13" id="post_name" value="<?php echo esc_attr( $editable_slug ); ?>" />
	<?php
}

/**
 * Display form field with list of authors.
 *
 * @since 2.6.0
 *
 * @global int $user_ID
 *
 * @param object $post
 */
function post_author_meta_box( $post ) {
	global $user_ID;
	?>
<label class="screen-reader-text" for="post_author_override"><?php _e( 'Author' ); ?></label>
	<?php
	wp_dropdown_users(
		array(
			'who'              => 'authors',
			'name'             => 'post_author_override',
			'selected'         => empty( $post->ID ) ? $user_ID : $post->post_author,
			'include_selected' => true,
			'show'             => 'display_name_with_login',
		)
	);
}

/**
 * Display list of revisions.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function post_revisions_meta_box( $post ) {
	wp_list_post_revisions( $post );
}

//
// Page-related Meta Boxes.
//

/**
 * Display page attributes form fields.
 *
 * @since 2.7.0
 *
 * @param object $post
 */
function page_attributes_meta_box( $post ) {
	if ( is_post_type_hierarchical( $post->post_type ) ) :
		$dropdown_args = array(
			'post_type'        => $post->post_type,
			'exclude_tree'     => $post->ID,
			'selected'         => $post->post_parent,
			'name'             => 'parent_id',
			'show_option_none' => __( '(no parent)' ),
			'sort_column'      => 'menu_order, post_title',
			'echo'             => 0,
		);

		/**
		 * Filters the arguments used to generate a Pages drop-down element.
		 *
		 * @since 3.3.0
		 *
		 * @see wp_dropdown_pages()
		 *
		 * @param array   $dropdown_args Array of arguments used to generate the pages drop-down.
		 * @param WP_Post $post          The current post.
		 */
		$dropdown_args = apply_filters( 'page_attributes_dropdown_pages_args', $dropdown_args, $post );
		$pages         = wp_dropdown_pages( $dropdown_args );
		if ( ! empty( $pages ) ) :
			?>
<p class="post-attributes-label-wrapper parent-id-label-wrapper"><label class="post-attributes-label" for="parent_id"><?php _e( 'Parent' ); ?></label></p>
			<?php echo $pages; ?>
			<?php
		endif; // End empty pages check.
	endif;  // End hierarchical check.

	if ( count( get_page_templates( $post ) ) > 0 && get_option( 'page_for_posts' ) != $post->ID ) :
		$template = ! empty( $post->page_template ) ? $post->page_template : false;
		?>
<p class="post-attributes-label-wrapper page-template-label-wrapper"><label class="post-attributes-label" for="page_template"><?php _e( 'Template' ); ?></label>
		<?php
		/**
		 * Fires immediately after the label inside the 'Template' section
		 * of the 'Page Attributes' meta box.
		 *
		 * @since 4.4.0
		 *
		 * @param string  $template The template used for the current post.
		 * @param WP_Post $post     The current post.
		 */
		do_action( 'page_attributes_meta_box_template', $template, $post );
		?>
</p>
<select name="page_template" id="page_template">
		<?php
		/**
		 * Filters the title of the default page template displayed in the drop-down.
		 *
		 * @since 4.1.0
		 *
		 * @param string $label   The display value for the default page template title.
		 * @param string $context Where the option label is displayed. Possible values
		 *                        include 'meta-box' or 'quick-edit'.
		 */
		$default_title = apply_filters( 'default_page_template_title', __( 'Default Template' ), 'meta-box' );
		?>
<option value="default"><?php echo esc_html( $default_title ); ?></option>
		<?php page_template_dropdown( $template, $post->post_type ); ?>
</select>
<?php endif; ?>
	<?php if ( post_type_supports( $post->post_type, 'page-attributes' ) ) : ?>
<p class="post-attributes-label-wrapper menu-order-label-wrapper"><label class="post-attributes-label" for="menu_order"><?php _e( 'Order' ); ?></label></p>
<input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo esc_attr( $post->menu_order ); ?>" />
		<?php
		/**
		 * Fires before the help hint text in the 'Page Attributes' meta box.
		 *
		 * @since 4.9.0
		 *
		 * @param WP_Post $post The current post.
		 */
		do_action( 'page_attributes_misc_attributes', $post );
		?>
		<?php if ( 'page' == $post->post_type && get_current_screen()->get_help_tabs() ) : ?>
<p class="post-attributes-help-text"><?php _e( 'Need help? Use the Help tab above the screen title.' ); ?></p>
			<?php
	endif;
	endif;
}

//
// Link-related Meta Boxes.
//

/**
 * Display link create form fields.
 *
 * @since 2.7.0
 *
 * @param object $link
 */
function link_submit_meta_box( $link ) {
	?>
<div class="submitbox" id="submitlink">

<div id="minor-publishing">

	<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key. ?>
<div style="display:none;">
	<?php submit_button( __( 'Save' ), '', 'save', false ); ?>
</div>

<div id="minor-publishing-actions">
<div id="preview-action">
	<?php if ( ! empty( $link->link_id ) ) { ?>
	<a class="preview button" href="<?php echo $link->link_url; ?>" target="_blank"><?php _e( 'Visit Link' ); ?></a>
<?php } ?>
</div>
<div class="clear"></div>
</div>

<div id="misc-publishing-actions">
<div class="misc-pub-section misc-pub-private">
	<label for="link_private" class="selectit"><input id="link_private" name="link_visible" type="checkbox" value="N" <?php checked( $link->link_visible, 'N' ); ?> /> <?php _e( 'Keep this link private' ); ?></label>
</div>
</div>

</div>

<div id="major-publishing-actions">
	<?php
	/** This action is documented in wp-admin/includes/meta-boxes.php */
	do_action( 'post_submitbox_start', null );
	?>
<div id="delete-action">
	<?php
	if ( ! empty( $_GET['action'] ) && 'edit' == $_GET['action'] && current_user_can( 'manage_links' ) ) {
		printf(
			'<a class="submitdelete deletion" href="%s" onclick="return confirm( \'%s\' );">%s</a>',
			wp_nonce_url( "link.php?action=delete&amp;link_id=$link->link_id", 'delete-bookmark_' . $link->link_id ),
			/* translators: %s: Link name. */
			esc_js( sprintf( __( "You are about to delete this link '%s'\n  'Cancel' to stop, 'OK' to delete." ), $link->link_name ) ),
			__( 'Delete' )
		);
	}
	?>
</div>

<div id="publishing-action">
	<?php if ( ! empty( $link->link_id ) ) { ?>
	<input name="save" type="submit" class="button button-primary button-large" id="publish" value="<?php esc_attr_e( 'Update Link' ); ?>" />
<?php } else { ?>
	<input name="save" type="submit" class="button button-primary button-large" id="publish" value="<?php esc_attr_e( 'Add Link' ); ?>" />
<?php } ?>
</div>
<div class="clear"></div>
</div>
	<?php
	/**
	 * Fires at the end of the Publish box in the Link editing screen.
	 *
	 * @since 2.5.0
	 */
	do_action( 'submitlink_box' );
	?>
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
function link_categories_meta_box( $link ) {
	?>
<div id="taxonomy-linkcategory" class="categorydiv">
	<ul id="category-tabs" class="category-tabs">
		<li class="tabs"><a href="#categories-all"><?php _e( 'All Categories' ); ?></a></li>
		<li class="hide-if-no-js"><a href="#categories-pop"><?php _ex( 'Most Used', 'categories' ); ?></a></li>
	</ul>

	<div id="categories-all" class="tabs-panel">
		<ul id="categorychecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">
			<?php
			if ( isset( $link->link_id ) ) {
				wp_link_category_checklist( $link->link_id );
			} else {
				wp_link_category_checklist();
			}
			?>
		</ul>
	</div>

	<div id="categories-pop" class="tabs-panel" style="display: none;">
		<ul id="categorychecklist-pop" class="categorychecklist form-no-clear">
			<?php wp_popular_terms_checklist( 'link_category' ); ?>
		</ul>
	</div>

	<div id="category-adder" class="wp-hidden-children">
		<a id="category-add-toggle" href="#category-add" class="taxonomy-add-new"><?php _e( '+ Add New Category' ); ?></a>
		<p id="link-category-add" class="wp-hidden-child">
			<label class="screen-reader-text" for="newcat"><?php _e( '+ Add New Category' ); ?></label>
			<input type="text" name="newcat" id="newcat" class="form-required form-input-tip" value="<?php esc_attr_e( 'New category name' ); ?>" aria-required="true" />
			<input type="button" id="link-category-add-submit" data-wp-lists="add:categorychecklist:link-category-add" class="button" value="<?php esc_attr_e( 'Add' ); ?>" />
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
function link_target_meta_box( $link ) {

	?>
<fieldset><legend class="screen-reader-text"><span><?php _e( 'Target' ); ?></span></legend>
<p><label for="link_target_blank" class="selectit">
<input id="link_target_blank" type="radio" name="link_target" value="_blank" <?php echo ( isset( $link->link_target ) && ( '_blank' === $link->link_target ) ? 'checked="checked"' : '' ); ?> />
	<?php _e( '<code>_blank</code> &mdash; new window or tab.' ); ?></label></p>
<p><label for="link_target_top" class="selectit">
<input id="link_target_top" type="radio" name="link_target" value="_top" <?php echo ( isset( $link->link_target ) && ( '_top' === $link->link_target ) ? 'checked="checked"' : '' ); ?> />
	<?php _e( '<code>_top</code> &mdash; current window or tab, with no frames.' ); ?></label></p>
<p><label for="link_target_none" class="selectit">
<input id="link_target_none" type="radio" name="link_target" value="" <?php echo ( isset( $link->link_target ) && ( '' === $link->link_target ) ? 'checked="checked"' : '' ); ?> />
	<?php _e( '<code>_none</code> &mdash; same window or tab.' ); ?></label></p>
</fieldset>
<p><?php _e( 'Choose the target frame for your link.' ); ?></p>
	<?php
}

/**
 * Display checked checkboxes attribute for xfn microformat options.
 *
 * @since 1.0.1
 *
 * @global object $link
 *
 * @param string $class
 * @param string $value
 * @param mixed $deprecated Never used.
 */
function xfn_check( $class, $value = '', $deprecated = '' ) {
	global $link;

	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '2.5.0' ); // Never implemented.
	}

	$link_rel = isset( $link->link_rel ) ? $link->link_rel : ''; // In PHP 5.3: $link_rel = $link->link_rel ?: '';
	$rels     = preg_split( '/\s+/', $link_rel );

	if ( '' != $value && in_array( $value, $rels ) ) {
		echo ' checked="checked"';
	}

	if ( '' == $value ) {
		if ( 'family' == $class && strpos( $link_rel, 'child' ) === false && strpos( $link_rel, 'parent' ) === false && strpos( $link_rel, 'sibling' ) === false && strpos( $link_rel, 'spouse' ) === false && strpos( $link_rel, 'kin' ) === false ) {
			echo ' checked="checked"';
		}
		if ( 'friendship' == $class && strpos( $link_rel, 'friend' ) === false && strpos( $link_rel, 'acquaintance' ) === false && strpos( $link_rel, 'contact' ) === false ) {
			echo ' checked="checked"';
		}
		if ( 'geographical' == $class && strpos( $link_rel, 'co-resident' ) === false && strpos( $link_rel, 'neighbor' ) === false ) {
			echo ' checked="checked"';
		}
		if ( 'identity' == $class && in_array( 'me', $rels ) ) {
			echo ' checked="checked"';
		}
	}
}

/**
 * Display xfn form fields.
 *
 * @since 2.6.0
 *
 * @param object $link
 */
function link_xfn_meta_box( $link ) {
	?>
<table class="links-table">
	<tr>
		<th scope="row"><label for="link_rel"><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'rel:' ); ?></label></th>
		<td><input type="text" name="link_rel" id="link_rel" value="<?php echo ( isset( $link->link_rel ) ? esc_attr( $link->link_rel ) : '' ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'identity' ); ?></th>
		<td><fieldset><legend class="screen-reader-text"><span><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'identity' ); ?></span></legend>
			<label for="me">
			<input type="checkbox" name="identity" value="me" id="me" <?php xfn_check( 'identity', 'me' ); ?> />
			<?php _e( 'another web address of mine' ); ?></label>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'friendship' ); ?></th>
		<td><fieldset><legend class="screen-reader-text"><span><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'friendship' ); ?></span></legend>
			<label for="contact">
			<input class="valinp" type="radio" name="friendship" value="contact" id="contact" <?php xfn_check( 'friendship', 'contact' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'contact' ); ?>
			</label>
			<label for="acquaintance">
			<input class="valinp" type="radio" name="friendship" value="acquaintance" id="acquaintance" <?php xfn_check( 'friendship', 'acquaintance' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'acquaintance' ); ?>
			</label>
			<label for="friend">
			<input class="valinp" type="radio" name="friendship" value="friend" id="friend" <?php xfn_check( 'friendship', 'friend' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'friend' ); ?>
			</label>
			<label for="friendship">
			<input name="friendship" type="radio" class="valinp" value="" id="friendship" <?php xfn_check( 'friendship' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'none' ); ?>
			</label>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"> <?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'physical' ); ?> </th>
		<td><fieldset><legend class="screen-reader-text"><span><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'physical' ); ?></span></legend>
			<label for="met">
			<input class="valinp" type="checkbox" name="physical" value="met" id="met" <?php xfn_check( 'physical', 'met' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'met' ); ?>
			</label>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"> <?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'professional' ); ?> </th>
		<td><fieldset><legend class="screen-reader-text"><span><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'professional' ); ?></span></legend>
			<label for="co-worker">
			<input class="valinp" type="checkbox" name="professional" value="co-worker" id="co-worker" <?php xfn_check( 'professional', 'co-worker' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'co-worker' ); ?>
			</label>
			<label for="colleague">
			<input class="valinp" type="checkbox" name="professional" value="colleague" id="colleague" <?php xfn_check( 'professional', 'colleague' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'colleague' ); ?>
			</label>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'geographical' ); ?></th>
		<td><fieldset><legend class="screen-reader-text"><span> <?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'geographical' ); ?> </span></legend>
			<label for="co-resident">
			<input class="valinp" type="radio" name="geographical" value="co-resident" id="co-resident" <?php xfn_check( 'geographical', 'co-resident' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'co-resident' ); ?>
			</label>
			<label for="neighbor">
			<input class="valinp" type="radio" name="geographical" value="neighbor" id="neighbor" <?php xfn_check( 'geographical', 'neighbor' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'neighbor' ); ?>
			</label>
			<label for="geographical">
			<input class="valinp" type="radio" name="geographical" value="" id="geographical" <?php xfn_check( 'geographical' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'none' ); ?>
			</label>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'family' ); ?></th>
		<td><fieldset><legend class="screen-reader-text"><span> <?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'family' ); ?> </span></legend>
			<label for="child">
			<input class="valinp" type="radio" name="family" value="child" id="child" <?php xfn_check( 'family', 'child' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'child' ); ?>
			</label>
			<label for="kin">
			<input class="valinp" type="radio" name="family" value="kin" id="kin" <?php xfn_check( 'family', 'kin' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'kin' ); ?>
			</label>
			<label for="parent">
			<input class="valinp" type="radio" name="family" value="parent" id="parent" <?php xfn_check( 'family', 'parent' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'parent' ); ?>
			</label>
			<label for="sibling">
			<input class="valinp" type="radio" name="family" value="sibling" id="sibling" <?php xfn_check( 'family', 'sibling' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'sibling' ); ?>
			</label>
			<label for="spouse">
			<input class="valinp" type="radio" name="family" value="spouse" id="spouse" <?php xfn_check( 'family', 'spouse' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'spouse' ); ?>
			</label>
			<label for="family">
			<input class="valinp" type="radio" name="family" value="" id="family" <?php xfn_check( 'family' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'none' ); ?>
			</label>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"><?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'romantic' ); ?></th>
		<td><fieldset><legend class="screen-reader-text"><span> <?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'romantic' ); ?> </span></legend>
			<label for="muse">
			<input class="valinp" type="checkbox" name="romantic" value="muse" id="muse" <?php xfn_check( 'romantic', 'muse' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'muse' ); ?>
			</label>
			<label for="crush">
			<input class="valinp" type="checkbox" name="romantic" value="crush" id="crush" <?php xfn_check( 'romantic', 'crush' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'crush' ); ?>
			</label>
			<label for="date">
			<input class="valinp" type="checkbox" name="romantic" value="date" id="date" <?php xfn_check( 'romantic', 'date' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'date' ); ?>
			</label>
			<label for="romantic">
			<input class="valinp" type="checkbox" name="romantic" value="sweetheart" id="romantic" <?php xfn_check( 'romantic', 'sweetheart' ); ?> />&nbsp;<?php /* translators: xfn: http://gmpg.org/xfn/ */ _e( 'sweetheart' ); ?>
			</label>
		</fieldset></td>
	</tr>

</table>
<p><?php _e( 'If the link is to a person, you can specify your relationship with them using the above form. If you would like to learn more about the idea check out <a href="http://gmpg.org/xfn/">XFN</a>.' ); ?></p>
	<?php
}

/**
 * Display advanced link options form fields.
 *
 * @since 2.6.0
 *
 * @param object $link
 */
function link_advanced_meta_box( $link ) {
	?>
<table class="links-table" cellpadding="0">
	<tr>
		<th scope="row"><label for="link_image"><?php _e( 'Image Address' ); ?></label></th>
		<td><input type="text" name="link_image" class="code" id="link_image" maxlength="255" value="<?php echo ( isset( $link->link_image ) ? esc_attr( $link->link_image ) : '' ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="rss_uri"><?php _e( 'RSS Address' ); ?></label></th>
		<td><input name="link_rss" class="code" type="text" id="rss_uri" maxlength="255" value="<?php echo ( isset( $link->link_rss ) ? esc_attr( $link->link_rss ) : '' ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="link_notes"><?php _e( 'Notes' ); ?></label></th>
		<td><textarea name="link_notes" id="link_notes" rows="10"><?php echo ( isset( $link->link_notes ) ? $link->link_notes : '' ); // textarea_escaped ?></textarea></td>
	</tr>
	<tr>
		<th scope="row"><label for="link_rating"><?php _e( 'Rating' ); ?></label></th>
		<td><select name="link_rating" id="link_rating" size="1">
		<?php
		for ( $parsed_args = 0; $parsed_args <= 10; $parsed_args++ ) {
			echo '<option value="' . $parsed_args . '"';
			if ( isset( $link->link_rating ) && $link->link_rating == $parsed_args ) {
				echo ' selected="selected"';
			}
			echo( '>' . $parsed_args . '</option>' );
		}
		?>
		</select>&nbsp;<?php _e( '(Leave at 0 for no rating.)' ); ?>
		</td>
	</tr>
</table>
	<?php
}

/**
 * Display post thumbnail meta box.
 *
 * @since 2.9.0
 *
 * @param WP_Post $post A post object.
 */
function post_thumbnail_meta_box( $post ) {
	$thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );
	echo _wp_post_thumbnail_html( $thumbnail_id, $post->ID );
}

/**
 * Display fields for ID3 data
 *
 * @since 3.9.0
 *
 * @param WP_Post $post A post object.
 */
function attachment_id3_data_meta_box( $post ) {
	$meta = array();
	if ( ! empty( $post->ID ) ) {
		$meta = wp_get_attachment_metadata( $post->ID );
	}

	foreach ( wp_get_attachment_id3_keys( $post, 'edit' ) as $key => $label ) :
		$value = '';
		if ( ! empty( $meta[ $key ] ) ) {
			$value = $meta[ $key ];
		}
		?>
	<p>
		<label for="title"><?php echo $label; ?></label><br />
		<input type="text" name="id3_<?php echo esc_attr( $key ); ?>" id="id3_<?php echo esc_attr( $key ); ?>" class="large-text" value="<?php echo esc_attr( $value ); ?>" />
	</p>
		<?php
	endforeach;
}

/**
 * Registers the default post meta boxes, and runs the `do_meta_boxes` actions.
 *
 * @since 5.0.0
 *
 * @param WP_Post $post The post object that these meta boxes are being generated for.
 */
function register_and_do_post_meta_boxes( $post ) {
	$post_type        = $post->post_type;
	$post_type_object = get_post_type_object( $post_type );

	$thumbnail_support = current_theme_supports( 'post-thumbnails', $post_type ) && post_type_supports( $post_type, 'thumbnail' );
	if ( ! $thumbnail_support && 'attachment' === $post_type && $post->post_mime_type ) {
		if ( wp_attachment_is( 'audio', $post ) ) {
			$thumbnail_support = post_type_supports( 'attachment:audio', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:audio' );
		} elseif ( wp_attachment_is( 'video', $post ) ) {
			$thumbnail_support = post_type_supports( 'attachment:video', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:video' );
		}
	}

	$publish_callback_args = array( '__back_compat_meta_box' => true );
	if ( post_type_supports( $post_type, 'revisions' ) && 'auto-draft' != $post->post_status ) {
		$revisions = wp_get_post_revisions( $post->ID );

		// We should aim to show the revisions meta box only when there are revisions.
		if ( count( $revisions ) > 1 ) {
			reset( $revisions ); // Reset pointer for key().
			$publish_callback_args = array(
				'revisions_count'        => count( $revisions ),
				'revision_id'            => key( $revisions ),
				'__back_compat_meta_box' => true,
			);
			add_meta_box( 'revisionsdiv', __( 'Revisions' ), 'post_revisions_meta_box', null, 'normal', 'core', array( '__back_compat_meta_box' => true ) );
		}
	}

	if ( 'attachment' == $post_type ) {
		wp_enqueue_script( 'image-edit' );
		wp_enqueue_style( 'imgareaselect' );
		add_meta_box( 'submitdiv', __( 'Save' ), 'attachment_submit_meta_box', null, 'side', 'core', array( '__back_compat_meta_box' => true ) );
		add_action( 'edit_form_after_title', 'edit_form_image_editor' );

		if ( wp_attachment_is( 'audio', $post ) ) {
			add_meta_box( 'attachment-id3', __( 'Metadata' ), 'attachment_id3_data_meta_box', null, 'normal', 'core', array( '__back_compat_meta_box' => true ) );
		}
	} else {
		add_meta_box( 'submitdiv', __( 'Publish' ), 'post_submit_meta_box', null, 'side', 'core', $publish_callback_args );
	}

	if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post_type, 'post-formats' ) ) {
		add_meta_box( 'formatdiv', _x( 'Format', 'post format' ), 'post_format_meta_box', null, 'side', 'core', array( '__back_compat_meta_box' => true ) );
	}

	// All taxonomies.
	foreach ( get_object_taxonomies( $post ) as $tax_name ) {
		$taxonomy = get_taxonomy( $tax_name );
		if ( ! $taxonomy->show_ui || false === $taxonomy->meta_box_cb ) {
			continue;
		}

		$label = $taxonomy->labels->name;

		if ( ! is_taxonomy_hierarchical( $tax_name ) ) {
			$tax_meta_box_id = 'tagsdiv-' . $tax_name;
		} else {
			$tax_meta_box_id = $tax_name . 'div';
		}

		add_meta_box(
			$tax_meta_box_id,
			$label,
			$taxonomy->meta_box_cb,
			null,
			'side',
			'core',
			array(
				'taxonomy'               => $tax_name,
				'__back_compat_meta_box' => true,
			)
		);
	}

	if ( post_type_supports( $post_type, 'page-attributes' ) || count( get_page_templates( $post ) ) > 0 ) {
		add_meta_box( 'pageparentdiv', $post_type_object->labels->attributes, 'page_attributes_meta_box', null, 'side', 'core', array( '__back_compat_meta_box' => true ) );
	}

	if ( $thumbnail_support && current_user_can( 'upload_files' ) ) {
		add_meta_box( 'postimagediv', esc_html( $post_type_object->labels->featured_image ), 'post_thumbnail_meta_box', null, 'side', 'low', array( '__back_compat_meta_box' => true ) );
	}

	if ( post_type_supports( $post_type, 'excerpt' ) ) {
		add_meta_box( 'postexcerpt', __( 'Excerpt' ), 'post_excerpt_meta_box', null, 'normal', 'core', array( '__back_compat_meta_box' => true ) );
	}

	if ( post_type_supports( $post_type, 'trackbacks' ) ) {
		add_meta_box( 'trackbacksdiv', __( 'Send Trackbacks' ), 'post_trackback_meta_box', null, 'normal', 'core', array( '__back_compat_meta_box' => true ) );
	}

	if ( post_type_supports( $post_type, 'custom-fields' ) ) {
		add_meta_box(
			'postcustom',
			__( 'Custom Fields' ),
			'post_custom_meta_box',
			null,
			'normal',
			'core',
			array(
				'__back_compat_meta_box'             => ! (bool) get_user_meta( get_current_user_id(), 'enable_custom_fields', true ),
				'__block_editor_compatible_meta_box' => true,
			)
		);
	}

	/**
	 * Fires in the middle of built-in meta box registration.
	 *
	 * @since 2.1.0
	 * @deprecated 3.7.0 Use {@see 'add_meta_boxes'} instead.
	 *
	 * @param WP_Post $post Post object.
	 */
	do_action_deprecated( 'dbx_post_advanced', array( $post ), '3.7.0', 'add_meta_boxes' );

	// Allow the Discussion meta box to show up if the post type supports comments,
	// or if comments or pings are open.
	if ( comments_open( $post ) || pings_open( $post ) || post_type_supports( $post_type, 'comments' ) ) {
		add_meta_box( 'commentstatusdiv', __( 'Discussion' ), 'post_comment_status_meta_box', null, 'normal', 'core', array( '__back_compat_meta_box' => true ) );
	}

	$stati = get_post_stati( array( 'public' => true ) );
	if ( empty( $stati ) ) {
		$stati = array( 'publish' );
	}
	$stati[] = 'private';

	if ( in_array( get_post_status( $post ), $stati ) ) {
		// If the post type support comments, or the post has comments,
		// allow the Comments meta box.
		if ( comments_open( $post ) || pings_open( $post ) || $post->comment_count > 0 || post_type_supports( $post_type, 'comments' ) ) {
			add_meta_box( 'commentsdiv', __( 'Comments' ), 'post_comment_meta_box', null, 'normal', 'core', array( '__back_compat_meta_box' => true ) );
		}
	}

	if ( ! ( 'pending' == get_post_status( $post ) && ! current_user_can( $post_type_object->cap->publish_posts ) ) ) {
		add_meta_box( 'slugdiv', __( 'Slug' ), 'post_slug_meta_box', null, 'normal', 'core', array( '__back_compat_meta_box' => true ) );
	}

	if ( post_type_supports( $post_type, 'author' ) && current_user_can( $post_type_object->cap->edit_others_posts ) ) {
		add_meta_box( 'authordiv', __( 'Author' ), 'post_author_meta_box', null, 'normal', 'core', array( '__back_compat_meta_box' => true ) );
	}

	/**
	 * Fires after all built-in meta boxes have been added.
	 *
	 * @since 3.0.0
	 *
	 * @param string  $post_type Post type.
	 * @param WP_Post $post      Post object.
	 */
	do_action( 'add_meta_boxes', $post_type, $post );

	/**
	 * Fires after all built-in meta boxes have been added, contextually for the given post type.
	 *
	 * The dynamic portion of the hook, `$post_type`, refers to the post type of the post.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_Post $post Post object.
	 */
	do_action( "add_meta_boxes_{$post_type}", $post );

	/**
	 * Fires after meta boxes have been added.
	 *
	 * Fires once for each of the default meta box contexts: normal, advanced, and side.
	 *
	 * @since 3.0.0
	 *
	 * @param string                $post_type Post type of the post on Edit Post screen, 'link' on Edit Link screen,
	 *                                         'dashboard' on Dashboard screen.
	 * @param string                $context   Meta box context. Possible values include 'normal', 'advanced', 'side'.
	 * @param WP_Post|object|string $post      Post object on Edit Post screen, link object on Edit Link screen,
	 *                                         an empty string on Dashboard screen.
	 */
	do_action( 'do_meta_boxes', $post_type, 'normal', $post );
	/** This action is documented in wp-admin/includes/meta-boxes.php */
	do_action( 'do_meta_boxes', $post_type, 'advanced', $post );
	/** This action is documented in wp-admin/includes/meta-boxes.php */
	do_action( 'do_meta_boxes', $post_type, 'side', $post );
}
