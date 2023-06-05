/**
 * External dependencies
 */
import { isString, isObject } from '@woocommerce/types';

export const parseStyle = ( style: unknown ): Record< string, unknown > => {
	if ( isString( style ) ) {
		return JSON.parse( style ) || {};
	}

	if ( isObject( style ) ) {
		return style;
	}

	return {};
};
