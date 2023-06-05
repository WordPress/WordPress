/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import Label from '@woocommerce/base-components/label';
import type { MouseEventHandler } from 'react';

/**
 * Internal dependencies
 */
import './style.scss';

interface LoadMoreButtonProps {
	onClick: MouseEventHandler;
	label?: string;
	screenReaderLabel?: string;
}

export const LoadMoreButton = ( {
	onClick,
	label = __( 'Load more', 'woo-gutenberg-products-block' ),
	screenReaderLabel = __( 'Load more', 'woo-gutenberg-products-block' ),
}: LoadMoreButtonProps ): JSX.Element => {
	return (
		<div className="wp-block-button wc-block-load-more wc-block-components-load-more">
			<button className="wp-block-button__link" onClick={ onClick }>
				<Label
					label={ label }
					screenReaderLabel={ screenReaderLabel }
				/>
			</button>
		</div>
	);
};

export default LoadMoreButton;
