/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import { createSlotFill } from '../../slot';

const slotName = '__experimentalOrderShippingPackages';
const {
	Fill: ExperimentalOrderShippingPackages,
	Slot: OrderShippingPackagesSlot,
} = createSlotFill( slotName );

const Slot = ( {
	className,
	noResultsMessage,
	renderOption,
	extensions,
	cart,
	components,
	context,
	collapsible,
	showItems,
} ) => {
	return (
		<OrderShippingPackagesSlot
			className={ classnames(
				'wc-block-components-shipping-rates-control',
				className
			) }
			fillProps={ {
				collapse: collapsible,
				collapsible,
				showItems,
				noResultsMessage,
				renderOption,
				extensions,
				cart,
				components,
				context,
			} }
		/>
	);
};

ExperimentalOrderShippingPackages.Slot = Slot;

export default ExperimentalOrderShippingPackages;
