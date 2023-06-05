/**
 * External dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import GridLayoutControl from '@woocommerce/editor-components/grid-layout-control';
import { getSetting } from '@woocommerce/settings';
import GridContentControl from '@woocommerce/editor-components/grid-content-control';
import ProductCategoryControl from '@woocommerce/editor-components/product-category-control';

/**
 * Internal dependencies
 */
import { Props } from './types';

export const ProductBestSellersInspectorControls = (
	props: Props
): JSX.Element => {
	const { attributes, setAttributes } = props;
	const {
		categories,
		catOperator,
		columns,
		contentVisibility,
		rows,
		alignButtons,
	} = attributes;

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
					'Filter by Product Category',
					'woo-gutenberg-products-block'
				) }
				initialOpen={ false }
			>
				<ProductCategoryControl
					selected={ categories }
					onChange={ ( value = [] ) => {
						const ids = value.map( ( { id } ) => id );
						setAttributes( { categories: ids } );
					} }
					operator={ catOperator }
					onOperatorChange={ ( value = 'any' ) =>
						setAttributes( { catOperator: value } )
					}
				/>
			</PanelBody>
		</InspectorControls>
	);
};
