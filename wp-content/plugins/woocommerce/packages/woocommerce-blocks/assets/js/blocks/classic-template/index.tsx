/**
 * External dependencies
 */
import {
	getBlockType,
	registerBlockType,
	unregisterBlockType,
} from '@wordpress/blocks';
import type { BlockEditProps } from '@wordpress/blocks';
import {
	isExperimentalBuild,
	WC_BLOCKS_IMAGE_URL,
} from '@woocommerce/block-settings';
import { useBlockProps } from '@wordpress/block-editor';
import { Button, Placeholder } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { box, Icon } from '@wordpress/icons';
import { select, useDispatch, subscribe } from '@wordpress/data';
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import './editor.scss';
import './style.scss';
import { BLOCK_SLUG, TEMPLATES, TYPES } from './constants';
import {
	isClassicTemplateBlockRegisteredWithAnotherTitle,
	hasTemplateSupportForClassicTemplateBlock,
	getTemplateDetailsBySlug,
} from './utils';
import {
	blockifiedProductCatalogConfig,
	blockifiedProductTaxonomyConfig,
} from './archive-product';
import * as blockifiedSingleProduct from './single-product';
import * as blockifiedProductSearchResults from './product-search-results';
import type { BlockifiedTemplateConfig } from './types';

type Attributes = {
	template: string;
	align: string;
};

const blockifiedFallbackConfig = {
	isConversionPossible: () => false,
	getBlockifiedTemplate: () => [],
	getDescription: () => '',
	getButtonLabel: () => '',
};

const conversionConfig: { [ key: string ]: BlockifiedTemplateConfig } = {
	[ TYPES.productCatalog ]: blockifiedProductCatalogConfig,
	[ TYPES.productTaxonomy ]: blockifiedProductTaxonomyConfig,
	[ TYPES.singleProduct ]: blockifiedSingleProduct,
	[ TYPES.productSearchResults ]: blockifiedProductSearchResults,
	fallback: blockifiedFallbackConfig,
};

const Edit = ( {
	clientId,
	attributes,
	setAttributes,
}: BlockEditProps< Attributes > ) => {
	const { replaceBlock } = useDispatch( 'core/block-editor' );

	const blockProps = useBlockProps();
	const templateDetails = getTemplateDetailsBySlug(
		attributes.template,
		TEMPLATES
	);
	const templateTitle = templateDetails?.title ?? attributes.template;
	const templatePlaceholder = templateDetails?.placeholder ?? 'fallback';
	const templateType = templateDetails?.type ?? 'fallback';

	useEffect(
		() =>
			setAttributes( {
				template: attributes.template,
				align: attributes.align ?? 'wide',
			} ),
		[ attributes.align, attributes.template, setAttributes ]
	);

	const {
		getBlockifiedTemplate,
		isConversionPossible,
		getDescription,
		getButtonLabel,
	} = conversionConfig[ templateType ];

	const canConvert = isConversionPossible();
	const placeholderDescription = getDescription( templateTitle, canConvert );

	return (
		<div { ...blockProps }>
			<Placeholder
				icon={ box }
				label={ templateTitle }
				className="wp-block-woocommerce-classic-template__placeholder"
			>
				<div className="wp-block-woocommerce-classic-template__placeholder-copy">
					<p>{ placeholderDescription }</p>
				</div>
				<div className="wp-block-woocommerce-classic-template__placeholder-wireframe">
					{ canConvert && (
						<div className="wp-block-woocommerce-classic-template__placeholder-migration-button-container">
							<Button
								isPrimary
								onClick={ () => {
									replaceBlock(
										clientId,
										getBlockifiedTemplate( attributes )
									);
								} }
								text={ getButtonLabel() }
							/>
						</div>
					) }
					<img
						className="wp-block-woocommerce-classic-template__placeholder-image"
						src={ `${ WC_BLOCKS_IMAGE_URL }template-placeholders/${ templatePlaceholder }.svg` }
						alt={ templateTitle }
					/>
				</div>
			</Placeholder>
		</div>
	);
};

const registerClassicTemplateBlock = ( {
	template,
	inserter,
}: {
	template?: string;
	inserter: boolean;
} ) => {
	/**
	 * The 'WooCommerce Legacy Template' block was renamed to 'WooCommerce Classic Template', however, the internal block
	 * name 'woocommerce/legacy-template' needs to remain the same. Otherwise, it would result in a corrupt block when
	 * loaded for users who have customized templates using the legacy-template (since the internal block name is
	 * stored in the database).
	 *
	 * See https://github.com/woocommerce/woocommerce-gutenberg-products-block/issues/5861 for more context
	 */
	registerBlockType( BLOCK_SLUG, {
		title:
			template && TEMPLATES[ template ]
				? TEMPLATES[ template ].title
				: __(
						'WooCommerce Classic Template',
						'woo-gutenberg-products-block'
				  ),
		icon: (
			<Icon
				icon={ box }
				className="wc-block-editor-components-block-icon"
			/>
		),
		category: 'woocommerce',
		apiVersion: 2,
		keywords: [ __( 'WooCommerce', 'woo-gutenberg-products-block' ) ],
		description: __(
			'Renders classic WooCommerce PHP templates.',
			'woo-gutenberg-products-block'
		),
		supports: {
			align: [ 'wide', 'full' ],
			html: false,
			multiple: false,
			reusable: false,
			inserter,
		},
		attributes: {
			/**
			 * Template attribute is used to determine which core PHP template gets rendered.
			 */
			template: {
				type: 'string',
				default: 'any',
			},
			align: {
				type: 'string',
				default: 'wide',
			},
		},
		edit: ( {
			attributes,
			clientId,
			setAttributes,
		}: BlockEditProps< Attributes > ) => {
			const newTemplate = template ?? attributes.template;

			return (
				<Edit
					attributes={ {
						...attributes,
						template: newTemplate,
					} }
					setAttributes={ setAttributes }
					clientId={ clientId }
				/>
			);
		},
		save: () => null,
	} );
};

// @todo Refactor when there will be possible to show a block according on a template/post with a Gutenberg API. https://github.com/WordPress/gutenberg/pull/41718

let currentTemplateId: string | undefined;

if ( isExperimentalBuild() ) {
	subscribe( () => {
		const previousTemplateId = currentTemplateId;
		const store = select( 'core/edit-site' );
		currentTemplateId = store?.getEditedPostId() as string | undefined;

		if ( previousTemplateId === currentTemplateId ) {
			return;
		}

		const parsedTemplate = currentTemplateId?.split( '//' )[ 1 ];

		if ( parsedTemplate === null || parsedTemplate === undefined ) {
			return;
		}

		const block = getBlockType( BLOCK_SLUG );

		if (
			block !== undefined &&
			( ! hasTemplateSupportForClassicTemplateBlock(
				parsedTemplate,
				TEMPLATES
			) ||
				isClassicTemplateBlockRegisteredWithAnotherTitle(
					block,
					parsedTemplate
				) )
		) {
			unregisterBlockType( BLOCK_SLUG );
			currentTemplateId = undefined;
			return;
		}

		if (
			block === undefined &&
			hasTemplateSupportForClassicTemplateBlock(
				parsedTemplate,
				TEMPLATES
			)
		) {
			registerClassicTemplateBlock( {
				template: parsedTemplate,
				inserter: true,
			} );
		}
	} );
} else {
	registerClassicTemplateBlock( {
		inserter: false,
	} );
}
