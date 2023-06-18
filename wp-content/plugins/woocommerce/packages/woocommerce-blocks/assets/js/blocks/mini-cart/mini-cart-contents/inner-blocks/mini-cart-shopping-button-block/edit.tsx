/**
 * External dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import EditableButton from '@woocommerce/editor-components/editable-button';

/**
 * Internal dependencies
 */
import { defaultStartShoppingButtonLabel } from './constants';
import { getVariant } from '../utils';

export const Edit = ( {
	attributes,
	setAttributes,
}: {
	attributes: {
		startShoppingButtonLabel: string;
	};
	setAttributes: ( attributes: Record< string, unknown > ) => void;
} ): JSX.Element => {
	const blockProps = useBlockProps( {
		className: 'wp-block-button aligncenter',
	} );
	const { startShoppingButtonLabel } = attributes;

	return (
		<div { ...blockProps }>
			<EditableButton
				className="wc-block-mini-cart__shopping-button"
				value={ startShoppingButtonLabel }
				placeholder={ defaultStartShoppingButtonLabel }
				onChange={ ( content ) => {
					setAttributes( {
						startShoppingButtonLabel: content,
					} );
				} }
				variant={ getVariant( blockProps.className, 'contained' ) }
			/>
		</div>
	);
};

export const Save = (): JSX.Element => {
	return <div { ...useBlockProps.save() }></div>;
};
