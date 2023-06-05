/**
 * External dependencies
 */
import type { NumberFormatValues } from 'react-number-format';

/**
  Check if that the value is minor than the max price and greater than 0.
 */
export const isValidMaxValue =
	( {
		maxConstraint,
		minorUnit,
	}: {
		maxConstraint: number;
		minorUnit: number;
	} ) =>
	( { floatValue }: NumberFormatValues ): boolean => {
		const maxPrice = maxConstraint / 10 ** minorUnit;

		return (
			floatValue !== undefined && floatValue <= maxPrice && floatValue > 0
		);
	};

/**
  Check if that the value is minor than the max price and greater than 0.
 */
export const isValidMinValue =
	( {
		minConstraint,
		currentMaxValue,
		minorUnit,
	}: {
		minConstraint: number;
		currentMaxValue: number;
		minorUnit: number;
	} ) =>
	( { floatValue }: NumberFormatValues ): boolean => {
		const minPrice = minConstraint / 10 ** minorUnit;
		const currentMaxPrice = currentMaxValue / 10 ** minorUnit;

		return (
			floatValue !== undefined &&
			floatValue >= minPrice &&
			floatValue < currentMaxPrice
		);
	};
