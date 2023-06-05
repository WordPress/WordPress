/**
 * External dependencies
 */
import { cleanForSlug } from '@wordpress/url';
import { __ } from '@wordpress/i18n';
import { isObject } from '@woocommerce/types';
import { getSetting } from '@woocommerce/settings';
/**
 * Internal dependencies
 */
import type {
	PickupLocation,
	SortablePickupLocation,
	ShippingMethodSettings,
} from './types';

export const indexLocationsById = (
	locations: PickupLocation[]
): SortablePickupLocation[] => {
	return locations.map( ( value, index ) => {
		return {
			...value,
			id: cleanForSlug( value.name ) + '-' + index,
		};
	} );
};

export const defaultSettings = {
	enabled: false,
	title: __( 'Local Pickup', 'woo-gutenberg-products-block' ),
	tax_status: 'taxable',
	cost: '',
};

export const defaultReadyOnlySettings = {
	hasLegacyPickup: false,
	storeCountry: '',
	storeState: '',
};
declare global {
	const hydratedScreenSettings: {
		pickupLocationSettings: {
			enabled: string;
			title: string;
			tax_status: string;
			cost: string;
		};
		pickupLocations: PickupLocation[];
		readonlySettings: typeof defaultReadyOnlySettings;
	};
}

export const getInitialSettings = (): ShippingMethodSettings => {
	const settings = hydratedScreenSettings.pickupLocationSettings;

	return {
		enabled: settings?.enabled
			? settings?.enabled === 'yes'
			: defaultSettings.enabled,
		title: settings?.title || defaultSettings.title,
		tax_status: settings?.tax_status || defaultSettings.tax_status,
		cost: settings?.cost || defaultSettings.cost,
	};
};

export const getInitialPickupLocations = (): SortablePickupLocation[] =>
	indexLocationsById( hydratedScreenSettings.pickupLocations || [] );

export const readOnlySettings =
	hydratedScreenSettings.readonlySettings || defaultReadyOnlySettings;

export const countries = getSetting< Record< string, string > >(
	'countries',
	[]
);
export const states = getSetting< Record< string, Record< string, string > > >(
	'countryStates',
	[]
);
export const getUserFriendlyAddress = ( address: unknown ) => {
	const updatedAddress = isObject( address ) && {
		...address,
		country:
			typeof address.country === 'string' && countries[ address.country ],
		state:
			typeof address.country === 'string' &&
			typeof address.state === 'string' &&
			states[ address.country ]?.[ address.state ]
				? states[ address.country ][ address.state ]
				: address.state,
	};

	return Object.values( updatedAddress )
		.filter( ( value ) => value !== '' )
		.join( ', ' );
};

// Outputs the list of countries and states in a single dropdown select.
const countryStateDropdownOptions = () => {
	const countryStateOptions = Object.keys( countries ).map( ( country ) => {
		const countryStates = states[ country ] || {};

		if ( Object.keys( countryStates ).length === 0 ) {
			return {
				options: [
					{
						value: country,
						label: countries[ country ],
					},
				],
			};
		}

		const stateOptions = Object.keys( countryStates ).map( ( state ) => ( {
			value: `${ country }:${ state }`,
			label: `${ countries[ country ] } — ${ countryStates[ state ] }`,
		} ) );
		return {
			label: countries[ country ],
			options: [ ...stateOptions ],
		};
	} );
	return {
		options: countryStateOptions,
	};
};
export const countryStateOptions = countryStateDropdownOptions();
