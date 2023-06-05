/**
 * External dependencies
 */
import { isValidElement } from '@wordpress/element';
import type { ReactNode } from 'react';
import type {
	PaymentMethodConfiguration,
	ExpressPaymentMethodConfiguration,
} from '@woocommerce/types';

export const assertValidPaymentMethodComponent = (
	component: () => unknown,
	componentName: string
): void => {
	if ( typeof component !== 'function' ) {
		throw new TypeError(
			`The ${ componentName } property for the payment method must be a functional component`
		);
	}
};

export const assertValidElement = (
	element: ReactNode,
	elementName: string
): void => {
	if ( element !== null && ! isValidElement( element ) ) {
		throw new TypeError(
			`The ${ elementName } property for the payment method must be a React element or null.`
		);
	}
};

export const assertValidElementOrString = (
	element: ReactNode,
	elementName: string
): void => {
	if (
		element !== null &&
		! isValidElement( element ) &&
		typeof element !== 'string'
	) {
		throw new TypeError(
			`The ${ elementName } property for the payment method must be a React element, a string, or null.`
		);
	}
};

export const assertConfigHasProperties = (
	config: ExpressPaymentMethodConfiguration | PaymentMethodConfiguration,
	expectedProperties: string[] = []
): void => {
	const missingProperties = expectedProperties.reduce(
		( acc: string[], property: string ) => {
			if ( ! config.hasOwnProperty( property ) ) {
				acc.push( property );
			}
			return acc;
		},
		[]
	);
	if ( missingProperties.length > 0 ) {
		const message =
			'The payment method configuration object is missing the following properties:';
		throw new TypeError( message + missingProperties.join( ', ' ) );
	}
};
