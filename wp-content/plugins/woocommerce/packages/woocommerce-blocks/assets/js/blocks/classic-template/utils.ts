/**
 * External dependencies
 */
import {
	type Block,
	type BlockInstance,
	getBlockType,
	createBlock,
} from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { TEMPLATES } from './constants';
import { TemplateDetails, InheritedAttributes } from './types';

// Finds the most appropriate template details object for specific template keys such as single-product-hoodie.
export function getTemplateDetailsBySlug(
	parsedTemplate: string,
	templates: TemplateDetails
) {
	const templateKeys = Object.keys( templates );
	let templateDetails = null;

	for ( let i = 0; templateKeys.length > i; i++ ) {
		const keyToMatch = parsedTemplate.substr( 0, templateKeys[ i ].length );
		const maybeTemplate = templates[ keyToMatch ];
		if ( maybeTemplate ) {
			templateDetails = maybeTemplate;
			break;
		}
	}

	return templateDetails;
}

export function isClassicTemplateBlockRegisteredWithAnotherTitle(
	// eslint-disable-next-line @typescript-eslint/no-explicit-any
	block: Block< any > | undefined,
	parsedTemplate: string
) {
	const templateDetails = getTemplateDetailsBySlug(
		parsedTemplate,
		TEMPLATES
	);
	return block?.title !== templateDetails?.title;
}

export function hasTemplateSupportForClassicTemplateBlock(
	parsedTemplate: string,
	templates: TemplateDetails
): boolean {
	return getTemplateDetailsBySlug( parsedTemplate, templates ) ? true : false;
}

export const createArchiveTitleBlock = (
	variationName: string,
	inheritedAttributes: InheritedAttributes
) => {
	const queryTitleBlockName = 'core/query-title';
	const queryTitleBlockVariations =
		getBlockType( queryTitleBlockName )?.variations || [];
	const archiveTitleVariation = queryTitleBlockVariations.find(
		( { name }: { name: string } ) => name === variationName
	);

	if ( ! archiveTitleVariation ) {
		return null;
	}

	const { attributes } = archiveTitleVariation;
	const extendedAttributes = {
		...attributes,
		...inheritedAttributes,
		showPrefix: false,
	};

	return createBlock( queryTitleBlockName, extendedAttributes );
};

export const createRowBlock = (
	innerBlocks: Array< BlockInstance >,
	inheritedAttributes: InheritedAttributes
) => {
	const groupBlockName = 'core/group';
	const rowVariationName = `group-row`;
	const groupBlockVariations =
		getBlockType( groupBlockName )?.variations || [];
	const rowVariation = groupBlockVariations.find(
		( { name }: { name: string } ) => name === rowVariationName
	);

	if ( ! rowVariation ) {
		return null;
	}

	const { attributes } = rowVariation;
	const extendedAttributes = {
		...attributes,
		...inheritedAttributes,
		layout: {
			...attributes.layout,
			justifyContent: 'space-between',
		},
	};

	return createBlock( groupBlockName, extendedAttributes, innerBlocks );
};
