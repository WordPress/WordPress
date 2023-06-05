/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useExpressPaymentMethods,
	usePaymentMethodInterface,
} from '@woocommerce/base-context/hooks';
import {
	cloneElement,
	isValidElement,
	useCallback,
	useRef,
} from '@wordpress/element';
import { useEditorContext } from '@woocommerce/base-context';
import deprecated from '@wordpress/deprecated';
import { useDispatch, useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import PaymentMethodErrorBoundary from './payment-method-error-boundary';
import { STORE_KEY as PAYMENT_STORE_KEY } from '../../../data/payment/constants';

const ExpressPaymentMethods = () => {
	const { isEditor } = useEditorContext();

	const { activePaymentMethod, paymentMethodData } = useSelect(
		( select ) => {
			const store = select( PAYMENT_STORE_KEY );
			return {
				activePaymentMethod: store.getActivePaymentMethod(),
				paymentMethodData: store.getPaymentMethodData(),
			};
		}
	);
	const {
		__internalSetActivePaymentMethod,
		__internalSetExpressPaymentStarted,
		__internalSetPaymentIdle,
		__internalSetPaymentError,
		__internalSetPaymentMethodData,
		__internalSetExpressPaymentError,
	} = useDispatch( PAYMENT_STORE_KEY );
	const { paymentMethods } = useExpressPaymentMethods();

	const paymentMethodInterface = usePaymentMethodInterface();
	const previousActivePaymentMethod = useRef( activePaymentMethod );
	const previousPaymentMethodData = useRef( paymentMethodData );

	/**
	 * onExpressPaymentClick should be triggered when the express payment button is clicked.
	 *
	 * This will store the previous active payment method, set the express method as active, and set the payment status
	 * to started.
	 */
	const onExpressPaymentClick = useCallback(
		( paymentMethodId ) => () => {
			previousActivePaymentMethod.current = activePaymentMethod;
			previousPaymentMethodData.current = paymentMethodData;
			__internalSetExpressPaymentStarted();
			__internalSetActivePaymentMethod( paymentMethodId );
		},
		[
			activePaymentMethod,
			paymentMethodData,
			__internalSetActivePaymentMethod,
			__internalSetExpressPaymentStarted,
		]
	);

	/**
	 * onExpressPaymentClose should be triggered when the express payment process is cancelled or closed.
	 *
	 * This restores the active method and returns the state to pristine.
	 */
	const onExpressPaymentClose = useCallback( () => {
		__internalSetPaymentIdle();
		__internalSetActivePaymentMethod(
			previousActivePaymentMethod.current,
			previousPaymentMethodData.current
		);
	}, [ __internalSetActivePaymentMethod, __internalSetPaymentIdle ] );

	/**
	 * onExpressPaymentError should be triggered when the express payment process errors.
	 *
	 * This shows an error message then restores the active method and returns the state to pristine.
	 */
	const onExpressPaymentError = useCallback(
		( errorMessage ) => {
			__internalSetPaymentError();
			__internalSetPaymentMethodData( errorMessage );
			__internalSetExpressPaymentError( errorMessage );
			__internalSetActivePaymentMethod(
				previousActivePaymentMethod.current,
				previousPaymentMethodData.current
			);
		},
		[
			__internalSetActivePaymentMethod,
			__internalSetPaymentError,
			__internalSetPaymentMethodData,
			__internalSetExpressPaymentError,
		]
	);

	/**
	 * Calling setExpressPaymentError directly is deprecated.
	 */
	const deprecatedSetExpressPaymentError = useCallback(
		( errorMessage = '' ) => {
			deprecated(
				'Express Payment Methods should use the provided onError handler instead.',
				{
					alternative: 'onError',
					plugin: 'woocommerce-gutenberg-products-block',
					link: 'https://github.com/woocommerce/woocommerce-gutenberg-products-block/pull/4228',
				}
			);
			if ( errorMessage ) {
				onExpressPaymentError( errorMessage );
			} else {
				__internalSetExpressPaymentError( '' );
			}
		},
		[ __internalSetExpressPaymentError, onExpressPaymentError ]
	);

	/**
	 * @todo Find a way to Memoize Express Payment Method Content
	 *
	 * Payment method content could potentially become a bottleneck if lots of logic is ran in the content component. It
	 * Currently re-renders excessively but is not easy to useMemo because paymentMethodInterface could become stale.
	 * paymentMethodInterface itself also updates on most renders.
	 */
	const entries = Object.entries( paymentMethods );
	const content =
		entries.length > 0 ? (
			entries.map( ( [ id, paymentMethod ] ) => {
				const expressPaymentMethod = isEditor
					? paymentMethod.edit
					: paymentMethod.content;
				return isValidElement( expressPaymentMethod ) ? (
					<li key={ id } id={ `express-payment-method-${ id }` }>
						{ cloneElement( expressPaymentMethod, {
							...paymentMethodInterface,
							onClick: onExpressPaymentClick( id ),
							onClose: onExpressPaymentClose,
							onError: onExpressPaymentError,
							setExpressPaymentError:
								deprecatedSetExpressPaymentError,
						} ) }
					</li>
				) : null;
			} )
		) : (
			<li key="noneRegistered">
				{ __(
					'No registered Payment Methods',
					'woocommerce'
				) }
			</li>
		);

	return (
		<PaymentMethodErrorBoundary isEditor={ isEditor }>
			<ul className="wc-block-components-express-payment__event-buttons">
				{ content }
			</ul>
		</PaymentMethodErrorBoundary>
	);
};

export default ExpressPaymentMethods;
