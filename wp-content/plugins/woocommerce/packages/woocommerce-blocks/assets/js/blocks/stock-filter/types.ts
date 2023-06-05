export interface Attributes {
	className?: string;
	heading: string;
	headingLevel: number;
	showCounts: boolean;
	showFilterButton: boolean;
	isPreview?: boolean;
	displayStyle: string;
	selectType: string;
}

export interface DisplayOption {
	value: string;
	name: string;
	label: JSX.Element;
	textLabel: string;
}

export type Current = {
	slug: string;
	name: string;
};
