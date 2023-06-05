/**
 * @typedef {Object} AddressField
 *
 * @property {string}  label         The label for the field.
 * @property {string}  optionalLabel The label for the field if made optional.
 * @property {string}  autocomplete  The HTML autocomplete attribute value. See
 *                                   https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/autocomplete
 * @property {boolean} required      Set to true if the field is required.
 * @property {boolean} hidden        Set to true if the field should not be
 *                                   rendered.
 * @property {number}  index         Fields will be sorted and render in this
 *                                   order, lowest to highest.
 */

/**
 * @typedef {string} CountryCode ISO 3166 Country code.
 */

/**
 * @typedef {string} AddressFieldKey Key for an address field, e.g. first_name.
 */

/**
 * @typedef {Object <CountryCode, Object <AddressFieldKey, AddressField>>} CountryAddressFields
 */

export {};
