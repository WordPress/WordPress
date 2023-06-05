/**
 * External dependencies
 */
import {
	createContext,
	useContext,
	useCallback,
	useState,
} from '@wordpress/element';
import { cleanForSlug } from '@wordpress/url';
import type { UniqueIdentifier } from '@dnd-kit/core';
import apiFetch from '@wordpress/api-fetch';
import { dispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { isEqual } from 'lodash';

/**
 * Internal dependencies
 */
import type {
	SortablePickupLocation,
	SettingsContextType,
	ShippingMethodSettings,
} from './types';
import {
	defaultSettings,
	getInitialSettings,
	defaultReadyOnlySettings,
	readOnlySettings,
	getInitialPickupLocations,
} from './utils';

const SettingsContext = createContext< SettingsContextType >( {
	settings: defaultSettings,
	readOnlySettings: defaultReadyOnlySettings,
	setSettingField: () => () => void null,
	pickupLocations: [],
	setPickupLocations: () => void null,
	toggleLocation: () => void null,
	updateLocation: () => void null,
	isSaving: false,
	save: () => void null,
} );

export const useSettingsContext = (): SettingsContextType => {
	return useContext( SettingsContext );
};

export const SettingsProvider = ( {
	children,
}: {
	children: JSX.Element[] | JSX.Element;
} ): JSX.Element => {
	const [ isSaving, setIsSaving ] = useState( false );
	const [ pickupLocations, setPickupLocations ] = useState<
		SortablePickupLocation[]
	>( getInitialPickupLocations );
	const [ settings, setSettings ] =
		useState< ShippingMethodSettings >( getInitialSettings );

	const setSettingField = useCallback(
		( field: keyof ShippingMethodSettings ) => ( newValue: unknown ) => {
			setSettings( ( prevValue ) => ( {
				...prevValue,
				[ field ]: newValue,
			} ) );
		},
		[]
	);

	const toggleLocation = useCallback( ( rowId: UniqueIdentifier ) => {
		setPickupLocations( ( previousLocations: SortablePickupLocation[] ) => {
			const locationIndex = previousLocations.findIndex(
				( { id } ) => id === rowId
			);
			const updated = [ ...previousLocations ];
			updated[ locationIndex ].enabled =
				! previousLocations[ locationIndex ].enabled;
			return updated;
		} );
	}, [] );

	const updateLocation = (
		rowId: UniqueIdentifier | 'new',
		locationData: SortablePickupLocation
	) => {
		setPickupLocations( ( prevData ) => {
			if ( rowId === 'new' ) {
				return [
					...prevData,
					{
						...locationData,
						id:
							cleanForSlug( locationData.name ) +
							'-' +
							prevData.length,
					},
				];
			}
			return prevData
				.map( ( location ): SortablePickupLocation => {
					if ( location.id === rowId ) {
						return locationData;
					}
					return location;
				} )
				.filter( Boolean );
		} );
	};

	const save = useCallback( () => {
		const data = {
			pickup_location_settings: {
				enabled: settings.enabled ? 'yes' : 'no',
				title: settings.title,
				tax_status: [ 'taxable', 'none' ].includes(
					settings.tax_status
				)
					? settings.tax_status
					: 'taxable',
				cost: settings.cost,
			},
			pickup_locations: pickupLocations.map( ( location ) => ( {
				name: location.name,
				address: location.address,
				details: location.details,
				enabled: location.enabled,
			} ) ),
		};

		setIsSaving( true );

		// @todo This should be improved to include error handling in case of API failure, or invalid data being sent that
		// does not match the schema. This would fail silently on the API side.
		apiFetch( {
			path: '/wp/v2/settings',
			method: 'POST',
			data,
		} ).then( ( response ) => {
			setIsSaving( false );
			if (
				isEqual(
					response.pickup_location_settings,
					data.pickup_location_settings
				) &&
				isEqual( response.pickup_locations, data.pickup_locations )
			) {
				dispatch( 'core/notices' ).createSuccessNotice(
					__(
						'Local Pickup settings have been saved.',
						'woo-gutenberg-products-block'
					)
				);
			}
		} );
	}, [ settings, pickupLocations ] );

	const settingsData = {
		settings,
		setSettingField,
		readOnlySettings,
		pickupLocations,
		setPickupLocations,
		toggleLocation,
		updateLocation,
		isSaving,
		save,
	};

	return (
		<SettingsContext.Provider value={ settingsData }>
			{ children }
		</SettingsContext.Provider>
	);
};
