/**
 * External dependencies
 */
import deprecated from '@wordpress/deprecated';
import type { ReactNode } from 'react';
import type {
	PaymentMethodConfiguration,
	Supports,
	CanMakePaymentCallback,
	PaymentMethodConfigInstance,
	PaymentMethodIcons,
} from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { getCanMakePayment } from './payment-method-config-helper';
import {
	assertConfigHasProperties,
	assertValidElement,
	assertValidElementOrString,
} from './assertions';

const NullComponent = () => {
	return null;
};

export default class PaymentMethodConfig
	implements PaymentMethodConfigInstance
{
	public name: string;
	public content: ReactNode;
	public edit: ReactNode;
	public paymentMethodId?: string;
	public supports: Supports;
	public icons: null | PaymentMethodIcons;
	public label: ReactNode;
	public ariaLabel: string;
	public placeOrderButtonLabel?: string;
	public savedTokenComponent?: ReactNode | null;
	public canMakePaymentFromConfig: CanMakePaymentCallback;

	constructor( config: PaymentMethodConfiguration ) {
		// validate config
		PaymentMethodConfig.assertValidConfig( config );
		this.name = config.name;
		this.label = config.label;
		this.placeOrderButtonLabel = config.placeOrderButtonLabel;
		this.ariaLabel = config.ariaLabel;
		this.content = config.content;
		this.savedTokenComponent = config.savedTokenComponent;
		this.icons = config.icons || null;
		this.edit = config.edit;
		this.paymentMethodId = config.paymentMethodId || this.name;
		this.supports = {
			showSavedCards:
				config?.supports?.showSavedCards ||
				config?.supports?.savePaymentInfo || // Kept for backward compatibility if methods still pass this when registering.
				false,
			showSaveOption: config?.supports?.showSaveOption || false,
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

	static assertValidConfig = ( config: PaymentMethodConfiguration ): void => {
		// set default for optional
		config.savedTokenComponent = config.savedTokenComponent || (
			<NullComponent />
		);
		assertConfigHasProperties( config, [
			'name',
			'label',
			'ariaLabel',
			'content',
			'edit',
			'canMakePayment',
		] );
		if ( typeof config.name !== 'string' ) {
			throw new Error(
				'The name property for the payment method must be a string'
			);
		}
		if (
			typeof config.icons !== 'undefined' &&
			! Array.isArray( config.icons ) &&
			config.icons !== null
		) {
			throw new Error(
				'The icons property for the payment method must be an array or null.'
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
			typeof config.placeOrderButtonLabel !== 'string' &&
			typeof config.placeOrderButtonLabel !== 'undefined'
		) {
			throw new TypeError(
				'The placeOrderButtonLabel property for the payment method must be a string'
			);
		}
		assertValidElementOrString( config.label, 'label' );
		assertValidElement( config.content, 'content' );
		assertValidElement( config.edit, 'edit' );
		assertValidElement( config.savedTokenComponent, 'savedTokenComponent' );
		if ( typeof config.ariaLabel !== 'string' ) {
			throw new TypeError(
				'The ariaLabel property for the payment method must be a string'
			);
		}
		if ( typeof config.canMakePayment !== 'function' ) {
			throw new TypeError(
				'The canMakePayment property for the payment method must be a function.'
			);
		}
		if (
			typeof config.supports?.showSavedCards !== 'undefined' &&
			typeof config.supports?.showSavedCards !== 'boolean'
		) {
			throw new TypeError(
				'If the payment method includes the `supports.showSavedCards` property, it must be a boolean'
			);
		}
		if ( typeof config.supports?.savePaymentInfo !== 'undefined' ) {
			deprecated(
				'Passing savePaymentInfo when registering a payment method.',
				{
					alternative: 'Pass showSavedCards and showSaveOption',
					plugin: 'woocommerce-gutenberg-products-block',
					link: 'https://github.com/woocommerce/woocommerce-gutenberg-products-block/pull/3686',
				}
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
		if (
			typeof config.supports?.showSaveOption !== 'undefined' &&
			typeof config.supports?.showSaveOption !== 'boolean'
		) {
			throw new TypeError(
				'If the payment method includes the `supports.showSaveOption` property, it must be a boolean'
			);
		}
	};
}
