/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import EditProductLink from '@woocommerce/editor-components/edit-product-link';
import { useProductDataContext } from '@woocommerce/shared-context';
import classnames from 'classnames';
import {
	Disabled,
	PanelBody,
	ToggleControl,
	Notice,
} from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { productSupportsAddToCartForm } from '@woocommerce/base-utils';

/**
 * Internal dependencies
 */
import './style.scss';
import Block from './block';
import withProductSelector from '../shared/with-product-selector';
import { BLOCK_TITLE, BLOCK_ICON } from './constants';

interface EditProps {
	attributes: {
		className: string;
		showFormElements: boolean;
	};
	setAttributes: ( attributes: { showFormElements: boolean } ) => void;
}

const Edit = ( { attributes, setAttributes }: EditProps ) => {
	const { product } = useProductDataContext();
	const { className, showFormElements } = attributes;

	return (
		<div
			className={ classnames(
				className,
				'wc-block-components-product-add-to-cart'
			) }
		>
			<EditProductLink productId={ product.id } />
			<InspectorControls>
				<PanelBody
					title={ __( 'Layout', 'woo-gutenberg-products-block' ) }
				>
					{ productSupportsAddToCartForm( product ) ? (
						<ToggleControl
							label={ __(
								'Display form elements',
								'woo-gutenberg-products-block'
							) }
							help={ __(
								'Depending on product type, allow customers to select a quantity, variations etc.',
								'woo-gutenberg-products-block'
							) }
							checked={ showFormElements }
							onChange={ () =>
								setAttributes( {
									showFormElements: ! showFormElements,
								} )
							}
						/>
					) : (
						<Notice
							className="wc-block-components-product-add-to-cart-notice"
							isDismissible={ false }
							status="info"
						>
							{ __(
								'This product does not support the block based add to cart form. A link to the product page will be shown instead.',
								'woo-gutenberg-products-block'
							) }
						</Notice>
					) }
				</PanelBody>
			</InspectorControls>
			<Disabled>
				<Block { ...attributes } />
			</Disabled>
		</div>
	);
};

export default withProductSelector( {
	icon: BLOCK_ICON,
	label: BLOCK_TITLE,
	description: __(
		'Choose a product to display its add to cart form.',
		'woo-gutenberg-products-block'
	),
} )( Edit );
