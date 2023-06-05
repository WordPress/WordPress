/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import './style.scss';

export interface ChipProps {
	/**
	 * Text for chip content.
	 */
	text: string | JSX.Element;
	/**
	 * Screenreader text for the content.
	 */
	screenReaderText?: string;
	/**
	 * The element type for the chip. Default 'li'.
	 */
	element?: string;
	/**
	 * CSS class used.
	 */
	className?: string;
	/**
	 * React children.
	 */
	children?: React.ReactNode | React.ReactNode[];
	/**
	 * Radius size.
	 */
	radius?: 'none' | 'small' | 'medium' | 'large';
}

/**
 * Component used to render a "chip" -- a list item containing some text.
 *
 * Each chip defaults to a list element but this can be customized by providing
 * a wrapperElement.
 *
 */
const Chip: React.FC< ChipProps > = ( {
	text,
	screenReaderText = '',
	element = 'li',
	className = '',
	radius = 'small',
	children = null,
	...props
} ) => {
	const Wrapper = element;
	const wrapperClassName = classNames(
		className,
		'wc-block-components-chip',
		'wc-block-components-chip--radius-' + radius
	);

	const showScreenReaderText = Boolean(
		screenReaderText && screenReaderText !== text
	);

	return (
		<Wrapper className={ wrapperClassName } { ...props }>
			<span
				aria-hidden={ showScreenReaderText }
				className="wc-block-components-chip__text"
			>
				{ text }
			</span>
			{ showScreenReaderText && (
				<span className="screen-reader-text">{ screenReaderText }</span>
			) }
			{ children }
		</Wrapper>
	);
};
export default Chip;
