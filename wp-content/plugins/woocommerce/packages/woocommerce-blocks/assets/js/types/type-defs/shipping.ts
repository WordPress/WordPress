/**
 * External dependencies
 */
import type { ReactElement } from 'react';

export interface PackageRateOption {
	label: string;
	value: string;
	description?: string | ReactElement | undefined;
	secondaryLabel?: string | ReactElement | undefined;
	secondaryDescription?: string | ReactElement | undefined;
	id?: string | undefined;
}
