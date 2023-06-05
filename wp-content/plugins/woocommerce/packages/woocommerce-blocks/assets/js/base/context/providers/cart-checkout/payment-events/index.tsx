/**
 * External dependencies
 */
import {
	createContext,
	useContext,
	useReducer,
	useRef,
	useEffect,
	useMemo,
} from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import {
	CHECKOUT_STORE_KEY,
	PAYMENT_STORE_KEY,
	VALIDATION_STORE_KEY,
} from '@woocommerce/block-data';
import deprecated from '@wordpress/deprecated';

/**
 * Internal dependencies
 */
import { useEventEmitters, reducer as emitReducer } from './event-emit';
import { emitterCallback } from '../../../event-emit';

type PaymentEventsContextType = {
	// Event registration callback for registering observers for the payment processing event.
	onPaymentProcessing: ReturnType< typeof emitterCallback >;
	onPaymentSetup: ReturnType< typeof emitterCallback >;
};

const PaymentEventsContext = createContext< PaymentEventsContextType >( {
	onPaymentProcessing: () => () => () => void null,
	onPaymentSetup: () => () => () => void null,
} );

export const usePaymentEventsContext = () => {
	return useContext( PaymentEventsContext );
};

/**
 * PaymentEventsProvider is automatically included in the CheckoutProvider.
 *
 * This provides the api interface (via the context hook) for payment status and data.
 *
 * @param {Object} props          Incoming props for provider
 * @param {Object} props.children The wrapped components in this provider.
 */
export const PaymentEventsProvider = ( {
	children,
}: {
	children: React.ReactNode;
} ): JSX.Element => {
	const {
		isProcessing: checkoutIsProcessing,
		isIdle: checkoutIsIdle,
		isCalculating: checkoutIsCalculating,
		hasError: checkoutHasError,
	} = useSelect( ( select ) => {
		const store = select( CHECKOUT_STORE_KEY );
		return {
			isProcessing: store.isProcessing(),
			isIdle: store.isIdle(),
			hasError: store.hasError(),
			isCalculating: store.isCalculating(),
		};
	} );
	const { isPaymentReady } = useSelect( ( select ) => {
		const store = select( PAYMENT_STORE_KEY );

		return {
			// The PROCESSING status represents before the checkout runs the observers
			// registered for the payment_setup event.
			isPaymentProcessing: store.isPaymentProcessing(),
			// the READY status represents when the observers have finished processing and payment data
			// synced with the payment store, ready to be sent to the StoreApi
			isPaymentReady: store.isPaymentReady(),
		};
	} );

	const { setValidationErrors } = useDispatch( VALIDATION_STORE_KEY );
	const [ observers, observerDispatch ] = useReducer( emitReducer, {} );
	const { onPaymentSetup } = useEventEmitters( observerDispatch );
	const currentObservers = useRef( observers );

	// ensure observers are always current.
	useEffect( () => {
		currentObservers.current = observers;
	}, [ observers ] );

	const {
		__internalSetPaymentProcessing,
		__internalSetPaymentIdle,
		__internalEmitPaymentProcessingEvent,
	} = useDispatch( PAYMENT_STORE_KEY );

	// flip payment to processing if checkout processing is complete and there are no errors
	useEffect( () => {
		if (
			checkoutIsProcessing &&
			! checkoutHasError &&
			! checkoutIsCalculating
		) {
			__internalSetPaymentProcessing();

			// Note: the nature of this event emitter is that it will bail on any
			// observer that returns a response that !== true. However, this still
			// allows for other observers that return true for continuing through
			// to the next observer (or bailing if there's a problem).
			__internalEmitPaymentProcessingEvent(
				currentObservers.current,
				setValidationErrors
			);
		}
	}, [
		checkoutIsProcessing,
		checkoutHasError,
		checkoutIsCalculating,
		__internalSetPaymentProcessing,
		__internalEmitPaymentProcessingEvent,
		setValidationErrors,
	] );

	// When checkout is returned to idle, and the payment setup has not completed, set payment status to idle
	useEffect( () => {
		if ( checkoutIsIdle && ! isPaymentReady ) {
			__internalSetPaymentIdle();
		}
	}, [ checkoutIsIdle, isPaymentReady, __internalSetPaymentIdle ] );

	// if checkout has an error sync payment status back to idle.
	useEffect( () => {
		if ( checkoutHasError && isPaymentReady ) {
			__internalSetPaymentIdle();
		}
	}, [ checkoutHasError, isPaymentReady, __internalSetPaymentIdle ] );

	/**
	 * @deprecated use onPaymentSetup instead
	 */
	const onPaymentProcessing = useMemo( () => {
		return function ( ...args: Parameters< typeof onPaymentSetup > ) {
			deprecated( 'onPaymentProcessing', {
				alternative: 'onPaymentSetup',
				plugin: 'WooCommerce Blocks',
			} );
			return onPaymentSetup( ...args );
		};
	}, [ onPaymentSetup ] );

	const paymentContextData = {
		onPaymentProcessing,
		onPaymentSetup,
	};

	return (
		<PaymentEventsContext.Provider value={ paymentContextData }>
			{ children }
		</PaymentEventsContext.Provider>
	);
};
