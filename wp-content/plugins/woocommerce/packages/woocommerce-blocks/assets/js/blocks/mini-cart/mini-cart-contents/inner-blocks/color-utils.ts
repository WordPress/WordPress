/**
 * External dependencies
 */
import classnames from 'classnames';
import { paramCase as kebabCase } from 'change-case';
import { isObject } from '@woocommerce/types';
import { getCSSRules } from '@wordpress/style-engine';
import { parseStyle } from '@woocommerce/base-utils';

interface WithStyle {
	style: Record< string, unknown >;
}

/**
 * Returns the inline styles to add depending on the style object
 *
 * @param {Object} styles Styles configuration.
 * @return {Object} Flattened CSS variables declaration.
 */
function getInlineStyles( styles = {} ) {
	const output = {} as Record< string, unknown >;

	getCSSRules( styles, { selector: '' } ).forEach( ( rule ) => {
		output[ rule.key ] = rule.value;
	} );

	return output;
}

/**
 * Get the classname for a given color.
 */
function getColorClassName(
	colorContextName: string | undefined,
	colorSlug: string | undefined
): string {
	if ( ! colorContextName || ! colorSlug ) {
		return '';
	}
	return `has-${ kebabCase( colorSlug ) }-${ colorContextName }`;
}

function getGradientClassName( gradientSlug: string | undefined ) {
	if ( ! gradientSlug ) {
		return undefined;
	}
	return `has-${ gradientSlug }-gradient-background`;
}

/**
 * Provides the CSS class names and inline styles for a block's color support
 * attributes.
 *
 * @param {Object} attributes Block attributes.
 *
 * @return {Object} Color block support derived CSS classes & styles.
 */
export function getColorClassesAndStyles(
	attributes: WithStyle & {
		backgroundColor?: string | undefined;
		textColor?: string | undefined;
		gradient?: string | undefined;
	}
) {
	const { backgroundColor, textColor, gradient, style } = attributes;

	// Collect color CSS classes.
	const backgroundClass = getColorClassName(
		'background-color',
		backgroundColor
	);
	const textClass = getColorClassName( 'color', textColor );

	const gradientClass = getGradientClassName( gradient );
	const hasGradient = gradientClass || style?.color?.gradient;

	// Determine color CSS class name list.
	const className = classnames( textClass, gradientClass, {
		// Don't apply the background class if there's a gradient.
		[ backgroundClass ]: ! hasGradient && !! backgroundClass,
		'has-text-color': textColor || style?.color?.text,
		'has-background':
			backgroundColor ||
			style?.color?.background ||
			gradient ||
			style?.color?.gradient,
		'has-link-color': isObject( style?.elements?.link )
			? style?.elements?.link?.color
			: undefined,
	} );

	// Collect inline styles for colors.
	const colorStyles = style?.color || {};
	const styleProp = getInlineStyles( { color: colorStyles } );

	return {
		className: className || undefined,
		style: styleProp,
	};
}

export const useColorProps = ( props ) => {
	const propsObject = isObject( props )
		? props
		: {
				style: {},
		  };

	const style = parseStyle( propsObject.style );

	return getColorClassesAndStyles( {
		...propsObject,
		style,
	} as WithStyle );
};
