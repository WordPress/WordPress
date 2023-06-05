<?php
/**
 * WooCommerce Admin WooCommerce Payments Note Provider.
 *
 * Adds a note to the merchant's inbox showing the benefits of the WooCommerce Payments.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\Notes;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * WooCommerce_Payments
 */
class WooCommercePayments {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-woocommerce-payments';

	/**
	 * Name of the note for use in the database.
	 */
	const PLUGIN_SLUG = 'woocommerce-payments';

	/**
	 * Name of the note for use in the database.
	 */
	const PLUGIN_FILE = 'woocommerce-payments/woocommerce-payments.php';

	/**
	 * Attach hooks.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'install_on_action' ) );
		add_action( 'wc-admin-woocommerce-payments_add_note', array( $this, 'add_note' ) );
	}

	/**
	 * Maybe add a note on WooCommerce Payments for US based sites older than a week without the plugin installed.
	 */
	public static function possibly_add_note() {
		if ( ! self::is_wc_admin_active_in_date_range( 'week-1-4' ) || 'US' !== WC()->countries->get_base_country() ) {
			return;
		}

		$data_store = Notes::load_data_store();

		// We already have this note? Then mark the note as actioned.
		$note_ids = $data_store->get_notes_with_name( self::NOTE_NAME );
		if ( ! empty( $note_ids ) ) {

			$note_id = array_pop( $note_ids );
			$note    = Notes::get_note( $note_id );
			if ( false === $note ) {
				return;
			}

			// If the WooCommerce Payments plugin was installed after the note was created, make sure it's marked as actioned.
			if ( self::is_installed() && Note::E_WC_ADMIN_NOTE_ACTIONED !== $note->get_status() ) {
				$note->set_status( Note::E_WC_ADMIN_NOTE_ACTIONED );
				$note->save();
			}

			return;
		}

		$current_date = new \DateTime();
		$publish_date = new \DateTime( '2020-04-14' );

		if ( $current_date >= $publish_date ) {

			$note = self::get_note();
			if ( self::can_be_added() ) {
				$note->save();
			}

			return;

		} else {

			$hook_name = sprintf( '%s_add_note', self::NOTE_NAME );

			if ( ! WC()->queue()->get_next( $hook_name ) ) {
				WC()->queue()->schedule_single( $publish_date->getTimestamp(), $hook_name );
			}
		}
	}

	/**
	 * Add a note about WooCommerce Payments.
	 *
	 * @return Note
	 */
	public static function get_note() {
		$note = new Note();
		$note->set_title( __( 'Try the new way to get paid', 'woocommerce' ) );
		$note->set_content(
			__( 'Securely accept credit and debit cards on your site. Manage transactions without leaving your WordPress dashboard. Only with <strong>WooCommerce Payments</strong>.', 'woocommerce' ) .
			'<br><br>' .
			sprintf(
				/* translators: 1: opening link tag, 2: closing tag */
				__( 'By clicking "Get started", you agree to our %1$sTerms of Service%2$s', 'woocommerce' ),
				'<a href="https://wordpress.com/tos/" target="_blank">',
				'</a>'
			)
		);
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_MARKETING );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action( 'learn-more', __( 'Learn more', 'woocommerce' ), 'https://woocommerce.com/payments/?utm_medium=product', Note::E_WC_ADMIN_NOTE_UNACTIONED );
		$note->add_action( 'get-started', __( 'Get started', 'woocommerce' ), wc_admin_url( '&action=setup-woocommerce-payments' ), Note::E_WC_ADMIN_NOTE_ACTIONED, true );
		$note->add_nonce_to_action( 'get-started', 'setup-woocommerce-payments', '' );

		// Create the note as "actioned" if the plugin is already installed.
		if ( self::is_installed() ) {
			$note->set_status( Note::E_WC_ADMIN_NOTE_ACTIONED );
		}
		return $note;
	}


	/**
	 * Check if the WooCommerce Payments plugin is active or installed.
	 */
	protected static function is_installed() {
		if ( defined( 'WC_Payments' ) ) {
			return true;
		}
		include_once ABSPATH . '/wp-admin/includes/plugin.php';
		return 0 === validate_plugin( self::PLUGIN_FILE );
	}

	/**
	 * Install and activate WooCommerce Payments.
	 *
	 * @return boolean Whether the plugin was successfully activated.
	 */
	private function install_and_activate_wcpay() {
		$install_request = array( 'plugins' => self::PLUGIN_SLUG );
		$installer       = new \Automattic\WooCommerce\Admin\API\Plugins();
		$result          = $installer->install_plugins( $install_request );
		if ( is_wp_error( $result ) ) {
			return false;
		}

		wc_admin_record_tracks_event( 'woocommerce_payments_install', array( 'context' => 'inbox' ) );

		$activate_request = array( 'plugins' => self::PLUGIN_SLUG );
		$result           = $installer->activate_plugins( $activate_request );
		if ( is_wp_error( $result ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Install & activate WooCommerce Payments plugin, and redirect to setup.
	 */
	public function install_on_action() {
		// TODO: Need to validate this request more strictly since we're taking install actions directly?
		if (
			! isset( $_GET['page'] ) ||
			'wc-admin' !== $_GET['page'] ||
			! isset( $_GET['action'] ) ||
			'setup-woocommerce-payments' !== $_GET['action']
		) {
			return;
		}

		$data_store = Notes::load_data_store();

		// We already have this note? Then mark the note as actioned.
		$note_ids = $data_store->get_notes_with_name( self::NOTE_NAME );
		if ( empty( $note_ids ) ) {
			return;
		}

		$note_id = array_pop( $note_ids );
		$note    = Notes::get_note( $note_id );
		if ( false === $note ) {
			return;
		}
		$action = $note->get_action( 'get-started' );
		if ( ! $action ||
			( isset( $action->nonce_action ) &&
				(
					empty( $_GET['_wpnonce'] ) ||
					! wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), $action->nonce_action ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
				)
			)
		) {
			return;
		}

		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$this->install_and_activate_wcpay();

		// WooCommerce Payments is installed at this point, so link straight into the onboarding flow.
		$connect_url = add_query_arg(
			array(
				'wcpay-connect' => '1',
				'_wpnonce'      => wp_create_nonce( 'wcpay-connect' ),
			),
			admin_url()
		);
		wp_safe_redirect( $connect_url );
		exit;
	}
}
