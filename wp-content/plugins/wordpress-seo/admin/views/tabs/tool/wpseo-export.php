<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Views
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/* translators: %1$s expands to Yoast SEO */
$submit_button_value = sprintf( __( 'Export your %1$s settings', 'wordpress-seo' ), 'Yoast SEO' );

// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: The nonce will be verified in WPSEO_Export below, We are only strictly comparing with '1'.
if ( isset( $_POST['do_export'] ) && wp_unslash( $_POST['do_export'] ) === '1' ) {
	$export = new WPSEO_Export();
	$export->export();
	return;
}

$wpseo_export_phrase = sprintf(
	/* translators: %1$s expands to Yoast SEO */
	__( 'Export your %1$s settings here, to copy them on another site.', 'wordpress-seo' ),
	'Yoast SEO'
);
?>

<p><?php echo esc_html( $wpseo_export_phrase ); ?></p>
<form
	action="<?php echo esc_url( admin_url( 'admin.php?page=wpseo_tools&tool=import-export#top#wpseo-export' ) ); ?>"
	method="post"
	accept-charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
	<?php wp_nonce_field( WPSEO_Export::NONCE_ACTION ); ?>
	<input type="hidden" name="do_export" value="1" />
	<button type="submit" class="button button-primary" id="export-button"><?php echo esc_html( $submit_button_value ); ?></button>
</form>
