/**
 * External dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ProductCategoryControl from '@woocommerce/editor-components/product-category-control';
import GridLayoutControl from '@woocommerce/editor-components/grid-layout-control';
import { getSetting } from '@woocommerce/settings';
import GridContentControl from '@woocommerce/editor-components/grid-content-control';
import ProductOrderbyControl from '@woocommerce/editor-components/product-orderby-control';
import ProductStockControl from '@woocommerce/editor-components/product-stock-control';

/**
 * Internal dependencies
 */
import { Attributes, Props } from './types';

export interface InspectorControlsProps extends Props {
	isEditing: boolean;
	setChangedAttributes: ( changedAttributes: Partial< Attributes > ) => void;
}

export const ProductsByCategoryInspectorControls = (
	props: InspectorControlsProps
): JSX.Element => {
	const { isEditing, attributes, setAttributes, setChangedAttributes } =
		props;
	const {
		columns,
		catOperator,
		contentVisibility,
		orderby,
		rows,
		alignButtons,
		stockStatus,
	} = attributes;

	return (
		<InspectorControls key="inspector">
			<PanelBody
				title={ __(
					'Product Category',
					'woo-gutenberg-products-block'
				) }
				initialOpen={ ! attributes.categories.length && ! isEditing }
			>
				<ProductCategoryControl
					selected={ attributes.categories }
					onChange={ ( value = [] ) => {
						const ids = value.map( ( { id } ) => id );
						const changes = { categories: ids };

						// Changes in the sidebar save instantly and overwrite any unsaved changes.
						setAttributes( changes );
						setChangedAttributes( changes );
					} }
					operator={ catOperator }
					onOperatorChange={ ( value = 'any' ) => {
						const changes = { catOperator: value };
						setAttributes( changes );
						setChangedAttributes( changes );
					} }
					isCompact={ true }
				/>
			</PanelBody>
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
