/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useInnerBlockLayoutContext } from '@woocommerce/shared-context';
import { Icon, warning } from '@wordpress/icons';

const NoProducts = (): JSX.Element => {
	const { parentClassName } = useInnerBlockLayoutContext();
	return (
		<div className={ `${ parentClassName }__no-products` }>
			<Icon
				className={ `${ parentClassName }__no-products-image` }
				icon={ warning }
				size={ 100 }
			/>
			<strong className={ `${ parentClassName }__no-products-title` }>
				{ __( 'No products', 'woo-gutenberg-products-block' ) }
			</strong>
			<p className={ `${ parentClassName }__no-products-description` }>
				{ __(
					'There are currently no products available to display.',
					'woo-gutenberg-products-block'
				) }
			</p>
		</div>
	);
};

export default NoProducts;
