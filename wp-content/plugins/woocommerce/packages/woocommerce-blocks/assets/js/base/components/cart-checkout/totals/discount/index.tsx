/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import LoadingMask from '@woocommerce/base-components/loading-mask';
import { RemovableChip } from '@woocommerce/base-components/chip';
import { applyCheckoutFilter, TotalsItem } from '@woocommerce/blocks-checkout';
import { getSetting } from '@woocommerce/settings';
import {
	CartResponseCouponItemWithLabel,
	CartTotalsItem,
	Currency,
	LooselyMustHave,
} from '@woocommerce/types';

/**
 * Internal dependencies
 */
import './style.scss';

export interface TotalsDiscountProps {
	cartCoupons: LooselyMustHave<
		CartResponseCouponItemWithLabel,
		'code' | 'label' | 'totals'
	>[];
	currency: Currency;
	isRemovingCoupon: boolean;
	removeCoupon: ( couponCode: string ) => void;
	values: LooselyMustHave<
		CartTotalsItem,
		'total_discount' | 'total_discount_tax'
	>;
}

const filteredCartCouponsFilterArg = {
	context: 'summary',
};

const TotalsDiscount = ( {
	cartCoupons = [],
	currency,
	isRemovingCoupon,
	removeCoupon,
	values,
}: TotalsDiscountProps ): JSX.Element | null => {
	const {
		total_discount: totalDiscount,
		total_discount_tax: totalDiscountTax,
	} = values;
	const discountValue = parseInt( totalDiscount, 10 );

	if ( ! discountValue && cartCoupons.length === 0 ) {
		return null;
	}

	const discountTaxValue = parseInt( totalDiscountTax, 10 );
	const discountTotalValue = getSetting(
		'displayCartPricesIncludingTax',
		false
	)
		? discountValue + discountTaxValue
		: discountValue;

	const filteredCartCoupons = applyCheckoutFilter( {
		arg: filteredCartCouponsFilterArg,
		filterName: 'coupons',
		defaultValue: cartCoupons,
	} );

	return (
		<TotalsItem
			className="wc-block-components-totals-discount"
			currency={ currency }
			description={
				filteredCartCoupons.length !== 0 && (
					<LoadingMask
						screenReaderLabel={ __(
							'Removing coupon…',
							'woo-gutenberg-products-block'
						) }
						isLoading={ isRemovingCoupon }
						showSpinner={ false }
					>
						<ul className="wc-block-components-totals-discount__coupon-list">
							{ filteredCartCoupons.map( ( cartCoupon ) => {
								return (
									<RemovableChip
										key={ 'coupon-' + cartCoupon.code }
										className="wc-block-components-totals-discount__coupon-list-item"
										text={ cartCoupon.label }
										screenReaderText={ sprintf(
											/* translators: %s Coupon code. */
											__(
												'Coupon: %s',
												'woo-gutenberg-products-block'
											),
											cartCoupon.label
										) }
										disabled={ isRemovingCoupon }
										onRemove={ () => {
											removeCoupon( cartCoupon.code );
										} }
										radius="large"
										ariaLabel={ sprintf(
											/* translators: %s is a coupon code. */
											__(
												'Remove coupon "%s"',
												'woo-gutenberg-products-block'
											),
											cartCoupon.label
										) }
									/>
								);
							} ) }
						</ul>
					</LoadingMask>
				)
			}
			label={
				!! discountTotalValue
					? __( 'Discount', 'woo-gutenberg-products-block' )
					: __( 'Coupons', 'woo-gutenberg-products-block' )
			}
			value={ discountTotalValue ? discountTotalValue * -1 : '-' }
		/>
	);
};

export default TotalsDiscount;
