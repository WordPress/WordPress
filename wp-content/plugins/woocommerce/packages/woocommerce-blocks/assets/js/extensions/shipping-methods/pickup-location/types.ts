/**
 * External dependencies
 */
import type { UniqueIdentifier } from '@dnd-kit/core';

/**
 * Internal dependencies
 */
import type { SortableData } from '../shared-components';

export interface PickupLocation {
	name: string;
	details: string;
	enabled: boolean;
	address: {
		address_1: string;
		city: string;
		state: string;
		postcode: string;
		country: string;
	};
}

export interface SortablePickupLocation extends PickupLocation, SortableData {}

export type ShippingMethodSettings = {
	enabled: boolean;
	title: string;
	tax_status: string;
	cost: string;
};

export type ReadOnlySettings = {
	storeCountry: string;
	storeState: string;
	hasLegacyPickup: boolean;
};

export type SettingsContextType = {
	settings: ShippingMethodSettings;
	readOnlySettings: ReadOnlySettings;
	setSettingField: (
		field: keyof ShippingMethodSettings
	) => ( value: unknown ) => void;
	pickupLocations: SortablePickupLocation[];
	setPickupLocations: ( locations: SortablePickupLocation[] ) => void;
	toggleLocation: ( rowId: UniqueIdentifier ) => void;
	updateLocation: (
		rowId: UniqueIdentifier | 'new',
		location: SortablePickupLocation | null
	) => void;
	isSaving: boolean;
	save: () => void;
};
