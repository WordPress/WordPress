<?php
/**
 * Email verification page.
 *
 * This displays instead of the thankyou page any time that the customer cannot be identified.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou-verify-email.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer) will need to copy
 * the new files to your theme to maintain compatibility. We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.9.0
 *
 * @var bool   $failed_submission Indicates if the last attempt to verify failed.
 * @var string $verify_url        The URL for the email verification form.
 */

defined( 'ABSPATH' ) || exit;
?>
<form name="checkout" method="post" class="woocommerce-form woocommerce-verify-email" action="<?php echo esc_url( $verify_url ); ?>" enctype="multipart/form-data">

	<?php
	wp_nonce_field( 'wc_verify_email', 'check_submission' );

	if ( $failed_submission ) {
		wc_print_notice( esc_html__( 'We were unable to verify the email address you provided. Please try again.', 'woocommerce' ), 'error' );
	}
	?>
	<p>
		<?php
		printf(
			/* translators: 1: opening login link 2: closing login link */
			esc_html__( 'To view this page, you must either %1$slogin%2$s or verify the email address associated with the order.', 'woocommerce' ),
			'<a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">',
			'</a>'
		);
		?>
	</p>

	<p class="form-row">
		<label for="email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="email" class="input-text" name="email" id="email" autocomplete="email" />
	</p>

	<p class="form-row">
		<button type="submit" class="woocommerce-button button <?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ); ?>" name="verify" value="1">
			<?php esc_html_e( 'Verify', 'woocommerce' ); ?>
		</button>
	</p>
</form>
