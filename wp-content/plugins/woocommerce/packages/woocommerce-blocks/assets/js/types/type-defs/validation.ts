/**
 * An interface to describe the validity of a Checkout field. This is what will be stored in the wc/store/validation
 * data store.
 */
export interface FieldValidationStatus {
	/**
	 * The message to display to the user.
	 */
	message: string;
	/**
	 * Whether this validation error should be hidden. Note, hidden errors still prevent checkout. Adding a hidden error
	 * allows required fields to be validated, but not show the error to the user until they interact with the input
	 * element, or try to submit the form.
	 */
	hidden: boolean;
}
