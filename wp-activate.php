<?php
/**
 * Confirms that the activation key that is sent in an email after a user signs
 * up for a new blog matches the key for that user and then displays confirmation.
 *
 * @package WordPress
 */

define( 'WP_INSTALLING', true );

/** Sets up the WordPress Environment. */
require( dirname(__FILE__) . '/wp-load.php' );

require( dirname( __FILE__ ) . '/wp-blog-header.php' );

if ( !is_multisite() ) {
	wp_redirect( wp_registration_url() );
	die();
}

if ( is_object( $wp_object_cache ) )
	$wp_object_cache->cache_enabled = false;

// Fix for page title
$wp_query->is_404 = false;

/**
 * Fires before the Site Activation page is loaded.
 *
 * @since 3.0.0
 */
do_action( 'activate_header' );

/**
 * Adds an action hook specific to this page that fires on wp_head
 *
 * @since MU
 */
function do_activate_header() {
    /**
     * Fires before the Site Activation page is loaded, but on the wp_head action.
     *
     * @since 3.0.0
     */
    do_action( 'activate_wp_head' );
}
add_action( 'wp_head', 'do_activate_header' );

/**
 * Loads styles specific to this page.
 *
 * @since MU
 */
function wpmu_activate_stylesheet() {
	?>
	<style type="text/css">
		form { margin-top: 2em; }
		#submit, #key { width: 90%; font-size: 24px; }
		#language { margin-top: .5em; }
		.error { background: #f66; }
		span.h3 { padding: 0 8px; font-size: 1.3em; font-weight: bold; }
	</style>
	<?php
}
add_action( 'wp_head', 'wpmu_activate_stylesheet' );

get_header( 'wp-activate' );
?>

<div id="signup-content" class="widecolumn">
	<div class="wp-activate-container">
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
		$result = wpmu_activate_signup( $key );
		if ( is_wp_error($result) ) {
			if ( 'already_active' == $result->get_error_code() || 'blog_taken' == $result->get_error_code() ) {
			    $signup = $result->get_error_data();
				?>
				<h2><?php _e('Your account is now active!'); ?></h2>
				<?php
				echo '<p class="lead-in">';
				if ( $signup->domain . $signup->path == '' ) {
					printf( __('Your account has been activated. You may now <a href="%1$s">log in</a> to the site using your chosen username of &#8220;%2$s&#8221;. Please check your email inbox at %3$s for your password and login instructions. If you do not receive an email, please check your junk or spam folder. If you still do not receive an email within an hour, you can <a href="%4$s">reset your password</a>.'), network_site_url( 'wp-login.php', 'login' ), $signup->user_login, $signup->user_email, wp_lostpassword_url() );
				} else {
					printf( __('Your site at <a href="%1$s">%2$s</a> is active. You may now log in to your site using your chosen username of &#8220;%3$s&#8221;. Please check your email inbox at %4$s for your password and login instructions. If you do not receive an email, please check your junk or spam folder. If you still do not receive an email within an hour, you can <a href="%5$s">reset your password</a>.'), 'http://' . $signup->domain, $signup->domain, $signup->user_login, $signup->user_email, wp_lostpassword_url() );
				}
				echo '</p>';
			} else {
				?>
				<h2><?php _e('An error occurred during the activation'); ?></h2>
				<?php
			    echo '<p>'.$result->get_error_message().'</p>';
			}
		} else {
			$url = isset( $result['blog_id'] ) ? get_blogaddress_by_id( (int) $result['blog_id'] ) : '';
			$user = get_userdata( (int) $result['user_id'] );
			?>
			<h2><?php _e('Your account is now active!'); ?></h2>

			<div id="signup-welcome">
				<p><span class="h3"><?php _e('Username:'); ?></span> <?php echo $user->user_login ?></p>
				<p><span class="h3"><?php _e('Password:'); ?></span> <?php echo $result['password']; ?></p>
			</div>

			<?php if ( $url && $url != network_home_url( '', 'http' ) ) :
				switch_to_blog( (int) $result['blog_id'] ); 
				$login_url = wp_login_url(); 
				restore_current_blog(); 
				?>
				<p class="view"><?php printf( __( 'Your account is now activated. <a href="%1$s">View your site</a> or <a href="%2$s">Log in</a>' ), $url, esc_url( $login_url ) ); ?></p>
			<?php else: ?>
				<p class="view"><?php printf( __('Your account is now activated. <a href="%1$s">Log in</a> or go back to the <a href="%2$s">homepage</a>.' ), network_site_url('wp-login.php', 'login'), network_home_url() ); ?></p>
			<?php endif;
		}
	}
	?>
	</div>
</div>
<script type="text/javascript">
	var key_input = document.getElementById('key');
	key_input && key_input.focus();
</script>
<?php get_footer( 'wp-activate' );
