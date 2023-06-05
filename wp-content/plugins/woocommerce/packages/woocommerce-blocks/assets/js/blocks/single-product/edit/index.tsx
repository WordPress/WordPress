/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { Placeholder, Button, PanelBody } from '@wordpress/components';
import { withProduct } from '@woocommerce/block-hocs';
import BlockErrorBoundary from '@woocommerce/base-components/block-error-boundary';
import EditProductLink from '@woocommerce/editor-components/edit-product-link';
import { singleProductBlockPreview } from '@woocommerce/resource-previews';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { ProductResponseItem } from '@woocommerce/types';
import ErrorPlaceholder, {
	ErrorObject,
} from '@woocommerce/editor-components/error-placeholder';

/**
 * Internal dependencies
 */
import './editor.scss';
import SharedProductControl from './shared-product-control';
import EditorBlockControls from './editor-block-controls';
import LayoutEditor from './layout-editor';
import { BLOCK_ICON } from '../constants';
import metadata from '../block.json';
import { Attributes } from '../types';

interface EditorProps {
	className: string;
	attributes: {
		productId: number;
		isPreview: boolean;
	};
	setAttributes: ( attributes: Attributes ) => void;
	error: string | ErrorObject;
	getProduct: () => void;
	product: ProductResponseItem;
	isLoading: boolean;
	clientId: string;
}

const Editor = ( {
	className,
	attributes,
	setAttributes,
	error,
	getProduct,
	product,
	isLoading,
	clientId,
}: EditorProps ) => {
	const { productId, isPreview } = attributes;
	const [ isEditing, setIsEditing ] = useState( ! productId );
	const blockProps = useBlockProps();

	if ( isPreview ) {
		return singleProductBlockPreview;
	}

	if ( error ) {
		return (
			<ErrorPlaceholder
				className="wc-block-editor-single-product-error"
				error={ error as ErrorObject }
				isLoading={ isLoading }
				onRetry={ getProduct }
			/>
		);
	}

	return (
		<div className={ className }>
			{ /* eslint-disable-next-line @typescript-eslint/ban-ts-comment */ }
			{ /* @ts-ignore */ }
			<BlockErrorBoundary
				header={ __(
					'Single Product Block Error',
					'woo-gutenberg-products-block'
				) }
			>
				<EditorBlockControls
					setIsEditing={ setIsEditing }
					isEditing={ isEditing }
				/>
				{ isEditing ? (
					<Placeholder
						icon={ BLOCK_ICON }
						label={ metadata.title }
						className="wc-block-editor-single-product"
					>
						{ metadata.description }
						<div className="wc-block-editor-single-product__selection">
							<SharedProductControl
								attributes={ attributes }
								setAttributes={ setAttributes }
							/>
							<Button
								isSecondary
								onClick={ () => {
									setIsEditing( false );
								} }
							>
								{ __( 'Done', 'woo-gutenberg-products-block' ) }
							</Button>
						</div>
					</Placeholder>
				) : (
					<div { ...blockProps }>
						<InspectorControls>
							<PanelBody
								title={ __(
									'Product',
									'woo-gutenberg-products-block'
								) }
								initialOpen={ false }
							>
								<SharedProductControl
									attributes={ attributes }
									setAttributes={ setAttributes }
								/>
							</PanelBody>
						</InspectorControls>

						<EditProductLink productId={ productId } />
						<LayoutEditor
							clientId={ clientId }
							product={ product }
							isLoading={ isLoading }
						/>
					</div>
				) }
			</BlockErrorBoundary>
		</div>
	);
};

export default withProduct( Editor );
