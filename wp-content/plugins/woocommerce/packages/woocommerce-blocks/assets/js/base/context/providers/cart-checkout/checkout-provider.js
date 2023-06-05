/**
 * External dependencies
 */
import { PluginArea } from '@wordpress/plugins';
import { CURRENT_USER_IS_ADMIN } from '@woocommerce/settings';
import BlockErrorBoundary from '@woocommerce/base-components/block-error-boundary';
/**
 * Internal dependencies
 */
import { PaymentEventsProvider } from './payment-events';
import { ShippingDataProvider } from './shipping';
import { CheckoutEventsProvider } from './checkout-events';
import CheckoutProcessor from './checkout-processor';

/**
 * Checkout provider
 * This wraps the checkout and provides an api interface for the checkout to
 * children via various hooks.
 *
 * @param {Object} props               Incoming props for the provider.
 * @param {Object} props.children      The children being wrapped.
 *                                     component.
 * @param {string} [props.redirectUrl] Initialize what the checkout will
 *                                     redirect to after successful
 *                                     submit.
 */
export const CheckoutProvider = ( { children, redirectUrl } ) => {
	return (
		<CheckoutEventsProvider redirectUrl={ redirectUrl }>
			<ShippingDataProvider>
				<PaymentEventsProvider>
					{ children }
					{ /* If the current user is an admin, we let BlockErrorBoundary render
								the error, or we simply die silently. */ }
					<BlockErrorBoundary
						renderError={
							CURRENT_USER_IS_ADMIN ? null : () => null
						}
					>
						<PluginArea scope="woocommerce-checkout" />
					</BlockErrorBoundary>
					<CheckoutProcessor />
				</PaymentEventsProvider>
			</ShippingDataProvider>
		</CheckoutEventsProvider>
	);
};
