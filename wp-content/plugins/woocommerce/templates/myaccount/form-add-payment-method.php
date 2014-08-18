<?php
/**
 * Add payment method form form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<form id="add_payment_method" method="post">
	<div id="payment">
		<ul class="payment_methods methods">
			<?php
				if ( $available_gateways = WC()->payment_gateways->get_available_payment_gateways() ) {
					// Chosen Method
					if ( sizeof( $available_gateways ) )
						current( $available_gateways )->set_current();

					foreach ( $available_gateways as $gateway ) {
						?>
						<li class="payment_method_<?php echo $gateway->id; ?>">
							<input id="payment_method_<?php echo $gateway->id; ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> />
							<label for="payment_method_<?php echo $gateway->id; ?>"><?php echo $gateway->get_title(); ?> <?php echo $gateway->get_icon(); ?></label>
							<?php
								if ( $gateway->has_fields() || $gateway->get_description() ) {
									echo '<div class="payment_box payment_method_' . $gateway->id . '" style="display:none;">';
									$gateway->payment_fields();
									echo '</div>';
								}
							?>
						</li>
						<?php
					}
				} else {

					echo '<p>' . __( 'Sorry, it seems that there are no payment methods which support adding a new payment method. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) . '</p>';

				}
			?>
		</ul>

		<div class="form-row">
			<?php wp_nonce_field( 'woocommerce-add-payment-method' ); ?>
			<input type="submit" class="button alt" id="place_order" value="<?php _e( 'Add Payment Method', 'woocommerce' ); ?>" />
			<input type="hidden" name="woocommerce_add_payment_method" value="1" />
		</div>

	</div>

</form>
