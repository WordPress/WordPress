/**
 * External dependencies
 */
import type { ReactElement } from 'react';
import type { PackageRateOption } from '@woocommerce/type-defs/shipping';
import type { CartShippingPackageShippingRate } from '@woocommerce/type-defs/cart';

export interface PackageItem {
	name: string;
	key: string;
	quantity: number;
}

export interface Destination {
	address_1: string;
	address_2: string;
	city: string;
	state: string;
	postcode: string;
	country: string;
}

export interface PackageData {
	destination: Destination;
	name: string;
	shipping_rates: CartShippingPackageShippingRate[];
	items: PackageItem[];
}

export type PackageRateRenderOption = (
	option: CartShippingPackageShippingRate
) => PackageRateOption;

// A flag can be ternary if true, false, and undefined are all valid options.
// In our case, we use this for collapsible and showItems, having a boolean will force that
// option, having undefined will let the component decide the logic based on other factors.
export type TernaryFlag = boolean | undefined;

export interface PackageProps {
	/* PackageId can be a string, WooCommerce Subscriptions uses strings for example, but WooCommerce core uses numbers */
	packageId: string | number;
	renderOption?: PackageRateRenderOption | undefined;
	collapse?: boolean;
	packageData: PackageData;
	className?: string;
	collapsible?: TernaryFlag;
	noResultsMessage: ReactElement;
	showItems?: TernaryFlag;
}
