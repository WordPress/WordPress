/**
 * External dependencies
 */
import TestRenderer from 'react-test-renderer';

/**
 * Internal dependencies
 */
import Label from '../';

describe( 'Label', () => {
	describe( 'without wrapperElement', () => {
		test( 'should render both label and screen reader label', () => {
			const component = TestRenderer.create(
				<Label label="Lorem" screenReaderLabel="Ipsum" />
			);

			expect( component.toJSON() ).toMatchSnapshot();
		} );

		test( 'should render only the label', () => {
			const component = TestRenderer.create( <Label label="Lorem" /> );

			expect( component.toJSON() ).toMatchSnapshot();
		} );

		test( 'should render only the screen reader label', () => {
			const component = TestRenderer.create(
				<Label screenReaderLabel="Ipsum" />
			);

			expect( component.toJSON() ).toMatchSnapshot();
		} );
	} );

	describe( 'with wrapperElement', () => {
		test( 'should render both label and screen reader label', () => {
			const component = TestRenderer.create(
				<Label
					label="Lorem"
					screenReaderLabel="Ipsum"
					wrapperElement="label"
					wrapperProps={ {
						className: 'foo-bar',
						'data-foo': 'bar',
					} }
				/>
			);

			expect( component.toJSON() ).toMatchSnapshot();
		} );

		test( 'should render only the label', () => {
			const component = TestRenderer.create(
				<Label
					label="Lorem"
					wrapperElement="label"
					wrapperProps={ {
						className: 'foo-bar',
						'data-foo': 'bar',
					} }
				/>
			);

			expect( component.toJSON() ).toMatchSnapshot();
		} );

		test( 'should render only the screen reader label', () => {
			const component = TestRenderer.create(
				<Label
					screenReaderLabel="Ipsum"
					wrapperElement="label"
					wrapperProps={ {
						className: 'foo-bar',
						'data-foo': 'bar',
					} }
				/>
			);

			expect( component.toJSON() ).toMatchSnapshot();
		} );
	} );
} );
