<?php
/**
 * Multisite themes administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

require_once( './admin.php' );

$wp_list_table = get_list_table('WP_MS_Themes_List_Table');
$wp_list_table->check_permissions();

$action = $wp_list_table->current_action();

$s = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';

// Clean up request URI from temporary args for screen options/paging uri's to work as expected.
$_SERVER['REQUEST_URI'] = remove_query_arg(array('network-enable', 'network-disable', 'network-enable-selected', 'network-disable-selected'), $_SERVER['REQUEST_URI']);

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

if ( ! $id )
	wp_die( __('Invalid site ID.') );
	
$wp_list_table->site_id = $id;
$wp_list_table->is_site_themes = true;
$wp_list_table->prepare_items();

$details = get_blog_details( $id );
if ( $details->site_id != $wpdb->siteid )
	wp_die( __( 'You do not have permission to access this page.' ) );

$is_main_site = is_main_site( $id );

if ( $action ) {
	switch_to_blog( $id );
	$allowed_themes = get_option( 'allowedthemes' );

	switch ( $action ) {
		case 'enable':
			$theme = $_GET['theme'];
			if ( !$allowed_themes )
				$allowed_themes = array( $theme => true );
			else
				$allowed_themes[$theme] = true;
			break;
		case 'disable':
			$theme = $_GET['theme'];
			if ( !$allowed_themes )
				$allowed_themes = array();
			else
				unset( $allowed_themes[$theme] );
			break;
		case 'enable-selected':
			$themes = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			if ( empty($themes) ) {
				restore_current_blog();
				wp_redirect( wp_get_referer() );
				exit;
			}						
			foreach( (array) $themes as $theme )
				$allowed_themes[ $theme ] = true;
			break;
		case 'disable-selected':
			$themes = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			if ( empty($themes) ) {
				restore_current_blog();
				wp_redirect( wp_get_referer() );
				exit;
			}						
			foreach( (array) $themes as $theme )
				unset( $allowed_themes[ $theme ] );
			break;
	}
	
	update_option( 'allowedthemes', $allowed_themes );
	restore_current_blog();
	
	wp_redirect( wp_get_referer() ); // @todo add_query_arg for update message
	exit;	
}

$title = sprintf( __('Edit Site: %s'), get_blogaddress_by_id($id));
$parent_file = 'sites.php';
$submenu_file = 'sites.php';

require('../admin-header.php');

add_thickbox();

add_screen_option( 'per_page', array('label' => _x( 'Themes', 'themes per page (screen options)' ), 'default' => 999) );

require_once(ABSPATH . 'wp-admin/admin-header.php');
?>

<div class="wrap">
<?php screen_icon('ms-admin'); ?>
<h2 id="edit-site"><?php echo $title ?></h2>
<h3 class="nav-tab-wrapper">
<?php
$tabs = array( 'site-info' => array( 'label' => __('Info'), 'url' => 'site-info.php'),  'site-options' => array( 'label' => __('Options'), 'url' => 'site-options.php'),
			  'site-users' => array( 'label' => __('Users'), 'url' => 'site-users.php'),  'site-themes' => array( 'label' => __('Themes'), 'url' => 'site-themes.php'));
foreach ( $tabs as $tab_id => $tab ) {
	$class = ( $tab['url'] == $pagenow ) ? ' nav-tab-active' : '';
	echo '<a href="' . $tab['url'] . '?id=' . $id .'" class="nav-tab' . $class . '">' .  esc_html( $tab['label'] ) . '</a>';
}
?>
</h3>
<p class="description"><?php _e( 'Network enabled themes are not shown on this screen.' ) ?></p>
<?php
if ( ! empty( $messages ) ) {
	foreach ( $messages as $msg )
		echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
}

$wp_list_table->views(); ?>

<form method="post" action="site-themes.php?action=update-site">
	<?php wp_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />

<?php $wp_list_table->display(); ?>

</form>

</div>
<?php include(ABSPATH . 'wp-admin/admin-footer.php'); ?>