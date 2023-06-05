/**
 * External dependencies
 */
import ServerSideRender from '@wordpress/server-side-render';
import { gridBlockPreview } from '@woocommerce/resource-previews';

/**
 * Internal dependencies
 */
import { Props } from './types';

export const HandpickedProductsBlock = ( props: Props ): JSX.Element => {
	const { attributes, name } = props;

	if ( attributes.isPreview ) {
		return gridBlockPreview;
	}

	return <ServerSideRender block={ name } attributes={ attributes } />;
};
