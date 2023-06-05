/**
 * External dependencies
 */
import { getPaymentMethods } from '@woocommerce/blocks-registry';

/**
 * Internal dependencies
 */
import type { SavedPaymentMethods } from '../types';

/**
 * Gets the payment methods saved for the current user after filtering out disabled ones.
 */
export const filterActiveSavedPaymentMethods = (
	availablePaymentMethods: string[] = [],
	savedPaymentMethods: SavedPaymentMethods
): SavedPaymentMethods => {
	if ( availablePaymentMethods.length === 0 ) {
		return {};
	}
	const registeredPaymentMethods = getPaymentMethods();
	const availablePaymentMethodsWithConfig = Object.fromEntries(
		availablePaymentMethods.map( ( name ) => [
			name,
			registeredPaymentMethods[ name ],
		] )
	);

	const paymentMethodKeys = Object.keys( savedPaymentMethods );
	const activeSavedPaymentMethods = {} as SavedPaymentMethods;
	paymentMethodKeys.forEach( ( type ) => {
		const methods = savedPaymentMethods[ type ].filter(
			( {
				method: { gateway },
			}: {
				method: {
					gateway: string;
				};
			} ) =>
				gateway in availablePaymentMethodsWithConfig &&
				availablePaymentMethodsWithConfig[ gateway ].supports
					?.showSavedCards
		);
		if ( methods.length ) {
			activeSavedPaymentMethods[ type ] = methods;
		}
	} );
	return activeSavedPaymentMethods;
};
