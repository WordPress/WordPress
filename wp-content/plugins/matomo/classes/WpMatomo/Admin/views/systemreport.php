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
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<style>
	.matomo-systemreport table a {
		color: inherit;
		text-decoration: underline;
	}
</style>
<?php
use WpMatomo\Access;
use WpMatomo\Admin\Menu;
use WpMatomo\Admin\SystemReport;

/** @var Access $access */
/** @var array $matomo_tables */
/** @var array $matomo_has_exception_logs */
/** @var bool $matomo_has_warning_and_no_errors */
/** @var bool $matomo_has_errors */
/** @var string $matomo_active_tab */
/** @var \WpMatomo\Settings $settings */
/** @var array $matomo_scheduled_tasks */

if ( ! function_exists( 'matomo_format_value_text' ) ) {
	function matomo_format_value_text( $value ) {
		if ( is_string( $value ) && ! empty( $value ) ) {
			$matomo_format = [
				'<br />' => ' ',
				'<br/>'  => ' ',
				'<br>'   => ' ',
			];
			foreach ( $matomo_format as $search => $replace ) {
				$value = str_replace( $search, $replace, $value );
			}
		}

		return $value;
	}
}
?>

<div class="wrap matomo-systemreport">
	<?php
	// if there are no errors, make sure the error notice is not displayed
	// (if there were errors previously, it may have been output before the system
	// report is shown)
	if ( empty( $matomo_has_errors ) && empty( $matomo_active_tab ) ) {
		?>
		<script>
			window.jQuery(document).ready(function ($) {
				$('#matomo-systemreporterrors.notice').remove();
			});
		</script>
		<?php
	}
	?>

	<?php
	if ( $matomo_has_warning_and_no_errors ) {
		?>
		<div class="notice notice-warning">
			<p><?php esc_html_e( 'There are some issues with your system. Matomo will run, but you might experience some minor problems. See below for more information.', 'matomo' ); ?></p>
		</div>
		<?php
	}
	?>
	<?php if ( $settings->is_network_enabled() && ! is_network_admin() && is_super_admin() ) { ?>
		<div class="updated notice">
			<p><?php esc_html_e( 'Only you are seeing this page as you are the super admin', 'matomo' ); ?></p>
		</div>
	<?php } ?>
	<div id="icon-plugins" class="icon32"></div>
	<h1><?php matomo_header_icon(); ?><?php esc_html_e( 'Diagnostics', 'matomo' ); ?></h1>

	<h2 class="nav-tab-wrapper">
		<a href="?page=<?php echo esc_attr( Menu::SLUG_SYSTEM_REPORT ); ?>"
		   class="nav-tab <?php echo empty( $matomo_active_tab ) ? 'nav-tab-active' : ''; ?>"> System report</a>
		<a href="?page=<?php echo esc_attr( Menu::SLUG_SYSTEM_REPORT ); ?>&tab=troubleshooting"
		   class="nav-tab <?php echo 'troubleshooting' === $matomo_active_tab ? 'nav-tab-active' : ''; ?>">Troubleshooting</a>
	</h2>

	<?php if ( empty( $matomo_active_tab ) ) { ?>

		<p><?php esc_html_e( 'Copy the below info in case our support team asks you for this information:', 'matomo' ); ?>
			<br/> <br/>
			<a href="javascript:void(0);"
			   onclick="var textarea = document.getElementById('matomo_system_report_info');textarea.select();document.execCommand('copy');"
			   class='button-primary'><?php esc_html_e( 'Copy system report', 'matomo' ); ?></a>

		</p>
		<textarea style="width:100%;height: 200px;" readonly
				  id="matomo_system_report_info">
				  <?php
					foreach ( $matomo_tables as $matomo_table ) {
						if ( empty( $matomo_table['rows'] ) ) {
							continue;
						}
						echo '# ' . esc_html( $matomo_table['title'] ) . "\n";
						foreach ( $matomo_table['rows'] as $index => $matomo_row ) {
							if ( ! empty( $matomo_row['section'] ) ) {
								echo "\n\n## " . esc_html( $matomo_row['section'] ) . "\n";
								continue;
							}
							$matomo_value = $matomo_row['value'];
							if ( true === $matomo_value ) {
								$matomo_value = 'Yes';
							} elseif ( false === $matomo_value ) {
								$matomo_value = 'No';
							}
							$matomo_class = '';
							if ( ! empty( $matomo_row['is_error'] ) ) {
								$matomo_class = 'Error ';
							} elseif ( ! empty( $matomo_row['is_warning'] ) ) {
								$matomo_class = 'Warning ';
							}
							echo "\n* " . esc_html( $matomo_class ) . esc_html( $matomo_row['name'] ) . ': ' . esc_html( matomo_anonymize_value( matomo_format_value_text( $matomo_value ) ) );
							if ( isset( $matomo_row['comment'] ) && '' !== $matomo_row['comment'] ) {
								 // We want to add links in the comments
                                 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo ' (' . matomo_anonymize_value( matomo_format_value_text( $matomo_row['comment'] ) ) . ')';
							}
						}
						echo "\n\n";
					}
					?>
	</textarea>

		<?php
		foreach ( $matomo_tables as $matomo_table ) {
			if ( empty( $matomo_table['rows'] ) ) {
				continue;
			}
			echo '<h2>' . esc_html( $matomo_table['title'] ) . "</h2><table class='widefat'><thead></thead><tbody>";
			foreach ( $matomo_table['rows'] as $matomo_row ) {
				if ( ! empty( $matomo_row['section'] ) ) {
					$matomo_section_id = preg_replace( '/[^a-zA-Z0-9_-]/', '', strtolower( $matomo_row['section'] ) );
					echo '</tbody><thead id="' . esc_attr( $matomo_section_id )
						. '"><tr><th colspan="3" class="section">' . esc_html( $matomo_row['section'] ) . '</th></tr></thead><tbody id="'
						. esc_attr( $matomo_section_id . '_body' ) . '">';
					continue;
				}
				$matomo_value = $matomo_row['value'];
				if ( true === $matomo_value ) {
					$matomo_value = esc_html__( 'Yes', 'matomo' );
				} elseif ( false === $matomo_value ) {
					$matomo_value = esc_html__( 'No', 'matomo' );
				}
				$matomo_class = '';
				if ( ! empty( $matomo_row['is_error'] ) ) {
					$matomo_class = 'error';
				} elseif ( ! empty( $matomo_row['is_warning'] ) ) {
					$matomo_class = 'warning';
				}
				echo "<tr class='" . esc_attr( $matomo_class ) . "'>";
				echo "<td width='30%'>" . esc_html( $matomo_row['name'] ) . '</td>';
				echo "<td width='" . ( ! empty( $matomo_table['has_comments'] ) ? 20 : 70 ) . "%'>" . esc_html( $matomo_value ) . '</td>';
				if ( ! empty( $matomo_table['has_comments'] ) ) {
					$matomo_replaced_elements = [
						'<code>'  => '__#CODEBACKUP#__',
						'</code>' => '__##CODEBACKUP##__',
						'<pre style="overflow-x: scroll;max-width: 600px;">' => '__#PREBACKUP#__',
						'</pre>'  => '__##PREBACKUP##__',
						'<br/>'   => '__#BRBACKUP#__',
						'<br />'  => '__#BRBACKUP#__',
						'<br>'    => '__#BRBACKUP#__',
					];
					$matomo_comment           = isset( $matomo_row['comment'] ) ? $matomo_row['comment'] : '';
					$matomo_replaced          = str_replace( array_keys( $matomo_replaced_elements ), array_values( $matomo_replaced_elements ), $matomo_comment );
					// note: the text is not escaped anymore. Instead, the escaping is made when generating the comment. It allows then to add links in the output.
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo "<td width='50%' class='matomo-systemreport-comment'>" . str_replace( array_values( $matomo_replaced_elements ), array_keys( $matomo_replaced_elements ), $matomo_replaced ) . '</td>';
				}

				echo '</tr>';
			}
			echo '</tbody></table>';
		}
		?>

	<?php } else { ?>
		<h1><?php esc_html_e( 'Troubleshooting', 'matomo' ); ?></h1>

		<form method="post">
			<?php wp_nonce_field( SystemReport::NONCE_NAME ); ?>

			<input name="<?php echo esc_attr( SystemReport::TROUBLESHOOT_ARCHIVE_NOW ); ?>" type="submit"
				   class='button-primary'
				   title="<?php esc_attr_e( 'If reports show no data even though they should, you may try to see if report generation works when manually triggering the report generation.', 'matomo' ); ?>"
				   value="<?php esc_html_e( 'Archive reports', 'matomo' ); ?>">
			<br/><br/>
			<input name="<?php echo esc_attr( SystemReport::TROUBLESHOOT_CLEAR_MATOMO_CACHE ); ?>" type="submit"
				   class='button-primary'
				   title="<?php esc_attr_e( 'Will reset / empty the Matomo cache which can be helpful if something is not working as expected for example after an update.', 'matomo' ); ?>"
				   value="<?php esc_html_e( 'Clear Matomo cache', 'matomo' ); ?>">
			<br/><br/>
			<?php if ( ! empty( $matomo_has_exception_logs ) ) { ?>
				<input name="<?php echo esc_attr( SystemReport::TROUBLESHOOT_CLEAR_LOGS ); ?>" type="submit"
					   class='button-primary'
					   title="<?php esc_attr_e( 'Removes all stored Matomo logs that are shown in the system report', 'matomo' ); ?>"
					   value="<?php esc_html_e( 'Clear system report logs', 'matomo' ); ?>">
				<br/><br/>
			<?php } ?>

			<input name="<?php echo esc_attr( SystemReport::TROUBLESHOOT_UPDATE_GEOIP_DB ); ?>" type="submit"
				   class='button-primary'
				   title="<?php esc_attr_e( 'Updates the geolocation database which is used to detect the location (city/region/country) of visitors. This task is performed automatically. If the geolocation DB is not loaded or updated, you may need to trigger it manually to find the error which is causing it.', 'matomo' ); ?>"
				   value="<?php esc_html_e( 'Install/Update Geo-IP DB', 'matomo' ); ?>">
			<br/><br/>

			<?php if ( ! $settings->is_network_enabled() || ! is_network_admin() ) { ?>
				<input name="<?php echo esc_attr( SystemReport::TROUBLESHOOT_SYNC_USERS ); ?>" type="submit"
					   class='button-primary'
					   title="<?php esc_attr_e( 'Users are synced automatically. If for some reason a user cannot access Matomo pages even though the user has the permission, then triggering a manual sync may help to fix this issue immediately or it may show which error prevents the automatic syncing.', 'matomo' ); ?>"
					   value="<?php esc_html_e( 'Sync users', 'matomo' ); ?>">
				<br/><br/>
				<input name="<?php echo esc_attr( SystemReport::TROUBLESHOOT_SYNC_SITE ); ?>" type="submit"
					   class='button-primary'
					   title="<?php esc_attr_e( 'Sites / blogs are synced automatically. If for some reason Matomo is not showing up for a specific blog, then triggering a manual sync may help to fix this issue immediately or it may show which error prevents the automatic syncing.', 'matomo' ); ?>"
					   value="<?php esc_html_e( 'Sync site (blog)', 'matomo' ); ?>">
				<br/><br/>
				<input name="<?php echo esc_attr( SystemReport::TROUBLESHOOT_RUN_UPDATER ); ?>" type="submit"
					   class='button-primary'
					   title="<?php esc_attr_e( 'Force trigger a Matomo update in case it failed error', 'matomo' ); ?>"
					   value="<?php esc_attr_e( 'Run Updater', 'matomo' ); ?>">
				<label for="matomo_troubleshooting_update_from">Run updates from version:</label>
				<input id="matomo_troubleshooting_update_from"
					   type="text"
					   name="matomo_troubleshooting_update_from"
					   title="<?php esc_attr_e( 'Enter a version to re-run updates from, eg, "5.0.0".', 'matomo' ); ?>"
					   value="">
				<br/><br/>
			<?php } ?>
			<?php if ( $settings->is_network_enabled() ) { ?>
				<input name="<?php echo esc_attr( SystemReport::TROUBLESHOOT_SYNC_ALL_USERS ); ?>" type="submit"
					   class='button-primary'
					   title="<?php esc_attr_e( 'Users are synced automatically. If for some reason a user cannot access Matomo pages even though the user has the permission, then triggering a manual sync may help to fix this issue immediately or it may show which error prevents the automatic syncing.', 'matomo' ); ?>"
					   value="<?php esc_attr_e( 'Sync all users across sites / blogs', 'matomo' ); ?>">
				<br/><br/>
				<input name="<?php echo esc_attr( SystemReport::TROUBLESHOOT_SYNC_ALL_SITES ); ?>" type="submit"
					   title="<?php esc_attr_e( 'Sites / blogs are synced automatically. If for some reason Matomo is not showing up for a specific blog, then triggering a manual sync may help to fix this issue immediately or it may show which error prevents the automatic syncing.', 'matomo' ); ?>"
					   class='button-primary'
					   value="<?php esc_attr_e( 'Sync all sites (blogs)', 'matomo' ); ?>">
				<br/><br/>
			<?php } ?>
			<input name="<?php echo esc_attr( SystemReport::REGENERATE_TRACKING_CODE ); ?>" type="submit"
				   class="button-primary"
				   title="<?php esc_attr_e( 'Force the cached tracking code to be regenerated. The tracking code is usually regenerated on update and after changing tracking settings, or if a Matomo plugin is installed/updated, but if you need to do it manually, you can do it here.', 'matomo' ); ?>"
				   value="<?php esc_attr_e( 'Regenerate tracking code', 'matomo' ); ?>">
			<br/>
			<br/>

			<input name="<?php echo esc_attr( SystemReport::RUN_SCHEDULED_TASK ); ?>" type="submit"
				   class="button-primary"
				   title="<?php esc_attr_e( 'Run a scheduled task by name.', 'matomo' ); ?>"
				   value="<?php esc_attr_e( 'Run scheduled task', 'matomo' ); ?>"
				   />
			<label for="matomo_troubleshooting_run_task">Task to run:</label>
			<select
				id="matomo_troubleshooting_run_task"
				name="matomo_troubleshooting_run_task"
				title="<?php esc_attr_e( 'Pick a task to run', 'matomo' ); ?>"
			>
				<?php foreach ( $matomo_scheduled_tasks as $matomo_task ) { ?>
				<option value="<?php echo esc_attr( $matomo_task ); ?>"><?php echo esc_html( $matomo_task ); ?></option>
				<?php } ?>
			</select>
			<br/>
			<br/>
		</form>

		<?php
		$show_troubleshooting_link = false;
		include 'info_help.php';
		?>
		<h3><?php esc_html_e( 'Popular Troubleshooting FAQs', 'matomo' ); ?></h3>
		<ul class="matomo-list">
			<li>
				<a href="https://matomo.org/faq/wordpress/matomo-for-wordpress-is-not-showing-any-statistics-not-archiving-how-do-i-fix-it/"
				   target="_blank"
				   rel="noreferrer noopener"><?php esc_html_e( 'Matomo is not showing any statistics / reports, how do I fix it?', 'matomo' ); ?></a>
			</li>
			<li><a href="https://matomo.org/faq/wordpress/i-cannot-open-backend-page-how-do-i-troubleshoot-it/"
				   target="_blank"
				   rel="noreferrer noopener"><?php esc_html_e( 'I cannot open the Matomo Reporting, Admin, or Tag Manager page, how do I troubleshoot it?', 'matomo' ); ?></a>
			</li>
			<li><a href="https://matomo.org/faq/wordpress/i-have-a-problem-how-do-i-troubleshoot-and-enable-wp_debug/"
				   target="_blank"
				   rel="noreferrer noopener"><?php esc_html_e( 'I have an issue with the plugin, how do I troubleshoot and enable debug mode?', 'matomo' ); ?></a>
			</li>
			<li><a href="https://matomo.org/faq/wordpress/how-do-i-manually-delete-all-matomo-for-wordpress-data/"
				   target="_blank"
				   rel="noreferrer noopener"><?php esc_html_e( 'How do I manually delete or reset all Matomo for WordPress data?', 'matomo' ); ?></a>
			</li>
			<li><a href="https://matomo.org/faq/wordpress/" target="_blank"
				   rel="noreferrer noopener"><?php esc_html_e( 'View all FAQs', 'matomo' ); ?></a></li>
		</ul>
		<?php include 'info_bug_report.php'; ?>
		<h4><?php esc_html_e( 'Before you create an issue', 'matomo' ); ?></h4>
		<p><?php esc_html_e( 'If you experience any issue in Matomo, it is always a good idea to first check your webserver logs (if possible) for any errors.', 'matomo' ); ?>
			<br/>
			<?php echo sprintf( esc_html__( 'You may also want to enable %1$s.', 'matomo' ), '<a href="https://matomo.org/faq/wordpress/i-have-a-problem-how-do-i-troubleshoot-and-enable-wp_debug/" target="_blank" rel="noreferrer noopener"><code>WP_DEBUG</code></a>' ); ?>
			<?php echo sprintf( esc_html__( 'To debug issues that happen in the background, for example report generation during a cronjob, you might also want to enable %1$s.', 'matomo' ), '<code>WP_DEBUG_LOG</code>' ); ?>

		</p>
		<h3><?php esc_html_e( 'Having performance issues?', 'matomo' ); ?></h3>
		<p>
			<?php
			echo sprintf(
				esc_html__( 'You may want to disable %1$s in your %2$s and set up an actual cronjob and %3$scheck out our recommended server sizing%4$s.', 'matomo' ),
				'<code>DISABLE_WP_CRON</code>',
				'<code>wp-config.php</code>',
				'<a target="_blank" rel="noreferrer noopener" href="https://matomo.org/docs/requirements/#recommended-servers-sizing-cpu-ram-disks">',
				'</a>'
			);
			?>
		</p>
		<?php include 'info_high_traffic.php'; ?>
	<?php } ?>
</div>
