export interface Attributes {
	className?: string;
	displayStyle: DisplayStyle;
	iconStyle: IconStyle;
	iconClass: string;
}

export enum DisplayStyle {
	ICON_AND_TEXT = 'icon_and_text',
	TEXT_ONLY = 'text_only',
	ICON_ONLY = 'icon_only',
}

export enum IconStyle {
	DEFAULT = 'default',
	ALT = 'alt',
}
