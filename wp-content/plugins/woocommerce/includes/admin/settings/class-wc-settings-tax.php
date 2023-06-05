<?php
/**
 * WooCommerce Tax Settings
 *
 * @package     WooCommerce\Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Settings_Tax', false ) ) {
	return new WC_Settings_Tax();
}

/**
 * WC_Settings_Tax.
 */
class WC_Settings_Tax extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'tax';
		$this->label = __( 'Tax', 'woocommerce' );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_admin_field_conflict_error', array( $this, 'conflict_error' ) );
		if ( wc_tax_enabled() ) {
			add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
		}
	}

	/**
	 * Creates the React mount point for the embedded banner.
	 */
	public function conflict_error() {
		?>
		<tr valign="top">
							<th scope="row" class="titledesc woocommerce_admin_tax_settings_slotfill_th">
							</th>
							<td class="forminp forminp-text woocommerce_admin_tax_settings_slotfill_td">
		<div id="wc_conflict_error_slotfill"> </div>
	</td>
	</tr>
		<?php
	}

	/**
	 * Add this page to settings.
	 *
	 * @param array $pages Existing pages.
	 * @return array|mixed
	 */
	public function add_settings_page( $pages ) {
		if ( wc_tax_enabled() ) {
			return parent::add_settings_page( $pages );
		} else {
			return $pages;
		}
	}

	/**
	 * Get own sections.
	 *
	 * @return array
	 */
	protected function get_own_sections() {
		$sections = array(
			''         => __( 'Tax options', 'woocommerce' ),
			'standard' => __( 'Standard rates', 'woocommerce' ),
		);

		// Get tax classes and display as links.
		$tax_classes = WC_Tax::get_tax_classes();

		foreach ( $tax_classes as $class ) {
			/* translators: $s tax rate section name */
			$sections[ sanitize_title( $class ) ] = sprintf( __( '%s rates', 'woocommerce' ), $class );
		}

		return $sections;
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings_for_default_section() {
		return include __DIR__ . '/views/settings-tax.php';
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		$tax_classes = WC_Tax::get_tax_class_slugs();

		if ( 'standard' === $current_section || in_array( $current_section, array_filter( $tax_classes ), true ) ) {
			$this->output_tax_rates();
		} else {
			parent::output();
		}
	}

	/**
	 * Save settings.
	 */
	public function save() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		global $current_section;

		if ( ! $current_section ) {
			$this->save_settings_for_current_section();

			if ( isset( $_POST['woocommerce_tax_classes'] ) ) {
				$this->save_tax_classes( wp_unslash( $_POST['woocommerce_tax_classes'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			}
		} elseif ( ! empty( $_POST['tax_rate_country'] ) ) {
			$this->save_tax_rates();
		} else {
			$this->save_settings_for_current_section();
		}

		$this->do_update_options_action();

		// Invalidate caches.
		WC_Cache_Helper::invalidate_cache_group( 'taxes' );
		WC_Cache_Helper::get_transient_version( 'shipping', true );
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Saves tax classes defined in the textarea to the tax class table instead of an option.
	 *
	 * @param string $raw_tax_classes Posted value.
	 * @return null
	 */
	public function save_tax_classes( $raw_tax_classes ) {
		$tax_classes          = array_filter( array_map( 'trim', explode( "\n", $raw_tax_classes ) ) );
		$existing_tax_classes = WC_Tax::get_tax_classes();
		$removed              = array_diff( $existing_tax_classes, $tax_classes );
		$added                = array_diff( $tax_classes, $existing_tax_classes );

		foreach ( $removed as $name ) {
			WC_Tax::delete_tax_class_by( 'name', $name );
		}

		foreach ( $added as $name ) {
			$tax_class = WC_Tax::create_tax_class( $name );

			// Display any error that could be triggered while creating tax classes.
			if ( is_wp_error( $tax_class ) ) {
				WC_Admin_Settings::add_error(
					sprintf(
						/* translators: 1: tax class name 2: error message */
						esc_html__( 'Additional tax class "%1$s" couldn\'t be saved. %2$s.', 'woocommerce' ),
						esc_html( $name ),
						$tax_class->get_error_message()
					)
				);
			}
		}

		return null;
	}

	/**
	 * Output tax rate tables.
	 */
	public function output_tax_rates() {
		global $current_section;

		$current_class = $this->get_current_tax_class();

		$countries = array();
		foreach ( WC()->countries->get_allowed_countries() as $value => $label ) {
			$countries[] = array(
				'value' => $value,
				'label' => esc_js( html_entity_decode( $label ) ),
			);
		}

		$states = array();
		foreach ( WC()->countries->get_allowed_country_states() as $label ) {
			foreach ( $label as $code => $state ) {
				$states[] = array(
					'value' => $code,
					'label' => esc_js( html_entity_decode( $state ) ),
				);
			}
		}

		$base_url = admin_url(
			add_query_arg(
				array(
					'page'    => 'wc-settings',
					'tab'     => 'tax',
					'section' => $current_section,
				),
				'admin.php'
			)
		);

		// Localize and enqueue our js.
		wp_localize_script(
			'wc-settings-tax',
			'htmlSettingsTaxLocalizeScript',
			array(
				'current_class' => $current_class,
				'wc_tax_nonce'  => wp_create_nonce( 'wc_tax_nonce-class:' . $current_class ),
				'base_url'      => $base_url,
				'rates'         => array_values( WC_Tax::get_rates_for_tax_class( $current_class ) ),
				'page'          => ! empty( $_GET['p'] ) ? absint( $_GET['p'] ) : 1, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				'limit'         => 100,
				'countries'     => $countries,
				'states'        => $states,
				'default_rate'  => array(
					'tax_rate_id'       => 0,
					'tax_rate_country'  => '',
					'tax_rate_state'    => '',
					'tax_rate'          => '',
					'tax_rate_name'     => '',
					'tax_rate_priority' => 1,
					'tax_rate_compound' => 0,
					'tax_rate_shipping' => 1,
					'tax_rate_order'    => null,
					'tax_rate_class'    => $current_class,
				),
				'strings'       => array(
					'no_rows_selected'        => __( 'No row(s) selected', 'woocommerce' ),
					'unload_confirmation_msg' => __( 'Your changed data will be lost if you leave this page without saving.', 'woocommerce' ),
					'csv_data_cols'           => array(
						__( 'Country code', 'woocommerce' ),
						__( 'State code', 'woocommerce' ),
						__( 'Postcode / ZIP', 'woocommerce' ),
						__( 'City', 'woocommerce' ),
						__( 'Rate %', 'woocommerce' ),
						__( 'Tax name', 'woocommerce' ),
						__( 'Priority', 'woocommerce' ),
						__( 'Compound', 'woocommerce' ),
						__( 'Shipping', 'woocommerce' ),
						__( 'Tax class', 'woocommerce' ),
					),
				),
			)
		);
		wp_enqueue_script( 'wc-settings-tax' );

		include __DIR__ . '/views/html-settings-tax.php';
	}

	/**
	 * Get tax class being edited.
	 *
	 * @return string
	 */
	private static function get_current_tax_class() {
		global $current_section;

		$tax_classes   = WC_Tax::get_tax_classes();
		$current_class = '';

		foreach ( $tax_classes as $class ) {
			if ( sanitize_title( $class ) === $current_section ) {
				$current_class = $class;
			}
		}

		return $current_class;
	}

	/**
	 * Get a posted tax rate.
	 *
	 * @param string $key   Key of tax rate in the post data array.
	 * @param int    $order Position/order of rate.
	 * @param string $class Tax class for rate.
	 * @return array
	 */
	private function get_posted_tax_rate( $key, $order, $class ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- this is called from 'save_tax_rates' only, where nonce is already verified.
		$tax_rate      = array();
		$tax_rate_keys = array(
			'tax_rate_country',
			'tax_rate_state',
			'tax_rate',
			'tax_rate_name',
			'tax_rate_priority',
		);

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		foreach ( $tax_rate_keys as $tax_rate_key ) {
			if ( isset( $_POST[ $tax_rate_key ], $_POST[ $tax_rate_key ][ $key ] ) ) {
				$tax_rate[ $tax_rate_key ] = wc_clean( wp_unslash( $_POST[ $tax_rate_key ][ $key ] ) );
			}
		}

		$tax_rate['tax_rate_compound'] = isset( $_POST['tax_rate_compound'][ $key ] ) ? 1 : 0;
		$tax_rate['tax_rate_shipping'] = isset( $_POST['tax_rate_shipping'][ $key ] ) ? 1 : 0;
		$tax_rate['tax_rate_order']    = $order;
		$tax_rate['tax_rate_class']    = $class;
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		return $tax_rate;
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Save tax rates.
	 */
	public function save_tax_rates() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- this is called via "do_action('woocommerce_settings_save_'...") in base class, where nonce is verified first.
		global $wpdb;

		$current_class = sanitize_title( $this->get_current_tax_class() );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.NonceVerification.Missing
		$posted_countries = wc_clean( wp_unslash( $_POST['tax_rate_country'] ) );

		// get the tax rate id of the first submitted row.
		$first_tax_rate_id = key( $posted_countries );

		// get the order position of the first tax rate id.
		$tax_rate_order = absint( $wpdb->get_var( $wpdb->prepare( "SELECT tax_rate_order FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_id = %s", $first_tax_rate_id ) ) );

		$index = isset( $tax_rate_order ) ? $tax_rate_order : 0;

		// Loop posted fields.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		foreach ( $posted_countries as $key => $value ) {
			$mode     = ( 0 === strpos( $key, 'new-' ) ) ? 'insert' : 'update';
			$tax_rate = $this->get_posted_tax_rate( $key, $index ++, $current_class );

			if ( 'insert' === $mode ) {
				$tax_rate_id = WC_Tax::_insert_tax_rate( $tax_rate );
			} elseif ( isset( $_POST['remove_tax_rate'][ $key ] ) && 1 === absint( $_POST['remove_tax_rate'][ $key ] ) ) {
				$tax_rate_id = absint( $key );
				WC_Tax::_delete_tax_rate( $tax_rate_id );
				continue;
			} else {
				$tax_rate_id = absint( $key );
				WC_Tax::_update_tax_rate( $tax_rate_id, $tax_rate );
			}

			if ( isset( $_POST['tax_rate_postcode'][ $key ] ) ) {
				WC_Tax::_update_tax_rate_postcodes( $tax_rate_id, wc_clean( wp_unslash( $_POST['tax_rate_postcode'][ $key ] ) ) );
			}
			if ( isset( $_POST['tax_rate_city'][ $key ] ) ) {
				WC_Tax::_update_tax_rate_cities( $tax_rate_id, wc_clean( wp_unslash( $_POST['tax_rate_city'][ $key ] ) ) );
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}
}

return new WC_Settings_Tax();
