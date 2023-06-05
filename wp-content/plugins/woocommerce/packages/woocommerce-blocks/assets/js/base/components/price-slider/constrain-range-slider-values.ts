/**
 * Validate a min and max value for a range slider against defined constraints (min, max, step).
 *
 * @return {[number, number]} Validated and updated min/max values that fit within the range slider constraints.
 */
export const constrainRangeSliderValues = (
	/**
	 * Tuple containing min and max values.
	 */
	values: [ number, number ],
	/**
	 * Min allowed value for the sliders.
	 */
	min?: number | null,
	/**
	 * Max allowed value for the sliders.
	 */
	max?: number | null,
	/**
	 * Step value for the sliders.
	 */
	step = 1,
	/**
	 * Whether we're currently interacting with the min range slider or not, so we update the correct values.
	 */
	isMin = false
): [ number, number ] => {
	let [ minValue, maxValue ] = values;

	const isFinite = ( n: number | undefined ): n is number =>
		Number.isFinite( n );

	if ( ! isFinite( minValue ) ) {
		minValue = min || 0;
	}

	if ( ! isFinite( maxValue ) ) {
		maxValue = max || step;
	}

	if ( isFinite( min ) && min > minValue ) {
		minValue = min;
	}

	if ( isFinite( max ) && max <= minValue ) {
		minValue = max - step;
	}

	if ( isFinite( min ) && min >= maxValue ) {
		maxValue = min + step;
	}

	if ( isFinite( max ) && max < maxValue ) {
		maxValue = max;
	}

	if ( ! isMin && minValue >= maxValue ) {
		minValue = maxValue - step;
	}

	if ( isMin && maxValue <= minValue ) {
		maxValue = minValue + step;
	}

	return [ minValue, maxValue ];
};
