/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Icon, external } from '@wordpress/icons';
import { ADMIN_URL } from '@woocommerce/settings';
import { InspectorControls } from '@wordpress/block-editor';
import { useProductDataContext } from '@woocommerce/shared-context';

interface EditProductLinkProps {
	id?: number | undefined;
	productId?: number | undefined;
}

/**
 * Component to render an edit product link in the sidebar.
 *
 * @param {Object} props Component props.
 */
const EditProductLink = ( props: EditProductLinkProps ): JSX.Element | null => {
	const productDataContext = useProductDataContext();
	const product = productDataContext.product || {};
	const productId = product.id || props.productId || 0;

	if ( ! productId ) {
		return null;
	}

	return (
		<InspectorControls>
			<div className="wc-block-single-product__edit-card">
				<div className="wc-block-single-product__edit-card-title">
					<a
						href={ `${ ADMIN_URL }post.php?post=${ productId }&action=edit` }
						target="_blank"
						rel="noopener noreferrer"
					>
						{ __(
							"Edit this product's details",
							'woo-gutenberg-products-block'
						) }
						<Icon icon={ external } size={ 16 } />
					</a>
				</div>
				<div className="wc-block-single-product__edit-card-description">
					{ __(
						'Edit details such as title, price, description and more.',
						'woo-gutenberg-products-block'
					) }
				</div>
			</div>
		</InspectorControls>
	);
};

export default EditProductLink;
