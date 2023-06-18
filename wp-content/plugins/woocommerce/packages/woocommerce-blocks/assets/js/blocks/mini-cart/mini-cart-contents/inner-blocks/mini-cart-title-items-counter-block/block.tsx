/**
 * External dependencies
 */
import { useStoreCart } from '@woocommerce/base-context';
import classNames from 'classnames';
import { _n, sprintf } from '@wordpress/i18n';
import { useStyleProps } from '@woocommerce/base-hooks';

type Props = {
	className?: string;
};

const Block = ( props: Props ): JSX.Element => {
	const { cartItemsCount } = useStoreCart();
	const styleProps = useStyleProps( props );

	return (
		<span
			className={ classNames( props.className, styleProps.className ) }
			style={ styleProps.style }
		>
			{ sprintf(
				/* translators: %d is the count of items in the cart. */
				_n(
					'(%d item)',
					'(%d items)',
					cartItemsCount,
					'woo-gutenberg-products-block'
				),
				cartItemsCount
			) }
		</span>
	);
};

export default Block;
