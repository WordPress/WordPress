/**
 * External dependencies
 */
import { useRef, useEffect } from '@wordpress/element';
import { useRegistry, dispatch } from '@wordpress/data';
import {
	createBlock,
	getBlockType,
	createBlocksFromInnerBlocksTemplate,
	TemplateArray,
} from '@wordpress/blocks';
import { useEditorContext } from '@woocommerce/base-context';

/**
 * Internal dependencies
 */
import { getMissingBlocks, findBlockPosition } from './utils';

/**
 * Hook to ensure FORCED blocks are rendered in the correct place.
 */
export const useForcedLayout = ( {
	clientId,
	registeredBlocks,
	defaultTemplate = [],
}: {
	// Client ID of the parent block.
	clientId: string;
	// An array of registered blocks that may be forced in this particular layout.
	registeredBlocks: Array< string >;
	// The default template for the inner blocks in this layout.
	defaultTemplate: TemplateArray;
} ) => {
	const currentRegisteredBlocks = useRef( registeredBlocks );
	const currentDefaultTemplate = useRef( defaultTemplate );
	const registry = useRegistry();
	const { isPreview } = useEditorContext();

	useEffect( () => {
		let templateSynced = false;

		if ( isPreview ) {
			return;
		}

		const { replaceInnerBlocks } = dispatch( 'core/block-editor' );

		return registry.subscribe( () => {
			const innerBlocks = registry
				.select( 'core/block-editor' )
				.getBlocks( clientId );

			// If there are NO inner blocks, sync with the given template.
			if (
				innerBlocks.length === 0 &&
				currentDefaultTemplate.current.length > 0 &&
				! templateSynced
			) {
				const nextBlocks = createBlocksFromInnerBlocksTemplate(
					currentDefaultTemplate.current
				);
				if ( nextBlocks.length !== 0 ) {
					templateSynced = true;
					replaceInnerBlocks( clientId, nextBlocks );
					return;
				}
			}

			const registeredBlockTypes = currentRegisteredBlocks.current.map(
				( blockName: string ) => getBlockType( blockName )
			);

			const missingBlocks = getMissingBlocks(
				innerBlocks,
				registeredBlockTypes
			);

			if ( missingBlocks.length === 0 ) {
				return;
			}

			// Initially set as -1, so we can skip checking the position multiple times. Later on in the map callback,
			// we check where the forced blocks should be inserted. This gets set to >= 0 if we find a missing block,
			// so we know we can skip calculating it.
			let insertAtPosition = -1;
			const blockConfig = missingBlocks.map( ( block ) => {
				const defaultTemplatePosition =
					currentDefaultTemplate.current.findIndex(
						( [ blockName ] ) => blockName === block.name
					);
				const createdBlock = createBlock( block.name );

				// As mentioned above, if this is not -1, this is the first time we're calculating the position, if it's
				// already been calculated we can skip doing so.
				if ( insertAtPosition === -1 ) {
					insertAtPosition = findBlockPosition( {
						defaultTemplatePosition,
						innerBlocks,
						currentDefaultTemplate,
					} );
				}

				return createdBlock;
			} );

			registry.batch( () => {
				registry
					.dispatch( 'core/block-editor' )
					.insertBlocks( blockConfig, insertAtPosition, clientId );
			} );
		}, 'core/block-editor' );
	}, [ clientId, isPreview, registry ] );
};
