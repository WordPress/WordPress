<?php
require_once( './admin.php' );

if ( !is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can( 'manage_network' ) )
	wp_die( __( 'You do not have permission to access this page.' ) );

$title = __( 'Network Admin' );
$parent_file = 'ms-admin.php';

require_once( './admin-header.php' );

$c_users = get_user_count();
$c_blogs = get_blog_count();

$user_text = sprintf( _n( '%s user', '%s users', $c_users ), number_format_i18n( $c_users ) );
$blog_text = sprintf( _n( '%s site', '%s sites', $c_blogs ), number_format_i18n( $c_blogs ) );

$sentence = sprintf( __( 'You have %1$s and %2$s.' ), $blog_text, $user_text );
?>

<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( $title ); ?></h2>

	<ul class="subsubsub">
	<li><a href="ms-sites.php#form-add-site"><?php _e( 'Create a New Site' ); ?></a> |</li>
	<li><a href="ms-users.php#form-add-user"><?php _e( 'Create a New User' ); ?></a></li>
	</ul>
	<br class="clear" />

	<p class="youhave"><?php echo $sentence; ?></p>
	<?php do_action( 'wpmuadminresult', '' ); ?>

	<form name="searchform" action="ms-users.php" method="get">
		<p>
			<input type="hidden" name="action" value="users" />
			<input type="text" name="s" value="" size="17" />
			<input class="button" type="submit" name="submit" value="<?php esc_attr_e( 'Search Users' ); ?>" />
		</p>
	</form>

	<form name="searchform" action="ms-sites.php" method="get">
		<p>
			<input type="hidden" name="action" value="blogs" />
			<input type="text" name="s" value="" size="17" />
			<input class="button" type="submit" name="blog_name" value="<?php esc_attr_e( 'Search Sites' ); ?>" />
		</p>
	</form>

	<?php do_action( 'mu_rightnow_end' ); ?>
	<?php do_action( 'mu_activity_box_end' ); ?>
</div>

<?php include( './admin-footer.php' ); ?>
