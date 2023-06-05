/**
 * Internal dependencies
 */
import { AddToCartFormStateContextProvider } from '../form-state';
import FormSubmit from './submit';

/**
 * Add to cart form provider.
 *
 * This wraps the add to cart form and provides an api interface for children via various hooks.
 *
 * @param {Object}  props                    Incoming props for the provider.
 * @param {Object}  props.children           The children being wrapped.
 * @param {Object}  [props.product]          The product for which the form belongs to.
 * @param {boolean} [props.showFormElements] Should form elements be shown.
 */
export const AddToCartFormContextProvider = ( {
	children,
	product,
	showFormElements,
} ) => {
	return (
		<AddToCartFormStateContextProvider
			product={ product }
			showFormElements={ showFormElements }
		>
			{ children }
			<FormSubmit />
		</AddToCartFormStateContextProvider>
	);
};
