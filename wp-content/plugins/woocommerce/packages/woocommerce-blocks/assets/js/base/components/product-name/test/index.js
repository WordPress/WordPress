/**
 * External dependencies
 */
import TestRenderer from 'react-test-renderer';

/**
 * Internal dependencies
 */
import ProductName from '..';

describe( 'ProductName', () => {
	test( 'should not render a link if disabled is true', () => {
		const component = TestRenderer.create(
			<ProductName disabled={ true } name="Test product" permalink="/" />
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should render a link if disabled is false', () => {
		const component = TestRenderer.create(
			<ProductName disabled={ false } name="Test product" permalink="/" />
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should render a link if disabled is not defined', () => {
		const component = TestRenderer.create(
			<ProductName name="Test product" permalink="/" />
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should merge classes and props', () => {
		const component = TestRenderer.create(
			<ProductName
				className="lorem-ipsum"
				name="Test product"
				permalink="/"
				rel="nofollow"
			/>
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
