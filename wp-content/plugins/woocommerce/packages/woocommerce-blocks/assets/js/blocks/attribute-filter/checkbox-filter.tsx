/**
 * External dependencies
 */
import CheckboxList from '@woocommerce/base-components/checkbox-list';
/**
 * Internal dependencies
 */
import { DisplayOption } from './types';

interface CheckboxFilterProps {
	className?: string;
	isLoading?: boolean;
	isDisabled?: boolean;
	limit?: number;
	checked?: string[];
	onChange: ( value: string ) => void;
	options?: DisplayOption[];
}

const CheckboxFilter = ( {
	isLoading = false,
	options,
	checked,
	onChange,
}: CheckboxFilterProps ) => {
	if ( isLoading ) {
		return (
			<>
				<span className="is-loading"></span>
				<span className="is-loading"></span>
			</>
		);
	}

	return (
		<CheckboxList
			className="wc-block-attribute-filter-list"
			options={ options }
			checked={ checked }
			onChange={ onChange }
			isLoading={ isLoading }
			isDisabled={ isLoading }
		/>
	);
};

export default CheckboxFilter;
