/**
 * External dependencies
 */
import { usePrevious } from '@woocommerce/base-hooks';

/**
 * Internal dependencies
 */
import { ROUND_UP, ROUND_DOWN } from './constants';

/**
 * Return the price constraint.
 *
 * @param {string}              price     Price in minor unit, e.g. cents.
 * @param {number}              minorUnit Price minor unit (number of digits after the decimal separator).
 * @param {ROUND_UP|ROUND_DOWN} direction Rounding flag whether we round up or down.
 */
export const usePriceConstraint = (
	price: string,
	minorUnit: number,
	direction: string
) => {
	const step = 10 * 10 ** minorUnit;
	let currentConstraint = null;
	const parsedPrice = parseFloat( price );

	if ( ! isNaN( parsedPrice ) ) {
		if ( direction === ROUND_UP ) {
			currentConstraint = Math.ceil( parsedPrice / step ) * step;
		} else if ( direction === ROUND_DOWN ) {
			currentConstraint = Math.floor( parsedPrice / step ) * step;
		}
	}

	const previousConstraint = usePrevious(
		currentConstraint,
		Number.isFinite
	);
	return Number.isFinite( currentConstraint )
		? currentConstraint
		: previousConstraint;
};

/**
 * Return the min and max price constraints.
 *
 * @param {Object}           priceData           Price data object.
 * @param {string|undefined} priceData.minPrice  Min price in minor unit, e.g. cents.
 * @param {string|undefined} priceData.maxPrice  Max price in minor unit, e.g. cents.
 * @param {number}           priceData.minorUnit Price minor unit (number of digits after the decimal separator).
 */
export default ( {
	minPrice,
	maxPrice,
	minorUnit,
}: {
	minPrice: string | undefined;
	maxPrice: string | undefined;
	minorUnit: number;
} ) => {
	return {
		minConstraint: usePriceConstraint(
			minPrice || '',
			minorUnit,
			ROUND_DOWN
		),
		maxConstraint: usePriceConstraint(
			maxPrice || '',
			minorUnit,
			ROUND_UP
		),
	};
};
