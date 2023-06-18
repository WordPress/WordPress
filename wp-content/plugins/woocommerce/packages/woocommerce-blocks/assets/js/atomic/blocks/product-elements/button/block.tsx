/**
 * External dependencies
 */
import classnames from 'classnames';
import { __, _n, sprintf } from '@wordpress/i18n';
import {
	useStoreEvents,
	useStoreAddToCart,
} from '@woocommerce/base-context/hooks';
import { useStyleProps } from '@woocommerce/base-hooks';
import { decodeEntities } from '@wordpress/html-entities';
import { CART_URL } from '@woocommerce/block-settings';
import { getSetting } from '@woocommerce/settings';
import {
	useInnerBlockLayoutContext,
	useProductDataContext,
} from '@woocommerce/shared-context';
import { withProductDataContext } from '@woocommerce/shared-hocs';

/**
 * Internal dependencies
 */
import './style.scss';
import type {
	BlockAttributes,
	AddToCartButtonAttributes,
	AddToCartButtonPlaceholderAttributes,
} from './types';

/**
 * Product Button Block Component.
 *
 * @param {Object} props             Incoming props.
 * @param {Object} [props.product]   Product.
 * @param {Object} [props.style]     Object contains CSS Styles.
 * @param {string} [props.className] String contains CSS class.
 * @param {Object} [props.textAlign] Text alignment.
 *
 * @return {*} The component.
 */
const AddToCartButton = ( {
	product,
	className,
	style,
	textAlign,
}: AddToCartButtonAttributes ): JSX.Element => {
	const {
		id,
		permalink,
		add_to_cart: productCartDetails,
		has_options: hasOptions,
		is_purchasable: isPurchasable,
		is_in_stock: isInStock,
	} = product;
	const { dispatchStoreEvent } = useStoreEvents();
	const { cartQuantity, addingToCart, addToCart } = useStoreAddToCart( id );

	const addedToCart = Number.isFinite( cartQuantity ) && cartQuantity > 0;
	const allowAddToCart = ! hasOptions && isPurchasable && isInStock;
	const buttonAriaLabel = decodeEntities(
		productCartDetails?.description || ''
	);
	const buttonText = addedToCart
		? sprintf(
				/* translators: %s number of products in cart. */
				_n(
					'%d in cart',
					'%d in cart',
					cartQuantity,
					'woo-gutenberg-products-block'
				),
				cartQuantity
		  )
		: decodeEntities(
				productCartDetails?.text ||
					__( 'Add to cart', 'woo-gutenberg-products-block' )
		  );

	const ButtonTag = allowAddToCart ? 'button' : 'a';
	const buttonProps = {} as HTMLAnchorElement & { onClick: () => void };

	if ( ! allowAddToCart ) {
		buttonProps.href = permalink;
		buttonProps.rel = 'nofollow';
		buttonProps.onClick = () => {
			dispatchStoreEvent( 'product-view-link', {
				product,
			} );
		};
	} else {
		buttonProps.onClick = async () => {
			await addToCart();
			dispatchStoreEvent( 'cart-add-item', {
				product,
			} );
			// redirect to cart if the setting to redirect to the cart page
			// on cart add item is enabled
			const { cartRedirectAfterAdd }: { cartRedirectAfterAdd: boolean } =
				getSetting( 'productsSettings' );
			if ( cartRedirectAfterAdd ) {
				window.location.href = CART_URL;
			}
		};
	}

	return (
		<ButtonTag
			{ ...buttonProps }
			aria-label={ buttonAriaLabel }
			disabled={ addingToCart }
			className={ classnames(
				className,
				'wp-block-button__link',
				'wp-element-button',
				'add_to_cart_button',
				'wc-block-components-product-button__button',
				{
					loading: addingToCart,
					added: addedToCart,
				},
				{
					[ `has-text-align-${ textAlign }` ]: textAlign,
				}
			) }
			style={ style }
		>
			{ buttonText }
		</ButtonTag>
	);
};

/**
 * Product Button Block Component.
 *
 * @param {Object} props             Incoming props.
 * @param {Object} [props.style]     Object contains CSS Styles.
 * @param {string} [props.className] String contains CSS class.
 *
 * @return {*} The component.
 */
const AddToCartButtonPlaceholder = ( {
	className,
	style,
}: AddToCartButtonPlaceholderAttributes ): JSX.Element => {
	return (
		<button
			className={ classnames(
				'wp-block-button__link',
				'wp-element-button',
				'add_to_cart_button',
				'wc-block-components-product-button__button',
				'wc-block-components-product-button__button--placeholder',
				className
			) }
			style={ style }
			disabled={ true }
		/>
	);
};

/**
 * Product Button Block Component.
 *
 * @param {Object} props             Incoming props.
 * @param {string} [props.className] CSS Class name for the component.
 * @param {string} [props.textAlign] Text alignment.
 * @return {*} The component.
 */
export const Block = ( props: BlockAttributes ): JSX.Element => {
	const { className, textAlign } = props;
	const styleProps = useStyleProps( props );
	const { parentClassName } = useInnerBlockLayoutContext();
	const { product } = useProductDataContext();

	return (
		<div
			className={ classnames(
				className,
				'wp-block-button',
				'wc-block-components-product-button',
				{
					[ `${ parentClassName }__product-add-to-cart` ]:
						parentClassName,
				},
				{
					[ `has-text-align-${ textAlign }` ]: textAlign,
				}
			) }
		>
			{ product.id ? (
				<AddToCartButton
					product={ product }
					style={ styleProps.style }
					className={ styleProps.className }
				/>
			) : (
				<AddToCartButtonPlaceholder
					style={ styleProps.style }
					className={ styleProps.className }
				/>
			) }
		</div>
	);
};

export default withProductDataContext( Block );
