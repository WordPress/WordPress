/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { CalculatorButton, CalculatorButtonProps } from './calculator-button';

export interface ShippingPlaceholderProps {
	showCalculator: boolean;
	isShippingCalculatorOpen: boolean;
	isCheckout?: boolean;
	setIsShippingCalculatorOpen: CalculatorButtonProps[ 'setIsShippingCalculatorOpen' ];
}

export const ShippingPlaceholder = ( {
	showCalculator,
	isShippingCalculatorOpen,
	setIsShippingCalculatorOpen,
	isCheckout = false,
}: ShippingPlaceholderProps ): JSX.Element => {
	if ( ! showCalculator ) {
		return (
			<em>
				{ isCheckout
					? __(
							'No shipping options available',
							'woo-gutenberg-products-block'
					  )
					: __(
							'Calculated during checkout',
							'woo-gutenberg-products-block'
					  ) }
			</em>
		);
	}

	return (
		<CalculatorButton
			label={ __(
				'Add an address for shipping options',
				'woo-gutenberg-products-block'
			) }
			isShippingCalculatorOpen={ isShippingCalculatorOpen }
			setIsShippingCalculatorOpen={ setIsShippingCalculatorOpen }
		/>
	);
};

export default ShippingPlaceholder;
