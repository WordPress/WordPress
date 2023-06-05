/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useExpressPaymentMethods } from '@woocommerce/base-context/hooks';
import { noticeContexts } from '@woocommerce/base-context';
import { StoreNoticesContainer } from '@woocommerce/blocks-checkout';
import LoadingMask from '@woocommerce/base-components/loading-mask';
import { useSelect } from '@wordpress/data';
import { CHECKOUT_STORE_KEY, PAYMENT_STORE_KEY } from '@woocommerce/block-data';

/**
 * Internal dependencies
 */
import ExpressPaymentMethods from '../express-payment-methods';
import './style.scss';

const CartExpressPayment = () => {
	const { paymentMethods, isInitialized } = useExpressPaymentMethods();
	const {
		isCalculating,
		isProcessing,
		isAfterProcessing,
		isBeforeProcessing,
		isComplete,
		hasError,
	} = useSelect( ( select ) => {
		const store = select( CHECKOUT_STORE_KEY );
		return {
			isCalculating: store.isCalculating(),
			isProcessing: store.isProcessing(),
			isAfterProcessing: store.isAfterProcessing(),
			isBeforeProcessing: store.isBeforeProcessing(),
			isComplete: store.isComplete(),
			hasError: store.hasError(),
		};
	} );
	const isExpressPaymentMethodActive = useSelect( ( select ) =>
		select( PAYMENT_STORE_KEY ).isExpressPaymentMethodActive()
	);

	if (
		! isInitialized ||
		( isInitialized && Object.keys( paymentMethods ).length === 0 )
	) {
		return null;
	}

	// Set loading state for express payment methods when payment or checkout is in progress.
	const checkoutProcessing =
		isProcessing ||
		isAfterProcessing ||
		isBeforeProcessing ||
		( isComplete && ! hasError );

	return (
		<>
			<LoadingMask
				isLoading={
					isCalculating ||
					checkoutProcessing ||
					isExpressPaymentMethodActive
				}
			>
				<div className="wc-block-components-express-payment wc-block-components-express-payment--cart">
					<div className="wc-block-components-express-payment__content">
						<StoreNoticesContainer
							context={ noticeContexts.EXPRESS_PAYMENTS }
						/>
						<ExpressPaymentMethods />
					</div>
				</div>
			</LoadingMask>
			<div className="wc-block-components-express-payment-continue-rule wc-block-components-express-payment-continue-rule--cart">
				{ /* translators: Shown in the Cart block between the express payment methods and the Proceed to Checkout button */ }
				{ __( 'Or', 'woocommerce' ) }
			</div>
		</>
	);
};

export default CartExpressPayment;
