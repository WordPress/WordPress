<?php
/**
 * Handles product form field related methods.
 */

namespace Automattic\WooCommerce\Internal\Admin\ProductForm;

/**
 * Field class.
 */
class Field extends Component {

	/**
	 * Constructor
	 *
	 * @param string $id Field id.
	 * @param string $plugin_id Plugin id.
	 * @param array  $additional_args Array containing the necessary arguments.
	 *     $args = array(
	 *       'type'            => (string) Field type. Required.
	 *       'section'         => (string) Field location. Required.
	 *       'order'           => (int) Field order.
	 *       'properties'      => (array) Field properties.
	 *     ).
	 * @throws \Exception If there are missing arguments.
	 */
	public function __construct( $id, $plugin_id, $additional_args ) {
		parent::__construct( $id, $plugin_id, $additional_args );
		$this->required_arguments = array(
			'type',
			'section',
			'properties.name',
			'properties.label',
		);

		$missing_arguments = self::get_missing_arguments( $additional_args );
		if ( count( $missing_arguments ) > 0 ) {
			throw new \Exception(
				sprintf(
				/* translators: 1: Missing arguments list. */
					esc_html__( 'You are missing required arguments of WooCommerce ProductForm Field: %1$s', 'woocommerce' ),
					join( ', ', $missing_arguments )
				)
			);
		}
	}
}
