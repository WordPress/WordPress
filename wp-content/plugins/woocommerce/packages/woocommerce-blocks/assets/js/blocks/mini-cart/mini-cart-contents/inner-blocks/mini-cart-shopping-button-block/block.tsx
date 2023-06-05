/**
 * External dependencies
 */
import { SHOP_URL } from '@woocommerce/block-settings';
import Button from '@woocommerce/base-components/button';
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import { defaultStartShoppingButtonLabel } from './constants';
import { getVariant } from '../utils';

type MiniCartShoppingButtonBlockProps = {
	className: string;
	startShoppingButtonLabel: string;
};

const Block = ( {
	className,
	startShoppingButtonLabel,
}: MiniCartShoppingButtonBlockProps ): JSX.Element | null => {
	if ( ! SHOP_URL ) {
		return null;
	}

	return (
		<div className="wp-block-button has-text-align-center">
			<Button
				className={ classNames(
					className,
					'wc-block-mini-cart__shopping-button'
				) }
				variant={ getVariant( className, 'contained' ) }
				href={ SHOP_URL }
			>
				{ startShoppingButtonLabel || defaultStartShoppingButtonLabel }
			</Button>
		</div>
	);
};

export default Block;
