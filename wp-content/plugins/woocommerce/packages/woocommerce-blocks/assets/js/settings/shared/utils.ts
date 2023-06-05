/**
 * External dependencies
 */
import compareVersions from 'compare-versions';

/**
 * Internal dependencies
 */
import { allSettings } from './settings-init';

/**
 * Retrieves a setting value from the setting state.
 *
 * If a setting with key `name` does not exist or is undefined,
 * the `fallback` will be returned instead. An optional `filter`
 * callback can be passed to format the returned value.
 */
export const getSetting = < T >(
	name: string,
	fallback: unknown = false,
	filter = ( val: unknown, fb: unknown ) =>
		typeof val !== 'undefined' ? val : fb
): T => {
	const value = name in allSettings ? allSettings[ name ] : fallback;
	return filter( value, fallback ) as T;
};

export const getSettingWithCoercion = < T >(
	name: string,
	fallback: T,
	typeguard: ( val: unknown, fb: unknown ) => val is T
): T => {
	const value = name in allSettings ? allSettings[ name ] : fallback;
	return typeguard( value, fallback ) ? value : fallback;
};

/**
 * Note: this attempts to coerce the wpVersion to a semver for comparison
 * This will result in dropping any beta/rc values.
 *
 * `5.3-beta1-4252` would get converted to `5.3.0-rc.4252`
 * `5.3-beta1` would get converted to `5.3.0-rc`.
 * `5.3` would not be touched.
 *
 * For the purpose of these comparisons all pre-release versions are normalized
 * to `rc`.
 *
 * @param {string}                          setting  Setting name (e.g. wpVersion or wcVersion).
 * @param {string}                          version  Version to compare.
 * @param {compareVersions.CompareOperator} operator Comparison operator.
 */
const compareVersionSettingIgnorePrerelease = (
	setting: string,
	version: string,
	operator: compareVersions.CompareOperator
): boolean => {
	const settingValue = getSetting( setting, '' ) as string;
	let replacement = settingValue.replace( /-[a-zA-Z0-9]*[\-]*/, '.0-rc.' );
	replacement = replacement.endsWith( '.' )
		? replacement.substring( 0, replacement.length - 1 )
		: replacement;
	return compareVersions.compare( replacement, version, operator );
};

/**
 * Compare the current WP version with the provided `version` param using the
 * `operator`.
 *
 * For example `isWpVersion( '5.6', '<=' )` returns true if the site WP version
 * is smaller or equal than `5.6` .
 */
export const isWpVersion = (
	version: string,
	operator: compareVersions.CompareOperator = '='
): boolean => {
	return compareVersionSettingIgnorePrerelease(
		'wpVersion',
		version,
		operator
	);
};

/**
 * Compare the current WC version with the provided `version` param using the
 * `operator`.
 *
 * For example `isWcVersion( '4.9.0', '<=' )` returns true if the site WC version
 * is smaller or equal than `4.9`.
 */
export const isWcVersion = (
	version: string,
	operator: compareVersions.CompareOperator = '='
): boolean => {
	return compareVersionSettingIgnorePrerelease(
		'wcVersion',
		version,
		operator
	);
};

/**
 * Returns a string with the site's wp-admin URL appended. JS version of `admin_url`.
 *
 * @param {string} path Relative path.
 * @return {string} Full admin URL.
 */
export const getAdminLink = ( path: string ): string =>
	getSetting( 'adminUrl' ) + path;
