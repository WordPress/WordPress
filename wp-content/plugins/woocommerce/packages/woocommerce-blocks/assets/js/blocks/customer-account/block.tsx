/**
 * External dependencies
 */
import { Icon } from '@wordpress/icons';
import {
	customerAccountStyle,
	customerAccountStyleAlt,
} from '@woocommerce/icons';
import { getSetting } from '@woocommerce/settings';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Attributes, DisplayStyle, IconStyle } from './types';

const AccountIcon = ( {
	iconStyle,
	displayStyle,
	iconClass,
}: {
	iconStyle: IconStyle;
	displayStyle: DisplayStyle;
	iconClass: string;
} ) => {
	const icon =
		iconStyle === IconStyle.ALT
			? customerAccountStyleAlt
			: customerAccountStyle;

	return displayStyle === DisplayStyle.TEXT_ONLY ? null : (
		<Icon className={ iconClass } icon={ icon } size={ 18 } />
	);
};

const Label = ( { displayStyle }: { displayStyle: DisplayStyle } ) => {
	if ( displayStyle === DisplayStyle.ICON_ONLY ) {
		return null;
	}

	const currentUserId = getSetting( 'currentUserId', null );

	return (
		<span className="label">
			{ currentUserId
				? __( 'My Account', 'woo-gutenberg-products-block' )
				: __( 'Log in', 'woo-gutenberg-products-block' ) }
		</span>
	);
};

export const CustomerAccountBlock = ( {
	attributes,
}: {
	attributes: Attributes;
} ): JSX.Element => {
	const { displayStyle, iconStyle, iconClass } = attributes;

	return (
		<a
			href={ getSetting(
				'dashboardUrl',
				getSetting( 'wpLoginUrl', '/wp-login.php' )
			) }
		>
			<AccountIcon
				iconStyle={ iconStyle }
				displayStyle={ displayStyle }
				iconClass={ iconClass }
			/>
			<Label displayStyle={ displayStyle } />
		</a>
	);
};

export default CustomerAccountBlock;
