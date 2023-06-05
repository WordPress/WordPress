/**
 * External dependencies
 */
import type { NoticeType } from '@woocommerce/types';

export interface StoreNoticesContainerProps {
	className?: string | undefined;
	context?: string | string[];
	// List of additional notices that were added inline and not stored in the `core/notices` store.
	additionalNotices?: NoticeType[];
}
