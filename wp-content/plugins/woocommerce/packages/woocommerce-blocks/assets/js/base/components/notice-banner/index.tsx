/**
 * External dependencies
 */
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import { Icon, close } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import './style.scss';
import { getDefaultPoliteness, getStatusIcon } from './utils';
import Button from '../button';
import { useSpokenMessage } from '../../hooks';

export interface NoticeBannerProps {
	// The displayed message of a notice. Also used as the spoken message for assistive technology, unless `spokenMessage` is provided as an alternative message.
	children: React.ReactNode;
	// Additional class name to give to the notice.
	className?: string | undefined;
	// Determines whether the notice can be dismissed by the user.
	isDismissible?: boolean | undefined;
	// Function called when dismissing the notice.
	onRemove?: ( () => void ) | undefined;
	// Determines the level of politeness for the notice for assistive technology.
	politeness?: 'polite' | 'assertive' | undefined;
	// Optionally provided to change the spoken message for assistive technology.
	spokenMessage?: string | React.ReactNode | undefined;
	// Status determines the color of the notice and the icon.
	status: 'success' | 'error' | 'info' | 'warning' | 'default';
	// Optional summary text shown above notice content, used when several notices are listed together.
	summary?: string | undefined;
}

/**
 * NoticeBanner: An informational UI displayed near the top of the store pages.
 *
 * Notices are informational UI displayed near the top of store pages. WooCommerce blocks, themes, and plugins all use
 * notices to indicate the result of an action, or to draw the user’s attention to necessary information.
 */
const NoticeBanner = ( {
	className,
	status = 'default',
	children,
	spokenMessage = children,
	onRemove = () => void 0,
	isDismissible = true,
	politeness = getDefaultPoliteness( status ),
	summary,
}: NoticeBannerProps ) => {
	useSpokenMessage( spokenMessage, politeness );

	const dismiss = ( event: React.SyntheticEvent ) => {
		if (
			typeof event?.preventDefault === 'function' &&
			event.preventDefault
		) {
			event.preventDefault();
		}
		onRemove();
	};

	return (
		<div
			className={ classnames(
				className,
				'wc-block-components-notice-banner',
				'is-' + status,
				{
					'is-dismissible': isDismissible,
				}
			) }
		>
			<Icon icon={ getStatusIcon( status ) } />
			<div className="wc-block-components-notice-banner__content">
				{ summary && (
					<p className="wc-block-components-notice-banner__summary">
						{ summary }
					</p>
				) }
				{ children }
			</div>
			{ !! isDismissible && (
				<Button
					className="wc-block-components-notice-banner__dismiss"
					icon={ close }
					label={ __(
						'Dismiss this notice',
						'woo-gutenberg-products-block'
					) }
					onClick={ dismiss }
					showTooltip={ false }
				/>
			) }
		</div>
	);
};

export default NoticeBanner;
