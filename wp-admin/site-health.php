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

if ( ! current_user_can( 'install_plugins' ) ) {
	wp_die( __( 'Sorry, you do not have permission to access site health information.' ), '', array( 'reponse' => 401 ) );
}

wp_enqueue_style( 'site-health' );
wp_enqueue_script( 'site-health' );

if ( ! class_exists( 'WP_Site_Health' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-site-health.php' );
}

$health_check_site_status = new WP_Site_Health();

require_once( ABSPATH . 'wp-admin/admin-header.php' );
?>

<div class="wrap health-check-header">
	<div class="title-section">
		<h1>
			<?php _ex( 'Site Health', 'Menu, Section and Page Title' ); ?>
		</h1>

		<div class="site-health-progress loading">
			<svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
				<circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
				<circle id="bar" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
			</svg>
			<span class="screen-reader-text"><?php _e( 'Current health score:' ); ?></span>
			<span class="progress-count"></span>
		</div>
	</div>

	<nav class="tabs-wrapper" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
		<a href="<?php echo esc_url( admin_url( 'site-health.php' ) ); ?>" class="tab active" aria-current="true">
			<?php _e( 'Status' ); ?>
		</a>

		<a href="<?php echo esc_url( admin_url( 'site-health.php?tab=debug' ) ); ?>" class="tab">
			<?php _e( 'Info' ); ?>
		</a>
	</nav>

	<div class="wp-clearfix"></div>
</div>

<div class="wrap health-check-body">
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

		<div class="issues-wrapper" id="health-check-issues-critical">
			<h3>
				<span class="issue-count">0</span> <?php _e( 'Critical issues' ); ?>
			</h3>

			<dl id="health-check-site-status-critical" role="presentation" class="health-check-accordion issues"></dl>
		</div>

		<div class="issues-wrapper" id="health-check-issues-recommended">
			<h3>
				<span class="issue-count">0</span> <?php _e( 'Recommended improvements' ); ?>
			</h3>

			<dl id="health-check-site-status-recommended" role="presentation" class="health-check-accordion issues"></dl>
		</div>
	</div>

	<div class="view-more">
		<button type="button" class="button site-health-view-passed" aria-expanded="false" aria-controls="health-check-issues-good">
			<?php _e( 'Passed tests' ); ?>
		</button>
	</div>

	<div class="issues-wrapper hidden" id="health-check-issues-good">
		<h3>
			<span class="issue-count">0</span> <?php _e( 'Items with no issues detected' ); ?>
		</h3>

		<dl id="health-check-site-status-good" role="presentation" class="health-check-accordion issues"></dl>
	</div>
</div>

<script id="tmpl-health-check-issue" type="text/template">
	<dt role="heading" aria-level="4">
		<button aria-expanded="false" class="health-check-accordion-trigger" aria-controls="health-check-accordion-block-{{ data.test }}" id="health-check-accordion-heading-{{ data.test }}" type="button">
			<span class="title">{{ data.label }}</span>
			<span class="badge {{ data.badge.color }}">{{ data.badge.label }}</span>
			<span class="icon"></span>
		</button>
	</dt>
	<dd id="health-check-accordion-block-{{ data.test }}" aria-labelledby="health-check-accordion-heading-{{ data.test }}" role="region" class="health-check-accordion-panel" hidden="hidden">
		{{{ data.description }}}
		<div class="actions">
			<p class="button-container">{{{ data.actions }}}</p>
		</div>
	</dd>
</script>

<?php
include( ABSPATH . 'wp-admin/admin-footer.php' );
