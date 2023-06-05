/**
 * External dependencies
 */
import { Fragment } from '@wordpress/element';
import classNames from 'classnames';
import type { ReactElement, HTMLProps } from 'react';

interface LabelProps extends HTMLProps< HTMLElement > {
	label?: string | undefined;
	screenReaderLabel?: string | undefined;
	wrapperElement?: string;
	wrapperProps?: HTMLProps< HTMLElement >;
}

/**
 * Component used to render an accessible text given a label and/or a
 * screenReaderLabel. The wrapper element and wrapper props can also be
 * specified via props.
 *
 */
const Label = ( {
	label,
	screenReaderLabel,
	wrapperElement,
	wrapperProps = {},
}: LabelProps ): ReactElement => {
	let Wrapper;

	const hasLabel = typeof label !== 'undefined' && label !== null;
	const hasScreenReaderLabel =
		typeof screenReaderLabel !== 'undefined' && screenReaderLabel !== null;

	if ( ! hasLabel && hasScreenReaderLabel ) {
		Wrapper = wrapperElement || 'span';
		wrapperProps = {
			...wrapperProps,
			className: classNames(
				wrapperProps.className,
				'screen-reader-text'
			),
		};

		return <Wrapper { ...wrapperProps }>{ screenReaderLabel }</Wrapper>;
	}

	Wrapper = wrapperElement || Fragment;

	if ( hasLabel && hasScreenReaderLabel && label !== screenReaderLabel ) {
		return (
			<Wrapper { ...wrapperProps }>
				<span aria-hidden="true">{ label }</span>
				<span className="screen-reader-text">
					{ screenReaderLabel }
				</span>
			</Wrapper>
		);
	}

	return <Wrapper { ...wrapperProps }>{ label }</Wrapper>;
};

export default Label;
