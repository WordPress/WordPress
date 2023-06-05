/**
 * Internal dependencies
 */
import type { StockStatus, StockStatusOptions } from '../type-defs';
import { isObject } from './object';

export const isStockStatusQueryCollection = (
	value: unknown
): value is StockStatus[] => {
	return (
		Array.isArray( value ) &&
		value.every( ( v ) =>
			[ 'instock', 'outofstock', 'onbackorder' ].includes( v )
		)
	);
};

export const isStockStatusOptions = (
	value: unknown
): value is StockStatusOptions => {
	return (
		isObject( value ) &&
		Object.keys( value ).every( ( v ) =>
			[ 'instock', 'outofstock', 'onbackorder' ].includes( v )
		)
	);
};
