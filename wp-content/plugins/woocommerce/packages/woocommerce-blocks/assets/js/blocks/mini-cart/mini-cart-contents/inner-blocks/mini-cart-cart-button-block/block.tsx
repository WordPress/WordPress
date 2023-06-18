/**
 * External dependencies
 */
import { CART_URL } from '@woocommerce/block-settings';
import Button from '@woocommerce/base-components/button';
import classNames from 'classnames';
import { useStyleProps } from '@woocommerce/base-hooks';

/**
 * Internal dependencies
 */
import { defaultCartButtonLabel } from './constants';
import { getVariant } from '../utils';

type MiniCartCartButtonBlockProps = {
	cartButtonLabel?: string;
	className?: string;
	style?: string;
};

const Block = ( {
	className,
	cartButtonLabel,
	style,
}: MiniCartCartButtonBlockProps ): JSX.Element | null => {
	const styleProps = useStyleProps( { style } );

	if ( ! CART_URL ) {
		return null;
	}

	return (
		<Button
			className={ classNames(
				className,
				styleProps.className,
				'wc-block-mini-cart__footer-cart'
			) }
			style={ styleProps.style }
			href={ CART_URL }
			variant={ getVariant( className, 'outlined' ) }
		>
			{ cartButtonLabel || defaultCartButtonLabel }
		</Button>
	);
};

export default Block;
