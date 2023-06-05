/**
 * External dependencies
 */
import deprecated from '@wordpress/deprecated';
import type {
	PaymentMethodConfiguration,
	ExpressPaymentMethodConfiguration,
	CanMakePaymentExtensionCallback,
	PaymentMethodConfigInstance,
	PaymentMethods,
	ExpressPaymentMethods,
} from '@woocommerce/types';
import { dispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { default as PaymentMethodConfig } from './payment-method-config';
import { default as ExpressPaymentMethodConfig } from './express-payment-method-config';
import { canMakePaymentExtensionsCallbacks } from './extensions-config';

import { STORE_KEY as PAYMENT_STORE_KEY } from '../../data/payment/constants'; // Full path here because otherwise there's a circular dependency.

type LegacyRegisterPaymentMethodFunction = ( config: unknown ) => unknown;
type LegacyRegisterExpressPaymentMethodFunction = (
	config: unknown
) => unknown;
const paymentMethods: PaymentMethods = {};
const expressPaymentMethods: ExpressPaymentMethods = {};

/**
 * Register a regular payment method.
 */
export const registerPaymentMethod = (
	options: PaymentMethodConfiguration | LegacyRegisterPaymentMethodFunction
): void => {
	let paymentMethodConfig: PaymentMethodConfigInstance | unknown;
	if ( typeof options === 'function' ) {
		// Legacy fallback for previous API, where client passes a function:
		// registerPaymentMethod( ( Config ) => new Config( options ) );
		paymentMethodConfig = options( PaymentMethodConfig );
		deprecated( 'Passing a callback to registerPaymentMethod()', {
			alternative: 'a config options object',
			plugin: 'woocommerce-gutenberg-products-block',
			link: 'https://github.com/woocommerce/woocommerce-gutenberg-products-block/pull/3404',
		} );
	} else {
		paymentMethodConfig = new PaymentMethodConfig( options );
	}
	if ( paymentMethodConfig instanceof PaymentMethodConfig ) {
		paymentMethods[ paymentMethodConfig.name ] = paymentMethodConfig;
	}
};

/**
 * Register an express payment method.
 */
export const registerExpressPaymentMethod = (
	options:
		| ExpressPaymentMethodConfiguration
		| LegacyRegisterExpressPaymentMethodFunction
): void => {
	let paymentMethodConfig;
	if ( typeof options === 'function' ) {
		// Legacy fallback for previous API, where client passes a function:
		// registerExpressPaymentMethod( ( Config ) => new Config( options ) );
		paymentMethodConfig = options( ExpressPaymentMethodConfig );
		deprecated( 'Passing a callback to registerExpressPaymentMethod()', {
			alternative: 'a config options object',
			plugin: 'woocommerce-gutenberg-products-block',
			link: 'https://github.com/woocommerce/woocommerce-gutenberg-products-block/pull/3404',
		} );
	} else {
		paymentMethodConfig = new ExpressPaymentMethodConfig( options );
	}
	if ( paymentMethodConfig instanceof ExpressPaymentMethodConfig ) {
		expressPaymentMethods[ paymentMethodConfig.name ] = paymentMethodConfig;
	}
};

/**
 * Allows extension to register callbacks for specific payment methods to determine if they can make payments
 */
export const registerPaymentMethodExtensionCallbacks = (
	namespace: string,
	callbacks: Record< string, CanMakePaymentExtensionCallback >
): void => {
	if ( canMakePaymentExtensionsCallbacks[ namespace ] ) {
		// eslint-disable-next-line no-console
		console.error(
			`The namespace provided to registerPaymentMethodExtensionCallbacks must be unique. Callbacks have already been registered for the ${ namespace } namespace.`
		);
	} else {
		// Set namespace up as an empty object.
		canMakePaymentExtensionsCallbacks[ namespace ] = {};

		Object.entries( callbacks ).forEach(
			( [ paymentMethodName, callback ] ) => {
				if ( typeof callback === 'function' ) {
					canMakePaymentExtensionsCallbacks[ namespace ][
						paymentMethodName
					] = callback;
				} else {
					// eslint-disable-next-line no-console
					console.error(
						`All callbacks provided to registerPaymentMethodExtensionCallbacks must be functions. The callback for the ${ paymentMethodName } payment method in the ${ namespace } namespace was not a function.`
					);
				}
			}
		);
	}
};

export const __experimentalDeRegisterPaymentMethod = (
	paymentMethodName: string
): void => {
	delete paymentMethods[ paymentMethodName ];
	const { __internalRemoveAvailablePaymentMethod } =
		dispatch( PAYMENT_STORE_KEY );
	__internalRemoveAvailablePaymentMethod( paymentMethodName );
};

export const __experimentalDeRegisterExpressPaymentMethod = (
	paymentMethodName: string
): void => {
	delete expressPaymentMethods[ paymentMethodName ];
	const { __internalRemoveAvailableExpressPaymentMethod } =
		dispatch( PAYMENT_STORE_KEY );
	__internalRemoveAvailableExpressPaymentMethod( paymentMethodName );
};

export const getPaymentMethods = (): PaymentMethods => {
	return paymentMethods;
};

export const getExpressPaymentMethods = (): ExpressPaymentMethods => {
	return expressPaymentMethods;
};
