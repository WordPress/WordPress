/**
 * External dependencies
 */
import { useRef, useEffect, useState, useCallback } from '@wordpress/element';
import classnames from 'classnames';
import { withInstanceId } from '@wordpress/compose';
import { isObject } from '@woocommerce/types';
import { useDispatch, useSelect } from '@wordpress/data';
import { VALIDATION_STORE_KEY } from '@woocommerce/block-data';
import { usePrevious } from '@woocommerce/base-hooks';
import type { InputHTMLAttributes } from 'react';

/**
 * Internal dependencies
 */
import TextInput from './text-input';
import './style.scss';
import { ValidationInputError } from '../validation-input-error';
import { getValidityMessageForInput } from '../../utils';

interface ValidatedTextInputProps
	extends Omit<
		InputHTMLAttributes< HTMLInputElement >,
		'onChange' | 'onBlur'
	> {
	// id to use for the input. If not provided, an id will be generated.
	id?: string;
	// Unique instance ID. id will be used instead if provided.
	instanceId: string;
	// Class name to add to the input.
	className?: string | undefined;
	// aria-describedby attribute to add to the input.
	ariaDescribedBy?: string | undefined;
	// id to use for the error message. If not provided, an id will be generated.
	errorId?: string;
	// if true, the input will be focused on mount.
	focusOnMount?: boolean;
	// Callback to run on change which is passed the updated value.
	onChange: ( newValue: string ) => void;
	// Optional label for the field.
	label?: string | undefined;
	// Field value.
	value: string;
	// If true, validation errors will be shown.
	showError?: boolean;
	// Error message to display alongside the field regardless of validation.
	errorMessage?: string | undefined;
	// Custom validation function that is run on change. Use setCustomValidity to set an error message.
	customValidation?:
		| ( ( inputObject: HTMLInputElement ) => boolean )
		| undefined;
}

const ValidatedTextInput = ( {
	className,
	instanceId,
	id,
	ariaDescribedBy,
	errorId,
	focusOnMount = false,
	onChange,
	showError = true,
	errorMessage: passedErrorMessage = '',
	value = '',
	customValidation,
	label,
	...rest
}: ValidatedTextInputProps ): JSX.Element => {
	const [ isPristine, setIsPristine ] = useState( true );
	const inputRef = useRef< HTMLInputElement >( null );
	const previousValue = usePrevious( value );
	const textInputId =
		typeof id !== 'undefined' ? id : 'textinput-' + instanceId;
	const errorIdString = errorId !== undefined ? errorId : textInputId;

	const { setValidationErrors, hideValidationError, clearValidationError } =
		useDispatch( VALIDATION_STORE_KEY );

	const { validationError, validationErrorId } = useSelect( ( select ) => {
		const store = select( VALIDATION_STORE_KEY );
		return {
			validationError: store.getValidationError( errorIdString ),
			validationErrorId: store.getValidationErrorId( errorIdString ),
		};
	} );

	const validateInput = useCallback(
		( errorsHidden = true ) => {
			const inputObject = inputRef.current || null;

			if ( inputObject === null ) {
				return;
			}

			// Trim white space before validation.
			inputObject.value = inputObject.value.trim();
			inputObject.setCustomValidity( '' );

			if ( previousValue === inputObject.value ) {
				return;
			}

			const inputIsValid = customValidation
				? inputObject.checkValidity() && customValidation( inputObject )
				: inputObject.checkValidity();

			if ( inputIsValid ) {
				clearValidationError( errorIdString );
				return;
			}

			setValidationErrors( {
				[ errorIdString ]: {
					message: label
						? getValidityMessageForInput( label, inputObject )
						: inputObject.validationMessage,
					hidden: errorsHidden,
				},
			} );
		},
		[
			previousValue,
			clearValidationError,
			customValidation,
			errorIdString,
			setValidationErrors,
			label,
		]
	);

	/**
	 * Handle browser autofill / changes via data store.
	 *
	 * Trigger validation on state change if the current element is not in focus. This is because autofilled elements do not
	 * trigger the blur() event, and so values can be validated in the background if the state changes elsewhere.
	 *
	 * Errors are immediately visible.
	 */
	useEffect( () => {
		if (
			value !== previousValue &&
			( value || previousValue ) &&
			inputRef &&
			inputRef.current !== null &&
			inputRef.current?.ownerDocument?.activeElement !== inputRef.current
		) {
			validateInput( false );
		}
		// We need to track value even if it is not directly used so we know when it changes.
	}, [ value, previousValue, validateInput ] );

	/**
	 * Validation on mount.
	 *
	 * If the input is in pristine state on mount, focus the element (if focusOnMount is enabled), and validate in the
	 * background.
	 *
	 * Errors are hidden until blur.
	 */
	useEffect( () => {
		if ( ! isPristine ) {
			return;
		}
		if ( focusOnMount ) {
			inputRef.current?.focus();
		}
		validateInput( true );
		setIsPristine( false );
	}, [ focusOnMount, isPristine, setIsPristine, validateInput ] );

	// Remove validation errors when unmounted.
	useEffect( () => {
		return () => {
			clearValidationError( errorIdString );
		};
	}, [ clearValidationError, errorIdString ] );

	if ( passedErrorMessage !== '' && isObject( validationError ) ) {
		validationError.message = passedErrorMessage;
	}

	const hasError = validationError?.message && ! validationError?.hidden;
	const describedBy =
		showError && hasError && validationErrorId
			? validationErrorId
			: ariaDescribedBy;

	return (
		<TextInput
			className={ classnames( className, {
				'has-error': hasError,
			} ) }
			aria-invalid={ hasError === true }
			id={ textInputId }
			feedback={
				showError && (
					<ValidationInputError
						errorMessage={ passedErrorMessage }
						propertyName={ errorIdString }
					/>
				)
			}
			ref={ inputRef }
			onChange={ ( val ) => {
				// Hide errors while typing.
				hideValidationError( errorIdString );

				// Revalidate on user input so we know if the value is valid.
				validateInput( true );

				// Push the changes up to the parent component if the value is valid.
				onChange( val );
			} }
			onBlur={ () => {
				validateInput( false );
			} }
			ariaDescribedBy={ describedBy }
			value={ value }
			title=""
			label={ label }
			{ ...rest }
		/>
	);
};
export const __ValidatedTexInputWithoutId = ValidatedTextInput;
export default withInstanceId( ValidatedTextInput );
