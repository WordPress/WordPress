/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { getSetting } from '@woocommerce/settings';
import { LOGIN_URL } from '@woocommerce/block-settings';
import { useSelect } from '@wordpress/data';
import { CHECKOUT_STORE_KEY } from '@woocommerce/block-data';

const LOGIN_TO_CHECKOUT_URL = `${ LOGIN_URL }?redirect_to=${ encodeURIComponent(
	window.location.href
) }`;

const LoginPrompt = () => {
	const customerId = useSelect( ( select ) =>
		select( CHECKOUT_STORE_KEY ).getCustomerId()
	);

	if ( ! getSetting( 'checkoutShowLoginReminder', true ) || customerId ) {
		return null;
	}

	return (
		<>
			{ __(
				'Already have an account? ',
				'woocommerce'
			) }
			<a href={ LOGIN_TO_CHECKOUT_URL }>
				{ __( 'Log in.', 'woocommerce' ) }
			</a>
		</>
	);
};

export default LoginPrompt;
