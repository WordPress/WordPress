/**
 * External dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { Button, Disabled, Tooltip } from '@wordpress/components';
import { Skeleton } from '@woocommerce/base-components/skeleton';

/**
 * Internal dependencies
 */
import './editor.scss';
export interface Attributes {
	className?: string;
}

const Edit = () => {
	const blockProps = useBlockProps( {
		className: 'wc-block-add-to-cart-form',
	} );

	return (
		<div { ...blockProps }>
			<Tooltip
				text="Customer will see product add-to-cart options in this space, dependend on the product type. "
				position="bottom right"
			>
				<div className="wc-block-editor-container">
					<Skeleton numberOfLines={ 3 } />
					<Disabled>
						<input
							type={ 'number' }
							value={ '1' }
							className={
								'wc-block-editor-add-to-cart-form__quantity'
							}
						/>
						<Button
							variant={ 'primary' }
							className={
								'wc-block-editor-add-to-cart-form__button'
							}
						>
							{ __(
								'Add to cart',
								'woo-gutenberg-products-block'
							) }
						</Button>
					</Disabled>
				</div>
			</Tooltip>
		</div>
	);
};

export default Edit;
