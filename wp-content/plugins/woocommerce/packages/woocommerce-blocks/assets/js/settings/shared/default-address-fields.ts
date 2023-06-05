/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';

export interface AddressField {
	// The label for the field.
	label: string;
	// The label for the field if made optional.
	optionalLabel: string;
	// The HTML autocomplete attribute value. See https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/autocomplete
	autocomplete: string;
	// How this field value is capitalized.
	autocapitalize?: string;
	// Set to true if the field is required.
	required: boolean;
	// Set to true if the field should not be rendered.
	hidden: boolean;
	// Fields will be sorted and render in this order, lowest to highest.
	index: number;
}

export interface LocaleSpecificAddressField extends AddressField {
	priority: number;
}

export interface AddressFields {
	first_name: AddressField;
	last_name: AddressField;
	company: AddressField;
	address_1: AddressField;
	address_2: AddressField;
	country: AddressField;
	city: AddressField;
	state: AddressField;
	postcode: AddressField;
}

export type AddressType = 'billing' | 'shipping';
export interface ShippingAddress {
	first_name: string;
	last_name: string;
	company: string;
	address_1: string;
	address_2: string;
	country: string;
	city: string;
	state: string;
	postcode: string;
	phone: string;
}

export type KeyedAddressField = AddressField & {
	key: keyof AddressFields;
	errorMessage?: string;
};
export interface BillingAddress extends ShippingAddress {
	email: string;
}
export type CountryAddressFields = Record< string, AddressFields >;

/**
 * Default address field properties.
 */
export const defaultAddressFields: AddressFields = {
	first_name: {
		label: __( 'First name', 'woo-gutenberg-products-block' ),
		optionalLabel: __(
			'First name (optional)',
			'woo-gutenberg-products-block'
		),
		autocomplete: 'given-name',
		autocapitalize: 'sentences',
		required: true,
		hidden: false,
		index: 10,
	},
	last_name: {
		label: __( 'Last name', 'woo-gutenberg-products-block' ),
		optionalLabel: __(
			'Last name (optional)',
			'woo-gutenberg-products-block'
		),
		autocomplete: 'family-name',
		autocapitalize: 'sentences',
		required: true,
		hidden: false,
		index: 20,
	},
	company: {
		label: __( 'Company', 'woo-gutenberg-products-block' ),
		optionalLabel: __(
			'Company (optional)',
			'woo-gutenberg-products-block'
		),
		autocomplete: 'organization',
		autocapitalize: 'sentences',
		required: false,
		hidden: false,
		index: 30,
	},
	address_1: {
		label: __( 'Address', 'woo-gutenberg-products-block' ),
		optionalLabel: __(
			'Address (optional)',
			'woo-gutenberg-products-block'
		),
		autocomplete: 'address-line1',
		autocapitalize: 'sentences',
		required: true,
		hidden: false,
		index: 40,
	},
	address_2: {
		label: __( 'Apartment, suite, etc.', 'woo-gutenberg-products-block' ),
		optionalLabel: __(
			'Apartment, suite, etc. (optional)',
			'woo-gutenberg-products-block'
		),
		autocomplete: 'address-line2',
		autocapitalize: 'sentences',
		required: false,
		hidden: false,
		index: 50,
	},
	country: {
		label: __( 'Country/Region', 'woo-gutenberg-products-block' ),
		optionalLabel: __(
			'Country/Region (optional)',
			'woo-gutenberg-products-block'
		),
		autocomplete: 'country',
		required: true,
		hidden: false,
		index: 60,
	},
	city: {
		label: __( 'City', 'woo-gutenberg-products-block' ),
		optionalLabel: __( 'City (optional)', 'woo-gutenberg-products-block' ),
		autocomplete: 'address-level2',
		autocapitalize: 'sentences',
		required: true,
		hidden: false,
		index: 70,
	},
	state: {
		label: __( 'State/County', 'woo-gutenberg-products-block' ),
		optionalLabel: __(
			'State/County (optional)',
			'woo-gutenberg-products-block'
		),
		autocomplete: 'address-level1',
		autocapitalize: 'sentences',
		required: true,
		hidden: false,
		index: 80,
	},
	postcode: {
		label: __( 'Postal code', 'woo-gutenberg-products-block' ),
		optionalLabel: __(
			'Postal code (optional)',
			'woo-gutenberg-products-block'
		),
		autocomplete: 'postal-code',
		autocapitalize: 'characters',
		required: true,
		hidden: false,
		index: 90,
	},
};

export default defaultAddressFields;
