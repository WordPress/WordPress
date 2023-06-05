/**
 * External dependencies
 */
import { withInstanceId } from '@wordpress/compose';
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import { useTabState, Tab, TabList, TabPanel } from 'reakit/Tab';
/**
 * Internal dependencies
 */
import './style.scss';

export interface TabsProps {
	/**
	 * Component wrapper classname
	 */
	className?: string;
	/**
	 * Event handler triggered when a tab is selected
	 */
	onSelect?: ( tabName: string ) => void;
	/**
	 * Array of tab objects
	 */
	tabs: Array< {
		name: string;
		title: string;
		content: JSX.Element;
		ariaLabel?: string;
	} >;
	/**
	 * Classname to be applied to the active tab
	 */
	activeClass?: string;
	/**
	 * Name of the tab to be selected by default
	 */
	initialTabName?: string | undefined;
	/**
	 * Aria label for the tablist
	 */
	ariaLabel?: string;
	/**
	 * Instance ID for the component
	 */
	instanceId: number;
	/**
	 * ID for the component
	 */
	id?: string;
}

/**
 * Exporting the component for Storybook. Use the default export instead.
 */
export const __TabsWithoutInstanceId = ( {
	className,
	onSelect = () => null,
	tabs,
	activeClass = 'is-active',
	initialTabName,
	ariaLabel = __( 'Tabbed Content', 'woo-gutenberg-products-block' ),
	instanceId,
	id,
}: TabsProps ): JSX.Element | null => {
	const initialTab = initialTabName
		? { selectedId: `${ instanceId }-${ initialTabName }` }
		: undefined;
	const tabState = useTabState( initialTab );
	if ( tabs.length === 0 ) {
		return null;
	}
	return (
		<div className={ classnames( 'wc-block-components-tabs', className ) }>
			<TabList
				{ ...tabState }
				id={ id }
				className={ 'wc-block-components-tabs__list' }
				aria-label={ ariaLabel }
			>
				{ tabs.map( ( { name, title, ariaLabel: tabAriaLabel } ) => (
					<Tab
						{ ...tabState }
						id={ `${ instanceId }-${ name }` }
						manual={ true }
						className={ classnames(
							'wc-block-components-tabs__item',
							{
								[ activeClass ]:
									// reakit uses the ID as the selectedId
									`${ instanceId }-${ name }` ===
									tabState.selectedId,
							}
						) }
						onClick={ () => onSelect( name ) }
						type="button"
						key={ name }
						aria-label={ tabAriaLabel }
					>
						<span className="wc-block-components-tabs__item-content">
							{ title }
						</span>
					</Tab>
				) ) }
			</TabList>

			{ tabs.map( ( { name, content } ) => (
				<TabPanel
					{ ...tabState }
					key={ name }
					id={ `${ instanceId }-${ name }-view` }
					tabId={ `${ instanceId }-${ name }` }
					className="wc-block-components-tabs__content"
				>
					{ tabState.selectedId === `${ instanceId }-${ name }` &&
						content }
				</TabPanel>
			) ) }
		</div>
	);
};

export default withInstanceId( __TabsWithoutInstanceId );
