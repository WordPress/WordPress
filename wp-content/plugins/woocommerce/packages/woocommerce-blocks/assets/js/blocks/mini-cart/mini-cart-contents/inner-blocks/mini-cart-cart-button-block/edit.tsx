/**
 * External dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import EditableButton from '@woocommerce/editor-components/editable-button';

/**
 * Internal dependencies
 */
import { defaultCartButtonLabel } from './constants';
import { getVariant } from '../utils';

export const Edit = ( {
	attributes,
	setAttributes,
}: {
	attributes: {
		cartButtonLabel: string;
	};
	setAttributes: ( attributes: Record< string, unknown > ) => void;
} ): JSX.Element => {
	const blockProps = useBlockProps();
	const { cartButtonLabel } = attributes;

	return (
		<EditableButton
			{ ...blockProps }
			className="wc-block-mini-cart__footer-cart"
			variant={ getVariant( blockProps.className, 'outlined' ) }
			value={ cartButtonLabel }
			placeholder={ defaultCartButtonLabel }
			onChange={ ( content ) => {
				setAttributes( {
					cartButtonLabel: content,
				} );
			} }
		/>
	);
};

export const Save = (): JSX.Element => {
	return <div { ...useBlockProps.save() }></div>;
};
