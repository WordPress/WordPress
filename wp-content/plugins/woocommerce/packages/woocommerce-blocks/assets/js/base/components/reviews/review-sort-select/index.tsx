/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import SortSelect from '@woocommerce/base-components/sort-select';
import type { ChangeEventHandler } from 'react';

/**
 * Internal dependencies
 */
import './style.scss';

interface ReviewSortSelectProps {
	onChange: ChangeEventHandler;
	readOnly: boolean;
	value: 'most-recent' | 'highest-rating' | 'lowest-rating';
}

const ReviewSortSelect = ( {
	onChange,
	readOnly,
	value,
}: ReviewSortSelectProps ): JSX.Element => {
	return (
		<SortSelect
			className="wc-block-review-sort-select wc-block-components-review-sort-select"
			label={ __( 'Order by', 'woo-gutenberg-products-block' ) }
			onChange={ onChange }
			options={ [
				{
					key: 'most-recent',
					label: __( 'Most recent', 'woo-gutenberg-products-block' ),
				},
				{
					key: 'highest-rating',
					label: __(
						'Highest rating',
						'woo-gutenberg-products-block'
					),
				},
				{
					key: 'lowest-rating',
					label: __(
						'Lowest rating',
						'woo-gutenberg-products-block'
					),
				},
			] }
			readOnly={ readOnly }
			screenReaderLabel={ __(
				'Order reviews by',
				'woo-gutenberg-products-block'
			) }
			value={ value }
		/>
	);
};

export default ReviewSortSelect;
