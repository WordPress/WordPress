/**
 * External dependencies
 */
import {
	createContext,
	useContext,
	useReducer,
	useEffect,
	useMemo,
	useRef,
} from '@wordpress/element';
import { useDispatch } from '@wordpress/data';
import { CHECKOUT_STORE_KEY } from '@woocommerce/block-data';

/**
 * Internal dependencies
 */
import type {
	ShippingDataContextType,
	ShippingDataProviderProps,
} from './types';
import { ERROR_TYPES, DEFAULT_SHIPPING_CONTEXT_DATA } from './constants';
import { hasInvalidShippingAddress } from './utils';
import { errorStatusReducer } from './reducers';
import {
	EMIT_TYPES,
	emitterObservers,
	reducer as emitReducer,
	emitEvent,
} from './event-emit';
import { useStoreCart } from '../../../hooks/cart/use-store-cart';
import { useShippingData } from '../../../hooks/shipping/use-shipping-data';

const { NONE, INVALID_ADDRESS, UNKNOWN } = ERROR_TYPES;
const ShippingDataContext = createContext( DEFAULT_SHIPPING_CONTEXT_DATA );

export const useShippingDataContext = (): ShippingDataContextType => {
	return useContext( ShippingDataContext );
};

/**
 * The shipping data provider exposes the interface for shipping in the checkout/cart.
 */
export const ShippingDataProvider = ( {
	children,
}: ShippingDataProviderProps ) => {
	const { __internalIncrementCalculating, __internalDecrementCalculating } =
		useDispatch( CHECKOUT_STORE_KEY );
	const { shippingRates, isLoadingRates, cartErrors } = useStoreCart();
	const { selectedRates, isSelectingRate } = useShippingData();
	const [ shippingErrorStatus, dispatchErrorStatus ] = useReducer(
		errorStatusReducer,
		NONE
	);
	const [ observers, observerDispatch ] = useReducer( emitReducer, {} );
	const currentObservers = useRef( observers );
	const eventObservers = useMemo(
		() => ( {
			onShippingRateSuccess:
				emitterObservers( observerDispatch ).onSuccess,
			onShippingRateFail: emitterObservers( observerDispatch ).onFail,
			onShippingRateSelectSuccess:
				emitterObservers( observerDispatch ).onSelectSuccess,
			onShippingRateSelectFail:
				emitterObservers( observerDispatch ).onSelectFail,
		} ),
		[ observerDispatch ]
	);

	// set observers on ref so it's always current.
	useEffect( () => {
		currentObservers.current = observers;
	}, [ observers ] );

	// increment/decrement checkout calculating counts when shipping is loading.
	useEffect( () => {
		if ( isLoadingRates ) {
			__internalIncrementCalculating();
		} else {
			__internalDecrementCalculating();
		}
	}, [
		isLoadingRates,
		__internalIncrementCalculating,
		__internalDecrementCalculating,
	] );

	// increment/decrement checkout calculating counts when shipping rates are being selected.
	useEffect( () => {
		if ( isSelectingRate ) {
			__internalIncrementCalculating();
		} else {
			__internalDecrementCalculating();
		}
	}, [
		__internalIncrementCalculating,
		__internalDecrementCalculating,
		isSelectingRate,
	] );

	// set shipping error status if there are shipping error codes
	useEffect( () => {
		if (
			cartErrors.length > 0 &&
			hasInvalidShippingAddress( cartErrors )
		) {
			dispatchErrorStatus( { type: INVALID_ADDRESS } );
		} else {
			dispatchErrorStatus( { type: NONE } );
		}
	}, [ cartErrors ] );

	const currentErrorStatus = useMemo(
		() => ( {
			isPristine: shippingErrorStatus === NONE,
			isValid: shippingErrorStatus === NONE,
			hasInvalidAddress: shippingErrorStatus === INVALID_ADDRESS,
			hasError:
				shippingErrorStatus === UNKNOWN ||
				shippingErrorStatus === INVALID_ADDRESS,
		} ),
		[ shippingErrorStatus ]
	);

	// emit events.
	useEffect( () => {
		if (
			! isLoadingRates &&
			( shippingRates.length === 0 || currentErrorStatus.hasError )
		) {
			emitEvent(
				currentObservers.current,
				EMIT_TYPES.SHIPPING_RATES_FAIL,
				{
					hasInvalidAddress: currentErrorStatus.hasInvalidAddress,
					hasError: currentErrorStatus.hasError,
				}
			);
		}
	}, [
		shippingRates,
		isLoadingRates,
		currentErrorStatus.hasError,
		currentErrorStatus.hasInvalidAddress,
	] );

	useEffect( () => {
		if (
			! isLoadingRates &&
			shippingRates.length > 0 &&
			! currentErrorStatus.hasError
		) {
			emitEvent(
				currentObservers.current,
				EMIT_TYPES.SHIPPING_RATES_SUCCESS,
				shippingRates
			);
		}
	}, [ shippingRates, isLoadingRates, currentErrorStatus.hasError ] );

	// emit shipping rate selection events.
	useEffect( () => {
		if ( isSelectingRate ) {
			return;
		}
		if ( currentErrorStatus.hasError ) {
			emitEvent(
				currentObservers.current,
				EMIT_TYPES.SHIPPING_RATE_SELECT_FAIL,
				{
					hasError: currentErrorStatus.hasError,
					hasInvalidAddress: currentErrorStatus.hasInvalidAddress,
				}
			);
		} else {
			emitEvent(
				currentObservers.current,
				EMIT_TYPES.SHIPPING_RATE_SELECT_SUCCESS,
				selectedRates.current
			);
		}
	}, [
		selectedRates,
		isSelectingRate,
		currentErrorStatus.hasError,
		currentErrorStatus.hasInvalidAddress,
	] );

	const ShippingData: ShippingDataContextType = {
		shippingErrorStatus: currentErrorStatus,
		dispatchErrorStatus,
		shippingErrorTypes: ERROR_TYPES,
		...eventObservers,
	};

	return (
		<>
			<ShippingDataContext.Provider value={ ShippingData }>
				{ children }
			</ShippingDataContext.Provider>
		</>
	);
};
