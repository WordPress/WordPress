/**
 * External dependencies
 */
import type { ReactNode } from 'react';
import type {
	ExpressPaymentMethodConfiguration,
	Supports,
	CanMakePaymentCallback,
	ExpressPaymentMethodConfigInstance,
} from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { getCanMakePayment } from './payment-method-config-helper';
import { assertConfigHasProperties, assertValidElement } from './assertions';

export default class ExpressPaymentMethodConfig
	implements ExpressPaymentMethodConfigInstance
{
	public name: string;
	public content: ReactNode;
	public edit: ReactNode;
	public paymentMethodId?: string;
	public supports: Supports;
	public canMakePaymentFromConfig: CanMakePaymentCallback;

	constructor( config: ExpressPaymentMethodConfiguration ) {
		// validate config
		ExpressPaymentMethodConfig.assertValidConfig( config );
		this.name = config.name;
		this.content = config.content;
		this.edit = config.edit;
		this.paymentMethodId = config.paymentMethodId || this.name;
		this.supports = {
			features: config?.supports?.features || [ 'products' ],
		};
		this.canMakePaymentFromConfig = config.canMakePayment;
	}

	// canMakePayment is calculated each time based on data that modifies outside of the class (eg: cart data).
	get canMakePayment(): CanMakePaymentCallback {
		return getCanMakePayment(
			this.canMakePaymentFromConfig,
			this.supports.features,
			this.name
		);
	}

	static assertValidConfig = (
		config: ExpressPaymentMethodConfiguration
	): void => {
		assertConfigHasProperties( config, [ 'name', 'content', 'edit' ] );
		if ( typeof config.name !== 'string' ) {
			throw new TypeError(
				'The name property for the express payment method must be a string'
			);
		}
		if (
			typeof config.paymentMethodId !== 'string' &&
			typeof config.paymentMethodId !== 'undefined'
		) {
			throw new Error(
				'The paymentMethodId property for the payment method must be a string or undefined (in which case it will be the value of the name property).'
			);
		}
		if (
			typeof config.supports?.features !== 'undefined' &&
			! Array.isArray( config.supports?.features )
		) {
			throw new Error(
				'The features property for the payment method must be an array or undefined.'
			);
		}
		assertValidElement( config.content, 'content' );
		assertValidElement( config.edit, 'edit' );
		if ( typeof config.canMakePayment !== 'function' ) {
			throw new TypeError(
				'The canMakePayment property for the express payment method must be a function.'
			);
		}
	};
}
