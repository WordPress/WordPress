/**
 * External dependencies
 */
import { dispatch, select } from '@wordpress/data';
import { debounce } from '@woocommerce/base-utils';

/**
 * Internal dependencies
 */
import { STORE_KEY as PAYMENT_STORE_KEY } from '../payment/constants';
import { STORE_KEY } from './constants';

/**
 * This function is used to update payment methods when the cart changes, or on first load.
 *
 * @return {boolean} True if the __internalUpdateAvailablePaymentMethods action was dispatched, false if not.
 */
export const updatePaymentMethods = async () => {
	const isInitialized =
		select( STORE_KEY ).hasFinishedResolution( 'getCartData' );
	if ( ! isInitialized ) {
		return false;
	}
	await dispatch(
		PAYMENT_STORE_KEY
	).__internalUpdateAvailablePaymentMethods();
	return true;
};

// We debounce this because it's possible for multiple cart updates to happen in quick succession, we don't want to run
// each payment method's canMakePayment function on every single change.
export const debouncedUpdatePaymentMethods = debounce(
	updatePaymentMethods,
	1000
);
