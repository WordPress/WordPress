/**
 * External dependencies
 */
import type { ReactElement } from 'react';
import type { BlockEditProps } from '@wordpress/blocks';

export interface BlockAttributes {
	className?: string;
	attributeId: number;
	showCounts: boolean;
	queryType: string;
	heading: string;
	headingLevel: number;
	displayStyle: string;
	showFilterButton: boolean;
	selectType: string;
	isPreview?: boolean;
}

export interface EditProps extends BlockEditProps< BlockAttributes > {
	debouncedSpeak: ( label: string ) => void;
}

export interface DisplayOption {
	value: string;
	name: string;
	label: JSX.Element;
	textLabel: string;
	formattedValue: string;
}

export type Notices = 'noAttributes' | 'noProducts';
export type GetNotice = ( type: Notices ) => ReactElement | null;
