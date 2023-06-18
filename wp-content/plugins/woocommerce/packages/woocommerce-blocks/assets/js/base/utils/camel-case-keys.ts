/**
 * External dependencies
 */
import { camelCase } from 'change-case';

/**
 * Internal dependencies
 */
import { mapKeys } from './map-keys';

export const camelCaseKeys = ( obj: object ) =>
	mapKeys( obj, ( _, key ) => camelCase( key ) );
