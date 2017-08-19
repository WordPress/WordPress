<?php
/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.5.2 for parent theme Decode
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 */
require_once get_template_directory() . '/library/tgmpa/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'travelify_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function travelify_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		
		array(
			'name' => 'Simple Social Share Icons', // The plugin name.
			'slug' => 'kiwi-social-share', // The plugin slug (typically the folder name).
			'source' => '', // The plugin source.
			'required' => false, // If false, the plugin is only 'recommended' instead of required.
			'version' => '1.0.3', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
			'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url' => '', // If set, overrides default API URL and points to an external URL.
		)

	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'default_path' => '',                      // Default absolute path to pre-packaged plugins.
		'menu'        => 'mt-install-plugins', // Menu slug.
		'has_notices' => true,                    // Show admin notices or not.
		'dismissable' => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg' => true,                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message' => '',                      // Message to output right before the plugins table.
		'strings' => array(
			'page_title' => __('Install Required Plugins', 'travelify'),
			'menu_title' => __('Install Plugins', 'travelify'),
			'installing' => __('Installing Plugin: %s', 'travelify'), // %s = plugin name.
			'oops' => __('Something went wrong with the plugin API.', 'travelify'),
			'notice_can_install_required' => _n_noop('This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'travelify'), // %1$s = plugin name(s).
			'notice_can_install_recommended' => _n_noop('This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'travelify'), // %1$s = plugin name(s).
			'notice_cannot_install' => _n_noop('Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'travelify'), // %1$s = plugin name(s).
			'notice_can_activate_required' => _n_noop('The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'travelify'), // %1$s = plugin name(s).
			'notice_can_activate_recommended' => _n_noop('The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'travelify'), // %1$s = plugin name(s).
			'notice_cannot_activate' => _n_noop('Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'travelify'), // %1$s = plugin name(s).
			'notice_ask_to_update' => _n_noop('The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'travelify'), // %1$s = plugin name(s).
			'notice_cannot_update' => _n_noop('Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'travelify'), // %1$s = plugin name(s).
			'install_link' => _n_noop('Begin installing plugin', 'Begin installing plugins', 'travelify'),
			'activate_link' => _n_noop('Begin activating plugin', 'Begin activating plugins', 'travelify'),
			'return' => __('Return to Required Plugins Installer', 'travelify'),
			'plugin_activated' => __('Plugin activated successfully.', 'travelify'),
			'complete' => __('All plugins installed and activated successfully. %s', 'travelify'), // %s = dashboard link.
			'nag_type' => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
		)
	);

	tgmpa( $plugins, $config );
}
