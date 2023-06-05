/**
 * Internal dependencies
 */
import reducer from '../reducers';
import { defaultState } from '../default-state';
import { STATUS } from '../constants';
import * as actions from '../actions';

describe.only( 'Checkout Store Reducer', () => {
	it( 'should return the initial state', () => {
		expect( reducer( undefined, {} ) ).toEqual( defaultState );
	} );

	it( 'should handle SET_IDLE', () => {
		const expectedState = {
			...defaultState,
			status: STATUS.IDLE,
		};

		expect( reducer( defaultState, actions.__internalSetIdle() ) ).toEqual(
			expectedState
		);
	} );

	it( 'should handle SET_REDIRECT_URL', () => {
		const expectedState = {
			...defaultState,
			redirectUrl: 'https://example.com',
		};

		expect(
			reducer(
				defaultState,
				actions.__internalSetRedirectUrl( 'https://example.com' )
			)
		).toEqual( expectedState );
	} );

	it( 'should handle SET_COMPLETE', () => {
		const expectedState = {
			...defaultState,
			status: STATUS.COMPLETE,
			redirectUrl: 'https://example.com',
		};

		expect(
			reducer(
				defaultState,
				actions.__internalSetComplete( {
					redirectUrl: 'https://example.com',
				} )
			)
		).toEqual( expectedState );
	} );

	it( 'should handle SET_PROCESSING', () => {
		const expectedState = {
			...defaultState,
			status: STATUS.PROCESSING,
		};

		expect(
			reducer( defaultState, actions.__internalSetProcessing() )
		).toEqual( expectedState );
	} );

	it( 'should handle SET_HAS_ERROR when status is PROCESSING', () => {
		const initialState = { ...defaultState, status: STATUS.PROCESSING };

		const expectedState = {
			...defaultState,
			hasError: true,
			status: STATUS.IDLE,
		};

		expect(
			reducer( initialState, actions.__internalSetHasError( true ) )
		).toEqual( expectedState );
	} );

	it( 'should handle SET_HAS_ERROR when status is BEFORE_PROCESSING', () => {
		const initialState = {
			...defaultState,
			status: STATUS.BEFORE_PROCESSING,
		};

		const expectedState = {
			...defaultState,
			hasError: true,
			status: STATUS.IDLE,
		};

		expect(
			reducer( initialState, actions.__internalSetHasError( true ) )
		).toEqual( expectedState );
	} );

	it( 'should handle SET_HAS_ERROR when status is anything else', () => {
		const initialState = {
			...defaultState,
			status: STATUS.AFTER_PROCESSING,
		};

		const expectedState = {
			...defaultState,
			hasError: false,
			status: STATUS.AFTER_PROCESSING,
		};

		expect(
			reducer( initialState, actions.__internalSetHasError( false ) )
		).toEqual( expectedState );
	} );

	it( 'should handle SET_BEFORE_PROCESSING', () => {
		const expectedState = {
			...defaultState,
			status: STATUS.BEFORE_PROCESSING,
		};

		expect(
			reducer( defaultState, actions.__internalSetBeforeProcessing() )
		).toEqual( expectedState );
	} );

	it( 'should handle SET_AFTER_PROCESSING', () => {
		const expectedState = {
			...defaultState,
			status: STATUS.AFTER_PROCESSING,
		};

		expect(
			reducer( defaultState, actions.__internalSetAfterProcessing() )
		).toEqual( expectedState );
	} );

	it( 'should handle INCREMENT_CALCULATING', () => {
		const expectedState = {
			...defaultState,
			calculatingCount: 1,
		};

		expect(
			reducer( defaultState, actions.__internalIncrementCalculating() )
		).toEqual( expectedState );
	} );

	it( 'should handle DECREMENT_CALCULATING', () => {
		const initialState = {
			...defaultState,
			calculatingCount: 1,
		};

		const expectedState = {
			...defaultState,
			calculatingCount: 0,
		};

		expect(
			reducer( initialState, actions.__internalDecrementCalculating() )
		).toEqual( expectedState );
	} );

	it( 'should handle SET_CUSTOMER_ID', () => {
		const expectedState = {
			...defaultState,
			customerId: 1,
		};

		expect(
			reducer( defaultState, actions.__internalSetCustomerId( 1 ) )
		).toEqual( expectedState );
	} );

	it( 'should handle SET_USE_SHIPPING_AS_BILLING', () => {
		const expectedState = {
			...defaultState,
			useShippingAsBilling: false,
		};

		expect(
			reducer(
				defaultState,
				actions.__internalSetUseShippingAsBilling( false )
			)
		).toEqual( expectedState );
	} );

	it( 'should handle SET_SHOULD_CREATE_ACCOUNT', () => {
		const expectedState = {
			...defaultState,
			shouldCreateAccount: true,
		};

		expect(
			reducer(
				defaultState,
				actions.__internalSetShouldCreateAccount( true )
			)
		).toEqual( expectedState );
	} );

	it( 'should handle SET_ORDER_NOTES', () => {
		const expectedState = {
			...defaultState,
			orderNotes: 'test',
		};

		expect(
			reducer( defaultState, actions.__internalSetOrderNotes( 'test' ) )
		).toEqual( expectedState );
	} );

	describe( 'should handle SET_EXTENSION_DATA', () => {
		it( 'should set data under a namespace', () => {
			const mockExtensionData = {
				extensionNamespace: {
					testKey: 'test-value',
					testKey2: 'test-value-2',
				},
			};
			const expectedState = {
				...defaultState,
				extensionData: mockExtensionData,
			};
			expect(
				reducer(
					defaultState,
					actions.__internalSetExtensionData(
						'extensionNamespace',
						mockExtensionData.extensionNamespace
					)
				)
			).toEqual( expectedState );
		} );
		it( 'should append data under a namespace', () => {
			const mockExtensionData = {
				extensionNamespace: {
					testKey: 'test-value',
					testKey2: 'test-value-2',
				},
			};
			const expectedState = {
				...defaultState,
				extensionData: mockExtensionData,
			};
			const firstState = reducer(
				defaultState,
				actions.__internalSetExtensionData( 'extensionNamespace', {
					testKey: 'test-value',
				} )
			);
			const secondState = reducer(
				firstState,
				actions.__internalSetExtensionData( 'extensionNamespace', {
					testKey2: 'test-value-2',
				} )
			);
			expect( secondState ).toEqual( expectedState );
		} );
		it( 'support replacing data under a namespace', () => {
			const mockExtensionData = {
				extensionNamespace: {
					testKey: 'test-value',
				},
			};
			const expectedState = {
				...defaultState,
				extensionData: mockExtensionData,
			};
			const firstState = reducer(
				defaultState,
				actions.__internalSetExtensionData( 'extensionNamespace', {
					testKeyOld: 'test-value',
				} )
			);
			const secondState = reducer(
				firstState,
				actions.__internalSetExtensionData(
					'extensionNamespace',
					{ testKey: 'test-value' },
					true
				)
			);
			expect( secondState ).toEqual( expectedState );
		} );
	} );
} );
