<?php
define( "WP_INSTALLING", true );

/** Sets up the WordPress Environment. */
require( dirname(__FILE__) . '/wp-load.php' );

require( 'wp-blog-header.php' );

if ( !is_multisite() ) {
	wp_redirect( get_option( 'siteurl' ) . "/wp-login.php?action=register" );
	die();
}

require_once( ABSPATH . WPINC . '/registration.php');

if ( is_object( $wp_object_cache ) )
	$wp_object_cache->cache_enabled = false;

do_action("activate_header");

function do_activate_header() {
	do_action("activate_wp_head");
}
add_action( 'wp_head', 'do_activate_header' );

function wpmu_activate_stylesheet() {
	?>
	<style type="text/css">
		form { margin-top: 2em; }
		#submit, #key { width: 90%; font-size: 24px; }
		#language { margin-top: .5em; }
		.error { background: #f66; }
		span.h3 { padding:0 8px; font-size:1.3em; font-family:'Trebuchet MS','Lucida Grande',Verdana,Arial,Sans-Serif; font-weight:700; color:#333333; }
	</style>
	<?php
}
add_action( 'wp_head', 'wpmu_activate_stylesheet' );

get_header();
?>

<div id="content" class="widecolumn">
	<?php if ( empty($_GET['key']) && empty($_POST['key']) ) { ?>

		<h2><?php _e('Activation Key Required') ?></h2>
		<form name="activateform" id="activateform" method="post" action="<?php echo network_site_url('wp-activate.php'); ?>">
			<p>
			    <label for="key"><?php _e('Activation Key:') ?></label>
			    <br /><input type="text" name="key" id="key" value="" size="50" />
			</p>
			<p class="submit">
			    <input id="submit" type="submit" name="Submit" class="submit" value="<?php esc_attr_e('Activate') ?>" />
			</p>
		</form>

	<?php } else {

		$key = !empty($_GET['key']) ? $_GET['key'] : $_POST['key'];
		$result = wpmu_activate_signup($key);
		if ( is_wp_error($result) ) {
			if ( 'already_active' == $result->get_error_code() || 'blog_taken' == $result->get_error_code() ) {
			    $signup = $result->get_error_data();
				?>
				<h2><?php _e('Your account is now active!'); ?></h2>
				<?php
			    if ( $signup->domain . $signup->path == '' ) {
			    	printf(__('<p class="lead-in">Your account has been activated. You may now <a href="%1$s">login</a> to the site using your chosen username of &#8220;%2$s&#8221;.  Please check your email inbox at %3$s for your password and login instructions. If you do not receive an email, please check your junk or spam folder. If you still do not receive an email within an hour, you can <a href="%4$s">reset your password</a>.</p>'), network_site_url('wp-login.php', 'login'), $signup->user_login, $signup->user_email, network_site_url('wp-login.php?action=lostpassword', 'login'));
			    } else {
			    	printf(__('<p class="lead-in">Your site at <a href="%1$s">%2$s</a> is active. You may now log in to your site using your chosen username of &#8220;%3$s&#8221;.  Please check your email inbox at %4$s for your password and login instructions.  If you do not receive an email, please check your junk or spam folder.  If you still do not receive an email within an hour, you can <a href="%5$s">reset your password</a>.</p>'), 'http://' . $signup->domain, $signup->domain, $signup->user_login, $signup->user_email, network_site_url('wp-login.php?action=lostpassword'));
			    }
			} else {
				?>
				<h2><?php _e('An error occurred during the activation'); ?></h2>
				<?php
			    echo '<p>'.$result->get_error_message().'</p>';
			}
		} else {
			extract($result);
			$url = get_blogaddress_by_id( (int) $blog_id);
			$user = new WP_User( (int) $user_id);
			?>
			<h2><?php _e('Your account is now active!'); ?></h2>

			<div id="signup-welcome">
				<p><span class="h3"><?php _e('Username:'); ?></span> <?php echo $user->user_login ?></p>
				<p><span class="h3"><?php _e('Password:'); ?></span> <?php echo $password; ?></p>
			</div>

			<?php if ( $url != network_home_url('', 'http') ) : ?>
				<p class="view"><?php printf(__('Your account is now activated. <a href="%1$s">View your site</a> or <a href="%2$s">Login</a>'), $url, $url . 'wp-login.php' ); ?></p>
			<?php else: ?>
				<p class="view"><?php printf( __( 'Your account is now activated. <a href="%1$s">Login</a> or go back to the <a href="%2$s">homepage</a>.' ), network_site_url('wp-login.php', 'login'), network_home_url() ); ?></p>
			<?php endif;
		}
	}
	?>
</div>

<?php get_footer(); ?>
