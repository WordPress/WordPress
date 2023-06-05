/**
 * External dependencies
 */
import { render, screen, act } from '@testing-library/react';

/**
 * Internal dependencies
 */
import { usePositionRelativeToViewport } from '../use-position-relative-to-viewport';

describe( 'usePositionRelativeToViewport', () => {
	function setup() {
		const TestComponent = () => {
			const [ referenceElement, positionRelativeToViewport ] =
				usePositionRelativeToViewport();

			return (
				<>
					{ referenceElement }
					{ positionRelativeToViewport === 'below' && (
						<p data-testid="below"></p>
					) }
					{ positionRelativeToViewport === 'visible' && (
						<p data-testid="visible"></p>
					) }
					{ positionRelativeToViewport === 'above' && (
						<p data-testid="above"></p>
					) }
				</>
			);
		};

		return render( <TestComponent /> );
	}

	it( "calls IntersectionObserver's `observe` and `unobserve` events", async () => {
		const observe = jest.fn();
		const unobserve = jest.fn();

		// @ts-ignore
		IntersectionObserver = jest.fn( () => ( {
			observe,
			unobserve,
		} ) );

		const { unmount } = setup();

		expect( observe ).toHaveBeenCalled();
		unmount();
		expect( unobserve ).toHaveBeenCalled();
	} );

	it.each`
		position       | isIntersecting | top
		${ 'visible' } | ${ true }      | ${ 0 }
		${ 'below' }   | ${ false }     | ${ 10 }
		${ 'above' }   | ${ false }     | ${ 0 }
		${ 'above' }   | ${ false }     | ${ -10 }
	`(
		"position relative to viewport is '$position' with isIntersecting=$isIntersecting and top=$top",
		( { position, isIntersecting, top } ) => {
			let intersectionObserverCallback = ( entries ) => entries;

			// @ts-ignore
			IntersectionObserver = jest.fn( ( callback ) => {
				// @ts-ignore
				intersectionObserverCallback = callback;

				return {
					observe: () => void null,
					unobserve: () => void null,
				};
			} );

			setup();

			act( () => {
				intersectionObserverCallback( [
					{ isIntersecting, boundingClientRect: { top } },
				] );
			} );

			expect( screen.getAllByTestId( position ) ).toHaveLength( 1 );
		}
	);
} );
