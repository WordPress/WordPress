<?php
/**
 * Tools Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'WP_Debug_Data' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
}
if ( ! class_exists( 'WP_Site_Health' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
}

$health_check_site_status = WP_Site_Health::get_instance();

wp_admin_notice(
	__( 'The Site Health check requires JavaScript.' ),
	array(
		'type'               => 'error',
		'additional_classes' => array( 'hide-if-js' ),
	)
);
?>

<div class="health-check-body health-check-debug-tab hide-if-no-js">
	<?php

	WP_Debug_Data::check_for_updates();

	$info = WP_Debug_Data::debug_data();

	?>

	<h2>
		<?php _e( 'Site Health Info' ); ?>
	</h2>

	<p>
		<?php
			/* translators: %s: URL to Site Health Status page. */
			printf( __( 'This page can show you every detail about the configuration of your WordPress website. For any improvements that could be made, see the <a href="%s">Site Health Status</a> page.' ), esc_url( admin_url( 'site-health.php' ) ) );
		?>
	</p>
	<p>
		<?php _e( 'If you want to export a handy list of all the information on this page, you can use the button below to copy it to the clipboard. You can then paste it in a text file and save it to your device, or paste it in an email exchange with a support engineer or theme/plugin developer for example.' ); ?>
	</p>

	<div class="site-health-copy-buttons">
		<div class="copy-button-wrapper">
			<button type="button" class="button copy-button" data-clipboard-text="<?php echo esc_attr( WP_Debug_Data::format( $info, 'debug' ) ); ?>">
				<?php _e( 'Copy site info to clipboard' ); ?>
			</button>
			<span class="success hidden" aria-hidden="true"><?php _e( 'Copied!' ); ?></span>
		</div>
	</div>

	<div id="health-check-debug" class="health-check-accordion">

		<?php

		$sizes_fields = array( 'uploads_size', 'themes_size', 'plugins_size', 'fonts_size', 'wordpress_size', 'database_size', 'total_size' );

		foreach ( $info as $section => $details ) {
			if ( ! isset( $details['fields'] ) || empty( $details['fields'] ) ) {
				continue;
			}

			?>
			<h3 class="health-check-accordion-heading">
				<button aria-expanded="false" class="health-check-accordion-trigger" aria-controls="health-check-accordion-block-<?php echo esc_attr( $section ); ?>" type="button">
					<span class="title">
						<?php echo esc_html( $details['label'] ); ?>
						<?php

						if ( isset( $details['show_count'] ) && $details['show_count'] ) {
							printf(
								'(%s)',
								number_format_i18n( count( $details['fields'] ) )
							);
						}

						?>
					</span>
					<?php

					if ( 'wp-paths-sizes' === $section ) {
						?>
						<span class="health-check-wp-paths-sizes spinner"></span>
						<?php
					}

					?>
					<span class="icon"></span>
				</button>
			</h3>

			<div id="health-check-accordion-block-<?php echo esc_attr( $section ); ?>" class="health-check-accordion-panel" hidden="hidden">
				<?php

				if ( isset( $details['description'] ) && ! empty( $details['description'] ) ) {
					printf( '<p>%s</p>', $details['description'] );
				}

				?>
				<table class="widefat striped health-check-table" role="presentation">
					<tbody>
					<?php

					foreach ( $details['fields'] as $field_name => $field ) {
						if ( is_array( $field['value'] ) ) {
							$values = '<ul>';

							foreach ( $field['value'] as $name => $value ) {
								$values .= sprintf( '<li>%s: %s</li>', esc_html( $name ), esc_html( $value ) );
							}

							$values .= '</ul>';
						} else {
							$values = esc_html( $field['value'] );
						}

						if ( in_array( $field_name, $sizes_fields, true ) ) {
							printf( '<tr><td>%s</td><td class="%s">%s</td></tr>', esc_html( $field['label'] ), esc_attr( $field_name ), $values );
						} else {
							printf( '<tr><td>%s</td><td>%s</td></tr>', esc_html( $field['label'] ), $values );
						}
					}

					?>
					</tbody>
				</table>
			</div>
		<?php } ?>
	</div>
</div>
