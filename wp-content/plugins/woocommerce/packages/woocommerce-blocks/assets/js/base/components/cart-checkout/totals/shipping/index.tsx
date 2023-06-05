/**
 * External dependencies
 */
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { useStoreCart } from '@woocommerce/base-context/hooks';
import { TotalsItem } from '@woocommerce/blocks-checkout';
import type { Currency } from '@woocommerce/price-format';
import { ShippingVia } from '@woocommerce/base-components/cart-checkout/totals/shipping/shipping-via';
import {
	isAddressComplete,
	isPackageRateCollectable,
} from '@woocommerce/base-utils';
import { CHECKOUT_STORE_KEY } from '@woocommerce/block-data';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import ShippingCalculator from '../../shipping-calculator';
import { hasShippingRate, getTotalShippingValue } from './utils';
import ShippingPlaceholder from './shipping-placeholder';
import ShippingAddress from './shipping-address';
import ShippingRateSelector from './shipping-rate-selector';
import './style.scss';

export interface TotalShippingProps {
	currency: Currency;
	values: {
		total_shipping: string;
		total_shipping_tax: string;
	}; // Values in use
	showCalculator?: boolean; //Whether to display the rate selector below the shipping total.
	showRateSelector?: boolean; // Whether to show shipping calculator or not.
	className?: string;
	isCheckout?: boolean;
}
export const TotalsShipping = ( {
	currency,
	values,
	showCalculator = true,
	showRateSelector = true,
	isCheckout = false,
	className,
}: TotalShippingProps ): JSX.Element => {
	const [ isShippingCalculatorOpen, setIsShippingCalculatorOpen ] =
		useState( false );
	const {
		shippingAddress,
		cartHasCalculatedShipping,
		shippingRates,
		isLoadingRates,
	} = useStoreCart();
	const totalShippingValue = getTotalShippingValue( values );
	const hasRates = hasShippingRate( shippingRates ) || totalShippingValue > 0;
	const showShippingCalculatorForm =
		showCalculator && isShippingCalculatorOpen;
	const prefersCollection = useSelect( ( select ) => {
		return select( CHECKOUT_STORE_KEY ).prefersCollection();
	} );
	const selectedShippingRates = shippingRates.flatMap(
		( shippingPackage ) => {
			return shippingPackage.shipping_rates
				.filter(
					( rate ) =>
						// If the shopper prefers collection, the rate is collectable AND selected.
						( prefersCollection &&
							isPackageRateCollectable( rate ) &&
							rate.selected ) ||
						// Or the shopper does not prefer collection and the rate is selected
						( ! prefersCollection && rate.selected )
				)
				.flatMap( ( rate ) => rate.name );
		}
	);

	const addressComplete = isAddressComplete( shippingAddress );

	return (
		<div
			className={ classnames(
				'wc-block-components-totals-shipping',
				className
			) }
		>
			<TotalsItem
				label={ __( 'Shipping', 'woo-gutenberg-products-block' ) }
				value={
					hasRates && cartHasCalculatedShipping
						? totalShippingValue
						: // if address is not complete, display the link to add an address.
						  ! addressComplete && (
								<ShippingPlaceholder
									showCalculator={ showCalculator }
									isCheckout={ isCheckout }
									isShippingCalculatorOpen={
										isShippingCalculatorOpen
									}
									setIsShippingCalculatorOpen={
										setIsShippingCalculatorOpen
									}
								/>
						  )
				}
				description={
					// If address is complete, display the shipping address.
					( hasRates && cartHasCalculatedShipping ) ||
					addressComplete ? (
						<>
							<ShippingVia
								selectedShippingRates={ selectedShippingRates }
							/>
							<ShippingAddress
								shippingAddress={ shippingAddress }
								showCalculator={ showCalculator }
								isShippingCalculatorOpen={
									isShippingCalculatorOpen
								}
								setIsShippingCalculatorOpen={
									setIsShippingCalculatorOpen
								}
							/>
						</>
					) : null
				}
				currency={ currency }
			/>
			{ showShippingCalculatorForm && (
				<ShippingCalculator
					onUpdate={ () => {
						setIsShippingCalculatorOpen( false );
					} }
					onCancel={ () => {
						setIsShippingCalculatorOpen( false );
					} }
				/>
			) }
			{ showRateSelector &&
				cartHasCalculatedShipping &&
				! showShippingCalculatorForm && (
					<ShippingRateSelector
						hasRates={ hasRates }
						shippingRates={ shippingRates }
						isLoadingRates={ isLoadingRates }
						isAddressComplete={ addressComplete }
					/>
				) }
		</div>
	);
};

export default TotalsShipping;
