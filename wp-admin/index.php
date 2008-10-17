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

wp_enqueue_script( 'dashboard' );
wp_enqueue_script( 'plugin-install' );
wp_enqueue_script( 'media-upload' );
wp_admin_css( 'dashboard' );
wp_admin_css( 'plugin-install' );
add_thickbox();

$title = __('Dashboard');
$parent_file = 'index.php';
require_once('admin-header.php');

$today = current_time('mysql', 1);
?>

<div id="edit-settings-wrap" class="hidden">
<h5><?php _e('Show on screen') ?></h5>
<form id="adv-settings" action="" method="get">
<div class="metabox-prefs">
<?php meta_box_prefs('dashboard') ?>
<br class="clear" />
</div></form>
</div>

<div class="wrap">
<h2><?php echo $title ?></h2>

<div id="dashboard-widgets-wrap">

<?php wp_dashboard(); ?>


</div><!-- dashboard-widgets-wrap -->

</div><!-- wrap -->

<?php require('./admin-footer.php'); ?>
