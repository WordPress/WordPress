/**
 * External dependencies
 */
import {
	createBlock,
	createBlocksFromInnerBlocksTemplate,
	type BlockInstance,
	type InnerBlockTemplate,
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

const createNoResultsParagraph = () =>
	createBlock( 'core/paragraph', {
		content: __(
			'No products were found matching your selection.',
			'woo-gutenberg-products-block'
		),
	} );

const createProductSearch = () =>
	createBlock( 'core/search', {
		buttonPosition: 'button-outside',
		buttonText: __( 'Search', 'woo-gutenberg-products-block' ),
		buttonUseIcon: false,
		showLabel: false,
		placeholder: __( 'Search products…', 'woo-gutenberg-products-block' ),
		query: { post_type: 'product' },
	} );

const extendInnerBlocksWithNoResultsContent = (
	innerBlocks: InnerBlockTemplate[],
	inheritedAttributes: InheritedAttributes
) => {
	const noResultsContent = [
		createNoResultsParagraph(),
		createProductSearch(),
	];

	const noResultsBlockName = 'core/query-no-results';
	const noResultsBlockIndex = innerBlocks.findIndex(
		( block ) => block[ 0 ] === noResultsBlockName
	);
	const noResultsBlock = innerBlocks[ noResultsBlockIndex ];
	const attributes = {
		...( noResultsBlock[ 1 ] || {} ),
		...inheritedAttributes,
	};

	const extendedNoResults = [
		noResultsBlockName,
		attributes,
		noResultsContent,
	];

	return [
		...productsInnerBlocksTemplate.slice( 0, noResultsBlockIndex ),
		extendedNoResults,
		...productsInnerBlocksTemplate.slice( noResultsBlockIndex + 1 ),
	];
};

const createProductsBlock = ( inheritedAttributes: InheritedAttributes ) => {
	const productsInnerBlocksWithNoResults =
		extendInnerBlocksWithNoResultsContent(
			productsInnerBlocksTemplate,
			inheritedAttributes
		);

	return createBlock(
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
		createBlocksFromInnerBlocksTemplate( productsInnerBlocksWithNoResults )
	);
};

const getBlockifiedTemplate = ( inheritedAttributes: InheritedAttributes ) =>
	[
		createArchiveTitleBlock( 'search-title', inheritedAttributes ),
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

export {
	getBlockifiedTemplate,
	isConversionPossible,
	getDescription,
	getButtonLabel,
};
