/**
 * External dependencies
 */
import deepFreeze from 'deep-freeze';

/**
 * Internal dependencies
 */
import reducer from '../reducers';
import { ACTION_TYPES } from '../action-types';

describe( 'paymentMethodDataReducer', () => {
	const originalState = deepFreeze( {
		currentStatus: {
			isPristine: true,
			isStarted: false,
			isProcessing: false,
			isFinished: false,
			hasError: false,
			hasFailed: false,
			isSuccessful: false,
		},
		availablePaymentMethods: {},
		availableExpressPaymentMethods: {},
		paymentMethodData: {},
		paymentMethodsInitialized: false,
		expressPaymentMethodsInitialized: false,
		shouldSavePaymentMethod: false,
		errorMessage: '',
		activePaymentMethod: '',
		activeSavedToken: '',
		incompatiblePaymentMethods: {},
	} );

	it( 'sets state as expected when adding a payment method', () => {
		const nextState = reducer( originalState, {
			type: ACTION_TYPES.SET_AVAILABLE_PAYMENT_METHODS,
			paymentMethods: { 'my-new-method': { express: false } },
		} );
		expect( nextState ).toEqual( {
			currentStatus: {
				isPristine: true,
				isStarted: false,
				isProcessing: false,
				isFinished: false,
				hasError: false,
				hasFailed: false,
				isSuccessful: false,
			},
			availablePaymentMethods: { 'my-new-method': { express: false } },
			availableExpressPaymentMethods: {},
			paymentMethodData: {},
			paymentMethodsInitialized: false,
			expressPaymentMethodsInitialized: false,
			shouldSavePaymentMethod: false,
			errorMessage: '',
			activePaymentMethod: '',
			activeSavedToken: '',
			incompatiblePaymentMethods: {},
		} );
	} );

	it( 'sets state as expected when removing a payment method', () => {
		const stateWithRegisteredMethod = deepFreeze( {
			currentStatus: {
				isPristine: true,
				isStarted: false,
				isProcessing: false,
				isFinished: false,
				hasError: false,
				hasFailed: false,
				isSuccessful: false,
			},
			availablePaymentMethods: { 'my-new-method': { express: false } },
			availableExpressPaymentMethods: {},
			paymentMethodData: {},
			paymentMethodsInitialized: false,
			expressPaymentMethodsInitialized: false,
			shouldSavePaymentMethod: false,
			errorMessage: '',
			activePaymentMethod: '',
			activeSavedToken: '',
			incompatiblePaymentMethods: {},
		} );
		const nextState = reducer( stateWithRegisteredMethod, {
			type: ACTION_TYPES.REMOVE_AVAILABLE_PAYMENT_METHOD,
			name: 'my-new-method',
		} );
		expect( nextState ).toEqual( {
			currentStatus: {
				isPristine: true,
				isStarted: false,
				isProcessing: false,
				isFinished: false,
				hasError: false,
				hasFailed: false,
				isSuccessful: false,
			},
			availablePaymentMethods: {},
			availableExpressPaymentMethods: {},
			paymentMethodData: {},
			paymentMethodsInitialized: false,
			expressPaymentMethodsInitialized: false,
			shouldSavePaymentMethod: false,
			errorMessage: '',
			activePaymentMethod: '',
			activeSavedToken: '',
			incompatiblePaymentMethods: {},
		} );
	} );

	it( 'sets state as expected when adding an express payment method', () => {
		const nextState = reducer( originalState, {
			type: ACTION_TYPES.SET_AVAILABLE_EXPRESS_PAYMENT_METHODS,
			paymentMethods: { 'my-new-method': { express: true } },
		} );
		expect( nextState ).toEqual( {
			currentStatus: {
				isPristine: true,
				isStarted: false,
				isProcessing: false,
				isFinished: false,
				hasError: false,
				hasFailed: false,
				isSuccessful: false,
			},
			availablePaymentMethods: {},
			availableExpressPaymentMethods: {
				'my-new-method': { express: true },
			},
			paymentMethodData: {},
			paymentMethodsInitialized: false,
			expressPaymentMethodsInitialized: false,
			shouldSavePaymentMethod: false,
			errorMessage: '',
			activePaymentMethod: '',
			activeSavedToken: '',
			incompatiblePaymentMethods: {},
		} );
	} );

	it( 'sets state as expected when removing an express payment method', () => {
		const stateWithRegisteredMethod = deepFreeze( {
			currentStatus: {
				isPristine: true,
				isStarted: false,
				isProcessing: false,
				isFinished: false,
				hasError: false,
				hasFailed: false,
				isSuccessful: false,
			},
			availablePaymentMethods: {},
			availableExpressPaymentMethods: [ 'my-new-method' ],
			paymentMethodData: {},
			paymentMethodsInitialized: false,
			expressPaymentMethodsInitialized: false,
			shouldSavePaymentMethod: false,
			errorMessage: '',
			activePaymentMethod: '',
			activeSavedToken: '',
			incompatiblePaymentMethods: {},
		} );
		const nextState = reducer( stateWithRegisteredMethod, {
			type: ACTION_TYPES.REMOVE_AVAILABLE_EXPRESS_PAYMENT_METHOD,
			name: 'my-new-method',
		} );
		expect( nextState ).toEqual( {
			currentStatus: {
				isPristine: true,
				isStarted: false,
				isProcessing: false,
				isFinished: false,
				hasError: false,
				hasFailed: false,
				isSuccessful: false,
			},
			availablePaymentMethods: {},
			availableExpressPaymentMethods: {},
			paymentMethodData: {},
			paymentMethodsInitialized: false,
			expressPaymentMethodsInitialized: false,
			shouldSavePaymentMethod: false,
			errorMessage: '',
			activePaymentMethod: '',
			activeSavedToken: '',
			incompatiblePaymentMethods: {},
		} );
	} );

	it( 'should handle SET_PAYMENT_RESULT', () => {
		const mockResponse = {
			message: 'success',
			redirectUrl: 'https://example.com',
			paymentStatus: 'not set',
			paymentDetails: {},
		};

		const expectedState = {
			...originalState,
			paymentResult: mockResponse,
		};

		expect(
			reducer( originalState, {
				type: ACTION_TYPES.SET_PAYMENT_RESULT,
				data: mockResponse,
			} )
		).toEqual( expectedState );
	} );
} );
