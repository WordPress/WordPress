/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import classnames from 'classnames';
import {
	PlainText,
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';

/**
 * Internal dependencies
 */
import FormStepHeading from './form-step-heading';
export interface FormStepBlockProps {
	attributes: { title: string; description: string; showStepNumber: boolean };
	setAttributes: ( attributes: Record< string, unknown > ) => void;
	className?: string;
	children?: React.ReactNode;
	lock?: { move: boolean; remove: boolean };
}

/**
 * Form Step Block for use in the editor.
 */
export const FormStepBlock = ( {
	attributes,
	setAttributes,
	className = '',
	children,
}: FormStepBlockProps ): JSX.Element => {
	const { title = '', description = '', showStepNumber = true } = attributes;
	const blockProps = useBlockProps( {
		className: classnames( 'wc-block-components-checkout-step', className, {
			'wc-block-components-checkout-step--with-step-number':
				showStepNumber,
		} ),
	} );
	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Form Step Options',
						'woo-gutenberg-products-block'
					) }
				>
					<ToggleControl
						label={ __(
							'Show step number',
							'woo-gutenberg-products-block'
						) }
						checked={ showStepNumber }
						onChange={ () =>
							setAttributes( {
								showStepNumber: ! showStepNumber,
							} )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<FormStepHeading>
				<PlainText
					className={ '' }
					value={ title }
					onChange={ ( value ) => setAttributes( { title: value } ) }
					style={ { backgroundColor: 'transparent' } }
				/>
			</FormStepHeading>
			<div className="wc-block-components-checkout-step__container">
				<p className="wc-block-components-checkout-step__description">
					<PlainText
						className={
							! description
								? 'wc-block-components-checkout-step__description-placeholder'
								: ''
						}
						value={ description }
						placeholder={ __(
							'Optional text for this form step.',
							'woo-gutenberg-products-block'
						) }
						onChange={ ( value ) =>
							setAttributes( {
								description: value,
							} )
						}
						style={ { backgroundColor: 'transparent' } }
					/>
				</p>
				<div className="wc-block-components-checkout-step__content">
					{ children }
				</div>
			</div>
		</div>
	);
};
