/**
 * External dependencies
 */
import * as wpDataFunctions from '@wordpress/data';
import { EventObserversType } from '@woocommerce/base-context';

/**
 * Internal dependencies
 */
import { PAYMENT_STORE_KEY } from '../index';
import { __internalEmitPaymentProcessingEvent } from '../thunks';

/**
 * If an observer returns billingAddress, shippingAddress, or paymentData, then the values of these
 * should be updated in the data stores.
 */
const testShippingAddress = {
	first_name: 'test',
	last_name: 'test',
	company: 'test',
	address_1: 'test',
	address_2: 'test',
	city: 'test',
	state: 'test',
	postcode: 'test',
	country: 'test',
	phone: 'test',
};
const testBillingAddress = {
	...testShippingAddress,
	email: 'test@test.com',
};
const testPaymentMethodData = {
	payment_method: 'test',
};

describe( 'wc/store/payment thunks', () => {
	const testPaymentProcessingCallback = jest.fn();
	const testPaymentProcessingCallback2 = jest.fn();
	const currentObservers: EventObserversType = {
		payment_setup: new Map(),
	};
	currentObservers.payment_setup.set( 'test', {
		callback: testPaymentProcessingCallback,
		priority: 10,
	} );
	currentObservers.payment_setup.set( 'test2', {
		callback: testPaymentProcessingCallback2,
		priority: 10,
	} );

	describe( '__internalEmitPaymentProcessingEvent', () => {
		beforeEach( () => {
			jest.resetAllMocks();
		} );
		it( 'calls all registered observers', async () => {
			const {
				__internalEmitPaymentProcessingEvent:
					__internalEmitPaymentProcessingEventFromStore,
			} = wpDataFunctions.dispatch( PAYMENT_STORE_KEY );
			await __internalEmitPaymentProcessingEventFromStore(
				currentObservers,
				jest.fn()
			);
			expect( testPaymentProcessingCallback ).toHaveBeenCalled();
			expect( testPaymentProcessingCallback2 ).toHaveBeenCalled();
		} );

		it( 'sets metadata if successful observers return it', async () => {
			const testSuccessCallbackWithMetadata = jest.fn().mockReturnValue( {
				type: 'success',
				meta: {
					billingAddress: testBillingAddress,
					shippingAddress: testShippingAddress,
					paymentMethodData: testPaymentMethodData,
				},
			} );

			currentObservers.payment_setup.set( 'test3', {
				callback: testSuccessCallbackWithMetadata,
				priority: 10,
			} );

			const setBillingAddressMock = jest.fn();
			const setShippingAddressMock = jest.fn();
			const setPaymentMethodDataMock = jest.fn();
			const registryMock = {
				dispatch: jest.fn().mockImplementation( ( store: string ) => {
					return {
						...wpDataFunctions.dispatch( store ),
						setBillingAddress: setBillingAddressMock,
						setShippingAddress: setShippingAddressMock,
					};
				} ),
			};

			// Await here because the function returned by the __internalEmitPaymentProcessingEvent action creator
			// (a thunk) returns a Promise.
			await __internalEmitPaymentProcessingEvent(
				currentObservers,
				jest.fn()
			)( {
				// eslint-disable-next-line @typescript-eslint/ban-ts-comment
				// @ts-ignore - it would be too much work to mock the entire registry, so we only mock dispatch on it,
				// which is all we need to test this thunk.
				registry: registryMock,
				dispatch: {
					...wpDataFunctions.dispatch( PAYMENT_STORE_KEY ),
					__internalSetPaymentMethodData: setPaymentMethodDataMock,
				},
			} );

			expect( setBillingAddressMock ).toHaveBeenCalledWith(
				testBillingAddress
			);
			expect( setShippingAddressMock ).toHaveBeenCalledWith(
				testShippingAddress
			);
			expect( setPaymentMethodDataMock ).toHaveBeenCalledWith(
				testPaymentMethodData
			);
		} );
		it( 'sets metadata if failed observers return it', async () => {
			const testFailingCallbackWithMetadata = jest.fn().mockReturnValue( {
				type: 'failure',
				meta: {
					billingAddress: testBillingAddress,
					paymentMethodData: testPaymentMethodData,
				},
			} );

			currentObservers.payment_setup.set( 'test4', {
				callback: testFailingCallbackWithMetadata,
				priority: 10,
			} );

			const setBillingAddressMock = jest.fn();
			const setPaymentMethodDataMock = jest.fn();
			const registryMock = {
				dispatch: jest.fn().mockImplementation( ( store: string ) => {
					return {
						...wpDataFunctions.dispatch( store ),
						setBillingAddress: setBillingAddressMock,
					};
				} ),
			};

			// Await here because the function returned by the __internalEmitPaymentProcessingEvent action creator
			// (a thunk) returns a Promise.
			await __internalEmitPaymentProcessingEvent(
				currentObservers,
				jest.fn()
			)( {
				// eslint-disable-next-line @typescript-eslint/ban-ts-comment
				// @ts-ignore - it would be too much work to mock the entire registry, so we only mock dispatch on it,
				// which is all we need to test this thunk.
				registry: registryMock,
				dispatch: {
					...wpDataFunctions.dispatch( PAYMENT_STORE_KEY ),
					__internalSetPaymentMethodData: setPaymentMethodDataMock,
				},
			} );

			expect( setBillingAddressMock ).toHaveBeenCalledWith(
				testBillingAddress
			);
			expect( setPaymentMethodDataMock ).toHaveBeenCalledWith(
				testPaymentMethodData
			);
		} );
		it( 'sets payment status to error if one observer is successful, but another errors', async () => {
			const testErrorCallbackWithMetadata = jest
				.fn()
				.mockImplementation( () => {
					return {
						type: 'error',
					};
				} );

			const testSuccessCallback = jest.fn().mockReturnValue( {
				type: 'success',
			} );

			currentObservers.payment_setup.set( 'test5', {
				callback: testErrorCallbackWithMetadata,
				priority: 10,
			} );
			currentObservers.payment_setup.set( 'test6', {
				callback: testSuccessCallback,
				priority: 9,
			} );

			const setPaymentErrorMock = jest.fn();
			const setPaymentReadyMock = jest.fn();
			const registryMock = {
				dispatch: jest
					.fn()
					.mockImplementation( wpDataFunctions.dispatch ),
			};

			// Await here because the function returned by the __internalEmitPaymentProcessingEvent action creator
			// (a thunk) returns a Promise.
			await __internalEmitPaymentProcessingEvent(
				currentObservers,
				jest.fn()
			)( {
				// eslint-disable-next-line @typescript-eslint/ban-ts-comment
				// @ts-ignore - it would be too much work to mock the entire registry, so we only mock dispatch on it,
				// which is all we need to test this thunk.
				registry: registryMock,
				dispatch: {
					...wpDataFunctions.dispatch( PAYMENT_STORE_KEY ),
					__internalSetPaymentError: setPaymentErrorMock,
					__internalSetPaymentReady: setPaymentReadyMock,
				},
			} );

			// The observer throwing will cause this.
			//expect( console ).toHaveErroredWith( new Error( 'test error' ) );
			expect( setPaymentErrorMock ).toHaveBeenCalled();
			expect( setPaymentReadyMock ).not.toHaveBeenCalled();
		} );
	} );
} );
