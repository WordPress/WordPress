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

interface FilterSubmitButtonProps {
	className?: string;
	isLoading?: boolean;
	disabled?: boolean;
	label?: string;
	onClick: () => void;
	screenReaderLabel?: string;
}

const FilterSubmitButton = ( {
	className,
	isLoading,
	disabled,
	/* translators: Submit button text for filters. */
	label = __( 'Apply', 'woo-gutenberg-products-block' ),
	onClick,
	screenReaderLabel = __( 'Apply filter', 'woo-gutenberg-products-block' ),
}: FilterSubmitButtonProps ): JSX.Element => {
	return (
		<button
			type="submit"
			className={ classNames(
				'wp-block-button__link',
				'wc-block-filter-submit-button',
				'wc-block-components-filter-submit-button',
				{ 'is-loading': isLoading },
				className
			) }
			disabled={ disabled }
			onClick={ onClick }
		>
			<Label label={ label } screenReaderLabel={ screenReaderLabel } />
		</button>
	);
};

export default FilterSubmitButton;
