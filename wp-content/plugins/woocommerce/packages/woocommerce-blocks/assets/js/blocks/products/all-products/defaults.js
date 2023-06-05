/**
 * External dependencies
 */
import { getSetting } from '@woocommerce/settings';

/**
 * Internal dependencies
 */
import { DEFAULT_PRODUCT_LIST_LAYOUT } from '../base-utils';

export default {
	columns: getSetting( 'default_columns', 3 ),
	rows: getSetting( 'default_rows', 3 ),
	alignButtons: false,
	contentVisibility: {
		orderBy: true,
	},
	orderby: 'date',
	layoutConfig: DEFAULT_PRODUCT_LIST_LAYOUT,
	isPreview: false,
};
