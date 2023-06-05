/**
 * Internal dependencies
 */
import { allSettings } from './settings-init';

/**
 * This exports all default core settings as constants.
 */
export const ADMIN_URL = allSettings.adminUrl;
export const COUNTRIES = allSettings.countries;
export const CURRENCY = allSettings.currency;
export const CURRENT_USER_IS_ADMIN = allSettings.currentUserIsAdmin as boolean;
export const HOME_URL = allSettings.homeUrl;
export const LOCALE = allSettings.locale;
export const ORDER_STATUSES = allSettings.orderStatuses;
export const PLACEHOLDER_IMG_SRC = allSettings.placeholderImgSrc as string;
export const SITE_TITLE = allSettings.siteTitle;
export const STORE_PAGES = allSettings.storePages as Record<
	string,
	{
		id: 0;
		title: '';
		permalink: '';
	}
>;
export const WC_ASSET_URL = allSettings.wcAssetUrl;
export const WC_VERSION = allSettings.wcVersion;
export const WP_LOGIN_URL = allSettings.wpLoginUrl;
export const WP_VERSION = allSettings.wpVersion;
