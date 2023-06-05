/**
 * External dependencies
 */
import { registerPaymentMethodExtensionCallbacks } from '@woocommerce/blocks-registry';
import type { PaymentMethodConfigInstance } from '@woocommerce/types';
/**
 * Internal dependencies
 */
import PaymentMethodConfig from '../payment-method-config';
import * as paymentMethodConfigHelpers from '../payment-method-config-helper';

describe( 'PaymentMethodConfig', () => {
	let paymentMethod: PaymentMethodConfigInstance;
	const extensionsCallbackSpy = jest.spyOn(
		paymentMethodConfigHelpers,
		'canMakePaymentWithExtensions'
	);
	beforeEach( () => {
		paymentMethod = new PaymentMethodConfig( {
			name: 'test-payment-method',
			label: 'Test payment method',
			ariaLabel: 'Test payment method',
			content: <div>Test payment content</div>,
			edit: <div>Test payment edit</div>,
			canMakePayment: () => true,
			supports: { features: [ 'products' ] },
		} );
	} );

	it( 'Uses canMakePaymentWithExtensions as the canMakePayment function if an extension registers a callback', () => {
		registerPaymentMethodExtensionCallbacks(
			'woocommerce-marketplace-extension',
			{
				// eslint-disable-next-line @typescript-eslint/naming-convention
				'unrelated-payment-method': () => true,
			}
		);

		// At this point, since no extensions have registered a callback for
		// test-payment-method we can expect the canMakePayment getter NOT
		// to execute canMakePaymentWithExtensions.
		// Disable no-unused-expressions because we just want to test the getter
		// eslint-disable-next-line no-unused-expressions
		paymentMethod.canMakePayment;
		expect( extensionsCallbackSpy ).toHaveBeenCalledTimes( 0 );

		registerPaymentMethodExtensionCallbacks(
			'other-woocommerce-marketplace-extension',
			{
				// eslint-disable-next-line @typescript-eslint/naming-convention
				'test-payment-method': () => true,
			}
		);

		// Now, because an extension _has_ registered a callback for test-payment-method
		// The getter will use canMakePaymentWithExtensions to create the
		// canMakePayment function.
		// Disable no-unused-expressions because we just want to test the getter
		// eslint-disable-next-line no-unused-expressions
		paymentMethod.canMakePayment;
		expect( extensionsCallbackSpy ).toHaveBeenCalledTimes( 1 );
	} );
} );
