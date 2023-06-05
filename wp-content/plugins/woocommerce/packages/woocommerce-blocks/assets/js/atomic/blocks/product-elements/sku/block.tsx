/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import classnames from 'classnames';
import {
	useInnerBlockLayoutContext,
	useProductDataContext,
} from '@woocommerce/shared-context';
import { withProductDataContext } from '@woocommerce/shared-hocs';
import type { HTMLAttributes } from 'react';
import {
	useColorProps,
	useSpacingProps,
	useTypographyProps,
} from '@woocommerce/base-hooks';

/**
 * Internal dependencies
 */
import './style.scss';
import type { Attributes } from './types';

type Props = Attributes & HTMLAttributes< HTMLDivElement >;

const Preview = ( {
	parentClassName,
	sku,
	className,
	style,
}: {
	parentClassName: string;
	sku: string;
	className?: string | undefined;
	style?: React.CSSProperties | undefined;
} ) => (
	<div
		className={ classnames( className, {
			[ `${ parentClassName }__product-sku` ]: parentClassName,
		} ) }
		style={ style }
	>
		{ __( 'SKU:', 'woo-gutenberg-products-block' ) }{ ' ' }
		<strong>{ sku }</strong>
	</div>
);

const Block = ( props: Props ): JSX.Element | null => {
	const { className } = props;
	const { parentClassName } = useInnerBlockLayoutContext();
	const { product } = useProductDataContext();
	const sku = product.sku;

	const colorProps = useColorProps( props );
	const typographyProps = useTypographyProps( props );
	const spacingProps = useSpacingProps( props );

	if ( props.isDescendentOfSingleProductTemplate ) {
		return (
			<Preview
				parentClassName={ parentClassName }
				className={ className }
				sku={ 'Product SKU' }
			/>
		);
	}

	if ( ! sku ) {
		return null;
	}

	return (
		<Preview
			className={ className }
			parentClassName={ parentClassName }
			sku={ sku }
			{ ...( props.isDescendantOfAllProducts && {
				className: classnames(
					className,
					'wc-block-components-product-sku wp-block-woocommerce-product-sku',
					{
						[ colorProps.className ]: colorProps.className,
						[ typographyProps.className ]:
							typographyProps.className,
					}
				),
				style: {
					...colorProps.style,
					...typographyProps.style,
					...spacingProps.style,
				},
			} ) }
		/>
	);
};

export default withProductDataContext( Block );
