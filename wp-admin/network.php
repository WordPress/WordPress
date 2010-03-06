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
	if ( network_domain_check() ) { ?>
		<h3><?php esc_html_e( 'Existing Network' ); ?></h3>
		<div class="updated inline"><p><strong><?php _e( 'Caution:' ); ?></strong> <?php _e( 'An existing network was detected.' ); ?></p></div>
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
				<td><?php _e('like <code>site1.example.com</code> and <code>site2.example.com</code>'); ?></td>
			</tr>
			<tr>
				<th><label><input type='radio' name='vhost' value='no'<?php checked( ! $rewrite_enabled ); ?> /> Sub-directories</label></th>
				<td><?php _e('like <code>example.com/site1</code> and <code>example.com/site2</code>'); ?></td>
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
function network_step2() {
	global $base, $wpdb;
?>
		<h3><?php esc_html_e( 'Enabling the Network' ); ?></h3>
		<p><?php _e( 'Complete the following steps to enable the features for creating a network of sites.' ); ?></p>
		<div class="updated inline"><p><?php _e( '<strong>Caution:</strong> We recommend you backup your existing <code>wp-config.php</code> and <code>.htaccess</code> files.' ); ?></p></div>
		<ol>
			<li><p><?php printf( __( 'Create a <code>blogs.dir</code> directory in <code>%s</code>. This directory is used to stored uploaded media for your additional sites and must be writeable by the web server.' ), WP_CONTENT_DIR ); ?></p></li>
			<li><p><?php printf( __( 'Add the following to your <code>wp-config.php</code> file in <code>%s</code>:' ), ABSPATH ); ?></p>
				<textarea class="code" readonly="readonly" cols="100" rows="7">
define( 'MULTISITE', true );
define( 'VHOST', '<?php echo 'yes' == stripslashes( $_POST['vhost'] ) ? 'yes' : 'no'; ?>' );
$base = '<?php echo $base; ?>';
define( 'DOMAIN_CURRENT_SITE', '<?php echo get_clean_basedomain(); ?>' );
define( 'PATH_CURRENT_SITE', '<?php echo $base; ?>' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );</textarea></li>
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
		if ( !network_domain_check() || isset( $_POST['existing_network'] ) && $_POST['existing_network'] == '0' )
			populate_network( 1, get_clean_basedomain(), sanitize_email( $_POST['email'] ), $_POST['weblog_title'], $base, $_POST['vhost'] );
		// create wp-config.php / htaccess
		network_step2();
		break;

	default:
		network_step1();
		break;
}
?>
</form>
</div>

<?php include( './admin-footer.php' ); ?>
