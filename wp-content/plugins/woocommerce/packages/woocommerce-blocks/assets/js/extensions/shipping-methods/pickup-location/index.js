/**
 * External dependencies
 */
import { render } from '@wordpress/element';

/**
 * Internal dependencies
 */
import SettingsPage from './settings-page';

const settingsContainer = document.getElementById(
	'wc-shipping-method-pickup-location-settings-container'
);
if ( settingsContainer ) {
	render( <SettingsPage />, settingsContainer );
}
