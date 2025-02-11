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
 * @return string|false Base domain if network exists, otherwise false.
 */
function network_domain_check() {
	global $wpdb;

	$sql = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $wpdb->site ) );
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
	$home   = get_option( 'home' );
	$domain = parse_url( $home, PHP_URL_HOST );
	if ( parse_url( $home, PHP_URL_PATH ) || 'localhost' === $domain || preg_match( '|^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$|', $domain ) ) {
		return false;
	}

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
	 * @param bool $allow Whether to enable the subdirectory installation feature in Multisite.
	 *                    Default false.
	 */
	if ( apply_filters( 'allow_subdirectory_install', false ) ) {
		return true;
	}

	if ( defined( 'ALLOW_SUBDIRECTORY_INSTALL' ) && ALLOW_SUBDIRECTORY_INSTALL ) {
		return true;
	}

	$post = $wpdb->get_row( "SELECT ID FROM $wpdb->posts WHERE post_date < DATE_SUB(NOW(), INTERVAL 1 MONTH) AND post_status = 'publish'" );
	if ( empty( $post ) ) {
		return true;
	}

	return false;
}

/**
 * Get base domain of network.
 *
 * @since 3.0.0
 * @return string Base domain.
 */
function get_clean_basedomain() {
	$existing_domain = network_domain_check();
	if ( $existing_domain ) {
		return $existing_domain;
	}
	$domain = preg_replace( '|https?://|', '', get_option( 'siteurl' ) );
	$slash  = strpos( $domain, '/' );
	if ( $slash ) {
		$domain = substr( $domain, 0, $slash );
	}
	return $domain;
}

/**
 * Prints step 1 for Network installation process.
 *
 * @todo Realistically, step 1 should be a welcome screen explaining what a Network is and such.
 *       Navigating to Tools > Network should not be a sudden "Welcome to a new install process!
 *       Fill this out and click here." See also contextual help todo.
 *
 * @since 3.0.0
 *
 * @global bool $is_apache
 *
 * @param false|WP_Error $errors Optional. Error object. Default false.
 */
function network_step1( $errors = false ) {
	global $is_apache;

	if ( defined( 'DO_NOT_UPGRADE_GLOBAL_TABLES' ) ) {
		$cannot_define_constant_message  = '<strong>' . __( 'Error:' ) . '</strong> ';
		$cannot_define_constant_message .= sprintf(
			/* translators: %s: DO_NOT_UPGRADE_GLOBAL_TABLES */
			__( 'The constant %s cannot be defined when creating a network.' ),
			'<code>DO_NOT_UPGRADE_GLOBAL_TABLES</code>'
		);

		wp_admin_notice(
			$cannot_define_constant_message,
			array(
				'additional_classes' => array( 'error' ),
			)
		);

		echo '</div>';
		require_once ABSPATH . 'wp-admin/admin-footer.php';
		die();
	}

	$active_plugins = get_option( 'active_plugins' );
	if ( ! empty( $active_plugins ) ) {
		wp_admin_notice(
			'<strong>' . __( 'Warning:' ) . '</strong> ' . sprintf(
				/* translators: %s: URL to Plugins screen. */
				__( 'Please <a href="%s">deactivate your plugins</a> before enabling the Network feature.' ),
				admin_url( 'plugins.php?plugin_status=active' )
			),
			array( 'type' => 'warning' )
		);
		echo '<p>' . __( 'Once the network is created, you may reactivate your plugins.' ) . '</p>';
		echo '</div>';
		require_once ABSPATH . 'wp-admin/admin-footer.php';
		die();
	}

	// Strip standard port from hostname.
	$hostname = preg_replace( '/(?::80|:443)$/', '', get_clean_basedomain() );

	echo '<form method="post">';

	wp_nonce_field( 'install-network-1' );

	$error_codes = array();
	if ( is_wp_error( $errors ) ) {
		$network_created_error_message = '<p><strong>' . __( 'Error: The network could not be created.' ) . '</strong></p>';
		foreach ( $errors->get_error_messages() as $error ) {
			$network_created_error_message .= "<p>$error</p>";
		}
		wp_admin_notice(
			$network_created_error_message,
			array(
				'additional_classes' => array( 'error' ),
				'paragraph_wrap'     => false,
			)
		);
		$error_codes = $errors->get_error_codes();
	}

	if ( ! empty( $_POST['sitename'] ) && ! in_array( 'empty_sitename', $error_codes, true ) ) {
		$site_name = $_POST['sitename'];
	} else {
		/* translators: %s: Default network title. */
		$site_name = sprintf( __( '%s Sites' ), get_option( 'blogname' ) );
	}

	if ( ! empty( $_POST['email'] ) && ! in_array( 'invalid_email', $error_codes, true ) ) {
		$admin_email = $_POST['email'];
	} else {
		$admin_email = get_option( 'admin_email' );
	}
	?>
	<p><?php _e( 'Welcome to the Network installation process!' ); ?></p>
	<p><?php _e( 'Fill in the information below and you&#8217;ll be on your way to creating a network of WordPress sites. Configuration files will be created in the next step.' ); ?></p>
	<?php

	if ( isset( $_POST['subdomain_install'] ) ) {
		$subdomain_install = (bool) $_POST['subdomain_install'];
	} elseif ( apache_mod_loaded( 'mod_rewrite' ) ) { // Assume nothing.
		$subdomain_install = true;
	} elseif ( ! allow_subdirectory_install() ) {
		$subdomain_install = true;
	} else {
		$subdomain_install = false;
		$got_mod_rewrite   = got_mod_rewrite();
		if ( $got_mod_rewrite ) { // Dangerous assumptions.
			$message_class = 'updated';
			$message       = '<p><strong>' . __( 'Warning:' ) . '</strong> ';
			$message      .= '<p>' . sprintf(
				/* translators: %s: mod_rewrite */
				__( 'Please make sure the Apache %s module is installed as it will be used at the end of this installation.' ),
				'<code>mod_rewrite</code>'
			) . '</p>';
		} elseif ( $is_apache ) {
			$message_class = 'error';
			$message       = '<p><strong>' . __( 'Warning:' ) . '</strong> ';
			$message      .= sprintf(
				/* translators: %s: mod_rewrite */
				__( 'It looks like the Apache %s module is not installed.' ),
				'<code>mod_rewrite</code>'
			) . '</p>';
		}

		if ( $got_mod_rewrite || $is_apache ) { // Protect against mod_rewrite mimicry (but ! Apache).
			$message .= '<p>' . sprintf(
				/* translators: 1: mod_rewrite, 2: mod_rewrite documentation URL, 3: Google search for mod_rewrite. */
				__( 'If %1$s is disabled, ask your administrator to enable that module, or look at the <a href="%2$s">Apache documentation</a> or <a href="%3$s">elsewhere</a> for help setting it up.' ),
				'<code>mod_rewrite</code>',
				'https://httpd.apache.org/docs/mod/mod_rewrite.html',
				'https://www.google.com/search?q=apache+mod_rewrite'
			) . '</p>';

			wp_admin_notice(
				$message,
				array(
					'additional_classes' => array( $message_class, 'inline' ),
					'paragraph_wrap'     => false,
				)
			);
		}
	}

	if ( allow_subdomain_install() && allow_subdirectory_install() ) :
		?>
		<h3><?php esc_html_e( 'Addresses of Sites in your Network' ); ?></h3>
		<p><?php _e( 'Please choose whether you would like sites in your WordPress network to use sub-domains or sub-directories.' ); ?>
			<strong><?php _e( 'You cannot change this later.' ); ?></strong></p>
		<p><?php _e( 'You will need a wildcard DNS record if you are going to use the virtual host (sub-domain) functionality.' ); ?></p>
		<?php // @todo Link to an MS readme? ?>
		<table class="form-table" role="presentation">
			<tr>
				<th><label><input type="radio" name="subdomain_install" value="1"<?php checked( $subdomain_install ); ?> /> <?php _e( 'Sub-domains' ); ?></label></th>
				<td>
				<?php
				printf(
					/* translators: 1: Host name. */
					_x( 'like <code>site1.%1$s</code> and <code>site2.%1$s</code>', 'subdomain examples' ),
					$hostname
				);
				?>
				</td>
			</tr>
			<tr>
				<th><label><input type="radio" name="subdomain_install" value="0"<?php checked( ! $subdomain_install ); ?> /> <?php _e( 'Sub-directories' ); ?></label></th>
				<td>
				<?php
				printf(
					/* translators: 1: Host name. */
					_x( 'like <code>%1$s/site1</code> and <code>%1$s/site2</code>', 'subdirectory examples' ),
					$hostname
				);
				?>
				</td>
			</tr>
		</table>

		<?php
	endif;

	if ( WP_CONTENT_DIR !== ABSPATH . 'wp-content' && ( allow_subdirectory_install() || ! allow_subdomain_install() ) ) {
		$subdirectory_warning_message  = '<strong>' . __( 'Warning:' ) . '</strong> ';
		$subdirectory_warning_message .= __( 'Subdirectory networks may not be fully compatible with custom wp-content directories.' );
		wp_admin_notice(
			$subdirectory_warning_message,
			array(
				'additional_classes' => array( 'error', 'inline' ),
			)
		);
	}

	$is_www = str_starts_with( $hostname, 'www.' );
	if ( $is_www ) :
		?>
		<h3><?php esc_html_e( 'Server Address' ); ?></h3>
		<p>
		<?php
		printf(
			/* translators: 1: Site URL, 2: Host name, 3: www. */
			__( 'You should consider changing your site domain to %1$s before enabling the network feature. It will still be possible to visit your site using the %3$s prefix with an address like %2$s but any links will not have the %3$s prefix.' ),
			'<code>' . substr( $hostname, 4 ) . '</code>',
			'<code>' . $hostname . '</code>',
			'<code>www</code>'
		);
		?>
		</p>
		<table class="form-table" role="presentation">
			<tr>
			<th scope='row'><?php esc_html_e( 'Server Address' ); ?></th>
			<td>
				<?php
					printf(
						/* translators: %s: Host name. */
						__( 'The internet address of your network will be %s.' ),
						'<code>' . $hostname . '</code>'
					);
				?>
				</td>
			</tr>
		</table>
		<?php endif; ?>

		<h3><?php esc_html_e( 'Network Details' ); ?></h3>
		<table class="form-table" role="presentation">
		<?php if ( 'localhost' === $hostname ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Sub-directory Installation' ); ?></th>
				<td>
				<?php
					printf(
						/* translators: 1: localhost, 2: localhost.localdomain */
						__( 'Because you are using %1$s, the sites in your WordPress network must use sub-directories. Consider using %2$s if you wish to use sub-domains.' ),
						'<code>localhost</code>',
						'<code>localhost.localdomain</code>'
					);
					// Uh oh:
				if ( ! allow_subdirectory_install() ) {
					echo ' <strong>' . __( 'Warning:' ) . ' ' . __( 'The main site in a sub-directory installation will need to use a modified permalink structure, potentially breaking existing links.' ) . '</strong>';
				}
				?>
				</td>
			</tr>
		<?php elseif ( ! allow_subdomain_install() ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Sub-directory Installation' ); ?></th>
				<td>
				<?php
					_e( 'Because your installation is in a directory, the sites in your WordPress network must use sub-directories.' );
					// Uh oh:
				if ( ! allow_subdirectory_install() ) {
					echo ' <strong>' . __( 'Warning:' ) . ' ' . __( 'The main site in a sub-directory installation will need to use a modified permalink structure, potentially breaking existing links.' ) . '</strong>';
				}
				?>
				</td>
			</tr>
		<?php elseif ( ! allow_subdirectory_install() ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Sub-domain Installation' ); ?></th>
				<td>
				<?php
				_e( 'Because your installation is not new, the sites in your WordPress network must use sub-domains.' );
					echo ' <strong>' . __( 'The main site in a sub-directory installation will need to use a modified permalink structure, potentially breaking existing links.' ) . '</strong>';
				?>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ( ! $is_www ) : ?>
			<tr>
				<th scope='row'><?php esc_html_e( 'Server Address' ); ?></th>
				<td>
					<?php
					printf(
						/* translators: %s: Host name. */
						__( 'The internet address of your network will be %s.' ),
						'<code>' . $hostname . '</code>'
					);
					?>
				</td>
			</tr>
		<?php endif; ?>
			<tr>
				<th scope='row'><label for="sitename"><?php esc_html_e( 'Network Title' ); ?></label></th>
				<td>
					<input name='sitename' id='sitename' type='text' size='45' value='<?php echo esc_attr( $site_name ); ?>' />
					<p class="description">
						<?php _e( 'What would you like to call your network?' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope='row'><label for="email"><?php esc_html_e( 'Network Admin Email' ); ?></label></th>
				<td>
					<input name='email' id='email' type='text' size='45' value='<?php echo esc_attr( $admin_email ); ?>' />
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
 * @global wpdb $wpdb     WordPress database abstraction object.
 * @global bool $is_nginx Whether the server software is Nginx or something else.
 *
 * @param false|WP_Error $errors Optional. Error object. Default false.
 */
function network_step2( $errors = false ) {
	global $wpdb, $is_nginx;

	$hostname          = get_clean_basedomain();
	$slashed_home      = trailingslashit( get_option( 'home' ) );
	$base              = parse_url( $slashed_home, PHP_URL_PATH );
	$document_root_fix = str_replace( '\\', '/', realpath( $_SERVER['DOCUMENT_ROOT'] ) );
	$abspath_fix       = str_replace( '\\', '/', ABSPATH );
	$home_path         = str_starts_with( $abspath_fix, $document_root_fix ) ? $document_root_fix . $base : get_home_path();
	$wp_siteurl_subdir = preg_replace( '#^' . preg_quote( $home_path, '#' ) . '#', '', $abspath_fix );
	$rewrite_base      = ! empty( $wp_siteurl_subdir ) ? ltrim( trailingslashit( $wp_siteurl_subdir ), '/' ) : '';

	$location_of_wp_config = $abspath_fix;
	if ( ! file_exists( ABSPATH . 'wp-config.php' ) && file_exists( dirname( ABSPATH ) . '/wp-config.php' ) ) {
		$location_of_wp_config = dirname( $abspath_fix );
	}
	$location_of_wp_config = trailingslashit( $location_of_wp_config );

	// Wildcard DNS message.
	if ( is_wp_error( $errors ) ) {
		wp_admin_notice(
			$errors->get_error_message(),
			array(
				'additional_classes' => array( 'error' ),
			)
		);
	}

	if ( $_POST ) {
		if ( allow_subdomain_install() ) {
			$subdomain_install = allow_subdirectory_install() ? ! empty( $_POST['subdomain_install'] ) : true;
		} else {
			$subdomain_install = false;
		}
	} else {
		if ( is_multisite() ) {
			$subdomain_install = is_subdomain_install();
			?>
	<p><?php _e( 'The original configuration steps are shown here for reference.' ); ?></p>
			<?php
		} else {
			$subdomain_install = (bool) $wpdb->get_var( "SELECT meta_value FROM $wpdb->sitemeta WHERE site_id = 1 AND meta_key = 'subdomain_install'" );

			wp_admin_notice(
				'<strong>' . __( 'Warning:' ) . '</strong> ' . __( 'An existing WordPress network was detected.' ),
				array(
					'additional_classes' => array( 'error' ),
				)
			);
			?>
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
		<?php
		$notice_message = '<strong>' . __( 'Caution:' ) . '</strong> ';
		$notice_args    = array(
			'type'               => 'warning',
			'additional_classes' => array( 'inline' ),
		);

		if ( file_exists( $home_path . '.htaccess' ) ) {
			$notice_message .= sprintf(
				/* translators: 1: wp-config.php, 2: .htaccess */
				__( 'You should back up your existing %1$s and %2$s files.' ),
				'<code>wp-config.php</code>',
				'<code>.htaccess</code>'
			);
		} elseif ( file_exists( $home_path . 'web.config' ) ) {
			$notice_message .= sprintf(
				/* translators: 1: wp-config.php, 2: web.config */
				__( 'You should back up your existing %1$s and %2$s files.' ),
				'<code>wp-config.php</code>',
				'<code>web.config</code>'
			);
		} else {
			$notice_message .= sprintf(
				/* translators: %s: wp-config.php */
				__( 'You should back up your existing %s file.' ),
				'<code>wp-config.php</code>'
			);
		}

		wp_admin_notice( $notice_message, $notice_args );
	}
	?>
	<ol>
		<li><p id="network-wpconfig-rules-description">
		<?php
		printf(
			/* translators: 1: wp-config.php, 2: Location of wp-config file, 3: Translated version of "That's all, stop editing! Happy publishing." */
			__( 'Add the following to your %1$s file in %2$s <strong>above</strong> the line reading %3$s:' ),
			'<code>wp-config.php</code>',
			'<code>' . $location_of_wp_config . '</code>',
			/*
			 * translators: This string should only be translated if wp-config-sample.php is localized.
			 * You can check the localized release package or
			 * https://i18n.svn.wordpress.org/<locale code>/branches/<wp version>/dist/wp-config-sample.php
			 */
			'<code>/* ' . __( 'That&#8217;s all, stop editing! Happy publishing.' ) . ' */</code>'
		);
		?>
		</p>
		<p class="configuration-rules-label"><label for="network-wpconfig-rules">
			<?php
			printf(
				/* translators: %s: File name (wp-config.php, .htaccess or web.config). */
				__( 'Network configuration rules for %s' ),
				'<code>wp-config.php</code>'
			);
			?>
		</label></p>
		<textarea id="network-wpconfig-rules" class="code" readonly="readonly" cols="100" rows="7" aria-describedby="network-wpconfig-rules-description">
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', <?php echo $subdomain_install ? 'true' : 'false'; ?> );
define( 'DOMAIN_CURRENT_SITE', '<?php echo $hostname; ?>' );
define( 'PATH_CURRENT_SITE', '<?php echo $base; ?>' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );
</textarea>
		<?php
		$keys_salts = array(
			'AUTH_KEY'         => '',
			'SECURE_AUTH_KEY'  => '',
			'LOGGED_IN_KEY'    => '',
			'NONCE_KEY'        => '',
			'AUTH_SALT'        => '',
			'SECURE_AUTH_SALT' => '',
			'LOGGED_IN_SALT'   => '',
			'NONCE_SALT'       => '',
		);
		foreach ( $keys_salts as $c => $v ) {
			if ( defined( $c ) ) {
				unset( $keys_salts[ $c ] );
			}
		}

		if ( ! empty( $keys_salts ) ) {
			$keys_salts_str = '';
			$from_api       = wp_remote_get( wp_get_update_api_base() . '/secret-key/1.1/salt/' );
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
		<p id="network-wpconfig-authentication-description">
			<?php
			if ( 1 === $num_keys_salts ) {
				printf(
					/* translators: %s: wp-config.php */
					__( 'This unique authentication key is also missing from your %s file.' ),
					'<code>wp-config.php</code>'
				);
			} else {
				printf(
					/* translators: %s: wp-config.php */
					__( 'These unique authentication keys are also missing from your %s file.' ),
					'<code>wp-config.php</code>'
				);
			}
			?>
			<?php _e( 'To make your installation more secure, you should also add:' ); ?>
		</p>
		<p class="configuration-rules-label"><label for="network-wpconfig-authentication"><?php _e( 'Network configuration authentication keys' ); ?></label></p>
		<textarea id="network-wpconfig-authentication" class="code" readonly="readonly" cols="100" rows="<?php echo $num_keys_salts; ?>" aria-describedby="network-wpconfig-authentication-description"><?php echo esc_textarea( $keys_salts_str ); ?></textarea>
			<?php
		}
		?>
		</li>
	<?php
	if ( iis7_supports_permalinks() ) :
		// IIS doesn't support RewriteBase, all your RewriteBase are belong to us.
		$iis_subdir_match       = ltrim( $base, '/' ) . $subdir_match;
		$iis_rewrite_base       = ltrim( $base, '/' ) . $rewrite_base;
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

			echo '<li><p id="network-webconfig-rules-description">';
			printf(
				/* translators: 1: File name (.htaccess or web.config), 2: File path. */
				__( 'Add the following to your %1$s file in %2$s, <strong>replacing</strong> other WordPress rules:' ),
				'<code>web.config</code>',
				'<code>' . $home_path . '</code>'
			);
		echo '</p>';
		if ( ! $subdomain_install && WP_CONTENT_DIR !== ABSPATH . 'wp-content' ) {
			echo '<p><strong>' . __( 'Warning:' ) . ' ' . __( 'Subdirectory networks may not be fully compatible with custom wp-content directories.' ) . '</strong></p>';
		}
		?>
			<p class="configuration-rules-label"><label for="network-webconfig-rules">
				<?php
				printf(
					/* translators: %s: File name (wp-config.php, .htaccess or web.config). */
					__( 'Network configuration rules for %s' ),
					'<code>web.config</code>'
				);
				?>
			</label></p>
			<textarea id="network-webconfig-rules" class="code" readonly="readonly" cols="100" rows="20" aria-describedby="network-webconfig-rules-description"><?php echo esc_textarea( $web_config_file ); ?></textarea>
		</li>
	</ol>

		<?php
	elseif ( $is_nginx ) : // End iis7_supports_permalinks(). Link to Nginx documentation instead:

		echo '<li><p>';
		printf(
			/* translators: %s: Documentation URL. */
			__( 'It seems your network is running with Nginx web server. <a href="%s">Learn more about further configuration</a>.' ),
			__( 'https://developer.wordpress.org/advanced-administration/server/web-server/nginx/' )
		);
		echo '</p></li>';

	else : // End $is_nginx. Construct an .htaccess file instead:

		$ms_files_rewriting = '';
		if ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) {
			$ms_files_rewriting  = "\n# uploaded files\nRewriteRule ^";
			$ms_files_rewriting .= $subdir_match . "files/(.+) {$rewrite_base}" . WPINC . "/ms-files.php?file={$subdir_replacement_12} [L]" . "\n";
		}

		$htaccess_file = <<<EOF
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
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

		echo '<li><p id="network-htaccess-rules-description">';
		printf(
			/* translators: 1: File name (.htaccess or web.config), 2: File path. */
			__( 'Add the following to your %1$s file in %2$s, <strong>replacing</strong> other WordPress rules:' ),
			'<code>.htaccess</code>',
			'<code>' . $home_path . '</code>'
		);
		echo '</p>';
		if ( ! $subdomain_install && WP_CONTENT_DIR !== ABSPATH . 'wp-content' ) {
			echo '<p><strong>' . __( 'Warning:' ) . ' ' . __( 'Subdirectory networks may not be fully compatible with custom wp-content directories.' ) . '</strong></p>';
		}
		?>
			<p class="configuration-rules-label"><label for="network-htaccess-rules">
				<?php
				printf(
					/* translators: %s: File name (wp-config.php, .htaccess or web.config). */
					__( 'Network configuration rules for %s' ),
					'<code>.htaccess</code>'
				);
				?>
			</label></p>
			<textarea id="network-htaccess-rules" class="code" readonly="readonly" cols="100" rows="<?php echo substr_count( $htaccess_file, "\n" ) + 1; ?>" aria-describedby="network-htaccess-rules-description"><?php echo esc_textarea( $htaccess_file ); ?></textarea>
		</li>
	</ol>

		<?php
	endif; // End IIS/Nginx/Apache code branches.

	if ( ! is_multisite() ) {
		?>
		<p><?php _e( 'Once you complete these steps, your network is enabled and configured. You will have to log in again.' ); ?> <a href="<?php echo esc_url( wp_login_url() ); ?>"><?php _e( 'Log In' ); ?></a></p>
		<?php
	}
}
