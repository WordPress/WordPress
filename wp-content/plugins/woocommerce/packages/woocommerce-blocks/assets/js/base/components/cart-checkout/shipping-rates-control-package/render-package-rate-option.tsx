/**
 * External dependencies
 */
import { decodeEntities } from '@wordpress/html-entities';
import { getCurrencyFromPriceResponse } from '@woocommerce/price-format';
import FormattedMonetaryAmount from '@woocommerce/base-components/formatted-monetary-amount';
import type { PackageRateOption } from '@woocommerce/types';
import { getSetting } from '@woocommerce/settings';
import { CartShippingPackageShippingRate } from '@woocommerce/types';

/**
 * Default render function for package rate options.
 *
 * @param {Object} rate Rate data.
 */
export const renderPackageRateOption = (
	rate: CartShippingPackageShippingRate
): PackageRateOption => {
	const priceWithTaxes: number = getSetting(
		'displayCartPricesIncludingTax',
		false
	)
		? parseInt( rate.price, 10 ) + parseInt( rate.taxes, 10 )
		: parseInt( rate.price, 10 );

	return {
		label: decodeEntities( rate.name ),
		value: rate.rate_id,
		description: (
			<>
				{ Number.isFinite( priceWithTaxes ) && (
					<FormattedMonetaryAmount
						currency={ getCurrencyFromPriceResponse( rate ) }
						value={ priceWithTaxes }
					/>
				) }
				{ Number.isFinite( priceWithTaxes ) && rate.delivery_time
					? ' — '
					: null }
				{ decodeEntities( rate.delivery_time ) }
			</>
		),
	};
};

export default renderPackageRateOption;
