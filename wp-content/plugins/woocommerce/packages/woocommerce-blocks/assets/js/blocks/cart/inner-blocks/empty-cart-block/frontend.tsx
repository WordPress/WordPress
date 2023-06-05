/**
 * External dependencies
 */
import { useStoreCart } from '@woocommerce/base-context/hooks';
import { useEffect } from '@wordpress/element';
import { dispatchEvent } from '@woocommerce/base-utils';

/**
 * Internal dependencies
 */
import './style.scss';

const FrontendBlock = ( {
	children,
	className,
}: {
	children: JSX.Element;
	className: string;
} ): JSX.Element | null => {
	const { cartItems, cartIsLoading } = useStoreCart();
	useEffect( () => {
		dispatchEvent( 'wc-blocks_render_blocks_frontend', {
			element: document.body.querySelector(
				'.wp-block-woocommerce-cart'
			),
		} );
	}, [] );
	if ( ! cartIsLoading && cartItems.length === 0 ) {
		return <div className={ className }>{ children }</div>;
	}
	return null;
};

export default FrontendBlock;
