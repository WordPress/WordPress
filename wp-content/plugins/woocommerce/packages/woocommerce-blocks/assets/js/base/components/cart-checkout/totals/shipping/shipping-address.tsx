/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	formatShippingAddress,
	isAddressComplete,
} from '@woocommerce/base-utils';
import { useEditorContext } from '@woocommerce/base-context';
import { ShippingAddress as ShippingAddressType } from '@woocommerce/settings';
import PickupLocation from '@woocommerce/base-components/cart-checkout/pickup-location';
import { CHECKOUT_STORE_KEY } from '@woocommerce/block-data';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import ShippingLocation from '../../shipping-location';
import { CalculatorButton, CalculatorButtonProps } from './calculator-button';

export interface ShippingAddressProps {
	showCalculator: boolean;
	isShippingCalculatorOpen: boolean;
	setIsShippingCalculatorOpen: CalculatorButtonProps[ 'setIsShippingCalculatorOpen' ];
	shippingAddress: ShippingAddressType;
}

export const ShippingAddress = ( {
	showCalculator,
	isShippingCalculatorOpen,
	setIsShippingCalculatorOpen,
	shippingAddress,
}: ShippingAddressProps ): JSX.Element | null => {
	const addressComplete = isAddressComplete( shippingAddress );
	const { isEditor } = useEditorContext();
	const prefersCollection = useSelect( ( select ) =>
		select( CHECKOUT_STORE_KEY ).prefersCollection()
	);
	// If the address is incomplete, and we're not in the editor, don't show anything.
	if ( ! addressComplete && ! isEditor ) {
		return null;
	}
	const formattedLocation = formatShippingAddress( shippingAddress );
	return (
		<>
			{ prefersCollection ? (
				<PickupLocation />
			) : (
				<ShippingLocation formattedLocation={ formattedLocation } />
			) }
			{ showCalculator && ! prefersCollection ? (
				<CalculatorButton
					label={ __(
						'Change address',
						'woo-gutenberg-products-block'
					) }
					isShippingCalculatorOpen={ isShippingCalculatorOpen }
					setIsShippingCalculatorOpen={ setIsShippingCalculatorOpen }
				/>
			) : null }
		</>
	);
};

export default ShippingAddress;
