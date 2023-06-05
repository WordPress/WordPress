/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import triggerFetch from '@wordpress/api-fetch';
import { useEffect, useCallback, useState } from '@wordpress/element';
import { decodeEntities } from '@wordpress/html-entities';
import { triggerAddedToCartEvent } from '@woocommerce/base-utils';
import { useDispatch, useSelect } from '@wordpress/data';
import { VALIDATION_STORE_KEY } from '@woocommerce/block-data';

/**
 * Internal dependencies
 */
import { useAddToCartFormContext } from '../../form-state';
import { useStoreCart } from '../../../../hooks/cart/use-store-cart';

/**
 * FormSubmit.
 *
 * Subscribes to add to cart form context and triggers processing via the API.
 */
const FormSubmit = () => {
	const {
		dispatchActions,
		product,
		quantity,
		eventRegistration,
		hasError,
		isProcessing,
		requestParams,
	} = useAddToCartFormContext();
	const { showAllValidationErrors } = useDispatch( VALIDATION_STORE_KEY );
	const hasValidationErrors = useSelect( ( select ) => {
		const store = select( VALIDATION_STORE_KEY );
		return store.hasValidationErrors;
	} );
	const { createErrorNotice, removeNotice } = useDispatch( 'core/notices' );
	const { receiveCart } = useStoreCart();
	const [ isSubmitting, setIsSubmitting ] = useState( false );
	const doSubmit = ! hasError && isProcessing;

	const checkValidationContext = useCallback( () => {
		if ( hasValidationErrors() ) {
			showAllValidationErrors();
			return {
				type: 'error',
			};
		}
		return true;
	}, [ hasValidationErrors, showAllValidationErrors ] );

	// Subscribe to emitter before processing.
	useEffect( () => {
		const unsubscribeProcessing =
			eventRegistration.onAddToCartBeforeProcessing(
				checkValidationContext,
				0
			);
		return () => {
			unsubscribeProcessing();
		};
	}, [ eventRegistration, checkValidationContext ] );

	// Triggers form submission to the API.
	const submitFormCallback = useCallback( () => {
		setIsSubmitting( true );
		removeNotice(
			'add-to-cart',
			`woocommerce/single-product/${ product?.id || 0 }`
		);

		const fetchData = {
			id: product.id || 0,
			quantity,
			...requestParams,
		};

		triggerFetch( {
			path: '/wc/store/v1/cart/add-item',
			method: 'POST',
			data: fetchData,
			cache: 'no-store',
			parse: false,
		} )
			.then( ( fetchResponse ) => {
				// Update nonce.
				triggerFetch.setNonce( fetchResponse.headers );

				// Handle response.
				fetchResponse.json().then( function ( response ) {
					if ( ! fetchResponse.ok ) {
						// We received an error response.
						if ( response.body && response.body.message ) {
							createErrorNotice(
								decodeEntities( response.body.message ),
								{
									id: 'add-to-cart',
									context: `woocommerce/single-product/${
										product?.id || 0
									}`,
								}
							);
						} else {
							createErrorNotice(
								__(
									'Something went wrong. Please contact us for assistance.',
									'woocommerce'
								),
								{
									id: 'add-to-cart',
									context: `woocommerce/single-product/${
										product?.id || 0
									}`,
								}
							);
						}
						dispatchActions.setHasError();
					} else {
						receiveCart( response );
					}
					triggerAddedToCartEvent( { preserveCartData: true } );
					dispatchActions.setAfterProcessing( response );
					setIsSubmitting( false );
				} );
			} )
			.catch( ( error ) => {
				error.json().then( function ( response ) {
					// If updated cart state was returned, also update that.
					if ( response.data?.cart ) {
						receiveCart( response.data.cart );
					}
					dispatchActions.setHasError();
					dispatchActions.setAfterProcessing( response );
					setIsSubmitting( false );
				} );
			} );
	}, [
		product,
		createErrorNotice,
		removeNotice,
		receiveCart,
		dispatchActions,
		quantity,
		requestParams,
	] );

	useEffect( () => {
		if ( doSubmit && ! isSubmitting ) {
			submitFormCallback();
		}
	}, [ doSubmit, submitFormCallback, isSubmitting ] );

	return null;
};

export default FormSubmit;
