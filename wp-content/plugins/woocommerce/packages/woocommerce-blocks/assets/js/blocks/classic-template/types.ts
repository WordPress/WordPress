/**
 * External dependencies
 */
import { type BlockInstance } from '@wordpress/blocks';

export type TemplateDetails = Record< string, Record< string, string > >;

export type InheritedAttributes = {
	align?: string;
};

export type BlockifiedTemplateConfig = {
	getBlockifiedTemplate: (
		inheritedAttributes: InheritedAttributes
	) => BlockInstance[];
	isConversionPossible: () => boolean;
	getDescription: ( templateTitle: string, canConvert: boolean ) => string;
	getButtonLabel: () => string;
};
