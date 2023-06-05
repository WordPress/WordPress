/**
 * External dependencies
 */
import { useSelect } from '@wordpress/data';
import { VALIDATION_STORE_KEY } from '@woocommerce/block-data';

/**
 * Internal dependencies
 */
import './style.scss';

interface ValidationInputErrorProps {
	errorMessage?: string;
	propertyName?: string;
	elementId?: string;
}

export const ValidationInputError = ( {
	errorMessage = '',
	propertyName = '',
	elementId = '',
}: ValidationInputErrorProps ): JSX.Element | null => {
	const { validationError, validationErrorId } = useSelect( ( select ) => {
		const store = select( VALIDATION_STORE_KEY );
		return {
			validationError: store.getValidationError( propertyName ),
			validationErrorId: store.getValidationErrorId( elementId ),
		};
	} );

	if ( ! errorMessage || typeof errorMessage !== 'string' ) {
		if ( validationError?.message && ! validationError?.hidden ) {
			errorMessage = validationError.message;
		} else {
			return null;
		}
	}

	return (
		<div className="wc-block-components-validation-error" role="alert">
			<p id={ validationErrorId }>{ errorMessage }</p>
		</div>
	);
};

export default ValidationInputError;
