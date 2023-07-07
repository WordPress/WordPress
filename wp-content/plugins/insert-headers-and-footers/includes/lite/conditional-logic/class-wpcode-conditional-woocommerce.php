<?php
/**
 * Class that handles conditional logic related to WooCommerce.
 *
 * @package WPCode
 */

/**
 * The WPCode_Conditional_WooCommerce class.
 */
class WPCode_Conditional_WooCommerce_Lite extends WPCode_Conditional_Type {

	/**
	 * The type unique name (slug).
	 *
	 * @var string
	 */
	public $name = 'woocommerce';

	/**
	 * Set the translatable label.
	 *
	 * @return void
	 */
	protected function set_label() {
		$this->label = 'WooCommerce (PRO)';
	}

	/**
	 * Set the type options for the admin mainly.
	 *
	 * @return void
	 */
	public function load_type_options() {
		$this->options = array(
			'wc_page' => array(
				'label'   => __( 'WooCommerce Page', 'insert-headers-and-footers' ),
				'type'    => 'select',
				'upgrade' => array(
					'title' => __( 'WooCommerce Page Rules is a Pro Feature', 'insert-headers-and-footers' ),
					'text'  => __( 'Get access to advanced conditional logic rules for WooCommerce by upgrading to PRO today.', 'insert-headers-and-footers' ),
					'link'  => wpcode_utm_url( 'https://wpcode.com/lite/', 'edit-snippet', 'conditional-logic', 'woocommerce-page' ),
				),
				'options' => array(
					array(
						'label'    => __( 'Checkout Page', 'insert-headers-and-footers' ),
						'value'    => '',
						'disabled' => true,
					),
					array(
						'label'    => __( 'Thank You Page', 'insert-headers-and-footers' ),
						'value'    => '',
						'disabled' => true,
					),
					array(
						'label'    => __( 'Cart Page', 'insert-headers-and-footers' ),
						'value'    => '',
						'disabled' => true,
					),
					array(
						'label'    => __( 'Single Product Page', 'insert-headers-and-footers' ),
						'value'    => '',
						'disabled' => true,
					),
					array(
						'label'    => __( 'Shop Page', 'insert-headers-and-footers' ),
						'value'    => '',
						'disabled' => true,
					),
					array(
						'label'    => __( 'Product Category Page', 'insert-headers-and-footers' ),
						'value'    => '',
						'disabled' => true,
					),
					array(
						'label'    => __( 'Product Tag Page', 'insert-headers-and-footers' ),
						'value'    => '',
						'disabled' => true,
					),
					array(
						'label'    => __( 'My Account Page', 'insert-headers-and-footers' ),
						'value'    => '',
						'disabled' => true,
					),
				),
			),
			'wc_cart' => array(
				'label'   => __( 'WooCommerce Cart', 'insert-headers-and-footers' ),
				'type'    => 'select',
				'options' => array(),
				'upgrade' => array(
					'title' => __( 'WooCommerce Cart Contents Rule is a Pro Feature', 'insert-headers-and-footers' ),
					'text'  => __( 'Get access to advanced conditional logic rules for WooCommerce by upgrading to PRO today.', 'insert-headers-and-footers' ),
					'link'  => wpcode_utm_url( 'https://wpcode.com/lite/', 'edit-snippet', 'conditional-logic', 'woocommerce-cart' ),
				),
			),
		);
	}
}

new WPCode_Conditional_WooCommerce_Lite();
