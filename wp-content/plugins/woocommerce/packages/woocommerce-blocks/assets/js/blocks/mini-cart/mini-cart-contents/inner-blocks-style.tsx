/**
 * This is a workaround to style inner blocks using the color
 * settings of the Mini Cart Contents block. It's possible to get
 * the Mini Cart Contents block's attributes inside the inner blocks
 * components, but we have 4 out of 7 inner blocks that inherit
 * style from the Mini Cart Contents block, so we need to apply the
 * styles here to avoid duplication.
 *
 * We only use this hack for the Site Editor. On the frontend, we
 * manipulate the style using block attributes and inject the CSS
 * via `wp_add_inline_style()` function.
 */
export const MiniCartInnerBlocksStyle = ( {
	style,
}: {
	style: Record< string, unknown >;
} ): JSX.Element => {
	const innerStyles = [
		{
			selector:
				'.wc-block-mini-cart__footer .wc-block-mini-cart__footer-actions .wc-block-mini-cart__footer-checkout',
			properties: [
				{
					property: 'color',
					value: style.backgroundColor,
				},
				{
					property: 'background-color',
					value: style.color,
				},
				{
					property: 'border-color',
					value: style.color,
				},
			],
		},
	]
		.map( ( { selector, properties } ) => {
			const rules = properties
				.filter( ( { value } ) => value )
				.map( ( { property, value } ) => `${ property }: ${ value };` )
				.join( '' );

			if ( rules ) return `${ selector } { ${ rules } }`;
			return '';
		} )
		.join( '' )
		.trim();

	if ( ! innerStyles ) {
		return <></>;
	}

	return <style>{ innerStyles } </style>;
};
