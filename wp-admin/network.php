<?php
/**
 * Network installation administration panel.
 *
 * A multi-step process allowing the user to enable a network of WordPress sites.
 *
 * @since 3.0.0
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( './admin.php' );

if ( ! is_super_admin() )
	wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );

if ( is_multisite() )
	wp_die( __( 'The Network feature is already enabled.' ) );

include( ABSPATH . 'wp-admin/includes/network.php' );

// We need to create references to ms global tables to enable Network.
foreach ( $wpdb->tables( 'ms_global' ) as $table => $prefixed_table )
	$wpdb->$table = $prefixed_table;

$title = __( 'Create a Network of WordPress Sites' );
$parent_file = 'tools.php';

add_contextual_help( $current_screen, __( '<a href="http://codex.wordpress.org/Settings_Network_SubPanel" target="_blank">Network Settings</a>') );

include( './admin-header.php' );

$dirs = array( substr( ABSPATH, 0, -1 ), ABSPATH . 'wp-content' );
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<form method="post" action="network.php">
<?php
/**
 * Prints step 1 for Network installation process.
 *
 * @todo Realistically, step 1 should be a welcome screen explaining what a Network is and such. Navigating to Tools > Network
 * 	should not be a sudden "Welcome to a new install process! Fill this out and click here."
 *
 * @since 3.0.0
 */
function ms_network_step1() {

	$active_plugins = get_option( 'active_plugins' );
	if ( ! empty( $active_plugins ) ) {
		printf( '<p>' . __( 'Please <a href="%s">deactivate</a> your plugins before enabling the Network feature. Once the network is created, you may reactivate your plugins.' ) . '</p>', admin_url( 'plugins.php' ) );
		include( './admin-footer.php' );
		die();
	}

	$hostname = get_clean_basedomain();
	if ( 'localhost' == $hostname || preg_match( '|[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+|', $hostname ) ) {
		echo '<p><strong>' . __('You cannot install a network of sites with your server address.' ) . '</strong></p>';
		echo '<p>' . __('You cannot use an IP address such as <code>127.0.0.1</code> or a single-word hostname like <code>localhost</code>.' ) . '</p>';
		if ( 'localhost' == $hostname )
			echo '<p>' . __('Consider using <code>localhost.localdomain</code>.') . '</p>';
		include( './admin-footer.php' );
		die();
	}

	?>
	<p><?php _e( 'Welcome to the Network installation process!' ); ?></p>
	<p><?php _e( "Fill in the information below and you&#8217;ll be on your way to creating a network of WordPress sites. We'll create configuration files in the next step." ); ?></p>
	<?php

	if ( apache_mod_loaded('mod_rewrite') ) { // assume nothing
		$rewrite_enabled = true;
	} else {
		$rewrite_enabled = false;
		if ( got_mod_rewrite() ) // dangerous assumptions
			echo '<p>' . __( 'Please make sure the Apache <code>mod_rewrite</code> module is installed as it will be used at the end of this install.' ) . '</p>';
		else
			echo '<p>' . __( '<strong>Warning!</strong> It looks like Apache <code>mod_rewrite</code> module is not installed.' ) . '</p>';
		echo '<p>' . __( 'If <code>mod_rewrite</code> is disabled ask your administrator to enable that module, or look at the <a href="http://httpd.apache.org/docs/mod/mod_rewrite.html">Apache documentation</a> or <a href="http://www.google.com/search?q=apache+mod_rewrite">elsewhere</a> for help setting it up.' ) . '</p>';
	}

	wp_nonce_field( 'install-network-1' );
	if ( network_domain_check() ) { ?>
		<h2><?php esc_html_e( 'Existing Sites' ); ?></h2>
		<p><?php _e( 'An existing WordPress Network was detected.' ); ?></p>
		<p class="existing-network">
			<label><input type='checkbox' name='existing_network' value='1' /> <?php _e( 'Yes, keep the existing network of sites.' ); ?></label><br />
		</p>

<?php 	} else { ?>
		<input type='hidden' name='existing_network' value='0' />
<?php	} ?>
		<input type='hidden' name='action' value='step2' />
		<h3><?php esc_html_e( 'Addresses of Sites in your Network' ); ?></h3>
	
		<p><?php _e( 'Please choose whether you would like sites in your WordPress network to use sub-domains or sub-directories. <strong>You cannot change this later.</strong>' ); ?></p>
		<p><?php _e( "You will need a wildcard DNS record if you're going to use the virtual host (sub-domain) functionality." ); ?></p>
		<?php /* @todo: Link to an MS readme? */ ?>
		<?php if ( ! $rewrite_enabled ) { ?>
		<p><?php _e( '<strong>Note</strong> It looks like <code>mod_rewrite</code> is not installed.' ); ?></p>
		<?php } ?>
		<table class="form-table">
			<tr>
				<th><label><input type='radio' name='vhost' value='yes'<?php checked( $rewrite_enabled ); ?> /> Sub-domains</label></th>
				<td><?php _e('like <code>blog1.example.com</code> and <code>blog2.example.com</code>'); ?></td>
			</tr>
			<tr>
				<th><label><input type='radio' name='vhost' value='no'<?php checked( ! $rewrite_enabled ); ?> /> Sub-directories</label></th>
				<td><?php _e('like <code>example.com/blog1</code> and <code>example.com/blog2</code>'); ?></td>
			</tr>
		</table>

		<?php
		$is_www = ( substr( $hostname, 0, 4 ) == 'www.' );
		if ( $is_www ) :
		?>
		<h3><?php esc_html_e( 'Server Address' ); ?></h3>
		<p><?php printf( __( 'We recommend you change your siteurl to <code>%1$s</code> before enabling the network feature. It will still be possible to visit your site using the "www" prefix with an address like <code>%2$s</code> but any links will not have the "www" prefix.' ), substr( $hostname, 4 ), $hostname ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope='row'><?php esc_html_e( 'Server Address' ); ?></th>
				<td>
					<?php printf( __( 'The Internet address of your network will be <code>%s</code>.' ), $hostname ); ?>
					<input type='hidden' name='basedomain' value='<?php echo esc_attr( $hostname ); ?>' />
				</td>
			</tr>
		</table>
		<?php endif; ?>

		<h3><?php esc_html_e( 'Network Details' ); ?></h3>
		<table class="form-table">
		<?php if ( ! $is_www ) : ?>
			<tr>
				<th scope='row'><?php esc_html_e( 'Server Address' ); ?></th>
				<td>
					<?php printf( __( 'The Internet address of your network will be <code>%s</code>.' ), $hostname ); ?>
				</td>
			</tr>
		<?php endif; ?>
			<tr>
				<th scope='row'><?php esc_html_e( 'Network Title' ); ?></th>
				<td>
					<input name='weblog_title' type='text' size='45' value='<?php echo esc_attr( sprintf( __('%s Sites'), get_option( 'blogname' ) ) ); ?>' />
					<br /><?php _e( 'What would you like to call your network?' ); ?>
				</td>
			</tr>
			<tr>
				<th scope='row'><?php esc_html_e( 'Admin E-mail Address' ); ?></th>
				<td>
					<input name='email' type='text' size='45' value='<?php echo esc_attr( get_option( 'admin_email' ) ); ?>' />
					<br /><?php _e( 'Your email address.' ); ?>
				</td>
			</tr>
		</table>
		<p class='submit'><input class="button-primary" name='submit' type='submit' value='<?php esc_attr_e( 'Install' ); ?>' /></p>
		<?php
}

/**
 * Prints step 2 for Network installation process.
 *
 * @since 3.0.0
 */
function ms_network_step2() {
	global $base, $wpdb;
?>
		<h3><?php esc_html_e( 'Enabling the Network' ); ?></h3>
		<p><?php _e( 'Complete the following steps to enable the features for creating a network of sites. <strong>Note:</strong> We recommend you make a backup copy of your existing <code>wp-config.php</code> and <code>.htaccess</code> files.' ); ?></p>
		<ol>
			<li><?php printf( __( 'Create a <code>%s/blogs.dir</code> directory. This directory is used to stored uploaded media for your additional sites and must be writeable by the web server.' ), WP_CONTENT_DIR ); ?></li>
<?php
	$vhost   = stripslashes( $_POST['vhost' ] );
	$prefix  = $wpdb->base_prefix;

	$config_sample = ABSPATH . 'wp-admin/includes/ms-config-sample.php';
	if ( ! file_exists( $config_sample ) )
		wp_die( sprintf( __( 'Sorry, I need a <code>%s</code> to work from. Please re-upload this file to your WordPress installation.' ), $config_sample ) );

	$wp_config_file = file( $config_sample );
?>
			<li><p><?php printf( __( 'Replace the contents of <code>%swp-config.php</code> with the following:' ), ABSPATH ); ?></p>
				<textarea name="wp-config" cols="120" rows="20">
<?php
	foreach ( $wp_config_file as $line ) {
		switch ( trim( substr( $line, 0, 16 ) ) ) {
			case "define('DB_NAME'":
				$output = str_replace( "wordpress", DB_NAME, $line );
				break;
			case "define('DB_USER'":
				$output = str_replace( "username", DB_USER, $line );
				break;
			case "define('DB_PASSW":
				$output = str_replace( "password", DB_PASSWORD, $line );
				break;
			case "define('DB_HOST'":
				$output = str_replace( "localhost", DB_HOST, $line );
				break;
			case "define('VHOST',":
				$output = str_replace( "VHOSTSETTING", $vhost, $line );
				break;
			case '$table_prefix  =':
				$output = str_replace( 'wp_', $prefix, $line );
				break;
			case '$base = \'BASE\';':
				$output = str_replace( 'BASE', $base, $line );
				break;
			case "define('DOMAIN_C":
				$domain = get_clean_basedomain();
				$output = str_replace( "current_site_domain", $domain, $line );
				break;
			case "define('PATH_CUR":
				$output = str_replace( "current_site_path", $base, $line );
				break;
			case "define('AUTH_KEY":
			case "define('AUTH_SAL":
			case "define('LOGGED_I":
			case "define('SECURE_A":
			case "define('NONCE_KE":
				$constant = substr( $line, 8, strpos( $line, "'", 9 ) - 8 );
				if ( defined( $constant ) )
					$hash = constant( $constant );
				else
					$hash = md5( mt_rand() ) . md5( mt_rand() );
				$output = str_replace( 'put your unique phrase here', $hash, $line );
				break;
			default:
				$output = $line;
				break;
		}
		echo $output;
	}
?>
				</textarea>
			</li>
<?php

	// remove ending slash from $base and $url
	$htaccess = '';
	if ( substr( $base, -1 ) == '/' )
		$base = substr( $base, 0, -1 );

	$htaccess_sample = ABSPATH . 'wp-admin/includes/htaccess.ms';
	if ( ! file_exists( $htaccess_sample ) )
		wp_die( sprintf( __( 'Sorry, I need a %s to work from. Please re-upload this file to your WordPress installation.' ), $htaccess_sample ) );

	$htaccess_file = file( $htaccess_sample );
	$fp = @fopen( $htaccess_sample, "r" );
	if ( $fp ) {
		while ( ! feof( $fp ) ) {
			$htaccess .= fgets( $fp, 4096 );
		}
		fclose( $fp );
		$htaccess_file = str_replace( "BASE", $base, $htaccess );
	} else {
		wp_die( sprintf( __( 'Sorry, I need to be able to read %s. Please check the permissions on this file.' ), $htaccess_sample ) );
	}
?>
			<li><p><?php printf( __( 'Replace the contents of your <code>%s.htaccess</code> with the following:' ), ABSPATH ); ?></p>
				<textarea name="htaccess" cols="120" rows="20">
<?php echo wp_htmledit_pre( $htaccess_file ); ?>
				</textarea>
			</li>
		</ol>
<?php
}

$action = isset( $_POST['action'] ) ? $_POST['action'] : null;

switch ( $action ) {
	case 'step2':
		check_admin_referer( 'install-network-1' );

		// Install!
		$base = trailingslashit( stripslashes( dirname( dirname( $_SERVER['SCRIPT_NAME'] ) ) ) );

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		// create network tables
		install_network();
		if ( !network_domain_check() || $_POST['existing_network'] == '0' )
			populate_network( 1, get_clean_basedomain(), sanitize_email( $_POST['email'] ), $_POST['weblog_title'], $base, $_POST['vhost'] );
		// create wp-config.php / htaccess
		ms_network_step2();
		break;

	default:
		ms_network_step1();
		break;
}
?>
</form>
</div>

<?php include( './admin-footer.php' ); ?>
