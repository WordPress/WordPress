/**
 * External dependencies
 */
import Label from '@woocommerce/base-components/filter-element-label';

export const previewOptions = [
	{
		value: 'preview-1',
		name: 'In Stock',
		label: <Label name="In Stock" count={ 3 } />,
		textLabel: 'In Stock (3)',
	},
	{
		value: 'preview-2',
		name: 'Out of stock',
		label: <Label name="Out of stock" count={ 3 } />,
		textLabel: 'Out of stock (3)',
	},
	{
		value: 'preview-3',
		name: 'On backorder',
		label: <Label name="On backorder" count={ 2 } />,
		textLabel: 'On backorder (2)',
	},
];
