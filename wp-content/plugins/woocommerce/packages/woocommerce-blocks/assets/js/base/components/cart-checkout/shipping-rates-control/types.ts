/**
 * External dependencies
 */
import { CartResponseShippingRate } from '@woocommerce/type-defs/cart-response';
import type { ReactElement } from 'react';

/**
 * Internal dependencies
 */
import {
	PackageRateRenderOption,
	TernaryFlag,
} from '../shipping-rates-control-package';

export interface PackagesProps {
	// Array of packages
	packages: CartResponseShippingRate[];

	// If the package should be rendered as a collapsible panel
	collapsible?: TernaryFlag;

	// If we should items below the package name
	showItems?: TernaryFlag;

	// Rendered when there are no rates in a package
	noResultsMessage: ReactElement;

	// Function to render a shipping rate
	renderOption: PackageRateRenderOption;
}

export interface ShippingRatesControlProps {
	// If true, when multiple packages are rendered, you can see each package's items
	showItems?: TernaryFlag;

	// If true, when multiple packages are rendered they can be toggled open and closed
	collapsible?: TernaryFlag;

	// Array of packages containing shipping rates
	shippingRates: CartResponseShippingRate[];

	// Class name for package rates
	className?: string | undefined;

	// True when rates are being loaded
	isLoadingRates: boolean;

	// Rendered when there are no packages
	noResultsMessage: ReactElement;

	// Function to render a shipping rate
	renderOption?: PackageRateRenderOption | undefined;

	// String equal to the block name where the Slot is rendered
	context: 'woocommerce/cart' | 'woocommerce/checkout';
}
