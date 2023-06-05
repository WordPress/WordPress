/**
 * External dependencies
 */
import { useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import Combobox from '../combobox';
import './style.scss';
import type { CountryInputWithCountriesProps } from './CountryInputProps';

export const CountryInput = ( {
	className,
	countries,
	id,
	label,
	onChange,
	value = '',
	autoComplete = 'off',
	required = false,
	errorId,
	errorMessage = __(
		'Please select a country.',
		'woo-gutenberg-products-block'
	),
}: CountryInputWithCountriesProps ): JSX.Element => {
	const options = useMemo(
		() =>
			Object.entries( countries ).map(
				( [ countryCode, countryName ] ) => ( {
					value: countryCode,
					label: decodeEntities( countryName ),
				} )
			),
		[ countries ]
	);

	return (
		<div
			className={ classnames(
				className,
				'wc-block-components-country-input'
			) }
		>
			<Combobox
				id={ id }
				label={ label }
				onChange={ onChange }
				options={ options }
				value={ value }
				errorId={ errorId }
				errorMessage={ errorMessage }
				required={ required }
				autoComplete={ autoComplete }
			/>
		</div>
	);
};

export default CountryInput;
