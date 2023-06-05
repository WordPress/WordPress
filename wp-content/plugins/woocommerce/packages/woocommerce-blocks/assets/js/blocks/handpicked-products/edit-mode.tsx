/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button, Placeholder } from '@wordpress/components';
import { Icon, stack } from '@wordpress/icons';
import ProductsControl from '@woocommerce/editor-components/products-control';

/**
 * Internal dependencies
 */
import { Props } from './types';

export interface EditModeProps extends Props {
	isEditing: boolean;
	setIsEditing: ( isEditing: boolean ) => void;
}

export const HandpickedProductsEditMode = (
	props: EditModeProps
): JSX.Element => {
	const {
		attributes,
		setAttributes,
		debouncedSpeak,
		isEditing,
		setIsEditing,
	} = props;
	const onDone = () => {
		setIsEditing( ! isEditing );
		debouncedSpeak(
			__(
				'Now displaying a preview of the Hand-picked Products block.',
				'woo-gutenberg-products-block'
			)
		);
	};

	return (
		<Placeholder
			icon={ <Icon icon={ stack } /> }
			label={ __(
				'Hand-picked Products',
				'woo-gutenberg-products-block'
			) }
			className="wc-block-products-grid wc-block-handpicked-products"
		>
			{ __(
				'Display a selection of hand-picked products in a grid.',
				'woo-gutenberg-products-block'
			) }
			<div className="wc-block-handpicked-products__selection">
				<ProductsControl
					selected={ attributes.products }
					onChange={ ( value = [] ) => {
						const ids = value.map( ( { id } ) => id );
						setAttributes( { products: ids } );
					} }
				/>
				<Button isPrimary onClick={ onDone }>
					{ __( 'Done', 'woo-gutenberg-products-block' ) }
				</Button>
			</div>
		</Placeholder>
	);
};
