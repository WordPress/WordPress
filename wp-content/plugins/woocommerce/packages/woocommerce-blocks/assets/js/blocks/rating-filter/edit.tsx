/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import classnames from 'classnames';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import type { BlockEditProps } from '@wordpress/blocks';
import {
	Disabled,
	Notice,
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
import { Attributes } from './types';
import './editor.scss';

const noRatingsNotice = (
	<Notice status="warning" isDismissible={ false }>
		<p>
			{ __(
				"Your store doesn't have any products with ratings yet. This filter option will display when a product receives a review.",
				'woo-gutenberg-products-block'
			) }
		</p>
	</Notice>
);

const Edit = ( {
	attributes,
	setAttributes,
}: BlockEditProps< Attributes > ) => {
	const {
		className,
		displayStyle,
		showCounts,
		showFilterButton,
		selectType,
	} = attributes;

	const blockProps = useBlockProps( {
		className: classnames( 'wc-block-rating-filter', className ),
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
						onChange={ ( value: string ) =>
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
			{
				<div { ...blockProps }>
					<Disabled>
						<Block
							attributes={ attributes }
							isEditor={ true }
							noRatingsNotice={ noRatingsNotice }
						/>
					</Disabled>
				</div>
			}
		</>
	);
};

export default withSpokenMessages( Edit );
