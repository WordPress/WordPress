/**
 * External dependencies
 */
import classnames from 'classnames';
import { useState } from '@wordpress/element';
import { ResizableBox } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { useThrottle } from '../../utils/useThrottle';

type ResizeCallback = Exclude< ResizableBox.Props[ 'onResize' ], undefined >;

export const ConstrainedResizable = ( {
	className = '',
	onResize,
	...props
}: ResizableBox.Props ): JSX.Element => {
	const [ isResizing, setIsResizing ] = useState( false );

	const classNames = classnames( className, {
		'is-resizing': isResizing,
	} );
	const throttledResize = useThrottle< ResizeCallback >(
		( event, direction, elt, _delta ) => {
			if ( ! isResizing ) setIsResizing( true );
			onResize?.( event, direction, elt, _delta );
		},
		50,
		{ leading: true }
	);

	return (
		<ResizableBox
			className={ classNames }
			enable={ { bottom: true } }
			onResize={ throttledResize }
			onResizeStop={ ( ...args ) => {
				onResize?.( ...args );
				setIsResizing( false );
			} }
			{ ...props }
		/>
	);
};
