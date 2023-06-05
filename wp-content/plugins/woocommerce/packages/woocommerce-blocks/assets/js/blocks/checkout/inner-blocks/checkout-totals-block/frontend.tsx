/**
 * External dependencies
 */
import classnames from 'classnames';
import { Sidebar } from '@woocommerce/base-components/sidebar-layout';

/**
 * Internal dependencies
 */
import './style.scss';

const FrontendBlock = ( {
	children,
	className,
}: {
	children: JSX.Element;
	className?: string;
} ): JSX.Element => {
	return (
		<Sidebar
			className={ classnames( 'wc-block-checkout__sidebar', className ) }
		>
			{ children }
		</Sidebar>
	);
};

export default FrontendBlock;
