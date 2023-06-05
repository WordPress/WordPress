/**
 * External dependencies
 */
import { registerPaymentMethodExtensionCallbacks } from '@woocommerce/blocks-registry';
/**
 * Internal dependencies
 */
import * as helpers from '../payment-method-config-helper';
import { canMakePaymentExtensionsCallbacks } from '../extensions-config';

const canMakePaymentArgument = {
	cartTotals: {
		total_items: '1488',
		total_items_tax: '312',
		total_fees: '0',
		total_fees_tax: '0',
		total_discount: '0',
		total_discount_tax: '0',
		total_shipping: '0',
		total_shipping_tax: '0',
		total_price: '1800',
		total_tax: '312',
		tax_lines: [
			{
				name: 'BTW',
				price: '312',
				rate: '21%',
			},
		],
		currency_code: 'EUR',
		currency_symbol: '€',
		currency_minor_unit: 2,
		currency_decimal_separator: ',',
		currency_thousand_separator: '.',
		currency_prefix: '€',
		currency_suffix: '',
	},
	cartNeedsShipping: true,
	billingAddress: {
		first_name: 'name',
		last_name: 'Name',
		company: '',
		address_1: 'fdsfdsfdsf',
		address_2: '',
		city: 'Berlin',
		state: '',
		postcode: 'xxxxx',
		country: 'DE',
		email: 'name.Name@test.com',
		phone: '1234',
	},
	shippingAddress: {
		first_name: 'name',
		last_name: 'Name',
		company: '',
		address_1: 'fdsfdsfdsf',
		address_2: '',
		city: 'Berlin',
		state: '',
		postcode: 'xxxxx',
		country: 'DE',
		phone: '1234',
	},
	selectedShippingMethods: {
		'0': 'free_shipping:1',
	},
	paymentRequirements: [ 'products' ],
};
describe( 'payment-method-config-helper', () => {
	const trueCallback = jest.fn().mockReturnValue( true );
	const falseCallback = jest.fn().mockReturnValue( false );
	const bacsCallback = jest.fn().mockReturnValue( false );
	const throwsCallback = jest.fn().mockImplementation( () => {
		throw new Error();
	} );
	beforeAll( () => {
		// Register extension callbacks for two payment methods.
		registerPaymentMethodExtensionCallbacks(
			'woocommerce-marketplace-extension',
			{
				// cod: one extension returns true, the other returns false.
				cod: trueCallback,
				// cheque: returns true only if arg.billingAddress.postcode is 12345.
				cheque: ( arg ) => arg.billingAddress.postcode === '12345',
				// bacs: both extensions return false.
				bacs: bacsCallback,
				// woopay: both extensions return true.
				woopay: trueCallback,
				// testpay: one callback errors, one returns true
				testpay: throwsCallback,
				// Used to check that only valid callbacks run in each namespace. It is not present in
				// 'other-woocommerce-marketplace-extension'.
				blocks_pay: trueCallback,
			}
		);
		registerPaymentMethodExtensionCallbacks(
			'other-woocommerce-marketplace-extension',
			{
				cod: falseCallback,
				woopay: trueCallback,
				testpay: trueCallback,
				bacs: bacsCallback,
			}
		);
	} );

	beforeEach( () => {
		trueCallback.mockClear();
		throwsCallback.mockClear();
		falseCallback.mockClear();
		bacsCallback.mockClear();
	} );
	describe( 'getCanMakePayment', () => {
		it( 'returns callback canMakePaymentWithFeaturesCheck if no extension callback is detected', () => {
			// Define arguments from a payment method ('missing-payment-method') with no registered extension callbacks.
			const args = {
				canMakePayment: jest.fn().mockImplementation( () => true ),
				features: [ 'products' ],
				paymentMethodName: 'missing-payment-method',
			};

			const canMakePayment = helpers.getCanMakePayment(
				args.canMakePayment,
				args.features,
				args.paymentMethodName
			)( canMakePaymentArgument );

			// Expect that the result of getCanMakePayment is the result of
			// the payment method's own canMakePayment, as no extension callbacks are called.
			expect( canMakePayment ).toEqual( args.canMakePayment() );
		} );

		it( 'returns callbacks from the extensions when they are defined', () => {
			// Define arguments from a payment method (bacs) with registered extension callbacks.
			const args = {
				canMakePaymentConfiguration: jest
					.fn()
					.mockImplementation( () => true ),
				features: [ 'products' ],
				paymentMethodName: 'bacs',
			};

			const canMakePayment = helpers.getCanMakePayment(
				args.canMakePaymentConfiguration,
				args.features,
				args.paymentMethodName
			)( canMakePaymentArgument );

			// Expect that the result of getCanMakePayment is not the result of
			// the payment method's own canMakePayment (args.canMakePaymentConfiguration),
			// but of the registered bacsCallback.
			expect( canMakePayment ).toBe( bacsCallback() );
		} );
	} );

	describe( 'canMakePaymentWithExtensions', () => {
		it( "Returns false without executing the registered callbacks, if the payment method's canMakePayment callback returns false.", () => {
			const canMakePayment = () => false;
			const canMakePaymentWithExtensionsResult =
				helpers.canMakePaymentWithExtensions(
					canMakePayment,
					canMakePaymentExtensionsCallbacks,
					'cod'
				)( canMakePaymentArgument );
			expect( canMakePaymentWithExtensionsResult ).toBe( false );
			expect( trueCallback ).not.toHaveBeenCalled();
		} );

		it( 'Returns early when a registered callback returns false, without executing all the registered callbacks', () => {
			helpers.canMakePaymentWithExtensions(
				() => true,
				canMakePaymentExtensionsCallbacks,
				'bacs'
			)( canMakePaymentArgument );
			expect( bacsCallback ).toHaveBeenCalledTimes( 1 );
		} );

		it( 'Returns true if all extension callbacks return true', () => {
			const result = helpers.canMakePaymentWithExtensions(
				() => true,
				canMakePaymentExtensionsCallbacks,
				'woopay'
			)( canMakePaymentArgument );
			expect( result ).toBe( true );
		} );

		it( 'Passes canPayArg to the callback', () => {
			helpers.canMakePaymentWithExtensions(
				() => true,
				canMakePaymentExtensionsCallbacks,
				'woopay'
			)( canMakePaymentArgument );
			expect( trueCallback ).toHaveBeenCalledWith(
				canMakePaymentArgument
			);
		} );

		it( 'Allows all valid callbacks to run, even if one causes an error', () => {
			helpers.canMakePaymentWithExtensions(
				() => true,
				canMakePaymentExtensionsCallbacks,
				'testpay'
			)( canMakePaymentArgument );
			expect( console ).toHaveErrored();
			expect( throwsCallback ).toHaveBeenCalledTimes( 1 );
			expect( trueCallback ).toHaveBeenCalledTimes( 1 );
		} );

		it( 'Does not error when a callback for a payment method is in one namespace but not another', () => {
			helpers.canMakePaymentWithExtensions(
				() => true,
				canMakePaymentExtensionsCallbacks,
				'blocks_pay'
			)( canMakePaymentArgument );
			expect( console ).not.toHaveErrored();
		} );
	} );
} );
