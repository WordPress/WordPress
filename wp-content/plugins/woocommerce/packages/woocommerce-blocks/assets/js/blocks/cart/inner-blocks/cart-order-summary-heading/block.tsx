/**
 * External dependencies
 */
import classnames from 'classnames';

const Block = ( {
	className,
	content = '',
}: {
	className: string;
	content: string;
} ): JSX.Element => {
	return (
		<span
			className={ classnames( className, 'wc-block-cart__totals-title' ) }
		>
			{ content }
		</span>
	);
};

export default Block;
