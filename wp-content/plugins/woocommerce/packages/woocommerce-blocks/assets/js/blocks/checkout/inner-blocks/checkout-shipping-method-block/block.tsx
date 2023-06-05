/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useShippingData } from '@woocommerce/base-context/hooks';
import {
	__experimentalRadio as Radio,
	__experimentalRadioGroup as RadioGroup,
} from 'wordpress-components';
import classnames from 'classnames';
import { Icon, store, shipping } from '@wordpress/icons';
import { useEffect } from '@wordpress/element';
import { VALIDATION_STORE_KEY } from '@woocommerce/block-data';
import { useDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import './style.scss';
import { RatePrice, getLocalPickupPrices, getShippingPrices } from './shared';
import type { minMaxPrices } from './shared';
import { defaultLocalPickupText, defaultShippingText } from './constants';
import { shippingAddressHasValidationErrors } from '../../../../data/cart/utils';

const SHIPPING_RATE_ERROR = {
	hidden: true,
	message: __(
		'Shipping options are not available',
		'woo-gutenberg-products-block'
	),
};

const LocalPickupSelector = ( {
	checked,
	rate,
	showPrice,
	showIcon,
	toggleText,
	multiple,
}: {
	checked: string;
	rate: minMaxPrices;
	showPrice: boolean;
	showIcon: boolean;
	toggleText: string;
	multiple: boolean;
} ) => {
	return (
		<Radio
			value="pickup"
			className={ classnames(
				'wc-block-checkout__shipping-method-option',
				{
					'wc-block-checkout__shipping-method-option--selected':
						checked === 'pickup',
				}
			) }
		>
			{ showIcon === true && (
				<Icon
					icon={ store }
					size={ 28 }
					className="wc-block-checkout__shipping-method-option-icon"
				/>
			) }
			<span className="wc-block-checkout__shipping-method-option-title">
				{ toggleText }
			</span>
			{ showPrice === true && (
				<RatePrice
					multiple={ multiple }
					minRate={ rate.min }
					maxRate={ rate.max }
				/>
			) }
		</Radio>
	);
};

const ShippingSelector = ( {
	checked,
	rate,
	showPrice,
	showIcon,
	toggleText,
	shippingCostRequiresAddress = false,
}: {
	checked: string;
	rate: minMaxPrices;
	showPrice: boolean;
	showIcon: boolean;
	shippingCostRequiresAddress: boolean;
	toggleText: string;
} ) => {
	const rateShouldBeHidden =
		shippingCostRequiresAddress && shippingAddressHasValidationErrors();
	const hasShippingPrices = rate.min !== undefined && rate.max !== undefined;
	const { setValidationErrors, clearValidationError } =
		useDispatch( VALIDATION_STORE_KEY );
	useEffect( () => {
		if ( checked === 'shipping' && ! hasShippingPrices ) {
			setValidationErrors( {
				'shipping-rates-error': SHIPPING_RATE_ERROR,
			} );
		} else {
			clearValidationError( 'shipping-rates-error' );
		}
	}, [
		checked,
		clearValidationError,
		hasShippingPrices,
		setValidationErrors,
	] );
	const Price =
		rate.min === undefined || rateShouldBeHidden ? (
			<span className="wc-block-checkout__shipping-method-option-price">
				{ __(
					'calculated with an address',
					'woo-gutenberg-products-block'
				) }
			</span>
		) : (
			<RatePrice minRate={ rate.min } maxRate={ rate.max } />
		);

	return (
		<Radio
			value="shipping"
			className={ classnames(
				'wc-block-checkout__shipping-method-option',
				{
					'wc-block-checkout__shipping-method-option--selected':
						checked === 'shipping',
				}
			) }
		>
			{ showIcon === true && (
				<Icon
					icon={ shipping }
					size={ 28 }
					className="wc-block-checkout__shipping-method-option-icon"
				/>
			) }
			<span className="wc-block-checkout__shipping-method-option-title">
				{ toggleText }
			</span>
			{ showPrice === true && Price }
		</Radio>
	);
};
const Block = ( {
	checked,
	onChange,
	showPrice,
	showIcon,
	localPickupText,
	shippingText,
	shippingCostRequiresAddress = false,
}: {
	checked: string;
	onChange: ( value: string ) => void;
	showPrice: boolean;
	showIcon: boolean;
	shippingCostRequiresAddress: boolean;
	localPickupText: string;
	shippingText: string;
} ): JSX.Element | null => {
	const { shippingRates } = useShippingData();

	return (
		<RadioGroup
			id="shipping-method"
			className="wc-block-checkout__shipping-method-container"
			label="options"
			onChange={ onChange }
			checked={ checked }
		>
			<ShippingSelector
				checked={ checked }
				rate={ getShippingPrices( shippingRates[ 0 ]?.shipping_rates ) }
				showPrice={ showPrice }
				showIcon={ showIcon }
				shippingCostRequiresAddress={ shippingCostRequiresAddress }
				toggleText={ shippingText || defaultShippingText }
			/>
			<LocalPickupSelector
				checked={ checked }
				rate={ getLocalPickupPrices(
					shippingRates[ 0 ]?.shipping_rates
				) }
				multiple={ shippingRates.length > 1 }
				showPrice={ showPrice }
				showIcon={ showIcon }
				toggleText={ localPickupText || defaultLocalPickupText }
			/>
		</RadioGroup>
	);
};

export default Block;
