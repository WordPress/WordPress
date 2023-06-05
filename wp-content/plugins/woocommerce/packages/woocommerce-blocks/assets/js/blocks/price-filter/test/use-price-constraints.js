// We need to disable the following eslint check as it's only applicable
// to testing-library/react not `react-test-renderer` used here
/* eslint-disable testing-library/await-async-query */
/**
 * External dependencies
 */
import TestRenderer from 'react-test-renderer';

/**
 * Internal dependencies
 */
import { usePriceConstraint } from '../use-price-constraints';
import { ROUND_UP, ROUND_DOWN } from '../constants';

describe( 'usePriceConstraints', () => {
	const TestComponent = ( { price } ) => {
		const maxPriceConstraint = usePriceConstraint( price, 2, ROUND_UP );
		const minPriceConstraint = usePriceConstraint( price, 2, ROUND_DOWN );
		return (
			<div
				minPriceConstraint={ minPriceConstraint }
				maxPriceConstraint={ maxPriceConstraint }
			/>
		);
	};

	it( 'max price constraint should be updated when new price is set', () => {
		const renderer = TestRenderer.create(
			<TestComponent price={ 1000 } />
		);
		const container = renderer.root.findByType( 'div' );

		expect( container.props.maxPriceConstraint ).toBe( 1000 );

		renderer.update( <TestComponent price={ 2000 } /> );

		expect( container.props.maxPriceConstraint ).toBe( 2000 );
	} );

	it( 'min price constraint should be updated when new price is set', () => {
		const renderer = TestRenderer.create(
			<TestComponent price={ 1000 } />
		);
		const container = renderer.root.findByType( 'div' );

		expect( container.props.minPriceConstraint ).toBe( 1000 );

		renderer.update( <TestComponent price={ 2000 } /> );

		expect( container.props.minPriceConstraint ).toBe( 2000 );
	} );

	it( 'previous price constraint should be preserved when new price is not a infinite number', () => {
		const renderer = TestRenderer.create(
			<TestComponent price={ 1000 } />
		);
		const container = renderer.root.findByType( 'div' );

		expect( container.props.maxPriceConstraint ).toBe( 1000 );

		renderer.update( <TestComponent price={ Infinity } /> );

		expect( container.props.maxPriceConstraint ).toBe( 1000 );
	} );

	it( 'max price constraint should be higher if the price is decimal', () => {
		const renderer = TestRenderer.create(
			<TestComponent price={ 1099 } />
		);
		const container = renderer.root.findByType( 'div' );

		expect( container.props.maxPriceConstraint ).toBe( 2000 );

		renderer.update( <TestComponent price={ 1999 } /> );

		expect( container.props.maxPriceConstraint ).toBe( 2000 );
	} );

	it( 'min price constraint should be lower if the price is decimal', () => {
		const renderer = TestRenderer.create( <TestComponent price={ 999 } /> );
		const container = renderer.root.findByType( 'div' );

		expect( container.props.minPriceConstraint ).toBe( 0 );

		renderer.update( <TestComponent price={ 1999 } /> );

		expect( container.props.minPriceConstraint ).toBe( 1000 );
	} );
} );
