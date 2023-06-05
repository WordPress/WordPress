/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Disabled, PanelBody, ToggleControl } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import {
	InspectorControls,
	BlockControls,
	AlignmentToolbar,
	useBlockProps,
} from '@wordpress/block-editor';
import { isFeaturePluginBuild } from '@woocommerce/block-settings';
import HeadingToolbar from '@woocommerce/editor-components/heading-toolbar';

/**
 * Internal dependencies
 */
import Block from './block';
import withProductSelector from '../shared/with-product-selector';
import { BLOCK_TITLE, BLOCK_ICON } from './constants';
import { Attributes } from './types';
import './editor.scss';

interface Props {
	attributes: Attributes;
	setAttributes: ( attributes: Record< string, unknown > ) => void;
}

const TitleEdit = ( { attributes, setAttributes }: Props ): JSX.Element => {
	const blockProps = useBlockProps();
	const { headingLevel, showProductLink, align, linkTarget } = attributes;
	return (
		<div { ...blockProps }>
			<BlockControls>
				<HeadingToolbar
					isCollapsed={ true }
					minLevel={ 1 }
					maxLevel={ 7 }
					selectedLevel={ headingLevel }
					onChange={ ( newLevel: number ) =>
						setAttributes( { headingLevel: newLevel } )
					}
				/>
				{ isFeaturePluginBuild() && (
					<AlignmentToolbar
						value={ align }
						onChange={ ( newAlign ) => {
							setAttributes( { align: newAlign } );
						} }
					/>
				) }
			</BlockControls>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Link settings',
						'woo-gutenberg-products-block'
					) }
				>
					<ToggleControl
						label={ __(
							'Make title a link',
							'woo-gutenberg-products-block'
						) }
						checked={ showProductLink }
						onChange={ () =>
							setAttributes( {
								showProductLink: ! showProductLink,
							} )
						}
					/>
					{ showProductLink && (
						<>
							<ToggleControl
								label={ __(
									'Open in new tab',
									'woo-gutenberg-products-block'
								) }
								onChange={ ( value ) =>
									setAttributes( {
										linkTarget: value ? '_blank' : '_self',
									} )
								}
								checked={ linkTarget === '_blank' }
							/>
						</>
					) }
				</PanelBody>
			</InspectorControls>
			<Disabled>
				<Block { ...attributes } />
			</Disabled>
		</div>
	);
};

const Title = isFeaturePluginBuild()
	? compose( [
			withProductSelector( {
				icon: BLOCK_ICON,
				label: BLOCK_TITLE,
				description: __(
					'Choose a product to display its title.',
					'woo-gutenberg-products-block'
				),
			} ),
	  ] )( TitleEdit )
	: TitleEdit;

export default Title;
