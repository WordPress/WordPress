/**
 * External dependencies
 */
import { sort } from 'fast-sort';
import { __, sprintf, _n } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import {
	InspectorControls,
	BlockControls,
	useBlockProps,
} from '@wordpress/block-editor';
import { Icon, category, external } from '@wordpress/icons';
import { SearchListControl } from '@woocommerce/editor-components/search-list-control';
import { getAdminLink, getSetting } from '@woocommerce/settings';
import BlockTitle from '@woocommerce/editor-components/block-title';
import classnames from 'classnames';
import { SearchListItem } from '@woocommerce/editor-components/search-list-control/types';
import { AttributeSetting } from '@woocommerce/types';
import {
	Placeholder,
	Disabled,
	PanelBody,
	ToggleControl,
	Button,
	ToolbarGroup,
	Notice,
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
import type { EditProps, GetNotice } from './types';
import { UpgradeNotice } from '../filter-wrapper/upgrade';

const ATTRIBUTES = getSetting< AttributeSetting[] >( 'attributes', [] );

const noticeContent = {
	noAttributes: __(
		'Please select an attribute to use this filter!',
		'woo-gutenberg-products-block'
	),
	noProducts: __(
		'There are no products with the selected attributes.',
		'woo-gutenberg-products-block'
	),
};

const getNotice: GetNotice = ( type ) => {
	const content = noticeContent[ type ];
	return content ? (
		<Notice status="warning" isDismissible={ false }>
			<p>{ content }</p>
		</Notice>
	) : null;
};

const Edit = ( {
	attributes,
	setAttributes,
	debouncedSpeak,
	clientId,
}: EditProps ) => {
	const {
		attributeId,
		className,
		displayStyle,
		heading,
		headingLevel,
		isPreview,
		queryType,
		showCounts,
		showFilterButton,
		selectType,
	} = attributes;

	const [ isEditing, setIsEditing ] = useState(
		! attributeId && ! isPreview
	);

	const blockProps = useBlockProps();

	const getBlockControls = () => {
		return (
			<BlockControls>
				<ToolbarGroup
					controls={ [
						{
							icon: 'edit',
							title: __( 'Edit', 'woo-gutenberg-products-block' ),
							onClick: () => setIsEditing( ! isEditing ),
							isActive: isEditing,
						},
					] }
				/>
			</BlockControls>
		);
	};

	const onChange = ( selected: SearchListItem[] ) => {
		if ( ! selected || ! selected.length ) {
			return;
		}

		const selectedId = selected[ 0 ].id;
		const productAttribute = ATTRIBUTES.find(
			( attribute ) => attribute.attribute_id === selectedId.toString()
		);

		if ( ! productAttribute || attributeId === selectedId ) {
			return;
		}

		setAttributes( {
			attributeId: selectedId as number,
		} );
	};

	const renderAttributeControl = ( {
		isCompact,
	}: {
		isCompact: boolean;
	} ) => {
		const messages = {
			clear: __(
				'Clear selected attribute',
				'woo-gutenberg-products-block'
			),
			list: __( 'Product Attributes', 'woo-gutenberg-products-block' ),
			noItems: __(
				"Your store doesn't have any product attributes.",
				'woo-gutenberg-products-block'
			),
			search: __(
				'Search for a product attribute:',
				'woo-gutenberg-products-block'
			),
			selected: ( n: number ) =>
				sprintf(
					/* translators: %d is the number of attributes selected. */
					_n(
						'%d attribute selected',
						'%d attributes selected',
						n,
						'woo-gutenberg-products-block'
					),
					n
				),
			updated: __(
				'Product attribute search results updated.',
				'woo-gutenberg-products-block'
			),
		};

		const list = sort(
			ATTRIBUTES.map( ( item ) => {
				return {
					id: parseInt( item.attribute_id, 10 ),
					name: item.attribute_label,
				};
			} )
		).asc( 'name' );

		return (
			<SearchListControl
				className="woocommerce-product-attributes"
				list={ list }
				selected={ list.filter( ( { id } ) => id === attributeId ) }
				onChange={ onChange }
				messages={ messages }
				isSingle
				isCompact={ isCompact }
			/>
		);
	};

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
					{ selectType === 'multiple' && (
						<ToggleGroupControl
							label={ __(
								'Filter Conditions',
								'woo-gutenberg-products-block'
							) }
							help={
								queryType === 'and'
									? __(
											'Choose to return filter results for all of the attributes selected.',
											'woo-gutenberg-products-block'
									  )
									: __(
											'Choose to return filter results for any of the attributes selected.',
											'woo-gutenberg-products-block'
									  )
							}
							value={ queryType }
							onChange={ ( value: string ) =>
								setAttributes( {
									queryType: value,
								} )
							}
							className="wc-block-attribute-filter__conditions-toggle"
						>
							<ToggleGroupControlOption
								value="and"
								label={ __(
									'All',
									'woo-gutenberg-products-block'
								) }
							/>
							<ToggleGroupControlOption
								value="or"
								label={ __(
									'Any',
									'woo-gutenberg-products-block'
								) }
							/>
						</ToggleGroupControl>
					) }
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
				<PanelBody
					title={ __(
						'Content Settings',
						'woo-gutenberg-products-block'
					) }
					initialOpen={ false }
				>
					{ renderAttributeControl( { isCompact: true } ) }
				</PanelBody>
			</InspectorControls>
		);
	};

	const noAttributesPlaceholder = () => (
		<Placeholder
			className="wc-block-attribute-filter"
			icon={ <Icon icon={ category } /> }
			label={ __(
				'Filter by Attribute',
				'woo-gutenberg-products-block'
			) }
			instructions={ __(
				'Display a list of filters based on the selected attributes.',
				'woo-gutenberg-products-block'
			) }
		>
			<p>
				{ __(
					"Attributes are needed for filtering your products. You haven't created any attributes yet.",
					'woo-gutenberg-products-block'
				) }
			</p>
			<Button
				className="wc-block-attribute-filter__add-attribute-button"
				isSecondary
				href={ getAdminLink(
					'edit.php?post_type=product&page=product_attributes'
				) }
			>
				{ __( 'Add new attribute', 'woo-gutenberg-products-block' ) +
					' ' }
				<Icon icon={ external } />
			</Button>
			<Button
				className="wc-block-attribute-filter__read_more_button"
				isTertiary
				href="https://docs.woocommerce.com/document/managing-product-taxonomies/"
			>
				{ __( 'Learn more', 'woo-gutenberg-products-block' ) }
			</Button>
		</Placeholder>
	);

	const onDone = () => {
		setIsEditing( false );
		debouncedSpeak(
			__(
				'Now displaying a preview of the Filter Products by Attribute block.',
				'woo-gutenberg-products-block'
			)
		);
	};

	const renderEditMode = () => {
		return (
			<Placeholder
				className="wc-block-attribute-filter"
				icon={ <Icon icon={ category } /> }
				label={ __(
					'Filter by Attribute',
					'woo-gutenberg-products-block'
				) }
			>
				<div className="wc-block-attribute-filter__instructions">
					{ __(
						'Display a list of filters based on the selected attributes.',
						'woo-gutenberg-products-block'
					) }
				</div>
				<div className="wc-block-attribute-filter__selection">
					{ renderAttributeControl( { isCompact: false } ) }
					<Button isPrimary onClick={ onDone }>
						{ __( 'Done', 'woo-gutenberg-products-block' ) }
					</Button>
				</div>
			</Placeholder>
		);
	};

	return Object.keys( ATTRIBUTES ).length === 0 ? (
		noAttributesPlaceholder()
	) : (
		<div { ...blockProps }>
			{ getBlockControls() }
			{ getInspectorControls() }
			<UpgradeNotice
				clientId={ clientId }
				attributes={ attributes }
				setAttributes={ setAttributes }
				filterType="attribute-filter"
			/>
			{ isEditing ? (
				renderEditMode()
			) : (
				<div
					className={ classnames(
						className,
						'wc-block-attribute-filter'
					) }
				>
					{ heading && (
						<BlockTitle
							className="wc-block-attribute-filter__title"
							headingLevel={ headingLevel }
							heading={ heading }
							onChange={ ( value: string ) =>
								setAttributes( { heading: value } )
							}
						/>
					) }
					<Disabled>
						<Block
							attributes={ attributes }
							isEditor={ true }
							getNotice={ getNotice }
						/>
					</Disabled>
				</div>
			) }
		</div>
	);
};

export default withSpokenMessages( Edit );
