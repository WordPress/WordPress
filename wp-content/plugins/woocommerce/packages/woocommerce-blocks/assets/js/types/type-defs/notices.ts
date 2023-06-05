/**
 * External dependencies
 */
import type { Notice } from '@wordpress/notices';

export interface NoticeType extends Partial< Omit< Notice, 'status' > > {
	id: string;
	content: string;
	status: 'success' | 'error' | 'info' | 'warning' | 'default';
	isDismissible: boolean;
	context?: string | undefined;
}
