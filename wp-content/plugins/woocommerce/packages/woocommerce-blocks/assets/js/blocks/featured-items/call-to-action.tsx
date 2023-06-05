/**
 * External dependencies
 */
import classnames from 'classnames';
import { RichText, InnerBlocks } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

interface CallToActionProps {
	itemId: number | 'preview';
	linkText: string;
	permalink: string;
}

export const CallToAction = ( {
	itemId,
	linkText,
	permalink,
}: CallToActionProps ) => {
	const buttonClasses = classnames(
		'wp-block-button__link',
		'is-style-fill'
	);
	const buttonStyle = {
		backgroundColor: 'vivid-green-cyan',
		borderRadius: '5px',
	};
	const wrapperStyle = {
		width: '100%',
	};
	return itemId === 'preview' ? (
		<div className="wp-block-button aligncenter" style={ wrapperStyle }>
			<RichText.Content
				tagName="a"
				className={ buttonClasses }
				href={ permalink }
				title={ linkText }
				style={ buttonStyle }
				value={ linkText }
				target={ permalink }
			/>
		</div>
	) : (
		<InnerBlocks
			template={ [
				[
					'core/buttons',
					{
						layout: { type: 'flex', justifyContent: 'center' },
					},
					[
						[
							'core/button',
							{
								text: __(
									'Shop now',
									'woo-gutenberg-products-block'
								),
								url: permalink,
							},
						],
					],
				],
			] }
			templateLock="all"
		/>
	);
};
