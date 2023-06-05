/**
 * External dependencies
 */
import { addFilter } from '@wordpress/hooks';

addFilter(
	'woocommerce_admin_analytics_settings',
	'woocommerce-blocks/exclude-draft-status-from-analytics',
	( settings ) => {
		const removeCheckoutDraft = ( optionsGroup ) => {
			if ( optionsGroup.key === 'customStatuses' ) {
				return {
					...optionsGroup,
					options: optionsGroup.options.filter(
						( option ) => option.value !== 'checkout-draft'
					),
				};
			}
			return optionsGroup;
		};

		const actionableStatusesOptions =
			settings.woocommerce_actionable_order_statuses.options.map(
				removeCheckoutDraft
			);
		const excludedStatusesOptions =
			settings.woocommerce_excluded_report_order_statuses.options.map(
				removeCheckoutDraft
			);

		return {
			...settings,
			woocommerce_actionable_order_statuses: {
				...settings.woocommerce_actionable_order_statuses,
				options: actionableStatusesOptions,
			},
			woocommerce_excluded_report_order_statuses: {
				...settings.woocommerce_excluded_report_order_statuses,
				options: excludedStatusesOptions,
			},
		};
	}
);
