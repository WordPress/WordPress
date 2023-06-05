/**
 * External dependencies
 */
import { select, dispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import type { View } from './types';

export const getView = (
	viewName: string,
	views: View[]
): View | undefined => {
	return views.find( ( view ) => view.view === viewName );
};

export const selectView = (
	clientId: string,
	viewName: string,
	selectParent = true
) => {
	const { updateBlockAttributes, selectBlock } =
		dispatch( 'core/block-editor' );
	updateBlockAttributes( clientId, {
		currentView: viewName,
	} );
	if ( selectParent ) {
		selectBlock(
			select( 'core/block-editor' )
				.getBlock( clientId )
				?.innerBlocks.find(
					( block: { name: string } ) => block.name === viewName
				)?.clientId || clientId
		);
	}
};

const defaultView = {
	views: [],
	currentView: '',
	viewClientId: '',
};

export const findParentBlockEditorViews = (
	clientId: string,
	maxDepth = 10,
	currentDepth = 0
): {
	views: View[];
	currentView: string;
	viewClientId: string;
} => {
	const depth = currentDepth + 1;

	if ( depth > maxDepth ) {
		return defaultView;
	}

	const { getBlockAttributes, getBlockRootClientId } =
		select( 'core/block-editor' );
	const rootId = getBlockRootClientId( clientId );

	if ( rootId === null || rootId === '' ) {
		return defaultView;
	}

	const rootAttributes = getBlockAttributes( rootId );

	if ( ! rootAttributes ) {
		return defaultView;
	}

	if ( rootAttributes.editorViews !== undefined ) {
		return {
			views: rootAttributes.editorViews,
			currentView:
				rootAttributes.currentView ||
				rootAttributes.editorViews[ 0 ].view,
			viewClientId: rootId,
		};
	}

	return findParentBlockEditorViews( rootId, maxDepth, depth );
};
