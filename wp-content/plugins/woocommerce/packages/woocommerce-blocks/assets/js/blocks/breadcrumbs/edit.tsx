/**
 * External dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import { Disabled } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export interface Attributes {
	className?: string;
}

const Edit = () => {
	const blockProps = useBlockProps( {
		className: 'woocommerce wc-block-breadcrumbs',
	} );

	return (
		<div { ...blockProps }>
			<Disabled>
				<a href="/">
					{ __( 'Breadcrumbs', 'woo-gutenberg-products-block' ) }
				</a>
				{ __( ' / Navigation / Path', 'woo-gutenberg-products-block' ) }
			</Disabled>
		</div>
	);
};

export default Edit;
