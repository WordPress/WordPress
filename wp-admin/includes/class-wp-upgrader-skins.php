<?php
/**
 * The User Interface "Skins" for the WordPress File Upgrader
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */

/**
 * Generic Skin for the WordPress Upgrader classes. This skin is designed to be extended for specific purposes.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class WP_Upgrader_Skin {

	public $upgrader;
	public $done_header = false;
	public $done_footer = false;
	public $result = false;
	public $options = array();

	public function __construct($args = array()) {
		$defaults = array( 'url' => '', 'nonce' => '', 'title' => '', 'context' => false );
		$this->options = wp_parse_args($args, $defaults);
	}

	/**
	 * @param WP_Upgrader $upgrader
	 */
	public function set_upgrader(&$upgrader) {
		if ( is_object($upgrader) )
			$this->upgrader =& $upgrader;
		$this->add_strings();
	}

	public function add_strings() {
	}

	public function set_result($result) {
		$this->result = $result;
	}

	public function request_filesystem_credentials( $error = false, $context = false, $allow_relaxed_file_ownership = false ) {
		$url = $this->options['url'];
		if ( ! $context ) {
			$context = $this->options['context'];
		}
		if ( !empty($this->options['nonce']) ) {
			$url = wp_nonce_url($url, $this->options['nonce']);
		}

		$extra_fields = array();

		return request_filesystem_credentials( $url, '', $error, $context, $extra_fields, $allow_relaxed_file_ownership );
	}

	public function header() {
		if ( $this->done_header ) {
			return;
		}
		$this->done_header = true;
		echo '<div class="wrap">';
		echo '<h2>' . $this->options['title'] . '</h2>';
	}
	public function footer() {
		if ( $this->done_footer ) {
			return;
		}
		$this->done_footer = true;
		echo '</div>';
	}

	public function error($errors) {
		if ( ! $this->done_header )
			$this->header();
		if ( is_string($errors) ) {
			$this->feedback($errors);
		} elseif ( is_wp_error($errors) && $errors->get_error_code() ) {
			foreach ( $errors->get_error_messages() as $message ) {
				if ( $errors->get_error_data() && is_string( $errors->get_error_data() ) )
					$this->feedback($message . ' ' . esc_html( strip_tags( $errors->get_error_data() ) ) );
				else
					$this->feedback($message);
			}
		}
	}

	public function feedback($string) {
		if ( isset( $this->upgrader->strings[$string] ) )
			$string = $this->upgrader->strings[$string];

		if ( strpos($string, '%') !== false ) {
			$args = func_get_args();
			$args = array_splice($args, 1);
			if ( $args ) {
				$args = array_map( 'strip_tags', $args );
				$args = array_map( 'esc_html', $args );
				$string = vsprintf($string, $args);
			}
		}
		if ( empty($string) )
			return;
		show_message($string);
	}
	public function before() {}
	public function after() {}

	/**
	 * Output JavaScript that calls function to decrement the update counts.
	 *
	 * @since 3.9.0
	 *
	 * @param string $type Type of update count to decrement. Likely values include 'plugin',
	 *                     'theme', 'translation', etc.
	 */
	protected function decrement_update_count( $type ) {
		if ( ! $this->result || is_wp_error( $this->result ) || 'up_to_date' === $this->result ) {
			return;
		}

		if ( defined( 'IFRAME_REQUEST' ) ) {
			echo '<script type="text/javascript">
					if ( window.postMessage && JSON ) {
						window.parent.postMessage( JSON.stringify( { action: "decrementUpdateCount", upgradeType: "' . $type . '" } ), window.location.protocol + "//" + window.location.hostname );
					}
				</script>';
		} else {
			echo '<script type="text/javascript">
					(function( wp ) {
						if ( wp && wp.updates.decrementCount ) {
							wp.updates.decrementCount( "' . $type . '" );
						}
					})( window.wp );
				</script>';
		}
	}

	public function bulk_header() {}
	public function bulk_footer() {}
}

/**
 * Plugin Upgrader Skin for WordPress Plugin Upgrades.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class Plugin_Upgrader_Skin extends WP_Upgrader_Skin {
	public $plugin = '';
	public $plugin_active = false;
	public $plugin_network_active = false;

	public function __construct($args = array()) {
		$defaults = array( 'url' => '', 'plugin' => '', 'nonce' => '', 'title' => __('Update Plugin') );
		$args = wp_parse_args($args, $defaults);

		$this->plugin = $args['plugin'];

		$this->plugin_active = is_plugin_active( $this->plugin );
		$this->plugin_network_active = is_plugin_active_for_network( $this->plugin );

		parent::__construct($args);
	}

	public function after() {
		$this->plugin = $this->upgrader->plugin_info();
		if ( !empty($this->plugin) && !is_wp_error($this->result) && $this->plugin_active ){
			echo '<iframe style="border:0;overflow:hidden" width="100%" height="170px" src="' . wp_nonce_url('update.php?action=activate-plugin&networkwide=' . $this->plugin_network_active . '&plugin=' . urlencode( $this->plugin ), 'activate-plugin_' . $this->plugin) .'"></iframe>';
		}

		$this->decrement_update_count( 'plugin' );

		$update_actions =  array(
			'activate_plugin' => '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . urlencode( $this->plugin ), 'activate-plugin_' . $this->plugin) . '" title="' . esc_attr__('Activate this plugin') . '" target="_parent">' . __('Activate Plugin') . '</a>',
			'plugins_page' => '<a href="' . self_admin_url('plugins.php') . '" title="' . esc_attr__('Go to plugins page') . '" target="_parent">' . __('Return to Plugins page') . '</a>'
		);
		if ( $this->plugin_active || ! $this->result || is_wp_error( $this->result ) || ! current_user_can( 'activate_plugins' ) )
			unset( $update_actions['activate_plugin'] );

		/**
		 * Filter the list of action links available following a single plugin update.
		 *
		 * @since 2.7.0
		 *
		 * @param array  $update_actions Array of plugin action links.
		 * @param string $plugin         Path to the plugin file.
		 */
		$update_actions = apply_filters( 'update_plugin_complete_actions', $update_actions, $this->plugin );

		if ( ! empty($update_actions) )
			$this->feedback(implode(' | ', (array)$update_actions));
	}
}

/**
 * Plugin Upgrader Skin for WordPress Plugin Upgrades.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 3.0.0
 */
class Bulk_Upgrader_Skin extends WP_Upgrader_Skin {
	public $in_loop = false;
	/**
	 * @var string|false
	 */
	public $error = false;

	public function __construct($args = array()) {
		$defaults = array( 'url' => '', 'nonce' => '' );
		$args = wp_parse_args($args, $defaults);

		parent::__construct($args);
	}

	public function add_strings() {
		$this->upgrader->strings['skin_upgrade_start'] = __('The update process is starting. This process may take a while on some hosts, so please be patient.');
		$this->upgrader->strings['skin_update_failed_error'] = __('An error occurred while updating %1$s: <strong>%2$s</strong>');
		$this->upgrader->strings['skin_update_failed'] = __('The update of %1$s failed.');
		$this->upgrader->strings['skin_update_successful'] = __( '%1$s updated successfully.' ) . ' <a onclick="%2$s" href="#" class="hide-if-no-js"><span>' . __( 'Show Details' ) . '</span><span class="hidden">' . __( 'Hide Details' ) . '</span></a>';
		$this->upgrader->strings['skin_upgrade_end'] = __('All updates have been completed.');
	}

	/**
	 * @param string $string
	 */
	public function feedback($string) {
		if ( isset( $this->upgrader->strings[$string] ) )
			$string = $this->upgrader->strings[$string];

		if ( strpos($string, '%') !== false ) {
			$args = func_get_args();
			$args = array_splice($args, 1);
			if ( $args ) {
				$args = array_map( 'strip_tags', $args );
				$args = array_map( 'esc_html', $args );
				$string = vsprintf($string, $args);
			}
		}
		if ( empty($string) )
			return;
		if ( $this->in_loop )
			echo "$string<br />\n";
		else
			echo "<p>$string</p>\n";
	}

	public function header() {
		// Nothing, This will be displayed within a iframe.
	}

	public function footer() {
		// Nothing, This will be displayed within a iframe.
	}
	public function error($error) {
		if ( is_string($error) && isset( $this->upgrader->strings[$error] ) )
			$this->error = $this->upgrader->strings[$error];

		if ( is_wp_error($error) ) {
			$messages = array();
			foreach ( $error->get_error_messages() as $emessage ) {
				if ( $error->get_error_data() && is_string( $error->get_error_data() ) )
					$messages[] = $emessage . ' ' . esc_html( strip_tags( $error->get_error_data() ) );
				else
					$messages[] = $emessage;
			}
			$this->error = implode(', ', $messages);
		}
		echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js($this->upgrader->update_current) . '\').hide();</script>';
	}

	public function bulk_header() {
		$this->feedback('skin_upgrade_start');
	}

	public function bulk_footer() {
		$this->feedback('skin_upgrade_end');
	}

	public function before($title = '') {
		$this->in_loop = true;
		printf( '<h4>' . $this->upgrader->strings['skin_before_update_header'] . ' <span class="spinner waiting-' . $this->upgrader->update_current . '"></span></h4>',  $title, $this->upgrader->update_current, $this->upgrader->update_count);
		echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js($this->upgrader->update_current) . '\').css("display", "inline-block");</script>';
		echo '<div class="update-messages hide-if-js" id="progress-' . esc_attr($this->upgrader->update_current) . '"><p>';
		$this->flush_output();
	}

	public function after($title = '') {
		echo '</p></div>';
		if ( $this->error || ! $this->result ) {
			if ( $this->error )
				echo '<div class="error"><p>' . sprintf($this->upgrader->strings['skin_update_failed_error'], $title, $this->error) . '</p></div>';
			else
				echo '<div class="error"><p>' . sprintf($this->upgrader->strings['skin_update_failed'], $title) . '</p></div>';

			echo '<script type="text/javascript">jQuery(\'#progress-' . esc_js($this->upgrader->update_current) . '\').show();</script>';
		}
		if ( $this->result && ! is_wp_error( $this->result ) ) {
			if ( ! $this->error )
				echo '<div class="updated"><p>' . sprintf($this->upgrader->strings['skin_update_successful'], $title, 'jQuery(\'#progress-' . esc_js($this->upgrader->update_current) . '\').toggle();jQuery(\'span\', this).toggle(); return false;') . '</p></div>';
			echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js($this->upgrader->update_current) . '\').hide();</script>';
		}

		$this->reset();
		$this->flush_output();
	}

	public function reset() {
		$this->in_loop = false;
		$this->error = false;
	}

	public function flush_output() {
		wp_ob_end_flush_all();
		flush();
	}
}

class Bulk_Plugin_Upgrader_Skin extends Bulk_Upgrader_Skin {
	public $plugin_info = array(); // Plugin_Upgrader::bulk() will fill this in.

	public function add_strings() {
		parent::add_strings();
		$this->upgrader->strings['skin_before_update_header'] = __('Updating Plugin %1$s (%2$d/%3$d)');
	}

	public function before($title = '') {
		parent::before($this->plugin_info['Title']);
	}

	public function after($title = '') {
		parent::after($this->plugin_info['Title']);
		$this->decrement_update_count( 'plugin' );
	}
	public function bulk_footer() {
		parent::bulk_footer();
		$update_actions =  array(
			'plugins_page' => '<a href="' . self_admin_url('plugins.php') . '" title="' . esc_attr__('Go to plugins page') . '" target="_parent">' . __('Return to Plugins page') . '</a>',
			'updates_page' => '<a href="' . self_admin_url('update-core.php') . '" title="' . esc_attr__('Go to WordPress Updates page') . '" target="_parent">' . __('Return to WordPress Updates') . '</a>'
		);
		if ( ! current_user_can( 'activate_plugins' ) )
			unset( $update_actions['plugins_page'] );

		/**
		 * Filter the list of action links available following bulk plugin updates.
		 *
		 * @since 3.0.0
		 *
		 * @param array $update_actions Array of plugin action links.
		 * @param array $plugin_info    Array of information for the last-updated plugin.
		 */
		$update_actions = apply_filters( 'update_bulk_plugins_complete_actions', $update_actions, $this->plugin_info );

		if ( ! empty($update_actions) )
			$this->feedback(implode(' | ', (array)$update_actions));
	}
}

class Bulk_Theme_Upgrader_Skin extends Bulk_Upgrader_Skin {
	public $theme_info = array(); // Theme_Upgrader::bulk() will fill this in.

	public function add_strings() {
		parent::add_strings();
		$this->upgrader->strings['skin_before_update_header'] = __('Updating Theme %1$s (%2$d/%3$d)');
	}

	public function before($title = '') {
		parent::before( $this->theme_info->display('Name') );
	}

	public function after($title = '') {
		parent::after( $this->theme_info->display('Name') );
		$this->decrement_update_count( 'theme' );
	}

	public function bulk_footer() {
		parent::bulk_footer();
		$update_actions =  array(
			'themes_page' => '<a href="' . self_admin_url('themes.php') . '" title="' . esc_attr__('Go to themes page') . '" target="_parent">' . __('Return to Themes page') . '</a>',
			'updates_page' => '<a href="' . self_admin_url('update-core.php') . '" title="' . esc_attr__('Go to WordPress Updates page') . '" target="_parent">' . __('Return to WordPress Updates') . '</a>'
		);
		if ( ! current_user_can( 'switch_themes' ) && ! current_user_can( 'edit_theme_options' ) )
			unset( $update_actions['themes_page'] );

		/**
		 * Filter the list of action links available following bulk theme updates.
		 *
		 * @since 3.0.0
		 *
		 * @param array $update_actions Array of theme action links.
		 * @param array $theme_info     Array of information for the last-updated theme.
		 */
		$update_actions = apply_filters( 'update_bulk_theme_complete_actions', $update_actions, $this->theme_info );

		if ( ! empty($update_actions) )
			$this->feedback(implode(' | ', (array)$update_actions));
	}
}

/**
 * Plugin Installer Skin for WordPress Plugin Installer.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class Plugin_Installer_Skin extends WP_Upgrader_Skin {
	public $api;
	public $type;

	public function __construct($args = array()) {
		$defaults = array( 'type' => 'web', 'url' => '', 'plugin' => '', 'nonce' => '', 'title' => '' );
		$args = wp_parse_args($args, $defaults);

		$this->type = $args['type'];
		$this->api = isset($args['api']) ? $args['api'] : array();

		parent::__construct($args);
	}

	public function before() {
		if ( !empty($this->api) )
			$this->upgrader->strings['process_success'] = sprintf( __('Successfully installed the plugin <strong>%s %s</strong>.'), $this->api->name, $this->api->version);
	}

	public function after() {

		$plugin_file = $this->upgrader->plugin_info();

		$install_actions = array();

		$from = isset($_GET['from']) ? wp_unslash( $_GET['from'] ) : 'plugins';

		if ( 'import' == $from )
			$install_actions['activate_plugin'] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;from=import&amp;plugin=' . urlencode( $plugin_file ), 'activate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Activate this plugin') . '" target="_parent">' . __('Activate Plugin &amp; Run Importer') . '</a>';
		else
			$install_actions['activate_plugin'] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . urlencode( $plugin_file ), 'activate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Activate this plugin') . '" target="_parent">' . __('Activate Plugin') . '</a>';

		if ( is_multisite() && current_user_can( 'manage_network_plugins' ) ) {
			$install_actions['network_activate'] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;networkwide=1&amp;plugin=' . urlencode( $plugin_file ), 'activate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Activate this plugin for all sites in this network') . '" target="_parent">' . __('Network Activate') . '</a>';
			unset( $install_actions['activate_plugin'] );
		}

		if ( 'import' == $from ) {
			$install_actions['importers_page'] = '<a href="' . admin_url('import.php') . '" title="' . esc_attr__('Return to Importers') . '" target="_parent">' . __('Return to Importers') . '</a>';
		} elseif ( $this->type == 'web' ) {
			$install_actions['plugins_page'] = '<a href="' . self_admin_url('plugin-install.php') . '" title="' . esc_attr__('Return to Plugin Installer') . '" target="_parent">' . __('Return to Plugin Installer') . '</a>';
		} else {
			$install_actions['plugins_page'] = '<a href="' . self_admin_url('plugins.php') . '" title="' . esc_attr__('Return to Plugins page') . '" target="_parent">' . __('Return to Plugins page') . '</a>';
		}

		if ( ! $this->result || is_wp_error($this->result) ) {
			unset( $install_actions['activate_plugin'], $install_actions['network_activate'] );
		} elseif ( ! current_user_can( 'activate_plugins' ) ) {
			unset( $install_actions['activate_plugin'] );
		}

		/**
		 * Filter the list of action links available following a single plugin installation.
		 *
		 * @since 2.7.0
		 *
		 * @param array  $install_actions Array of plugin action links.
		 * @param object $api             Object containing WordPress.org API plugin data. Empty
		 *                                for non-API installs, such as when a plugin is installed
		 *                                via upload.
		 * @param string $plugin_file     Path to the plugin file.
		 */
		$install_actions = apply_filters( 'install_plugin_complete_actions', $install_actions, $this->api, $plugin_file );

		if ( ! empty($install_actions) )
			$this->feedback(implode(' | ', (array)$install_actions));
	}
}

/**
 * Theme Installer Skin for the WordPress Theme Installer.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class Theme_Installer_Skin extends WP_Upgrader_Skin {
	public $api;
	public $type;

	public function __construct($args = array()) {
		$defaults = array( 'type' => 'web', 'url' => '', 'theme' => '', 'nonce' => '', 'title' => '' );
		$args = wp_parse_args($args, $defaults);

		$this->type = $args['type'];
		$this->api = isset($args['api']) ? $args['api'] : array();

		parent::__construct($args);
	}

	public function before() {
		if ( !empty($this->api) )
			$this->upgrader->strings['process_success'] = sprintf( $this->upgrader->strings['process_success_specific'], $this->api->name, $this->api->version);
	}

	public function after() {
		if ( empty($this->upgrader->result['destination_name']) )
			return;

		$theme_info = $this->upgrader->theme_info();
		if ( empty( $theme_info ) )
			return;

		$name       = $theme_info->display('Name');
		$stylesheet = $this->upgrader->result['destination_name'];
		$template   = $theme_info->get_template();

		$preview_link = add_query_arg( array(
			'preview'    => 1,
			'template'   => urlencode( $template ),
			'stylesheet' => urlencode( $stylesheet ),
		), trailingslashit( home_url() ) );

		$activate_link = add_query_arg( array(
			'action'     => 'activate',
			'template'   => urlencode( $template ),
			'stylesheet' => urlencode( $stylesheet ),
		), admin_url('themes.php') );
		$activate_link = wp_nonce_url( $activate_link, 'switch-theme_' . $stylesheet );

		$install_actions = array();
		$install_actions['preview']  = '<a href="' . esc_url( $preview_link ) . '" class="hide-if-customize" title="' . esc_attr( sprintf( __('Preview &#8220;%s&#8221;'), $name ) ) . '">' . __('Preview') . '</a>';
		if ( current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
			$install_actions['preview'] .= '<a href="' . wp_customize_url( $stylesheet ) . '" class="hide-if-no-customize load-customize" title="' . esc_attr( sprintf( __('Preview &#8220;%s&#8221;'), $name ) ) . '">' . __('Live Preview') . '</a>';
		}
		$install_actions['activate'] = '<a href="' . esc_url( $activate_link ) . '" class="activatelink" title="' . esc_attr( sprintf( __('Activate &#8220;%s&#8221;'), $name ) ) . '">' . __('Activate') . '</a>';

		if ( is_network_admin() && current_user_can( 'manage_network_themes' ) )
			$install_actions['network_enable'] = '<a href="' . esc_url( wp_nonce_url( 'themes.php?action=enable&amp;theme=' . urlencode( $stylesheet ), 'enable-theme_' . $stylesheet ) ) . '" title="' . esc_attr__( 'Enable this theme for all sites in this network' ) . '" target="_parent">' . __( 'Network Enable' ) . '</a>';

		if ( $this->type == 'web' )
			$install_actions['themes_page'] = '<a href="' . self_admin_url('theme-install.php') . '" title="' . esc_attr__('Return to Theme Installer') . '" target="_parent">' . __('Return to Theme Installer') . '</a>';
		elseif ( current_user_can( 'switch_themes' ) || current_user_can( 'edit_theme_options' ) )
			$install_actions['themes_page'] = '<a href="' . self_admin_url('themes.php') . '" title="' . esc_attr__('Themes page') . '" target="_parent">' . __('Return to Themes page') . '</a>';

		if ( ! $this->result || is_wp_error($this->result) || is_network_admin() || ! current_user_can( 'switch_themes' ) )
			unset( $install_actions['activate'], $install_actions['preview'] );

		/**
		 * Filter the list of action links available following a single theme installation.
		 *
		 * @since 2.8.0
		 *
		 * @param array    $install_actions Array of theme action links.
		 * @param object   $api             Object containing WordPress.org API theme data.
		 * @param string   $stylesheet      Theme directory name.
		 * @param WP_Theme $theme_info      Theme object.
		 */
		$install_actions = apply_filters( 'install_theme_complete_actions', $install_actions, $this->api, $stylesheet, $theme_info );
		if ( ! empty($install_actions) )
			$this->feedback(implode(' | ', (array)$install_actions));
	}
}

/**
 * Theme Upgrader Skin for WordPress Theme Upgrades.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class Theme_Upgrader_Skin extends WP_Upgrader_Skin {
	public $theme = '';

	public function __construct($args = array()) {
		$defaults = array( 'url' => '', 'theme' => '', 'nonce' => '', 'title' => __('Update Theme') );
		$args = wp_parse_args($args, $defaults);

		$this->theme = $args['theme'];

		parent::__construct($args);
	}

	public function after() {
		$this->decrement_update_count( 'theme' );

		$update_actions = array();
		if ( ! empty( $this->upgrader->result['destination_name'] ) && $theme_info = $this->upgrader->theme_info() ) {
			$name       = $theme_info->display('Name');
			$stylesheet = $this->upgrader->result['destination_name'];
			$template   = $theme_info->get_template();

			$preview_link = add_query_arg( array(
				'preview'    => 1,
				'template'   => urlencode( $template ),
				'stylesheet' => urlencode( $stylesheet ),
			), trailingslashit( home_url() ) );

			$activate_link = add_query_arg( array(
				'action'     => 'activate',
				'template'   => urlencode( $template ),
				'stylesheet' => urlencode( $stylesheet ),
			), admin_url('themes.php') );
			$activate_link = wp_nonce_url( $activate_link, 'switch-theme_' . $stylesheet );

			if ( get_stylesheet() == $stylesheet ) {
				if ( current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
					$update_actions['preview']  = '<a href="' . wp_customize_url( $stylesheet ) . '" class="hide-if-no-customize load-customize" title="' . esc_attr( sprintf( __('Customize &#8220;%s&#8221;'), $name ) ) . '">' . __('Customize') . '</a>';
				}
			} elseif ( current_user_can( 'switch_themes' ) ) {
				$update_actions['preview']  = '<a href="' . esc_url( $preview_link ) . '" class="hide-if-customize" title="' . esc_attr( sprintf( __('Preview &#8220;%s&#8221;'), $name ) ) . '">' . __('Preview') . '</a>';
				if ( current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
					$update_actions['preview'] .= '<a href="' . wp_customize_url( $stylesheet ) . '" class="hide-if-no-customize load-customize" title="' . esc_attr( sprintf( __('Preview &#8220;%s&#8221;'), $name ) ) . '">' . __('Live Preview') . '</a>';
				}
				$update_actions['activate'] = '<a href="' . esc_url( $activate_link ) . '" class="activatelink" title="' . esc_attr( sprintf( __('Activate &#8220;%s&#8221;'), $name ) ) . '">' . __('Activate') . '</a>';
			}

			if ( ! $this->result || is_wp_error( $this->result ) || is_network_admin() )
				unset( $update_actions['preview'], $update_actions['activate'] );
		}

		$update_actions['themes_page'] = '<a href="' . self_admin_url('themes.php') . '" title="' . esc_attr__('Return to Themes page') . '" target="_parent">' . __('Return to Themes page') . '</a>';

		/**
		 * Filter the list of action links available following a single theme update.
		 *
		 * @since 2.8.0
		 *
		 * @param array  $update_actions Array of theme action links.
		 * @param string $theme          Theme directory name.
		 */
		$update_actions = apply_filters( 'update_theme_complete_actions', $update_actions, $this->theme );

		if ( ! empty($update_actions) )
			$this->feedback(implode(' | ', (array)$update_actions));
	}
}

/**
 * Translation Upgrader Skin for WordPress Translation Upgrades.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 3.7.0
 */
class Language_Pack_Upgrader_Skin extends WP_Upgrader_Skin {
	public $language_update = null;
	public $done_header = false;
	public $done_footer = false;
	public $display_footer_actions = true;

	public function __construct( $args = array() ) {
		$defaults = array( 'url' => '', 'nonce' => '', 'title' => __( 'Update Translations' ), 'skip_header_footer' => false );
		$args = wp_parse_args( $args, $defaults );
		if ( $args['skip_header_footer'] ) {
			$this->done_header = true;
			$this->done_footer = true;
			$this->display_footer_actions = false;
		}
		parent::__construct( $args );
	}

	public function before() {
		$name = $this->upgrader->get_name_for_update( $this->language_update );

		echo '<div class="update-messages lp-show-latest">';

		printf( '<h4>' . __( 'Updating translations for %1$s (%2$s)&#8230;' ) . '</h4>', $name, $this->language_update->language );
	}

	public function error( $error ) {
		echo '<div class="lp-error">';
		parent::error( $error );
		echo '</div>';
	}

	public function after() {
		echo '</div>';
	}

	public function bulk_footer() {
		$this->decrement_update_count( 'translation' );
		$update_actions = array();
		$update_actions['updates_page'] = '<a href="' . self_admin_url( 'update-core.php' ) . '" title="' . esc_attr__( 'Go to WordPress Updates page' ) . '" target="_parent">' . __( 'Return to WordPress Updates' ) . '</a>';

		/**
		 * Filter the list of action links available following a translations update.
		 *
		 * @since 3.7.0
		 *
		 * @param array $update_actions Array of translations update links.
		 */
		$update_actions = apply_filters( 'update_translations_complete_actions', $update_actions );

		if ( $update_actions && $this->display_footer_actions )
			$this->feedback( implode( ' | ', $update_actions ) );
	}
}

/**
 * Upgrader Skin for Automatic WordPress Upgrades
 *
 * This skin is designed to be used when no output is intended, all output
 * is captured and stored for the caller to process and log/email/discard.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 3.7.0
 */
class Automatic_Upgrader_Skin extends WP_Upgrader_Skin {
	protected $messages = array();

	public function request_filesystem_credentials( $error = false, $context = '', $allow_relaxed_file_ownership = false ) {
		if ( $context ) {
			$this->options['context'] = $context;
		}
		// TODO: fix up request_filesystem_credentials(), or split it, to allow us to request a no-output version
		// This will output a credentials form in event of failure, We don't want that, so just hide with a buffer
		ob_start();
		$result = parent::request_filesystem_credentials( $error, $context, $allow_relaxed_file_ownership );
		ob_end_clean();
		return $result;
	}

	public function get_upgrade_messages() {
		return $this->messages;
	}

	/**
	 * @param string|array|WP_Error $data
	 */
	public function feedback( $data ) {
		if ( is_wp_error( $data ) ) {
			$string = $data->get_error_message();
		} elseif ( is_array( $data ) ) {
			return;
		} else {
			$string = $data;
		}
		if ( ! empty( $this->upgrader->strings[ $string ] ) )
			$string = $this->upgrader->strings[ $string ];

		if ( strpos( $string, '%' ) !== false ) {
			$args = func_get_args();
			$args = array_splice( $args, 1 );
			if ( ! empty( $args ) )
				$string = vsprintf( $string, $args );
		}

		$string = trim( $string );

		// Only allow basic HTML in the messages, as it'll be used in emails/logs rather than direct browser output.
		$string = wp_kses( $string, array(
			'a' => array(
				'href' => true
			),
			'br' => true,
			'em' => true,
			'strong' => true,
		) );

		if ( empty( $string ) )
			return;

		$this->messages[] = $string;
	}

	public function header() {
		ob_start();
	}

	public function footer() {
		$output = ob_get_contents();
		if ( ! empty( $output ) )
			$this->feedback( $output );
		ob_end_clean();
	}

	public function bulk_header() {}
	public function bulk_footer() {}
	public function before() {}
	public function after() {}
}
