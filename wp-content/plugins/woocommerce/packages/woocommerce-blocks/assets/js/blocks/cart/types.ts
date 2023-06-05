export type InnerBlockTemplate = [
	string,
	Record< string, unknown >,
	InnerBlockTemplate[] | undefined
];

export interface Attributes {
	isPreview: boolean;
	hasDarkControls: boolean;
}
