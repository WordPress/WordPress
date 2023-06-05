/**
 * External dependencies
 */
import Summary from '@woocommerce/base-components/summary';
import { blocksConfig } from '@woocommerce/block-settings';

interface ProductSummaryProps {
	className?: string;
	shortDescription?: string;
	fullDescription?: string;
}
/**
 * Returns an element containing a summary of the product.
 *
 * @param {Object} props                  Incoming props for the component.
 * @param {string} props.className        CSS class name used.
 * @param {string} props.shortDescription Short description for the product.
 * @param {string} props.fullDescription  Full description for the product.
 */
const ProductSummary = ( {
	className,
	shortDescription = '',
	fullDescription = '',
}: ProductSummaryProps ): JSX.Element | null => {
	const source = shortDescription ? shortDescription : fullDescription;

	if ( ! source ) {
		return null;
	}

	return (
		<Summary
			className={ className }
			source={ source }
			maxLength={ 15 }
			countType={ blocksConfig.wordCountType || 'words' }
		/>
	);
};

export default ProductSummary;
