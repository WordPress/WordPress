/**
 * External dependencies
 */
import { decodeEntities } from '@wordpress/html-entities';
import classnames from 'classnames';
import type { AnchorHTMLAttributes, HTMLAttributes } from 'react';

/**
 * Internal dependencies
 */
import './style.scss';

export interface ProductNameProps
	extends AnchorHTMLAttributes< HTMLAnchorElement > {
	/**
	 * If `true` renders a `span` element instead of a link
	 */
	disabled?: boolean;
	/**
	 * The product name
	 *
	 * Note: can be an HTML string
	 */
	name: string;
	/**
	 * Click handler
	 */
	onClick?: () => void;
	/**
	 * Link for the product
	 */
	permalink?: string;
}

/**
 * Render the Product name.
 *
 * The store API runs titles through `wp_kses_post()` which removes dangerous HTML tags, so using it inside `dangerouslySetInnerHTML` is considered safe.
 */
export const ProductName = ( {
	className = '',
	disabled = false,
	name,
	permalink = '',
	target,
	rel,
	style,
	onClick,
	...props
}: ProductNameProps ): JSX.Element => {
	const classes = classnames( 'wc-block-components-product-name', className );
	if ( disabled ) {
		// Cast the props as type HTMLSpanElement.
		const disabledProps = props as HTMLAttributes< HTMLSpanElement >;
		return (
			<span
				className={ classes }
				{ ...disabledProps }
				dangerouslySetInnerHTML={ {
					__html: decodeEntities( name ),
				} }
			/>
		);
	}
	return (
		<a
			className={ classes }
			href={ permalink }
			target={ target }
			{ ...props }
			dangerouslySetInnerHTML={ {
				__html: decodeEntities( name ),
			} }
			style={ style }
		/>
	);
};

export default ProductName;
