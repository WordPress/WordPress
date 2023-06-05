/**
 * External dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { getSetting } from '@woocommerce/settings';
import GridContentControl from '@woocommerce/editor-components/grid-content-control';
import ProductOrderbyControl from '@woocommerce/editor-components/product-orderby-control';
import ProductsControl from '@woocommerce/editor-components/products-control';

/**
 * Internal dependencies
 */
import { Props } from './types';

export const HandpickedProductsInspectorControls = (
	props: Props
): JSX.Element => {
	const { attributes, setAttributes } = props;
	const { columns, contentVisibility, orderby, alignButtons } = attributes;

	return (
		<InspectorControls key="inspector">
			<PanelBody
				title={ __( 'Layout', 'woo-gutenberg-products-block' ) }
				initialOpen
			>
				<RangeControl
					label={ __( 'Columns', 'woo-gutenberg-products-block' ) }
					value={ columns }
					onChange={ ( value ) =>
						setAttributes( { columns: value } )
					}
					min={ getSetting( 'min_columns', 1 ) }
					max={ getSetting( 'max_columns', 6 ) }
				/>
				<ToggleControl
					label={ __(
						'Align Buttons',
						'woo-gutenberg-products-block'
					) }
					help={
						alignButtons
							? __(
									'Buttons are aligned vertically.',
									'woo-gutenberg-products-block'
							  )
							: __(
									'Buttons follow content.',
									'woo-gutenberg-products-block'
							  )
					}
					checked={ alignButtons }
					onChange={ () =>
						setAttributes( { alignButtons: ! alignButtons } )
					}
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
				title={ __( 'Products', 'woo-gutenberg-products-block' ) }
				initialOpen={ false }
			>
				<ProductsControl
					selected={ attributes.products }
					onChange={ ( value = [] ) => {
						const ids = value.map( ( { id } ) => id );
						setAttributes( { products: ids } );
					} }
					isCompact={ true }
				/>
			</PanelBody>
		</InspectorControls>
	);
};
