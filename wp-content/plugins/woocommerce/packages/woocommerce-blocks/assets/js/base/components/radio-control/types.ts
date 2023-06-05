/**
 * External dependencies
 */
import type { ReactElement } from 'react';

export interface RadioControlProps {
	// Class name for control.
	className?: string;
	// ID for the control.
	id?: string;
	// The selected option. This is a controlled component.
	selected: string;
	// Fired when an option is changed.
	onChange: ( value: string ) => void;
	// List of radio control options.
	options: RadioControlOption[];
}

export interface RadioControlOptionProps {
	checked: boolean;
	name?: string;
	onChange: ( value: string ) => void;
	option: RadioControlOption;
}

interface RadioControlOptionContent {
	label: string | JSX.Element;
	description?: string | ReactElement | undefined;
	secondaryLabel?: string | ReactElement | undefined;
	secondaryDescription?: string | ReactElement | undefined;
}

export interface RadioControlOption extends RadioControlOptionContent {
	value: string;
	onChange?: ( value: string ) => void;
}

export interface RadioControlOptionLayout extends RadioControlOptionContent {
	id?: string;
}
