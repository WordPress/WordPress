/**
 * Internal dependencies
 */
import { assertValidContextValue } from '../utils';

describe( 'assertValidContextValue', () => {
	const contextName = 'testContext';
	const validationMap = {
		cheeseburger: {
			required: false,
			type: 'string',
		},
		amountKetchup: {
			required: true,
			type: 'number',
		},
	};
	it.each`
		testValue
		${ {} }
		${ 10 }
		${ { amountKetchup: '10' } }
	`(
		'The value of $testValue is expected to trigger an Error',
		( { testValue } ) => {
			const invokeTest = () => {
				assertValidContextValue(
					contextName,
					validationMap,
					testValue
				);
			};
			expect( invokeTest ).toThrow();
		}
	);
	it.each`
		testValue
		${ { amountKetchup: 20 } }
		${ { cheeseburger: 'fries', amountKetchup: 20 } }
	`(
		'The value of $testValue is not expected to trigger an Error',
		( { testValue } ) => {
			const invokeTest = () => {
				assertValidContextValue(
					contextName,
					validationMap,
					testValue
				);
			};
			expect( invokeTest ).not.toThrow();
		}
	);
} );
