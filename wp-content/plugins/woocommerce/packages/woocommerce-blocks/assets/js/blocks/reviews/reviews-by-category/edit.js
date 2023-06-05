/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	Placeholder,
	ToggleControl,
	withSpokenMessages,
} from '@wordpress/components';
import PropTypes from 'prop-types';
import ProductCategoryControl from '@woocommerce/editor-components/product-category-control';
import { Icon, commentContent } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import EditorContainerBlock from '../editor-container-block.js';
import NoReviewsPlaceholder from './no-reviews-placeholder.js';
import {
	getBlockControls,
	getSharedReviewContentControls,
	getSharedReviewListControls,
} from '../edit-utils.js';

/**
 * Component to handle edit mode of "Reviews by Category".
 *
 * @param {Object}            props                Incoming props for the component.
 * @param {Object}            props.attributes     Incoming block attributes.
 * @param {function(any):any} props.debouncedSpeak
 * @param {function(any):any} props.setAttributes  Setter for block attributes.
 */
const ReviewsByCategoryEditor = ( {
	attributes,
	debouncedSpeak,
	setAttributes,
} ) => {
	const { editMode, categoryIds } = attributes;

	const getInspectorControls = () => {
		return (
			<InspectorControls key="inspector">
				<PanelBody
					title={ __( 'Category', 'woocommerce' ) }
					initialOpen={ false }
				>
					<ProductCategoryControl
						selected={ attributes.categoryIds }
						onChange={ ( value = [] ) => {
							const ids = value.map( ( { id } ) => id );
							setAttributes( { categoryIds: ids } );
						} }
						isCompact={ true }
						showReviewCount={ true }
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Content', 'woocommerce' ) }
				>
					<ToggleControl
						label={ __(
							'Product name',
							'woocommerce'
						) }
						checked={ attributes.showProductName }
						onChange={ () =>
							setAttributes( {
								showProductName: ! attributes.showProductName,
							} )
						}
					/>
					{ getSharedReviewContentControls(
						attributes,
						setAttributes
					) }
				</PanelBody>
				<PanelBody
					title={ __(
						'List Settings',
						'woocommerce'
					) }
				>
					{ getSharedReviewListControls( attributes, setAttributes ) }
				</PanelBody>
			</InspectorControls>
		);
	};

	const renderEditMode = () => {
		const onDone = () => {
			setAttributes( { editMode: false } );
			debouncedSpeak(
				__(
					'Now displaying a preview of the reviews for the products in the selected categories.',
					'woocommerce'
				)
			);
		};

		return (
			<Placeholder
				icon={
					<Icon
						icon={ commentContent }
						className="block-editor-block-icon"
					/>
				}
				label={ __(
					'Reviews by Category',
					'woocommerce'
				) }
				className="wc-block-reviews-by-category"
			>
				{ __(
					'Show product reviews from specific categories.',
					'woocommerce'
				) }
				<div className="wc-block-reviews__selection">
					<ProductCategoryControl
						selected={ attributes.categoryIds }
						onChange={ ( value = [] ) => {
							const ids = value.map( ( { id } ) => id );
							setAttributes( { categoryIds: ids } );
						} }
						showReviewCount={ true }
					/>
					<Button isPrimary onClick={ onDone }>
						{ __( 'Done', 'woocommerce' ) }
					</Button>
				</div>
			</Placeholder>
		);
	};

	if ( ! categoryIds || editMode ) {
		return renderEditMode();
	}

	const buttonTitle = __(
		'Edit selected categories',
		'woocommerce'
	);

	return (
		<>
			{ getBlockControls( editMode, setAttributes, buttonTitle ) }
			{ getInspectorControls() }
			<EditorContainerBlock
				attributes={ attributes }
				icon={
					<Icon
						icon={ commentContent }
						className="block-editor-block-icon"
					/>
				}
				name={ __(
					'Reviews by Category',
					'woocommerce'
				) }
				noReviewsPlaceholder={ NoReviewsPlaceholder }
			/>
		</>
	);
};

ReviewsByCategoryEditor.propTypes = {
	/**
	 * The attributes for this block.
	 */
	attributes: PropTypes.object.isRequired,
	/**
	 * The register block name.
	 */
	name: PropTypes.string.isRequired,
	/**
	 * A callback to update attributes.
	 */
	setAttributes: PropTypes.func.isRequired,
	// from withSpokenMessages
	debouncedSpeak: PropTypes.func.isRequired,
};

export default withSpokenMessages( ReviewsByCategoryEditor );
