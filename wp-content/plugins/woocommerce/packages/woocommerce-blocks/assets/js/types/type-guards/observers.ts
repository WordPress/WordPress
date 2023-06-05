/**
 * External dependencies
 */
import { ObserverResponse } from '@woocommerce/base-context';
import { isObject, objectHasProp } from '@woocommerce/types';

/**
 * Whether the passed object is an ObserverResponse.
 */
export const isObserverResponse = (
	response: unknown
): response is ObserverResponse => {
	return isObject( response ) && objectHasProp( response, 'type' );
};
