/**
 * External dependencies
 */
import * as wpDataFunctions from '@wordpress/data';
import { previewCart } from '@woocommerce/resource-previews';
import { PAYMENT_STORE_KEY, CART_STORE_KEY } from '@woocommerce/block-data';
import {
	registerPaymentMethod,
	registerExpressPaymentMethod,
	__experimentalDeRegisterPaymentMethod,
	__experimentalDeRegisterExpressPaymentMethod,
} from '@woocommerce/blocks-registry';
import { CanMakePaymentArgument } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { checkPaymentMethodsCanPay } from '../utils/check-payment-methods';

const requiredKeyCheck = ( args: CanMakePaymentArgument ) => {
	const requiredKeys = [
		'billingData',
		'billingAddress',
		'cart',
		'cartNeedsShipping',
		'cartTotals',
		'paymentMethods',
		'paymentRequirements',
		'selectedShippingMethods',
		'shippingAddress',
	];
	const argKeys = Object.keys( args );

	const requiredCartKeys = [
		'cartCoupons',
		'cartItems',
		'crossSellsProducts',
		'cartFees',
		'cartItemsCount',
		'cartItemsWeight',
		'cartNeedsPayment',
		'cartNeedsShipping',
		'cartItemErrors',
		'cartTotals',
		'cartIsLoading',
		'cartErrors',
		'billingData',
		'billingAddress',
		'shippingAddress',
		'extensions',
		'shippingRates',
		'isLoadingRates',
		'cartHasCalculatedShipping',
		'paymentRequirements',
		'receiveCart',
	];
	const cartKeys = Object.keys( args.cart );
	const requiredTotalsKeys = [
		'total_items',
		'total_items_tax',
		'total_fees',
		'total_fees_tax',
		'total_discount',
		'total_discount_tax',
		'total_shipping',
		'total_shipping_tax',
		'total_price',
		'total_tax',
		'tax_lines',
		'currency_code',
		'currency_symbol',
		'currency_minor_unit',
		'currency_decimal_separator',
		'currency_thousand_separator',
		'currency_prefix',
		'currency_suffix',
	];
	const totalsKeys = Object.keys( args.cartTotals );
	return (
		requiredKeys.every( ( key ) => argKeys.includes( key ) ) &&
		requiredTotalsKeys.every( ( key ) => totalsKeys.includes( key ) ) &&
		requiredCartKeys.every( ( key ) => cartKeys.includes( key ) )
	);
};

const mockedCanMakePayment = jest.fn().mockImplementation( requiredKeyCheck );
const mockedExpressCanMakePayment = jest
	.fn()
	.mockImplementation( requiredKeyCheck );

const registerMockPaymentMethods = ( savedCards = true ) => {
	[ 'credit-card' ].forEach( ( name ) => {
		registerPaymentMethod( {
			name,
			label: name,
			content: <div>A payment method</div>,
			edit: <div>A payment method</div>,
			icons: null,
			canMakePayment: mockedCanMakePayment,
			supports: {
				showSavedCards: savedCards,
				showSaveOption: true,
				features: [ 'products' ],
			},
			ariaLabel: name,
		} );
	} );
	[ 'express-payment' ].forEach( ( name ) => {
		const Content = ( {
			onClose = () => void null,
			onClick = () => void null,
		} ) => {
			return (
				<>
					<button onClick={ onClick }>
						{ name + ' express payment method' }
					</button>
					<button onClick={ onClose }>
						{ name + ' express payment method close' }
					</button>
				</>
			);
		};
		registerExpressPaymentMethod( {
			name,
			content: <Content />,
			edit: <div>An express payment method</div>,
			canMakePayment: mockedExpressCanMakePayment,
			paymentMethodId: name,
			supports: {
				features: [ 'products' ],
			},
		} );
	} );
	wpDataFunctions
		.dispatch( PAYMENT_STORE_KEY )
		.__internalUpdateAvailablePaymentMethods();
	wpDataFunctions.dispatch( CART_STORE_KEY ).receiveCart( {
		...previewCart,
		payment_methods: [ 'cheque', 'bacs', 'credit-card' ],
	} );
};

const resetMockPaymentMethods = () => {
	[ 'cheque', 'bacs', 'credit-card' ].forEach( ( name ) => {
		__experimentalDeRegisterPaymentMethod( name );
	} );
	[ 'express-payment' ].forEach( ( name ) => {
		__experimentalDeRegisterExpressPaymentMethod( name );
	} );
};

describe( 'checkPaymentMethods', () => {
	beforeEach( registerMockPaymentMethods );
	afterEach( resetMockPaymentMethods );

	it( `Sends correct arguments to regular payment methods' canMakePayment functions`, async () => {
		await checkPaymentMethodsCanPay();
		expect( mockedCanMakePayment ).toHaveReturnedWith( true );
	} );

	it( `Sends correct arguments to express payment methods' canMakePayment functions`, async () => {
		await checkPaymentMethodsCanPay( true );
		expect( mockedExpressCanMakePayment ).toHaveReturnedWith( true );
	} );
} );
