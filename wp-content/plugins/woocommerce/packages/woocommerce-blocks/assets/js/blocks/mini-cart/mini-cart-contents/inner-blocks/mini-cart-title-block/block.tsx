/**
 * External dependencies
 */
import { sprintf, _n, __ } from '@wordpress/i18n';
import { useStoreCart } from '@woocommerce/base-context/hooks';
import classNames from 'classnames';

/**
 * Internal dependencies
 */

type MiniCartTitleBlockProps = {
	className: string;
};

const Block = ( { className }: MiniCartTitleBlockProps ): JSX.Element => {
	const { cartItemsCount, cartIsLoading } = useStoreCart();
	return (
		<h2 className={ classNames( className, 'wc-block-mini-cart__title' ) }>
			{ cartIsLoading
				? __( 'Your cart', 'woo-gutenberg-products-block' )
				: sprintf(
						/* translators: %d is the count of items in the cart. */
						_n(
							'Your cart (%d item)',
							'Your cart (%d items)',
							cartItemsCount,
							'woo-gutenberg-products-block'
						),
						cartItemsCount
				  ) }
		</h2>
	);
};

export default Block;
