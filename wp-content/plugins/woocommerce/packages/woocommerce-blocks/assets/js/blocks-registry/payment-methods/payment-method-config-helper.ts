/**
 * External dependencies
 */
import type {
	CanMakePaymentCallback,
	CanMakePaymentExtensionCallback,
} from '@woocommerce/types';

/**
 * Internal dependencies
 */
import {
	NamespacedCanMakePaymentExtensionsCallbacks,
	PaymentMethodName,
	ExtensionNamespace,
	extensionsConfig,
} from './extensions-config';

// Filter out payment methods by supported features and cart requirement.
export const canMakePaymentWithFeaturesCheck =
	(
		canMakePayment: CanMakePaymentCallback,
		features: string[]
	): CanMakePaymentCallback =>
	( canPayArgument ) => {
		const requirements = canPayArgument?.paymentRequirements || [];
		const featuresSupportRequirements = requirements.every(
			( requirement ) => features.includes( requirement )
		);
		return featuresSupportRequirements && canMakePayment( canPayArgument );
	};

// Filter out payment methods by callbacks registered by extensions.
export const canMakePaymentWithExtensions =
	(
		canMakePayment: CanMakePaymentCallback,
		extensionsCallbacks: NamespacedCanMakePaymentExtensionsCallbacks,
		paymentMethodName: PaymentMethodName
	): CanMakePaymentCallback =>
	( canPayArgument ) => {
		// Validate whether the payment method is available based on its own criteria first.
		let canPay = canMakePayment( canPayArgument );

		if ( canPay ) {
			// Gather all callbacks for paymentMethodName.
			const namespacedCallbacks: Record<
				ExtensionNamespace,
				CanMakePaymentExtensionCallback
			> = {};

			Object.entries( extensionsCallbacks ).forEach(
				( [ namespace, callbacks ] ) => {
					if (
						! ( paymentMethodName in callbacks ) ||
						typeof callbacks[ paymentMethodName ] !== 'function'
					) {
						return;
					}
					namespacedCallbacks[ namespace ] =
						callbacks[ paymentMethodName ];
				}
			);

			canPay = Object.keys( namespacedCallbacks ).every(
				( namespace ) => {
					try {
						return namespacedCallbacks[ namespace ](
							canPayArgument
						);
					} catch ( err ) {
						// eslint-disable-next-line no-console
						console.error(
							`Error when executing callback for ${ paymentMethodName } in ${ namespace }`,
							err
						);
						// .every() expects a return value at the end of every arrow function and
						// this ensures that the error is ignored when computing the whole result.
						return true;
					}
				}
			);
		}

		return canPay;
	};

export const getCanMakePayment = (
	canMakePayment: CanMakePaymentCallback,
	features: string[],
	paymentMethodName: string
): CanMakePaymentCallback => {
	const canPay = canMakePaymentWithFeaturesCheck( canMakePayment, features );
	// Loop through all callbacks to check if there are any registered for this payment method.
	return (
		Object.values( extensionsConfig.canMakePayment ) as Record<
			PaymentMethodName,
			CanMakePaymentCallback
		>[]
	 ).some( ( callbacks ) => paymentMethodName in callbacks )
		? canMakePaymentWithExtensions(
				canPay,
				extensionsConfig.canMakePayment,
				paymentMethodName
		  )
		: canPay;
};
