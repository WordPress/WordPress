/**
 * External dependencies
 */
import TestRenderer from 'react-test-renderer';

/**
 * Internal dependencies
 */
import ProductDetails from '..';

describe( 'ProductDetails', () => {
	test( 'should render details', () => {
		const details = [
			{ name: 'Lorem', value: 'Ipsum' },
			{ name: 'LOREM', value: 'Ipsum', display: 'IPSUM' },
			{ value: 'Ipsum' },
		];
		const component = TestRenderer.create(
			<ProductDetails details={ details } />
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should not render hidden details', () => {
		const details = [
			{ name: 'Lorem', value: 'Ipsum', hidden: true },
			{ name: 'LOREM', value: 'Ipsum', display: 'IPSUM' },
		];
		const component = TestRenderer.create(
			<ProductDetails details={ details } />
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should not rendering anything if all details are hidden', () => {
		const details = [
			{ name: 'Lorem', value: 'Ipsum', hidden: true },
			{ name: 'LOREM', value: 'Ipsum', display: 'IPSUM', hidden: true },
		];
		const component = TestRenderer.create(
			<ProductDetails details={ details } />
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should not rendering anything if details is an empty array', () => {
		const details = [];
		const component = TestRenderer.create(
			<ProductDetails details={ details } />
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
