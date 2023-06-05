/**
 * Internal dependencies
 */
import { constrainRangeSliderValues } from '../constrain-range-slider-values';

describe( 'constrainRangeSliderValues', () => {
	test.each`
		values                | min       | max       | step    | isMin      | expected
		${ [ 20, 60 ] }       | ${ 0 }    | ${ 70 }   | ${ 10 } | ${ true }  | ${ [ 20, 60 ] }
		${ [ 20, 60 ] }       | ${ 20 }   | ${ 60 }   | ${ 10 } | ${ true }  | ${ [ 20, 60 ] }
		${ [ 20, 60 ] }       | ${ 30 }   | ${ 50 }   | ${ 10 } | ${ true }  | ${ [ 30, 50 ] }
		${ [ 50, 50 ] }       | ${ 20 }   | ${ 60 }   | ${ 10 } | ${ true }  | ${ [ 50, 60 ] }
		${ [ 50, 50 ] }       | ${ 20 }   | ${ 60 }   | ${ 10 } | ${ false } | ${ [ 40, 50 ] }
		${ [ 20, 60 ] }       | ${ null } | ${ null } | ${ 10 } | ${ true }  | ${ [ 20, 60 ] }
		${ [ null, null ] }   | ${ 20 }   | ${ 60 }   | ${ 10 } | ${ true }  | ${ [ 20, 60 ] }
		${ [ '20', '60' ] }   | ${ 30 }   | ${ 50 }   | ${ 10 } | ${ true }  | ${ [ 30, 50 ] }
		${ [ -60, -20 ] }     | ${ -70 }  | ${ 0 }    | ${ 10 } | ${ true }  | ${ [ -60, -20 ] }
		${ [ -60, -20 ] }     | ${ -60 }  | ${ -20 }  | ${ 10 } | ${ true }  | ${ [ -60, -20 ] }
		${ [ -60, -20 ] }     | ${ -50 }  | ${ -30 }  | ${ 10 } | ${ true }  | ${ [ -50, -30 ] }
		${ [ -50, -50 ] }     | ${ -60 }  | ${ -20 }  | ${ 10 } | ${ true }  | ${ [ -50, -40 ] }
		${ [ -50, -50 ] }     | ${ -60 }  | ${ -20 }  | ${ 10 } | ${ false } | ${ [ -60, -50 ] }
		${ [ -60, -20 ] }     | ${ null } | ${ null } | ${ 10 } | ${ true }  | ${ [ -60, -20 ] }
		${ [ null, null ] }   | ${ -60 }  | ${ -20 }  | ${ 10 } | ${ true }  | ${ [ -60, -20 ] }
		${ [ '-60', '-20' ] } | ${ -50 }  | ${ -30 }  | ${ 10 } | ${ true }  | ${ [ -50, -30 ] }
	`(
		`correctly sets prices to its constraints with arguments values: $values, min: $min, max: $max, step: $step and isMin: $isMin`,
		( { values, min, max, step, isMin, expected } ) => {
			const constrainedValues = constrainRangeSliderValues(
				values,
				min,
				max,
				step,
				isMin
			);

			expect( constrainedValues ).toEqual( expected );
		}
	);
} );
