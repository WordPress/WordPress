/**
 * External dependencies
 */
import { useArgs } from '@storybook/client-api';
import type { Story, Meta } from '@storybook/react';
import {
	currenciesAPIShape as currencies,
	currencyControl,
	INTERACTION_TIMEOUT,
} from '@woocommerce/storybook-controls';
import {
	CartResponseCouponItemWithLabel,
	CartTotalsItem,
	LooselyMustHave,
} from '@woocommerce/types';

/**
 * Internal dependencies
 */
import Discount, { TotalsDiscountProps } from '..';

const EXAMPLE_COUPONS: CartResponseCouponItemWithLabel[] = [
	{
		code: 'AWSMSB',
		discount_type: '',
		label: 'Awesome Storybook coupon',
		totals: {
			...currencies.EUR,
			total_discount: '5000',
			total_discount_tax: '250',
		},
	},
	{
		code: 'STONKS',
		discount_type: '',
		label: 'Most valuable coupon',
		totals: {
			...currencies.EUR,
			total_discount: '10000',
			total_discount_tax: '1000',
		},
	},
];

function extractValuesFromCoupons(
	coupons: LooselyMustHave< CartResponseCouponItemWithLabel, 'totals' >[]
) {
	return coupons.reduce(
		( acc, curr ) => {
			const totalDiscount =
				Number( acc.total_discount ) +
				Number( curr.totals.total_discount );
			const totalDiscountTax =
				Number( acc.total_discount_tax ) +
				Number( curr.totals.total_discount_tax );

			return {
				total_discount: String( totalDiscount ),
				total_discount_tax: String( totalDiscountTax ),
			};
		},
		{ total_discount: '0', total_discount_tax: '0' } as LooselyMustHave<
			CartTotalsItem,
			'total_discount' | 'total_discount_tax'
		>
	);
}

export default {
	title: 'WooCommerce Blocks/@base-components/cart-checkout/totals/Discount',
	component: Discount,
	argTypes: {
		currency: currencyControl,
		removeCoupon: { action: 'Removing coupon with code' },
	},
	args: {
		cartCoupons: EXAMPLE_COUPONS,
		isRemovingCoupon: false,
		values: extractValuesFromCoupons( EXAMPLE_COUPONS ),
	},
} as Meta< TotalsDiscountProps >;

const Template: Story< TotalsDiscountProps > = ( args ) => {
	const [ {}, setArgs ] = useArgs();

	const removeCoupon = ( code: string ) => {
		args.removeCoupon( code );
		setArgs( { isRemovingCoupon: true } );

		const cartCoupons = args.cartCoupons.filter(
			( coupon ) => coupon.code !== code
		);

		const values = extractValuesFromCoupons( cartCoupons );

		setTimeout(
			() => setArgs( { cartCoupons, values, isRemovingCoupon: false } ),
			INTERACTION_TIMEOUT
		);
	};

	return <Discount { ...args } removeCoupon={ removeCoupon } />;
};

export const Default = Template.bind( {} );
Default.args = {};

export const RemovingCoupon = Template.bind( {} );
RemovingCoupon.args = {
	isRemovingCoupon: true,
};
