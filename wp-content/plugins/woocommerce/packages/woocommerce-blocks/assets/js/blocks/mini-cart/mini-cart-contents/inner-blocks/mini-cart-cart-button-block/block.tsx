/**
 * External dependencies
 */
import { CART_URL } from '@woocommerce/block-settings';
import Button from '@woocommerce/base-components/button';
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import { defaultCartButtonLabel } from './constants';
import { getVariant } from '../utils';
import { useColorProps } from '../color-utils';

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
	const colorProps = useColorProps( { style } );

	if ( ! CART_URL ) {
		return null;
	}

	return (
		<Button
			className={ classNames(
				className,
				colorProps.className,
				'wc-block-mini-cart__footer-cart'
			) }
			style={ { ...colorProps.style } }
			href={ CART_URL }
			variant={ getVariant( className, 'outlined' ) }
		>
			{ cartButtonLabel || defaultCartButtonLabel }
		</Button>
	);
};

export default Block;
