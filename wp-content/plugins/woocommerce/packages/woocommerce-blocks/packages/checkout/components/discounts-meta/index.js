/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import { createSlotFill, hasValidFills, useSlot } from '../../slot';
import TotalsWrapper from '../totals-wrapper';

const slotName = '__experimentalDiscountsMeta';

const { Fill: ExperimentalDiscountsMeta, Slot: DiscountsMetaSlot } =
	createSlotFill( slotName );

const Slot = ( { className, extensions, cart, context } ) => {
	const { fills } = useSlot( slotName );
	return (
		hasValidFills( fills ) && (
			<TotalsWrapper slotWrapper={ true }>
				<DiscountsMetaSlot
					className={ classnames(
						className,
						'wc-block-components-discounts-meta'
					) }
					fillProps={ { extensions, cart, context } }
				/>
			</TotalsWrapper>
		)
	);
};

ExperimentalDiscountsMeta.Slot = Slot;

export default ExperimentalDiscountsMeta;
