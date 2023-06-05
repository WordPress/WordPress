/* eslint-disable @wordpress/no-unsafe-wp-apis */
/**
 * External dependencies
 */
import { isObject, isString } from '@woocommerce/types';
import { parseStyle } from '@woocommerce/base-utils';

type WithClass = {
	className: string;
};

type WithStyle = {
	style: Record< string, unknown >;
};

export const useTypographyProps = (
	attributes: unknown
): WithStyle & WithClass => {
	const attributesObject = isObject( attributes ) ? attributes : {};
	const style = parseStyle( attributesObject.style );
	const typography = isObject( style.typography )
		? ( style.typography as Record< string, string > )
		: {};

	const classNameFallback = isString( typography.fontFamily )
		? typography.fontFamily
		: '';
	const className = attributesObject.fontFamily
		? `has-${ attributesObject.fontFamily }-font-family`
		: classNameFallback;

	return {
		className,
		style: {
			fontSize: attributesObject.fontSize
				? `var(--wp--preset--font-size--${ attributesObject.fontSize })`
				: typography.fontSize,
			fontStyle: typography.fontStyle,
			fontWeight: typography.fontWeight,
			letterSpacing: typography.letterSpacing,
			lineHeight: typography.lineHeight,
			textDecoration: typography.textDecoration,
			textTransform: typography.textTransform,
		},
	};
};
