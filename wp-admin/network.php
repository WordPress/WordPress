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

define( 'WP_INSTALLING_NETWORK', true );

/** WordPress Administration Bootstrap */
require_once( './admin.php' );

if ( ! is_super_admin() )
	wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );

if ( is_multisite() ) {
	if ( ! is_network_admin() ) {
		wp_redirect( network_admin_url( 'setup.php' ) );
		exit;
	}
	if ( ! defined( 'MULTISITE' ) )
		wp_die( __( 'The Network creation panel is not for WordPress MU networks.' ) );
}

// We need to create references to ms global tables to enable Network.
foreach ( $wpdb->tables( 'ms_global' ) as $table => $prefixed_table )
	$wpdb->$table = $prefixed_table;

/**
 * Check for an existing network.
 *
 * @since 3.0.0
 * @return Whether a network exists.
 */
function network_domain_check() {
	global $wpdb;
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->site'" ) )
		return $wpdb->get_var( "SELECT domain FROM $wpdb->site ORDER BY id ASC LIMIT 1" );
	return false;
}

/**
 * Allow subdomain install
 *
 * @since 3.0.0
 * @return bool Whether subdomain install is allowed
 */
function allow_subdomain_install() {
	$domain = preg_replace( '|https?://([^/]+)|', '$1', get_option( 'home' ) );
	if( parse_url( get_option( 'home' ), PHP_URL_PATH ) || 'localhost' == $domain || preg_match( '|^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$|', $domain ) )
		return false;

	return true;
}
/**
 * Allow subdirectory install
 *
 * @since 3.0.0
 * @return bool Whether subdirectory install is allowed
 */
function allow_subdirectory_install() {
	global $wpdb;
	if ( apply_filters( 'allow_subdirectory_install', false ) )
		return true;

	if ( defined( 'ALLOW_SUBDIRECTORY_INSTALL' ) && ALLOW_SUBDIRECTORY_INSTALL )
		return true;

	$post = $wpdb->get_row( "SELECT ID FROM $wpdb->posts WHERE post_date < DATE_SUB(NOW(), INTERVAL 1 MONTH) AND post_status = 'publish'" );
	if ( empty( $post ) )
		return true;

	return false;
}
/**
 * Get base domain of network.
 *
 * @since 3.0.0
 * @return string Base domain.
 */
function get_clean_basedomain() {
	if ( $existing_domain = network_domain_check() )
		return $existing_domain;
	$domain = preg_replace( '|https?://|', '', get_option( 'siteurl' ) );
	if ( $slash = strpos( $domain, '/' ) )
		$domain = substr( $domain, 0, $slash );
	return $domain;
}

if ( ! network_domain_check() && ( ! defined( 'WP_ALLOW_MULTISITE' ) || ! WP_ALLOW_MULTISITE ) )
	wp_die( __( 'You must define the <code>WP_ALLOW_MULTISITE</code> constant as true in your wp-config.php file to allow creation of a Network.' ) );

if ( is_network_admin() ) {
	$title = __( 'Network Setup' );
	$parent_file = 'settings.php';
} else {
	$title = __( 'Create a Network of WordPress Sites' );
	$parent_file = 'tools.php';
}

$network_help = '<p>' . __('This screen allows you to configure a network as having subdomains (<code>site1.example.com</code>) or subdirectories (<code>example.com/site1</code>). Subdomains require wildcard subdomains to be enabled in Apache and DNS records, if your host allows it.') . '</p>' .
	'<p>' . __('Choose subdomains or subdirectories; this can only be switched afterwards by reconfiguring your install. Fill out the network details, and click install. If this does not work, you may have to add a wildcard DNS record (for subdomains) or change to another setting in Permalinks (for subdirectories).') . '</p>' .
	'<p>' . __('The next screen for Network Setup will give you individually-generated lines of code to add to your wp-config.php and .htaccess files. Make sure the settings of your FTP client make files starting with a dot visible, so that you can find .htaccess; you may have to create this file if it really is not there. Make backup copies of those two files.') . '</p>' .
	'<p>' . __('Add the designated lines of code to wp-config.php (just before <code>/*...stop editing...*/</code>) and <code>.htaccess</code> (replacing the existing WordPress rules).') . '</p>' .
	'<p>' . __('Once you add this code and refresh your browser, multisite should be enabled. This screen, now in the Network Admin navigation menu, will keep an archive of the added code. You can toggle between Network Admin and Site Admin by clicking on the Network Admin or an individual site name under the My Sites dropdown in the Toolbar.') . '</p>' .
	'<p>' . __('The choice of subdirectory sites is disabled if this setup is more than a month old because of permalink problems with &#8220;/blog/&#8221; from the main site. This disabling will be addressed in a future version.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Create_A_Network" target="_blank">Documentation on Creating a Network</a>') . '</p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Tools_Network_Screen" target="_blank">Documentation on the Network Screen</a>') . '</p>';

get_current_screen()->add_help_tab( array(
	'id'      => 'network',
	'title'   => __('Network'),
	'content' => $network_help,
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Create_A_Network" target="_blank">Documentation on Creating a Network</a>') . '</p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Tools_Network_Screen" target="_blank">Documentation on the Network Screen</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
<div class="wrap">
<?php screen_icon('tools'); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<?php
/**
 * Prints step 1 for Network installation process.
 *
 * @todo Realistically, step 1 should be a welcome screen explaining what a Network is and such. Navigating to Tools > Network
 * 	should not be a sudden "Welcome to a new install process! Fill this out and click here." See also contextual help todo.
 *
 * @since 3.0.0
 */
function network_step1( $errors = false ) {
	global $is_apache;

	if ( defined('DO_NOT_UPGRADE_GLOBAL_TABLES') ) {
		echo '<div class="error"><p><strong>' . __('ERROR:') . '</strong> ' . __( 'The constant DO_NOT_UPGRADE_GLOBAL_TABLES cannot be defined when creating a network.' ) . '</p></div>';
		echo '</div>';
		include ( ABSPATH . 'wp-admin/admin-footer.php' );
		die();
	}

	$active_plugins = get_option( 'active_plugins' );
	if ( ! empty( $active_plugins ) ) {
		echo '<div class="updated"><p><strong>' . __('Warning:') . '</strong> ' . sprintf( __( 'Please <a href="%s">deactivate your plugins</a> before enabling the Network feature.' ), admin_url( 'plugins.php?plugin_status=active' ) ) . '</p></div><p>' . __( 'Once the network is created, you may reactivate your plugins.' ) . '</p>';
		echo '</div>';
		include( ABSPATH . 'wp-admin/admin-footer.php' );
		die();
	}

	$hostname = get_clean_basedomain();
	$has_ports = strstr( $hostname, ':' );
	if ( ( false !== $has_ports && ! in_array( $has_ports, array( ':80', ':443' ) ) ) ) {
		echo '<div class="error"><p><strong>' . __( 'ERROR:') . '</strong> ' . __( 'You cannot install a network of sites with your server address.' ) . '</p></div>';
		echo '<p>' . sprintf( __( 'You cannot use port numbers such as <code>%s</code>.' ), $has_ports ) . '</p>';
		echo '<a href="' . esc_url( admin_url() ) . '">' . __( 'Return to Dashboard' ) . '</a>';
		echo '</div>';
		include( ABSPATH . 'wp-admin/admin-footer.php' );
		die();
	}

	echo '<form method="post" action="">';

	wp_nonce_field( 'install-network-1' );

	$error_codes = array();
	if ( is_wp_error( $errors ) ) {
		echo '<div class="error"><p><strong>' . __( 'ERROR: The network could not be created.' ) . '</strong></p>';
		foreach ( $errors->get_error_messages() as $error )
			echo "<p>$error</p>";
		echo '</div>';
		$error_codes = $errors->get_error_codes();
	}

	$site_name = ( ! empty( $_POST['sitename'] ) && ! in_array( 'empty_sitename', $error_codes ) ) ? $_POST['sitename'] : sprintf( _x('%s Sites', 'Default network name' ), get_option( 'blogname' ) );
	$admin_email = ( ! empty( $_POST['email'] ) && ! in_array( 'invalid_email', $error_codes ) ) ? $_POST['email'] : get_option( 'admin_email' );
	?>
	<p><?php _e( 'Welcome to the Network installation process!' ); ?></p>
	<p><?php _e( 'Fill in the information below and you&#8217;ll be on your way to creating a network of WordPress sites. We will create configuration files in the next step.' ); ?></p>
	<?php

	if ( isset( $_POST['subdomain_install'] ) ) {
		$subdomain_install = (bool) $_POST['subdomain_install'];
	} elseif ( apache_mod_loaded('mod_rewrite') ) { // assume nothing
		$subdomain_install = true;
	} elseif ( !allow_subdirectory_install() ) {
		$subdomain_install = true;
	} else {
		$subdomain_install = false;
		if ( $got_mod_rewrite = got_mod_rewrite() ) // dangerous assumptions
			echo '<div class="updated inline"><p><strong>' . __( 'Note:' ) . '</strong> ' . __( 'Please make sure the Apache <code>mod_rewrite</code> module is installed as it will be used at the end of this installation.' ) . '</p>';
		elseif ( $is_apache )
			echo '<div class="error inline"><p><strong>' . __( 'Warning!' ) . '</strong> ' . __( 'It looks like the Apache <code>mod_rewrite</code> module is not installed.' ) . '</p>';
		if ( $got_mod_rewrite || $is_apache ) // Protect against mod_rewrite mimicry (but ! Apache)
			echo '<p>' . __( 'If <code>mod_rewrite</code> is disabled, ask your administrator to enable that module, or look at the <a href="http://httpd.apache.org/docs/mod/mod_rewrite.html">Apache documentation</a> or <a href="http://www.google.com/search?q=apache+mod_rewrite">elsewhere</a> for help setting it up.' ) . '</p></div>';
	}

	if ( allow_subdomain_install() && allow_subdirectory_install() ) : ?>
		<h3><?php esc_html_e( 'Addresses of Sites in your Network' ); ?></h3>
		<p><?php _e( 'Please choose whether you would like sites in your WordPress network to use sub-domains or sub-directories. <strong>You cannot change this later.</strong>' ); ?></p>
		<p><?php _e( 'You will need a wildcard DNS record if you are going to use the virtual host (sub-domain) functionality.' ); ?></p>
		<?php // @todo: Link to an MS readme? ?>
		<table class="form-table">
			<tr>
				<th><label><input type='radio' name='subdomain_install' value='1'<?php checked( $subdomain_install ); ?> /> <?php _e( 'Sub-domains' ); ?></label></th>
				<td><?php printf( _x( 'like <code>site1.%1$s</code> and <code>site2.%1$s</code>', 'subdomain examples' ), $hostname ); ?></td>
			</tr>
			<tr>
				<th><label><input type='radio' name='subdomain_install' value='0'<?php checked( ! $subdomain_install ); ?> /> <?php _e( 'Sub-directories' ); ?></label></th>
				<td><?php printf( _x( 'like <code>%1$s/site1</code> and <code>%1$s/site2</code>', 'subdirectory examples' ), $hostname ); ?></td>
			</tr>
		</table>

<?php
	endif;

		if ( WP_CONTENT_DIR != ABSPATH . 'wp-content' && ( allow_subdirectory_install() || ! allow_subdomain_install() ) )
			echo '<div class="error inline"><p><strong>' . __('Warning!') . '</strong> ' . __( 'Subdirectory networks may not be fully compatible with custom wp-content directories.' ) . '</p></div>';

		$is_www = ( 0 === strpos( $hostname, 'www.' ) );
		if ( $is_www ) :
		?>
		<h3><?php esc_html_e( 'Server Address' ); ?></h3>
		<p><?php printf( __( 'We recommend you change your siteurl to <code>%1$s</code> before enabling the network feature. It will still be possible to visit your site using the <code>www</code> prefix with an address like <code>%2$s</code> but any links will not have the <code>www</code> prefix.' ), substr( $hostname, 4 ), $hostname ); ?></p>
		<table class="form-table">
			<tr>
				<th scope='row'><?php esc_html_e( 'Server Address' ); ?></th>
				<td>
					<?php printf( __( 'The internet address of your network will be <code>%s</code>.' ), $hostname ); ?>
				</td>
			</tr>
		</table>
		<?php endif; ?>

		<h3><?php esc_html_e( 'Network Details' ); ?></h3>
		<table class="form-table">
		<?php if ( 'localhost' == $hostname ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Sub-directory Install' ); ?></th>
				<td><?php
					_e( 'Because you are using <code>localhost</code>, the sites in your WordPress network must use sub-directories. Consider using <code>localhost.localdomain</code> if you wish to use sub-domains.' );
					// Uh oh:
					if ( !allow_subdirectory_install() )
						echo ' <strong>' . __( 'Warning!' ) . ' ' . __( 'The main site in a sub-directory install will need to use a modified permalink structure, potentially breaking existing links.' ) . '</strong>';
				?></td>
			</tr>
		<?php elseif ( !allow_subdomain_install() ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Sub-directory Install' ); ?></th>
				<td><?php
					_e( 'Because your install is in a directory, the sites in your WordPress network must use sub-directories.' );
					// Uh oh:
					if ( !allow_subdirectory_install() )
						echo ' <strong>' . __( 'Warning!' ) . ' ' . __( 'The main site in a sub-directory install will need to use a modified permalink structure, potentially breaking existing links.' ) . '</strong>';
				?></td>
			</tr>
		<?php elseif ( !allow_subdirectory_install() ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Sub-domain Install' ); ?></th>
				<td><?php _e( 'Because your install is not new, the sites in your WordPress network must use sub-domains.' );
					echo ' <strong>' . __( 'The main site in a sub-directory install will need to use a modified permalink structure, potentially breaking existing links.' ) . '</strong>';
				?></td>
			</tr>
		<?php endif; ?>
		<?php if ( ! $is_www ) : ?>
			<tr>
				<th scope='row'><?php esc_html_e( 'Server Address' ); ?></th>
				<td>
					<?php printf( __( 'The internet address of your network will be <code>%s</code>.' ), $hostname ); ?>
				</td>
			</tr>
		<?php endif; ?>
			<tr>
				<th scope='row'><?php esc_html_e( 'Network Title' ); ?></th>
				<td>
					<input name='sitename' type='text' size='45' value='<?php echo esc_attr( $site_name ); ?>' />
					<br /><?php _e( 'What would you like to call your network?' ); ?>
				</td>
			</tr>
			<tr>
				<th scope='row'><?php esc_html_e( 'Admin E-mail Address' ); ?></th>
				<td>
					<input name='email' type='text' size='45' value='<?php echo esc_attr( $admin_email ); ?>' />
					<br /><?php _e( 'Your email address.' ); ?>
				</td>
			</tr>
		</table>
		<?php submit_button( __( 'Install' ), 'primary', 'submit' ); ?>
	</form>
	<?php
}

/**
 * Prints step 2 for Network installation process.
 *
 * @since 3.0.0
 */
function network_step2( $errors = false ) {
	global $wpdb;

	$hostname          = get_clean_basedomain();
	$slashed_home      = trailingslashit( get_option( 'home' ) );
	$base              = parse_url( $slashed_home, PHP_URL_PATH );
	$wp_dir_from_root  = preg_replace( '#^' . preg_quote( $_SERVER['DOCUMENT_ROOT'], '#' ) . '#', '', ABSPATH );
	$wp_siteurl_subdir = trailingslashit( '/' . preg_replace( '#^' . preg_quote( $base, '#' ) . '#', '', $wp_dir_from_root ) );
	$rewrite_base      = ! empty( $wp_siteurl_subdir ) ? ltrim( trailingslashit( $wp_siteurl_subdir ), '/' ) : '';

	$home_path         = get_home_path();

	$location_of_wp_config = ABSPATH;
	if ( ! file_exists( ABSPATH . 'wp-config.php' ) && file_exists( dirname( ABSPATH ) . '/wp-config.php' ) )
		$location_of_wp_config = trailingslashit( dirname( ABSPATH ) );

	// Wildcard DNS message.
	if ( is_wp_error( $errors ) )
		echo '<div class="error">' . $errors->get_error_message() . '</div>';

	if ( $_POST ) {
		if ( allow_subdomain_install() )
			$subdomain_install = allow_subdirectory_install() ? ! empty( $_POST['subdomain_install'] ) : true;
		else
			$subdomain_install = false;
	} else {
		if ( is_multisite() ) {
			$subdomain_install = is_subdomain_install();
?>
	<p><?php _e( 'The original configuration steps are shown here for reference.' ); ?></p>
<?php
		} else {
			$subdomain_install = (bool) $wpdb->get_var( "SELECT meta_value FROM $wpdb->sitemeta WHERE site_id = 1 AND meta_key = 'subdomain_install'" );
?>
	<div class="error"><p><strong><?php _e('Warning:'); ?></strong> <?php _e( 'An existing WordPress network was detected.' ); ?></p></div>
	<p><?php _e( 'Please complete the configuration steps. To create a new network, you will need to empty or remove the network database tables.' ); ?></p>
<?php
		}
	}

	$subdir_match          = $subdomain_install ? '' : '([_0-9a-zA-Z-]+/)?';
	$subdir_replacement_01 = $subdomain_install ? '' : '$1';
	$subdir_replacement_12 = $subdomain_install ? '$1' : '$2';

	if ( $_POST || ! is_multisite() ) {
?>
		<h3><?php esc_html_e( 'Enabling the Network' ); ?></h3>
		<p><?php _e( 'Complete the following steps to enable the features for creating a network of sites.' ); ?></p>
		<div class="updated inline"><p><?php
			if ( file_exists( $home_path . '.htaccess' ) )
				printf( __( '<strong>Caution:</strong> We recommend you back up your existing <code>wp-config.php</code> and <code>%s</code> files.' ), '.htaccess' );
			elseif ( file_exists( $home_path . 'web.config' ) )
				printf( __( '<strong>Caution:</strong> We recommend you back up your existing <code>wp-config.php</code> and <code>%s</code> files.' ), 'web.config' );
			else
				_e( '<strong>Caution:</strong> We recommend you back up your existing <code>wp-config.php</code> file.' );
		?></p></div>
<?php
	}
?>
		<ol>
			<li><p><?php printf( __( 'Add the following to your <code>wp-config.php</code> file in <code>%s</code> <strong>above</strong> the line reading <code>/* That&#8217;s all, stop editing! Happy blogging. */</code>:' ), $location_of_wp_config ); ?></p>
				<textarea class="code" readonly="readonly" cols="100" rows="6">
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', <?php echo $subdomain_install ? 'true' : 'false'; ?>);
define('DOMAIN_CURRENT_SITE', '<?php echo $hostname; ?>');
define('PATH_CURRENT_SITE', '<?php echo $base; ?>');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);
</textarea>
<?php
	$keys_salts = array( 'AUTH_KEY' => '', 'SECURE_AUTH_KEY' => '', 'LOGGED_IN_KEY' => '', 'NONCE_KEY' => '', 'AUTH_SALT' => '', 'SECURE_AUTH_SALT' => '', 'LOGGED_IN_SALT' => '', 'NONCE_SALT' => '' );
	foreach ( $keys_salts as $c => $v ) {
		if ( defined( $c ) )
			unset( $keys_salts[ $c ] );
	}

	if ( ! empty( $keys_salts ) ) {
		$keys_salts_str = '';
		$from_api = wp_remote_get( 'https://api.wordpress.org/secret-key/1.1/salt/' );
		if ( is_wp_error( $from_api ) ) {
			foreach ( $keys_salts as $c => $v ) {
				$keys_salts_str .= "\ndefine( '$c', '" . wp_generate_password( 64, true, true ) . "' );";
			}
		} else {
			$from_api = explode( "\n", wp_remote_retrieve_body( $from_api ) );
			foreach ( $keys_salts as $c => $v ) {
				$keys_salts_str .= "\ndefine( '$c', '" . substr( array_shift( $from_api ), 28, 64 ) . "' );";
			}
		}
		$num_keys_salts = count( $keys_salts );
?>
	<p><?php
		echo _n( 'This unique authentication key is also missing from your <code>wp-config.php</code> file.', 'These unique authentication keys are also missing from your <code>wp-config.php</code> file.', $num_keys_salts ); ?> <?php _e( 'To make your installation more secure, you should also add:' ) ?></p>
	<textarea class="code" readonly="readonly" cols="100" rows="<?php echo $num_keys_salts; ?>"><?php echo esc_textarea( $keys_salts_str ); ?></textarea>
<?php
	}
?>
</li>
<?php
	if ( iis7_supports_permalinks() ) :
		// IIS doesn't support RewriteBase, all your RewriteBase are belong to us
		$iis_subdir_match = ltrim( $base, '/' ) . $subdir_match;
		$iis_rewrite_base = ltrim( $base, '/' ) . $rewrite_base;
		$iis_subdir_replacement = $subdomain_install ? '' : '{R:1}';

		$web_config_file = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="WordPress Rule 1" stopProcessing="true">
                    <match url="^index\.php$" ignoreCase="false" />
                    <action type="None" />
                </rule>';
				if ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) {
					$web_config_file .= '
                <rule name="WordPress Rule for Files" stopProcessing="true">
                    <match url="^{$iis_subdir_match}files/(.+)" ignoreCase="false" />
                    <action type="Rewrite" url="{$iis_rewrite_base}wp-includes/ms-files.php?file={R:1}" appendQueryString="false" />
                </rule>';
                }
                $web_config_file .= '
                <rule name="WordPress Rule 2" stopProcessing="true">
                    <match url="^{$iis_subdir_match}wp-admin$" ignoreCase="false" />
                    <action type="Redirect" url="{$iis_subdir_replacement}wp-admin/" redirectType="Permanent" />
                </rule>
                <rule name="WordPress Rule 3" stopProcessing="true">
                    <match url="^" ignoreCase="false" />
                    <conditions logicalGrouping="MatchAny">
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" />
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" />
                    </conditions>
                    <action type="None" />
                </rule>
                <rule name="WordPress Rule 4" stopProcessing="true">
                    <match url="^{$iis_subdir_match}(wp-(content|admin|includes).*)" ignoreCase="false" />
                    <action type="Rewrite" url="{$iis_rewrite_base}{R:1}" />
                </rule>
                <rule name="WordPress Rule 5" stopProcessing="true">
                    <match url="^{$iis_subdir_match}([_0-9a-zA-Z-]+/)?(.*\.php)$" ignoreCase="false" />
                    <action type="Rewrite" url="{$iis_rewrite_base}{R:2}" />
                </rule>
                <rule name="WordPress Rule 6" stopProcessing="true">
                    <match url="." ignoreCase="false" />
                    <action type="Rewrite" url="index.php" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
EOF;

	?>
		<li><p><?php printf( __( 'Add the following to your <code>web.config</code> file in <code>%s</code>, replacing other WordPress rules:' ), $home_path ); ?></p>
		<?php
		if ( ! $subdomain_install && WP_CONTENT_DIR != ABSPATH . 'wp-content' )
			echo '<p><strong>' . __('Warning:') . ' ' . __( 'Subdirectory networks may not be fully compatible with custom wp-content directories.' ) . '</strong></p>';
		?>
		<textarea class="code" readonly="readonly" cols="100" rows="20"><?php echo esc_textarea( $web_config_file ); ?>
		</textarea></li>
		</ol>

	<?php else : // end iis7_supports_permalinks(). construct an htaccess file instead:

		$ms_files_rewriting = '';
		if ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) {
			$ms_files_rewriting = "\n# uploaded files\nRewriteRule ^";
			$ms_files_rewriting .= $subdir_match . "files/(.+) {$rewrite_base}wp-includes/ms-files.php?file={$subdir_replacement_12} [L]" . "\n";
		}

		$htaccess_file = <<<EOF
RewriteEngine On
RewriteBase {$base}
RewriteRule ^index\.php$ - [L]
{$ms_files_rewriting}
# add a trailing slash to /wp-admin
RewriteRule ^{$subdir_match}wp-admin$ {$subdir_replacement_01}wp-admin/ [R=301,L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^{$subdir_match}(wp-(content|admin|includes).*) {$rewrite_base}{$subdir_replacement_12} [L]
RewriteRule ^{$subdir_match}(.*\.php)$ {$rewrite_base}$subdir_replacement_12 [L]
RewriteRule . index.php [L]
EOF;

		?>
		<li><p><?php printf( __( 'Add the following to your <code>.htaccess</code> file in <code>%s</code>, replacing other WordPress rules:' ), $home_path ); ?></p>
		<?php
		if ( ! $subdomain_install && WP_CONTENT_DIR != ABSPATH . 'wp-content' )
			echo '<p><strong>' . __('Warning:') . ' ' . __( 'Subdirectory networks may not be fully compatible with custom wp-content directories.' ) . '</strong></p>';
		?>
		<textarea class="code" readonly="readonly" cols="100" rows="<?php echo substr_count( $htaccess_file, "\n" ) + 1; ?>">
<?php echo esc_textarea( $htaccess_file ); ?></textarea></li>
		</ol>

	<?php endif; // end IIS/Apache code branches.

	if ( !is_multisite() ) { ?>
		<p><?php printf( __( 'Once you complete these steps, your network is enabled and configured. You will have to log in again.') ); ?> <a href="<?php echo esc_url( site_url( 'wp-login.php' ) ); ?>"><?php _e( 'Log In' ); ?></a></p>
<?php
	}
}

if ( $_POST ) {

	check_admin_referer( 'install-network-1' );

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	// create network tables
	install_network();
	$base              = parse_url( trailingslashit( get_option( 'home' ) ), PHP_URL_PATH );
	$subdomain_install = allow_subdomain_install() ? !empty( $_POST['subdomain_install'] ) : false;
	if ( ! network_domain_check() ) {
		$result = populate_network( 1, get_clean_basedomain(), sanitize_email( $_POST['email'] ), stripslashes( $_POST['sitename'] ), $base, $subdomain_install );
		if ( is_wp_error( $result ) ) {
			if ( 1 == count( $result->get_error_codes() ) && 'no_wildcard_dns' == $result->get_error_code() )
				network_step2( $result );
			else
				network_step1( $result );
		} else {
			network_step2();
		}
	} else {
		network_step2();
	}
} elseif ( is_multisite() || network_domain_check() ) {
	network_step2();
} else {
	network_step1();
}
?>
</div>

<?php include( ABSPATH . 'wp-admin/admin-footer.php' ); ?>
