/**
 * External dependencies
 */
import { CHECKOUT_STORE_KEY, PAYMENT_STORE_KEY } from '@woocommerce/block-data';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { useCheckoutEventsContext } from '../providers';
import { usePaymentMethods } from './payment-methods/use-payment-methods';

/**
 * Returns the submitButtonText, onSubmit interface from the checkout context,
 * and an indication of submission status.
 */
export const useCheckoutSubmit = () => {
	const {
		isCalculating,
		isBeforeProcessing,
		isProcessing,
		isAfterProcessing,
		isComplete,
		hasError,
	} = useSelect( ( select ) => {
		const store = select( CHECKOUT_STORE_KEY );
		return {
			isCalculating: store.isCalculating(),
			isBeforeProcessing: store.isBeforeProcessing(),
			isProcessing: store.isProcessing(),
			isAfterProcessing: store.isAfterProcessing(),
			isComplete: store.isComplete(),
			hasError: store.hasError(),
		};
	} );
	const { activePaymentMethod, isExpressPaymentMethodActive } = useSelect(
		( select ) => {
			const store = select( PAYMENT_STORE_KEY );

			return {
				activePaymentMethod: store.getActivePaymentMethod(),
				isExpressPaymentMethodActive:
					store.isExpressPaymentMethodActive(),
			};
		}
	);

	const { onSubmit } = useCheckoutEventsContext();

	const { paymentMethods = {} } = usePaymentMethods();
	const paymentMethod = paymentMethods[ activePaymentMethod ] || {};
	const waitingForProcessing =
		isProcessing || isAfterProcessing || isBeforeProcessing;
	const waitingForRedirect = isComplete && ! hasError;
	const paymentMethodButtonLabel = paymentMethod.placeOrderButtonLabel;

	return {
		paymentMethodButtonLabel,
		onSubmit,
		isCalculating,
		isDisabled: isProcessing || isExpressPaymentMethodActive,
		waitingForProcessing,
		waitingForRedirect,
	};
};
