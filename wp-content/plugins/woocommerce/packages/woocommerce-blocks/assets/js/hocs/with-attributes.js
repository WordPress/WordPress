/**
 * External dependencies
 */
import { useState, useEffect } from '@wordpress/element';
import { getAttributes, getTerms } from '@woocommerce/editor-components/utils';

/**
 * Internal dependencies
 */
import { formatError } from '../base/utils/errors.js';

/**
 * Get attribute data (name, taxonomy etc) from server data.
 *
 * @param {number}     attributeId   Attribute ID to look for.
 * @param {Array|null} attributeList List of attributes.
 * @param {string}     matchField    Field to match on. e.g. id or slug.
 */
const getAttributeData = ( attributeId, attributeList, matchField = 'id' ) => {
	return Array.isArray( attributeList )
		? attributeList.find( ( attr ) => attr[ matchField ] === attributeId )
		: null;
};

/**
 * HOC that calls the useAttributes hook.
 *
 * @param {Function} OriginalComponent Component being wrapped.
 */
const withAttributes = ( OriginalComponent ) => {
	return ( props ) => {
		const { selected = [] } = props;
		const selectedSlug = selected.length ? selected[ 0 ].attr_slug : null;
		const [ attributes, setAttributes ] = useState( null );
		const [ expandedAttribute, setExpandedAttribute ] = useState( 0 );
		const [ termsList, setTermsList ] = useState( {} );
		const [ loading, setLoading ] = useState( true );
		const [ termsLoading, setTermsLoading ] = useState( false );
		const [ error, setError ] = useState( null );

		useEffect( () => {
			if ( attributes === null ) {
				getAttributes()
					.then( ( newAttributes ) => {
						newAttributes = newAttributes.map( ( attribute ) => ( {
							...attribute,
							parent: 0,
						} ) );

						setAttributes( newAttributes );

						if ( selectedSlug ) {
							const selectedAttributeFromTerm = getAttributeData(
								selectedSlug,
								newAttributes,
								'taxonomy'
							);

							if ( selectedAttributeFromTerm ) {
								setExpandedAttribute(
									selectedAttributeFromTerm.id
								);
							}
						}
					} )
					.catch( async ( e ) => {
						setError( await formatError( e ) );
					} )
					.finally( () => {
						setLoading( false );
					} );
			}
		}, [ attributes, selectedSlug ] );

		useEffect( () => {
			const attributeData = getAttributeData(
				expandedAttribute,
				attributes
			);

			if ( ! attributeData ) {
				return;
			}

			setTermsLoading( true );

			getTerms( expandedAttribute )
				.then( ( newTerms ) => {
					newTerms = newTerms.map( ( term ) => ( {
						...term,
						parent: expandedAttribute,
						attr_slug: attributeData.taxonomy,
					} ) );

					setTermsList( ( previousTermsList ) => ( {
						...previousTermsList,
						[ expandedAttribute ]: newTerms,
					} ) );
				} )
				.catch( async ( e ) => {
					setError( await formatError( e ) );
				} )
				.finally( () => {
					setTermsLoading( false );
				} );
		}, [ expandedAttribute, attributes ] );

		return (
			<OriginalComponent
				{ ...props }
				attributes={ attributes || [] }
				error={ error }
				expandedAttribute={ expandedAttribute }
				onExpandAttribute={ setExpandedAttribute }
				isLoading={ loading }
				termsAreLoading={ termsLoading }
				termsList={ termsList }
			/>
		);
	};
};

export default withAttributes;
