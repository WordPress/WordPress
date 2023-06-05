/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button, Placeholder } from '@wordpress/components';
import { category, Icon } from '@wordpress/icons';
import ProductAttributeTermControl from '@woocommerce/editor-components/product-attribute-term-control';

/**
 * Internal dependencies
 */
import { Props } from './types';

export interface EditModeProps extends Props {
	isEditing: boolean;
	setIsEditing: ( isEditing: boolean ) => void;
}

export const ProductsByAttributeEditMode = (
	props: EditModeProps
): JSX.Element => {
	const {
		attributes: blockAttributes,
		setAttributes,
		setIsEditing,
		isEditing,
		debouncedSpeak,
	} = props;

	const onDone = () => {
		setIsEditing( ! isEditing );
		debouncedSpeak(
			__(
				'Showing Products by Attribute block preview.',
				'woo-gutenberg-products-block'
			)
		);
	};

	return (
		<Placeholder
			icon={ <Icon icon={ category } /> }
			label={ __(
				'Products by Attribute',
				'woo-gutenberg-products-block'
			) }
			className="wc-block-products-grid wc-block-products-by-attribute"
		>
			{ __(
				'Display a grid of products from your selected attributes.',
				'woo-gutenberg-products-block'
			) }
			<div className="wc-block-products-by-attribute__selection">
				<ProductAttributeTermControl
					selected={ blockAttributes.attributes }
					onChange={ ( value = [] ) => {
						const result = value.map(
							( { id, value: attributeSlug } ) => ( {
								id,
								attr_slug: attributeSlug,
							} )
						);
						setAttributes( { attributes: result } );
					} }
					operator={ blockAttributes.attrOperator }
					onOperatorChange={ ( value = 'any' ) =>
						setAttributes( { attrOperator: value } )
					}
				/>
				<Button isPrimary onClick={ onDone }>
					{ __( 'Done', 'woo-gutenberg-products-block' ) }
				</Button>
			</div>
		</Placeholder>
	);
};
