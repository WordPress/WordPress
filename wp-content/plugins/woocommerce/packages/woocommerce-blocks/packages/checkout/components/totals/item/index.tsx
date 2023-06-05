/**
 * External dependencies
 */
import classnames from 'classnames';
import { isValidElement } from '@wordpress/element';
import FormattedMonetaryAmount from '@woocommerce/base-components/formatted-monetary-amount';
import type { ReactElement, ReactNode } from 'react';
import type { Currency } from '@woocommerce/price-format';

/**
 * Internal dependencies
 */
import './style.scss';

export interface TotalsItemProps {
	className?: string;
	currency: Currency;
	label: string;
	// Value may be a number, or react node. Numbers are passed to FormattedMonetaryAmount.
	value: number | ReactNode;
	description?: ReactNode;
}

const TotalsItemValue = ( {
	value,
	currency,
}: Partial< TotalsItemProps > ): ReactElement | null => {
	if ( isValidElement( value ) ) {
		return (
			<div className="wc-block-components-totals-item__value">
				{ value }
			</div>
		);
	}

	return Number.isFinite( value ) ? (
		<FormattedMonetaryAmount
			className="wc-block-components-totals-item__value"
			currency={ currency || {} }
			value={ value as number }
		/>
	) : null;
};

const TotalsItem = ( {
	className,
	currency,
	label,
	value,
	description,
}: TotalsItemProps ): ReactElement => {
	return (
		<div
			className={ classnames(
				'wc-block-components-totals-item',
				className
			) }
		>
			<span className="wc-block-components-totals-item__label">
				{ label }
			</span>
			<TotalsItemValue value={ value } currency={ currency } />
			<div className="wc-block-components-totals-item__description">
				{ description }
			</div>
		</div>
	);
};

export default TotalsItem;
