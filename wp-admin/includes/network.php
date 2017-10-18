<?php
/**
 * WordPress Network Administration API.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.4.0
 */

/**
 * Check for an existing network.
 *
 * @since 3.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return Whether a network exists.
 */
function network_domain_check() {
	global $wpdb;

	$sql = $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->esc_like( $wpdb->site ) );
	if ( $wpdb->get_var( $sql ) ) {
		return $wpdb->get_var( "SELECT domain FROM $wpdb->site ORDER BY id ASC LIMIT 1" );
	}
	return false;
}

/**
 * Allow subdomain installation
 *
 * @since 3.0.0
 * @return bool Whether subdomain installation is allowed
 */
function allow_subdomain_install() {
	$domain = preg_replace( '|https?://([^/]+)|', '$1', get_option( 'home' ) );
	if ( parse_url( get_option( 'home' ), PHP_URL_PATH ) || 'localhost' == $domain || preg_match( '|^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$|', $domain ) )
		return false;

	return true;
}

/**
 * Allow subdirectory installation.
 *
 * @since 3.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return bool Whether subdirectory installation is allowed
 */
function allow_subdirectory_install() {
	global $wpdb;
        /**
         * Filters whether to enable the subdirectory installation feature in Multisite.
         *
         * @since 3.0.0
         *
         * @param bool $allow Whether to enable the subdirectory installation feature in Multisite. Default is false.
         */
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

/**
 * Prints step 1 for Network installation process.
 *
 * @todo Realistically, step 1 should be a welcome screen explaining what a Network is and such. Navigating to Tools > Network
 * 	should not be a sudden "Welcome to a new install process! Fill this out and click here." See also contextual help todo.
 *
 * @since 3.0.0
 *
 * @global bool $is_apache
 *
 * @param WP_Error $errors
 */
function network_step1( $errors = false ) {
	global $is_apache;

	if ( defined('DO_NOT_UPGRADE_GLOBAL_TABLES') ) {
		echo '<div class="error"><p><strong>' . __( 'ERROR:' ) . '</strong> ' . __( 'The constant DO_NOT_UPGRADE_GLOBAL_TABLES cannot be defined when creating a network.' ) . '</p></div>';
		echo '</div>';
		include( ABSPATH . 'wp-admin/admin-footer.php' );
		die();
	}

	$active_plugins = get_option( 'active_plugins' );
	if ( ! empty( $active_plugins ) ) {
		echo '<div class="updated"><p><strong>' . __( 'Warning:' ) . '</strong> ' . sprintf( __( 'Please <a href="%s">deactivate your plugins</a> before enabling the Network feature.' ), admin_url( 'plugins.php?plugin_status=active' ) ) . '</p></div><p>' . __( 'Once the network is created, you may reactivate your plugins.' ) . '</p>';
		echo '</div>';
		include( ABSPATH . 'wp-admin/admin-footer.php' );
		die();
	}

	$hostname = get_clean_basedomain();
	$has_ports = strstr( $hostname, ':' );
	if ( ( false !== $has_ports && ! in_array( $has_ports, array( ':80', ':443' ) ) ) ) {
		echo '<div class="error"><p><strong>' . __( 'ERROR:' ) . '</strong> ' . __( 'You cannot install a network of sites with your server address.' ) . '</p></div>';
		echo '<p>' . sprintf(
			/* translators: %s: port number */
			__( 'You cannot use port numbers such as %s.' ),
			'<code>' . $has_ports . '</code>'
		) . '</p>';
		echo '<a href="' . esc_url( admin_url() ) . '">' . __( 'Return to Dashboard' ) . '</a>';
		echo '</div>';
		include( ABSPATH . 'wp-admin/admin-footer.php' );
		die();
	}

	echo '<form method="post">';

	wp_nonce_field( 'install-network-1' );

	$error_codes = array();
	if ( is_wp_error( $errors ) ) {
		echo '<div class="error"><p><strong>' . __( 'ERROR: The network could not be created.' ) . '</strong></p>';
		foreach ( $errors->get_error_messages() as $error )
			echo "<p>$error</p>";
		echo '</div>';
		$error_codes = $errors->get_error_codes();
	}

	if ( ! empty( $_POST['sitename'] ) && ! in_array( 'empty_sitename', $error_codes ) ) {
		$site_name = $_POST['sitename'];
	} else {
		/* translators: %s: Default network name */
		$site_name = sprintf( __( '%s Sites' ), get_option( 'blogname' ) );
	}

	if ( ! empty( $_POST['email'] ) && ! in_array( 'invalid_email', $error_codes ) ) {
		$admin_email = $_POST['email'];
	} else {
		$admin_email = get_option( 'admin_email' );
	}
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
		if ( $got_mod_rewrite = got_mod_rewrite() ) { // dangerous assumptions
			echo '<div class="updated inline"><p><strong>' . __( 'Note:' ) . '</strong> ';
			/* translators: %s: mod_rewrite */
			printf( __( 'Please make sure the Apache %s module is installed as it will be used at the end of this installation.' ),
				'<code>mod_rewrite</code>'
			);
			echo '</p>';
		} elseif ( $is_apache ) {
			echo '<div class="error inline"><p><strong>' . __( 'Warning:' ) . '</strong> ';
			/* translators: %s: mod_rewrite */
			printf( __( 'It looks like the Apache %s module is not installed.' ),
				'<code>mod_rewrite</code>'
			);
			echo '</p>';
		}

		if ( $got_mod_rewrite || $is_apache ) { // Protect against mod_rewrite mimicry (but ! Apache)
			echo '<p>';
			/* translators: 1: mod_rewrite, 2: mod_rewrite documentation URL, 3: Google search for mod_rewrite */
			printf( __( 'If %1$s is disabled, ask your administrator to enable that module, or look at the <a href="%2$s">Apache documentation</a> or <a href="%3$s">elsewhere</a> for help setting it up.' ),
				'<code>mod_rewrite</code>',
				'https://httpd.apache.org/docs/mod/mod_rewrite.html',
				'https://www.google.com/search?q=apache+mod_rewrite'
			);
			echo '</p></div>';
		}
	}

	if ( allow_subdomain_install() && allow_subdirectory_install() ) : ?>
		<h3><?php esc_html_e( 'Addresses of Sites in your Network' ); ?></h3>
		<p><?php _e( 'Please choose whether you would like sites in your WordPress network to use sub-domains or sub-directories.' ); ?>
			<strong><?php _e( 'You cannot change this later.' ); ?></strong></p>
		<p><?php _e( 'You will need a wildcard DNS record if you are going to use the virtual host (sub-domain) functionality.' ); ?></p>
		<?php // @todo: Link to an MS readme? ?>
		<table class="form-table">
			<tr>
				<th><label><input type="radio" name="subdomain_install" value="1"<?php checked( $subdomain_install ); ?> /> <?php _e( 'Sub-domains' ); ?></label></th>
				<td><?php printf(
					/* translators: 1: hostname */
					_x( 'like <code>site1.%1$s</code> and <code>site2.%1$s</code>', 'subdomain examples' ),
					$hostname
				); ?></td>
			</tr>
			<tr>
				<th><label><input type="radio" name="subdomain_install" value="0"<?php checked( ! $subdomain_install ); ?> /> <?php _e( 'Sub-directories' ); ?></label></th>
				<td><?php printf(
					/* translators: 1: hostname */
					_x( 'like <code>%1$s/site1</code> and <code>%1$s/site2</code>', 'subdirectory examples' ),
					$hostname
				); ?></td>
			</tr>
		</table>

<?php
	endif;

		if ( WP_CONTENT_DIR != ABSPATH . 'wp-content' && ( allow_subdirectory_install() || ! allow_subdomain_install() ) )
			echo '<div class="error inline"><p><strong>' . __( 'Warning:' ) . '</strong> ' . __( 'Subdirectory networks may not be fully compatible with custom wp-content directories.' ) . '</p></div>';

		$is_www = ( 0 === strpos( $hostname, 'www.' ) );
		if ( $is_www ) :
		?>
		<h3><?php esc_html_e( 'Server Address' ); ?></h3>
		<p><?php printf(
			/* translators: 1: site url 2: host name 3. www */
			__( 'We recommend you change your siteurl to %1$s before enabling the network feature. It will still be possible to visit your site using the %3$s prefix with an address like %2$s but any links will not have the %3$s prefix.' ),
			'<code>' . substr( $hostname, 4 ) . '</code>',
			'<code>' . $hostname . '</code>',
			'<code>www</code>'
		); ?></p>
		<table class="form-table">
			<tr>
				<th scope='row'><?php esc_html_e( 'Server Address' ); ?></th>
				<td>
					<?php printf(
						/* translators: %s: host name */
						__( 'The internet address of your network will be %s.' ),
						'<code>' . $hostname . '</code>'
					); ?>
				</td>
			</tr>
		</table>
		<?php endif; ?>

		<h3><?php esc_html_e( 'Network Details' ); ?></h3>
		<table class="form-table">
		<?php if ( 'localhost' == $hostname ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Sub-directory Installation' ); ?></th>
				<td><?php
					printf(
						/* translators: 1: localhost 2: localhost.localdomain */
						__( 'Because you are using %1$s, the sites in your WordPress network must use sub-directories. Consider using %2$s if you wish to use sub-domains.' ),
						'<code>localhost</code>',
						'<code>localhost.localdomain</code>'
					);
					// Uh oh:
					if ( !allow_subdirectory_install() )
						echo ' <strong>' . __( 'Warning:' ) . ' ' . __( 'The main site in a sub-directory installation will need to use a modified permalink structure, potentially breaking existing links.' ) . '</strong>';
				?></td>
			</tr>
		<?php elseif ( !allow_subdomain_install() ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Sub-directory Installation' ); ?></th>
				<td><?php
					_e( 'Because your installation is in a directory, the sites in your WordPress network must use sub-directories.' );
					// Uh oh:
					if ( !allow_subdirectory_install() )
						echo ' <strong>' . __( 'Warning:' ) . ' ' . __( 'The main site in a sub-directory installation will need to use a modified permalink structure, potentially breaking existing links.' ) . '</strong>';
				?></td>
			</tr>
		<?php elseif ( !allow_subdirectory_install() ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Sub-domain Installation' ); ?></th>
				<td><?php _e( 'Because your installation is not new, the sites in your WordPress network must use sub-domains.' );
					echo ' <strong>' . __( 'The main site in a sub-directory installation will need to use a modified permalink structure, potentially breaking existing links.' ) . '</strong>';
				?></td>
			</tr>
		<?php endif; ?>
		<?php if ( ! $is_www ) : ?>
			<tr>
				<th scope='row'><?php esc_html_e( 'Server Address' ); ?></th>
				<td>
					<?php printf(
						/* translators: %s: host name */
						__( 'The internet address of your network will be %s.' ),
						'<code>' . $hostname . '</code>'
					); ?>
				</td>
			</tr>
		<?php endif; ?>
			<tr>
				<th scope='row'><?php esc_html_e( 'Network Title' ); ?></th>
				<td>
					<input name='sitename' type='text' size='45' value='<?php echo esc_attr( $site_name ); ?>' />
					<p class="description">
						<?php _e( 'What would you like to call your network?' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope='row'><?php esc_html_e( 'Network Admin Email' ); ?></th>
				<td>
					<input name='email' type='text' size='45' value='<?php echo esc_attr( $admin_email ); ?>' />
					<p class="description">
						<?php _e( 'Your email address.' ); ?>
					</p>
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
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param WP_Error $errors
 */
function network_step2( $errors = false ) {
	global $wpdb;

	$hostname          = get_clean_basedomain();
	$slashed_home      = trailingslashit( get_option( 'home' ) );
	$base              = parse_url( $slashed_home, PHP_URL_PATH );
	$document_root_fix = str_replace( '\\', '/', realpath( $_SERVER['DOCUMENT_ROOT'] ) );
	$abspath_fix       = str_replace( '\\', '/', ABSPATH );
	$home_path         = 0 === strpos( $abspath_fix, $document_root_fix ) ? $document_root_fix . $base : get_home_path();
	$wp_siteurl_subdir = preg_replace( '#^' . preg_quote( $home_path, '#' ) . '#', '', $abspath_fix );
	$rewrite_base      = ! empty( $wp_siteurl_subdir ) ? ltrim( trailingslashit( $wp_siteurl_subdir ), '/' ) : '';


	$location_of_wp_config = $abspath_fix;
	if ( ! file_exists( ABSPATH . 'wp-config.php' ) && file_exists( dirname( ABSPATH ) . '/wp-config.php' ) ) {
		$location_of_wp_config = dirname( $abspath_fix );
	}
	$location_of_wp_config = trailingslashit( $location_of_wp_config );

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
	<div class="error"><p><strong><?php _e( 'Warning:' ); ?></strong> <?php _e( 'An existing WordPress network was detected.' ); ?></p></div>
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
			if ( file_exists( $home_path . '.htaccess' ) ) {
				echo '<strong>' . __( 'Caution:' ) . '</strong> ';
				printf(
					/* translators: 1: wp-config.php 2: .htaccess */
					__( 'We recommend you back up your existing %1$s and %2$s files.' ),
					'<code>wp-config.php</code>',
					'<code>.htaccess</code>'
				);
			} elseif ( file_exists( $home_path . 'web.config' ) ) {
				echo '<strong>' . __( 'Caution:' ) . '</strong> ';
				printf(
					/* translators: 1: wp-config.php 2: web.config */
					__( 'We recommend you back up your existing %1$s and %2$s files.' ),
					'<code>wp-config.php</code>',
					'<code>web.config</code>'
				);
			} else {
				echo '<strong>' . __( 'Caution:' ) . '</strong> ';
				printf(
					/* translators: 1: wp-config.php */
					__( 'We recommend you back up your existing %s file.' ),
					'<code>wp-config.php</code>'
				);
			}
		?></p></div>
<?php
	}
?>
		<ol>
			<li><p><?php printf(
				/* translators: 1: wp-config.php 2: location of wp-config file, 3: translated version of "That's all, stop editing! Happy blogging." */
				__( 'Add the following to your %1$s file in %2$s <strong>above</strong> the line reading %3$s:' ),
				'<code>wp-config.php</code>',
				'<code>' . $location_of_wp_config . '</code>',
				/*
				 * translators: This string should only be translated if wp-config-sample.php is localized.
				 * You can check the localized release package or
				 * https://i18n.svn.wordpress.org/<locale code>/branches/<wp version>/dist/wp-config-sample.php
				 */
				'<code>/* ' . __( 'That&#8217;s all, stop editing! Happy blogging.' ) . ' */</code>'
			); ?></p>
				<textarea class="code" readonly="readonly" cols="100" rows="7">
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
	<p>
		<?php
			if ( 1 == $num_keys_salts ) {
				printf(
					/* translators: 1: wp-config.php */
					__( 'This unique authentication key is also missing from your %s file.' ),
					'<code>wp-config.php</code>'
				);
			} else {
				printf(
					/* translators: 1: wp-config.php */
					__( 'These unique authentication keys are also missing from your %s file.' ),
					'<code>wp-config.php</code>'
				);
			}
		?>
		<?php _e( 'To make your installation more secure, you should also add:' ); ?>
	</p>
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

		$web_config_file = '<?xml version="1.0" encoding="UTF-8"?>
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
                    <match url="^' . $iis_subdir_match . 'files/(.+)" ignoreCase="false" />
                    <action type="Rewrite" url="' . $iis_rewrite_base . WPINC . '/ms-files.php?file={R:1}" appendQueryString="false" />
                </rule>';
                }
                $web_config_file .= '
                <rule name="WordPress Rule 2" stopProcessing="true">
                    <match url="^' . $iis_subdir_match . 'wp-admin$" ignoreCase="false" />
                    <action type="Redirect" url="' . $iis_subdir_replacement . 'wp-admin/" redirectType="Permanent" />
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
                    <match url="^' . $iis_subdir_match . '(wp-(content|admin|includes).*)" ignoreCase="false" />
                    <action type="Rewrite" url="' . $iis_rewrite_base . '{R:1}" />
                </rule>
                <rule name="WordPress Rule 5" stopProcessing="true">
                    <match url="^' . $iis_subdir_match . '([_0-9a-zA-Z-]+/)?(.*\.php)$" ignoreCase="false" />
                    <action type="Rewrite" url="' . $iis_rewrite_base . '{R:2}" />
                </rule>
                <rule name="WordPress Rule 6" stopProcessing="true">
                    <match url="." ignoreCase="false" />
                    <action type="Rewrite" url="index.php" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
';

		echo '<li><p>';
		printf(
			/* translators: 1: a filename like .htaccess. 2: a file path. */
			__( 'Add the following to your %1$s file in %2$s, <strong>replacing</strong> other WordPress rules:' ),
			'<code>web.config</code>',
			'<code>' . $home_path . '</code>'
		);
		echo '</p>';
		if ( ! $subdomain_install && WP_CONTENT_DIR != ABSPATH . 'wp-content' )
			echo '<p><strong>' . __( 'Warning:' ) . ' ' . __( 'Subdirectory networks may not be fully compatible with custom wp-content directories.' ) . '</strong></p>';
		?>
		<textarea class="code" readonly="readonly" cols="100" rows="20"><?php echo esc_textarea( $web_config_file ); ?>
		</textarea></li>
		</ol>

	<?php else : // end iis7_supports_permalinks(). construct an htaccess file instead:

		$ms_files_rewriting = '';
		if ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) {
			$ms_files_rewriting = "\n# uploaded files\nRewriteRule ^";
			$ms_files_rewriting .= $subdir_match . "files/(.+) {$rewrite_base}" . WPINC . "/ms-files.php?file={$subdir_replacement_12} [L]" . "\n";
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

		echo '<li><p>';
		printf(
			/* translators: 1: a filename like .htaccess. 2: a file path. */
			__( 'Add the following to your %1$s file in %2$s, <strong>replacing</strong> other WordPress rules:' ),
			'<code>.htaccess</code>',
			'<code>' . $home_path . '</code>'
		);
		echo '</p>';
		if ( ! $subdomain_install && WP_CONTENT_DIR != ABSPATH . 'wp-content' )
			echo '<p><strong>' . __( 'Warning:' ) . ' ' . __( 'Subdirectory networks may not be fully compatible with custom wp-content directories.' ) . '</strong></p>';
		?>
		<textarea class="code" readonly="readonly" cols="100" rows="<?php echo substr_count( $htaccess_file, "\n" ) + 1; ?>">
<?php echo esc_textarea( $htaccess_file ); ?></textarea></li>
		</ol>

	<?php endif; // end IIS/Apache code branches.

	if ( !is_multisite() ) { ?>
		<p><?php _e( 'Once you complete these steps, your network is enabled and configured. You will have to log in again.' ); ?> <a href="<?php echo esc_url( wp_login_url() ); ?>"><?php _e( 'Log In' ); ?></a></p>
<?php
	}
}
