/**
 * External dependencies
 */
import { select, dispatch } from '@wordpress/data';
import { PlainPaymentMethods } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { STORE_KEY as PAYMENT_STORE_KEY } from '../constants';

export const setDefaultPaymentMethod = async (
	paymentMethods: PlainPaymentMethods
) => {
	const paymentMethodKeys = Object.keys( paymentMethods );

	const expressPaymentMethodKeys = Object.keys(
		select( PAYMENT_STORE_KEY ).getAvailableExpressPaymentMethods()
	);

	const allPaymentMethodKeys = [
		...paymentMethodKeys,
		...expressPaymentMethodKeys,
	];

	const savedPaymentMethods =
		select( PAYMENT_STORE_KEY ).getSavedPaymentMethods();

	const savedPaymentMethod =
		Object.keys( savedPaymentMethods ).flatMap(
			( type ) => savedPaymentMethods[ type ]
		)[ 0 ] || undefined;

	if ( savedPaymentMethod ) {
		const token = savedPaymentMethod.tokenId.toString();
		const paymentMethodSlug = savedPaymentMethod.method.gateway;

		const savedTokenKey = `wc-${ paymentMethodSlug }-payment-token`;

		dispatch( PAYMENT_STORE_KEY ).__internalSetActivePaymentMethod(
			paymentMethodSlug,
			{
				token,
				payment_method: paymentMethodSlug,
				[ savedTokenKey ]: token,
				isSavedToken: true,
			}
		);
		return;
	}

	const activePaymentMethod =
		select( PAYMENT_STORE_KEY ).getActivePaymentMethod();

	// Return if current method is valid.
	if (
		activePaymentMethod &&
		allPaymentMethodKeys.includes( activePaymentMethod )
	) {
		return;
	}

	dispatch( PAYMENT_STORE_KEY ).__internalSetPaymentIdle();

	dispatch( PAYMENT_STORE_KEY ).__internalSetActivePaymentMethod(
		paymentMethodKeys[ 0 ]
	);
};
