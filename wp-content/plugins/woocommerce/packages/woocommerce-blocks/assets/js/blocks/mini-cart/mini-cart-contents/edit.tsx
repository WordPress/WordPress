/* eslint-disable jsdoc/check-alignment */
/**
 * External dependencies
 */
import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { EditorProvider } from '@woocommerce/base-context';
import { isFeaturePluginBuild } from '@woocommerce/block-settings';
import type { TemplateArray } from '@wordpress/blocks';
import { useEffect } from '@wordpress/element';
import type { FocusEvent, ReactElement } from 'react';
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { useForcedLayout } from '../../cart-checkout-shared';
import { MiniCartInnerBlocksStyle } from './inner-blocks-style';
import './editor.scss';
import { attributes as defaultAttributes } from './attributes';

// Array of allowed block names.
const ALLOWED_BLOCKS = [
	'woocommerce/filled-mini-cart-contents-block',
	'woocommerce/empty-mini-cart-contents-block',
];
const MIN_WIDTH = 300;

interface Props {
	clientId: string;
	attributes: Record< string, unknown >;
	setAttributes: ( attributes: Record< string, unknown > ) => void;
}

const Edit = ( {
	clientId,
	attributes,
	setAttributes,
}: Props ): ReactElement => {
	const { currentView, width } = attributes;

	const blockProps = useBlockProps( {
		/**
		 * This is a workaround for the Site Editor to calculate the
		 * correct height of the Mini Cart template part on the first load.
		 *
		 * @see https://github.com/woocommerce/woocommerce-gutenberg-products-block/pull/5825
		 */
		style: {
			minHeight: '100vh',
			width,
		},
	} );

	const defaultTemplate = [
		[ 'woocommerce/filled-mini-cart-contents-block', {}, [] ],
		[ 'woocommerce/empty-mini-cart-contents-block', {}, [] ],
	] as TemplateArray;

	useForcedLayout( {
		clientId,
		registeredBlocks: ALLOWED_BLOCKS,
		defaultTemplate,
	} );

	/**
	 * This is a workaround for the Site Editor to set the correct
	 * background color of the Mini Cart Contents block base on
	 * the main background color set by the theme.
	 */
	useEffect( () => {
		const canvasEl = document.querySelector(
			'.edit-site-visual-editor__editor-canvas'
		);
		if ( ! ( canvasEl instanceof HTMLIFrameElement ) ) {
			return;
		}
		const canvas =
			canvasEl.contentDocument || canvasEl.contentWindow?.document;
		if ( ! canvas ) {
			return;
		}
		if ( canvas.getElementById( 'mini-cart-contents-background-color' ) ) {
			return;
		}
		const styles = canvas.querySelectorAll( 'style' );
		const [ cssRule ] = Array.from( styles )
			.map( ( style ) => Array.from( style.sheet?.cssRules || [] ) )
			.flatMap( ( style ) => style )
			.filter( Boolean )
			.filter(
				( rule ) =>
					rule.selectorText === '.editor-styles-wrapper' &&
					rule.style.backgroundColor
			);
		if ( ! cssRule ) {
			return;
		}
		const backgroundColor = cssRule.style.backgroundColor;
		if ( ! backgroundColor ) {
			return;
		}
		const style = document.createElement( 'style' );
		style.id = 'mini-cart-contents-background-color';
		style.appendChild(
			document.createTextNode(
				`:where(.wp-block-woocommerce-mini-cart-contents) {
				background-color: ${ backgroundColor };
			}`
			)
		);
		const body = canvas.querySelector( '.editor-styles-wrapper' );
		if ( ! body ) {
			return;
		}
		body.appendChild( style );
	}, [] );

	return (
		<>
			{ isFeaturePluginBuild() && (
				<InspectorControls key="inspector">
					<PanelBody
						title={ __(
							'Dimensions',
							'woo-gutenberg-products-block'
						) }
						initialOpen
					>
						<UnitControl
							onChange={ ( value ) => {
								setAttributes( { width: value } );
							} }
							onBlur={ ( e: FocusEvent< HTMLInputElement > ) => {
								if ( e.target.value === '' ) {
									setAttributes( {
										width: defaultAttributes.width.default,
									} );
								} else if (
									Number( e.target.value ) < MIN_WIDTH
								) {
									setAttributes( {
										width: MIN_WIDTH + 'px',
									} );
								}
							} }
							value={ width }
							units={ [
								{
									value: 'px',
									label: 'px',
									default: defaultAttributes.width.default,
								},
							] }
						/>
					</PanelBody>
				</InspectorControls>
			) }
			<div
				className="wc-block-components-drawer__screen-overlay"
				aria-hidden="true"
			></div>
			<div className="wc-block-editor-mini-cart-contents__wrapper">
				<div { ...blockProps }>
					<EditorProvider currentView={ currentView }>
						<InnerBlocks
							allowedBlocks={ ALLOWED_BLOCKS }
							template={ defaultTemplate }
							templateLock={ false }
						/>
					</EditorProvider>
					<MiniCartInnerBlocksStyle style={ blockProps.style } />
				</div>
			</div>
		</>
	);
};

export default Edit;

export const Save = (): JSX.Element => {
	return (
		<div { ...useBlockProps.save() }>
			<InnerBlocks.Content />
		</div>
	);
};
