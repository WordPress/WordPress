/**
 * External dependencies
 */
import {
	createBlock,
	createBlocksFromInnerBlocksTemplate,
	type BlockInstance,
} from '@wordpress/blocks';
import { isWpVersion } from '@woocommerce/settings';
import { isExperimentalBuild } from '@woocommerce/block-settings';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import {
	INNER_BLOCKS_TEMPLATE as productsInnerBlocksTemplate,
	QUERY_DEFAULT_ATTRIBUTES as productsQueryDefaultAttributes,
} from '../product-query/constants';
import { VARIATION_NAME as productsVariationName } from '../product-query/variations/product-query';
import { createArchiveTitleBlock, createRowBlock } from './utils';
import { type InheritedAttributes } from './types';

const createProductsBlock = ( inheritedAttributes: InheritedAttributes ) =>
	createBlock(
		'core/query',
		{
			...productsQueryDefaultAttributes,
			...inheritedAttributes,
			namespace: productsVariationName,
			query: {
				...productsQueryDefaultAttributes.query,
				inherit: true,
			},
		},
		createBlocksFromInnerBlocksTemplate( productsInnerBlocksTemplate )
	);

const getBlockifiedTemplate = (
	inheritedAttributes: InheritedAttributes,
	withTermDescription = false
) =>
	[
		createBlock( 'woocommerce/breadcrumbs', inheritedAttributes ),
		createArchiveTitleBlock( 'archive-title', inheritedAttributes ),
		withTermDescription
			? createBlock( 'core/term-description', inheritedAttributes )
			: null,
		createBlock( 'woocommerce/store-notices', inheritedAttributes ),
		createRowBlock(
			[
				createBlock( 'woocommerce/product-results-count' ),
				createBlock( 'woocommerce/catalog-sorting' ),
			],
			inheritedAttributes
		),
		createProductsBlock( inheritedAttributes ),
	].filter( Boolean ) as BlockInstance[];

const getBlockifiedTemplateWithTermDescription = (
	inheritedAttributes: InheritedAttributes
) => getBlockifiedTemplate( inheritedAttributes, true );

const isConversionPossible = () => {
	// Blockification is possible for the WP version 6.1 and above,
	// which are the versions the Products block supports.
	return isExperimentalBuild() && isWpVersion( '6.1', '>=' );
};

const getDescriptionAllowingConversion = ( templateTitle: string ) =>
	sprintf(
		/* translators: %s is the template title */
		__(
			"This block serves as a placeholder for your %s. We recommend upgrading to the Products block for more features to edit your products visually. Don't worry, you can always revert back.",
			'woo-gutenberg-products-block'
		),
		templateTitle
	);

const getDescriptionDisallowingConversion = ( templateTitle: string ) =>
	sprintf(
		/* translators: %s is the template title */
		__(
			'This block serves as a placeholder for your %s. It will display the actual product image, title, price in your store. You can move this placeholder around and add more blocks around to customize the template.',
			'woo-gutenberg-products-block'
		),
		templateTitle
	);

const getDescription = ( templateTitle: string, canConvert: boolean ) => {
	if ( canConvert ) {
		return getDescriptionAllowingConversion( templateTitle );
	}

	return getDescriptionDisallowingConversion( templateTitle );
};

const getButtonLabel = () =>
	__( 'Upgrade to Products block', 'woo-gutenberg-products-block' );

export const blockifiedProductCatalogConfig = {
	getBlockifiedTemplate,
	isConversionPossible,
	getDescription,
	getButtonLabel,
};

export const blockifiedProductTaxonomyConfig = {
	getBlockifiedTemplate: getBlockifiedTemplateWithTermDescription,
	isConversionPossible,
	getDescription,
	getButtonLabel,
};
