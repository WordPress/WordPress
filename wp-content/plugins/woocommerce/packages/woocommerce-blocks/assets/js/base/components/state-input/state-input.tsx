/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';
import { useCallback, useMemo, useEffect, useRef } from '@wordpress/element';
import classnames from 'classnames';
import { ValidatedTextInput } from '@woocommerce/blocks-checkout';

/**
 * Internal dependencies
 */
import Combobox from '../combobox';
import './style.scss';
import type { StateInputWithStatesProps } from './StateInputProps';

const optionMatcher = (
	value: string,
	options: { label: string; value: string }[]
): string => {
	const foundOption = options.find(
		( option ) =>
			option.label.toLocaleUpperCase() === value.toLocaleUpperCase() ||
			option.value.toLocaleUpperCase() === value.toLocaleUpperCase()
	);
	return foundOption ? foundOption.value : '';
};

const StateInput = ( {
	className,
	id,
	states,
	country,
	label,
	onChange,
	autoComplete = 'off',
	value = '',
	required = false,
	errorId = '',
}: StateInputWithStatesProps ): JSX.Element => {
	const countryStates = states[ country ];
	const options = useMemo(
		() =>
			countryStates
				? Object.keys( countryStates ).map( ( key ) => ( {
						value: key,
						label: decodeEntities( countryStates[ key ] ),
				  } ) )
				: [],
		[ countryStates ]
	);

	/**
	 * Handles state selection onChange events. Finds a matching state by key or value.
	 */
	const onChangeState = useCallback(
		( stateValue: string ) => {
			onChange(
				options.length > 0
					? optionMatcher( stateValue, options )
					: stateValue
			);
		},
		[ onChange, options ]
	);

	/**
	 * Track value changes.
	 */
	const valueRef = useRef< string >( value );

	useEffect( () => {
		if ( valueRef.current !== value ) {
			valueRef.current = value;
		}
	}, [ value ] );

	/**
	 * If given a list of options, ensure the value matches those options or trigger change.
	 */
	useEffect( () => {
		if ( options.length > 0 && valueRef.current ) {
			const match = optionMatcher( valueRef.current, options );
			if ( match !== valueRef.current ) {
				onChangeState( match );
			}
		}
	}, [ options, onChangeState ] );

	if ( options.length > 0 ) {
		return (
			<Combobox
				className={ classnames(
					className,
					'wc-block-components-state-input'
				) }
				id={ id }
				label={ label }
				onChange={ onChangeState }
				options={ options }
				value={ value }
				errorMessage={ __(
					'Please select a state.',
					'woo-gutenberg-products-block'
				) }
				errorId={ errorId }
				required={ required }
				autoComplete={ autoComplete }
			/>
		);
	}

	return (
		<ValidatedTextInput
			className={ className }
			id={ id }
			label={ label }
			onChange={ onChangeState }
			autoComplete={ autoComplete }
			value={ value }
			required={ required }
		/>
	);
};

export default StateInput;
