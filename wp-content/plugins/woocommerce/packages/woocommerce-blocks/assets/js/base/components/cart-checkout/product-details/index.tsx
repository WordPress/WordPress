/**
 * External dependencies
 */
import { paramCase as kebabCase } from 'change-case';
import { decodeEntities } from '@wordpress/html-entities';
import type { ProductResponseItemData } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import './style.scss';

interface ProductDetailsProps {
	details: ProductResponseItemData[];
}
// Component to display cart item data and variations.
const ProductDetails = ( {
	details = [],
}: ProductDetailsProps ): JSX.Element | null => {
	if ( ! Array.isArray( details ) ) {
		return null;
	}

	details = details.filter( ( detail ) => ! detail.hidden );

	if ( details.length === 0 ) {
		return null;
	}

	return (
		<ul className="wc-block-components-product-details">
			{ details.map( ( detail ) => {
				// Support both `key` and `name` props
				const name = detail?.key || detail.name || '';
				const className =
					detail?.className ||
					( name
						? `wc-block-components-product-details__${ kebabCase(
								name
						  ) }`
						: '' );
				return (
					<li
						key={ name + ( detail.display || detail.value ) }
						className={ className }
					>
						{ name && (
							<>
								<span className="wc-block-components-product-details__name">
									{ decodeEntities( name ) }:
								</span>{ ' ' }
							</>
						) }
						<span className="wc-block-components-product-details__value">
							{ decodeEntities( detail.display || detail.value ) }
						</span>
					</li>
				);
			} ) }
		</ul>
	);
};

export default ProductDetails;
