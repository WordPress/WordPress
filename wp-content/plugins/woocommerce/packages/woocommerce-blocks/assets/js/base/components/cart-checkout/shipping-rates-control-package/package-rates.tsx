/**
 * External dependencies
 */
import { useState, useEffect } from '@wordpress/element';
import RadioControl, {
	RadioControlOptionLayout,
} from '@woocommerce/base-components/radio-control';
import type { CartShippingPackageShippingRate } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { renderPackageRateOption } from './render-package-rate-option';
import type { PackageRateRenderOption } from '../shipping-rates-control-package';

interface PackageRates {
	onSelectRate: ( selectedRateId: string ) => void;
	rates: CartShippingPackageShippingRate[];
	renderOption?: PackageRateRenderOption | undefined;
	className?: string;
	noResultsMessage: JSX.Element;
	selectedRate: CartShippingPackageShippingRate | undefined;
}

const PackageRates = ( {
	className = '',
	noResultsMessage,
	onSelectRate,
	rates,
	renderOption = renderPackageRateOption,
	selectedRate,
}: PackageRates ): JSX.Element => {
	const selectedRateId = selectedRate?.rate_id || '';

	// Store selected rate ID in local state so shipping rates changes are shown in the UI instantly.
	const [ selectedOption, setSelectedOption ] = useState( selectedRateId );

	// Update the selected option if cart state changes in the data stores.
	useEffect( () => {
		if ( selectedRateId ) {
			setSelectedOption( selectedRateId );
		}
	}, [ selectedRateId ] );

	// Update the selected option if there is no rate selected on mount.
	useEffect( () => {
		if ( ! selectedOption && rates[ 0 ] ) {
			setSelectedOption( rates[ 0 ].rate_id );
			onSelectRate( rates[ 0 ].rate_id );
		}
	}, [ onSelectRate, rates, selectedOption ] );

	if ( rates.length === 0 ) {
		return noResultsMessage;
	}

	if ( rates.length > 1 ) {
		return (
			<RadioControl
				className={ className }
				onChange={ ( value: string ) => {
					setSelectedOption( value );
					onSelectRate( value );
				} }
				selected={ selectedOption }
				options={ rates.map( renderOption ) }
			/>
		);
	}

	const { label, secondaryLabel, description, secondaryDescription } =
		renderOption( rates[ 0 ] );

	return (
		<RadioControlOptionLayout
			label={ label }
			secondaryLabel={ secondaryLabel }
			description={ description }
			secondaryDescription={ secondaryDescription }
		/>
	);
};

export default PackageRates;
