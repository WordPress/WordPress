<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Bank Transfer Payment Gateway
 *
 * Provides a Bank Transfer Payment Gateway. Based on code by Mike Pepper.
 *
 * @class 		WC_Gateway_BACS
 * @extends		WC_Payment_Gateway
 * @version		2.1.0
 * @package		WooCommerce/Classes/Payment
 * @author 		WooThemes
 */
class WC_Gateway_BACS extends WC_Payment_Gateway {

    /**
     * Constructor for the gateway.
     */
    public function __construct() {
		$this->id                 = 'bacs';
		$this->icon               = apply_filters('woocommerce_bacs_icon', '');
		$this->has_fields         = false;
		$this->method_title       = __( 'BACS', 'woocommerce' );
		$this->method_description = __( 'Allows payments by BACS, more commonly known as direct bank/wire transfer.', 'woocommerce' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

        // Define user set variables
		$this->title        = $this->get_option( 'title' );
		$this->description  = $this->get_option( 'description' );
		$this->instructions = $this->get_option( 'instructions', $this->description );

		// BACS account fields shown on the thanks page and in emails
		$this->account_details = get_option( 'woocommerce_bacs_accounts',
			array(
				array(
					'account_name'   => $this->get_option( 'account_name' ),
					'account_number' => $this->get_option( 'account_number' ),
					'sort_code'      => $this->get_option( 'sort_code' ),
					'bank_name'      => $this->get_option( 'bank_name' ),
					'iban'           => $this->get_option( 'iban' ),
					'bic'            => $this->get_option( 'bic' )
				)
			)
		);

		// Actions
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'save_account_details' ) );
    	add_action( 'woocommerce_thankyou_bacs', array( $this, 'thankyou_page' ) );

    	// Customer Emails
    	add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
    }

    /**
     * Initialise Gateway Settings Form Fields
     */
    public function init_form_fields() {
    	$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Bank Transfer', 'woocommerce' ),
				'default' => 'yes'
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default'     => __( 'Direct Bank Transfer', 'woocommerce' ),
				'desc_tip'    => true,
			),
			'description' => array(
				'title'       => __( 'Description', 'woocommerce' ),
				'type'        => 'textarea',
				'description' => __( 'Payment method description that the customer will see on your checkout.', 'woocommerce' ),
				'default'     => __( 'Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order won\'t be shipped until the funds have cleared in our account.', 'woocommerce' ),
				'desc_tip'    => true,
			),
			'instructions' => array(
				'title'       => __( 'Instructions', 'woocommerce' ),
				'type'        => 'textarea',
				'description' => __( 'Instructions that will be added to the thank you page and emails.', 'woocommerce' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'account_details' => array(
				'type'        => 'account_details'
			),
		);
    }

    /**
     * generate_account_details_html function.
     */
    public function generate_account_details_html() {
    	ob_start();
	    ?>
	    <tr valign="top">
            <th scope="row" class="titledesc"><?php _e( 'Account Details', 'woocommerce' ); ?>:</th>
            <td class="forminp" id="bacs_accounts">
			    <table class="widefat wc_input_table sortable" cellspacing="0">
		    		<thead>
		    			<tr>
		    				<th class="sort">&nbsp;</th>
		    				<th><?php _e( 'Account Name', 'woocommerce' ); ?></th>
			            	<th><?php _e( 'Account Number', 'woocommerce' ); ?></th>
			            	<th><?php _e( 'Bank Name', 'woocommerce' ); ?></th>
			            	<th><?php _e( 'Sort Code', 'woocommerce' ); ?></th>
			            	<th><?php _e( 'IBAN', 'woocommerce' ); ?></th>
			            	<th><?php _e( 'BIC / Swift', 'woocommerce' ); ?></th>
		    			</tr>
		    		</thead>
		    		<tfoot>
		    			<tr>
		    				<th colspan="7"><a href="#" class="add button"><?php _e( '+ Add Account', 'woocommerce' ); ?></a> <a href="#" class="remove_rows button"><?php _e( 'Remove selected account(s)', 'woocommerce' ); ?></a></th>
		    			</tr>
		    		</tfoot>
		    		<tbody class="accounts">
		            	<?php
		            	$i = -1;
		            	if ( $this->account_details ) {
		            		foreach ( $this->account_details as $account ) {
		                		$i++;

		                		echo '<tr class="account">
		                			<td class="sort"></td>
		                			<td><input type="text" value="' . esc_attr( $account['account_name'] ) . '" name="bacs_account_name[' . $i . ']" /></td>
		                			<td><input type="text" value="' . esc_attr( $account['account_number'] ) . '" name="bacs_account_number[' . $i . ']" /></td>
		                			<td><input type="text" value="' . esc_attr( $account['bank_name'] ) . '" name="bacs_bank_name[' . $i . ']" /></td>
		                			<td><input type="text" value="' . esc_attr( $account['sort_code'] ) . '" name="bacs_sort_code[' . $i . ']" /></td>
		                			<td><input type="text" value="' . esc_attr( $account['iban'] ) . '" name="bacs_iban[' . $i . ']" /></td>
		                			<td><input type="text" value="' . esc_attr( $account['bic'] ) . '" name="bacs_bic[' . $i . ']" /></td>
			                    </tr>';
		            		}
		            	}
		            	?>
		        	</tbody>
		        </table>
		       	<script type="text/javascript">
					jQuery(function() {
						jQuery('#bacs_accounts').on( 'click', 'a.add', function(){

							var size = jQuery('#bacs_accounts tbody .account').size();

							jQuery('<tr class="account">\
		                			<td class="sort"></td>\
		                			<td><input type="text" name="bacs_account_name[' + size + ']" /></td>\
		                			<td><input type="text" name="bacs_account_number[' + size + ']" /></td>\
		                			<td><input type="text" name="bacs_bank_name[' + size + ']" /></td>\
		                			<td><input type="text" name="bacs_sort_code[' + size + ']" /></td>\
		                			<td><input type="text" name="bacs_iban[' + size + ']" /></td>\
		                			<td><input type="text" name="bacs_bic[' + size + ']" /></td>\
			                    </tr>').appendTo('#bacs_accounts table tbody');

							return false;
						});
					});
				</script>
            </td>
	    </tr>
        <?php
        return ob_get_clean();
    }

    /**
     * Save account details table
     */
    public function save_account_details() {
    	$accounts = array();

    	if ( isset( $_POST['bacs_account_name'] ) ) {

			$account_names   = array_map( 'wc_clean', $_POST['bacs_account_name'] );
			$account_numbers = array_map( 'wc_clean', $_POST['bacs_account_number'] );
			$bank_names      = array_map( 'wc_clean', $_POST['bacs_bank_name'] );
			$sort_codes      = array_map( 'wc_clean', $_POST['bacs_sort_code'] );
			$ibans           = array_map( 'wc_clean', $_POST['bacs_iban'] );
			$bics            = array_map( 'wc_clean', $_POST['bacs_bic'] );

			foreach ( $account_names as $i => $name ) {
				if ( ! isset( $account_names[ $i ] ) ) {
					continue;
				}

	    		$accounts[] = array(
	    			'account_name'   => $account_names[ $i ],
					'account_number' => $account_numbers[ $i ],
					'bank_name'      => $bank_names[ $i ],
					'sort_code'      => $sort_codes[ $i ],
					'iban'           => $ibans[ $i ],
					'bic'            => $bics[ $i ]
	    		);
	    	}
    	}

    	update_option( 'woocommerce_bacs_accounts', $accounts );
    }

    /**
     * Output for the order received page.
     */
    public function thankyou_page( $order_id ) {
		if ( $this->instructions ) {
        	echo wpautop( wptexturize( wp_kses_post( $this->instructions ) ) );
        }
        $this->bank_details( $order_id );
    }

    /**
     * Add content to the WC emails.
     *
     * @access public
     * @param WC_Order $order
     * @param bool $sent_to_admin
     * @param bool $plain_text
     * @return void
     */
    public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
    	if ( ! $sent_to_admin && 'bacs' === $order->payment_method && 'on-hold' === $order->status ) {
			if ( $this->instructions ) {
				echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
			}
			$this->bank_details( $order->id );
		}
    }

    /**
     * Get bank details and place into a list format
     */
    private function bank_details( $order_id = '' ) {
    	if ( empty( $this->account_details ) ) {
    		return;
    	}

    	echo '<h2>' . __( 'Our Bank Details', 'woocommerce' ) . '</h2>' . PHP_EOL;

    	$bacs_accounts = apply_filters( 'woocommerce_bacs_accounts', $this->account_details );

    	if ( ! empty( $bacs_accounts ) ) {
	    	foreach ( $bacs_accounts as $bacs_account ) {
	    		echo '<ul class="order_details bacs_details">' . PHP_EOL;

	    		$bacs_account = (object) $bacs_account;

	    		// BACS account fields shown on the thanks page and in emails
				$account_fields = apply_filters( 'woocommerce_bacs_account_fields', array(
					'account_number'=> array(
						'label' => __( 'Account Number', 'woocommerce' ),
						'value' => $bacs_account->account_number
					),
					'sort_code'		=> array(
						'label' => __( 'Sort Code', 'woocommerce' ),
						'value' => $bacs_account->sort_code
					),
					'iban'			=> array(
						'label' => __( 'IBAN', 'woocommerce' ),
						'value' => $bacs_account->iban
					),
					'bic'			=> array(
						'label' => __( 'BIC', 'woocommerce' ),
						'value' => $bacs_account->bic
					)
				), $order_id );

				if ( $bacs_account->account_name || $bacs_account->bank_name ) {
					echo '<h3>' . implode( ' - ', array_filter( array( $bacs_account->account_name, $bacs_account->bank_name ) ) ) . '</h3>' . PHP_EOL;
				}

	    		foreach ( $account_fields as $field_key => $field ) {
				    if ( ! empty( $field['value'] ) ) {
				    	echo '<li class="' . esc_attr( $field_key ) . '">' . esc_attr( $field['label'] ) . ': <strong>' . wptexturize( $field['value'] ) . '</strong></li>' . PHP_EOL;
				    }
				}

	    		echo '</ul>';
	    	}
	    }
    }

    /**
     * Process the payment and return the result
     *
     * @param int $order_id
     * @return array
     */
    public function process_payment( $order_id ) {

		$order = new WC_Order( $order_id );

		// Mark as on-hold (we're awaiting the payment)
		$order->update_status( 'on-hold', __( 'Awaiting BACS payment', 'woocommerce' ) );

		// Reduce stock levels
		$order->reduce_order_stock();

		// Remove cart
		WC()->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result' 	=> 'success',
			'redirect'	=> $this->get_return_url( $order )
		);
    }
}
