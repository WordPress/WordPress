/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import PropTypes from 'prop-types';
import { Icon, listView } from '@wordpress/icons';
import {
	Disabled,
	PanelBody,
	ToggleControl,
	Placeholder,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControl as ToggleGroupControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';

const EmptyPlaceholder = () => (
	<Placeholder
		icon={ <Icon icon={ listView } /> }
		label={ __(
			'Product Categories List',
			'woocommerce'
		) }
		className="wc-block-product-categories"
	>
		{ __(
			'This block displays the product categories for your store. To use it you first need to create a product and assign it to a category.',
			'woocommerce'
		) }
	</Placeholder>
);

/**
 * Component displaying the categories as dropdown or list.
 *
 * @param {Object}            props               Incoming props for the component.
 * @param {Object}            props.attributes    Incoming block attributes.
 * @param {function(any):any} props.setAttributes Setter for block attributes.
 * @param {string}            props.name          Name for block.
 */
const ProductCategoriesBlock = ( { attributes, setAttributes, name } ) => {
	const getInspectorControls = () => {
		const { hasCount, hasImage, hasEmpty, isDropdown, isHierarchical } =
			attributes;

		return (
			<InspectorControls key="inspector">
				<PanelBody
					title={ __(
						'List Settings',
						'woocommerce'
					) }
					initialOpen
				>
					<ToggleGroupControl
						label={ __(
							'Display style',
							'woocommerce'
						) }
						value={ isDropdown ? 'dropdown' : 'list' }
						onChange={ ( value ) =>
							setAttributes( {
								isDropdown: value === 'dropdown',
							} )
						}
					>
						<ToggleGroupControlOption
							value="list"
							label={ __(
								'List',
								'woocommerce'
							) }
						/>
						<ToggleGroupControlOption
							value="dropdown"
							label={ __(
								'Dropdown',
								'woocommerce'
							) }
						/>
					</ToggleGroupControl>
				</PanelBody>
				<PanelBody
					title={ __( 'Content', 'woocommerce' ) }
					initialOpen
				>
					<ToggleControl
						label={ __(
							'Show product count',
							'woocommerce'
						) }
						checked={ hasCount }
						onChange={ () =>
							setAttributes( { hasCount: ! hasCount } )
						}
					/>
					{ ! isDropdown && (
						<ToggleControl
							label={ __(
								'Show category images',
								'woocommerce'
							) }
							help={
								hasImage
									? __(
											'Category images are visible.',
											'woocommerce'
									  )
									: __(
											'Category images are hidden.',
											'woocommerce'
									  )
							}
							checked={ hasImage }
							onChange={ () =>
								setAttributes( { hasImage: ! hasImage } )
							}
						/>
					) }
					<ToggleControl
						label={ __(
							'Show hierarchy',
							'woocommerce'
						) }
						checked={ isHierarchical }
						onChange={ () =>
							setAttributes( {
								isHierarchical: ! isHierarchical,
							} )
						}
					/>
					<ToggleControl
						label={ __(
							'Show empty categories',
							'woocommerce'
						) }
						checked={ hasEmpty }
						onChange={ () =>
							setAttributes( { hasEmpty: ! hasEmpty } )
						}
					/>
				</PanelBody>
			</InspectorControls>
		);
	};

	const blockProps = useBlockProps( {
		className: 'wc-block-product-categories',
	} );

	return (
		<div { ...blockProps }>
			{ getInspectorControls() }
			<Disabled>
				<ServerSideRender
					block={ name }
					attributes={ attributes }
					EmptyResponsePlaceholder={ EmptyPlaceholder }
				/>
			</Disabled>
		</div>
	);
};

ProductCategoriesBlock.propTypes = {
	/**
	 * The attributes for this block
	 */
	attributes: PropTypes.object.isRequired,
	/**
	 * The register block name.
	 */
	name: PropTypes.string.isRequired,
	/**
	 * A callback to update attributes
	 */
	setAttributes: PropTypes.func.isRequired,
};

export default ProductCategoriesBlock;
