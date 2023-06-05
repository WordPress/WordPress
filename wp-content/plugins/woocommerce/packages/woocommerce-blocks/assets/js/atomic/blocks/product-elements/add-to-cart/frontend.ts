/**
 * External dependencies
 */
import { withFilteredAttributes } from '@woocommerce/shared-hocs';

/**
 * Internal dependencies
 */
import Block from './block';
import attributes from './attributes';

export default withFilteredAttributes( attributes )( Block );
