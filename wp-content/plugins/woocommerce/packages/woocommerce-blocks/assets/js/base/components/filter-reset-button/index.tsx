/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import classNames from 'classnames';
import Label from '@woocommerce/base-components/label';

/**
 * Internal dependencies
 */
import './style.scss';

interface FilterResetButtonProps {
	className?: string;
	label?: string;
	onClick: () => void;
	screenReaderLabel?: string;
}

const FilterResetButton = ( {
	className,
	/* translators: Reset button text for filters. */
	label = __( 'Reset', 'woo-gutenberg-products-block' ),
	onClick,
	screenReaderLabel = __( 'Reset filter', 'woo-gutenberg-products-block' ),
}: FilterResetButtonProps ): JSX.Element => {
	return (
		<button
			className={ classNames(
				'wc-block-components-filter-reset-button',
				className
			) }
			onClick={ onClick }
		>
			<Label label={ label } screenReaderLabel={ screenReaderLabel } />
		</button>
	);
};

export default FilterResetButton;
