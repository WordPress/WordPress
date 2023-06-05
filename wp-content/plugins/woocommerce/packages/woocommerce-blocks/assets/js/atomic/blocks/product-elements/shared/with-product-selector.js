/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import ProductControl from '@woocommerce/editor-components/product-control';
import { Placeholder, Button, ToolbarGroup } from '@wordpress/components';
import { BlockControls } from '@wordpress/block-editor';
import TextToolbarButton from '@woocommerce/editor-components/text-toolbar-button';
import { useProductDataContext } from '@woocommerce/shared-context';

/**
 * Internal dependencies
 */
import './editor.scss';

/**
 * This HOC shows a product selection interface if context is not present in the editor.
 *
 * @param {Object} selectorArgs Options for the selector.
 *
 */
const withProductSelector = ( selectorArgs ) => ( OriginalComponent ) => {
	return ( props ) => {
		const productDataContext = useProductDataContext();
		const { attributes, setAttributes } = props;
		const { productId } = attributes;
		const [ isEditing, setIsEditing ] = useState( ! productId );

		if (
			productDataContext.hasContext ||
			Number.isFinite( props.context?.queryId )
		) {
			return <OriginalComponent { ...props } />;
		}

		return (
			<>
				{ isEditing ? (
					<Placeholder
						icon={ selectorArgs.icon || '' }
						label={ selectorArgs.label || '' }
						className="wc-atomic-blocks-product"
					>
						{ !! selectorArgs.description && (
							<div>{ selectorArgs.description }</div>
						) }
						<div className="wc-atomic-blocks-product__selection">
							<ProductControl
								selected={ productId || 0 }
								showVariations
								onChange={ ( value = [] ) => {
									setAttributes( {
										productId: value[ 0 ]
											? value[ 0 ].id
											: 0,
									} );
								} }
							/>
							<Button
								isSecondary
								disabled={ ! productId }
								onClick={ () => {
									setIsEditing( false );
								} }
							>
								{ __( 'Done', 'woocommerce' ) }
							</Button>
						</div>
					</Placeholder>
				) : (
					<>
						<BlockControls>
							<ToolbarGroup>
								<TextToolbarButton
									onClick={ () => setIsEditing( true ) }
								>
									{ __(
										'Switch product…',
										'woocommerce'
									) }
								</TextToolbarButton>
							</ToolbarGroup>
						</BlockControls>
						<OriginalComponent { ...props } />
					</>
				) }
			</>
		);
	};
};

export default withProductSelector;
