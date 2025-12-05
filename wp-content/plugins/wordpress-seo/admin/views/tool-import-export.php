<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

$yform            = Yoast_Form::get_instance();
$yoast_seo_import = false;

/**
 * The import method is used to determine if there should be something imported.
 *
 * In case of POST the user is on the Yoast SEO import page and in case of the GET the user sees a notice from
 * Yoast SEO that we can import stuff for that plugin.
 */
// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are only comparing the variable so no need to sanitize.
if ( isset( $_POST['import_external'] ) && wp_unslash( $_POST['import_external'] ) === __( 'Import', 'wordpress-seo' ) ) {
	check_admin_referer( 'wpseo-import-plugins' );
	if ( isset( $_POST['import_external_plugin'] ) && is_string( $_POST['import_external_plugin'] ) ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are using the variable as a class name.
		$yoast_seo_class = wp_unslash( $_POST['import_external_plugin'] );
		if ( class_exists( $yoast_seo_class ) ) {
			$yoast_seo_import = new WPSEO_Import_Plugin( new $yoast_seo_class(), 'import' );
		}
	}
}
// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are only comparing the variable so no need to sanitize.
elseif ( isset( $_POST['clean_external'] ) && wp_unslash( $_POST['clean_external'] ) === __( 'Clean up', 'wordpress-seo' ) ) {
	check_admin_referer( 'wpseo-clean-plugins' );
	if ( isset( $_POST['clean_external_plugin'] ) && is_string( $_POST['clean_external_plugin'] ) ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are using the variable as a class name.
		$yoast_seo_class = wp_unslash( $_POST['clean_external_plugin'] );
		if ( class_exists( $yoast_seo_class ) ) {
			$yoast_seo_import = new WPSEO_Import_Plugin( new $yoast_seo_class(), 'cleanup' );
		}
	}
}
// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are only comparing to an empty string.
elseif ( isset( $_POST['settings_import'] ) && wp_unslash( $_POST['settings_import'] ) !== '' ) {
	$yoast_seo_import = new WPSEO_Import_Settings();
	$yoast_seo_import->import();
}

/**
 * Allow custom import actions.
 *
 * @param WPSEO_Import_Status $yoast_seo_import Contains info about the handled import.
 */
$yoast_seo_import = apply_filters( 'wpseo_handle_import', $yoast_seo_import );

if ( $yoast_seo_import ) {

	$yoast_seo_message = '';
	if ( $yoast_seo_import->status instanceof WPSEO_Import_Status ) {
		$yoast_seo_message = $yoast_seo_import->status->get_msg();
	}

	/**
	 * Allow customization of import/export message.
	 *
	 * @param string $yoast_seo_msg The message.
	 */
	$yoast_seo_msg = apply_filters( 'wpseo_import_message', $yoast_seo_message );

	if ( ! empty( $yoast_seo_msg ) ) {
		$yoast_seo_status = 'error';
		if ( $yoast_seo_import->status->status ) {
			$yoast_seo_status = 'updated';
		}

		$yoast_seo_class = 'message ' . $yoast_seo_status;

		echo '<div id="message" class="', esc_attr( $yoast_seo_status ), '"><p>', esc_html( $yoast_seo_msg ), '</p></div>';
	}
}

$yoast_seo_tabs = [
	'wpseo-import' => [
		'label' => __( 'Import settings', 'wordpress-seo' ),
	],
	'wpseo-export' => [
		'label' => __( 'Export settings', 'wordpress-seo' ),
	],
	'import-seo'   => [
		'label' => __( 'Import from other SEO plugins', 'wordpress-seo' ),
	],
];

?>
	<br/><br/>

	<h2 class="nav-tab-wrapper" id="wpseo-tabs">
		<?php foreach ( $yoast_seo_tabs as $identifier => $tab ) : ?>
			<a class="nav-tab" id="<?php echo esc_attr( $identifier . '-tab' ); ?>" href="<?php echo esc_url( '#top#' . $identifier ); ?>"><?php echo esc_html( $tab['label'] ); ?></a>
		<?php endforeach; ?>

		<?php
		/**
		 * Allow adding a custom import tab header.
		 */
		do_action( 'wpseo_import_tab_header' );
		?>
	</h2>

<?php

foreach ( $yoast_seo_tabs as $identifier => $tab ) {
	printf( '<div id="%s" class="wpseotab">', esc_attr( $identifier ) );
	require_once WPSEO_PATH . 'admin/views/tabs/tool/' . $identifier . '.php';
	echo '</div>';
}

/**
 * Allow adding a custom import tab.
 */
do_action( 'wpseo_import_tab_content' );
