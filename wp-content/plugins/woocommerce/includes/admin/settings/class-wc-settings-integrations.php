<?php
/**
 * WooCommerce Integration Settings
 *
 * @package     WooCommerce\Admin
 * @version     2.1.0
 */

use Automattic\Jetpack\Constants;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Settings_Integrations', false ) ) :

	/**
	 * WC_Settings_Integrations.
	 */
	class WC_Settings_Integrations extends WC_Settings_Page {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id    = 'integration';
			$this->label = __( 'Integration', 'woocommerce' );

			if ( isset( WC()->integrations ) && WC()->integrations->get_integrations() ) {
				parent::__construct();
			}
		}

		/**
		 * Get own sections.
		 *
		 * @return array
		 */
		protected function get_own_sections() {
			global $current_section;

			$sections = array();

			if ( ! $this->wc_is_installing() ) {
				$integrations = $this->get_integrations();

				if ( ! $current_section && ! empty( $integrations ) ) {
					$current_section = current( $integrations )->id;
				}

				if ( count( $integrations ) > 1 ) {
					foreach ( $integrations as $integration ) {
						$title                                      = empty( $integration->method_title ) ? ucfirst( $integration->id ) : $integration->method_title;
						$sections[ strtolower( $integration->id ) ] = esc_html( $title );
					}
				}
			}

			return $sections;
		}

		/**
		 * Is WC_INSTALLING constant defined?
		 * This method exists to ease unit testing.
		 *
		 * @return bool True is the WC_INSTALLING constant is defined.
		 */
		protected function wc_is_installing() {
			return Constants::is_defined( 'WC_INSTALLING' );
		}

		/**
		 * Get the currently available integrations.
		 * This method exists to ease unit testing.
		 *
		 * @return array Currently available integrations.
		 */
		protected function get_integrations() {
			return WC()->integrations->get_integrations();
		}

		/**
		 * Output the settings.
		 */
		public function output() {
			global $current_section;

			$integrations = $this->get_integrations();

			if ( isset( $integrations[ $current_section ] ) ) {
				$integrations[ $current_section ]->admin_options();
			}
		}
	}

endif;

return new WC_Settings_Integrations();
