/**
 * External dependencies
 */
import classnames from 'classnames';
import {
	Disabled,
	Button,
	ButtonGroup,
	PanelBody,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	AlignmentToolbar,
	BlockControls,
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import type { BlockEditProps } from '@wordpress/blocks';
import { useEffect } from '@wordpress/element';
import { ProductQueryContext as Context } from '@woocommerce/blocks/product-query/types';

/**
 * Internal dependencies
 */
import Block from './block';
import { BlockAttributes } from './types';

function WidthPanel( {
	selectedWidth,
	setAttributes,
}: {
	selectedWidth: number | undefined;
	setAttributes: ( attributes: BlockAttributes ) => void;
} ) {
	function handleChange( newWidth: number ) {
		// Check if we are toggling the width off
		const width = selectedWidth === newWidth ? undefined : newWidth;

		// Update attributes.
		setAttributes( { width } );
	}

	return (
		<PanelBody
			title={ __( 'Width settings', 'woo-gutenberg-products-block' ) }
		>
			<ButtonGroup
				aria-label={ __(
					'Button width',
					'woo-gutenberg-products-block'
				) }
			>
				{ [ 25, 50, 75, 100 ].map( ( widthValue ) => {
					return (
						<Button
							key={ widthValue }
							isSmall
							variant={
								widthValue === selectedWidth
									? 'primary'
									: undefined
							}
							onClick={ () => handleChange( widthValue ) }
						>
							{ widthValue }%
						</Button>
					);
				} ) }
			</ButtonGroup>
		</PanelBody>
	);
}

const Edit = ( {
	attributes,
	setAttributes,
	context,
}: BlockEditProps< BlockAttributes > & {
	context?: Context | undefined;
} ): JSX.Element => {
	const blockProps = useBlockProps();
	const isDescendentOfQueryLoop = Number.isFinite( context?.queryId );
	const { width } = attributes;

	useEffect(
		() => setAttributes( { isDescendentOfQueryLoop } ),
		[ setAttributes, isDescendentOfQueryLoop ]
	);
	return (
		<>
			<BlockControls>
				{ isDescendentOfQueryLoop && (
					<AlignmentToolbar
						value={ attributes.textAlign }
						onChange={ ( newAlign ) => {
							setAttributes( { textAlign: newAlign || '' } );
						} }
					/>
				) }
			</BlockControls>
			<InspectorControls>
				<WidthPanel
					selectedWidth={ width }
					setAttributes={ setAttributes }
				/>
			</InspectorControls>
			<div { ...blockProps }>
				<Disabled>
					<Block
						{ ...{ ...attributes, ...context } }
						className={ classnames( attributes.className, {
							[ `has-custom-width wp-block-button__width-${ width }` ]:
								width,
						} ) }
					/>
				</Disabled>
			</div>
		</>
	);
};

export default Edit;
