/**
 * External dependencies
 */
import { forwardRef } from '@wordpress/element';
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import { ForwardRefProps } from './types';

const Sidebar = forwardRef< HTMLInputElement, ForwardRefProps >(
	( { children, className = '' }, ref ): JSX.Element => {
		return (
			<div
				ref={ ref }
				className={ classNames(
					'wc-block-components-sidebar',
					className
				) }
			>
				{ children }
			</div>
		);
	}
);

export default Sidebar;
