/**
 * External dependencies
 */
import classNames from 'classnames';
import { ContainerWidthContextProvider } from '@woocommerce/base-context';

/**
 * Internal dependencies
 */
import './style.scss';
interface SidebarLayoutProps {
	children: JSX.Element | JSX.Element[];
	className: string;
}

const SidebarLayout = ( {
	children,
	className,
}: SidebarLayoutProps ): JSX.Element => {
	return (
		<ContainerWidthContextProvider
			className={ classNames(
				'wc-block-components-sidebar-layout',
				className
			) }
		>
			{ children }
		</ContainerWidthContextProvider>
	);
};

export default SidebarLayout;
