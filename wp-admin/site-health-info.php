<?php
/**
 * Tools Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

$title = __( 'Site Health Info' );

if ( ! current_user_can( 'install_plugins' ) ) {
	wp_die( __( 'Sorry, you are not allowed to access the debug data.' ), '', 403 );
}

wp_enqueue_style( 'site-health' );
wp_enqueue_script( 'site-health' );

if ( ! class_exists( 'WP_Debug_Data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-debug-data.php' );
}
if ( ! class_exists( 'WP_Site_Health' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-site-health.php' );
}

$health_check_site_status = new WP_Site_Health();

require_once( ABSPATH . 'wp-admin/admin-header.php' );
?>
<div class="health-check-header">
	<div class="health-check-title-section">
		<h1>
			<?php _e( 'Site Health' ); ?>
		</h1>

		<div class="site-health-progress hide-if-no-js loading">
			<svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
				<circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
				<circle id="bar" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
			</svg>
			<span class="screen-reader-text"><?php _e( 'Current health score:' ); ?></span>
			<span class="site-health-progress-count"></span>
		</div>
	</div>

	<nav class="health-check-tabs-wrapper hide-if-no-js" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
		<a href="<?php echo esc_url( admin_url( 'site-health.php' ) ); ?>" class="health-check-tab">
			<?php _e( 'Status' ); ?>
		</a>

		<a href="<?php echo esc_url( admin_url( 'site-health.php?tab=debug' ) ); ?>" class="health-check-tab active" aria-current="true">
			<?php _e( 'Info' ); ?>
		</a>
	</nav>
</div>

<hr class="wp-header-end">

<div class="notice notice-error hide-if-js">
	<p><?php _e( 'The Site Health check requires JavaScript.' ); ?></p>
</div>

<div class="health-check-body hide-if-no-js">
	<?php
	WP_Debug_Data::check_for_updates();

	$info         = WP_Debug_Data::debug_data();
	$english_info = '';
	if ( 0 !== strpos( get_user_locale(), 'en' ) ) {
		$english_info = WP_Debug_Data::debug_data( 'en_US' );
	}
	?>

	<h2>
		<?php _e( 'Site Health Info' ); ?>
	</h2>

	<p>
		<?php _e( 'This page can show you every detail about the configuration of your WordPress website. If we see anything here that could be improved, we will let you know on the Site Health Status page.' ); ?>
	</p>
	<p>
		<?php _e( 'If you want to export a handy list of all the information on this page, you can use the button below to copy it to the clipboard. You can then paste it in a text file and save it to your harddrive, or paste it in an email exchange with a support engineer or theme/plugin developer for example.' ); ?>
	</p>

	<div class="site-health-copy-buttons">
		<div class="copy-button-wrapper">
			<button type="button" class="button button-primary copy-button" data-clipboard-text="<?php echo esc_attr( WP_Debug_Data::format( $info, 'text' ) ); ?>"><?php _e( 'Copy site info to clipboard' ); ?></button>
			<span class="success" aria-hidden="true">Copied!</span>
		</div>
		<?php if ( $english_info ) : ?>
			<div class="copy-button-wrapper">
				<button type="button" class="button copy-button" data-clipboard-text="<?php echo esc_attr( WP_Debug_Data::format( $english_info, 'text' ) ); ?>"><?php _e( 'Copy site info to clipboard (English)' ); ?></button>
				<span class="success" aria-hidden="true">Copied!</span>
			</div>
		<?php endif; ?>
	</div>

	<div id="health-check-debug" class="health-check-accordion">

		<?php
		foreach ( $info as $section => $details ) {
			if ( ! isset( $details['fields'] ) || empty( $details['fields'] ) ) {
				continue;
			}
			?>
			<h3 class="health-check-accordion-heading">
				<button aria-expanded="false" class="health-check-accordion-trigger" aria-controls="health-check-accordion-block-<?php echo esc_attr( $section ); ?>" type="button">
					<span class="title">
						<?php echo esc_html( $details['label'] ); ?>

						<?php if ( isset( $details['show_count'] ) && $details['show_count'] ) : ?>
							<?php printf( '(%d)', count( $details['fields'] ) ); ?>
						<?php endif; ?>
					</span>
					<span class="icon"></span>
				</button>
			</h3>

			<div id="health-check-accordion-block-<?php echo esc_attr( $section ); ?>" class="health-check-accordion-panel" hidden="hidden">
				<?php
				if ( isset( $details['description'] ) && ! empty( $details['description'] ) ) {
					printf(
						'<p>%s</p>',
						wp_kses(
							$details['description'],
							array(
								'a'      => array(
									'href' => true,
								),
								'strong' => true,
								'em'     => true,
							)
						)
					);
				}
				?>
				<table class="widefat striped health-check-table" role="presentation">
					<tbody>
					<?php
					foreach ( $details['fields'] as $field ) {
						if ( is_array( $field['value'] ) ) {
							$values = '<ul>';
							foreach ( $field['value'] as $name => $value ) {
								$values .= sprintf(
									'<li>%s: %s</li>',
									esc_html( $name ),
									esc_html( $value )
								);
							}
							$values .= '</ul>';
						} else {
							$values = esc_html( $field['value'] );
						}

						printf(
							'<tr><td>%s</td><td>%s</td></tr>',
							esc_html( $field['label'] ),
							$values
						);
					}
					?>
					</tbody>
				</table>
			</div>
		<?php } ?>
	</div>
</div>

<?php
include( ABSPATH . 'wp-admin/admin-footer.php' );
