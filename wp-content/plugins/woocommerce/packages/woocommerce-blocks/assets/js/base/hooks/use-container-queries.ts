/**
 * External dependencies
 */
import { useResizeObserver } from '@wordpress/compose';

/**
 * Returns a resizeListener element and a class name based on its width.
 * Class names are based on the smaller of the breakpoints:
 * https://github.com/WordPress/gutenberg/tree/master/packages/viewport#usage
 * Values are also based on those breakpoints minus ~80px which is approximately
 * the left + right margin in Storefront with a font-size of 16px.
 * _Note: `useContainerQueries` will return an empty class name `` until after
 * first render_
 *
 * @return {Array} An array of {Element} `resizeListener` and {string} `className`.
 *
 * @example
 *
 * ```js
 * const App = () => {
 * 	const [ resizeListener, containerClassName ] = useContainerQueries();
 *
 * 	return (
 * 		<div className={ containerClassName }>
 * 			{ resizeListener }
 * 			Your content here
 * 		</div>
 * 	);
 * };
 * ```
 */
export const useContainerQueries = (): [ React.ReactElement, string ] => {
	const [ resizeListener, { width } ] = useResizeObserver();

	let className = '';
	if ( width > 700 ) {
		className = 'is-large';
	} else if ( width > 520 ) {
		className = 'is-medium';
	} else if ( width > 400 ) {
		className = 'is-small';
	} else if ( width ) {
		className = 'is-mobile';
	}

	return [ resizeListener, className ];
};
