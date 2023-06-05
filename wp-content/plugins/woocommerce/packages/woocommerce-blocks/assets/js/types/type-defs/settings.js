/**
 * @typedef {Object} WooCommerceSiteCurrency
 *
 * @property {string} code              The ISO code for the currency.
 * @property {number} precision         The precision (decimal places).
 * @property {string} symbol            The symbol for the currency (eg '$')
 * @property {string} symbolPosition    The position for the symbol ('left',
 *                                      or 'right')
 * @property {string} decimalSeparator  The string used for the decimal
 *                                      separator.
 * @property {string} thousandSeparator The string used for the thousands
 *                                      separator.
 * @property {string} priceFormat       The format string use for displaying
 *                                      an amount in this currency.
 */

/**
 * @typedef {Object} WooCommerceSiteLocale
 *
 * @property {string}        siteLocale    The locale string for the current
 *                                         site.
 * @property {string}        userLocale    The locale string for the current
 *                                         user.
 * @property {Array<string>} weekdaysShort An array of short weekday strings
 *                                         in the current user's locale.
 */

/**
 * @typedef {Object} WooCommerceSharedSettings
 *
 * @property {string}                  adminUrl         The url for the current
 *                                                      site's dashboard.
 * @property {Object}                  countries        An object of countries
 *                                                      where the keys are
 *                                                      Country codes and values
 *                                                      are country names
 *                                                      localized for the site's
 *                                                      current language.
 * @property {WooCommerceSiteCurrency} currency         The current site
 *                                                      currency object.
 * @property {string}                  defaultDateRange The default date range
 *                                                      query string to use.
 * @property {WooCommerceSiteLocale}   locale           Locale information for
 *                                                      the site.
 * @property {Object}                  orderStatuses    An object of order
 *                                                      statuses indexed by
 *                                                      status key and localized
 *                                                      status value.
 * @property {string}                  siteTitle        The current title of the
 *                                                      site.
 * @property {string}                  wcAssetUrl       The url to the assets
 *                                                      directory for the
 *                                                      WooCommerce plugin.
 */

export {};
