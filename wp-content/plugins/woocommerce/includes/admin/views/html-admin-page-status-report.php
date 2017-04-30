<div class="updated woocommerce-message">
	<p><?php _e( 'Please include this information when requesting support:', 'woocommerce' ); ?> </p>
	<p class="submit"><a href="#" class="button-primary debug-report"><?php _e( 'Get System Report', 'woocommerce' ); ?></a></p>
	<div id="debug-report"><textarea readonly="readonly"></textarea></div>
</div>
<br/>
<table class="wc_status_table widefat" cellspacing="0">

	<thead>
		<tr>
			<th colspan="2"><?php _e( 'Environment', 'woocommerce' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td><?php _e( 'Home URL','woocommerce' ); ?>:</td>
			<td><?php echo home_url(); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Site URL','woocommerce' ); ?>:</td>
			<td><?php echo site_url(); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WC Version','woocommerce' ); ?>:</td>
			<td><?php echo esc_html( WC()->version ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WC Database Version','woocommerce' ); ?>:</td>
			<td><?php echo esc_html( get_option( 'woocommerce_db_version' ) ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Version','woocommerce' ); ?>:</td>
			<td><?php bloginfo('version'); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Multisite Enabled','woocommerce' ); ?>:</td>
			<td><?php if ( is_multisite() ) echo __( 'Yes', 'woocommerce' ); else echo __( 'No', 'woocommerce' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Web Server Info','woocommerce' ); ?>:</td>
			<td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'PHP Version','woocommerce' ); ?>:</td>
			<td><?php if ( function_exists( 'phpversion' ) ) echo esc_html( phpversion() ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'MySQL Version','woocommerce' ); ?>:</td>
			<td>
				<?php
				/** @global wpdb $wpdb */
				global $wpdb;
				echo $wpdb->db_version();
				?>
			</td>
		</tr>
		<tr>
			<td><?php _e( 'WP Memory Limit','woocommerce' ); ?>:</td>
			<td><?php
				$memory = wc_let_to_num( WP_MEMORY_LIMIT );

				if ( $memory < 67108864 ) {
					echo '<mark class="error">' . sprintf( __( '%s - We recommend setting memory to at least 64MB. See: <a href="%s">Increasing memory allocated to PHP</a>', 'woocommerce' ), size_format( $memory ), 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP' ) . '</mark>';
				} else {
					echo '<mark class="yes">' . size_format( $memory ) . '</mark>';
				}
			?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Debug Mode', 'woocommerce' ); ?>:</td>
			<td><?php if ( defined('WP_DEBUG') && WP_DEBUG ) echo '<mark class="yes">' . __( 'Yes', 'woocommerce' ) . '</mark>'; else echo '<mark class="no">' . __( 'No', 'woocommerce' ) . '</mark>'; ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Language', 'woocommerce' ); ?>:</td>
			<td><?php if ( defined( 'WPLANG' ) && WPLANG ) echo WPLANG; else  _e( 'Default', 'woocommerce' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Max Upload Size','woocommerce' ); ?>:</td>
			<td><?php echo size_format( wp_max_upload_size() ); ?></td>
		</tr>
		<?php if ( function_exists( 'ini_get' ) ) : ?>
			<tr>
				<td><?php _e('PHP Post Max Size','woocommerce' ); ?>:</td>
				<td><?php echo size_format( wc_let_to_num( ini_get('post_max_size') ) ); ?></td>
			</tr>
			<tr>
				<td><?php _e('PHP Time Limit','woocommerce' ); ?>:</td>
				<td><?php echo ini_get('max_execution_time'); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'PHP Max Input Vars','woocommerce' ); ?>:</td>
				<td><?php echo ini_get('max_input_vars'); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'SUHOSIN Installed','woocommerce' ); ?>:</td>
				<td><?php echo extension_loaded( 'suhosin' ) ? __( 'Yes', 'woocommerce' ) : __( 'No', 'woocommerce' ); ?></td>
			</tr>
		<?php endif; ?>
		<tr>
			<td><?php _e( 'WC Logging','woocommerce' ); ?>:</td>
			<td><?php
				if ( @fopen( WC()->plugin_path() . '/logs/paypal.txt', 'a' ) )
					echo '<mark class="yes">' . __( 'Log directory is writable.', 'woocommerce' ) . '</mark>';
				else
					echo '<mark class="error">' . __( 'Log directory (<code>woocommerce/logs/</code>) is not writable. Logging will not be possible.', 'woocommerce' ) . '</mark>';
			?></td>
		</tr>
		<tr>
			<td><?php _e( 'Default Timezone','woocommerce' ); ?>:</td>
			<td><?php
				$default_timezone = date_default_timezone_get();
				if ( 'UTC' !== $default_timezone ) {
					echo '<mark class="error">' . sprintf( __( 'Default timezone is %s - it should be UTC', 'woocommerce' ), $default_timezone ) . '</mark>';
				} else {
					echo '<mark class="yes">' . sprintf( __( 'Default timezone is %s', 'woocommerce' ), $default_timezone ) . '</mark>';
				} ?>
			</td>
		</tr>
		<?php
			$posting = array();

			// fsockopen/cURL
			$posting['fsockopen_curl']['name'] = __( 'fsockopen/cURL','woocommerce');
			if ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ) {
				if ( function_exists( 'fsockopen' ) && function_exists( 'curl_init' )) {
					$posting['fsockopen_curl']['note'] = __('Your server has fsockopen and cURL enabled.', 'woocommerce' );
				} elseif ( function_exists( 'fsockopen' )) {
					$posting['fsockopen_curl']['note'] = __( 'Your server has fsockopen enabled, cURL is disabled.', 'woocommerce' );
				} else {
					$posting['fsockopen_curl']['note'] = __( 'Your server has cURL enabled, fsockopen is disabled.', 'woocommerce' );
				}
				$posting['fsockopen_curl']['success'] = true;
			} else {
				$posting['fsockopen_curl']['note'] = __( 'Your server does not have fsockopen or cURL enabled - PayPal IPN and other scripts which communicate with other servers will not work. Contact your hosting provider.', 'woocommerce' ). '</mark>';
				$posting['fsockopen_curl']['success'] = false;
			}

			// SOAP
			$posting['soap_client']['name'] = __( 'SOAP Client','woocommerce' );
			if ( class_exists( 'SoapClient' ) ) {
				$posting['soap_client']['note'] = __('Your server has the SOAP Client class enabled.', 'woocommerce' );
				$posting['soap_client']['success'] = true;
			} else {
				$posting['soap_client']['note'] = sprintf( __( 'Your server does not have the <a href="%s">SOAP Client</a> class enabled - some gateway plugins which use SOAP may not work as expected.', 'woocommerce' ), 'http://php.net/manual/en/class.soapclient.php' ) . '</mark>';
				$posting['soap_client']['success'] = false;
			}

			// WP Remote Post Check
			$posting['wp_remote_post']['name'] = __( 'WP Remote Post','woocommerce');
			$request['cmd'] = '_notify-validate';
			$params = array(
				'sslverify' 	=> false,
				'timeout' 		=> 60,
				'user-agent'	=> 'WooCommerce/' . WC()->version,
				'body'			=> $request
			);
			$response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', $params );

			if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
				$posting['wp_remote_post']['note'] = __('wp_remote_post() was successful - PayPal IPN is working.', 'woocommerce' );
				$posting['wp_remote_post']['success'] = true;
			} elseif ( is_wp_error( $response ) ) {
				$posting['wp_remote_post']['note'] = __( 'wp_remote_post() failed. PayPal IPN won\'t work with your server. Contact your hosting provider. Error:', 'woocommerce' ) . ' ' . $response->get_error_message();
				$posting['wp_remote_post']['success'] = false;
			} else {
				$posting['wp_remote_post']['note'] = __( 'wp_remote_post() failed. PayPal IPN may not work with your server.', 'woocommerce' );
				$posting['wp_remote_post']['success'] = false;
			}

			$posting = apply_filters( 'woocommerce_debug_posting', $posting );

			foreach( $posting as $post ) { $mark = ( isset( $post['success'] ) && $post['success'] == true ) ? 'yes' : 'error';
				?>
				<tr>
					<td><?php echo esc_html( $post['name'] ); ?>:</td>
					<td>
						<mark class="<?php echo $mark; ?>">
							<?php echo wp_kses_data( $post['note'] ); ?>
						</mark>
					</td>
				</tr>
				<?php
			}
		?>
	</tbody>

	<thead>
		<tr>
			<th colspan="2"><?php _e( 'Locale', 'woocommerce' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
			$locale = localeconv();

			foreach ( $locale as $key => $val )
				if ( in_array( $key, array( 'decimal_point', 'mon_decimal_point', 'thousands_sep', 'mon_thousands_sep' ) ) )
					echo '<tr><td>' . $key . ':</td><td>' . $val . '</td></tr>';
		?>
	</tbody>

	<thead>
		<tr>
			<th colspan="2"><?php _e( 'Plugins', 'woocommerce' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td><?php _e( 'Installed Plugins','woocommerce' ); ?>:</td>
			<td><?php
				$active_plugins = (array) get_option( 'active_plugins', array() );

				if ( is_multisite() )
					$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );

				$wc_plugins = array();

				foreach ( $active_plugins as $plugin ) {

					$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
					$dirname        = dirname( $plugin );
					$version_string = '';

					if ( ! empty( $plugin_data['Name'] ) ) {

						// link the plugin name to the plugin url if available
						$plugin_name = $plugin_data['Name'];
						if ( ! empty( $plugin_data['PluginURI'] ) ) {
							$plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . __( 'Visit plugin homepage' , 'woocommerce' ) . '">' . $plugin_name . '</a>';
						}

						if ( strstr( $dirname, 'woocommerce' ) ) {

							if ( false === ( $version_data = get_transient( md5( $plugin ) . '_version_data' ) ) ) {
								$changelog = wp_remote_get( 'http://dzv365zjfbd8v.cloudfront.net/changelogs/' . $dirname . '/changelog.txt' );
								$cl_lines  = explode( "\n", wp_remote_retrieve_body( $changelog ) );
								if ( ! empty( $cl_lines ) ) {
									foreach ( $cl_lines as $line_num => $cl_line ) {
										if ( preg_match( '/^[0-9]/', $cl_line ) ) {

											$date         = str_replace( '.' , '-' , trim( substr( $cl_line , 0 , strpos( $cl_line , '-' ) ) ) );
											$version      = preg_replace( '~[^0-9,.]~' , '' ,stristr( $cl_line , "version" ) );
											$update       = trim( str_replace( "*" , "" , $cl_lines[ $line_num + 1 ] ) );
											$version_data = array( 'date' => $date , 'version' => $version , 'update' => $update , 'changelog' => $changelog );
											set_transient( md5( $plugin ) . '_version_data', $version_data, 60*60*12 );
											break;
										}
									}
								}
							}

							if ( ! empty( $version_data['version'] ) && version_compare( $version_data['version'], $plugin_data['Version'], '>' ) )
								$version_string = ' &ndash; <strong style="color:red;">' . $version_data['version'] . ' ' . __( 'is available', 'woocommerce' ) . '</strong>';
						}

						$wc_plugins[] = $plugin_name . ' ' . __( 'by', 'woocommerce' ) . ' ' . $plugin_data['Author'] . ' ' . __( 'version', 'woocommerce' ) . ' ' . $plugin_data['Version'] . $version_string;

					}
				}

				if ( sizeof( $wc_plugins ) == 0 )
					echo '-';
				else
					echo implode( ', <br/>', $wc_plugins );

			?></td>
		</tr>
	</tbody>

	<thead>
		<tr>
			<th colspan="2"><?php _e( 'Settings', 'woocommerce' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td><?php _e( 'Force SSL','woocommerce' ); ?>:</td>
			<td><?php echo get_option( 'woocommerce_force_ssl_checkout' ) === 'yes' ? '<mark class="yes">'.__( 'Yes', 'woocommerce' ).'</mark>' : '<mark class="no">'.__( 'No', 'woocommerce' ).'</mark>'; ?></td>
		</tr>
	</tbody>

	<thead>
		<tr>
			<th colspan="2"><?php _e( 'WC Pages', 'woocommerce' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
			$check_pages = array(
				_x( 'Shop Base', 'Page setting', 'woocommerce' ) => array(
						'option' => 'woocommerce_shop_page_id',
						'shortcode' => ''
					),
				_x( 'Cart', 'Page setting', 'woocommerce' ) => array(
						'option' => 'woocommerce_cart_page_id',
						'shortcode' => '[' . apply_filters( 'woocommerce_cart_shortcode_tag', 'woocommerce_cart' ) . ']'
					),
				_x( 'Checkout', 'Page setting', 'woocommerce' ) => array(
						'option' => 'woocommerce_checkout_page_id',
						'shortcode' => '[' . apply_filters( 'woocommerce_checkout_shortcode_tag', 'woocommerce_checkout' ) . ']'
					),
				_x( 'My Account', 'Page setting', 'woocommerce' ) => array(
						'option' => 'woocommerce_myaccount_page_id',
						'shortcode' => '[' . apply_filters( 'woocommerce_my_account_shortcode_tag', 'woocommerce_my_account' ) . ']'
					)
			);

			$alt = 1;

			foreach ( $check_pages as $page_name => $values ) {

				if ( $alt == 1 ) echo '<tr>'; else echo '<tr>';

				echo '<td>' . esc_html( $page_name ) . ':</td><td>';

				$error = false;

				$page_id = get_option( $values['option'] );

				// Page ID check
				if ( ! $page_id ) {
					echo '<mark class="error">' . __( 'Page not set', 'woocommerce' ) . '</mark>';
					$error = true;
				} else {

					// Shortcode check
					if ( $values['shortcode'] ) {
						$page = get_post( $page_id );

						if ( empty( $page ) ) {

							echo '<mark class="error">' . sprintf( __( 'Page does not exist', 'woocommerce' ) ) . '</mark>';
							$error = true;

						} else if ( ! strstr( $page->post_content, $values['shortcode'] ) ) {

							echo '<mark class="error">' . sprintf( __( 'Page does not contain the shortcode: %s', 'woocommerce' ), $values['shortcode'] ) . '</mark>';
							$error = true;

						}
					}

				}

				if ( ! $error ) echo '<mark class="yes">#' . absint( $page_id ) . ' - ' . str_replace( home_url(), '', get_permalink( $page_id ) ) . '</mark>';

				echo '</td></tr>';

				$alt = $alt * -1;
			}
		?>
	</tbody>

	<thead>
		<tr>
			<th colspan="2"><?php _e( 'WC Taxonomies', 'woocommerce' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td><?php _e( 'Order Statuses', 'woocommerce' ); ?>:</td>
			<td><?php
				$display_terms = array();
				$terms = get_terms( 'shop_order_status', array( 'hide_empty' => 0 ) );
				foreach ( $terms as $term )
					$display_terms[] = $term->name . ' (' . $term->slug . ')';
				echo implode( ', ', array_map( 'esc_html', $display_terms ) );
			?></td>
		</tr>
		<tr>
			<td><?php _e( 'Product Types', 'woocommerce' ); ?>:</td>
			<td><?php
				$display_terms = array();
				$terms = get_terms( 'product_type', array( 'hide_empty' => 0 ) );
				foreach ( $terms as $term )
					$display_terms[] = $term->name . ' (' . $term->slug . ')';
				echo implode( ', ', array_map( 'esc_html', $display_terms ) );
			?></td>
		</tr>
	</tbody>

	<thead>
		<tr>
			<th colspan="2"><?php _e( 'Theme', 'woocommerce' ); ?></th>
		</tr>
	</thead>

        <?php
        $active_theme = wp_get_theme();
        if ( $active_theme->{'Author URI'} == 'http://www.woothemes.com' ) :

			$theme_dir = substr( strtolower( str_replace( ' ','', $active_theme->Name ) ), 0, 45 );

			if ( false === ( $theme_version_data = get_transient( $theme_dir . '_version_data' ) ) ) :

        		$theme_changelog = wp_remote_get( 'http://dzv365zjfbd8v.cloudfront.net/changelogs/' . $theme_dir . '/changelog.txt' );
				$cl_lines  = explode( "\n", wp_remote_retrieve_body( $theme_changelog ) );
				if ( ! empty( $cl_lines ) ) :

					foreach ( $cl_lines as $line_num => $cl_line ) {
						if ( preg_match( '/^[0-9]/', $cl_line ) ) :

							$theme_date    		= str_replace( '.' , '-' , trim( substr( $cl_line , 0 , strpos( $cl_line , '-' ) ) ) );
							$theme_version      = preg_replace( '~[^0-9,.]~' , '' ,stristr( $cl_line , "version" ) );
							$theme_update       = trim( str_replace( "*" , "" , $cl_lines[ $line_num + 1 ] ) );
							$theme_version_data = array( 'date' => $theme_date , 'version' => $theme_version , 'update' => $theme_update , 'changelog' => $theme_changelog );
							set_transient( $theme_dir . '_version_data', $theme_version_data , 60*60*12 );
							break;

						endif;
					}

				endif;

			endif;

		endif;
		?>
	<tbody>
            <tr>
                <td><?php _e( 'Theme Name', 'woocommerce' ); ?>:</td>
                <td><?php
					echo $active_theme->Name;
                ?></td>
            </tr>
            <tr>
                <td><?php _e( 'Theme Version', 'woocommerce' ); ?>:</td>
                <td><?php
					echo $active_theme->Version;

					if ( ! empty( $theme_version_data['version'] ) && version_compare( $theme_version_data['version'], $active_theme->Version, '!=' ) )
						echo ' &ndash; <strong style="color:red;">' . $theme_version_data['version'] . ' ' . __( 'is available', 'woocommerce' ) . '</strong>';
                ?></td>
            </tr>
            <tr>
                <td><?php _e( 'Author URL', 'woocommerce' ); ?>:</td>
                <td><?php
					echo $active_theme->{'Author URI'};
                ?></td>
            </tr>
	</tbody>

	<thead>
		<tr>
			<th colspan="2"><?php _e( 'Templates', 'woocommerce' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<?php

				$template_paths = apply_filters( 'woocommerce_template_overrides_scan_paths', array( 'WooCommerce' => WC()->plugin_path() . '/templates/' ) );
				$scanned_files  = array();
				$found_files    = array();

				foreach ( $template_paths as $plugin_name => $template_path ) {
					$scanned_files[ $plugin_name ] = $this->scan_template_files( $template_path );
				}

				foreach ( $scanned_files as $plugin_name => $files ) {
					foreach ( $files as $file ) {
						if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
							$theme_file = get_stylesheet_directory() . '/' . $file;
						} elseif ( file_exists( get_stylesheet_directory() . '/woocommerce/' . $file ) ) {
							$theme_file = get_stylesheet_directory() . '/woocommerce/' . $file;
						} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
							$theme_file = get_template_directory() . '/' . $file;
						} elseif( file_exists( get_template_directory() . '/woocommerce/' . $file ) ) {
							$theme_file = get_template_directory() . '/woocommerce/' . $file;
						} else {
							$theme_file = false;
						}

						if ( $theme_file ) {
							$core_version  = $this->get_file_version( WC()->plugin_path() . '/templates/' . $file );
							$theme_version = $this->get_file_version( $theme_file );

							if ( $core_version && ( empty( $theme_version ) || version_compare( $theme_version, $core_version, '<' ) ) ) {
								$found_files[ $plugin_name ][] = sprintf( __( '<code>%s</code> version <strong style="color:red">%s</strong> is out of date. The core version is %s', 'woocommerce' ), basename( $theme_file ), $theme_version ? $theme_version : '-', $core_version );
							} else {
								$found_files[ $plugin_name ][] = sprintf( '<code>%s</code>', basename( $theme_file ) );
							}
						}
					}
				}

				if ( $found_files ) {
					foreach ( $found_files as $plugin_name => $found_plugin_files ) {
						?>
						<td><?php _e( 'Template Overrides', 'woocommerce' ); ?> (<?php echo $plugin_name; ?>):</td>
						<td><?php echo implode( ', <br/>', $found_plugin_files ); ?></td>
						<?php
					}
				} else {
					?>
					<td><?php _e( 'Template Overrides', 'woocommerce' ); ?>:</td>
					<td><?php _e( 'No overrides present in theme.', 'woocommerce' ); ?></td>
					<?php
				}
			?>
		</tr>
	</tbody>

</table>

<script type="text/javascript">
	/*
	@var i string default
	@var l how many repeat s
	@var s string to repeat
	@var w where s should indent
	*/
	jQuery.wc_strPad = function(i,l,s,w) {
		var o = i.toString();
		if (!s) { s = '0'; }
		while (o.length < l) {
			// empty
			if(w == 'undefined'){
				o = s + o;
			}else{
				o = o + s;
			}
		}
		return o;
	};


	jQuery('a.debug-report').click(function(){

		var report = "";

		jQuery('.wc_status_table thead, .wc_status_table tbody').each(function(){

			if ( jQuery( this ).is('thead') ) {

				report = report + "\n### " + jQuery.trim( jQuery( this ).text() ) + " ###\n\n";

			} else {

				jQuery('tr', jQuery( this )).each(function(){

					var the_name    = jQuery.wc_strPad( jQuery.trim( jQuery( this ).find('td:eq(0)').text() ), 25, ' ' );
					var the_value   = jQuery.trim( jQuery( this ).find('td:eq(1)').text() );
					var value_array = the_value.split( ', ' );

					if ( value_array.length > 1 ){

						// if value have a list of plugins ','
						// split to add new line
						var output = '';
						var temp_line ='';
						jQuery.each( value_array, function(key, line){
							var tab = ( key == 0 )?0:25;
							temp_line = temp_line + jQuery.wc_strPad( '', tab, ' ', 'f' ) + line +'\n';
						});

						the_value = temp_line;
					}

					report = report +''+ the_name + the_value + "\n";
				});

			}
		} );

		try {
			jQuery("#debug-report").slideDown();
			jQuery("#debug-report textarea").val( report ).focus().select();
			jQuery(this).fadeOut();
			return false;
		} catch(e){ console.log( e ); }

		return false;
	});
</script>
