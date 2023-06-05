/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { getAdminLink } from '@woocommerce/settings';
import { blocksConfig } from '@woocommerce/block-settings';
import BlockTitle from '@woocommerce/editor-components/block-title';
import { Icon, currencyDollar, external } from '@wordpress/icons';
import type { BlockEditProps } from '@wordpress/blocks';
import {
	Placeholder,
	Disabled,
	PanelBody,
	ToggleControl,
	Button,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControl as ToggleGroupControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import Block from './block';
import './editor.scss';
import type { Attributes } from './types';
import { UpgradeNotice } from '../filter-wrapper/upgrade';

export default function ( {
	attributes,
	setAttributes,
	clientId,
}: BlockEditProps< Attributes > ) {
	const {
		heading,
		headingLevel,
		showInputFields,
		inlineInput,
		showFilterButton,
	} = attributes;

	const blockProps = useBlockProps();

	const getInspectorControls = () => {
		return (
			<InspectorControls key="inspector">
				<PanelBody
					title={ __( 'Settings', 'woo-gutenberg-products-block' ) }
				>
					<ToggleGroupControl
						label={ __(
							'Price Range Selector',
							'woo-gutenberg-products-block'
						) }
						value={ showInputFields ? 'editable' : 'text' }
						onChange={ ( value: string ) =>
							setAttributes( {
								showInputFields: value === 'editable',
							} )
						}
						className="wc-block-price-filter__price-range-toggle"
					>
						<ToggleGroupControlOption
							value="editable"
							label={ __(
								'Editable',
								'woo-gutenberg-products-block'
							) }
						/>
						<ToggleGroupControlOption
							value="text"
							label={ __(
								'Text',
								'woo-gutenberg-products-block'
							) }
						/>
					</ToggleGroupControl>
					{ showInputFields && (
						<ToggleControl
							label={ __(
								'Inline input fields',
								'woo-gutenberg-products-block'
							) }
							checked={ inlineInput }
							onChange={ () =>
								setAttributes( {
									inlineInput: ! inlineInput,
								} )
							}
							help={ __(
								'Show input fields inline with the slider.',
								'woo-gutenberg-products-block'
							) }
						/>
					) }
					<ToggleControl
						label={ __(
							"Show 'Apply filters' button",
							'woo-gutenberg-products-block'
						) }
						help={ __(
							'Products will update when the button is clicked.',
							'woo-gutenberg-products-block'
						) }
						checked={ showFilterButton }
						onChange={ () =>
							setAttributes( {
								showFilterButton: ! showFilterButton,
							} )
						}
					/>
				</PanelBody>
			</InspectorControls>
		);
	};

	const noProductsPlaceholder = () => (
		<Placeholder
			className="wc-block-price-slider"
			icon={ <Icon icon={ currencyDollar } /> }
			label={ __( 'Filter by Price', 'woo-gutenberg-products-block' ) }
			instructions={ __(
				'Display a slider to filter products in your store by price.',
				'woo-gutenberg-products-block'
			) }
		>
			<p>
				{ __(
					'To filter your products by price you first need to assign prices to your products.',
					'woo-gutenberg-products-block'
				) }
			</p>
			<Button
				className="wc-block-price-slider__add-product-button"
				isSecondary
				href={ getAdminLink( 'post-new.php?post_type=product' ) }
			>
				{ __( 'Add new product', 'woo-gutenberg-products-block' ) +
					' ' }
				<Icon icon={ external } />
			</Button>
			<Button
				className="wc-block-price-slider__read_more_button"
				isTertiary
				href="https://docs.woocommerce.com/document/managing-products/"
			>
				{ __( 'Learn more', 'woo-gutenberg-products-block' ) }
			</Button>
		</Placeholder>
	);

	return (
		<div { ...blockProps }>
			{ blocksConfig.productCount === 0 ? (
				noProductsPlaceholder()
			) : (
				<>
					{ getInspectorControls() }
					<UpgradeNotice
						attributes={ attributes }
						clientId={ clientId }
						setAttributes={ setAttributes }
						filterType="price-filter"
					/>
					{ heading && (
						<BlockTitle
							className="wc-block-price-filter__title"
							headingLevel={ headingLevel }
							heading={ heading }
							onChange={ ( value: string ) =>
								setAttributes( { heading: value } )
							}
						/>
					) }
					<Disabled>
						<Block attributes={ attributes } isEditor={ true } />
					</Disabled>
				</>
			) }
		</div>
	);
}
