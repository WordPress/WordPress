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
import withScrollToTop from '../index';

const TestComponent = withScrollToTop( ( props ) => (
	<span { ...props }>
		<button />
	</span>
) );

const focusedMock = jest.fn();
const scrollIntoViewMock = jest.fn();

const mockedButton = {
	focus: focusedMock,
	scrollIntoView: scrollIntoViewMock,
};
const render = ( { inView } ) => {
	const getBoundingClientRect = () => ( {
		bottom: inView ? 0 : -10,
	} );
	return TestRenderer.create( <TestComponent />, {
		createNodeMock: ( element ) => {
			if ( element.type === 'button' ) {
				return {
					...mockedButton,
					getBoundingClientRect,
				};
			}
			if ( element.type === 'div' ) {
				return {
					getBoundingClientRect,
					parentElement: {
						querySelectorAll: () => [
							{ ...mockedButton, getBoundingClientRect },
						],
					},
					scrollIntoView: scrollIntoViewMock,
				};
			}
			return null;
		},
	} );
};

describe( 'withScrollToTop Component', () => {
	afterEach( () => {
		focusedMock.mockReset();
		scrollIntoViewMock.mockReset();
	} );

	describe( 'if component is not in view', () => {
		beforeEach( () => {
			const renderer = render( { inView: false } );
			const props = renderer.root.findByType( 'span' ).props;
			props.scrollToTop( {
				focusableSelector: 'button',
			} );
		} );

		it( 'scrolls to top of the component when scrollToTop is called', () => {
			expect( scrollIntoViewMock ).toHaveBeenCalledTimes( 1 );
		} );

		it( 'moves focus to top of the component when scrollToTop is called', () => {
			expect( focusedMock ).toHaveBeenCalledTimes( 1 );
		} );
	} );

	describe( 'if component is in view', () => {
		beforeEach( () => {
			const renderer = render( { inView: true } );
			const props = renderer.root.findByType( 'span' ).props;
			props.scrollToTop( {
				focusableSelector: 'button',
			} );
		} );

		it( "doesn't scroll to top of the component when scrollToTop is called", () => {
			expect( scrollIntoViewMock ).toHaveBeenCalledTimes( 0 );
		} );

		it( 'moves focus to top of the component when scrollToTop is called', () => {
			expect( focusedMock ).toHaveBeenCalledTimes( 1 );
		} );
	} );
} );
