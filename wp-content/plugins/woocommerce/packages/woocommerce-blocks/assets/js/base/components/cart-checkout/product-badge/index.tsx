/**
 * External dependencies
 */
import classNames from 'classnames';
import type { ReactNode } from 'react';

/**
 * Internal dependencies
 */
import './style.scss';

interface ProductBadgeProps {
	children?: ReactNode;
	className?: string;
}
const ProductBadge = ( {
	children,
	className,
}: ProductBadgeProps ): JSX.Element => {
	return (
		<div
			className={ classNames(
				'wc-block-components-product-badge',
				className
			) }
		>
			{ children }
		</div>
	);
};

export default ProductBadge;
