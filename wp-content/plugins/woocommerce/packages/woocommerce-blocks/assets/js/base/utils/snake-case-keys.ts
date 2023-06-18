/**
 * External dependencies
 */
import { snakeCase } from 'change-case';

/**
 * Internal dependencies
 */
import { mapKeys } from './map-keys';

export const snakeCaseKeys = ( obj: object ) =>
	mapKeys( obj, ( _, key ) => snakeCase( key ) );
