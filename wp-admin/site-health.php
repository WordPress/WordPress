<?php
/**
 * Tools Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

if ( isset( $_GET['tab'] ) && 'debug' === $_GET['tab'] ) {
	require_once( dirname( __FILE__ ) . '/site-health-info.php' );
	return;
}

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

$title = __( 'Site Health Status' );

if ( ! current_user_can( 'view_site_health_checks' ) ) {
	wp_die( __( 'Sorry, you are not allowed to access site health information.' ), '', 403 );
}

wp_enqueue_style( 'site-health' );
wp_enqueue_script( 'site-health' );

if ( ! class_exists( 'WP_Site_Health' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-site-health.php' );
}

$health_check_site_status = new WP_Site_Health();

// Start by checking if this is a special request checking for the existence of certain filters.
$health_check_site_status->check_wp_version_check_exists();

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
		<a href="<?php echo esc_url( admin_url( 'site-health.php' ) ); ?>" class="health-check-tab active" aria-current="true">
			<?php
			/* translators: Tab heading for Site Health Status page. */
			_ex( 'Status', 'Site Health' );
			?>
		</a>

		<a href="<?php echo esc_url( admin_url( 'site-health.php?tab=debug' ) ); ?>" class="health-check-tab">
			<?php
			/* translators: Tab heading for Site Health Info page. */
			_ex( 'Info', 'Site Health' );
			?>
		</a>
	</nav>
</div>

<hr class="wp-header-end">

<div class="notice notice-error hide-if-js">
	<p><?php _e( 'The Site Health check requires JavaScript.' ); ?></p>
</div>

<div class="health-check-body hide-if-no-js">
	<div class="site-status-all-clear hide">
		<p class="icon">
			<span class="dashicons dashicons-yes"></span>
		</p>

		<p class="encouragement">
			<?php _e( 'Great job!' ); ?>
		</p>

		<p>
			<?php _e( 'Everything is running smoothly here.' ); ?>
		</p>
	</div>

	<div class="site-status-has-issues">
		<h2>
			<?php _e( 'Site Health Status' ); ?>
		</h2>

		<p><?php _e( 'The site health check shows critical information about your WordPress configuration and items that require your attention.' ); ?></p>

		<div class="site-health-issues-wrapper" id="health-check-issues-critical">
			<h3 class="site-health-issue-count-title">
				<?php
					/* translators: %s: Number of critical issues found. */
					printf( _n( '%s Critical issue', '%s Critical issues', 0 ), '<span class="issue-count">0</span>' );
				?>
			</h3>

			<div id="health-check-site-status-critical" class="health-check-accordion issues"></div>
		</div>

		<div class="site-health-issues-wrapper" id="health-check-issues-recommended">
			<h3 class="site-health-issue-count-title">
				<?php
					/* translators: %s: Number of recommended improvements. */
					printf( _n( '%s Recommended improvement', '%s Recommended improvements', 0 ), '<span class="issue-count">0</span>' );
				?>
			</h3>

			<div id="health-check-site-status-recommended" class="health-check-accordion issues"></div>
		</div>
	</div>

	<div class="site-health-view-more">
		<button type="button" class="button site-health-view-passed" aria-expanded="false" aria-controls="health-check-issues-good">
			<?php _e( 'Passed tests' ); ?>
			<span class="icon"></span>
		</button>
	</div>

	<div class="site-health-issues-wrapper hidden" id="health-check-issues-good">
		<h3 class="site-health-issue-count-title">
			<?php
				/* translators: %s: Number of items with no issues. */
				printf( _n( '%s Item with no issues detected', '%s Items with no issues detected', 0 ), '<span class="issue-count">0</span>' );
			?>
		</h3>

		<div id="health-check-site-status-good" class="health-check-accordion issues"></div>
	</div>
</div>

<script id="tmpl-health-check-issue" type="text/template">
	<h4 class="health-check-accordion-heading">
		<button aria-expanded="false" class="health-check-accordion-trigger" aria-controls="health-check-accordion-block-{{ data.test }}" type="button">
			<span class="title">{{ data.label }}</span>
			<span class="badge {{ data.badge.color }}">{{ data.badge.label }}</span>
			<span class="icon"></span>
		</button>
	</h4>
	<div id="health-check-accordion-block-{{ data.test }}" class="health-check-accordion-panel" hidden="hidden">
		{{{ data.description }}}
		<div class="actions">
			<p class="button-container">{{{ data.actions }}}</p>
		</div>
	</div>
</script>

<?php
include( ABSPATH . 'wp-admin/admin-footer.php' );
