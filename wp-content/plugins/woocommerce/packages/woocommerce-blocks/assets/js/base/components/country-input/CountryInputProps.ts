/**
 * Internal dependencies
 */
import { ComboboxProps } from '../combobox';
import { countries } from './stories/countries-filler';

export interface CountryInputProps extends Omit< ComboboxProps, 'options' > {
	/**
	 * Classes to assign to the wrapper component of the input
	 */
	className?: string;
	/**
	 * Whether input elements can by default have their values automatically completed by the browser.
	 *
	 * This value gets assigned to both the wrapper `Combobox` and the wrapped input element.
	 */
	autoComplete?: string;
}

export interface CountryInputWithCountriesProps extends CountryInputProps {
	/**
	 * List of countries to allow in the selection
	 *
	 * Object shape should be: `{ [Alpha-2 Country Code]: 'Full country name' }`
	 */
	countries: Partial< typeof countries >;
}
