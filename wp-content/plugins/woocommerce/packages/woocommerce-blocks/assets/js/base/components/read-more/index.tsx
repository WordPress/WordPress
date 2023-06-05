/**
 * External dependencies
 */
import { createRef, Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import type { MouseEvent, RefObject, ReactNode } from 'react';

/**
 * Internal dependencies
 */
import { clampLines } from './utils';

export interface ReadMoreProps {
	/**
	 * The entire content to clamp
	 */
	children: ReactNode;
	/**
	 * Class names for the wrapped component
	 */
	className: string;
	/**
	 * What symbol to show after the allowed lines are reached
	 *
	 * @default '&hellip';
	 */
	ellipsis: string;
	/**
	 * The string to show to collapse the entire text into its clamped form
	 *
	 * @default 'Read less'
	 */
	lessText: string;
	/**
	 * How many lines to show before the text is clamped
	 *
	 * @default 3
	 */
	maxLines: number;
	/**
	 * The string to show to expande the entire text
	 *
	 * @default 'Read more'
	 */
	moreText: string;
}

interface ReadMoreState {
	/**
	 * This is true when read more has been pressed and the full review is shown.
	 */
	isExpanded: boolean;
	/**
	 * True if we are clamping content. False if the review is short. Null during init.
	 */
	clampEnabled: boolean | null;
	/**
	 * Content is passed in via children.
	 */
	content: ReactNode;
	/**
	 * Summary content generated from content HTML.
	 */
	summary: string;
}

export const defaultProps = {
	className: 'read-more-content',
	ellipsis: '&hellip;',
	lessText: __( 'Read less', 'woo-gutenberg-products-block' ),
	maxLines: 3,
	moreText: __( 'Read more', 'woo-gutenberg-products-block' ),
};

/**
 * Show text based content, limited to a number of lines, with a read more link.
 *
 * Based on https://github.com/zoltantothcom/react-clamp-lines.
 */
class ReadMore extends Component< ReadMoreProps, ReadMoreState > {
	static defaultProps = defaultProps;

	private reviewSummary: RefObject< HTMLDivElement >;
	private reviewContent: RefObject< HTMLDivElement >;

	constructor( props: ReadMoreProps ) {
		super( props );

		this.state = {
			/**
			 * This is true when read more has been pressed and the full review is shown.
			 */
			isExpanded: false,
			/**
			 * True if we are clamping content. False if the review is short. Null during init.
			 */
			clampEnabled: null,
			/**
			 * Content is passed in via children.
			 */
			content: props.children,
			/**
			 * Summary content generated from content HTML.
			 */
			summary: '.',
		};

		this.reviewContent = createRef< HTMLDivElement >();
		this.reviewSummary = createRef< HTMLDivElement >();
		this.getButton = this.getButton.bind( this );
		this.onClick = this.onClick.bind( this );
	}

	componentDidMount(): void {
		this.setSummary();
	}

	componentDidUpdate( prevProps: ReadMoreProps ): void {
		if (
			prevProps.maxLines !== this.props.maxLines ||
			prevProps.children !== this.props.children
		) {
			/**
			 * if maxLines or content changed we need to reset the state to
			 * initial values so that summary can be calculated again
			 */
			this.setState(
				{
					clampEnabled: null,
					summary: '.',
				},
				this.setSummary
			);
		}
	}

	setSummary(): void {
		if ( this.props.children ) {
			const { maxLines, ellipsis } = this.props;

			if (
				! this.reviewSummary.current ||
				! this.reviewContent.current
			) {
				return;
			}

			const lineHeight = this.reviewSummary.current.clientHeight + 1;
			const reviewHeight = this.reviewContent.current.clientHeight + 1;
			const maxHeight = lineHeight * maxLines + 1;
			const clampEnabled = reviewHeight > maxHeight;

			this.setState( {
				clampEnabled,
			} );

			if ( clampEnabled ) {
				this.setState( {
					summary: clampLines(
						this.reviewContent.current.innerHTML,
						this.reviewSummary.current,
						maxHeight,
						ellipsis
					),
				} );
			}
		}
	}

	getButton(): JSX.Element | undefined {
		const { isExpanded } = this.state;
		const { className, lessText, moreText } = this.props;

		const buttonText = isExpanded ? lessText : moreText;

		if ( ! buttonText ) {
			return;
		}

		return (
			<a
				href="#more"
				className={ className + '__read_more' }
				onClick={ this.onClick }
				aria-expanded={ ! isExpanded }
				role="button"
			>
				{ buttonText }
			</a>
		);
	}

	/**
	 * Handles the click event for the read more/less button.
	 */
	onClick( e: MouseEvent< HTMLAnchorElement, MouseEvent > ): void {
		e.preventDefault();

		const { isExpanded } = this.state;

		this.setState( {
			isExpanded: ! isExpanded,
		} );
	}

	render(): JSX.Element | null {
		const { className } = this.props;
		const { content, summary, clampEnabled, isExpanded } = this.state;

		if ( ! content ) {
			return null;
		}

		if ( clampEnabled === false ) {
			return (
				<div className={ className }>
					<div ref={ this.reviewContent }>{ content }</div>
				</div>
			);
		}

		return (
			<div className={ className }>
				{ ( ! isExpanded || clampEnabled === null ) && (
					<div
						ref={ this.reviewSummary }
						aria-hidden={ isExpanded }
						dangerouslySetInnerHTML={ {
							__html: summary,
						} }
					/>
				) }
				{ ( isExpanded || clampEnabled === null ) && (
					<div
						ref={ this.reviewContent }
						aria-hidden={ ! isExpanded }
					>
						{ content }
					</div>
				) }
				{ this.getButton() }
			</div>
		);
	}
}

export default ReadMore;
