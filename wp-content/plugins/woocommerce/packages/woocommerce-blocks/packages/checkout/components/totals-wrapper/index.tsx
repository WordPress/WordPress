/**
 * External dependencies
 */
import classnames from 'classnames';
import { Children } from '@wordpress/element';
import type { ReactNode } from 'react';

/**
 * Internal dependencies
 */
import './style.scss';

interface TotalsWrapperProps {
	children: ReactNode;
	/* If this TotalsWrapper is being used to wrap a Slot */
	slotWrapper?: boolean;
	className?: string;
}

const TotalsWrapper = ( {
	children,
	slotWrapper = false,
	className,
}: TotalsWrapperProps ): JSX.Element | null => {
	return Children.count( children ) ? (
		<div
			className={ classnames(
				className,
				'wc-block-components-totals-wrapper',
				{
					'slot-wrapper': slotWrapper,
				}
			) }
		>
			{ children }
		</div>
	) : null;
};

export default TotalsWrapper;
