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
use WpMatomo\Admin\AdminSettings;
use WpMatomo\Admin\GetStarted;
use WpMatomo\Admin\Menu;
use WpMatomo\Admin\TrackingSettings;
use WpMatomo\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>

	<h1><?php esc_html_e( 'Import your WP Statistics data into Matomo', 'matomo' ); ?></h1>

		<h2>1. <?php esc_html_e( 'Install the WP-CLI', 'matomo' ); ?></h2>

		<?php echo sprintf( esc_html__( 'The WP-CLI is the official WordPress client command line tool. Follow these installation instructions: %s.', 'matomo' ), '<a href="https://wp-cli.org/#installing" rel="noreferrer noopener" target="_blank">here</a>' ); ?>
	<h2>2. <?php esc_html_e( 'Run the import', 'matomo' ); ?></h2>

	<?php echo sprintf( esc_html__( 'Run the command %1$s or depending on your set-up you may need to run it like this: %2$s.', 'matomo' ), '<code>php wp-cli.phar matomo importWpStatistics</code>', '<code>wp matomo importWpStatistics</code>' ); ?>

	<h2>3. <?php esc_html_e( 'Done', 'matomo' ); ?></h2>
	<p>
		<?php esc_html_e( 'The data will now show up in your Matomo Summary page and Matomo Reporting. Please note that the "Visits Log" feature won\'t show any data, as we only import aggregated reports.', 'matomo' ); ?>
		<br/>
		<br/>
		This page will disappear when the WP Statistics plugin is deactivated.<br/>
		<br/>
	</p>

	<?php
	$show_troubleshooting_link = false;
	require 'info_help.php';
	?>
</div>
