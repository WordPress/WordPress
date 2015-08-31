<?php

/*
Plugin Name: Sample Plugin
Version: 1.0
Plugin URI: https://yoast.com/
Description: A sample plugin to test the License Manager
Author: Yoast, DvanKooten
Author URI: http://yoast.com/
Text Domain: sample-plugin
*/

/**
 * Class Sample_Plugin
 *
 */
class Sample_Plugin {

	public function __construct() {

		// we only need license stuff inside the admin area
		if ( is_admin() ) {

			// add menu item
			add_action( 'admin_menu', array( $this, 'add_license_menu' ) );

			// load license class
			$this->load_license_manager();
		}


	}

	/**
	 * Loads the License_Plugin_Manager class
	 *
	 * The class will take care of the rest: notices, license (de)activations, updates, etc..
	 */
	public function load_license_manager() {

		// Instantiate license class
		$license_manager = new Yoast_Plugin_License_Manager( new Sample_Product() );

		// Setup the required hooks
		$license_manager->setup_hooks();

	}

	/**
	 * Add license page and add it to Themes menu
	 */
	public function add_license_menu() {
		$theme_page = add_options_page( sprintf( __( '%s License', $this->text_domain ), $this->item_name ), sprintf( __( '%s License', $this->text_domain ), $this->item_name ), 'manage_options', $this->text_domain . '-license', array( $this, 'show_license_page' ) );
	}

	/**
	 * Shows license page
	 */
	public function show_license_page() {

		// Instantiate license class
		$license_manager = new Yoast_Plugin_License_Manager( new Sample_Product() );

		?>
		<div class="wrap">
			<?php //settings_errors(); ?>

			<?php $license_manager->show_license_form( false ); ?>
		</div>
	<?php
	}
}

new Sample_Plugin();
