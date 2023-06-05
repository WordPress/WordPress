/**
 * External dependencies
 */
import { SymbolPosition, CurrencyCode } from '@woocommerce/types';

declare global {
	interface Window {
		wcSettings: Record< string, unknown >;
	}
}

export interface WooCommerceSiteCurrency {
	// The ISO code for the currency.
	code: CurrencyCode;
	// The precision (decimal places).
	precision: number;
	// The symbol for the currency (eg '$')
	symbol: string;
	// The position for the symbol ('left', or 'right')
	symbolPosition: SymbolPosition;
	// The string used for the decimal separator.
	decimalSeparator: string;
	// The string used for the thousands separator.
	thousandSeparator: string;
	// The format string use for displaying an amount in this currency.
	priceFormat: string;
}

export interface WooCommerceSiteLocale {
	// The locale string for the current site.
	siteLocale: string;
	// The locale string for the current user.
	userLocale: string;
	// An array of short weekday strings in the current user's locale.
	weekdaysShort: string[];
}

export interface WooCommerceSharedSettings {
	adminUrl: string;
	countries: Record< string, string > | never[];
	currency: WooCommerceSiteCurrency;
	currentUserId: number;
	currentUserIsAdmin: boolean;
	homeUrl: string;
	locale: WooCommerceSiteLocale;
	orderStatuses: Record< string, string > | never[];
	placeholderImgSrc: string;
	siteTitle: string;
	storePages: Record< string, string > | never[];
	wcAssetUrl: string;
	wcVersion: string;
	wpLoginUrl: string;
	wpVersion: string;
}

const defaults: WooCommerceSharedSettings = {
	adminUrl: '',
	countries: [],
	currency: {
		code: 'USD',
		precision: 2,
		symbol: '$',
		symbolPosition: 'left',
		decimalSeparator: '.',
		priceFormat: '%1$s%2$s',
		thousandSeparator: ',',
	},
	currentUserId: 0,
	currentUserIsAdmin: false,
	homeUrl: '',
	locale: {
		siteLocale: 'en_US',
		userLocale: 'en_US',
		weekdaysShort: [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ],
	},
	orderStatuses: [],
	placeholderImgSrc: '',
	siteTitle: '',
	storePages: [],
	wcAssetUrl: '',
	wcVersion: '',
	wpLoginUrl: '',
	wpVersion: '',
};

const globalSharedSettings =
	typeof window.wcSettings === 'object' ? window.wcSettings : {};

interface AllSettings extends Record< string, unknown > {
	currency: WooCommerceSiteCurrency;
}

// Use defaults or global settings, depending on what is set.
const allSettings: AllSettings = {
	...defaults,
	...globalSharedSettings,
};

allSettings.currency = {
	...defaults.currency,
	...( allSettings.currency as WooCommerceSiteCurrency ),
};

allSettings.locale = {
	...defaults.locale,
	...( allSettings.locale as Record< string, unknown > ),
};

export { allSettings };
