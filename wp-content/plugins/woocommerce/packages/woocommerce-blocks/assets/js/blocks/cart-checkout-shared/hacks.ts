/**
 * HACKS
 *
 * This file contains functionality to "lock" blocks i.e. to prevent blocks being moved or deleted. This needs to be
 * kept in place until native support for locking is available in WordPress (estimated WordPress 5.9).
 */

/**
 * @todo Remove custom block locking (requires native WordPress support)
 */

/**
 * External dependencies
 */
import {
	useBlockProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { isTextField } from '@wordpress/dom';
import { subscribe, select as _select } from '@wordpress/data';
import { useEffect, useRef } from '@wordpress/element';
import { BACKSPACE, DELETE } from '@wordpress/keycodes';
import { hasFilter } from '@wordpress/hooks';
import { getBlockType } from '@wordpress/blocks';
import type { MutableRefObject } from 'react';

/**
 * Toggle class on body.
 *
 * @param {string}  className CSS Class name.
 * @param {boolean} add       True to add, false to remove.
 */
const toggleBodyClass = ( className: string, add = true ) => {
	if ( add ) {
		window.document.body.classList.add( className );
	} else {
		window.document.body.classList.remove( className );
	}
};

/**
 * addClassToBody
 *
 * This components watches the current selected block and adds a class name to the body if that block is locked. If the
 * current block is not locked, it removes the class name. The appended body class is used to hide UI elements to prevent
 * the block from being deleted.
 *
 * We use a component so we can react to changes in the store.
 */
export const addClassToBody = (): void => {
	if ( ! hasFilter( 'blocks.registerBlockType', 'core/lock/addAttribute' ) ) {
		subscribe( () => {
			const blockEditorSelect = _select( blockEditorStore );

			if ( ! blockEditorSelect ) {
				return;
			}

			const selectedBlock = blockEditorSelect.getSelectedBlock();

			if ( ! selectedBlock ) {
				return;
			}

			toggleBodyClass(
				'wc-lock-selected-block--remove',
				!! selectedBlock?.attributes?.lock?.remove
			);

			toggleBodyClass(
				'wc-lock-selected-block--move',
				!! selectedBlock?.attributes?.lock?.move
			);
		} );
	}
};

const isBlockLocked = ( clientId: string ): boolean => {
	if ( ! clientId ) {
		return false;
	}
	const { getBlock } = _select( blockEditorStore );
	const block = getBlock( clientId );
	// If lock.remove is defined at the block instance (not using the default value)
	// Then we use it.
	if ( typeof block?.attributes?.lock?.remove === 'boolean' ) {
		return block.attributes.lock.remove;
	}

	// If we don't have lock on the block instance, we check the type
	const blockType = getBlockType( block.name );
	if ( typeof blockType?.attributes?.lock?.default?.remove === 'boolean' ) {
		return blockType?.attributes?.lock?.default?.remove;
	}
	// If nothing is defined, return false
	return false;
};

/**
 * This is a hook we use in conjunction with useBlockProps. Its goal is to check if of the block's children is locked and being deleted.
 * It will stop the keydown event from propagating to stop it from being deleted via the keyboard.
 *
 */
const useLockedChildren = ( {
	ref,
}: {
	ref: MutableRefObject< HTMLElement | undefined >;
} ): void => {
	const lockInCore = hasFilter(
		'blocks.registerBlockType',
		'core/lock/addAttribute'
	);

	const node = ref.current;
	return useEffect( () => {
		if ( ! node || lockInCore ) {
			return;
		}
		function onKeyDown( event: KeyboardEvent ) {
			const { keyCode, target } = event;

			if ( ! ( target instanceof HTMLElement ) ) {
				return;
			}
			// We're not trying to delete something here.
			if ( keyCode !== BACKSPACE && keyCode !== DELETE ) {
				return;
			}

			// We're in a field, so we should let text be deleted.
			if ( isTextField( target ) ) {
				return;
			}

			// Typecast to fix issue with isTextField.
			const targetNode = target as HTMLElement;

			// Our target isn't a block.
			if ( targetNode.dataset.block === undefined ) {
				return;
			}

			const clientId = targetNode.dataset.block;
			const isLocked = isBlockLocked( clientId );
			// Prevent the keyboard event from propogating if it supports locking.
			if ( isLocked ) {
				event.preventDefault();
				event.stopPropagation();
				event.stopImmediatePropagation();
			}
		}
		node.addEventListener( 'keydown', onKeyDown, {
			capture: true,
			passive: false,
		} );

		return () => {
			node.removeEventListener( 'keydown', onKeyDown, {
				capture: true,
			} );
		};
	}, [ node, lockInCore ] );
};

/**
 * This hook is a light wrapper to useBlockProps, it wraps that hook plus useLockBlock to pass data between them.
 */
export const useBlockPropsWithLocking = (
	props: Record< string, unknown > = {}
): Record< string, unknown > => {
	const ref = useRef< HTMLElement >();
	const blockProps = useBlockProps( { ref, ...props } );
	useLockedChildren( {
		ref,
	} );
	return blockProps;
};
