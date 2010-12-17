<?php
/**
 * My Sites dashboard.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

require_once( './admin.php' );

if ( !is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can('read') )
	wp_die( __( 'You do not have sufficient permissions to view this page.' ) );

$action = isset( $_POST['action'] ) ? $_POST['action'] : 'splash';

$blogs = get_blogs_of_user( $current_user->id );

if ( empty( $blogs ) )
	wp_die( __( 'You must be a member of at least one site to use this page.' ) );

$updated = false;
if ( 'updateblogsettings' == $action && isset( $_POST['primary_blog'] ) ) {
	check_admin_referer( 'update-my-sites' );

	$blog = get_blog_details( (int) $_POST['primary_blog'] );
	if ( $blog && isset( $blog->domain ) ) {
		update_user_option( $current_user->id, 'primary_blog', (int) $_POST['primary_blog'], true );
		$updated = true;
	} else {
		wp_die( __( 'The primary site you chose does not exist.' ) );
	}
}

$title = __( 'My Sites' );
$parent_file = 'index.php';

add_contextual_help($current_screen,
	'<p>' . __('This screen shows an individual user all of their sites in this network, and also allows that user to set a primary site. He or she can use the links under each site to visit either the frontend or the dashboard for that site.') . '</p>' .
	'<p>' . __('Up until WordPress version 3.0, what is now called a Multi-site Network had to be installed separately as WordPress MU (multi-user).') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Dashboard_My_Sites_SubPanel" target="_blank">Documentation on My Sites</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

require_once( './admin-header.php' );

if ( $updated ) { ?>
	<div id="message" class="updated"><p><strong><?php _e( 'Settings saved.' ); ?></strong></p></div>
<?php } ?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>
<form id="myblogs" action="" method="post">
	<?php
	choose_primary_blog();
	do_action( 'myblogs_allblogs_options' );
	?>
	<br clear="all" />
	<table class="widefat fixed">
	<?php
	$settings_html = apply_filters( 'myblogs_options', '', 'global' );
	if ( $settings_html != '' ) {
		echo '<tr><td valign="top"><h3>' . __( 'Global Settings' ) . '</h3></td><td>';
		echo $settings_html;
		echo '</td></tr>';
	}
	reset( $blogs );
	$num = count( $blogs );
	$cols = 1;
	if ( $num >= 20 )
		$cols = 4;
	elseif ( $num >= 10 )
		$cols = 2;
	$num_rows = ceil( $num / $cols );
	$split = 0;
	for ( $i = 1; $i <= $num_rows; $i++ ) {
		$rows[] = array_slice( $blogs, $split, $cols );
		$split = $split + $cols;
	}

	$c = '';
	foreach ( $rows as $row ) {
		$c = $c == 'alternate' ? '' : 'alternate';
		echo "<tr class='$c'>";
		$i = 0;
		foreach ( $row as $user_blog ) {
			$s = $i == 3 ? '' : 'border-right: 1px solid #ccc;';
			echo "<td valign='top' style='$s'>";
			echo "<h3>{$user_blog->blogname}</h3>";
			echo "<p>" . apply_filters( 'myblogs_blog_actions', "<a href='" . esc_url( get_home_url( $user_blog->userblog_id ) ). "'>" . __( 'Visit' ) . "</a> | <a href='" . esc_url( get_admin_url( $user_blog->userblog_id ) ) . "'>" . __( 'Dashboard' ) . "</a>", $user_blog ) . "</p>";
			echo apply_filters( 'myblogs_options', '', $user_blog );
			echo "</td>";
			$i++;
		}
		echo "</tr>";
	}?>
	</table>
	<input type="hidden" name="action" value="updateblogsettings" />
	<?php wp_nonce_field( 'update-my-sites' ); ?>
	<?php submit_button(); ?>
	</form>
	</div>
<?php
include( './admin-footer.php' );
?>
