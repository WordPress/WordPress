/**
 * External dependencies
 */
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import GridLayoutControl from '@woocommerce/editor-components/grid-layout-control';
import { getSetting } from '@woocommerce/settings';
import GridContentControl from '@woocommerce/editor-components/grid-content-control';
import ProductAttributeTermControl from '@woocommerce/editor-components/product-attribute-term-control';
import ProductOrderbyControl from '@woocommerce/editor-components/product-orderby-control';
import ProductStockControl from '@woocommerce/editor-components/product-stock-control';
import { InspectorControls } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { Props } from './types';

export const ProductsByAttributeInspectorControls = (
	props: Props
): JSX.Element => {
	const { setAttributes } = props;
	const {
		attributes,
		attrOperator,
		columns,
		contentVisibility,
		orderby,
		rows,
		alignButtons,
		stockStatus,
	} = props.attributes;

	return (
		<InspectorControls key="inspector">
			<PanelBody
				title={ __( 'Layout', 'woo-gutenberg-products-block' ) }
				initialOpen
			>
				<GridLayoutControl
					columns={ columns }
					rows={ rows }
					alignButtons={ alignButtons }
					setAttributes={ setAttributes }
					minColumns={ getSetting( 'min_columns', 1 ) }
					maxColumns={ getSetting( 'max_columns', 6 ) }
					minRows={ getSetting( 'min_rows', 1 ) }
					maxRows={ getSetting( 'max_rows', 6 ) }
				/>
			</PanelBody>
			<PanelBody
				title={ __( 'Content', 'woo-gutenberg-products-block' ) }
				initialOpen
			>
				<GridContentControl
					settings={ contentVisibility }
					onChange={ ( value ) =>
						setAttributes( { contentVisibility: value } )
					}
				/>
			</PanelBody>
			<PanelBody
				title={ __(
					'Filter by Product Attribute',
					'woo-gutenberg-products-block'
				) }
				initialOpen={ false }
			>
				<ProductAttributeTermControl
					selected={ attributes }
					onChange={ ( value = [] ) => {
						const result = value.map(
							( { id, attr_slug: attributeSlug } ) => ( {
								id,
								attr_slug: attributeSlug,
							} )
						);
						setAttributes( { attributes: result } );
					} }
					operator={ attrOperator }
					onOperatorChange={ ( value = 'any' ) =>
						setAttributes( { attrOperator: value } )
					}
					isCompact={ true }
				/>
			</PanelBody>
			<PanelBody
				title={ __( 'Order By', 'woo-gutenberg-products-block' ) }
				initialOpen={ false }
			>
				<ProductOrderbyControl
					setAttributes={ setAttributes }
					value={ orderby }
				/>
			</PanelBody>
			<PanelBody
				title={ __(
					'Filter by stock status',
					'woo-gutenberg-products-block'
				) }
				initialOpen={ false }
			>
				<ProductStockControl
					setAttributes={ setAttributes }
					value={ stockStatus }
				/>
			</PanelBody>
		</InspectorControls>
	);
};
