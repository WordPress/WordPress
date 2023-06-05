/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEditorContext } from '@woocommerce/base-context';
import { CheckboxControl } from '@woocommerce/blocks-checkout';
import PropTypes from 'prop-types';
import { useSelect, useDispatch } from '@wordpress/data';
import { CHECKOUT_STORE_KEY, PAYMENT_STORE_KEY } from '@woocommerce/block-data';

/**
 * Internal dependencies
 */
import PaymentMethodErrorBoundary from './payment-method-error-boundary';

/**
 * Component used to render the contents of a payment method card.
 *
 * @param {Object}  props                Incoming props for the component.
 * @param {boolean} props.showSaveOption Whether that payment method allows saving
 *                                       the data for future purchases.
 * @param {Object}  props.children       Content of the payment method card.
 *
 * @return {*} The rendered component.
 */
const PaymentMethodCard = ( { children, showSaveOption } ) => {
	const { isEditor } = useEditorContext();
	const { shouldSavePaymentMethod, customerId } = useSelect( ( select ) => {
		const paymentMethodStore = select( PAYMENT_STORE_KEY );
		const checkoutStore = select( CHECKOUT_STORE_KEY );
		return {
			shouldSavePaymentMethod:
				paymentMethodStore.getShouldSavePaymentMethod(),
			customerId: checkoutStore.getCustomerId(),
		};
	} );
	const { __internalSetShouldSavePaymentMethod } =
		useDispatch( PAYMENT_STORE_KEY );
	return (
		<PaymentMethodErrorBoundary isEditor={ isEditor }>
			{ children }
			{ customerId > 0 && showSaveOption && (
				<CheckboxControl
					className="wc-block-components-payment-methods__save-card-info"
					label={ __(
						'Save payment information to my account for future purchases.',
						'woocommerce'
					) }
					checked={ shouldSavePaymentMethod }
					onChange={ () =>
						__internalSetShouldSavePaymentMethod(
							! shouldSavePaymentMethod
						)
					}
				/>
			) }
		</PaymentMethodErrorBoundary>
	);
};

PaymentMethodCard.propTypes = {
	showSaveOption: PropTypes.bool,
	children: PropTypes.node,
};

export default PaymentMethodCard;
