/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';

/**
 * Converts an input's validityState to a string to display on the frontend.
 *
 * This returns custom messages for invalid/required fields. Other error types use defaults from the browser (these
 * could be implemented in the future but are not currently used by the block checkout).
 */
const getValidityMessageForInput = (
	label: string,
	inputElement: HTMLInputElement
): string => {
	const { valid, customError, valueMissing, badInput, typeMismatch } =
		inputElement.validity;

	// No errors, or custom error - return early.
	if ( valid || customError ) {
		return inputElement.validationMessage;
	}

	const invalidFieldMessage = sprintf(
		/* translators: %s field label */
		__( 'Please enter a valid %s', 'woo-gutenberg-products-block' ),
		label.toLowerCase()
	);

	if ( valueMissing || badInput || typeMismatch ) {
		return invalidFieldMessage;
	}

	return inputElement.validationMessage || invalidFieldMessage;
};

export default getValidityMessageForInput;
