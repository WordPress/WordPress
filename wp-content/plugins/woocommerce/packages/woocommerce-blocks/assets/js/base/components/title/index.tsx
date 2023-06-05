/**
 * External dependencies
 */
import classNames from 'classnames';
import type { ReactNode } from 'react';

/**
 * Internal dependencies
 */
import './style.scss';

/** @typedef {import('react')} React */

/**
 * Component that renders a block title.
 *
 * @param {Object}          props              Incoming props for the component.
 * @param {React.ReactNode} [props.children]   Children elements this component wraps.
 * @param {string}          [props.className]  CSS class used.
 * @param {string}          props.headingLevel Heading level for title.
 * @param {Object}          [props.props]      Rest of props passed through to component.
 */
const Title = ( {
	children,
	className,
	headingLevel,
	...props
}: TitleProps ): JSX.Element => {
	const buttonClassName = classNames(
		'wc-block-components-title',
		className
	);
	const TagName = `h${ headingLevel }` as keyof JSX.IntrinsicElements;

	return (
		<TagName className={ buttonClassName } { ...props }>
			{ children }
		</TagName>
	);
};

interface TitleProps {
	headingLevel: '1' | '2' | '3' | '4' | '5' | '6';
	className: string;
	children: ReactNode;
}

export default Title;
