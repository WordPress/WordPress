/**
 * External dependencies
 */
import { useStoreCart } from '@woocommerce/base-context/hooks';
import { useEffect, useRef } from '@wordpress/element';

/**
 * Internal dependencies
 */

type EmptyMiniCartContentsBlockProps = {
	children: JSX.Element | JSX.Element[];
	className: string;
};

const EmptyMiniCartContentsBlock = ( {
	children,
	className,
}: EmptyMiniCartContentsBlockProps ): JSX.Element | null => {
	const { cartItems, cartIsLoading } = useStoreCart();

	const elementRef = useRef< HTMLDivElement >( null );

	useEffect( () => {
		if ( cartItems.length === 0 && ! cartIsLoading ) {
			elementRef.current?.focus();
		}
	}, [ cartItems, cartIsLoading ] );

	if ( cartIsLoading || cartItems.length > 0 ) {
		return null;
	}

	return (
		<div tabIndex={ -1 } ref={ elementRef } className={ className }>
			<div className="wc-block-mini-cart__empty-cart-wrapper">
				{ children }
			</div>
		</div>
	);
};

export default EmptyMiniCartContentsBlock;
