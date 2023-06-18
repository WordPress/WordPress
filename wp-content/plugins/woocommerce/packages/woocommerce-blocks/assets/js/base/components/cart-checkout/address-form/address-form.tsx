/**
 * External dependencies
 */
import { ValidatedTextInput, isPostcode } from '@woocommerce/blocks-checkout';
import {
	BillingCountryInput,
	ShippingCountryInput,
} from '@woocommerce/base-components/country-input';
import {
	BillingStateInput,
	ShippingStateInput,
} from '@woocommerce/base-components/state-input';
import { useEffect, useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { withInstanceId } from '@wordpress/compose';
import { useShallowEqual } from '@woocommerce/base-hooks';
import {
	AddressField,
	AddressFields,
	AddressType,
	defaultAddressFields,
	ShippingAddress,
} from '@woocommerce/settings';
import { useSelect, useDispatch, dispatch } from '@wordpress/data';
import { VALIDATION_STORE_KEY } from '@woocommerce/block-data';
import { FieldValidationStatus } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import prepareAddressFields from './prepare-address-fields';

// If it's the shipping address form and the user starts entering address
// values without having set the country first, show an error.
const validateShippingCountry = (
	values: ShippingAddress,
	setValidationErrors: (
		errors: Record< string, FieldValidationStatus >
	) => void,
	clearValidationError: ( error: string ) => void,
	hasValidationError: boolean
): void => {
	const validationErrorId = 'shipping_country';
	if (
		! hasValidationError &&
		! values.country &&
		( values.city || values.state || values.postcode )
	) {
		setValidationErrors( {
			[ validationErrorId ]: {
				message: __(
					'Please select a country to calculate rates.',
					'woo-gutenberg-products-block'
				),
				hidden: false,
			},
		} );
	}
	if ( hasValidationError && values.country ) {
		clearValidationError( validationErrorId );
	}
};

interface AddressFormProps {
	// Id for component.
	id?: string;
	// Unique id for form.
	instanceId: string;
	// Array of fields in form.
	fields: ( keyof AddressFields )[];
	// Field configuration for fields in form.
	fieldConfig?: Record< keyof AddressFields, Partial< AddressField > >;
	// Function to all for an form onChange event.
	onChange: ( newValue: ShippingAddress ) => void;
	// Type of form.
	type?: AddressType;
	// Values for fields.
	values: ShippingAddress;
}

/**
 * Checkout address form.
 */
const AddressForm = ( {
	id = '',
	fields = Object.keys(
		defaultAddressFields
	) as unknown as ( keyof AddressFields )[],
	fieldConfig = {} as Record< keyof AddressFields, Partial< AddressField > >,
	instanceId,
	onChange,
	type = 'shipping',
	values,
}: AddressFormProps ): JSX.Element => {
	const validationErrorId = 'shipping_country';
	const { setValidationErrors, clearValidationError } =
		useDispatch( VALIDATION_STORE_KEY );

	const countryValidationError = useSelect( ( select ) => {
		const store = select( VALIDATION_STORE_KEY );
		return store.getValidationError( validationErrorId );
	} );

	const currentFields = useShallowEqual( fields );

	const addressFormFields = useMemo( () => {
		return prepareAddressFields(
			currentFields,
			fieldConfig,
			values.country
		);
	}, [ currentFields, fieldConfig, values.country ] );

	// Clear values for hidden fields.
	useEffect( () => {
		addressFormFields.forEach( ( field ) => {
			if ( field.hidden && values[ field.key ] ) {
				onChange( {
					...values,
					[ field.key ]: '',
				} );
			}
		} );
	}, [ addressFormFields, onChange, values ] );

	// Clear postcode validation error if postcode is not required.
	useEffect( () => {
		addressFormFields.forEach( ( field ) => {
			if ( field.key === 'postcode' && field.required === false ) {
				const store = dispatch( 'wc/store/validation' );

				if ( type === 'shipping' ) {
					store.clearValidationError( 'shipping_postcode' );
				}

				if ( type === 'billing' ) {
					store.clearValidationError( 'billing_postcode' );
				}
			}
		} );
	}, [ addressFormFields, type, clearValidationError ] );

	useEffect( () => {
		if ( type === 'shipping' ) {
			validateShippingCountry(
				values,
				setValidationErrors,
				clearValidationError,
				!! countryValidationError?.message &&
					! countryValidationError?.hidden
			);
		}
	}, [
		values,
		countryValidationError?.message,
		countryValidationError?.hidden,
		setValidationErrors,
		clearValidationError,
		type,
	] );

	id = id || instanceId;

	/**
	 * Custom validation handler for fields with field specific handling.
	 */
	const customValidationHandler = (
		inputObject: HTMLInputElement,
		field: string,
		customValues: {
			country: string;
		}
	): boolean => {
		if (
			field === 'postcode' &&
			customValues.country &&
			! isPostcode( {
				postcode: inputObject.value,
				country: customValues.country,
			} )
		) {
			inputObject.setCustomValidity(
				__(
					'Please enter a valid postcode',
					'woo-gutenberg-products-block'
				)
			);
			return false;
		}
		return true;
	};

	return (
		<div id={ id } className="wc-block-components-address-form">
			{ addressFormFields.map( ( field ) => {
				if ( field.hidden ) {
					return null;
				}

				// Create a consistent error ID based on the field key and type
				const errorId = `${ type }_${ field.key }`;

				if ( field.key === 'country' ) {
					const Tag =
						type === 'shipping'
							? ShippingCountryInput
							: BillingCountryInput;
					return (
						<Tag
							key={ field.key }
							id={ `${ id }-${ field.key }` }
							errorId={ errorId }
							label={
								field.required
									? field.label
									: field.optionalLabel
							}
							value={ values.country }
							autoComplete={ field.autocomplete }
							onChange={ ( newValue ) =>
								onChange( {
									...values,
									country: newValue,
									state: '',
								} )
							}
							errorMessage={ field.errorMessage }
							required={ field.required }
						/>
					);
				}

				if ( field.key === 'state' ) {
					const Tag =
						type === 'shipping'
							? ShippingStateInput
							: BillingStateInput;
					return (
						<Tag
							key={ field.key }
							id={ `${ id }-${ field.key }` }
							errorId={ errorId }
							country={ values.country }
							label={
								field.required
									? field.label
									: field.optionalLabel
							}
							value={ values.state }
							autoComplete={ field.autocomplete }
							onChange={ ( newValue ) =>
								onChange( {
									...values,
									state: newValue,
								} )
							}
							errorMessage={ field.errorMessage }
							required={ field.required }
						/>
					);
				}

				return (
					<ValidatedTextInput
						key={ field.key }
						id={ `${ id }-${ field.key }` }
						errorId={ errorId }
						className={ `wc-block-components-address-form__${ field.key }` }
						label={
							field.required ? field.label : field.optionalLabel
						}
						value={ values[ field.key ] }
						autoCapitalize={ field.autocapitalize }
						autoComplete={ field.autocomplete }
						onChange={ ( newValue: string ) =>
							onChange( {
								...values,
								[ field.key ]:
									field.key === 'postcode'
										? newValue.trimStart().toUpperCase()
										: newValue,
							} )
						}
						customValidation={ ( inputObject: HTMLInputElement ) =>
							field.required || inputObject.value
								? customValidationHandler(
										inputObject,
										field.key,
										values
								  )
								: true
						}
						errorMessage={ field.errorMessage }
						required={ field.required }
					/>
				);
			} ) }
		</div>
	);
};

export default withInstanceId( AddressForm );
