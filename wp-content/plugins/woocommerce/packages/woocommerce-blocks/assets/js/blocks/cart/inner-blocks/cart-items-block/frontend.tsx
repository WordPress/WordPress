/**
 * External dependencies
 */
import { Main } from '@woocommerce/base-components/sidebar-layout';
import classnames from 'classnames';

const FrontendBlock = ( {
	children,
	className,
}: {
	children: JSX.Element;
	className: string;
} ): JSX.Element => {
	return (
		<Main className={ classnames( 'wc-block-cart__main', className ) }>
			{ children }
		</Main>
	);
};

export default FrontendBlock;
