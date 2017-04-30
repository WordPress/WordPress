<?php

// don't load directly
if ( ! defined( 'ABSPATH' ) )
	die( '-1' );

?><div class="wrap">

<h2><?php
	if ( $post->initial() ) {
		echo esc_html( __( 'Add New Contact Form', 'contact-form-7' ) );
	} else {
		echo esc_html( __( 'Edit Contact Form', 'contact-form-7' ) );

		echo ' <a href="' . esc_url( menu_page_url( 'wpcf7-new', false ) ) . '" class="add-new-h2">' . esc_html( __( 'Add New', 'contact-form-7' ) ) . '</a>';
	}
?></h2>

<?php do_action( 'wpcf7_admin_notices' ); ?>

<br class="clear" />

<?php
if ( $post ) :

	if ( current_user_can( 'wpcf7_edit_contact_form', $post_id ) )
		$disabled = '';
	else
		$disabled = ' disabled="disabled"';
?>

<form method="post" action="<?php echo esc_url( add_query_arg( array( 'post' => $post_id ), menu_page_url( 'wpcf7', false ) ) ); ?>" id="wpcf7-admin-form-element"<?php do_action( 'wpcf7_post_edit_form_tag' ); ?>>
	<?php if ( current_user_can( 'wpcf7_edit_contact_form', $post_id ) )
		wp_nonce_field( 'wpcf7-save-contact-form_' . $post_id ); ?>
	<input type="hidden" id="post_ID" name="post_ID" value="<?php echo (int) $post_id; ?>" />
	<input type="hidden" id="wpcf7-id" name="wpcf7-id" value="<?php echo (int) get_post_meta( $post->id(), '_old_cf7_unit_id', true ); ?>" />
	<input type="hidden" id="wpcf7-locale" name="wpcf7-locale" value="<?php echo esc_attr( $post->locale ); ?>" />
	<input type="hidden" id="hiddenaction" name="action" value="save" />

	<div id="poststuff" class="metabox-holder">

	<div id="titlediv">
		<input type="text" id="wpcf7-title" name="wpcf7-title" size="80" value="<?php echo esc_attr( $post->title() ); ?>"<?php echo $disabled; ?> />

		<?php if ( ! $post->initial() ) : ?>
		<p class="tagcode">
			<?php echo esc_html( __( "Copy this code and paste it into your post, page or text widget content.", 'contact-form-7' ) ); ?><br />

			<input type="text" id="contact-form-anchor-text" onfocus="this.select();" readonly="readonly" class="wp-ui-text-highlight code" />
		</p>

		<p class="tagcode" style="display: none;">
			<?php echo esc_html( __( "Old code is also available.", 'contact-form-7' ) ); ?><br />

			<input type="text" id="contact-form-anchor-text-old" onfocus="this.select();" readonly="readonly" class="wp-ui-text-highlight code" />
		</p>
		<?php endif; ?>

		<?php if ( current_user_can( 'wpcf7_edit_contact_form', $post_id ) ) : ?>
		<div class="save-contact-form">
			<input type="submit" class="button-primary" name="wpcf7-save" value="<?php echo esc_attr( __( 'Save', 'contact-form-7' ) ); ?>" />
		</div>
		<?php endif; ?>

		<?php if ( current_user_can( 'wpcf7_edit_contact_form', $post_id ) && ! $post->initial() ) : ?>
		<div class="actions-link">
			<?php $copy_nonce = wp_create_nonce( 'wpcf7-copy-contact-form_' . $post_id ); ?>
			<input type="submit" name="wpcf7-copy" class="copy" value="<?php echo esc_attr( __( 'Duplicate', 'contact-form-7' ) ); ?>"
			<?php echo "onclick=\"this.form._wpnonce.value = '$copy_nonce'; this.form.action.value = 'copy'; return true;\""; ?> />
			|

			<?php $delete_nonce = wp_create_nonce( 'wpcf7-delete-contact-form_' . $post_id ); ?>
			<input type="submit" name="wpcf7-delete" class="delete" value="<?php echo esc_attr( __( 'Delete', 'contact-form-7' ) ); ?>"
			<?php echo "onclick=\"if (confirm('" .
				esc_js( __( "You are about to delete this contact form.\n  'Cancel' to stop, 'OK' to delete.", 'contact-form-7' ) ) .
				"')) {this.form._wpnonce.value = '$delete_nonce'; this.form.action.value = 'delete'; return true;} return false;\""; ?> />
		</div>
		<?php endif; ?>
	</div>

<?php

do_action( 'wpcf7_admin_after_general_settings', $post );

do_meta_boxes( null, 'form', $post );

do_action( 'wpcf7_admin_after_form', $post );

do_meta_boxes( null, 'mail', $post );

do_action( 'wpcf7_admin_after_mail', $post );

do_meta_boxes( null, 'mail_2', $post );

do_action( 'wpcf7_admin_after_mail_2', $post );

do_meta_boxes( null, 'messages', $post );

do_action( 'wpcf7_admin_after_messages', $post );

do_meta_boxes( null, 'additional_settings', $post );

do_action( 'wpcf7_admin_after_additional_settings', $post );

wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );

?>
	</div>

</form>

<?php endif; ?>

</div>

<?php do_action( 'wpcf7_admin_footer', $post ); ?>
