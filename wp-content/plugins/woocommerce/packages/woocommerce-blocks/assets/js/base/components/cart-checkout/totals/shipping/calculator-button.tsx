/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';

export interface CalculatorButtonProps {
	label?: string;
	isShippingCalculatorOpen: boolean;
	setIsShippingCalculatorOpen: ( isShippingCalculatorOpen: boolean ) => void;
}

export const CalculatorButton = ( {
	label = __( 'Calculate', 'woo-gutenberg-products-block' ),
	isShippingCalculatorOpen,
	setIsShippingCalculatorOpen,
}: CalculatorButtonProps ): JSX.Element => {
	return (
		<a
			role="button"
			href="#wc-block-components-shipping-calculator-address__link"
			className="wc-block-components-totals-shipping__change-address__link"
			id="wc-block-components-totals-shipping__change-address__link"
			onClick={ ( e ) => {
				e.preventDefault();
				setIsShippingCalculatorOpen( ! isShippingCalculatorOpen );
			} }
			aria-label={ label }
			aria-expanded={ isShippingCalculatorOpen }
		>
			{ label }
		</a>
	);
};

export default CalculatorButton;
