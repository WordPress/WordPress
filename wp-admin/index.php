<?php
/**
 * Dashboard Administration Panel
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Bootstrap */
require_once('admin.php');

/** Load WordPress dashboard API */
require_once(ABSPATH . 'wp-admin/includes/dashboard.php');

wp_dashboard_setup();

/**
 * Display dashboard widget custom JavaScript.
 *
 * @since unknown
 */
function index_js() {
?>
<script type="text/javascript">
jQuery(function($) {
	var ajaxWidgets = {
		dashboard_incoming_links: 'incominglinks',
		dashboard_primary: 'devnews',
		dashboard_secondary: 'planetnews',
		dashboard_plugins: 'plugins'
	};
	$.each( ajaxWidgets, function(i,a) {
		var e = jQuery('#' + i + ' div.dashboard-widget-content').not('.dashboard-widget-control').find('.widget-loading');
		if ( e.size() ) { e.parent().load('index-extra.php?jax=' + a); }
	} );
});
</script>
<?php
}
add_action( 'admin_head', 'index_js' );

wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'plugin-install' );
wp_admin_css( 'dashboard' );
wp_admin_css( 'plugin-install' );
add_thickbox();

$title = __('Dashboard');
$parent_file = 'index.php';
require_once('admin-header.php');

$today = current_time('mysql', 1);
?>

<div class="wrap">
<div id="dashboard-widgets-wrap">

<?php wp_dashboard(); ?>


</div><!-- dashboard-widgets-wrap -->

</div><!-- wrap -->

<?php require('./admin-footer.php'); ?>
