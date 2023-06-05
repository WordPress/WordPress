/**
 * External dependencies
 */
import type { RegisteredBlockComponent } from '@woocommerce/types';

const registeredBlockComponents: Record<
	string,
	Record< string, RegisteredBlockComponent >
> = {};

export { registeredBlockComponents };
