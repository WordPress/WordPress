/**
 * External dependencies
 */
import { useRef, useLayoutEffect } from '@wordpress/element';
import { focus } from '@wordpress/dom';
import { useDebouncedCallback } from 'use-debounce';

/**
 * Names of control nodes which need to be disabled.
 */
const FOCUSABLE_NODE_NAMES = [
	'BUTTON',
	'FIELDSET',
	'INPUT',
	'OPTGROUP',
	'OPTION',
	'SELECT',
	'TEXTAREA',
	'A',
];

/**
 * Noninteractive component
 *
 * Makes children elements Noninteractive, preventing both mouse and keyboard events without affecting how the elements
 * appear visually. Used for previews.
 *
 * Based on the <Disabled> component in WordPress.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/packages/components/src/disabled/index.js
 */
const Noninteractive = ( {
	children,
	style = {},
	...props
}: {
	children: React.ReactNode;
	style?: Record< string, string >;
} ): JSX.Element => {
	const node = useRef< HTMLDivElement >( null );

	const disableFocus = () => {
		if ( node.current ) {
			focus.focusable.find( node.current ).forEach( ( focusable ) => {
				if ( FOCUSABLE_NODE_NAMES.includes( focusable.nodeName ) ) {
					focusable.setAttribute( 'tabindex', '-1' );
				}
				if ( focusable.hasAttribute( 'contenteditable' ) ) {
					focusable.setAttribute( 'contenteditable', 'false' );
				}
			} );
		}
	};

	// Debounce re-disable since disabling process itself will incur additional mutations which should be ignored.
	const debounced = useDebouncedCallback( disableFocus, 0, {
		leading: true,
	} );

	useLayoutEffect( () => {
		let observer: MutationObserver | undefined;
		disableFocus();
		if ( node.current ) {
			observer = new window.MutationObserver( debounced );
			observer.observe( node.current, {
				childList: true,
				attributes: true,
				subtree: true,
			} );
		}
		return () => {
			if ( observer ) {
				observer.disconnect();
			}
			debounced.cancel();
		};
	}, [ debounced ] );

	return (
		<div
			ref={ node }
			aria-disabled="true"
			style={ {
				userSelect: 'none',
				pointerEvents: 'none',
				cursor: 'normal',
				...style,
			} }
			{ ...props }
		>
			{ children }
		</div>
	);
};

export default Noninteractive;
