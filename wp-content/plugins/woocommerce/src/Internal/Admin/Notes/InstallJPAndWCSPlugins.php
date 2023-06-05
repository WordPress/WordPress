<?php
/**
 * WooCommerce Admin Add Install Jetpack and WooCommerce Shipping & Tax Plugin Note Provider.
 *
 * Adds a note to the merchant's inbox prompting them to install the Jetpack
 * and WooCommerce Shipping & Tax plugins after it fails to install during
 * WooCommerce setup.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\Notes;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;
use Automattic\WooCommerce\Admin\PluginsHelper;

/**
 * Install_JP_And_WCS_Plugins
 */
class InstallJPAndWCSPlugins {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-install-jp-and-wcs-plugins';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_note_action_install-jp-and-wcs-plugins', array( $this, 'install_jp_and_wcs_plugins' ) );
		add_action( 'activated_plugin', array( $this, 'action_note' ) );
		add_action( 'woocommerce_plugins_install_api_error', array( $this, 'on_install_error' ) );
		add_action( 'woocommerce_plugins_install_error', array( $this, 'on_install_error' ) );
		add_action( 'woocommerce_plugins_activate_error', array( $this, 'on_install_error' ) );
	}

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		$content = __( 'We noticed that there was a problem during the Jetpack and WooCommerce Shipping & Tax install. Please try again and enjoy all the advantages of having the plugins connected to your store! Sorry for the inconvenience. The "Jetpack" and "WooCommerce Shipping & Tax" plugins will be installed & activated for free.', 'woocommerce' );

		$note = new Note();
		$note->set_title( __( 'Uh oh... There was a problem during the Jetpack and WooCommerce Shipping & Tax install. Please try again.', 'woocommerce' ) );
		$note->set_content( $content );
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action(
			'install-jp-and-wcs-plugins',
			__( 'Install plugins', 'woocommerce' ),
			false,
			Note::E_WC_ADMIN_NOTE_ACTIONED
		);
		return $note;
	}

	/**
	 * Action the Install Jetpack and WooCommerce Shipping & Tax note, if any exists,
	 * and as long as both the Jetpack and WooCommerce Shipping & Tax plugins have been
	 * activated.
	 */
	public static function action_note() {
		// Make sure that both plugins are active before actioning the note.
		$active_plugin_slugs = PluginsHelper::get_active_plugin_slugs();
		$jp_active           = in_array( 'jetpack', $active_plugin_slugs, true );
		$wcs_active          = in_array( 'woocommerce-services', $active_plugin_slugs, true );

		if ( ! $jp_active || ! $wcs_active ) {
			return;
		}

		// Action any notes with a matching name.
		$data_store = Notes::load_data_store();
		$note_ids   = $data_store->get_notes_with_name( self::NOTE_NAME );

		foreach ( $note_ids as $note_id ) {
			$note = Notes::get_note( $note_id );

			if ( $note ) {
				$note->set_status( Note::E_WC_ADMIN_NOTE_ACTIONED );
				$note->save();
			}
		}
	}

	/**
	 * Install the Jetpack and WooCommerce Shipping & Tax plugins in response to the action
	 * being clicked in the admin note.
	 *
	 * @param Note $note The note being actioned.
	 */
	public function install_jp_and_wcs_plugins( $note ) {
		if ( self::NOTE_NAME !== $note->get_name() ) {
			return;
		}

		$this->install_and_activate_plugin( 'jetpack' );
		$this->install_and_activate_plugin( 'woocommerce-services' );
	}

	/**
	 * Installs and activates the specified plugin.
	 *
	 * @param string $plugin The plugin slug.
	 */
	private function install_and_activate_plugin( $plugin ) {
		$install_request = array( 'plugin' => $plugin );
		$installer       = new \Automattic\WooCommerce\Admin\API\OnboardingPlugins();
		$result          = $installer->install_plugin( $install_request );

		// @todo Use the error statuses to decide whether or not to action the note.
		if ( is_wp_error( $result ) ) {
			return;
		}

		$activate_request = array( 'plugins' => $plugin );

		$installer->activate_plugins( $activate_request );
	}

	/**
	 * Create an alert notification in response to an error installing a plugin.
	 *
	 * @param string $slug The slug of the plugin being installed.
	 */
	public function on_install_error( $slug ) {
		// Exit early if we're not installing the Jetpack or the WooCommerce Shipping & Tax plugins.
		if ( 'jetpack' !== $slug && 'woocommerce-services' !== $slug ) {
			return;
		}

		self::possibly_add_note();
	}
}
