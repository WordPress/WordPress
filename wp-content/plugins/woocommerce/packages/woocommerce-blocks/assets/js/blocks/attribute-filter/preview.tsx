/**
 * External dependencies
 */
import Label from '@woocommerce/base-components/filter-element-label';
import { AttributeObject } from '@woocommerce/types';

export const previewOptions = [
	{
		value: 'preview-1',
		formattedValue: 'preview-1',
		name: 'Blue',
		label: <Label name="Blue" count={ 3 } />,
		textLabel: 'Blue (3)',
	},
	{
		value: 'preview-2',
		formattedValue: 'preview-2',
		name: 'Green',
		label: <Label name="Green" count={ 3 } />,
		textLabel: 'Green (3)',
	},
	{
		value: 'preview-3',
		formattedValue: 'preview-3',
		name: 'Red',
		label: <Label name="Red" count={ 2 } />,
		textLabel: 'Red (2)',
	},
];

export const previewAttributeObject: AttributeObject = {
	count: 0,
	has_archives: true,
	id: 0,
	label: 'Preview',
	name: 'preview',
	order: 'menu_order',
	parent: 0,
	taxonomy: 'preview',
	type: '',
};
