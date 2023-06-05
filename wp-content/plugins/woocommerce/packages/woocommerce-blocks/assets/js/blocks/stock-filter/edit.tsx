/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import classnames from 'classnames';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import BlockTitle from '@woocommerce/editor-components/block-title';
import type { BlockEditProps } from '@wordpress/blocks';
import {
	Disabled,
	PanelBody,
	ToggleControl,
	withSpokenMessages,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControl as ToggleGroupControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import Block from './block';
import './editor.scss';
import { Attributes } from './types';
import { UpgradeNotice } from '../filter-wrapper/upgrade';

const Edit = ( {
	clientId,
	attributes,
	setAttributes,
}: BlockEditProps< Attributes > ) => {
	const {
		className,
		heading,
		headingLevel,
		showCounts,
		showFilterButton,
		selectType,
		displayStyle,
	} = attributes;

	const blockProps = useBlockProps( {
		className: classnames( 'wc-block-stock-filter', className ),
	} );

	const getInspectorControls = () => {
		return (
			<InspectorControls key="inspector">
				<PanelBody
					title={ __(
						'Display Settings',
						'woo-gutenberg-products-block'
					) }
				>
					<ToggleControl
						label={ __(
							'Display product count',
							'woo-gutenberg-products-block'
						) }
						checked={ showCounts }
						onChange={ () =>
							setAttributes( {
								showCounts: ! showCounts,
							} )
						}
					/>
					<ToggleGroupControl
						label={ __(
							'Allow selecting multiple options?',
							'woo-gutenberg-products-block'
						) }
						value={ selectType || 'multiple' }
						onChange={ ( value: string ) =>
							setAttributes( {
								selectType: value,
							} )
						}
						className="wc-block-attribute-filter__multiple-toggle"
					>
						<ToggleGroupControlOption
							value="multiple"
							label={ __(
								'Multiple',
								'woo-gutenberg-products-block'
							) }
						/>
						<ToggleGroupControlOption
							value="single"
							label={ __(
								'Single',
								'woo-gutenberg-products-block'
							) }
						/>
					</ToggleGroupControl>
					<ToggleGroupControl
						label={ __(
							'Display Style',
							'woo-gutenberg-products-block'
						) }
						value={ displayStyle }
						onChange={ ( value ) =>
							setAttributes( {
								displayStyle: value,
							} )
						}
						className="wc-block-attribute-filter__display-toggle"
					>
						<ToggleGroupControlOption
							value="list"
							label={ __(
								'List',
								'woo-gutenberg-products-block'
							) }
						/>
						<ToggleGroupControlOption
							value="dropdown"
							label={ __(
								'Dropdown',
								'woo-gutenberg-products-block'
							) }
						/>
					</ToggleGroupControl>
					<ToggleControl
						label={ __(
							"Show 'Apply filters' button",
							'woo-gutenberg-products-block'
						) }
						help={ __(
							'Products will update when the button is clicked.',
							'woo-gutenberg-products-block'
						) }
						checked={ showFilterButton }
						onChange={ ( value ) =>
							setAttributes( {
								showFilterButton: value,
							} )
						}
					/>
				</PanelBody>
			</InspectorControls>
		);
	};

	return (
		<>
			{ getInspectorControls() }
			<UpgradeNotice
				clientId={ clientId }
				attributes={ attributes }
				setAttributes={ setAttributes }
				filterType="stock-filter"
			/>
			{
				<div { ...blockProps }>
					{ heading && (
						<BlockTitle
							className="wc-block-stock-filter__title"
							headingLevel={ headingLevel }
							heading={ heading }
							onChange={ ( value: string ) =>
								setAttributes( { heading: value } )
							}
						/>
					) }
					<Disabled>
						<Block attributes={ attributes } isEditor={ true } />
					</Disabled>
				</div>
			}
		</>
	);
};

export default withSpokenMessages( Edit );
