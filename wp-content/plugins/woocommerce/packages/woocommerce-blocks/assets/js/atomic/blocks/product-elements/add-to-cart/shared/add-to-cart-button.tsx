/**
 * External dependencies
 */
import { __, _n, sprintf } from '@wordpress/i18n';
import Button, { ButtonProps } from '@woocommerce/base-components/button';
import { Icon, check } from '@wordpress/icons';
import { useState, useEffect } from '@wordpress/element';
import { useAddToCartFormContext } from '@woocommerce/base-context';
import {
	useStoreEvents,
	useStoreAddToCart,
} from '@woocommerce/base-context/hooks';
import { useInnerBlockLayoutContext } from '@woocommerce/shared-context';

type LinkProps = Pick< ButtonProps, 'className' | 'href' | 'onClick' | 'text' >;

interface ButtonComponentProps
	extends Pick< ButtonProps, 'className' | 'onClick' > {
	/**
	 * Whether the button is disabled or not.
	 */
	isDisabled: boolean;
	/**
	 * Whether processing is done.
	 */
	isDone: boolean;
	/**
	 * Whether processing action is occurring.
	 */
	isProcessing: ButtonProps[ 'showSpinner' ];
	/**
	 * Quantity of said item currently in the cart.
	 */
	quantityInCart: number;
}

/**
 * Button component for non-purchasable products.
 */
const LinkComponent = ( { className, href, text, onClick }: LinkProps ) => {
	return (
		<Button
			className={ className }
			href={ href }
			onClick={ onClick }
			rel="nofollow"
		>
			{ text }
		</Button>
	);
};

/**
 * Button for purchasable products.
 */
const ButtonComponent = ( {
	className,
	quantityInCart,
	isProcessing,
	isDisabled,
	isDone,
	onClick,
}: ButtonComponentProps ) => {
	return (
		<Button
			className={ className }
			disabled={ isDisabled }
			showSpinner={ isProcessing }
			onClick={ onClick }
		>
			{ isDone && quantityInCart > 0
				? sprintf(
						/* translators: %s number of products in cart. */
						_n(
							'%d in cart',
							'%d in cart',
							quantityInCart,
							'woo-gutenberg-products-block'
						),
						quantityInCart
				  )
				: __( 'Add to cart', 'woo-gutenberg-products-block' ) }
			{ !! isDone && <Icon icon={ check } /> }
		</Button>
	);
};

/**
 * Add to Cart Form Button Component.
 */
const AddToCartButton = () => {
	// @todo Add types for `useAddToCartFormContext`
	const {
		showFormElements,
		productIsPurchasable,
		productHasOptions,
		product,
		productType,
		isDisabled,
		isProcessing,
		eventRegistration,
		hasError,
		dispatchActions,
	} = useAddToCartFormContext();
	const { parentName } = useInnerBlockLayoutContext();
	const { dispatchStoreEvent } = useStoreEvents();
	const { cartQuantity } = useStoreAddToCart( product.id || 0 );
	const [ addedToCart, setAddedToCart ] = useState( false );
	const addToCartButtonData = product.add_to_cart || {
		url: '',
		text: '',
	};

	// Subscribe to emitter for after processing.
	useEffect( () => {
		const onSuccess = () => {
			if ( ! hasError ) {
				setAddedToCart( true );
			}
			return true;
		};
		const unsubscribeProcessing =
			eventRegistration.onAddToCartAfterProcessingWithSuccess(
				onSuccess,
				0
			);
		return () => {
			unsubscribeProcessing();
		};
	}, [ eventRegistration, hasError ] );

	/**
	 * We can show a real button if we are:
	 *
	 *  	a) Showing a full add to cart form.
	 * 		b) The product doesn't have options and can therefore be added directly to the cart.
	 * 		c) The product is purchasable.
	 *
	 * Otherwise we show a link instead.
	 */
	const showButton =
		( showFormElements ||
			( ! productHasOptions && productType === 'simple' ) ) &&
		productIsPurchasable;

	return showButton ? (
		<ButtonComponent
			className="wc-block-components-product-add-to-cart-button"
			quantityInCart={ cartQuantity }
			isDisabled={ isDisabled }
			isProcessing={ isProcessing }
			isDone={ addedToCart }
			onClick={ () => {
				dispatchActions.submitForm(
					`woocommerce/single-product/${ product?.id || 0 }`
				);
				dispatchStoreEvent( 'cart-add-item', {
					product,
					listName: parentName,
				} );
			} }
		/>
	) : (
		<LinkComponent
			className="wc-block-components-product-add-to-cart-button"
			href={ addToCartButtonData.url }
			text={
				addToCartButtonData.text ||
				__( 'View Product', 'woo-gutenberg-products-block' )
			}
			onClick={ () => {
				dispatchStoreEvent( 'product-view-link', {
					product,
					listName: parentName,
				} );
			} }
		/>
	);
};

export default AddToCartButton;
