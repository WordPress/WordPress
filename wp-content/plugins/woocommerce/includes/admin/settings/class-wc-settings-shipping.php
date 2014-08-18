<?php
/**
 * WooCommerce Shipping Settings
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Settings_Shipping' ) ) :

/**
 * WC_Settings_Shipping
 */
class WC_Settings_Shipping extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'shipping';
		$this->label = __( 'Shipping', 'woocommerce' );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_admin_field_shipping_methods', array( $this, 'shipping_methods_setting' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get sections
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			''         => __( 'Shipping Options', 'woocommerce' )
		);

		// Load shipping methods so we can show any global options they may have
		$shipping_methods = WC()->shipping->load_shipping_methods();

		foreach ( $shipping_methods as $method ) {

			if ( ! $method->has_settings() ) continue;

			$title = empty( $method->method_title ) ? ucfirst( $method->id ) : $method->method_title;

			$sections[ strtolower( get_class( $method ) ) ] = esc_html( $title );
		}

		return $sections;
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {
		return apply_filters('woocommerce_shipping_settings', array(

			array( 'title' => __( 'Shipping Options', 'woocommerce' ), 'type' => 'title', 'id' => 'shipping_options' ),

			array(
				'title' 		=> __( 'Shipping Calculations', 'woocommerce' ),
				'desc' 		=> __( 'Enable shipping', 'woocommerce' ),
				'id' 		=> 'woocommerce_calc_shipping',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'		=> 'start'
			),

			array(
				'desc' 		=> __( 'Enable the shipping calculator on the cart page', 'woocommerce' ),
				'id' 		=> 'woocommerce_enable_shipping_calc',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'		=> '',
				'autoload'      => false
			),

			array(
				'desc' 		=> __( 'Hide shipping costs until an address is entered', 'woocommerce' ),
				'id' 		=> 'woocommerce_shipping_cost_requires_address',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
				'checkboxgroup'		=> 'end',
				'autoload'      => false
			),

			array(
				'title' 	=> __( 'Shipping Display Mode', 'woocommerce' ),
				'desc' 		=> __( 'This controls how multiple shipping methods are displayed on the frontend.', 'woocommerce' ),
				'id' 		=> 'woocommerce_shipping_method_format',
				'default'	=> '',
				'type' 		=> 'radio',
				'options' => array(
					''  			=> __( 'Display shipping methods with "radio" buttons', 'woocommerce' ),
					'select'		=> __( 'Display shipping methods in a dropdown', 'woocommerce' ),
				),
				'desc_tip'	=>  true,
				'autoload'      => false
			),

			array(
				'title'           => __( 'Shipping Destination', 'woocommerce' ),
				'desc'            => __( 'Ship to billing address by default', 'woocommerce' ),
				'id'              => 'woocommerce_ship_to_billing',
				'default'         => 'yes',
				'type'            => 'checkbox',
				'checkboxgroup'   => 'start',
				'autoload'        => false,
				'show_if_checked' => 'option',
			),

			array(
				'desc'            => __( 'Only ship to the users billing address', 'woocommerce' ),
				'id'              => 'woocommerce_ship_to_billing_address_only',
				'default'         => 'no',
				'type'            => 'checkbox',
				'checkboxgroup'   => 'end',
				'autoload'        => false,
				'show_if_checked' => 'yes',
			),

			array(
				'title' => __( 'Restrict shipping to Location(s)', 'woocommerce' ),
				'desc' 		=> sprintf( __( 'Choose which countries you want to ship to, or choose to ship to all <a href="%s">locations you sell to</a>.', 'woocommerce' ), admin_url( 'admin.php?page=wc-settings&tab=general' ) ),
				'id' 		=> 'woocommerce_ship_to_countries',
				'default'	=> '',
				'type' 		=> 'select',
				'class'		=> 'chosen_select',
				'desc_tip'	=> false,
				'options' => array(
					''         => __( 'Ship to all countries you sell to', 'woocommerce' ),
					'all'      => __( 'Ship to all countries', 'woocommerce' ),
					'specific' => __( 'Ship to specific countries only', 'woocommerce' )
				)
			),

			array(
				'title' => __( 'Specific Countries', 'woocommerce' ),
				'desc' 		=> '',
				'id' 		=> 'woocommerce_specific_ship_to_countries',
				'css' 		=> '',
				'default'	=> '',
				'type' 		=> 'multi_select_countries'
			),

			array(
				'type' 		=> 'shipping_methods',
			),

			array( 'type' => 'sectionend', 'id' => 'shipping_options' ),

		)); // End shipping settings
	}

	/**
	 * Output the settings
	 */
	public function output() {
		global $current_section;

		// Load shipping methods so we can show any global options they may have
		$shipping_methods = WC()->shipping->load_shipping_methods();

		if ( $current_section ) {
 			foreach ( $shipping_methods as $method ) {
				if ( strtolower( get_class( $method ) ) == strtolower( $current_section ) && $method->has_settings() ) {
					$method->admin_options();
					break;
				}
			}
 		} else {
			$settings = $this->get_settings();

			WC_Admin_Settings::output_fields( $settings );
		}
	}

	/**
	 * Output shipping method settings.
	 *
	 * @access public
	 * @return void
	 */
	public function shipping_methods_setting() {
		$default_shipping_method = esc_attr( get_option('woocommerce_default_shipping_method') );
		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php _e( 'Shipping Methods', 'woocommerce' ) ?></th>
		    <td class="forminp">
				<table class="wc_shipping widefat" cellspacing="0">
					<thead>
						<tr>
							<th class="default"><?php _e( 'Default', 'woocommerce' ); ?></th>
							<th class="name"><?php _e( 'Name', 'woocommerce' ); ?></th>
							<th class="id"><?php _e( 'ID', 'woocommerce' ); ?></th>
							<th class="status"><?php _e( 'Status', 'woocommerce' ); ?></th>
							<th class="settings">&nbsp;</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th width="1%" class="default">
								<input type="radio" name="default_shipping_method" value="" <?php checked( $default_shipping_method, '' ); ?> />
							</th>
							<th><?php _e( 'Automatic', 'woocommerce' ); ?> <a class="tips" data-tip="<?php _e( 'The cheapest available shipping method will be selected by default.', 'woocommerce' ); ?>">[?]</a></th>
							<th colspan="3"><span class="description"><?php _e( 'Drag and drop the above shipping methods to control their display order.', 'woocommerce' ); ?></span></th>
						</tr>
					</tfoot>
					<tbody>
				    	<?php
				    	foreach ( WC()->shipping->load_shipping_methods() as $key => $method ) {
					    	echo '<tr>
					    		<td width="1%" class="default">
					    			<input type="radio" name="default_shipping_method" value="' . esc_attr( $method->id ) . '" ' . checked( $default_shipping_method, $method->id, false ) . ' />
					    			<input type="hidden" name="method_order[]" value="' . esc_attr( $method->id ) . '" />
					    		</td>
				    			<td class="name">
				    				' . $method->get_title() . '
				    			</td>
				    			<td class="id">
				    				' . $method->id . '
				    			</td>
				    			<td class="status">';

				    		if ( $method->enabled == 'yes' )
						        echo '<span class="status-enabled tips" data-tip="' . __ ( 'Enabled', 'woocommerce' ) . '">' . __ ( 'Enabled', 'woocommerce' ) . '</span>';
						   	else
						   		echo '-';

				    		echo '</td>
				    			<td class="settings">';

				    		if ( $method->has_settings ) {
				    			echo '<a class="button" href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=' . strtolower( get_class( $method ) ) ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
				    		}

				    		echo '</td>
				    		</tr>';
				    	}
				    	?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save settings
	 */
	public function save() {
		global $current_section;

		if ( ! $current_section ) {

			$settings = $this->get_settings();

			WC_Admin_Settings::save_fields( $settings );
			WC()->shipping->process_admin_options();

		} elseif ( class_exists( $current_section ) ) {

			$current_section_class = new $current_section();

			do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section_class->id );
		}
	}
}

endif;

return new WC_Settings_Shipping();
