/**
 * External dependencies
 */
import { useEffect } from '@wordpress/element';
import {
	useCollection,
	useQueryStateByKey,
} from '@woocommerce/base-context/hooks';
import { decodeEntities } from '@wordpress/html-entities';
import { __ } from '@wordpress/i18n';
import { getSettingWithCoercion } from '@woocommerce/settings';
import {
	AttributeObject,
	isAttributeQueryCollection,
	isAttributeTermCollection,
	isBoolean,
} from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { renderRemovableListItem, removeArgsFromFilterUrl } from './utils';
import { removeAttributeFilterBySlug } from '../../utils/attributes-query';

interface ActiveAttributeFiltersProps {
	displayStyle: string;
	operator: 'and' | 'in';
	slugs: string[];
	attributeObject: AttributeObject;
	isLoadingCallback: ( val: boolean ) => void;
}

/**
 * Component that renders active attribute (terms) filters.
 *
 * @param {Object} props                   Incoming props for the component.
 * @param {Object} props.attributeObject   The attribute object.
 * @param {Array}  props.slugs             The slugs for attributes.
 * @param {string} props.operator          The operator for the filter.
 * @param {string} props.displayStyle      The style used for displaying the filters.
 * @param {string} props.isLoadingCallback The callback to trigger the loading complete state.
 */
const ActiveAttributeFilters = ( {
	attributeObject,
	slugs = [],
	operator = 'in',
	displayStyle,
	isLoadingCallback,
}: ActiveAttributeFiltersProps ) => {
	const { results, isLoading } = useCollection( {
		namespace: '/wc/store/v1',
		resourceName: 'products/attributes/terms',
		resourceValues: [ attributeObject.id ],
	} );

	const [ productAttributes, setProductAttributes ] = useQueryStateByKey(
		'attributes',
		[]
	);

	useEffect( () => {
		isLoadingCallback( isLoading );
	}, [ isLoading, isLoadingCallback ] );

	if (
		! Array.isArray( results ) ||
		! isAttributeTermCollection( results ) ||
		! isAttributeQueryCollection( productAttributes )
	) {
		return null;
	}

	const attributeLabel = attributeObject.label;

	const filteringForPhpTemplate = getSettingWithCoercion(
		'is_rendering_php_template',
		false,
		isBoolean
	);

	return (
		<li>
			<span className="wc-block-active-filters__list-item-type">
				{ attributeLabel }:
			</span>
			<ul>
				{ slugs.map( ( slug, index ) => {
					const termObject = results.find( ( term ) => {
						return term.slug === slug;
					} );

					if ( ! termObject ) {
						return null;
					}

					let prefix: string | JSX.Element = '';

					if ( index > 0 && operator === 'and' ) {
						prefix = (
							<span className="wc-block-active-filters__list-item-operator">
								{ __( 'All', 'woo-gutenberg-products-block' ) }
							</span>
						);
					}

					return renderRemovableListItem( {
						type: attributeLabel,
						name: decodeEntities( termObject.name || slug ),
						prefix,
						isLoading,
						removeCallback: () => {
							const currentAttribute = productAttributes.find(
								( { attribute } ) =>
									attribute === `pa_${ attributeObject.name }`
							);

							// If only one attribute was selected, we remove both filter and query type from the URL.
							if ( currentAttribute?.slug.length === 1 ) {
								removeArgsFromFilterUrl(
									`query_type_${ attributeObject.name }`,
									`filter_${ attributeObject.name }`
								);
							} else {
								// Remove only the slug from the URL.
								removeArgsFromFilterUrl( {
									[ `filter_${ attributeObject.name }` ]:
										slug,
								} );
							}

							if ( ! filteringForPhpTemplate ) {
								removeAttributeFilterBySlug(
									productAttributes,
									setProductAttributes,
									attributeObject,
									slug
								);
							}
						},
						showLabel: false,
						displayStyle,
					} );
				} ) }
			</ul>
		</li>
	);
};

export default ActiveAttributeFilters;
