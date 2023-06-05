/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import './style.scss';
import Spinner from '../spinner';

interface LoadingMaskProps {
	children?: React.ReactNode | React.ReactNode[];
	className?: string;
	screenReaderLabel?: string;
	showSpinner?: boolean;
	isLoading?: boolean;
}
// @todo Find a way to block buttons/form components when LoadingMask isLoading
const LoadingMask = ( {
	children,
	className,
	screenReaderLabel,
	showSpinner = false,
	isLoading = true,
}: LoadingMaskProps ): JSX.Element => {
	return (
		<div
			className={ classNames( className, {
				'wc-block-components-loading-mask': isLoading,
			} ) }
		>
			{ isLoading && showSpinner && <Spinner /> }
			<div
				className={ classNames( {
					'wc-block-components-loading-mask__children': isLoading,
				} ) }
				aria-hidden={ isLoading }
			>
				{ children }
			</div>
			{ isLoading && (
				<span className="screen-reader-text">
					{ screenReaderLabel ||
						__( 'Loading…', 'woo-gutenberg-products-block' ) }
				</span>
			) }
		</div>
	);
};

export default LoadingMask;
