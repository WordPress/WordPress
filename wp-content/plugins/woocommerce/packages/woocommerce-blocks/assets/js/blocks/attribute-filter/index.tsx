/**
 * External dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { isFeaturePluginBuild } from '@woocommerce/block-settings';
import { Icon, category } from '@wordpress/icons';
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import edit from './edit';
import type { BlockAttributes } from './types';
import { blockAttributes } from './attributes';
import metadata from './block.json';

registerBlockType( metadata, {
	icon: {
		src: (
			<Icon
				icon={ category }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	supports: {
		...metadata.supports,
		...( isFeaturePluginBuild() && {
			__experimentalBorder: {
				radius: false,
				color: true,
				width: false,
			},
		} ),
	},
	attributes: {
		...metadata.attributes,
		...blockAttributes,
	},
	edit,
	// Save the props to post content.
	save( { attributes }: { attributes: BlockAttributes } ) {
		const {
			className,
			showCounts,
			queryType,
			attributeId,
			heading,
			headingLevel,
			displayStyle,
			showFilterButton,
			selectType,
		} = attributes;
		const data: Record< string, unknown > = {
			'data-attribute-id': attributeId,
			'data-show-counts': showCounts,
			'data-query-type': queryType,
			'data-heading': heading,
			'data-heading-level': headingLevel,
		};
		if ( displayStyle !== 'list' ) {
			data[ 'data-display-style' ] = displayStyle;
		}
		if ( showFilterButton ) {
			data[ 'data-show-filter-button' ] = showFilterButton;
		}
		if ( selectType === 'single' ) {
			data[ 'data-select-type' ] = selectType;
		}
		return (
			<div
				{ ...useBlockProps.save( {
					className: classNames( 'is-loading', className ),
				} ) }
				{ ...data }
			>
				<span
					aria-hidden
					className="wc-block-product-attribute-filter__placeholder"
				/>
			</div>
		);
	},
} );
