<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */
/**
 * phpcs considers all of our variables as global and want them prefixed with matomo
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */
use WpMatomo\Admin\Dashboard;
use WpMatomo\Admin\Menu;
use WpMatomo\Admin\Summary;
use WpMatomo\Report\Dates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var array $report_metadata */
/** @var array $report_dates */
/** @var array $reports_to_show */
/** @var string $report_date */
/** @var string $report_period_selected */
/** @var string $report_date_selected */
/** @var bool $matomo_pinned */
/** @var bool $is_tracking */
/** @var bool $matomo_is_version_pre55 */
/** @var Dashboard $matomo_dashboard */
global $wp;

$matomo_dashboard_nonce = wp_create_nonce( Summary::NONCE_DASHBOARD );
?>
<?php
if ( $matomo_pinned ) {
	echo '<div class="notice notice-success"><p>' . esc_html__( 'Dashboard updated.', 'matomo' ) . '</p></div>';
}
if ( $matomo_is_version_pre55 ) {
	echo '<style type="text/css">.handle-actions { position: absolute; right: 0;top: 0;}</style>';
}
?>
<?php if ( ! $is_tracking ) { ?>
	<div class="notice notice-warning">
		<p><?php esc_html_e( 'Matomo Tracking is not enabled. If you have added the Matomo tracking code in a different way, for example using a consent plugin, then you can ignore this message.', 'matomo' ); ?></p>
	</div>
<?php } ?>
<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h1><?php matomo_header_icon(); ?><?php esc_html_e( 'Summary', 'matomo' ); ?></h1>
	<?php
	if ( Dates::TODAY === $report_date ) {
		echo '<div class="notice notice-info" style="padding:8px;">' . esc_html__( 'Reports for today are only refreshed approximately every hour through the WordPress cronjob.', 'matomo' ) . '</div>';
	}
	?>
	<p><?php esc_html_e( 'Looking for all reports and advanced features like segmentation, real time reports, and more?', 'matomo' ); ?>
		<a href="<?php echo esc_url( add_query_arg( [ 'report_date' => $report_date ], menu_page_url( Menu::SLUG_REPORTING, false ) ) ); ?>"
		><?php esc_html_e( 'View full reporting', 'matomo' ); ?></a>
		<br/><br/>
		<?php esc_html_e( 'Change date:', 'matomo' ); ?>
		<?php
		foreach ( $report_dates as $matomo_report_date_key => $matomo_report_name ) {
			$matomo_button_class = 'button';
			if ( $report_date === $matomo_report_date_key ) {
				$matomo_button_class = 'button-primary';
			}
			echo '<a href="' . esc_url( add_query_arg( [ 'report_date' => $matomo_report_date_key ], menu_page_url( Menu::SLUG_REPORT_SUMMARY, false ) ) ) . '" class="' . esc_attr( $matomo_button_class ) . '">' . esc_html( $matomo_report_name ) . '</a> ';
		}
		?>

	<div id="dashboard-widgets" class="metabox-holder  has-right-sidebar matomo-dashboard-container">
		<?php
		$matomo_columns = [ 1, 0 ];
		foreach ( $matomo_columns as $matomo_column_index => $matomo_column_modulo ) {
			?>
			<div id="postbox-container-<?php echo ( esc_html( $matomo_column_index + 1 ) ); ?>" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<?php
					foreach ( $reports_to_show as $matomo_index => $matomo_report_meta ) {
						if ( $matomo_index % 2 === $matomo_column_modulo ) {
							continue;
						}
						$shortcode = sprintf( '[matomo_report unique_id=%s report_date=%s limit=10]', $matomo_report_meta['uniqueId'], $report_date );
						?>
						<div class="postbox ">
							<div class="postbox-header">
								<h2 class="hndle ui-sortable-handle"
									style="cursor: help;"
									title="<?php echo ! empty( $matomo_report_meta['documentation'] ) ? ( esc_html( wp_strip_all_tags( $matomo_report_meta['documentation'] ) . ' ' ) ) : null; ?><?php esc_html_e( 'You can embed this report on any page using the shortcode:', 'matomo' ); ?> <?php echo esc_attr( $shortcode ); ?>">
									<?php echo esc_html( $matomo_report_meta['name'] ); ?></h2>
								<div class="handle-actions hide-if-no-js">
									<?php if ( ! empty( $matomo_report_meta['page'] ) ) { ?>
										<button type="button" class="handlediv" aria-expanded="true"
												title="<?php esc_html_e( 'Click to view the report in detail', 'matomo' ); ?>">
											<a
													href="
										<?php

													echo esc_url(
														Menu::get_matomo_reporting_url(
															$matomo_report_meta['page']['category'],
															$matomo_report_meta['page']['subcategory'],
															[
																'period' => $report_period_selected,
																'date'   => $report_date_selected,
															]
														)
													);
										?>
												" style="color: inherit;text-decoration: none;" target="_blank"
													rel="noreferrer noopener"
													class="dashicons-before dashicons-external" aria-hidden="true"></a>
										</button>
									<?php } ?>

									<?php $matomo_is_dashboard_widget = $matomo_dashboard->has_widget( $matomo_report_meta['uniqueId'], $report_date ); ?>
                                    <?php // phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentBeforeOpen ?>
									<button type="button" class="handlediv" aria-expanded="true" title="<?php
									if ( $matomo_is_dashboard_widget ) {
										esc_html_e( 'Click to remove this report from the WordPress admin dashboard', 'matomo' );
									} else {
										esc_html_e( 'Click to add this report to the WordPress admin dashboard', 'matomo' );
									}
                                    // phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentAfterEnd
									?>"><a
												href="
										<?php
												echo esc_url(
													add_query_arg(
														[
															'pin'             => true,
															'_wpnonce'        => $matomo_dashboard_nonce,
															'report_uniqueid' => $matomo_report_meta['uniqueId'],
															'report_date'     => $report_date,
														],
														menu_page_url( Menu::SLUG_REPORT_SUMMARY, false )
													)
												);
										?>
												" style="color: inherit;text-decoration: none;
										<?php
										if ( $matomo_is_dashboard_widget ) {
											echo 'opacity: 0.4 !important';
										}
										?>
												"
												class="dashicons-before dashicons-admin-post" aria-hidden="true"></a>
									</button>

								</div>
							</div>
							<div>
								<?php echo do_shortcode( $shortcode ); ?>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
		}
		?>
	</div>

	<p style="clear:both;">
		<?php esc_html_e( 'Did you know? You can embed any report into any page or post using a shortcode. Simply hover the title to find the correct shortcode.', 'matomo' ); ?>
		<?php esc_html_e( 'Only users with view access will be able to view the content of the report.', 'matomo' ); ?>
		<?php esc_html_e( 'Note: Embedding report data can be tricky if you are using caching plugins that cache the entire HTML of your page or post. In case you are using such a plugin, we recommend you disable the caching for these pages.', 'matomo' ); ?>
	</p>
</div>
