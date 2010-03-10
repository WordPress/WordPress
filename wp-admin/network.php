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

// We need to create references to ms global tables to enable Network.
foreach ( $wpdb->tables( 'ms_global' ) as $table => $prefixed_table )
	$wpdb->$table = $prefixed_table;

/**
 * Check for existing network data/tables.
 *
 * @since 3.0.0
 */
function network_domain_check() {
	global $wpdb;
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->site'" ) )
		return $wpdb->get_var( "SELECT domain FROM $wpdb->site ORDER BY id ASC LIMIT 1" );
	return false;
}

/**
 * Get base domain of network.
 *
 * @since 3.0.0
 */
function get_clean_basedomain() {
	global $wpdb;
	$existing_domain = network_domain_check();
	if ( $existing_domain )
		return $existing_domain;
	$domain = preg_replace( '|https?://|', '', get_option( 'siteurl' ) );
	if ( strpos( $domain, '/' ) )
		$domain = substr( $domain, 0, strpos( $domain, '/' ) );
	return $domain;
}

if ( ! network_domain_check() && ( ! defined( 'WP_ALLOW_MULTISITE' ) || ! WP_ALLOW_MULTISITE ) )
	wp_die( __( 'You must define the <code>WP_ALLOW_MULTISITE</code> constant as true in your wp-config.php file to allow creation of a Network.' ) );

$title = __( 'Create a Network of WordPress Sites' );
$parent_file = 'tools.php';

add_contextual_help( $current_screen, __( '<a href="http://codex.wordpress.org/Settings_Network_SubPanel" target="_blank">Network Settings</a>') );

include( './admin-header.php' );
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
function network_step1() {

	$active_plugins = get_option( 'active_plugins' );
	if ( ! empty( $active_plugins ) ) {
		echo '<div class="updated"><p><strong>' . __('Warning:') . '</strong> ' . sprintf( __( 'Please <a href="%s">deactivate</a> your plugins before enabling the Network feature.' ), admin_url( 'plugins.php' ) ) . '</p></div><p>' . __(' Once the network is created, you may reactivate your plugins.' ) . '</p>';
		include( './admin-footer.php' );
		die();
	}

	$hostname = get_clean_basedomain();
	$has_ports = strstr( $hostname, ':' );
	if ( ( false !== $has_ports && ! in_array( $has_ports, array( ':80', ':443' ) ) )
		|| ( $no_ip = preg_match( '|[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+|', $hostname ) ) ) {
		echo '<div class="error"><p><strong>' . __( 'Error:') . '</strong> ' . __( 'You cannot install a network of sites with your server address.' ) . '</strong></p></div>';
		if ( $no_ip )
			echo '<p>' . __('You cannot use an IP address such as <code>127.0.0.1</code>.' ) . '</p>';
		else
			echo '<p>' . sprintf( __('You cannot use port numbers such as <code>%s</code>.' ), $has_ports ) . '</p>';
		echo '<a href="' . esc_url( admin_url() ) . '">' . __( 'Return to Dashboard' ) . '</a>';
		include( './admin-footer.php' );
		die();
	}

	?>
	<p><?php _e( 'Welcome to the Network installation process!' ); ?></p>
	<p><?php _e( "Fill in the information below and you'll be on your way to creating a network of WordPress sites. We'll create configuration files in the next step." ); ?></p>
	<?php

	// @todo IIS...
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

	if ( 'localhost' != $hostname ) : ?>
		<h3><?php esc_html_e( 'Addresses of Sites in your Network' ); ?></h3>
		<p><?php _e( 'Please choose whether you would like sites in your WordPress network to use sub-domains or sub-directories. <strong>You cannot change this later.</strong>' ); ?></p>
		<p><?php _e( "You will need a wildcard DNS record if you're going to use the virtual host (sub-domain) functionality." ); ?></p>
		<?php /* @todo: Link to an MS readme? */ ?>
		<?php if ( ! $rewrite_enabled ) { ?>
		<p><?php _e( '<strong>Note</strong> It looks like <code>mod_rewrite</code> is not installed.' ); ?></p>
		<?php } ?>
		<table class="form-table">
			<tr>
				<th><label><input type='radio' name='subdomain_install' value='1'<?php checked( $rewrite_enabled ); ?> /> Sub-domains</label></th>
				<td><?php _e('like <code>site1.example.com</code> and <code>site2.example.com</code>'); ?></td>
			</tr>
			<tr>
				<th><label><input type='radio' name='subdomain_install' value='0'<?php checked( ! $rewrite_enabled ); ?> /> Sub-directories</label></th>
				<td><?php _e('like <code>example.com/site1</code> and <code>example.com/site2</code>'); ?></td>
			</tr>
		</table>

<?php
	endif;

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
		<?php if ( 'localhost' == $hostname ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Sub-directory Install' ); ?></th>
				<td><?php _e('Because you are using <code>localhost</code>, the sites in your WordPress network must use sub-directories. Consider using <code>localhost.localdomain</code> if you wish to use sub-domains.'); ?></td>
			</tr>
		<?php endif; ?>
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
function network_step2() {
	global $base, $wpdb;
	$hostname = get_clean_basedomain();
	if ( $_POST ) {
		$vhost = 'localhost' == $hostname ? false : (bool) $_POST['subdomain_install'];
	} else {
		if ( is_multisite() ) {
			$vhost = is_subdomain_install();
?>
	<div class="updated"><p><strong><?php _e( 'Notice: The Network feature is already enabled.' ); ?></strong> <?php _e( 'The original configuration steps are shown here for reference.' ); ?></p></div>
<?php	} else {
			$vhost = (bool) $wpdb->get_var( "SELECT meta_value FROM $wpdb->sitemeta WHERE site_id = 1 AND meta_key = 'subdomain_install'" );
?>
	<div class="error"><p><strong><?php _e('Warning:'); ?></strong> <?php _e( 'An existing WordPress network was detected.' ); ?></p></div>
	<p><?php _e( 'Please complete the configuration steps. To create a new network, you will need to empty or remove the network database tables.' ); ?></p>
<?php
		}
	}

	if ( $_POST || ! is_multisite() ) {
?>
		<h3><?php esc_html_e( 'Enabling the Network' ); ?></h3>
		<p><?php _e( 'Complete the following steps to enable the features for creating a network of sites.' ); ?></p>
		<div class="updated inline"><p><?php _e( '<strong>Caution:</strong> We recommend you backup your existing <code>wp-config.php</code> and <code>.htaccess</code> files.' ); ?></p></div>
<?php
	}
?>
		<ol>
			<li><p><?php printf( __( 'Create a <code>blogs.dir</code> directory in <code>%s</code>. This directory is used to stored uploaded media for your additional sites and must be writeable by the web server.' ), WP_CONTENT_DIR ); ?></p></li>
			<li><p><?php printf( __( 'Add the following to your <code>wp-config.php</code> file in <code>%s</code>:' ), ABSPATH ); ?></p>
				<textarea class="code" readonly="readonly" cols="100" rows="7">
define( 'MULTISITE', true );
define( 'VHOST', '<?php echo $vhost ? 'yes' : 'no'; ?>' );
$base = '<?php echo $base; ?>';
define( 'DOMAIN_CURRENT_SITE', '<?php echo $hostname; ?>' );
define( 'PATH_CURRENT_SITE', '<?php echo $base; ?>' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );</textarea>
<?php
	$keys_salts = array( 'AUTH_KEY' => '', 'SECURE_AUTH_KEY' => '', 'LOGGED_IN_KEY' => '', 'NONCE_KEY' => '', 'AUTH_SALT' => '', 'SECURE_AUTH_SALT' => '', 'LOGGED_IN_SALT' => '', 'NONCE_SALT' => '' );
	foreach ( $keys_salts as $c => $v ) {
		if ( defined( $c ) )
			unset( $keys_salts[ $c ] );
	}
	if ( ! empty( $keys_salts ) ) {
		$from_api = wp_remote_get( 'https://api.wordpress.org/secret-key/1.1/salt/' );
		if ( is_wp_error( $from_api ) ) {
			foreach ( $keys_salts as $c => $v ) {
				$keys_salts[ $c ] = wp_generate_password( 64, true, true );
			}
		} else {
			$from_api = explode( "\n", wp_remote_retrieve_body( $from_api ) );
			foreach ( $keys_salts as $c => $v ) {
				$keys_salts[ $c ] = substr( array_shift( $from_api ), 28, 64 );
			}
		}
		$num_keys_salts = count( $keys_salts );
?>
	<p><?php
		echo _n( 'This unique authentication key is also missing from your <code>wp-config.php</code> file.', 'These unique authentication keys are also missing from your <code>wp-config.php</code> file.', $num_keys_salts ); ?> <?php _e( 'To make your installation more secure, you should also add:' ) ?></p>
	<textarea class="code" readonly="readonly" cols="100" rows="<?php echo $num_keys_salts; ?>"><?php
	foreach ( $keys_salts as $c => $v ) {
		echo "\ndefine( '$c', '$v' );";
	}
?></textarea>
<?php	
	}
?>
</li>
<?php

	// remove ending slash from $base and $url
	$htaccess = '';
	$base = rtrim( $base, '/' );

	$htaccess_file = 'RewriteEngine On
RewriteBase ' . $base . '/

#uploaded files
RewriteRule ^(.*/)?files/$ index.php [L]
RewriteCond %{REQUEST_URI} !.*wp-content/plugins.*
RewriteRule ^(.*/)?files/(.*) wp-includes/ms-files.php?file=$2 [L]

# add a trailing slash to /wp-admin
RewriteCond %{REQUEST_URI} ^.*/wp-admin$
RewriteRule ^(.+)$ $1/ [R=301,L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule . - [L]
RewriteRule  ^([_0-9a-zA-Z-]+/)?(wp-(content|admin|includes).*) $2 [L]
RewriteRule  ^([_0-9a-zA-Z-]+/)?(.*\.php)$ $2 [L]
RewriteRule . index.php [L]';
?>
			<li><p><?php printf( __( 'Add the following to your <code>.htaccess</code> file in <code>%s</code>, replacing other WordPress rules:' ), ABSPATH ); ?></p>
				<textarea class="code" readonly="readonly" cols="100" rows="18">
<?php echo wp_htmledit_pre( $htaccess_file ); ?>
</textarea></li>
		</ol>
<?php if ( !is_multisite() ) { ?>
		<p><?php printf( __( 'Once you complete these steps, your network is enabled and configured.') ); ?> <a href="<?php echo esc_url( admin_url() ); ?>"><?php _e( 'Return to Dashboard' ); ?></a></p>
<?php
	}
}

$base = trailingslashit( stripslashes( dirname( dirname( $_SERVER['SCRIPT_NAME'] ) ) ) );

if ( $_POST ) {
	check_admin_referer( 'install-network-1' );

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	// create network tables
	install_network();
	$hostname = get_clean_basedomain();
	$subdomain_install = 'localhost' == $hostname ? false : (bool) $_POST['subdomain_install'];
	if ( ! network_domain_check() )
		populate_network( 1, get_clean_basedomain(), sanitize_email( $_POST['email'] ), $_POST['weblog_title'], $base, $subdomain_install );
	// create wp-config.php / htaccess
	network_step2();
} elseif ( is_multisite() || network_domain_check() ) {
	network_step2();
} else {
	network_step1();
}
?>
</form>
</div>

<?php include( './admin-footer.php' ); ?>
