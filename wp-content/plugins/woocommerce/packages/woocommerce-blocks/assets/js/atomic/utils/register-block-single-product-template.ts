/**
 * External dependencies
 */
import {
	BlockAttributes,
	BlockConfiguration,
	BlockVariation,
	getBlockType,
	registerBlockType,
	registerBlockVariation,
	unregisterBlockType,
	unregisterBlockVariation,
} from '@wordpress/blocks';
import { subscribe, select } from '@wordpress/data';

// Creating a local cache to prevent multiple registration tries.
const blocksRegistered = new Set();

function parseTemplateId( templateId: string | undefined ) {
	return templateId?.split( '//' )[ 1 ];
}

export const registerBlockSingleProductTemplate = ( {
	blockName,
	blockMetadata,
	blockSettings,
	isVariationBlock = false,
	variationName,
}: {
	blockName: string;
	blockMetadata: Partial< BlockConfiguration >;
	blockSettings: Partial< BlockConfiguration >;
	isVariationBlock?: boolean;
	variationName?: string;
} ) => {
	let currentTemplateId: string | undefined = '';

	subscribe( () => {
		const previousTemplateId = currentTemplateId;
		const store = select( 'core/edit-site' );
		currentTemplateId = parseTemplateId( store?.getEditedPostId() );
		const hasChangedTemplate = previousTemplateId !== currentTemplateId;
		const hasTemplateId = Boolean( currentTemplateId );

		if ( ! hasChangedTemplate || ! hasTemplateId || ! blockName ) {
			return;
		}

		let isBlockRegistered = Boolean( getBlockType( blockName ) );

		/**
		 * We need to unregister the block each time the user visits or leaves the Single Product template.
		 *
		 * The Single Product template is the only template where the `ancestor` property is not needed because it provides the context
		 * for the product blocks. We need to unregister and re-register the block to remove or add the `ancestor` property depending on which
		 * location (template, post, page, etc.) the user is in.
		 *
		 */
		if (
			isBlockRegistered &&
			( currentTemplateId?.includes( 'single-product' ) ||
				previousTemplateId?.includes( 'single-product' ) )
		) {
			if ( isVariationBlock && variationName ) {
				unregisterBlockVariation( blockName, variationName );
			} else {
				unregisterBlockType( blockName );
			}
			isBlockRegistered = false;
		}

		if ( ! isBlockRegistered ) {
			if ( isVariationBlock ) {
				registerBlockVariation( blockName, {
					...blockSettings,
					// @ts-expect-error: `ancestor` key is typed in WordPress core
					ancestor: ! currentTemplateId?.includes( 'single-product' )
						? blockSettings?.ancestor
						: undefined,
				} );
			} else {
				// @ts-expect-error: `registerBlockType` is typed in WordPress core
				registerBlockType( blockMetadata, {
					...blockSettings,
					ancestor: ! currentTemplateId?.includes( 'single-product' )
						? blockSettings?.ancestor
						: undefined,
				} );
			}
		}
	}, 'core/edit-site' );

	subscribe( () => {
		const isBlockRegistered = Boolean( variationName )
			? blocksRegistered.has( variationName )
			: blocksRegistered.has( blockName );
		// This subscribe callback could be invoked with the core/blocks store
		// which would cause infinite registration loops because of the `registerBlockType` call.
		// This local cache helps prevent that.
		if ( ! isBlockRegistered ) {
			if ( isVariationBlock ) {
				blocksRegistered.add( variationName );
				registerBlockVariation(
					blockName,
					blockSettings as BlockVariation< BlockAttributes >
				);
			} else {
				blocksRegistered.add( blockName );
				// @ts-expect-error: `registerBlockType` is typed in WordPress core
				registerBlockType( blockMetadata, blockSettings );
			}
		}
	}, 'core/edit-post' );
};
