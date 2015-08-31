<?php
/**
 * @package WPSEO\Admin
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * @todo [JRF => testers] Extensively test the export & import of the (new) settings!
 * If that all works fine, getting testers to export before and after upgrade will make testing easier.
 *
 * @todo [Yoast] The import for the RSS Footer plugin checks for data already entered via Yoast SEO,
 * the other import routines should do that too.
 */

$yform = Yoast_Form::get_instance();

$replace = false;

if ( isset( $_POST['import'] ) || isset( $_GET['import'] ) ) {

	check_admin_referer( 'wpseo-import' );

	if ( isset( $_POST['wpseo']['deleteolddata'] ) && $_POST['wpseo']['deleteolddata'] == 'on' ) {
		$replace = true;
	}

	if ( isset( $_POST['wpseo']['importwoo'] ) ) {
		$import = new WPSEO_Import_WooThemes_SEO( $replace );
	}

	if ( isset( $_POST['wpseo']['importaioseo'] ) || isset( $_GET['importaioseo'] ) ) {
		$import = new WPSEO_Import_AIOSEO( $replace );
	}

	if ( isset( $_POST['wpseo']['importheadspace'] ) ) {
		$import = new WPSEO_Import_External( $replace );
		$import->import_headspace();
	}

	if ( isset( $_POST['wpseo']['importrobotsmeta'] ) || isset( $_GET['importrobotsmeta'] ) ) {
		$import = new WPSEO_Import_External( $replace );
		$import->import_robots_meta();
	}

	if ( isset( $_POST['wpseo']['importrssfooter'] ) ) {
		$import = new WPSEO_Import_External( $replace );
		$import->import_rss_footer();
	}

	if ( isset( $_POST['wpseo']['importbreadcrumbs'] ) ) {
		$import = new WPSEO_Import_External( $replace );
		$import->import_yoast_breadcrumbs();
	}

	// Allow custom import actions.
	do_action( 'wpseo_handle_import' );

}

if ( isset( $_FILES['settings_import_file'] ) ) {
	check_admin_referer( 'wpseo-import-file' );

	$import = new WPSEO_Import();
}

if ( isset( $import ) ) {
	/**
	 * Allow customization of import&export message
	 * @api  string  $msg  The message.
	 */
	$msg = apply_filters( 'wpseo_import_message', $import->msg );

	// Check if we've deleted old data and adjust message to match it.
	if ( $replace ) {
		$msg .= ' ' . __( 'The old data of the imported plugin was deleted successfully.', 'wordpress-seo' );
	}

	if ( $msg != '' ) {
		echo '<div id="message" class="message updated" style="width:94%;"><p>', $msg, '</p></div>';
	}
}

?>
<br/><br/>
<h2 class="nav-tab-wrapper" id="wpseo-tabs">
	<a class="nav-tab nav-tab-active" id="wpseo-import-tab"
	   href="#top#wpseo-import"><?php _e( 'Import', 'wordpress-seo' ); ?></a>
	<a class="nav-tab" id="wpseo-export-tab" href="#top#wpseo-export"><?php _e( 'Export', 'wordpress-seo' ); ?></a>
	<a class="nav-tab" id="import-seo-tab"
	   href="#top#import-seo"><?php _e( 'Import from other SEO plugins', 'wordpress-seo' ); ?></a>
	<a class="nav-tab" id="import-other-tab"
	   href="#top#import-other"><?php _e( 'Import from other plugins', 'wordpress-seo' ); ?></a>
	<?php
	/**
	 * Allow adding a custom import tab header
	 */
	do_action( 'wpseo_import_tab_header' );
	?>
</h2>

<div id="wpseo-import" class="wpseotab">
	<p><?php _e( 'Import settings by locating <em>settings.zip</em> and clicking "Import settings"', 'wordpress-seo' ); ?></p>

	<form
		action="<?php echo esc_attr( admin_url( 'admin.php?page=wpseo_tools&tool=import-export#top#wpseo-import' ) ); ?>"
		method="post" enctype="multipart/form-data"
		accept-charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
		<?php wp_nonce_field( 'wpseo-import-file', '_wpnonce', true, true ); ?>
		<input type="file" name="settings_import_file" accept="application/x-zip,application/x-zip-compressed,application/zip" />
		<input type="hidden" name="action" value="wp_handle_upload"/><br/>
		<br/>
		<input type="submit" class="button-primary" value="<?php _e( 'Import settings', 'wordpress-seo' ); ?>"/>
	</form>
</div>

<div id="wpseo-export" class="wpseotab">
	<p><?php
		/* translators: %1$s expands to Yoast SEO */
		printf( __( 'Export your %1$s settings here, to import them again later or to import them on another site.', 'wordpress-seo' ), 'Yoast SEO' );
		?></p>
	<?php $yform->checkbox( 'include_taxonomy_meta', __( 'Include Taxonomy Metadata', 'wordpress-seo' ) ); ?><br/>
	<button class="button-primary" id="export-button"><?php
		/* translators: %1$s expands to Yoast SEO */
		printf( __( 'Export your %1$s settings', 'wordpress-seo' ), 'Yoast SEO' );
		?></button>
	<script>
		var wpseo_export_nonce = '<?php echo wp_create_nonce( 'wpseo-export' ); ?>';
	</script>
</div>

<div id="import-seo" class="wpseotab">
	<p><?php _e( 'No doubt you\'ve used an SEO plugin before if this site isn\'t new. Let\'s make it easy on you, you can import the data below. If you want, you can import first, check if it was imported correctly, and then import &amp; delete. No duplicate data will be imported.', 'wordpress-seo' ); ?></p>

	<p><?php printf( __( 'If you\'ve used another SEO plugin, try the %sSEO Data Transporter%s plugin to move your data into this plugin, it rocks!', 'wordpress-seo' ), '<a href="https://wordpress.org/plugins/seo-data-transporter/">', '</a>' ); ?></p>

	<form
		action="<?php echo esc_attr( admin_url( 'admin.php?page=wpseo_tools&tool=import-export#top#import-seo' ) ); ?>"
		method="post" accept-charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
		<?php
		wp_nonce_field( 'wpseo-import', '_wpnonce', true, true );
		$yform->checkbox( 'importheadspace', __( 'Import from HeadSpace2?', 'wordpress-seo' ) );
		$yform->checkbox( 'importaioseo', __( 'Import from All-in-One SEO?', 'wordpress-seo' ) );
		$yform->checkbox( 'importwoo', __( 'Import from WooThemes SEO framework?', 'wordpress-seo' ) );
		?>
		<br/>
		<?php
		$yform->checkbox( 'deleteolddata', __( 'Delete the old data after import? (recommended)', 'wordpress-seo' ) );
		?>
		<br/>
		<input type="submit" class="button-primary" name="import"
		       value="<?php _e( 'Import', 'wordpress-seo' ); ?>"/>
	</form>
	<br/>
	<br/>
</div>

<div id="import-other" class="wpseotab">
	<p><?php _e( 'If you want to import data from (by now ancient) Yoast plugins, you can do so here:', 'wordpress-seo' ); ?></p>

	<form
		action="<?php echo esc_attr( admin_url( 'admin.php?page=wpseo_tools&tool=import-export#top#import-other' ) ); ?>"
		method="post" accept-charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
		<?php
		wp_nonce_field( 'wpseo-import', '_wpnonce', true, true );
		$yform->checkbox( 'importrobotsmeta', __( 'Import from Robots Meta (by Yoast)?', 'wordpress-seo' ) );
		$yform->checkbox( 'importrssfooter', __( 'Import from RSS Footer (by Yoast)?', 'wordpress-seo' ) );
		$yform->checkbox( 'importbreadcrumbs', __( 'Import from Yoast Breadcrumbs?', 'wordpress-seo' ) );

		/**
		 * Allow option of importing from other 'other' plugins
		 * @api  string  $content  The content containing all import and export methods
		 */
		echo apply_filters( 'wpseo_import_other_plugins', '' );

		?>
		<br/>
		<input type="submit" class="button-primary" name="import" value="<?php _e( 'Import', 'wordpress-seo' ); ?>"/>
	</form>
	<br/>
</div>
<?php
/**
 * Allow adding a custom import tab
 */
do_action( 'wpseo_import_tab_content' );

