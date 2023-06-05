/**
 * External dependencies
 */
import classnames from 'classnames';
import { Main } from '@woocommerce/base-components/sidebar-layout';

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
		<Main className={ classnames( 'wc-block-checkout__main', className ) }>
			<form className="wc-block-components-form wc-block-checkout__form">
				{ children }
			</form>
		</Main>
	);
};

export default FrontendBlock;
