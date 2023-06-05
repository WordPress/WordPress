/**
 * External dependencies
 */
import TestRenderer from 'react-test-renderer';

/**
 * Internal dependencies
 */
import { Chip, RemovableChip } from '..';

describe( 'Chip', () => {
	test( 'should render text', () => {
		const component = TestRenderer.create( <Chip text="Test" /> );

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should render nodes as the text', () => {
		const component = TestRenderer.create(
			<Chip text={ <h1>Test</h1> } />
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should render defined radius', () => {
		const component = TestRenderer.create(
			<Chip text="Test" radius="large" />
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should render screen reader text', () => {
		const component = TestRenderer.create(
			<Chip text="Test" screenReaderText="Test 2" />
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should render children nodes', () => {
		const component = TestRenderer.create(
			<Chip text="Test">Lorem Ipsum</Chip>
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	describe( 'with custom wrapper', () => {
		test( 'should render a chip made up of a div instead of a li', () => {
			const component = TestRenderer.create(
				<Chip text="Test" element="div" />
			);

			expect( component.toJSON() ).toMatchSnapshot();
		} );
	} );
} );

describe( 'RemovableChip', () => {
	test( 'should render text and the remove button', () => {
		const component = TestRenderer.create( <RemovableChip text="Test" /> );

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should render with disabled remove button', () => {
		const component = TestRenderer.create(
			<RemovableChip text="Test" disabled={ true } />
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should render custom aria label', () => {
		const component = TestRenderer.create(
			<RemovableChip text={ <h1>Test</h1> } ariaLabel="Aria test" />
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should render default aria label if text is a node', () => {
		const component = TestRenderer.create(
			<RemovableChip text={ <h1>Test</h1> } screenReaderText="Test 2" />
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'should render screen reader text aria label', () => {
		const component = TestRenderer.create(
			<RemovableChip text="Test" screenReaderText="Test 2" />
		);

		expect( component.toJSON() ).toMatchSnapshot();
	} );

	describe( 'with removeOnAnyClick', () => {
		test( 'should be a button when removeOnAnyClick is set to true', () => {
			const component = TestRenderer.create(
				<RemovableChip text="Test" removeOnAnyClick={ true } />
			);

			expect( component.toJSON() ).toMatchSnapshot();
		} );
	} );
} );
