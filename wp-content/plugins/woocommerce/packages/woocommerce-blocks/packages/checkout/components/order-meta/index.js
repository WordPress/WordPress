/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import { createSlotFill, hasValidFills, useSlot } from '../../slot';
import TotalsWrapper from '../totals-wrapper';

const slotName = '__experimentalOrderMeta';

const { Fill: ExperimentalOrderMeta, Slot: OrderMetaSlot } =
	createSlotFill( slotName );

const Slot = ( { className, extensions, cart, context } ) => {
	const { fills } = useSlot( slotName );
	return (
		hasValidFills( fills ) && (
			<TotalsWrapper slotWrapper={ true }>
				<OrderMetaSlot
					className={ classnames(
						className,
						'wc-block-components-order-meta'
					) }
					fillProps={ { extensions, cart, context } }
				/>
			</TotalsWrapper>
		)
	);
};

ExperimentalOrderMeta.Slot = Slot;

export default ExperimentalOrderMeta;
