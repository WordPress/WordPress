/**
 * External dependencies
 */
import Title from '@woocommerce/base-components/title';
import classnames from 'classnames';

const Block = ( {
	className,
	content = '',
}: {
	className: string;
	content: string;
} ): JSX.Element => {
	return (
		<Title
			headingLevel="2"
			className={ classnames( className, 'wc-block-cart__totals-title' ) }
		>
			{ content }
		</Title>
	);
};

export default Block;
