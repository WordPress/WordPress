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
import withTransformSingleSelectToMultipleSelect from '../with-transform-single-select-to-multiple-select';

const TestComponent = withTransformSingleSelectToMultipleSelect( ( props ) => {
	return <div selected={ props.selected } />;
} );

describe( 'withTransformSingleSelectToMultipleSelect Component', () => {
	describe( 'when the API returns an error', () => {
		it( 'converts the selected value into an array', () => {
			const selected = 123;
			const renderer = TestRenderer.create(
				<TestComponent selected={ selected } />
			);
			const props = renderer.root.findByType( 'div' ).props;
			expect( props.selected ).toEqual( [ selected ] );
		} );

		it( 'passes an empty array as the selected prop if selected was null', () => {
			const renderer = TestRenderer.create(
				<TestComponent selected={ null } />
			);
			const props = renderer.root.findByType( 'div' ).props;
			expect( props.selected ).toEqual( [] );
		} );
	} );
} );
