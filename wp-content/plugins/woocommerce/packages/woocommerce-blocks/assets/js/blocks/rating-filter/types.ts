export interface Attributes {
	className?: string;
	displayStyle: string;
	selectType: string;
	showCounts: boolean;
	showFilterButton: boolean;
	isPreview?: boolean;
}

export interface DisplayOption {
	label: JSX.Element;
	value: string;
}
