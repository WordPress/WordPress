<?php
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}

/**
 * @var Yoast_Product $product
 */
$product = $this->product;

$this->show_license_form_heading();

if( $api_host_available['availability'] === false ){
	echo '<p style="color: red; max-width: 600px;"><strong>' . sprintf( __( 'We couldn\'t create a connection to our API to verify your license key(s). Please ask your hosting company to allow outgoing connections from your server to %s.', $product->get_text_domain() ), $api_host_available['url'] ) . '</strong></p>';
}

if( $api_host_available['curl_version'] !== false && version_compare( $api_host_available['curl_version'], '7.20.0', '<')){
	echo '<p style="color: red; max-width: 600px;"><strong>' . sprintf( __( 'Your server has an outdated version of the PHP module cURL (Version: %s). Please ask your hosting company to update this to a recent version of cURL. You can read more about that in our %sKnowledge base%s.', $product->get_text_domain() ), $api_host_available['curl_version'], '<a href="http://kb.yoast.com/article/90-is-my-curl-up-to-date" target="_blank">', '</a>' ) . '</strong></p>';
}

// Output form tags if we're not embedded in another form
if( ! $embedded ) {
	echo '<form method="post" action="">';
}

wp_nonce_field( $nonce_name, $nonce_name ); ?>
<table class="form-table yoast-license-form">
	<tbody>
		<tr valign="top">
			<th scope="row" valign="top"><?php _e( 'License status', $product->get_text_domain() ); ?></th>
			<td>
				<?php if( $this->license_is_valid() ) { ?>
				<span style="color: white; background: limegreen; padding:3px 6px;">ACTIVE</span> &nbsp; - &nbsp; you are receiving updates.
				<?php } else { ?>
				<span style="color:white; background: red; padding: 3px 6px;">INACTIVE</span> &nbsp; - &nbsp; you are <strong>not</strong> receiving updates.
				<?php } ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" valign="top"><?php _e('Toggle license status', $product->get_text_domain() ); ?></th>
			<td class="yoast-license-toggler">

				<?php if( $this->license_is_valid() ) { ?>
					<button name="<?php echo esc_attr( $action_name ); ?>" type="submit" class="button-secondary yoast-license-deactivate" value="deactivate"><?php echo esc_html_e( 'Deactivate License', $product->get_text_domain() ); ?></button> &nbsp;
					<small><?php _e( '(deactivate your license so you can activate it on another WordPress site)', $product->get_text_domain() ); ?></small>
				<?php } else {

					if( $this->get_license_key() !== '') { ?>
						<button name="<?php echo esc_attr( $action_name ); ?>" type="submit" class="button-secondary yoast-license-activate" value="activate" /><?php echo esc_html_e('Activate License', $product->get_text_domain() ); ?></button> &nbsp;
					<?php } else {
						_e( 'Please enter a license key in the field below first.', $product->get_text_domain() );
					}

				} ?>

			</td>
		</tr>
		<tr valign="top">
			<th scope="row" valign="top"><?php _e( 'License Key', $product->get_text_domain() ); ?></th>
			<td>
				<input name="<?php echo esc_attr( $key_name ); ?>" type="text" class="regular-text yoast-license-key-field <?php if( $obfuscate ) { ?>yoast-license-obfuscate<?php } ?>" value="<?php echo esc_attr( $visible_license_key ); ?>" placeholder="<?php echo esc_attr( sprintf( __( 'Paste your %s license key here..', $product->get_text_domain() ), $product->get_item_name() ) ); ?>" <?php if( $readonly ) { echo 'readonly="readonly"'; } ?> />
				<?php if( $this->license_constant_is_defined ) { ?>
				<p class="help"><?php printf( __( "You defined your license key using the %s PHP constant.", $product->get_text_domain() ), '<code>' . $this->license_constant_name . '</code>' ); ?></p>
				<?php } ?>
			</td>
		</tr>

	</tbody>
</table>

<?php

if( $this->license_is_valid() ) {

	$expiry_date = strtotime( $this->get_license_expiry_date() );

	if( $expiry_date !== false ) {
		echo '<p>';

		printf( __( 'Your %s license will expire on %s.', $product->get_text_domain() ), $product->get_item_name(), date('F jS Y', $expiry_date ) );

		if( strtotime( '+3 months' ) > $expiry_date ) {
			printf( ' ' . __('%sRenew your license now%s.', $product->get_text_domain() ), '<a href="'. $this->product->get_tracking_url( 'renewal' ) .'">', '</a>' );
		}

		echo '</p>';
	}
}

// Only show a "Save Changes" button and end form if we're not embedded in another form.
if( ! $embedded ) {

	// only show "Save Changes" button if license is not activated and not defined with a constant
	if( $readonly === false && $api_host_available['availability'] === true ) {
		submit_button();
	}

	echo '</form>';
}

$product = null;
