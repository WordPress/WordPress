// Disabling because of `__experimental` property names.
/* eslint-disable @typescript-eslint/naming-convention */

/**
 * External dependencies
 */
import { InnerBlocks } from '@wordpress/block-editor';
import { registerBlockType } from '@wordpress/blocks';
import { getSetting } from '@woocommerce/settings';
import { isFeaturePluginBuild } from '@woocommerce/block-settings';
import type { FunctionComponent } from 'react';
import type { BlockConfiguration } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { Edit } from './edit';

type CSSDirections = 'top' | 'right' | 'bottom' | 'left';

interface ExtendedBlockSupports {
	supports: {
		color?: {
			background: string;
			gradients: boolean;
			link: boolean;
			text: string;
		};
		spacing?: {
			margin: boolean | CSSDirections[];
			padding: boolean | CSSDirections[];
			__experimentalDefaultControls?: {
				margin?: boolean;
				padding?: boolean;
			};
			__experimentalSkipSerialization?: boolean;
		};
		__experimentalBorder?: {
			color: boolean;
			radius: boolean;
			width: boolean;
			__experimentalSkipSerialization?: boolean;
		};
	};
}

export function register(
	Block: FunctionComponent,
	example: { attributes: Record< string, unknown > },
	metadata: BlockConfiguration & ExtendedBlockSupports,
	settings: Partial< BlockConfiguration >
): void {
	const DEFAULT_SETTINGS = {
		attributes: {
			...metadata.attributes,
			/**
			 * A minimum height for the block.
			 *
			 * Note: if padding is increased, this way the inner content will never
			 * overflow, but instead will resize the container.
			 *
			 * It was decided to change this to make this block more in line with
			 * the “Cover” block.
			 */
			minHeight: {
				type: 'number',
				default: getSetting( 'default_height', 500 ),
			},
		},
		supports: {
			...metadata.supports,
			color: {
				background: metadata.supports?.color?.background,
				text: metadata.supports?.color?.text,
			},
			spacing: {
				padding: metadata.supports?.spacing?.padding,
				...( isFeaturePluginBuild() && {
					__experimentalDefaultControls: {
						padding:
							metadata.supports?.spacing
								?.__experimentalDefaultControls,
					},
					__experimentalSkipSerialization:
						metadata.supports?.spacing
							?.__experimentalSkipSerialization,
				} ),
			},
			...( isFeaturePluginBuild() && {
				__experimentalBorder: metadata?.supports?.__experimentalBorder,
			} ),
		},
	};

	const DEFAULT_EXAMPLE = {
		attributes: {
			alt: '',
			contentAlign: 'center',
			dimRatio: 50,
			editMode: false,
			hasParallax: false,
			isRepeated: false,
			height: getSetting( 'default_height', 500 ),
			mediaSrc: '',
			overlayColor: '#000000',
			showDesc: true,
		},
	};

	registerBlockType( metadata, {
		...DEFAULT_SETTINGS,
		example: {
			...DEFAULT_EXAMPLE,
			...example,
		},
		/**
		 * Renders and manages the block.
		 *
		 * @param {Object} props Props to pass to block.
		 */
		edit: Edit( Block ),
		/**
		 * Block content is rendered in PHP, not via save function.
		 */
		save: () => <InnerBlocks.Content />,
		...settings,
	} );
}
