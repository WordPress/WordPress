/**
 * Internal dependencies
 */
import './style.scss';

interface FilterTitlePlaceholderProps {
	children?: React.ReactNode;
}

const FilterTitlePlaceholder = ( {
	children,
}: FilterTitlePlaceholderProps ): JSX.Element => {
	return (
		<div className="wc-block-filter-title-placeholder">{ children }</div>
	);
};

export default FilterTitlePlaceholder;
