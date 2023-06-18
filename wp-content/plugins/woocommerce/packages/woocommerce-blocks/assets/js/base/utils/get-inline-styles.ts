/**
 * External dependencies
 */
import classnames from 'classnames';
import { paramCase as kebabCase } from 'change-case';
import { getCSSRules } from '@wordpress/style-engine';
import { isObject } from '@woocommerce/types';

type StyleValue = string | NestedStyle;

interface NestedStyle {
	[ key: string ]: StyleValue | undefined;
}

export type WithStyle = {
	style: Record< string, NestedStyle > | undefined;
};

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

/**
 * Generates a CSS class name consisting of all the applicable border color
 * classes given the current block attributes.
 */
function getBorderClassName(
	attributes: WithStyle & {
		borderColor?: string;
	}
) {
	const { borderColor, style } = attributes;
	const borderColorClass = borderColor
		? getColorClassName( 'border-color', borderColor )
		: '';

	return classnames( {
		'has-border-color': borderColor || style?.border?.color,
		borderColorClass,
	} );
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

/**
 * Provides the CSS class names and inline styles for a block's border support
 * attributes.
 *
 * @param {Object} attributes Block attributes.
 * @return {Object} Border block support derived CSS classes & styles.
 */
export function getBorderClassesAndStyles( attributes: WithStyle ) {
	const border = attributes.style?.border || {};
	const className = getBorderClassName( attributes );

	return {
		className: className || undefined,
		style: getInlineStyles( { border } ),
	};
}

/**
 * Provides the CSS class names and inline styles for a block's spacing support
 * attributes.
 *
 * @param {Object} attributes Block attributes.
 *
 * @return {Object} Spacing block support derived CSS classes & styles.
 */
export function getSpacingClassesAndStyles( attributes: WithStyle ) {
	const { style } = attributes;

	// Collect inline styles for spacing.
	const spacingStyles = style?.spacing || {};
	const styleProp = getInlineStyles( { spacing: spacingStyles } );

	return {
		className: undefined,
		style: styleProp,
	};
}
