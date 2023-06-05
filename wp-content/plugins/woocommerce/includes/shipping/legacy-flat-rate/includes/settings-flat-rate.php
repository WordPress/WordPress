<?php
/**
 * Legacy flat rate settings.
 *
 * @package WooCommerce\Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cost_desc = __( 'Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.', 'woocommerce' ) . '<br/>' . __( 'Supports the following placeholders: <code>[qty]</code> = number of items, <code>[cost]</code> = cost of items, <code>[fee percent="10" min_fee="20"]</code> = Percentage based fee.', 'woocommerce' );

/**
 * Settings for flat rate shipping.
 */
$settings = array(
	'enabled'      => array(
		'title'   => __( 'Enable/Disable', 'woocommerce' ),
		'type'    => 'checkbox',
		'label'   => __( 'Once disabled, this legacy method will no longer be available.', 'woocommerce' ),
		'default' => 'no',
	),
	'title'        => array(
		'title'       => __( 'Method title', 'woocommerce' ),
		'type'        => 'text',
		'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
		'default'     => __( 'Flat rate', 'woocommerce' ),
		'desc_tip'    => true,
	),
	'availability' => array(
		'title'   => __( 'Availability', 'woocommerce' ),
		'type'    => 'select',
		'default' => 'all',
		'class'   => 'availability wc-enhanced-select',
		'options' => array(
			'all'      => __( 'All allowed countries', 'woocommerce' ),
			'specific' => __( 'Specific Countries', 'woocommerce' ),
		),
	),
	'countries'    => array(
		'title'             => __( 'Specific countries', 'woocommerce' ),
		'type'              => 'multiselect',
		'class'             => 'wc-enhanced-select',
		'css'               => 'width: 400px;',
		'default'           => '',
		'options'           => WC()->countries->get_shipping_countries(),
		'custom_attributes' => array(
			'data-placeholder' => __( 'Select some countries', 'woocommerce' ),
		),
	),
	'tax_status'   => array(
		'title'   => __( 'Tax status', 'woocommerce' ),
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'default' => 'taxable',
		'options' => array(
			'taxable' => __( 'Taxable', 'woocommerce' ),
			'none'    => _x( 'None', 'Tax status', 'woocommerce' ),
		),
	),
	'cost'         => array(
		'title'       => __( 'Cost', 'woocommerce' ),
		'type'        => 'text',
		'placeholder' => '',
		'description' => $cost_desc,
		'default'     => '',
		'desc_tip'    => true,
	),
);

$shipping_classes = WC()->shipping()->get_shipping_classes();

if ( ! empty( $shipping_classes ) ) {
	$settings['class_costs'] = array(
		'title'       => __( 'Shipping class costs', 'woocommerce' ),
		'type'        => 'title',
		'default'     => '',
		/* translators: %s: Admin shipping settings URL */
		'description' => sprintf( __( 'These costs can optionally be added based on the <a href="%s">product shipping class</a>.', 'woocommerce' ), admin_url( 'admin.php?page=wc-settings&tab=shipping&section=classes' ) ),
	);
	foreach ( $shipping_classes as $shipping_class ) {
		if ( ! isset( $shipping_class->term_id ) ) {
			continue;
		}
		$settings[ 'class_cost_' . $shipping_class->term_id ] = array(
			/* translators: %s: shipping class name */
			'title'       => sprintf( __( '"%s" shipping class cost', 'woocommerce' ), esc_html( $shipping_class->name ) ),
			'type'        => 'text',
			'placeholder' => __( 'N/A', 'woocommerce' ),
			'description' => $cost_desc,
			'default'     => $this->get_option( 'class_cost_' . $shipping_class->slug ), // Before 2.5.0, we used slug here which caused issues with long setting names.
			'desc_tip'    => true,
		);
	}
	$settings['no_class_cost'] = array(
		'title'       => __( 'No shipping class cost', 'woocommerce' ),
		'type'        => 'text',
		'placeholder' => __( 'N/A', 'woocommerce' ),
		'description' => $cost_desc,
		'default'     => '',
		'desc_tip'    => true,
	);
	$settings['type']          = array(
		'title'   => __( 'Calculation type', 'woocommerce' ),
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'default' => 'class',
		'options' => array(
			'class' => __( 'Per class: Charge shipping for each shipping class individually', 'woocommerce' ),
			'order' => __( 'Per order: Charge shipping for the most expensive shipping class', 'woocommerce' ),
		),
	);
}

if ( apply_filters( 'woocommerce_enable_deprecated_additional_flat_rates', $this->get_option( 'options', false ) ) ) {
	$settings['additional_rates'] = array(
		'title'       => __( 'Additional rates', 'woocommerce' ),
		'type'        => 'title',
		'default'     => '',
		'description' => __( 'These rates are extra shipping options with additional costs (based on the flat rate).', 'woocommerce' ),
	);
	$settings['options']          = array(
		'title'       => __( 'Additional rates', 'woocommerce' ),
		'type'        => 'textarea',
		'description' => __( 'One per line: Option name | Additional cost [+- Percents] | Per cost type (order, class, or item) Example: <code>Priority mail | 6.95 [+ 0.2%] | order</code>.', 'woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Option name | Additional cost [+- Percents%] | Per cost type (order, class, or item)', 'woocommerce' ),
	);
}

return $settings;
