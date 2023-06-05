/**
 * External dependencies
 */
import type { Block } from '@wordpress/blocks';

export interface LockableBlock extends Block {
	attributes: {
		lock?: {
			type: 'object';
			remove?: boolean;
			move: boolean;
			default?: {
				remove?: boolean;
				move?: boolean;
			};
		};
	};
}
