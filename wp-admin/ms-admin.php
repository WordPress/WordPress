<?php
require_once('admin.php');

if ( !is_multisite() )
	wp_die( __('Multisite support is not enabled.') );

$title = __('Site Admin');
$parent_file = 'ms-admin.php';

function index_css() {
	wp_admin_css( 'css/dashboard' );
}
add_action( 'admin_head', 'index_css' );

require_once('admin-header.php');

if ( !is_super_admin() )
	wp_die( __('You do not have permission to access this page.') );

global $wpdb;
$c_users = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->users}");
$c_blogs = $wpdb->get_var("SELECT COUNT(blog_id) FROM {$wpdb->blogs}");

$user_text = sprintf( __ngettext( '%s user', '%s users', $c_users ), number_format_i18n( $c_users ) );
$blog_text = sprintf( __ngettext( '%s blog', '%s blogs', $c_blogs ), number_format_i18n( $c_blogs ) );

$sentence = sprintf( __( 'You have %1$s and %2$s.' ), $blog_text, $user_text );
$title = __( 'Site Admin' );
?>

<div class="wrap">
	<h2><?php echo esc_html( $title ); ?></h2>

	<ul class="subsubsub">
	<li><a href="ms-sites.php#form-add-blog" class="rbutton"><strong><?php _e('Create a New Blog'); ?></strong></a> | </li>
	<li><a href="ms-users.php#form-add-user" class="rbutton"><?php _e('Create a New User'); ?></a></li>
	</ul>
	<br clear='all' />

	<p class="youhave"><?php echo $sentence; ?></p>
	<?php do_action('wpmuadminresult', ''); ?>

	<form name="searchform" action="ms-users.php" method="get">
		<p>
			<input type="hidden" name="action" value="users" />
			<input type="text" name="s" value="" size="17" />
			<input class="button" type="submit" name="submit" value="<?php esc_attr_e("Search Users"); ?>" />
		</p>
	</form>

	<form name="searchform" action="ms-sites.php" method="get">
		<p>
			<input type="hidden" name="action" value="blogs" />
			<input type="text" name="s" value="" size="17" />
			<input class="button" type="submit" name="blog_name" value="<?php esc_attr_e("Search Blogs"); ?>" />
		</p>
	</form>

	<?php do_action( 'mu_rightnow_end' ); ?>
	<?php do_action( 'mu_activity_box_end' ); ?>
	</div><!-- rightnow -->
</div>

<?php include('admin-footer.php'); ?>
