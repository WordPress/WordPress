/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { PanelBody, SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
/**
 * Internal dependencies
 */
import { formatTitle } from '../utils';

const PageSelector = ( { setPageId, pageId, labels } ) => {
	const pages =
		useSelect( ( select ) => {
			return select( 'core' ).getEntityRecords( 'postType', 'page', {
				status: 'publish',
				orderby: 'title',
				order: 'asc',
				per_page: 100,
			} );
		}, [] ) || null;
	if ( pages ) {
		return (
			<PanelBody title={ labels.title }>
				<SelectControl
					label={ __( 'Link to', 'woocommerce' ) }
					value={ pageId }
					options={ [
						{
							label: labels.default,
							value: 0,
						},
						...pages.map( ( page ) => {
							return {
								label: formatTitle( page, pages ),
								value: parseInt( page.id, 10 ),
							};
						} ),
					] }
					onChange={ ( value ) => setPageId( parseInt( value, 10 ) ) }
				/>
			</PanelBody>
		);
	}
	return null;
};

export default PageSelector;
