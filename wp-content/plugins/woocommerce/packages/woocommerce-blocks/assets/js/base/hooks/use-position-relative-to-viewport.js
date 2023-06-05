/**
 * External dependencies
 */
import { useRef, useLayoutEffect, useState } from '@wordpress/element';

/** @typedef {import('react')} React */

/** @type {React.CSSProperties} */
const style = {
	bottom: 0,
	left: 0,
	opacity: 0,
	pointerEvents: 'none',
	position: 'absolute',
	right: 0,
	top: 0,
	zIndex: -1,
};

/**
 * Returns an element and a string (`above`, `visible` or `below`) based on the
 * element position relative to the viewport.
 * _Note: `usePositionRelativeToViewport` will return an empty position (``)
 * until after first render_
 *
 * @return {Array} An array of {Element} `referenceElement` and {string} `positionRelativeToViewport`.
 *
 * @example
 *
 * ```js
 * const App = () => {
 * 	const [ referenceElement, positionRelativeToViewport ] = useContainerQueries();
 *
 * 	return (
 * 		<>
 * 			{ referenceElement }
 * 			{ positionRelativeToViewport === 'below' && <p>Reference element is below the viewport.</p> }
 * 			{ positionRelativeToViewport === 'visible' && <p>Reference element is visible in the viewport.</p> }
 * 			{ positionRelativeToViewport === 'above' && <p>Reference element is above the viewport.</p> }
 * 		</>
 * 	);
 * };
 * ```
 */
export const usePositionRelativeToViewport = () => {
	const [ positionRelativeToViewport, setPositionRelativeToViewport ] =
		useState( '' );
	const referenceElementRef = useRef( null );
	const intersectionObserver = useRef(
		new IntersectionObserver(
			( entries ) => {
				if ( entries[ 0 ].isIntersecting ) {
					setPositionRelativeToViewport( 'visible' );
				} else {
					setPositionRelativeToViewport(
						entries[ 0 ].boundingClientRect.top > 0
							? 'below'
							: 'above'
					);
				}
			},
			{ threshold: 1.0 }
		)
	);

	useLayoutEffect( () => {
		const referenceElementNode = referenceElementRef.current;
		const observer = intersectionObserver.current;

		if ( referenceElementNode ) {
			observer.observe( referenceElementNode );
		}

		return () => {
			observer.unobserve( referenceElementNode );
		};
	}, [] );

	const referenceElement = (
		<div aria-hidden={ true } ref={ referenceElementRef } style={ style } />
	);

	return [ referenceElement, positionRelativeToViewport ];
};
