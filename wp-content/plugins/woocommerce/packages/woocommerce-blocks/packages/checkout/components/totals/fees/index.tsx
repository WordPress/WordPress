/**
 * External dependencies
 */
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import { getSetting } from '@woocommerce/settings';
import type { Currency } from '@woocommerce/price-format';
import type { CartFeeItem } from '@woocommerce/types';
import type { ReactElement } from 'react';

/**
 * Internal dependencies
 */
import TotalsItem from '../item';

export interface TotalsFeesProps {
	/**
	 * Currency
	 */
	currency: Currency;
	/**
	 * Cart fees
	 */
	cartFees: CartFeeItem[];
	/**
	 * Component wrapper classname
	 *
	 * @default 'wc-block-components-totals-fees'
	 */
	className?: string;
}

const TotalsFees = ( {
	currency,
	cartFees,
	className,
}: TotalsFeesProps ): ReactElement | null => {
	return (
		<>
			{ cartFees.map( ( { id, name, totals }, index ) => {
				const feesValue = parseInt( totals.total, 10 );

				if ( ! feesValue ) {
					return null;
				}

				const feesTaxValue = parseInt( totals.total_tax, 10 );

				return (
					<TotalsItem
						key={ id || `${ index }-${ name }` }
						className={ classnames(
							'wc-block-components-totals-fees',
							className
						) }
						currency={ currency }
						label={
							name || __( 'Fee', 'woo-gutenberg-products-block' )
						}
						value={
							getSetting( 'displayCartPricesIncludingTax', false )
								? feesValue + feesTaxValue
								: feesValue
						}
					/>
				);
			} ) }
		</>
	);
};

export default TotalsFees;
