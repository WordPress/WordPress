/**
 * External dependencies
 */
import classnames from 'classnames';
import type { NoticeType } from '@woocommerce/types';
import { useReducedMotion } from '@wordpress/compose';
import { useRef } from '@wordpress/element';
import { CSSTransition, TransitionGroup } from 'react-transition-group';

/**
 * Internal dependencies
 */
import './style.scss';
import Snackbar from './snackbar';

export type SnackbarListProps = {
	// Class name to be added to the container.
	className?: string | undefined;
	// List of notices to be rendered.
	notices: NoticeType[];
	// Callback to be called when a notice is dismissed.
	onRemove: ( id: string ) => void;
};

/**
 * A temporary informational UI displayed at the bottom of store pages.
 */
const SnackbarList = ( {
	notices,
	className,
	onRemove = () => void 0,
}: SnackbarListProps ): JSX.Element => {
	const listRef = useRef< HTMLDivElement | null >( null );
	const isReducedMotion = useReducedMotion();

	const removeNotice = ( notice: NoticeType ) => () =>
		onRemove( notice?.id || '' );

	return (
		<div
			className={ classnames(
				className,
				'wc-block-components-notice-snackbar-list'
			) }
			tabIndex={ -1 }
			ref={ listRef }
		>
			{ isReducedMotion ? (
				notices.map( ( notice ) => {
					const { content, ...restNotice } = notice;
					return (
						<Snackbar
							{ ...restNotice }
							onRemove={ removeNotice( notice ) }
							listRef={ listRef }
							key={ notice.id }
						>
							{ notice.content }
						</Snackbar>
					);
				} )
			) : (
				<TransitionGroup>
					{ notices.map( ( notice ) => {
						const { content, ...restNotice } = notice;
						return (
							<CSSTransition
								key={ 'snackbar-' + notice.id }
								timeout={ 500 }
								classNames="notice-transition"
							>
								<Snackbar
									{ ...restNotice }
									onRemove={ removeNotice( notice ) }
									listRef={ listRef }
								>
									{ content }
								</Snackbar>
							</CSSTransition>
						);
					} ) }
				</TransitionGroup>
			) }
		</div>
	);
};

export default SnackbarList;
