/**
 * External dependencies
 */
import { getSetting, STORE_PAGES } from '@woocommerce/settings';

export type WordCountType =
	| 'words'
	| 'characters_excluding_spaces'
	| 'characters_including_spaces';

interface WcBlocksConfig {
	buildPhase: number;
	pluginUrl: string;
	productCount: number;
	defaultAvatar: string;
	restApiRoutes: Record< string, string[] >;
	wordCountType: WordCountType;
}

export const blocksConfig = getSetting( 'wcBlocksConfig', {
	buildPhase: 1,
	pluginUrl: '',
	productCount: 0,
	defaultAvatar: '',
	restApiRoutes: {},
	wordCountType: 'words',
} ) as WcBlocksConfig;

export const WC_BLOCKS_IMAGE_URL = blocksConfig.pluginUrl + 'images/';
export const WC_BLOCKS_BUILD_URL = blocksConfig.pluginUrl + 'build/';
export const WC_BLOCKS_PHASE = blocksConfig.buildPhase;
export const SHOP_URL = STORE_PAGES.shop?.permalink;
export const CHECKOUT_PAGE_ID = STORE_PAGES.checkout.id;
export const CHECKOUT_URL = STORE_PAGES.checkout.permalink;
export const PRIVACY_URL = STORE_PAGES.privacy.permalink;
export const PRIVACY_PAGE_NAME = STORE_PAGES.privacy.title;
export const TERMS_URL = STORE_PAGES.terms.permalink;
export const TERMS_PAGE_NAME = STORE_PAGES.terms.title;
export const CART_PAGE_ID = STORE_PAGES.cart.id;
export const CART_URL = STORE_PAGES.cart.permalink;
export const LOGIN_URL = STORE_PAGES.myaccount.permalink
	? STORE_PAGES.myaccount.permalink
	: getSetting( 'wpLoginUrl', '/wp-login.php' );
export const SHIPPING_COUNTRIES = getSetting< Record< string, string > >(
	'shippingCountries',
	{}
);
export const ALLOWED_COUNTRIES = getSetting< Record< string, string > >(
	'allowedCountries',
	{}
);
export const SHIPPING_STATES = getSetting<
	Record< string, Record< string, string > >
>( 'shippingStates', {} );
export const ALLOWED_STATES = getSetting< Record< string, string > >(
	'allowedStates',
	{}
);
export const LOCAL_PICKUP_ENABLED = getSetting< boolean >(
	'localPickupEnabled',
	false
);
