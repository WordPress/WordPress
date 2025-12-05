import { register } from '@elementor/frontend-handlers';

register( {
	elementType: 'e-tabs',
	uniqueId: 'e-tabs-handler',
	callback: ( { element, signal } ) => {
		const tabs = element.querySelectorAll( '[data-element_type="e-tab"]' );
		const tabPanels = element.querySelectorAll( '[data-element_type="e-tab-panel"]' );

		const setActiveTab = ( id ) => {
			tabPanels.forEach( ( tabPanel ) => {
				const activeTab = tabPanel.getAttribute( 'data-tab-id' ) === id;

				if ( activeTab ) {
					tabPanel.style.removeProperty( 'display' );
					tabPanel.removeAttribute( 'hidden' );

					return;
				}

				tabPanel.style.display = 'none';
				tabPanel.setAttribute( 'hidden', 'true' );
			} );
		};

		const defaultActiveTab = element.getAttribute( 'data-active-tab' );

		setActiveTab( defaultActiveTab );

		tabs.forEach( ( tab ) => {
			const clickHandler = () => {
				const tabId = tab.getAttribute( 'data-id' );
				setActiveTab( tabId );
			};

			tab.addEventListener( 'click', clickHandler, { signal } );
		} );
	},
} );
